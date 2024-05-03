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

use nextjs_woo_plugin\Engine\Base;

/**
 * Block of this plugin
 */
class Block extends Base
{

	/**
	 * Initialize the class.
	 *
	 * @return void|bool
	 */
	public function initialize()
	{
		parent::initialize();

		\add_action('init', array($this, 'register_block'));
	}

	/**
	 * Registers and enqueue the block assets
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_block()
	{
		// Register the block by passing the location of block.json to register_block_type.
		$json = \S_PLUGIN_ROOT . 'assets/block.json';

		\register_block_type($json);
	}
}
