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

namespace nextjs_woo_plugin\Frontend\Extras;

use nextjs_woo_plugin\Engine\Base;

/**
 * Add custom css class to <body>
 */
class Body_Class extends Base
{

	/**
	 * Initialize the class.
	 *
	 * @return void|bool
	 */
	public function initialize()
	{
		parent::initialize();

		\add_filter('body_class', array(self::class, 'add_s_class'), 10, 1);
	}

	/**
	 * Add class in the body on the frontend
	 *
	 * @param array $classes The array with all the classes of the page.
	 * @since 1.0.0
	 * @return array
	 */
	public static function add_s_class(array $classes)
	{
		$classes[] = S_TEXTDOMAIN;

		return $classes;
	}
}
