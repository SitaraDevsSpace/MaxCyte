<?php

/**
 * @class PPSlidingMenusModule
 */
class PPSlidingMenusModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'            => __( 'Mobile Menu', 'fl-builder' ),
				'description'     => __( 'Mobile Menu', 'fl-builder' ),
				 'category'        => __( 'Custom Addons', 'fl-builder' ),
				'dir'             => FL_CHILD_THEME_URL . '/add-ons/mobile-menu/',
				'url'             => FL_CHILD_THEME_URL . '/add-ons/mobile-menu/',
				'editor_export'   => true,
				'partial_refresh' => true,
			)
		);
	}

	/**
	 * Nav Menu Index
	 *
	 * @since  2.8.0
	 * @var    int
	 */
	public $nav_menu_index = 1;

	/**
	 * Get Available Menu
	 *
	 * Return the list of available WP menus
	 *
	 * @since  2.8.0
	 * @return array
	 */
	public static function get_available_menus() {
		if ( ! isset( $_GET['fl_builder'] ) ) {
			return array();
		}

		$get_menus =  get_terms( 'nav_menu', array( 'hide_empty' => true ) );
		$options = array();

		if ( $get_menus ) {

			foreach( $get_menus as $key => $menu ) {

				if ( $key == 0 ) {
					$fields['default'] = $menu->name;
				}

				$options[ $menu->slug ] = $menu->name;
			}

		} else {
			$options = array( '' => __( 'No Menus Found', 'fl-builder' ) );
		}

		return $options;
	}

	/**
	 * Get Nav Menu Index
	 *
	 * @since  2.8.0
	 * @return int
	 */
	public function get_nav_menu_index() {
		return $this->nav_menu_index++;
	}

	/**
	 * Handle Link Classes
	 *
	 * @since  2.8.0
	 * @return array
	 */
	public function handle_link_classes( $atts, $item, $args, $depth ) {
		$classes = $depth ? 'pp-slide-menu-item-link pp-slide-menu-sub-item-link' : 'pp-slide-menu-item-link';

		if ( in_array( 'current-menu-item', $item->classes ) ) {
			$classes .= '  pp-slide-menu-item-link-current';
		}

		if ( empty( $atts['class'] ) ) {
			$atts['class'] = $classes;
		} else {
			$atts['class'] .= ' ' . $classes;
		}

		return $atts;
	}

	/**
	 * Handle Submenu Classes
	 *
	 * @since  2.8.0
	 * @return array
	 */
	public function handle_sub_menu_classes( $classes ) {
		$classes[] = 'pp-slide-menu-sub-menu';

		return $classes;
	}

	/**
	 * Handle Menu Item Classes
	 *
	 * @since  2.8.0
	 * @return array
	 */
	public function handle_menu_item_classes( $classes ) {
		$classes[] = 'pp-slide-menu-item';

		if ( in_array( 'menu-item-has-children', $classes ) ) {
			$classes[] = 'pp-slide-menu-item-has-children';
		}

		if ( in_array( 'current-menu-item', $classes ) ) {
			$classes[] = 'pp-slide-menu-item-current';
		}

		return $classes;
	}
}

/**
 * Register the module settings.
 */
