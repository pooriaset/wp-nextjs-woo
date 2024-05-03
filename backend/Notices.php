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

use I18n_Notice_WordPressOrg;
use nextjs_woo_plugin\Engine\Base;

/**
 * Everything that involves notification on the WordPress dashboard
 */
class Notices extends Base
{

	/**
	 * Initialize the class
	 *
	 * @return void|bool
	 */
	public function initialize()
	{
		if (!parent::initialize()) {
			return;
		}

		\wpdesk_wp_notice(\__('Updated Messages', S_TEXTDOMAIN), 'updated');

		$builder = new \Page_Madness_Detector(); // phpcs:ignore

		if ($builder->has_entropy()) {
			\wpdesk_wp_notice(\__('A Page Builder/Visual Composer was found on this website!', S_TEXTDOMAIN), 'error', true);
		}

		/*
		 * Review plugin notice.
		 */
		new \WP_Review_Me(
			array(
				'days_after' => 15,
				'type'       => 'plugin',
				'slug'       => S_TEXTDOMAIN,
				'rating'     => 5,
				'message'    => \__('Review me!', S_TEXTDOMAIN),
				'link_label' => \__('Click here to review', S_TEXTDOMAIN),
			)
		);

		/*
		 * Alert after few days to suggest to contribute to the localization if it is incomplete
		 * on translate.wordpress.org, the filter enables to remove globally.
		 */
		if (\apply_filters('nextjs_woo_plugin_alert_localization', true)) {
			new I18n_Notice_WordPressOrg(
				array(
					'textdomain'  => S_TEXTDOMAIN,
					'nextjs_woo_plugin' => S_NAME,
					'hook'        => 'admin_notices',
				),
				true
			);
		}
	}
}
