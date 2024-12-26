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

namespace WpNextJsWoo\Engine;

/**
 * Base skeleton of the plugin
 */
class Base
{

	/**
	 * @var array The settings of the plugin.
	 */
	public $settings = array();

	/**
	 * Initialize the class and get the plugin settings
	 *
	 * @return bool
	 */
	public function initialize()
	{
		$this->settings = \s_get_settings();
		// add_action('wp', array($this, 'headlesswp_frontend_redirect'));

		return true;
	}

	/**
	 * Die if we try to access a page or the front page
	 *
	 * @return void
	 */
	public static function headlesswp_frontend_redirect()
	{
		if (! is_admin()) {

			/**
			 * Fetch the IDs of the post, page or blog page
			 */
			$post_ID     = get_the_id();
			$homepage_id = get_option('page_on_front');
			$blogpage_id = get_option('page_for_posts');

			/**
			 * Do a wp_die so we can't access the site
			 */
			if ($homepage_id === $post_ID || $blogpage_id === $post_ID || is_front_page()) {
				wp_die('This site is not accessible');
				exit;
			} else {

				/**
				 * Else do a redirect back to WP admin again
				 */

				$post_edit_link = admin_url('post.php?post=' . $post_ID . '&action=edit');

				if (is_user_logged_in()) {
					/**
					 * Logged in users go to the post edit screen
					 */
					wp_safe_redirect($post_edit_link);
					exit;
				} else {
					/**
					 * Not logged in? Redirect to login page
					 */
					wp_safe_redirect(wp_login_url($post_edit_link));
					exit;
				}
			}
		}
	}
}
