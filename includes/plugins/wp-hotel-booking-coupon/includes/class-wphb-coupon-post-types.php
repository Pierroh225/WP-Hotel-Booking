<?php

/**
 * WP Hotel Booking Coupon custom post type class.
 *
 * @class       WPHB_Coupon_Post_Types
 * @version     2.0
 * @package     WP_Hotel_Booking_Coupon/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_Coupon_Post_Types' ) ) {

	/**
	 * Class WPHB_Coupon_Post_Types.
	 *
	 * @since 2.0
	 */
	class WPHB_Coupon_Post_Types {

		/**
		 * WPHB_Coupon_Post_Types constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'register_post_types' ) );

			// custom coupon columns
			add_filter( 'manage_hb_coupon_posts_columns', array( $this, 'custom_coupon_columns' ) );
			add_action( 'manage_hb_coupon_posts_custom_column', array( $this, 'custom_coupon_columns_filter' ) );

			// coupon meta box
			add_action( 'admin_init', array( $this, 'coupon_meta_boxes' ), 50 );
			// update coupon meta box
			add_filter( 'hb_meta_box_update_meta_value', array( $this, 'update_coupon_date_meta' ), 10, 3 );
		}

		/**
		 * Register custom post types.
		 *
		 * @since 2.0
		 */
		public function register_post_types() {

			/**
			 * Register coupon custom post type.
			 */
			$args = array(
				'labels'             => array(
					'name'               => _x( 'Coupons', 'Coupons', 'wphb-coupon' ),
					'singular_name'      => _x( 'Coupon', 'Coupon', 'wphb-coupon' ),
					'menu_name'          => __( 'Coupons', 'wphb-coupon' ),
					'parent_item_colon'  => __( 'Parent Item:', 'wphb-coupon' ),
					'all_items'          => __( 'Coupons', 'wphb-coupon' ),
					'view_item'          => __( 'View Coupon', 'wphb-coupon' ),
					'add_new_item'       => __( 'Add New Coupon', 'wphb-coupon' ),
					'add_new'            => __( 'Add New', 'wphb-coupon' ),
					'edit_item'          => __( 'Edit Coupon', 'wphb-coupon' ),
					'update_item'        => __( 'Update Coupon', 'wphb-coupon' ),
					'search_items'       => __( 'Search Coupon', 'wphb-coupon' ),
					'not_found'          => __( 'No coupon found', 'wphb-coupon' ),
					'not_found_in_trash' => __( 'No coupon found in Trash', 'wphb-coupon' ),
				),
				'public'             => false,
				'query_var'          => true,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'has_archive'        => false,
				'capability_type'    => 'hb_room',
				'map_meta_cap'       => true,
				'show_in_menu'       => 'tp_hotel_booking',
				'show_in_admin_bar'  => true,
				'show_in_nav_menus'  => true,
				'supports'           => array( 'title' ),
				'hierarchical'       => false
			);
			$args = apply_filters( 'hotel_booking_register_post_type_coupon_arg', $args );

			register_post_type( 'hb_coupon', $args );
		}

		/**
		 * Custom coupon post type columns.
		 *
		 * @since 2.0
		 *
		 * @param $columns
		 *
		 * @return mixed
		 */
		public function custom_coupon_columns( $columns ) {
			$columns['type']             = __( 'Type', 'wphb-coupon' );
			$columns['from']             = __( 'Validate From', 'wphb-coupon' );
			$columns['to']               = __( 'Validate To', 'wphb-coupon' );
			$columns['minimum_spend']    = __( 'Minimum spend', 'wphb-coupon' );
			$columns['maximum_spend']    = __( 'Maximum spend', 'wphb-coupon' );
			$columns['limit_per_coupon'] = __( 'Usage', 'wphb-coupon' );
			unset( $columns['date'] );

			return $columns;
		}

		/**
		 * Custom coupon post type column content.
		 *
		 * @since 2.0
		 *
		 * @param $column
		 */
		public function custom_coupon_columns_filter( $column ) {
			global $post;
			switch ( $column ) {
				case 'type':
					switch ( get_post_meta( $post->ID, '_hb_coupon_discount_type', true ) ) {
						case 'fixed_cart':
							_e( 'Fixed cart', 'wphb-coupon' );
							break;
						case 'percent_cart':
							_e( 'Percent cart', 'wphb-coupon' );
							break;
					}
					break;
				case 'from':
				case 'to':
					if ( $from = get_post_meta( $post->ID, '_hb_coupon_date_' . $column, true ) ) {
						echo date_i18n( hb_get_date_format(), $from );
					} else {
						echo '-';
					}
					break;
				case 'minimum_spend':
				case 'maximum_spend':
					if ( $value = get_post_meta( $post->ID, '_hb_' . $column, true ) ) {
						if ( get_post_meta( $post->ID, '_hb_coupon_discount_type', true ) == 'fixed_cart' ) {
							echo hb_format_price( $value );
						} else {
							echo sprintf( '%s', $value . '%' );
						}
					} else {
						echo '-';
					}
					break;
				case 'limit_per_coupon':
					echo intval( get_post_meta( $post->ID, '_hb_usage_count', true ) ) . ' / ';
					if ( $value = get_post_meta( $post->ID, '_hb_' . $column, true ) ) {
						echo sprintf( '%s', $value );
					} else {
						echo __( 'Unlimited', 'wphb-coupon' );
					}
			}
		}


		/**
		 * Coupon meta box settings.
		 *
		 * @since 2.0
		 */
		public function coupon_meta_boxes() {
			if ( class_exists( 'WPHB_Meta_Box' ) ) {
				// coupon meta box
				WPHB_Meta_Box::instance(
					'coupon_settings',
					array(
						'title'           => __( 'Coupon Settings', 'wphb-coupon' ),
						'post_type'       => 'hb_coupon',
						'meta_key_prefix' => '_hb_',
						'context'         => 'normal',
						'priority'        => 'high'
					),
					array()
				)->add_field(
					array(
						'name'  => 'coupon_description',
						'label' => __( 'Description', 'wphb-coupon' ),
						'type'  => 'textarea',
						'std'   => ''
					),
					array(
						'name'    => 'coupon_discount_type',
						'label'   => __( 'Discount type', 'wphb-coupon' ),
						'type'    => 'select',
						'std'     => 'fixed_cart',
						'options' => array(
							'fixed_cart'   => __( 'Cart discount', 'wphb-coupon' ),
							'percent_cart' => __( 'Cart % discount', 'wphb-coupon' )
						)
					),
					array(
						'name'  => 'coupon_discount_value',
						'label' => __( 'Discount value', 'wphb-coupon' ),
						'type'  => 'number',
						'std'   => '',
						'min'   => 0,
						'step'  => 0.1
					),
					array(
						'name'   => 'coupon_date_from',
						'label'  => __( 'Validate from', 'wphb-coupon' ),
						'type'   => 'datetime',
						'filter' => array( $this, 'get_coupon_date_meta_box_field' )
					),
					array(
						'name'  => 'coupon_date_from_timestamp',
						'label' => '',
						'type'  => 'hidden'
					),
					array(
						'name'   => 'coupon_date_to',
						'label'  => __( 'Validate until', 'wphb-coupon' ),
						'type'   => 'datetime',
						'filter' => array( $this, 'get_coupon_date_meta_box_field' )
					),
					array(
						'name'  => 'coupon_date_to_timestamp',
						'label' => '',
						'type'  => 'hidden'
					),
					array(
						'name'  => 'minimum_spend',
						'label' => __( 'Minimum spend', 'wphb-coupon' ),
						'type'  => 'number',
						'desc'  => __( 'This field allows you to set the minimum subtotal needed to use the coupon.', 'wphb-coupon' ),
						'min'   => 0,
						'step'  => 0.1
					),
					array(
						'name'  => 'maximum_spend',
						'label' => __( 'Maximum spend', 'wphb-coupon' ),
						'type'  => 'number',
						'desc'  => __( 'This field allows you to set the maximum subtotal allowed when using the coupon.', 'wphb-coupon' ),
						'min'   => 0,
						'step'  => 0.1
					),
					array(
						'name'  => 'limit_per_coupon',
						'label' => __( 'Usage limit per coupon', 'wphb-coupon' ),
						'type'  => 'number',
						'desc'  => __( 'How many times this coupon can be used before it is void.', 'wphb-coupon' ),
						'min'   => 0
					)
				);
			}
		}

		/**
		 * Transfer coupon date from timestamp data to date in meta box field.
		 *
		 * @param $value
		 *
		 * @return false|string
		 */
		public function get_coupon_date_meta_box_field( $value ) {
			if ( intval( $value ) ) {
				return date( hb_get_date_format(), $value );
			}

			return $value;
		}


		/**
		 * Add coupon date meta to meta box class save meta.
		 *
		 * @since 2.0
		 *
		 * @param $value
		 * @param $field_name
		 * @param $meta_box_name
		 *
		 * @return false|int|string
		 */
		public function update_coupon_date_meta( $value, $field_name, $meta_box_name ) {
			if ( in_array( $field_name, array(
					'coupon_date_from',
					'coupon_date_to'
				) ) && $meta_box_name == 'coupon_settings'
			) {
				if ( isset( $_POST[ '_hb_' . $field_name . '_timestamp' ] ) ) {
					$value = sanitize_text_field( $_POST[ '_hb_' . $field_name . '_timestamp' ] );
				} else {
					$value = strtotime( $value );
				}
			}

			return $value;
		}
	}
}

new WPHB_Coupon_Post_Types();