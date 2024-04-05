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

namespace shop_core_plugin\Integrations;

use shop_core_plugin\Engine\Base;

/**
 * The various Cron of this plugin
 */
class Cron extends Base {

	/**
	 * Initialize the class.
	 *
	 * @return void|bool
	 */
	public function initialize() {
		/*
		 * Load CronPlus
		 */
		$args = array(
			'recurrence'       => 'hourly',
			'schedule'         => 'schedule',
			'name'             => 'hourly_cron',
			'cb'               => array( $this, 'hourly_cron' ),
			'plugin_root_file' => 'shop-core-plugin.php',
		);

		$cronplus = new \CronPlus( $args );
		// Schedule the event
		$cronplus->schedule_event();
		// Remove the event by the schedule
		// $cronplus->clear_schedule_by_hook();
		// Jump the scheduled event
		// $cronplus->unschedule_specific_event();
	}

	/**
	 * Cron Hourly example
	 *
	 * @since 1.0.0
	 * @param int $id The ID.
	 * @return void
	 */
	public function hourly_cron( int $id ) {
		echo \esc_html( (string) $id );
	}

}
