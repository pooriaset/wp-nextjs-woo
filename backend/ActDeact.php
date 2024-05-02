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

namespace shop_core_plugin\Backend;

use shop_core_plugin\Engine\Base;

/**
 * Activate and deactive method of the plugin and relates.
 */
class ActDeact extends Base
{

	/**
	 * Initialize the class.
	 *
	 * @return void|bool
	 */
	public function initialize()
	{
		if (!parent::initialize()) {
			return;
		}

		// Activate plugin when new blog is added
		\add_action('wpmu_new_blog', array($this, 'activate_new_site'));

		\add_action('admin_init', array($this, 'upgrade_procedure'));
	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @param int $blog_id ID of the new blog.
	 * @since 1.0.0
	 * @return void
	 */
	public function activate_new_site(int $blog_id)
	{
		if (1 !== \did_action('wpmu_new_blog')) {
			return;
		}

		\switch_to_blog($blog_id);
		self::single_activate();
		\restore_current_blog();
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @param bool|null $network_wide True if active in a multiste, false if classic site.
	 * @since 1.0.0
	 * @return void
	 */
	public static function activate($network_wide)
	{
		if (\function_exists('is_multisite') && \is_multisite()) {
			if ($network_wide) {
				// Get all blog ids
				/** @var array<\WP_Site> $blogs */
				$blogs = \get_sites();

				foreach ($blogs as $blog) {
					\switch_to_blog((int) $blog->blog_id);
					self::single_activate();
					\restore_current_blog();
				}

				return;
			}
		}

		self::single_activate();
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @param bool $network_wide True if WPMU superadmin uses
	 * "Network Deactivate" action, false if
	 * WPMU is disabled or plugin is
	 * deactivated on an individual blog.
	 * @since 1.0.0
	 * @return void
	 */
	public static function deactivate(bool $network_wide)
	{
		if (\function_exists('is_multisite') && \is_multisite()) {
			if ($network_wide) {
				// Get all blog ids
				/** @var array<\WP_Site> $blogs */
				$blogs = \get_sites();

				foreach ($blogs as $blog) {
					\switch_to_blog((int) $blog->blog_id);
					self::single_deactivate();
					\restore_current_blog();
				}

				return;
			}
		}

		self::single_deactivate();
	}

	/**
	 * Add admin capabilities
	 *
	 * @return void
	 */
	public static function add_capabilities()
	{
		// Add the capabilites to all the roles
		$caps  = array(
			'create_plugins',
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
		);
		$roles = array(
			\get_role('administrator'),
			\get_role('editor'),
			\get_role('author'),
			\get_role('contributor'),
			\get_role('subscriber'),
		);

		foreach ($roles as $role) {
			foreach ($caps as $cap) {
				if (\is_null($role)) {
					continue;
				}

				$role->add_cap($cap);
			}
		}
	}

	/**
	 * Remove capabilities to specific roles
	 *
	 * @return void
	 */
	public static function remove_capabilities()
	{
		// Remove capabilities to specific roles
		$bad_caps = array(
			'create_sliders',
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
		);
		$roles    = array(
			\get_role('author'),
			\get_role('contributor'),
			\get_role('subscriber'),
		);

		foreach ($roles as $role) {
			foreach ($bad_caps as $cap) {
				if (\is_null($role)) {
					continue;
				}

				$role->remove_cap($cap);
			}
		}
	}

	/**
	 * Upgrade procedure
	 *
	 * @return void
	 */
	public static function upgrade_procedure()
	{
		if (!\is_admin()) {
			return;
		}

		$version = \strval(\get_option('shop-core-plugin-version'));

		if (!\version_compare(S_VERSION, $version, '>')) {
			return;
		}

		\update_option('shop-core-plugin-version', S_VERSION);
		\delete_option(S_TEXTDOMAIN . '_fake-meta');
	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function single_activate()
	{
		// @TODO: Define activation functionality here
		// add_role( 'advanced', __( 'Advanced' ) ); //Add a custom roles
		self::add_capabilities();
		self::upgrade_procedure();
		// Clear the permalinks
		\flush_rewrite_rules();
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function single_deactivate()
	{
		// @TODO: Define deactivation functionality here
		self::remove_capabilities();
		// Clear the permalinks
		\flush_rewrite_rules();
	}
}
