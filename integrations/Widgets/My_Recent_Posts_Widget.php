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

namespace WpNextJsWoo\Integrations\Widgets;

/**
 * Create custom widget class extending WPH_Widget
 */
class My_Recent_Posts_Widget extends \WPH_Widget
{

	/**
	 * Initialize the widget
	 *
	 * @return void
	 */
	public function __construct()
	{ // phpcs:ignore
		$args = array(
			'label'       => \__('My Recent Posts Example', S_TEXTDOMAIN),
			'description' => \__('My Recent Posts Widget Description', S_TEXTDOMAIN),
			'slug'        => 'recent-posts',
			// 'options' => array( 'cache' => true )
		);

		$args['fields'] = array(
			// Title field
			array(
				// Field name/label
				'name'     => \__('Title', S_TEXTDOMAIN),
				// Field description
				'desc'     => \__('Enter the widget title.', S_TEXTDOMAIN),
				// Field id
				'id'       => 'title',
				// Field type ( text, checkbox, textarea, select, select-group, taxonomy, taxonomyterm, pages, hidden )
				'type'     => 'text',
				// Class, rows, cols
				'class'    => 'widefat',
				// Default value
				'std'      => \__('Recent Posts', S_TEXTDOMAIN),
				/**
				Set the field validation type/s
				'alpha_dash'
				Returns FALSE if the value contains anything other than alpha-numeric characters, underscores or dashes.
				'alpha'
				Returns FALSE if the value contains anything other than alphabetical characters.
				'alpha_numeric'
				Returns FALSE if the value contains anything other than alpha-numeric characters.
				'numeric'
				Returns FALSE if the value contains anything other than numeric characters.
				'boolean'
				Returns FALSE if the value contains anything other than a boolean value ( true or false ).
				You can define custom validation methods. Make sure to return a boolean ( TRUE/FALSE ).
				Example:
				'validate' => 'my_custom_validation',
				Will call for: $this->my_custom_validation( $value_to_validate );
				 */
				'validate' => 'alpha_dash',
				/**
				Filter data before entering the DB
				strip_tags ( default )
				wp_strip_all_tags
				esc_attr
				esc_url
				esc_textarea
				 */
				'filter'   => 'strip_tags|esc_attr',
			),
			// Taxonomy Field
			array(
				'name'  => \__('Taxonomy', S_TEXTDOMAIN),
				'desc'  => \__('Set the taxonomy.', S_TEXTDOMAIN),
				'id'    => 'taxonomy',
				'type'  => 'taxonomy',
				'class' => 'widefat',
			),
			// Taxonomy Field
			array(
				'name'     => \__('Taxonomy terms', S_TEXTDOMAIN),
				'desc'     => \__('Set the taxonomy terms.', S_TEXTDOMAIN),
				'id'       => 'taxonomyterm',
				'type'     => 'taxonomyterm',
				'taxonomy' => 'category',
				'class'    => 'widefat',
			),
			// Pages Field
			array(
				'name'  => \__('Pages', S_TEXTDOMAIN),
				'desc'  => \__('Set the page.', S_TEXTDOMAIN),
				'id'    => 'pages',
				'type'  => 'pages',
				'class' => 'widefat',
			),
			// Post type Field
			array(
				'name'     => \__('Post type', S_TEXTDOMAIN),
				'desc'     => \__('Set the post type.', S_TEXTDOMAIN),
				'id'       => 'posttype',
				'type'     => 'posttype',
				'posttype' => 'post',
				'class'    => 'widefat',
			),
			// Amount Field
			array(
				'name'     => \__('Amount', S_TEXTDOMAIN),
				'desc'     => \__('Select how many posts to show.', S_TEXTDOMAIN),
				'id'       => 'amount',
				'type'     => 'select',
				// Selectbox fields
				'fields'   => array(
					array(
						'name'  => \__('1 Post', S_TEXTDOMAIN),
						'value' => '1',
					),
					array(
						'name'  => \__('2 Posts', S_TEXTDOMAIN),
						'value' => '2',
					),
					array(
						'name'  => \__('3 Posts', S_TEXTDOMAIN),
						'value' => '3',
					),

					// Add more options
				),
				'validate' => 'my_custom_validation',
				'filter'   => 'strip_tags|esc_attr',
			),
			// Output type checkbox
			array(
				'name'   => \__('Output as list', S_TEXTDOMAIN),
				'desc'   => \__('Wraps posts with the <li> tag.', S_TEXTDOMAIN),
				'id'     => 'list',
				'type'   => 'checkbox',
				// Checked by default:
				'std'    => 1, // 0 or 1
				'filter' => 'strip_tags|esc_attr',
			),
		);
		// Create widget
		$this->create_widget($args);
	}

	/**
	 * Custom validation for this widget
	 *
	 * @since 1.0.0
	 * @param string $value The text.
	 * @return bool
	 */
	public function my_custom_validation(string $value)
	{
		return \strlen($value) <= 1;
	}

	/**
	 * Output function
	 *
	 * @since 1.0.0
	 * @param array $args     The argument shared to the output from WordPress.
	 * @param array $instance The settings saved.
	 * @return void
	 */
	public function widget($args, $instance)
	{ //phpcs:ignore
		$out = $args['before_widget'];
		// And here do whatever you want
		$out .= $args['before_title'];
		$out .= $instance['title'];
		$out .= $args['after_title'];

		// Here you would get the most recent posts based on the selected amount: $instance['amount']
		// Then return those posts on the $out variable ready for the output
		$out .= '<p>Hey There! </p>';

		$out .= $args['after_widget'];
		echo $out; // phpcs:ignore
	}

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public function initialize()
	{
		\add_action(
			'widgets_init',
			static function () {
				\register_widget('WpNextJsWoo\Integrations\Widgets\My_Recent_Posts_Widget');
			}
		);
	}
}
