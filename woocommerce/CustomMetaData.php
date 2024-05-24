<?php

/**
 * nextjs_woo_plugin
 *
 * @package   nextjs_woo_plugin
 * @author    Pooria Setayesh <pooriaset@yahoo.com>
 * @copyright 2022 Shop
 * @license   GPL 2.0+
 * @link      
 */

namespace nextjs_woo_plugin\Woocommerce;

use nextjs_woo_plugin\Engine\Base;

class CustomMetaData extends Base
{

    /**
     * Initialize the class.
     *
     * @return void|bool
     */
    public function initialize()
    {
        parent::initialize();
        add_action('save_post', array($this, 'calculate_and_save_discounts'));
        add_action('woocommerce_product_set_stock_status', array($this, 'calculate_and_save_discounts'));
        add_action('woocommerce_variation_set_stock_status', array($this, 'calculate_and_save_discounts'));
        add_action('woocommerce_save_product_variation', array($this, 'calculate_and_save_discounts'));


        // Graphql
        add_action('graphql_register_types', array($this, "register_custom_meta_data_properties"));
    }


    public function calculate_and_save_discounts($post_id)
    {
        if (get_post_type($post_id) == 'product') {
            $max_discount_amount = 0;
            $max_discount_percentage = 0;

            $product = wc_get_product($post_id);
            if ($product->is_type('variable')) {
                // Loop through variations
                $variations = $product->get_children();
                foreach ($variations as $variation_id) {
                    $regular_price = get_post_meta($variation_id, '_regular_price', true);
                    $sale_price = get_post_meta($variation_id, '_sale_price', true);

                    if ($regular_price && $sale_price && $regular_price > $sale_price) {
                        // Calculate discount amount
                        $discount_amount = $regular_price - $sale_price;

                        // Calculate discount percentage
                        $discount_percentage = (($regular_price - $sale_price) / $regular_price) * 100;

                        // Update maximum values
                        if ($discount_amount > $max_discount_amount) {
                            $max_discount_amount = $discount_amount;
                        }
                        if ($discount_percentage > $max_discount_percentage) {
                            $max_discount_percentage = $discount_percentage;
                        }
                    }
                }
            } else {
                // For simple products
                $regular_price = get_post_meta($post_id, '_regular_price', true);
                $sale_price = get_post_meta($post_id, '_sale_price', true);

                if ($regular_price && $sale_price && $regular_price > $sale_price) {
                    // Calculate discount amount
                    $max_discount_amount = $regular_price - $sale_price;

                    // Calculate discount percentage
                    $max_discount_percentage = (($regular_price - $sale_price) / $regular_price) * 100;
                }
            }

            // Save the maximum discount values
            update_post_meta($post_id, '_discount_amount', $max_discount_amount);
            update_post_meta($post_id, '_discount_percentage', $max_discount_percentage);
        }
    }

    public function register_custom_meta_data_properties()
    {
        // Register discount amount and percentage fields for Product type
        register_graphql_field('Product', 'discountAmount', [
            'type' => 'Float',
            'description' => __('The discount amount', 'wp-graphql-woocommerce'),
            'resolve' => function ($product) {
                $discount_amount = get_post_meta($product->ID, '_discount_amount', true);
                return !empty($discount_amount) ? $discount_amount : 0;
            }
        ]);

        register_graphql_field('Product', 'discountPercentage', [
            'type' => 'Float',
            'description' => __('The discount percentage', 'wp-graphql-woocommerce'),
            'resolve' => function ($product) {
                $discount_percentage = get_post_meta($product->ID, '_discount_percentage', true);
                return !empty($discount_percentage) ? $discount_percentage  : 0;
            }
        ]);

        // Register discount amount and percentage fields for ProductVariation type
        register_graphql_field('ProductVariation', 'discountAmount', [
            'type' => 'Float',
            'description' => __('The discount amount', 'wp-graphql-woocommerce'),
            'resolve' => function ($variation) {
                $discount_amount = get_post_meta($variation->ID, '_discount_amount', true);
                return !empty($discount_amount) ? $discount_amount : 0;
            }
        ]);

        register_graphql_field('ProductVariation', 'discountPercentage', [
            'type' => 'Float',
            'description' => __('The discount percentage', 'wp-graphql-woocommerce'),
            'resolve' => function ($variation) {
                $discount_percentage = get_post_meta($variation->ID, '_discount_percentage', true);
                return !empty($discount_percentage) ? $discount_percentage  : 0;
            }
        ]);
    }
}
