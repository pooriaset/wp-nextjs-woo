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
        // add_action('save_post', array($this, 'save_post'));
        add_action('updated_post_meta', array($this, 'my_product_price_update_hook'), 10, 4);
        add_filter('woocommerce_duplicate_product_exclude_meta', array($this, 'exclude_custom_meta_from_duplication'));


        // Graphql
        add_action('graphql_register_types', array($this, "register_custom_meta_data_properties"));
        add_action('graphql_register_types', array($this, "add_total_on_sale_discount_amount_to_cart_item"));
    }


    function exclude_custom_meta_from_duplication($meta_to_exclude)
    {
        $meta_to_exclude[] = '_discount_amount';
        $meta_to_exclude[] = '_discount_percentage';

        return $meta_to_exclude;
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

    public static function calculate_and_save_discounts($post_id)
    {
        if (get_transient('calculating_discounts_' . $post_id)) {
            return;
        }

        set_transient('calculating_discounts_' . $post_id, true, 10);

        if (get_post_type($post_id) == 'product' || get_post_type($post_id) == 'product_variation') {
            $product = wc_get_product($post_id);
            $variations = $product->get_children();

            $max_discount_percentage = 0;
            $max_discount_amount = 0;

            foreach ($variations as $variation_id) {
                ['amount' => $amount, "percentage" => $percentage] = self::calculate_discount($variation_id);


                if ($percentage > $max_discount_percentage) {
                    $max_discount_percentage = $percentage;
                    $max_discount_amount = $amount;
                }

                update_post_meta($variation_id, '_discount_amount', $amount);
                update_post_meta($variation_id, '_discount_percentage', $percentage);
            }

            update_post_meta($post_id, '_discount_amount', $max_discount_amount);
            update_post_meta($post_id, '_discount_percentage', $max_discount_percentage);
        }
    }

    public function my_product_price_update_hook($meta_id, $post_id, $meta_key, $meta_value)
    {
        if (get_post_type($post_id) !== 'product') {
            return;
        }

        if ($meta_key === '_regular_price' || $meta_key === "_sale_price") {
            self::calculate_and_save_discounts($post_id);
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