FLBuilder::register_module(
	'PPSlidingMenusModule',
	array(
		'General'    => array(
			'title'    => __( 'General', 'fl-builder' ),
			'sections' => array(
				'settings' => array(
					'title'  => __( 'Settings', 'fl-builder' ),
					'fields' => array(
						'menu'            => array(
							'type'    => 'select',
							'label'   => __( 'Menu', 'fl-builder' ),
							'default' => '',
							'options' => PPSlidingMenusModule::get_available_menus(),
						),
						'back_text'       => array(
							'type'        => 'text',
							'label'       => __( 'Back Label', 'fl-builder' ),
							'default'     => __( 'Back', 'fl-builder' ),
							'connections' => array( 'string' ),
						),
						'effect'          => array(
							'type'    => 'select',
							'label'   => __( 'Effect', 'fl-builder' ),
							'default' => 'overlay',
							'options' => array(
								'overlay' => __( 'Overlay', 'fl-builder' ),
								'push'    => __( 'Push', 'fl-builder' ),
							),
						),
						'direction'       => array(
							'type'    => 'select',
							'label'   => __( 'Direction', 'fl-builder' ),
							'default' => 'left',
							'options' => array(
								'left'   => __( 'Left', 'fl-builder' ),
								'right'  => __( 'Right', 'fl-builder' ),
								'bottom' => __( 'Bottom', 'fl-builder' ),
								'top'    => __( 'Top', 'fl-builder' ),
							),
						),
						'duration'        => array(
							'type'       => 'unit',
							'label'      => __( 'Transition Duration', 'fl-builder' ),
							'default'    => '',
							'units'      => array( 's' ),
							'slider'     => array(
								'min'  => '0',
								'max'  => '3',
								'step' => '0.1',
							),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.mobile-menu .pp-slide-menu__menu, .mobile-menu .mobile-menu .pp-slide-menu-sub-menu',
								'property' => 'transition-duration',
								'unit'     => 's',
							),
						),
						'link_navigation' => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Link Navigation', 'fl-builder' ),
							'default' => 'yes',
							'options' => array(
								'yes' => __( 'Yes', 'fl-builder' ),
								'no'  => __( 'No', 'fl-builder' ),
							),
							'help'    => __( 'Allow navigating to sub-menus by clicking the links instead of just the arrows.', 'fl-builder' ),
						),
					),
				),
			),
		),
		'style'      => array( // Tab
			'title'    => __( 'Style', 'fl-builder' ), // Tab title
			'sections' => array( // Tab Sections
				'menu_style'   => array( // Section
					'title'  => __( 'Menu', 'fl-builder' ), // Section Title
					'fields' => array( // Section Fields
						'width'      => array(
							'type'       => 'unit',
							'label'      => __( 'Width', 'fl-builder' ),
							'default'    => '',
							'units'      => array( 'px', '%' ),
							'slider'     => array(
								'px' => array(
									'min' => 100,
									'max' => 1000,
								),
								'%'  => array(
									'min' => 0,
									'max' => 100,
								),
							),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.mobile-menu',
								'property' => 'width',
								'unit'     => 'px',
							),
						),
						'min_height' => array(
							'type'       => 'unit',
							'label'      => __( 'Minimum Height', 'fl-builder' ),
							'default'    => '',
							'units'      => array( 'px' ),
							'slider'     => array(
								'min' => 100,
								'max' => 1000,
							),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.mobile-menu',
								'property' => 'min-height',
								'unit'     => 'px',
							),
						),
						'alignment'  => array(
							'type'    => 'align',
							'label'   => __( ' Align', 'fl-builder' ),
							'default' => 'left',
							'preview' => array(
								'type'     => 'css',
								'selector' => '.fl-module-mobile-menu',
								'property' => 'text-align',
							),
						),
						'bg_color'   => array(
							'type'        => 'color',
							'label'       => __( 'Background Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.mobile-menu, .mobile-menu .pp-slide-menu-sub-menu',
								'property' => 'background-color',
							),
						),
						'border'     => array(
							'type'  => 'border',
							'label' => __( 'Border', 'fl-builder' ),
						),
					),
				),
				'links_style'  => array(
					'title'     => __( 'Links', 'fl-builder' ),
					'collapsed' => true,
					'fields'    => array(
						'links_spacing'               => array(
							'type'       => 'unit',
							'label'      => __( 'Spacing', 'fl-builder' ),
							'default'    => '0',
							'units'      => array( 'px' ),
							'slider'     => array(
								'min' => 0,
								'max' => 50,
							),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.mobile-menu .pp-slide-menu-item:not(:last-child)',
								'property' => 'margin-bottom',
							),
						),
						'links_separator_thickness'   => array(
							'type'       => 'unit',
							'label'      => __( 'Separator Thickness', 'fl-builder' ),
							'default'    => '',
							'units'      => array( 'px' ),
							'slider'     => array(
								'min' => 0,
								'max' => 50,
							),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.mobile-menu .pp-slide-menu-item',
								'property' => 'border-bottom-width',
								'unit'     => 'px',
							),
						),
						'links_transition_easing'     => array(
							'type'    => 'select',
							'label'   => __( 'Transition', 'fl-builder' ),
							'default' => 'ease-in',
							'options' => array(
								'linear'      => __( 'Linear', 'fl-builder' ),
								'ease-in'     => __( 'Ease In', 'fl-builder' ),
								'ease-out'    => __( 'Ease Out', 'fl-builder' ),
								'ease-in-out' => __( 'Ease In Out', 'fl-builder' ),
							),
							'preview' => array(
								'type'     => 'css',
								'selector' => '.mobile-menu .pp-slide-menu-item-link, .mobile-menu .pp-slide-menu-arrow',
								'property' => 'transition-timing-function',
							),
						),
						'links_transition_duration'   => array(
							'type'    => 'unit',
							'label'   => __( 'Transition Duration', 'fl-builder' ),
							'default' => '0.3',
							'units'   => array( 's' ),
							'slider'  => array(
								'min'  => '0',
								'max'  => '3',
								'step' => '0.1',
							),
							'preview' => array(
								'type'     => 'css',
								'selector' => '.mobile-menu .pp-slide-menu-item-link, .mobile-menu .pp-slide-menu-arrow',
								'property' => 'transition-duration',
								'unit'     => 's',
							),
						),
						'links_padding'               => array(
							'type'    => 'dimension',
							'label'   => __( 'Links Padding', 'fl-builder' ),
							'slider'  => true,
							'units'   => array( 'px' ),
							'default' => '',
							'preview' => array(
								'type'     => 'css',
								'selector' => '.mobile-menu .pp-slide-menu-item-link, .mobile-menu .pp-slide-menu-arrow',
								'property' => 'padding',
								'unit'     => 'px',
							),
						),
						'links_alignment'             => array(
							'type'    => 'align',
							'label'   => __( 'Links Align', 'fl-builder' ),
							'default' => 'left',
							'preview' => array(
								'type'     => 'css',
								'selector' => '.mobile-menu .pp-slide-menu-item-link',
								'property' => 'text-align',
							),
						),
						'links_colors_separator'      => array(
							'type'  => 'pp-separator',
							'color' => 'e6eaed',
						),
						'links_bg_color'              => array(
							'type'        => 'color',
							'label'       => __( 'Background Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.mobile-menu .pp-slide-menu-item-link',
								'property' => 'bakcground-color',
							),
						),
						'links_color'                 => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.mobile-menu .pp-slide-menu-item-link',
								'property' => 'color',
							),
						),
						'links_separator_color'       => array(
							'type'        => 'color',
							'label'       => __( 'Separator Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.mobile-menu .pp-slide-menu-item',
								'property' => 'border-color',
							),
						),
						'links_bg_color_hover'        => array(
							'type'        => 'color',
							'label'       => __( 'Background Hover Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.mobile-menu .pp-slide-menu-item-link:hover',
								'property' => 'background-color',
							),
						),
						'links_color_hover'           => array(
							'type'        => 'color',
							'label'       => __( 'Hover Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.mobile-menu .pp-slide-menu-item-link:hover',
								'property' => 'color',
							),
						),
						'links_separator_color_hover' => array(
							'type'        => 'color',
							'label'       => __( 'Separator Hover Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.mobile-menu .pp-slide-menu-item:hover',
								'property' => 'border-color',
							),
						),
					),
				),
				'arrows_style' => array(
					'title'     => __( 'Arrows', 'fl-builder' ),
					'collapsed' => true,
					'fields'    => array(
						'arrow_separator_thickness'    => array(
							'type'       => 'unit',
							'label'      => __( 'Separator Thickness', 'fl-builder' ),
							'default'    => '',
							'units'      => array( 'px' ),
							'slider'     => array(
								'min' => 0,
								'max' => 50,
							),
							'responsive' => true,
							'preview'    => array(
								'type'  => 'css',
								'rules' => array(
									array(
										'selector' => '.mobile-menu .pp-slide-menu-item.pp-slide-menu-item-has-children > .pp-slide-menu-arrow',
										'property' => 'border-left-width',
										'unit'     => 'px',
									),
									array(
										'selector' => '.mobile-menu .pp-slide-menu-back > .pp-slide-menu-arrow',
										'property' => 'border-right-width',
										'unit'     => 'px',
									),
								),
							),
						),
						'arrow_size'                   => array(
							'type'       => 'unit',
							'label'      => __( 'Size', 'fl-builder' ),
							'default'    => '14',
							'units'      => array( 'px' ),
							'slider'     => array(
								'min' => 0,
								'max' => 50,
							),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.mobile-menu .pp-slide-menu-arrow i',
								'property' => 'font-size',
								'unit'     => 'px',
							),
						),
						'arrow_left_padding'           => array(
							'type'       => 'unit',
							'label'      => __( 'Left Padding', 'fl-builder' ),
							'default'    => '',
							'units'      => array( 'px' ),
							'slider'     => array(
								'min' => 0,
								'max' => 50,
							),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.mobile-menu .pp-slide-menu-arrow',
								'property' => 'padding-left',
								'unit'     => 'px',
							),
						),
						'arrow_right_padding'          => array(
							'type'       => 'unit',
							'label'      => __( 'Right Padding', 'fl-builder' ),
							'default'    => '',
							'units'      => array( 'px' ),
							'slider'     => array(
								'min' => 0,
								'max' => 50,
							),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.mobile-menu .pp-slide-menu-arrow',
								'property' => 'padding-right',
								'unit'     => 'px',
							),
						),
						'arrows_colors_separator'      => array(
							'type'  => 'pp-separator',
							'color' => 'e6eaed',
						),
						'arrows_bg_color'              => array(
							'type'        => 'color',
							'label'       => __( 'Background Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.mobile-menu .pp-slide-menu-arrow',
								'property' => 'background-color',
							),
						),
						'arrows_color'                 => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.mobile-menu .pp-slide-menu-arrow',
								'property' => 'color',
							),
						),
						'arrows_separator_color'       => array(
							'type'        => 'color',
							'label'       => __( 'Separator Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.mobile-menu .pp-slide-menu-arrow',
								'property' => 'border-color',
							),
						),
						'arrows_bg_color_hover'        => array(
							'type'        => 'color',
							'label'       => __( 'Background Hover Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.mobile-menu .pp-slide-menu-arrow:hover',
								'property' => 'background-color',
							),
						),
						'arrows_color_hover'           => array(
							'type'        => 'color',
							'label'       => __( 'Hover Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.mobile-menu .pp-slide-menu-arrow:hover',
								'property' => 'color',
							),
						),
						'arrows_separator_color_hover' => array(
							'type'        => 'color',
							'label'       => __( 'Separator Hover Color', 'fl-builder' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.mobile-menu .pp-slide-menu-arrow:hover',
								'property' => 'border-color',
							),
						),
					),
				),
			),
		),
		'typography' => array(
			'title'    => __( 'Typography', 'fl-builder' ),
			'sections' => array(
				'menu_typography' => array(
					'title'  => __( 'Menu', 'fl-builder' ),
					'fields' => array(
						'menu_typography' => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'fl-builder' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.mobile-menu',
							),
						),
					),
				),
			),
		),
	)
);
