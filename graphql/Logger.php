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

namespace nextjs_woo_plugin\Graphql;

use nextjs_woo_plugin\Engine\Base;


class Logger extends Base
{

    /**
     * Initialize the class.
     *
     * @return void|bool
     */
    public function initialize()
    {
        parent::initialize();

        add_action('do_graphql_request', array($this, "initialize_logger"), 10, 4);
    }

    /**
     * Registers graphql logger
     *
     * @since 1.0.0
     * @return void
     */
    public function initialize_logger($query, $operation, $variables, $params)
    {
        error_log(wp_json_encode([
            'query' => $query,
            'operation' => $operation,
            'variables' => $variables,
            'params' => $params
        ]));
    }
}
