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

namespace WpNextJsWoo\Internals;

use CPT_columns;
use Seravo_Custom_Bulk_Action;
use WpNextJsWoo\Engine\Base;

/**
 * Post Types and Taxonomies
 */
class PostTypes extends Base
{

	/**
	 * Initialize the class.
	 *
	 * @return void|bool
	 */
	public function initialize()
	{ // phpcs:ignore
		parent::initialize();

		\add_action('init', array($this, 'load_cpts'));
		/*
		 * Custom Columns
		 */
		$post_columns = new CPT_columns('slider');
		$post_columns->add_column(
			'cmb2_field',
			array(
				'label'    => \__('CMB2 Field', S_TEXTDOMAIN),
				'type'     => 'post_meta',
				'meta_key' => '_slider_' . S_TEXTDOMAIN . '_text', // phpcs:ignore WordPress.DB
				'orderby'  => 'meta_value',
				'sortable' => true,
				'prefix'   => '<b>',
				'suffix'   => '</b>',
				'def'      => 'Not defined', // Default value in case post meta not found
				'order'    => '-1',
			)
		);

		/*
		 * Custom Bulk Actions
		 */
		$bulk_actions = new Seravo_Custom_Bulk_Action(array('post_type' => 'slider'));
		$bulk_actions->register_bulk_action(
			array(
				'menu_text'    => 'Mark meta',
				'admin_notice' => 'Written something on custom bulk meta',
				'callback'     => static function ($post_ids) {
					foreach ($post_ids as $post_id) {
						\update_post_meta($post_id, '_slider_' . S_TEXTDOMAIN . '_text', 'Random stuff');
					}

					return true;
				},
			)
		);
		$bulk_actions->init();
		// Add bubble notification for cpt pending
		\add_action('admin_menu', array($this, 'pending_cpt_bubble'), 999);
		\add_filter('pre_get_posts', array($this, 'filter_search'));
	}

	/**
	 * Add support for custom CPT on the search box
	 *
	 * @param \WP_Query $query WP_Query.
	 * @since 1.0.0
	 * @return \WP_Query
	 */
	public function filter_search(\WP_Query $query)
	{
		if ($query->is_search && !\is_admin()) {
			$post_types = $query->get('post_type');

			if ('post' === $post_types) {
				$post_types = array($post_types);
				$query->set('post_type', \array_push($post_types, array('slider')));
			}
		}

		return $query;
	}

