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

namespace WpNextJsWoo\Ajax;

use WpNextJsWoo\Engine\Base;

/**
 * AJAX in the public
 */
class Ajax extends Base
{

	/**
	 * Initialize the class.
	 *
	 * @return void|bool
	 */
	public function initialize()
	{
		if (!\apply_filters('nextjs_woo_plugin_s_ajax_initialize', true)) {
			return;
		}

		// For not logged user
		\add_action('wp_ajax_nopriv_your_method', array($this, 'your_method'));
	}

	/**
	 * The method to run on ajax
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function your_method()
	{
		$return = array(
			'message' => 'Saved',
			'ID'      => 1,
		);

		\wp_send_json_success($return);
		// wp_send_json_error( $return );
	}
}
