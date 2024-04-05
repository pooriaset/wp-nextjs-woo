<?php
/**
 * shop_core_plugin
 *
 * @package   shop_core_plugin
 * @author    Pooria Setayesh <pooriaset@yahoo.com>
 * @copyright 2022 Shop
 * @license   GPL 2.0+
 * @link      
 */

/**
 * Get the settings of the plugin in a filterable way
 *
 * @since 1.0.0
 * @return array
 */
function s_get_settings() {
	return apply_filters( 's_get_settings', get_option( S_TEXTDOMAIN . '-settings' ) );
}
