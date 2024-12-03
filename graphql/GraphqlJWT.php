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


class GraphqlJWT extends Base
{

    /**
     * Initialize the class.
     *
     * @return void|bool
     */
    public function initialize()
    {
        parent::initialize();
        add_filter('graphql_jwt_auth_expire', array($this, "custom_jwt_expiration"), 10);
    }

    public function custom_jwt_expiration($expiration)
    {
        return 1 * 24 * 60 * 60;
    }
}
