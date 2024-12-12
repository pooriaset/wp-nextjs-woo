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
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\WooCommerce\Data\Connection\Product_Attribute_Connection_Resolver;

class CustomProductAttributeConnection extends Base
{
	/**
	 * Initialize the class.
	 *
	 * @return void|bool
	 */
	public function initialize()
	{
		parent::initialize();
		// From Product to CustomProductAttribute.
		register_graphql_connection(
			self::get_connection_config()
		);

		// From Product to LocalProductAttribute.
		register_graphql_connection(
			self::get_connection_config(
				[
					'toType'         => 'LocalProductAttribute',
					'fromFieldName'  => 'localAttributes',
					'connectionArgs' => [],
				]
			)
		);

		// From Product to GlobalProductAttribute.
		register_graphql_connection(
			self::get_connection_config(
				[
					'toType'         => 'GlobalProductAttribute',
					'fromFieldName'  => 'globalAttributes',
					'connectionArgs' => [],
				]
			)
		);
	}

	/**
	 * Given an array of $args, this returns the connection config, merging the provided args
	 * with the defaults.
	 *
	 * @param array $args - Connection configuration.
	 * @return array
	 */
	public static function get_connection_config($args = []): array
	{
		return array_merge(
			[
				'fromType'       => 'Product',
				'toType'         => 'CustomProductAttribute',
				'fromFieldName'  => 'customAttributes',
				'connectionArgs' => self::get_connection_args(),
				'resolve'        => static function ($source, array $args, AppContext $context, ResolveInfo $info) {
					$resolver = new Product_Attribute_Connection_Resolver();
					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					switch ($info->fieldName) {
						case 'globalAttributes':
							return $resolver->resolve($source, $args, $context, $info, 'global');
						case 'localAttributes':
							return $resolver->resolve($source, $args, $context, $info, 'local');
						default:
							return $resolver->resolve($source, $args, $context, $info);
					}
				},
			],
			$args
		);
	}

	/**
	 * Returns array of where args.
	 *
	 * @return array
	 */
	public static function get_connection_args(): array
	{
		return [
			'type' => [
				'type'        => 'ProductAttributeTypesEnum',
				'description' => __('Filter results by attribute scope.', 'wp-graphql-woocommerce'),
			],
		];
	}
}
