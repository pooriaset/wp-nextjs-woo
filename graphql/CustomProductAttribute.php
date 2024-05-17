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


class CustomProductAttribute extends Base
{

    /**
     * Initialize the class.
     *
     * @return void|bool
     */
    public function initialize()
    {
        parent::initialize();

        register_graphql_interface_type(
            'CustomProductAttribute',
            [
                'description' => __('Product attribute object', 'wp-graphql-woocommerce'),
                'interfaces'  => ['Node'],
                'fields'      => self::get_fields(),
                'resolveType' => static function ($value) {
                    $type_registry = \WPGraphQL::get_type_registry();
                    if ($value->is_taxonomy()) {
                        return $type_registry->get_type('GlobalProductAttribute');
                    } else {
                        return $type_registry->get_type('LocalProductAttribute');
                    }
                },
            ]
        );
    }

    public static function get_fields()
    {
        return [
            'id'          => [
                'type'        => ['non_null' => 'ID'],
                'description' => __('Attribute Global ID', 'wp-graphql-woocommerce'),
            ],
            'attributeId' => [
                'type'        => ['non_null' => 'Int'],
                'description' => __('Attribute ID', 'wp-graphql-woocommerce'),
                'resolve'     => static function ($attribute) {
                    return !is_null($attribute->get_id()) ? $attribute->get_id() : null;
                },
            ],
            'name'        => [
                'type'        => 'String',
                'description' => __('Attribute name', 'wp-graphql-woocommerce'),
                'resolve'     => static function ($attribute) {
                    return !empty($attribute->get_name()) ? $attribute->get_name() : null;
                },
            ],
            'label'       => [
                'type'        => 'String',
                'description' => __('Attribute label', 'wp-graphql-woocommerce'),
                'resolve'     => static function ($attribute) {
                    return !empty($attribute->get_name()) ? ucwords(preg_replace('/(-|_)/', ' ', $attribute->get_name())) : null;
                },
            ],
            'options'     => [
                'type'        => ['list_of' => 'String'],
                'description' => __('Attribute options', 'wp-graphql-woocommerce'),
                'resolve'     => static function ($attribute) {
                    $slugs = $attribute->get_slugs();
                    return !empty($slugs) ? $slugs : null;
                },
            ],
            'optionNames'     => [
                'type'        => ['list_of' => 'String'],
                'description' => __('Attribute option names', 'wp-graphql-woocommerce'),
                'resolve'     => static function ($attribute) {
                    $terms = $attribute->get_terms();
                    $options = [];
                    foreach ($terms as $term) {
                        array_push($options, $term->name);
                    }

                    return $options;
                },
            ],
            'position'    => [
                'type'        => 'Int',
                'description' => __('Attribute position', 'wp-graphql-woocommerce'),
                'resolve'     => static function ($attribute) {
                    return !is_null($attribute->get_position()) ? $attribute->get_position() : null;
                },
            ],
            'visible'     => [
                'type'        => 'Boolean',
                'description' => __('Is attribute visible', 'wp-graphql-woocommerce'),
                'resolve'     => static function ($attribute) {
                    return !is_null($attribute->get_visible()) ? $attribute->get_visible() : null;
                },
            ],
            'variation'   => [
                'type'        => 'Boolean',
                'description' => __('Is attribute on product variation', 'wp-graphql-woocommerce'),
                'resolve'     => static function ($attribute) {
                    return !is_null($attribute->get_variation()) ? $attribute->get_variation() : null;
                },
            ],
            'scope'       => [
                'type'        => ['non_null' => 'ProductAttributeTypesEnum'],
                'description' => __('Product attribute scope.', 'wp-graphql-woocommerce'),
                'resolve'     => static function ($attribute) {
                    return $attribute->is_taxonomy() ? 'global' : 'local';
                },
            ],
        ];
    }
}
