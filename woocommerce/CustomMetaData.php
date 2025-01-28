<?php

/**
 * WpNextJsWoo
 *
 * @package  WpNextJsWoo
 * @author    Pooria Setayesh <pooriaset@yahoo.com>
 * @copyright 2022 Shop
 * @license   GPL 2.0+
 * @link      
 */

namespace WpNextJsWoo\Woocommerce;

use WpNextJsWoo\Engine\Base;

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
        add_action('graphql_register_types', array($this, "add_total_on_sale_discount_amount_to_cart_item"));
    }

    /**
     * Calculate discount amount and percentage for a given product.
     *
     * @param int $post_id The ID of the product post.
     * @return array{amount: float, percentage: float} An array containing the discount amount and percentage.
     */
    public static function calculate_discount($post_id)
    {
        $regular_price = get_post_meta($post_id, '_regular_price', true);
        $sale_price = get_post_meta($post_id, '_sale_price', true);

        $amount = 0;
        $percentage = 0;
        if ($regular_price && $sale_price && $regular_price > $sale_price) {
            $amount = $regular_price - $sale_price;
            $percentage = (($regular_price - $sale_price) / $regular_price) * 100;
        }

        return  ["amount" => $amount, "percentage" => $percentage];
    }

    public function add_total_on_sale_discount_amount_to_cart_item()
    {
        register_graphql_field('CartItem', 'totalOnSaleDiscount', [
            'type' => 'Float',
            'description' => __('The discount amount for the product variant in the cart.', 'wp-graphql-woocommerce'),
            'resolve' => function ($cart_item) {
                $variation_id = $cart_item['variation_id'];
                $quantity = (int)$cart_item['quantity'];

                ['amount' => $amount]  = self::calculate_discount($variation_id);
                $discount_amount = $amount * $quantity;

                return $discount_amount;
            },
        ]);
    }

    public function calculate_and_save_discounts($post_id)
    {
        if (get_post_type($post_id) == 'product' || get_post_type($post_id) == 'product_variation') {
            $product = wc_get_product($post_id);

            if ($product->is_type('variable')) {
                $variations = $product->get_children();
                foreach ($variations as $variation_id) {
                    ['amount' => $amount, "percentage" => $percentage] = self::calculate_discount($variation_id);

                    update_post_meta($variation_id, '_discount_amount', $amount);
                    update_post_meta($variation_id, '_discount_percentage', $percentage);
                }
            } else {
                ['amount' => $amount, "percentage" => $percentage] = self::calculate_discount($post_id);

                update_post_meta($post_id, '_discount_amount', $amount);
                update_post_meta($post_id, '_discount_percentage', $percentage);
            }
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
