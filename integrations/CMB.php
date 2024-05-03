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

namespace nextjs_woo_plugin\Integrations;

use nextjs_woo_plugin\Engine\Base;

/**
 * All the CMB related code.
 */
class CMB extends Base
{

	protected static $prefix = '_slider_';

	/**
	 * Initialize class.
	 *
	 * @since 1.0.0
	 * @return void|bool
	 */
	public function initialize()
	{
		parent::initialize();

		require_once S_PLUGIN_ROOT . 'vendor/cmb2/init.php';
		require_once S_PLUGIN_ROOT . 'vendor/cmb2-grid/Cmb2GridPluginLoad.php';

		\add_action('cmb2_init', array($this, 'cmb_slider_metaboxes'));
		add_action('graphql_register_types', array($this, 'add_graphql_fields'));
	}

	/**
	 * Your metabox on Demo CPT
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function cmb_slider_metaboxes()
	{
		$cmb_demo = \new_cmb2_box(
			array(
				'id'           => self::$prefix . 'metabox',
				'title'        => \__('Properties', S_TEXTDOMAIN),
				'object_types' => array('slider'),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true, // Show field names on the left
			)
		);
		$cmb2Grid = new \Cmb2Grid\Grid\Cmb2Grid($cmb_demo); //phpcs:ignore WordPress.NamingConventions
		$row      = $cmb2Grid->addRow(); //phpcs:ignore WordPress.NamingConventions
		$url = $cmb_demo->add_field(
			array(
				'name' => \__('Url', S_TEXTDOMAIN),
				'desc' => \__('Url (optional)', S_TEXTDOMAIN),
				'id'   => self::$prefix . S_TEXTDOMAIN . '_url',
				'type' => 'text',
			)
		);

		$row->addColumns(array($url));
	}

	public function add_graphql_fields()
	{
		if (function_exists("register_graphql_fields")) {
			register_graphql_fields('slider', [
				'url' => [
					'type' => 'String',
					'description' => __('The url of the slider', 'wp-graphql'),
					'resolve' => function ($post) {
						$value = get_post_meta($post->ID, self::$prefix . S_TEXTDOMAIN . '_' . "url", true);
						return !empty($value) ? $value : null;
					}
				]
			]);
		}
	}
}
