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


class TotalCount extends Base
{

    /**
     * Initialize the class.
     *
     * @return void|bool
     */
    public function initialize()
    {
        parent::initialize();

        if (class_exists('WPGraphQL')) {
            add_filter('graphql_connection_page_info', array($this, 'resolve_total_field'), 10, 2);
            add_filter('graphql_connection_query_args', array($this, 'count_total_rows'));
            add_filter('graphql_register_types', array($this, 'register_total_field'));
        }
    }

    /**
     * Resolve the total field.
     *
     * @param $page_info
     * @param $connection
     *
     * @return mixed
     */
    public function resolve_total_field($page_info, $connection)
    {
        $page_info['total'] = null;
        if ($connection->get_query() instanceof \WP_Query) {
            if (isset($connection->get_query()->found_posts)) {
                $page_info['total'] = (int) $connection->get_query()->found_posts;
            }
        } elseif ($connection->get_query() instanceof \WP_User_Query) {
            if (isset($connection->get_query()->total_users)) {
                $page_info['total'] = (int) $connection->get_query()->total_users;
            }
        }

        return $page_info;
    }

    /**
     * Tell the underlying WP_Query to count the total number of rows.
     *
     * @param $args
     *
     * @return mixed
     */
    public function count_total_rows($args)
    {
        $args['no_found_rows'] = false;
        $args['count_total']   = true;

        return $args;
    }

    /**
     * Register a total field for queries.
     */
    public function register_total_field()
    {
        register_graphql_field('WPPageInfo', 'total', [
            'type' => 'Int',
        ]);
    }
}
