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
		\add_action('cmb2_init', array($this, 'cmb_demo_metaboxes'));
	}

	/**
	 * Your metabox on Demo CPT
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function cmb_demo_metaboxes()
	{ // phpcs:ignore
		// Start with an underscore to hide fields from custom fields list
		$prefix   = '_demo_';
		$cmb_demo = \new_cmb2_box(
			array(
				'id'           => $prefix . 'metabox',
				'title'        => \__('Demo Metabox', S_TEXTDOMAIN),
				'object_types' => array('demo'),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true, // Show field names on the left
			)
		);
		$cmb2Grid = new \Cmb2Grid\Grid\Cmb2Grid($cmb_demo); //phpcs:ignore WordPress.NamingConventions
		$row      = $cmb2Grid->addRow(); //phpcs:ignore WordPress.NamingConventions
		$field1 = $cmb_demo->add_field(
			array(
				'name' => \__('Text', S_TEXTDOMAIN),
				'desc' => \__('field description (optional)', S_TEXTDOMAIN),
				'id'   => $prefix . S_TEXTDOMAIN . '_text',
				'type' => 'text',
			)
		);
		$field2 = $cmb_demo->add_field(
			array(
				'name' => \__('Text 2', S_TEXTDOMAIN),
				'desc' => \__('field description (optional)', S_TEXTDOMAIN),
				'id'   => $prefix . S_TEXTDOMAIN . '_text2',
				'type' => 'text',
			)
		);

		$field3 = $cmb_demo->add_field(
			array(
				'name' => \__('Text Small', S_TEXTDOMAIN),
				'desc' => \__('field description (optional)', S_TEXTDOMAIN),
				'id'   => $prefix . S_TEXTDOMAIN . '_textsmall',
				'type' => 'text_small',
			)
		);
		$field4 = $cmb_demo->add_field(
			array(
				'name' => \__('Text Small 2', S_TEXTDOMAIN),
				'desc' => \__('field description (optional)', S_TEXTDOMAIN),
				'id'   => $prefix . S_TEXTDOMAIN . '_textsmall2',
				'type' => 'text_small',
			)
		);
		$row->addColumns(array($field1, $field2));
		$row = $cmb2Grid->addRow(); //phpcs:ignore WordPress.NamingConventions
		$row->addColumns(array($field3, $field4));
	}
}
