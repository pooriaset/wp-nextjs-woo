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

namespace WpNextJsWoo\Graphql;

use WpNextJsWoo\Engine\Base;


class GraphqlWoo extends Base
{

    /**
     * Initialize the class.
     *
     * @return void|bool
     */
    public function initialize()
    {
        parent::initialize();
        add_filter('graphql_is_generate_woocommerce_session_token', array($this, "graphql_is_generate_woocommerce_session_token"), 10);
    }

    public function graphql_is_generate_woocommerce_session_token()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $headers = getallheaders();
        return  $method !== "OPTIONS" && !isset($headers['x-server-side']);
    }
}