	/**
	 * Load CPT and Taxonomies on WordPress
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function load_cpts()
	{ //phpcs:ignore
		// Create Custom Post Type https://github.com/johnbillion/extended-cpts/wiki
		$slider_cpt = \register_extended_post_type(
			'slider',
			array(
				// Show all posts on the post type archive:
				'archive'            => array(
					'nopaging' => true,
				),
				'slug'               => 'slider',
				'show_in_rest'       => true,
				'show_in_graphql' => true,
				'graphql_single_name' => 'slider',
				'graphql_plural_name' => 'sliders',
				'public' => true,
				'publicly_queryable' => true,
				'dashboard_activity' => true,
				'capability_type'    => array('slider', 'sliders'),
				// Add some custom columns to the admin screen
				'admin_cols'         => array(
					'featured_image' => array(
						'title'          => 'Featured Image',
						'featured_image' => 'thumbnail',
					),
					'title',
					'genre'          => array(
						'taxonomy' => 'slider-section',
					),
					'custom_field'   => array(
						'title'    => 'By Lib',
						'meta_key' => '_slider_' . S_TEXTDOMAIN . '_text', // phpcs:ignore
						'cap'      => 'manage_options',
					),
					'date'           => array(
						'title'   => 'Date',
						'default' => 'ASC',
					),
				),
				// Add a dropdown filter to the admin screen:
				'admin_filters'      => array(
					'genre' => array(
						'taxonomy' => 'slider-section',
					),
				),
			),
			array(
				// Override the base names used for labels:
				'singular' => \__('slider', S_TEXTDOMAIN),
				'plural'   => \__('sliders', S_TEXTDOMAIN),
			)
		);


		\register_extended_post_type(
			'size',
			array(
				// Show all posts on the post type archive:
				'archive'            => array(
					'nopaging' => true,
				),
				'slug'               => 'size',
				'show_in_rest'       => true,
				'show_in_graphql' => true,
				'graphql_single_name' => 'size',
				'graphql_plural_name' => 'sizes',
				'public' => true,
				'publicly_queryable' => true,
				'dashboard_activity' => true,
				'capability_type'    => array('size', 'sizes'),
				// Add some custom columns to the admin screen
				'admin_cols'         => array(
					'featured_image' => array(
						'title'          => 'Featured Image',
						'featured_image' => 'thumbnail',
					),
					'title',
					'date'           => array(
						'title'   => 'Date',
						'default' => 'ASC',
					),
				)
			),
			array(
				// Override the base names used for labels:
				'singular' => \__('Size', S_TEXTDOMAIN),
				'plural'   => \__('Sizes', S_TEXTDOMAIN),
			)
		);


		$slider_cpt->add_taxonomy('slider-section', array('hierarchical' => false, 'show_ui' => false));
		// Create Custom Taxonomy https://github.com/johnbillion/extended-taxos
		\register_extended_taxonomy(
			'slider-section',
			'slider',
			array(
				// Use radio buttons in the meta box for this taxonomy on the post editing screen:
				'meta_box'         => 'radio',
				// Show this taxonomy in the 'At a Glance' dashboard widget:
				'dashboard_glance' => true,
				// Add a custom column to the admin screen:
				'admin_cols'       => array(
					'featured_image' => array(
						'title'          => 'Featured Image',
						'featured_image' => 'thumbnail',
					),
				),
				'slug'             => 'slider-cat',
				'show_in_rest'     => true,
				'capabilities'     => array(
					'manage_terms' => 'manage_sliders',
					'edit_terms'   => 'manage_sliders',
					'delete_terms' => 'manage_sliders',
					'assign_terms' => 'read_slider',
				),
				'show_in_graphql' => true,
				'graphql_single_name' => 'sliderCategory',
				'graphql_plural_name' => 'sliderCategories',
			),
			array(
				// Override the base names used for labels:
				'singular' => \__('Slider Category', S_TEXTDOMAIN),
				'plural'   => \__('Slider Categories', S_TEXTDOMAIN),
			)
		);
	}

	/**
	 * Bubble Notification for pending cpt<br>
	 * NOTE: add in $post_types your cpts<br>
	 *
	 *        Reference:  http://wordpress.stackexchange.com/questions/89028/put-update-like-notification-bubble-on-multiple-cpts-menus-for-pending-items/95058
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function pending_cpt_bubble()
	{
		global $menu;

		$post_types = array('slider');

		foreach ($post_types as $type) {
			if (!\post_type_exists($type)) {
				continue;
			}

			// Count posts
			$cpt_count = \wp_count_posts($type);

			if (!$cpt_count->pending) {
				continue;
			}

			// Locate the key of
			$key = self::recursive_array_search_php('edit.php?post_type=' . $type, $menu);

			// Not found, just in case
			if (!$key) {
				return;
			}

			// Modify menu item
			$menu[$key][0] .= \sprintf( //phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				'<span class="update-plugins count-%1$s"><span class="plugin-count">%1$s</span></span>',
				$cpt_count->pending
			);
		}
	}

	/**
	 * Required for the bubble notification<br>
	 *
	 *  Reference:  http://wordpress.stackexchange.com/questions/89028/put-update-like-notification-bubble-on-multiple-cpts-menus-for-pending-items/95058
	 *
	 * @param string $needle First parameter.
	 * @param array  $haystack Second parameter.
	 * @since 1.0.0
	 * @return string|bool
	 */
	private function recursive_array_search_php(string $needle, array $haystack)
	{
		foreach ($haystack as $key => $value) {
			$current_key = $key;

			if (
				$needle === $value ||
				(\is_array($value) &&
					false !== self::recursive_array_search_php($needle, $value))
			) {
				return $current_key;
			}
		}

		return false;
	}
}
