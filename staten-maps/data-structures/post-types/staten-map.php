<?php

namespace Staten_Maps\Data_Structures\Post_Types;


use Staten_Maps\Ajax\Lat_Lng;

class Staten_Map {

	const POST_TYPE = 'staten-map';
	const ADDRESS_KEY = 'staten-address';
	const LAT_KEY = 'staten-lat';
	const LNG_KEY = 'staten-lng';
	const HANDLE = 'staten-map';


	/**
	 *
	 */
	public function init() {

		$this->attach_hooks();
		$this->register_settings();


	}

	public function attach_hooks() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		add_action( 'acf/render_field', array( $this, 'add_get_lat_lng_button' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

	}

	public function add_get_lat_lng_button( $field ) {
		if ( is_array( $field ) && $field['key'] == self::ADDRESS_KEY ) {
			?>
			<div class="lat-lng-message"></div>
			<div class="nonce" data-nonce="<?php echo wp_create_nonce( Lat_Lng::NONCE ); ?>"></div>
			<button class="button staten-get-lat-lng">Get Lat and Lng</button><br>&nbsp;
			<?php
		}
	}


	/**
	 *
	 */
	public function register_post_type() {
		register_post_type( self::POST_TYPE, array(
			'labels'              => array(
				'name'               => __( 'Maps', 'staten-maps' ),
				'singular_name'      => __( 'Map', 'staten-maps' ),
				'add_new'            => __( 'Add New', 'staten-maps' ),
				'add_new_item'       => __( 'Add New Map', 'staten-maps' ),
				'edit_item'          => __( 'Edit Map', 'staten-maps' ),
				'new_item'           => __( 'New Map', 'staten-maps' ),
				'all_items'          => __( 'All Maps', 'staten-maps' ),
				'view_item'          => __( 'View Map', 'staten-maps' ),
				'search_items'       => __( 'Search Maps', 'staten-maps' ),
				'not_found'          => __( 'No Maps Found', 'staten-maps' ),
				'not_found_in_trash' => __( 'No Maps found in Trash', 'staten-maps' ),
				'menu_name'          => __( 'Maps', 'staten-maps' )
			),
			'menu_icon'           => 'dashicons dashicons-location-alt',
			'query_var'           => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'hierarchical'        => true,
			'show_in_menu'        => true,
			'public'              => false,
			'show_ui'             => true,
			'has_archive'         => false,
			'supports'            => array( 'title', 'page-attributes' ),
			'map_meta_cap'        => true,

		) );


	}


	public function save_post( $post_id, $post ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) :
			return;
		endif;
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && ! defined( 'OVERRIDE_AJAX' ) ) :
			return;
		endif;
		if ( ! current_user_can( 'edit_post', $post_id ) ) :
			return;
		endif;
		if ( false !== wp_is_post_revision( $post_id ) ) :
			return;
		endif;

		if ( $post->post_type === self::POST_TYPE ) {


		}


		return;


	}

	public function register_settings() {
		if ( function_exists( 'acf_add_local_field_group' ) ):

			acf_add_local_field_group( array(
				'key'                   => 'group_5814c696176d4',
				'title'                 => 'Map Points',
				'fields'                => array(
					array(
						'key'               => 'field_5814c69b1e6f5',
						'label'             => 'Map Points',
						'name'              => 'map_points',
						'type'              => 'repeater',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'collapsed'         => '',
						'min'               => '',
						'max'               => '',
						'layout'            => 'block',
						'button_label'      => 'Add Row',
						'sub_fields'        => array(
							array(
								'key'               => 'field_5814c6aa1e6f6',
								'label'             => 'Title',
								'name'              => 'title',
								'type'              => 'text',
								'instructions'      => '',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => '',
								'prepend'           => '',
								'append'            => '',
								'maxlength'         => '',
							),
							array(
								'key'               => self::ADDRESS_KEY,
								'label'             => 'Address',
								'name'              => 'address',
								'type'              => 'text',
								'instructions'      => 'If you do not set the latitude and longitude explicitly, when you save this will try to acquire latitude and longitude and set it. Put the full address e.g. 742 Evergreen Terrace Springfield, NT 49007 here.',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => '',
								'prepend'           => '',
								'append'            => '',
								'maxlength'         => '',
							),
							array(
								'key'               => self::LAT_KEY,
								'label'             => 'Latitude',
								'name'              => 'latitude',
								'type'              => 'text',
								'instructions'      => 'If you want to set the latitude explicitly, set it here, otherwise we will try to get it for you upon saving.',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => '',
								'prepend'           => '',
								'append'            => '',
								'maxlength'         => '',
							),
							array(
								'key'               => self::LNG_KEY,
								'label'             => 'Longitude',
								'name'              => 'longitude',
								'type'              => 'text',
								'instructions'      => 'If you want to set the longitude explicitly, set it here, otherwise we will try to get it for you upon saving.',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => '',
								'prepend'           => '',
								'append'            => '',
								'maxlength'         => '',
							),
							array(
								'key'               => 'field_5814c6da1e6fa',
								'label'             => 'Tooltip',
								'name'              => 'tooltip',
								'type'              => 'textarea',
								'instructions'      => '',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => '',
								'maxlength'         => '',
								'rows'              => '',
								'new_lines'         => 'wpautop',
							),
							array(
								'key'               => 'field_5814c6e51e6fb',
								'label'             => 'Type',
								'name'              => 'type',
								'type'              => 'select',
								'instructions'      => '',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'choices'           => array(
									'red'    => 'Red',
									'blue'   => 'Blue',
									'orange' => 'Orange',
								),
								'default_value'     => array(),
								'allow_null'        => 0,
								'multiple'          => 0,
								'ui'                => 0,
								'ajax'              => 0,
								'return_format'     => 'value',
								'placeholder'       => '',
							),
						),
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => self::POST_TYPE,
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => 1,
				'description'           => '',
			) );

		endif;


	}

	public function enqueue() {
		$post_type = null;
		if ( is_array( $_GET ) ) {
			if ( ! empty( $_GET['post'] ) ) {
				$post_id   = $_GET['post'];
				$post_type = get_post_type( $post_id );
			} elseif ( ! empty( $_GET['post_type'] ) ) {
				$post_type = $_GET['post_type'];
			}
		}
		if ( $post_type !== self::POST_TYPE ) {
			return;
		}

		wp_enqueue_script( self::HANDLE, plugin_dir_url( __FILE__ ) . 'js/staten-map.js', array(
			'jquery',
			'underscore'
		) );


	}


}