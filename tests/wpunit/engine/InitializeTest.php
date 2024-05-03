<?php

namespace nextjs_woo_plugin\Tests\WPUnit;

use Inpsyde\WpContext;

class InitializeTest extends \Codeception\TestCase\WPTestCase
{
	/**
	 * @var string
	 */
	protected $root_dir;

	public function setUp(): void
	{
		parent::setUp();

		// your set up methods here
		$this->root_dir = dirname(dirname(dirname(__FILE__)));

		wp_set_current_user(0);
		wp_logout();
		wp_safe_redirect(wp_login_url());
	}

	public function tearDown(): void
	{
		parent::tearDown();
	}

	/**
	 * @test
	 * it should be front
	 */
	public function it_should_be_front()
	{
		do_action('plugins_loaded');

		$classes   = array();
		$classes[] = 'nextjs_woo_plugin\Internals\PostTypes';
		$classes[] = 'nextjs_woo_plugin\Internals\Shortcode';
		$classes[] = 'nextjs_woo_plugin\Internals\Transient';
		$classes[] = 'nextjs_woo_plugin\Integrations\CMB';
		$classes[] = 'nextjs_woo_plugin\Integrations\Cron';
		$classes[] = 'nextjs_woo_plugin\Integrations\Template';
		$classes[] = 'nextjs_woo_plugin\Integrations\Widgets\My_Recent_Posts_Widget';
		$classes[] = 'nextjs_woo_plugin\Frontend\Enqueue';
		$classes[] = 'nextjs_woo_plugin\Frontend\Extras\Body_Class';

		$all_classes = get_declared_classes();
		foreach ($classes as $class) {
			$this->assertTrue(in_array($class, $all_classes));
		}
	}

	/**
	 * @test
	 * it should be ajax
	 */
	public function it_should_be_ajax()
	{
		add_filter('wp_doing_ajax', '__return_true');
		do_action('plugins_loaded');

		$classes   = array();
		$classes[] = 'nextjs_woo_plugin\Ajax\Ajax';
		$classes[] = 'nextjs_woo_plugin\Ajax\Ajax_Admin';

		$all_classes = get_declared_classes();
		foreach ($classes as $class) {
			$this->assertTrue(in_array($class, $all_classes));
		}
	}
}
