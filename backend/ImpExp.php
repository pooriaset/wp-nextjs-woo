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

namespace WpNextJsWoo\Backend;

use WP_Query;
use WpNextJsWoo\Engine\Base;
use WpNextJsWoo\Woocommerce\CustomMetaData;

/**
 * Provide Import and Export of the settings of the plugin
 */
class ImpExp extends Base
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

		if (!\current_user_can('manage_options')) {
			return;
		}

		// Add the export settings method
		\add_action('admin_init', array($this, 'settings_export'));
		// Add the import settings method
		\add_action('admin_init', array($this, 'settings_import'));
		// Add the update metadata methid
		\add_action('admin_init', array($this, 'upadte_product_metadata'));
	}

	/**
	 * upadte product metadata
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function upadte_product_metadata()
	{
		if (
			empty($_POST['s_action']) || //phpcs:ignore WordPress.Security.NonceVerification
			'update_product_metadata' !== \sanitize_text_field(\wp_unslash($_POST['s_action'])) //phpcs:ignore WordPress.Security.NonceVerification
		) {
			return;
		}

		if (!\wp_verify_nonce(\sanitize_text_field(\wp_unslash($_POST['s_update_nonce'])), 's_update_nonce')) { //phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			return;
		}

		// $args = array(
		// 	'post_type' => 'product',
		// 	'posts_per_page' => -1
		// );

		// $products = new WP_Query($args);

		CustomMetaData::calculate_and_save_discounts(292);

		exit; // phpcs:ignore
	}


	/**
	 * Process a settings export from config
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function settings_export()
	{
		if (
			empty($_POST['s_action']) || //phpcs:ignore WordPress.Security.NonceVerification
			'export_settings' !== \sanitize_text_field(\wp_unslash($_POST['s_action'])) //phpcs:ignore WordPress.Security.NonceVerification
		) {
			return;
		}

		if (!\wp_verify_nonce(\sanitize_text_field(\wp_unslash($_POST['s_export_nonce'])), 's_export_nonce')) { //phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			return;
		}

		$settings      = array();
		$settings[0] = \get_option(S_TEXTDOMAIN . '-settings');
		$settings[1] = \get_option(S_TEXTDOMAIN . '-settings-second');

		\ignore_user_abort(true);

		\nocache_headers();
		\header('Content-Type: application/json; charset=utf-8');
		\header('Content-Disposition: attachment; filename=WpNextJsWoo-settings-export-' . \gmdate('m-d-Y') . '.json');
		\header('Expires: 0');

		echo \wp_json_encode($settings, JSON_PRETTY_PRINT);

		exit; // phpcs:ignore
	}

	/**
	 * Process a settings import from a json file
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function settings_import()
	{
		if (
			empty($_POST['s_action']) || //phpcs:ignore WordPress.Security.NonceVerification
			'import_settings' !== \sanitize_text_field(\wp_unslash($_POST['s_action'])) //phpcs:ignore WordPress.Security.NonceVerification
		) {
			return;
		}

		if (!\wp_verify_nonce(\sanitize_text_field(\wp_unslash($_POST['s_import_nonce'])), 's_import_nonce')) { //phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			return;
		}

		if (!isset($_FILES['s_import_file']['name'])) {
			return;
		}

		$file_name_parts = \explode('.', $_FILES['s_import_file']['name']); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$extension       = \end($file_name_parts);

		if ('json' !== $extension) {
			\wp_die(\esc_html__('Please upload a valid .json file', S_TEXTDOMAIN));
		}

		$import_file = $_FILES['s_import_file']['tmp_name']; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput

		if (empty($import_file)) {
			\wp_die(\esc_html__('Please upload a file to import', S_TEXTDOMAIN));
		}

		// Retrieve the settings from the file and convert the json object to an array.
		$settings_file = file_get_contents($import_file); // phpcs:ignore

		if ($settings_file !== false) {
			$settings = \json_decode((string) $settings_file);

			if (\is_array($settings)) {
				\update_option(S_TEXTDOMAIN . '-settings', \get_object_vars($settings[0]));
				\update_option(S_TEXTDOMAIN . '-settings-second', \get_object_vars($settings[1]));
			}

			\wp_safe_redirect(\admin_url('options-general.php?page=' . S_TEXTDOMAIN));
			exit;
		}

		new \WP_Error(
			'nextjs_woo_plugin_import_settings_failed',
			\__('Failed to import the settings.', S_TEXTDOMAIN)
		);
	}
}
