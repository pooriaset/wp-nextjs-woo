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

namespace nextjs_woo_plugin\Internals;

use DecodeLabs\Tagged as Html;
use nextjs_woo_plugin\Engine\Base;

/**
 * Shortcodes of this plugin
 */
class Shortcode extends Base
{

	/**
	 * Initialize the class.
	 *
	 * @return void|bool
	 */
	public function initialize()
	{
		parent::initialize();

		\add_shortcode('foobar', array($this, 'foobar_func'));
	}

	/**
	 * Shortcode example
	 *
	 * @param array $atts Parameters.
	 * @since 1.0.0
	 * @return string
	 */
	public static function foobar_func(array $atts)
	{
		\shortcode_atts(array('foo' => 'something', 'bar' => 'something else'), $atts);

		return Html::{'span.foo'}('foo = ' . $atts['foo']) . Html::{'span.bar'}('bar = ' . $atts['bar']);
	}
}
