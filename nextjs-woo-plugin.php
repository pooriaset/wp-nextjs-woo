<?php

/**
 * @package   nextjs_woo_plugin
 * @author    Pooria Setayesh <pooriaset@yahoo.com>
 * @copyright 2022 Shop
 * @license   GPL 2.0+
 * @link      
 *
 * Plugin Name:     nextjs-woo-plugin
 * Plugin URI:      @TODO
 * Description:     @TODO
 * Version:         1.0.0
 * Author:          Pooria Setayesh
 * Author URI:      
 * Text Domain:     nextjs-woo-plugin
 * License:         GPL 2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path:     /languages
 * Requires PHP:    7.4
 * WordPress-Plugin-Boilerplate-Powered: v3.3.0
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	die('We\'re sorry, but you can not directly access this file.');
}

define('S_VERSION', '1.0.0');
define('S_TEXTDOMAIN', 'nextjs-woo-plugin');
define('S_NAME', 'nextjs-woo-plugin');
define('S_PLUGIN_ROOT', plugin_dir_path(__FILE__));
define('S_PLUGIN_ABSOLUTE', __FILE__);
define('S_MIN_PHP_VERSION', '7.4');
define('S_WP_VERSION', '5.3');

add_action(
	'init',
	static function () {
		load_plugin_textdomain(S_TEXTDOMAIN, false, dirname(plugin_basename(__FILE__)) . '/languages');
	}
);

if (version_compare(PHP_VERSION, S_MIN_PHP_VERSION, '<=')) {
	add_action(
		'admin_init',
		static function () {
			deactivate_plugins(plugin_basename(__FILE__));
		}
	);
	add_action(
		'admin_notices',
		static function () {
			echo wp_kses_post(
				sprintf(
					'<div class="notice notice-error"><p>%s</p></div>',
					__('"nextjs-woo-plugin" requires PHP 5.6 or newer.', S_TEXTDOMAIN)
				)
			);
		}
	);

	// Return early to prevent loading the plugin.
	return;
}

$nextjs_woo_plugin_libraries = require S_PLUGIN_ROOT . 'vendor/autoload.php'; //phpcs:ignore

require_once S_PLUGIN_ROOT . 'functions/functions.php';
require_once S_PLUGIN_ROOT . 'functions/debug.php';

// Add your new plugin on the wiki: https://github.com/WPBP/WordPress-Plugin-Boilerplate-Powered/wiki/Plugin-made-with-this-Boilerplate

$requirements = new \Micropackage\Requirements\Requirements(
	'nextjs-woo-plugin',
	array(
		'php'            => S_MIN_PHP_VERSION,
		'php_extensions' => array('mbstring'),
		'wp'             => S_WP_VERSION,
		// 'plugins'            => array(
		// array( 'file' => 'hello-dolly/hello.php', 'name' => 'Hello Dolly', 'version' => '1.5' )
		// ),
	)
);

if (!$requirements->satisfied()) {
	$requirements->print_notice();

	return;
}


/**
 * Create a helper function for easy SDK access.
 *
 * @global type $s_fs
 * @return object
 */
function s_fs()
{
	global $s_fs;

	if (!isset($s_fs)) {
		require_once S_PLUGIN_ROOT . 'vendor/freemius/wordpress-sdk/start.php';
		$s_fs = fs_dynamic_init(
			array(
				'id'             => '',
				'slug'           => 'nextjs-woo-plugin',
				'public_key'     => '',
				'is_live'        => false,
				'is_premium'     => true,
				'has_addons'     => false,
				'has_paid_plans' => true,
				'menu'           => array(
					'slug' => 'nextjs-woo-plugin',
				),
			)
		);

		if ($s_fs->is_premium()) {
			$s_fs->add_filter(
				'support_forum_url',
				static function ($wp_org_support_forum_url) { //phpcs:ignore
					return 'https://your-url.test';
				}
			);
		}
	}

	return $s_fs;
}

// s_fs();

// Documentation to integrate GitHub, GitLab or BitBucket https://github.com/YahnisElsts/plugin-update-checker/blob/master/README.md
Puc_v4_Factory::buildUpdateChecker('https://github.com/user-name/repo-name/', __FILE__, 'unique-plugin-or-theme-slug');

if (!wp_installing()) {
	register_activation_hook(S_TEXTDOMAIN . '/' . S_TEXTDOMAIN . '.php', array(new \nextjs_woo_plugin\Backend\ActDeact, 'activate'));
	register_deactivation_hook(S_TEXTDOMAIN . '/' . S_TEXTDOMAIN . '.php', array(new \nextjs_woo_plugin\Backend\ActDeact, 'deactivate'));
	add_action(
		'plugins_loaded',
		static function () use ($nextjs_woo_plugin_libraries) {
			new \nextjs_woo_plugin\Engine\Initialize($nextjs_woo_plugin_libraries);
		}
	);
}
