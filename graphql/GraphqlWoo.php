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
        add_filter('graphql_generate_woocommerce_session_token_condition', array($this, "graphql_generate_woocommerce_session_token_condition"), 10);
        add_filter('wc_session_expiring', array($this, "wc_session_expiring"), 10);
        add_filter('wc_session_expiration', array($this, "wc_session_expiration"), 10);
    }

    /**
     * Condition of generating new token.
     *
     * @return bool
     */
    public function graphql_generate_woocommerce_session_token_condition()
    {
        $headers = getallheaders();
        return  isset($headers['x-generate-session']);
    }

    /**
     * Woocommerce session expiring.
     *
     * @return int
     */
    public function wc_session_expiring()
    {
        return time() +   (60 * 60 * 24 * 7) - (1 * 60 * 60);
    }


    /**
     * Woocommerce session expiration.
     *
     * @return int
     */
    public function wc_session_expiration()
    {
        // 7 days
        return time() +   60 * 60 * 24 * 7;
    }
}
