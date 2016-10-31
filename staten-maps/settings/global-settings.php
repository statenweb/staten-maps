<?php

namespace Staten_Maps\Settings;


/**
 * Class Site_Settings
 */
class Global_Settings {


	/**
	 * Location Parameter for ACF fields
	 * @var string
	 */
	const LOCATION_PARAM = 'options_page';

	/**
	 * Location value for ACF fields
	 * @var string
	 */
	const LOCATION_VALUE = 'global-options';

	/**
	 * Initilization method
	 */
	public function init() {

		$this->attach_hooks();

	}

	/**
	 * Attach wordpress hooks
	 */
	public function attach_hooks() {

		add_action( 'init', array( $this, 'add_menu_page' ) );
		add_action( 'init', array( $this, 'add_settings' ) );

	}


	/**
	 * Menu page to house ACF fields
	 */
	public function add_menu_page() {
		if ( function_exists( 'acf_add_options_sub_page' ) ) {
			acf_add_options_sub_page( array(
				'page_title'  => 'Staten Maps Settings',
				'menu_title'  => 'Staten Maps',
				'menu_slug'   => static::LOCATION_VALUE,
				'capability'  => apply_filters( 'statenweb-maps-global-settings-permssions', 'update_plugins' ),
				'parent_slug' => apply_filters( 'statenweb-maps-global-setttings-parent-slug', 'options-general.php' ),
			) );
		}
	}

	/**
	 * Add ACF Fields
	 */
	public function add_settings() {

		if ( function_exists( 'acf_add_local_field_group' ) ):

			acf_add_local_field_group( array(
				'key'                   => 'group_581685fa299ac',
				'title'                 => 'Staten Maps',
				'fields'                => array(
					array(
						'key'               => 'field_58168605119d0',
						'label'             => 'Google Maps API Key',
						'name'              => 'staten_maps_google_maps_api_key',
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
						'key'               => 'field_58168612119d1',
						'label'             => 'Enqueue Type',
						'name'              => 'staten_maps_enqueue_type',
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
							'default'        => 'Globally enqueue',
							'shortcode-only' => 'Only on posts that have the shortcode',
							'none'           => 'Do not enqueue anything',
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
				'location'              => array(
					array(
						array(
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => 'global-options',
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

}
