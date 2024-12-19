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

namespace WpNextJsWoo\Rest;

use WpNextJsWoo\Engine\Base;

/**
 * Example class for REST
 */
class Example extends Base
{

	/**
	 * Initialize the class.
	 *
	 * @return void|bool
	 */
	public function initialize()
	{
		parent::initialize();

		\add_action('rest_api_init', array($this, 'add_custom_stuff'));
	}

	/**
	 * Examples
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_custom_stuff()
	{
		$this->add_custom_ruote();
	}

	/**
	 * Examples
	 *
	 * @since 1.0.0
	 *
	 *  Make an instance of this class somewhere, then
	 *  call this method and test on the command line with
	 * `curl http://example.com/wp-json/wp/v2/calc?first=1&second=2`
	 * @return void
	 */
	public function add_custom_ruote()
	{
		// Only an example with 2 parameters
		\register_rest_route(
			'wp/v2',
			'demo',
			array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array($this, 'demo'),
				'permission_callback' => function () {
					return current_user_can("administrator");
				},
			)
		);
	}

	/**
	 * Examples
	 *
	 * @since 1.0.0
	 * @param \WP_REST_Request<array> $request Values.
	 * @return array
	 */
	public function demo(\WP_REST_Request $request)
	{
		return ["Hello World!"];
	}
}
