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

namespace nextjs_woo_plugin\Backend;

use nextjs_woo_plugin\Engine\Base;

/**
 * Activate and deactive method of the plugin and relates.
 */
class Caps extends Base
{
    public static $caps = array(
        'create_plugins',

        // Sliders
        'read_slider',
        'read_private_sliders',
        'edit_slider',
        'edit_sliders',
        'edit_private_sliders',
        'edit_published_sliders',
        'edit_others_sliders',
        'publish_sliders',
        'delete_slider',
        'delete_sliders',
        'delete_private_sliders',
        'delete_published_sliders',
        'delete_others_sliders',
        'manage_sliders',

        // Size
        'read_size',
        'read_private_sizes',
        'edit_size',
        'edit_sizes',
        'edit_private_sizes',
        'edit_published_sizes',
        'edit_others_sizes',
        'publish_sizes',
        'delete_size',
        'delete_sizes',
        'delete_private_sizes',
        'delete_published_sizes',
        'delete_others_sizes',
        'manage_sizes',
    );
}
