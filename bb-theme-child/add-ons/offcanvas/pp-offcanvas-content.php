<?php

/**
 * @class PPOffcanvasContent
 */


/**
 * Get color value hex or rgba
 */
function pp_get_color_value( $color )
{
    if ( $color == '' || ! $color ) {
        return;
    }
    if ( false === strpos( $color, 'rgb' ) ) {
        return '#' . $color;
    } else {
        return $color;
    }
}

class PPOffcanvasContent extends FLBuilderModule {

	/**
	 * Class constructor.
	 *
	 * @since 2.7.11
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'            => __( 'Off-Canvas Content', 'bb-powerpack' ),
				'description'     => __( 'Addon to display Off-Canvas Content.', 'bb-powerpack' ),
				'category'        => __( 'Custom Addons', 'fl-builder' ),
				'dir'             => FL_CHILD_THEME_URL . 'modules/pp-offcanvas-content/',
				'url'             => FL_CHILD_THEME_URL . 'modules/pp-offcanvas-content/',
				'editor_export'   => true, // Defaults to true and can be omitted.
				'enabled'         => true, // Defaults to true and can be omitted.
				'partial_refresh' => true,
			)
		);
	}

	public function enqueue_icon_styles() {
		$enqueue = false;
		$settings = $this->settings;

		if ( isset( $settings->toggle_source ) && 'button' === $settings->toggle_source ) {
			if ( isset( $settings->button_icon ) && ! empty( $settings->button_icon ) ) {
				$enqueue = true;
			}
		}

		if ( isset( $settings->close_button ) && 'yes' === $settings->close_button ) {
			if ( isset( $settings->close_button_icon ) && ! empty( $settings->button_icon ) ) {
				$enqueue = true;
			}
		}

		if ( $enqueue && is_callable( 'parent::enqueue_icon_styles' ) ) {
			parent::enqueue_icon_styles();
		}
	}

	public function update( $settings ) {
		if ( ! isset( $settings->close_button_icon ) || empty( $settings->close_button_icon ) ) {
			$settings->close_button_icon = 'fa fa-times';
		}

		return $settings;
	}

	/**
	 * Render content output on the frontend.
	 *
	 * @since 2.7.11
	 * @access public
	 */
	public function render_content( $settings ) {
		$html = '';

		switch ( $settings->content_type ) {
			case 'content':
				global $wp_embed;
				$html = '';
				foreach ( $settings->content_form as $key => $form ) {
					$html .= '<div itemprop="title" class="pp-offcanvas-custom-content pp-offcanvas-' . ( $key + 1 ) . '">';
					$html .= '<h3 class="pp-offcanvas-content-title">' . $form->content_title . '</h3>';
					$html .= '<div itemprop="text" class="pp-offcanvas-content-description">';
					$html .= $form->content_description;
					$html .= '</div>';
					$html .= '</div>';
				}
				break;
			case 'module':
				$html = '[fl_builder_insert_layout id="' . $settings->content_module . '" type="fl-builder-template"]';
				break;
			case 'row':
				$html = '[fl_builder_insert_layout id="' . $settings->content_row . '" type="fl-builder-template"]';
				break;
			case 'layout':
				$html = '[fl_builder_insert_layout id="' . $settings->content_layout . '" type="fl-builder-template"]';
				break;
			case 'sidebar':
				$sidebar = $settings->content_sidebar;
				if ( empty( $sidebar ) ) {
					return;
				}
				ob_start();
				dynamic_sidebar( $sidebar );
				$html = ob_get_clean();
				break;
			default:
				break;
		}

		return $html;
	}

	/**
	 * Get WP Widgets.
	 *
	 * Retrieve WordPress Widgets based on selected source.
	 *
	 * @since 2.7.11
	 * @return array
	 */
	public static function get_wp_widgets() {
		global $wp_registered_sidebars;

		$content_sidebar = '';
		$options         = [];

		if ( ! $wp_registered_sidebars ) {
			$options[''] = __( 'No sidebars were found', 'bb-powerpack' );
		} else {
			$options[''] = __( 'Choose Sidebar', 'bb-powerpack' );

			foreach ( $wp_registered_sidebars as $sidebar_id => $sidebar ) {
				$options[ $sidebar_id ] = $sidebar['name'];
			}
		}
		$default_key     = array_keys( $options );
		$default_key     = array_shift( $default_key );
		$content_sidebar = array(
			'type'    => 'select',
			'label'   => __( 'Sidebar', 'bb-powerpack' ),
			'default' => $default_key,
			'options' => $options,
		);
		return $content_sidebar;
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module(
	'PPOffcanvasContent',
	array(
		'items'      => array(
			'title'    => __( 'General', 'bb-powerpack' ),
			'sections' => array(
				'general'  => array(
					'title'  => __( 'Off-Canvas Content', 'bb-powerpack' ),
					'fields' => array(
						'content_type'    => array(
							'type'    => 'select',
							'label'   => __( 'Type', 'bb-powerpack' ),
							'default' => 'content',
							'options' => array(
								'content' => __( 'Custom Content', 'bb-powerpack' ),
								'module'  => __( 'Saved Module', 'bb-powerpack' ),
								'row'     => __( 'Saved Row', 'bb-powerpack' ),
								'layout'  => __( 'Saved Layout', 'bb-powerpack' ),
								'sidebar' => __( 'Sidebar', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'content' => array(
									'fields' => array( 'content_form' ),
								),
								'module'  => array(
									'fields' => array( 'content_module' ),
								),
								'row'     => array(
									'fields' => array( 'content_row' ),
								),
								'layout'  => array(
									'fields' => array( 'content_layout' ),
								),
								'sidebar' => array(
									'fields' => array( 'content_sidebar' ),
								),
							),
						),
						'content_form'    => array(
							'type'         => 'form',
							'label'        => __( 'Content', 'bb-powerpack' ),
							'form'         => 'pp_content_form',
							'preview_text' => 'content_title',
							'multiple'     => true,
						),
						'content_module'  => array(
							'type'    => 'select',
							'label'   => __( 'Saved Module', 'bb-powerpack' ),
							'options' => array(),
						),
						'content_row'     => array(
							'type'    => 'select',
							'label'   => __( 'Saved Row', 'bb-powerpack' ),
							'options' => array(),
						),
						'content_layout'  => array(
							'type'    => 'select',
							'label'   => __( 'Saved Layout', 'bb-powerpack' ),
							'options' => array(),
						),
						'content_sidebar' => PPOffcanvasContent::get_wp_widgets(),
					),
				),
				'toggle'   => array(
					'title'     => __( 'Toggle', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'toggle_source'     => array(
							'type'    => 'select',
							'label'   => __( 'Toggle Source', 'bb-powerpack' ),
							'default' => 'button',
							'options' => array(
								'button'    => __( 'Button', 'bb-powerpack' ),
								'hamburger' => __( 'Hamburger Icon', 'bb-powerpack' ),
								'class'     => __( 'Custom Class Name', 'bb-powerpack' ),
								'id'        => __( 'Custom ID Name', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'button'    => array(
									'sections' => array( 'toggle_style' ),
									'fields' => array( 'toggle_full_width', 'button_text', 'button_icon', 'toggle_text_align', 'toggle_text_space', 'button_icon_color', 'button_icon_color_hover', 'button_icon_size' ),
								),
								'hamburger' => array(
									'sections' => array( 'toggle_style' ),
									'fields' => array( 'toggle_animation', 'burger_label', 'toggle_text_align', 'toggle_text_space', 'hamburger_size', 'hamburger_thickness' ),
								),
								'class'     => array(
									'fields' => array( 'toggle_class' ),
								),
								'id'        => array(
									'fields' => array( 'toggle_id' ),
								),
							),
						),
						'button_text'       => array(
							'type'    => 'text',
							'label'   => __( 'Button Text', 'bb-powerpack' ),
							'default' => __( 'Reveal Off-Canvas', 'bb-powerpack' ),
							'connections' => array( 'string', 'html' ),
						),
						'button_icon'       => array(
							'type'        => 'icon',
							'label'       => __( 'Icon', 'bb-powerpack' ),
							'show_remove' => true,
						),
						'burger_label'      => array(
							'type'    => 'text',
							'label'   => __( 'Label', 'bb-powerpack' ),
							'default' => __( 'Open Off-Canvas Content', 'bb-powerpack' ),
							'connections' => array( 'string', 'html' ),
						),
						'toggle_text_align' => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Text Position', 'bb-powerpack' ),
							'default' => 'after',
							'options' => array(
								'after'  => __( 'Before', 'bb-powerpack' ),
								'before' => __( 'After', 'bb-powerpack' ),
							),
						),
						'toggle_text_space' => array(
							'type'    => 'unit',
							'label'   => __( 'Spacing', 'bb-powerpack' ),
							'default' => '10',
							'units'   => array( 'px' ),
							'slider'  => true,
						),
						'toggle_animation'  => array(
							'type'    => 'select',
							'label'   => __( 'Animation', 'bb-powerpack' ),
							'default' => 'none',
							'options' => array(
								'none'        => __( 'None', 'bb-powerpack' ),
								'arrow'       => __( 'Arrow Left', 'bb-powerpack' ),
								'arrow-r'     => __( 'Arrow Right', 'bb-powerpack' ),
								'arrowalt'    => __( 'Arrow Alt Left', 'bb-powerpack' ),
								'arrowalt-r'  => __( 'Arrow Alt Right', 'bb-powerpack' ),
								'arrowturn'   => __( 'Arrow Turn Left', 'bb-powerpack' ),
								'arrowturn-r' => __( 'Arrow Turn Right', 'bb-powerpack' ),
								'boring'      => __( 'Boring', 'bb-powerpack' ),
								'collapse'    => __( 'Collapse Left', 'bb-powerpack' ),
								'collapse-r'  => __( 'Collapse Right', 'bb-powerpack' ),
								'elastic'     => __( 'Elastic Left', 'bb-powerpack' ),
								'elastic-r'   => __( 'Elastic Right', 'bb-powerpack' ),
								'emphatic'    => __( 'Emphatic Left', 'bb-powerpack' ),
								'emphatic-r'  => __( 'Emphatic Right', 'bb-powerpack' ),
								'minus'       => __( 'Minus', 'bb-powerpack' ),
								'slider'      => __( 'Slider Left', 'bb-powerpack' ),
								'slider-r'    => __( 'Slider Right', 'bb-powerpack' ),
								'spin'        => __( 'Spin Left', 'bb-powerpack' ),
								'spin-r'      => __( 'Spin Right', 'bb-powerpack' ),
								'spring'      => __( 'Spring Left', 'bb-powerpack' ),
								'spring-r'    => __( 'Spring Right', 'bb-powerpack' ),
								'squeeze'     => __( 'Squeeze', 'bb-powerpack' ),
								'stand'       => __( 'Stand Left', 'bb-powerpack' ),
								'stand-r'     => __( 'Stand Right', 'bb-powerpack' ),
								'vortex'      => __( 'Vortex Left', 'bb-powerpack' ),
								'vortex-r'    => __( 'Vortex Right', 'bb-powerpack' ),
								'3dx'         => __( '3DX', 'bb-powerpack' ),
								'3dy'         => __( '3DY', 'bb-powerpack' ),
								'3dxy'        => __( '3DXY', 'bb-powerpack' ),
							),
						),
						'toggle_class'      => array(
							'type'  => 'text',
							'label' => __( 'Toggle CSS Class', 'bb-powerpack' ),
						),
						'toggle_id'         => array(
							'type'  => 'text',
							'label' => __( 'Toggle CSS Id', 'bb-powerpack' ),
						),
					),
				),
				'settings' => array(
					'title'     => __( 'Settings', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'direction'          => array(
							'type'    => 'select',
							'label'   => __( 'Reveal Direction', 'bb-powerpack' ),
							'default' => 'left',
							'options' => array(
								'top'    => __( 'Top', 'bb-powerpack' ),
								'bottom' => __( 'Bottom', 'bb-powerpack' ),
								'left'   => __( 'Left', 'bb-powerpack' ),
								'right'  => __( 'Right', 'bb-powerpack' ),
							),
						),
						'content_transition' => array(
							'label'   => __( 'Reveal Transition', 'bb-powerpack' ),
							'type'    => 'select',
							'default' => 'slide',
							'options' => array(
								'slide'       => __( 'Slide', 'bb-powerpack' ),
								'reveal'      => __( 'Reveal', 'bb-powerpack' ),
								'push'        => __( 'Push', 'bb-powerpack' ),
								'slide-along' => __( 'Slide Along', 'bb-powerpack' ),
							),
						),
						'close_button'       => array(
							'label'   => __( 'Show Close Button', 'bb-powerpack' ),
							'type'    => 'pp-switch',
							'default' => 'yes',
							'options' => array(
								'yes' => __( 'Yes', 'bb-powerpack' ),
								'no'  => __( 'No', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'yes' => array(
									'fields'   => array( 'close_button_icon' ),
									'sections' => array( 'close_button' ),
								),
							),
						),
						'close_button_icon'    => array(
							'type'        => 'icon',
							'label'       => __( 'Icon', 'bb-powerpack' ),
							'show_remove' => true,
							'default'	  => 'fa fa-times',
							'preview'     => array(
								'type' => 'none',
							),
						),
						'esc_close'          => array(
							'label'   => __( 'Esc to Close', 'bb-powerpack' ),
							'type'    => 'pp-switch',
							'default' => 'yes',
							'options' => array(
								'yes' => __( 'Yes', 'bb-powerpack' ),
								'no'  => __( 'No', 'bb-powerpack' ),
							),
						),
						'body_click_close'   => array(
							'label'   => __( 'Click anywhere to Close', 'bb-powerpack' ),
							'type'    => 'pp-switch',
							'default' => 'yes',
							'options' => array(
								'yes' => __( 'Yes', 'bb-powerpack' ),
								'no'  => __( 'No', 'bb-powerpack' ),
							),
						),
					),
				),
			),
		),
		'style'      => array(
			'title'    => __( 'Style', 'bb-powerpack' ),
			'sections' => array(
				'toggle_style'  => array(
					'title'  => __( 'Toggle', 'bb-powerpack' ),
					'fields' => array(
						'toggle_full_width' => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Full Width', 'bb-powerpack' ),
							'default' => 'no',
							'options' => array(
								'yes' => __( 'Yes', 'bb-powerpack' ),
								'no'  => __( 'No', 'bb-powerpack' ),
							),
						),
						'toggle_align'              => array(
							'type'    => 'align',
							'label'   => __( 'Alignment', 'bb-powerpack' ),
							'default' => 'left',
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-offcanvas-toggle-wrap',
								'property' => 'text-align',
							),
						),
						'toggle_text_color'         => array(
							'type'        => 'color',
							'label'       => __( 'Text Color', 'powerpack' ),
							'default'     => '000',
							'show_reset'  => true,
							'connections' => array( 'color' ),
						),
						'toggle_color_hover'        => array(
							'type'        => 'color',
							'label'       => __( 'Text Hover Color', 'powerpack' ),
							'default'     => '000',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type' => 'none',
							),
						),
						'toggle_bg_color'           => array(
							'type'        => 'color',
							'label'       => __( 'Background Color', 'powerpack' ),
							'default'     => 'a0a0a0',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-offcanvas-toggle-wrap .pp-offcanvas-toggle',
								'property' => 'background-color',
							),
						),
						'toggle_bg_color_hover'     => array(
							'type'        => 'color',
							'label'       => __( 'Background Hover Color', 'powerpack' ),
							'default'     => 'a0a0a0',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type' => 'none',
							),
						),
						'toggle_border'             => array(
							'type'       => 'border',
							'label'      => __( 'Border Style', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-offcanvas-toggle-wrap .pp-offcanvas-toggle',
							),
						),
						'toggle_border_color_hover' => array(
							'type'        => 'color',
							'label'       => __( 'Border Hover Color', 'powerpack' ),
							'default'     => 'a0a0a0',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type' => 'none',
							),
						),
						'toggle_padding'            => array(
							'type'       => 'dimension',
							'label'      => __( 'Padding', 'bb-powerpack' ),
							'default'    => 10,
							'slider'     => true,
							'responsive' => true,
							'units'      => array( 'px' ),
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-offcanvas-toggle-wrap .pp-offcanvas-toggle',
								'property' => 'padding',
								'unit'     => 'px',
							),
						),
						'button_icon_color'         => array(
							'type'        => 'color',
							'label'       => __( 'Icon Color', 'powerpack' ),
							'default'     => '000',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-offcanvas-toggle-wrap .pp-offcanvas-toggle-icon',
								'property' => 'color',
							),
						),
						'button_icon_color_hover'   => array(
							'type'        => 'color',
							'label'       => __( 'Icon Hover Color', 'powerpack' ),
							'default'     => '000',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type' => 'none',
							),
						),
						'button_icon_size'          => array(
							'type'    => 'unit',
							'label'   => __( 'Icon Size', 'bb-powerpack' ),
							'default' => '15',
							'units'   => array( 'px' ),
							'slider'  => true,
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-offcanvas-toggle-wrap .pp-offcanvas-toggle-icon',
								'property' => 'font-size',
							),
						),
						'hamburger_size'            => array(
							'type'    => 'unit',
							'label'   => __( 'Hamburger Size', 'bb-powerpack' ),
							'default' => '40',
							'units'   => array( 'px' ),
							'slider'  => true,
						),
						'hamburger_thickness'       => array(
							'type'    => 'unit',
							'label'   => __( 'Hamburger Thickness', 'bb-powerpack' ),
							'default' => '4',
							'units'   => array( 'px' ),
							'slider'  => true,
						),
						'hamburger_color'           => array(
							'type'        => 'color',
							'label'       => __( 'Hamburger Color', 'powerpack' ),
							'default'     => '000',
							'show_reset'  => true,
							'connections' => array( 'color' ),
						),
						'hamburger_color_hover'     => array(
							'type'        => 'color',
							'label'       => __( 'Hamburger Hover Color', 'powerpack' ),
							'default'     => '000',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type' => 'none',
							),
						),
					),
				),
				'offcanvas'     => array(
					'title'     => __( 'Off-Canvas Container', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'offcanvas_bar_width'   => array(
							'type'       => 'unit',
							'label'      => __( 'Size', 'bb-powerpack' ),
							'units'      => array( 'px', '%' ),
							'default'    => '300',
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type' => 'none',
							),
						),
						'offcanvas_bar_bg'      => array(
							'type'        => 'color',
							'label'       => __( 'Background', 'bb-powerpack' ),
							'default'     => 'eee',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type' => 'none',
							),
						),
						'offcanvas_bar_border'  => array(
							'type'       => 'border',
							'label'      => __( 'Border Style', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type' => 'none',
							),
						),
						'offcanvas_bar_padding' => array(
							'type'       => 'dimension',
							'label'      => __( 'Padding', 'bb-powerpack' ),
							'default'    => 10,
							'slider'     => true,
							'responsive' => true,
							'units'      => array( 'px' ),
							'preview'    => array(
								'type' => 'none',
							),
						),
					),
				),
				'content_style' => array(
					'title'     => __( 'Off-Canvas Content', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'content_align'      => array(
							'type'    => 'align',
							'label'   => __( 'Alignment', 'bb-powerpack' ),
							'default' => 'left',
							'preview' => array(
								'type' => 'none',
							),
						),
						'content_text_color' => array(
							'type'        => 'color',
							'label'       => __( 'Text Color', 'powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type' => 'none',
							),
						),
						'content_link_color' => array(
							'type'        => 'color',
							'label'       => __( 'Link Color', 'powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type' => 'none',
							),
						),
						'content_bg_color'   => array(
							'type'        => 'color',
							'label'       => __( 'Background Color', 'powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type' => 'none',
							),
						),
						'content_border'     => array(
							'type'       => 'border',
							'label'      => __( 'Border Style', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type' => 'none',
							),
						),
						'content_padding'    => array(
							'type'       => 'dimension',
							'label'      => __( 'Padding', 'bb-powerpack' ),
							'default'    => 10,
							'slider'     => true,
							'responsive' => true,
							'units'      => array( 'px' ),
							'preview'    => array(
								'type' => 'none',
							),
						),
					),
				),
				'close_button'  => array(
					'title'     => __( 'Close Button', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'close_button_align'   => array(
							'type'    => 'align',
							'label'   => __( 'Alignment', 'bb-powerpack' ),
							'default' => 'left',
							'preview' => array(
								'type' => 'none',
							),
						),
						'close_button_color'   => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'powerpack' ),
							'default'     => '333',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type' => 'none',
							),
						),
						'close_button_size'    => array(
							'type'    => 'unit',
							'label'   => __( 'Size', 'bb-powerpack' ),
							'default' => '',
							'units'   => array( 'px' ),
							'slider'  => true,
							'preview' => array(
								'type' => 'none',
							),
						),
						'close_button_padding' => array(
							'type'       => 'dimension',
							'label'      => __( 'Padding', 'bb-powerpack' ),
							'default'    => 10,
							'slider'     => true,
							'responsive' => true,
							'units'      => array( 'px' ),
							'preview'    => array(
								'type' => 'none',
							),
						),
					),
				),
				'overlay_style' => array(
					'title'     => __( 'Overlay', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'overlay_color' => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'powerpack' ),
							'show_alpha'  => true,
							'show_reset'  => true,
							'connections' => array( 'color' ),
						),
					),
				),
			),
		),
		'typography' => array(
			'title'    => __( 'Typography', 'bb-powerpack' ),
			'sections' => array(
				'toggle_typography'  => array(
					'title'  => __( 'Toggle Typography', 'bb-powerpack' ),
					'fields' => array(
						'toggle_typography' => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-offcanvas-toggle .pp-toggle-label',
							),
						),
					),
				),
				'content_typography' => array(
					'title'  => __( 'Off-Canvas Content', 'bb-powerpack' ),
					'fields' => array(
						'title_typography'       => array(
							'type'       => 'typography',
							'label'      => __( 'Title Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type' => 'none',
							),
						),
						'description_typography' => array(
							'type'       => 'typography',
							'label'      => __( 'Description Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type' => 'none',
							),
						),
					),
				),
			),
		),
	)
);

FLBuilder::register_settings_form(
	'pp_content_form',
	array(
		'title' => __( 'Off-Canvas Content', 'bb-powerpack' ),
		'tabs'  => array(
			'content_general' => array(
				'title'    => __( 'General', 'bb-powerpack' ),
				'description' => sprintf( __( 'Apply this CSS class on any custom element which you want to use to close the panel.%s', 'bb-powerpack' ), '<br><br><span class="pp-module-id-class"></span>' ),
				'sections' => array(
					'content_general' => array(
						'title'  => __( 'General', 'bb-powerpack' ),
						'fields' => array(
							'content_title'       => array(
								'type'        => 'text',
								'label'       => __( 'Title', 'bb-powerpack' ),
								'default'     => __( 'Title', 'bb-powerpack' ),
								'connections' => array( 'string', 'html', 'url' ),
							),
							'content_description' => array(
								'type'        => 'editor',
								'label'       => '',
								'connections' => array( 'string', 'html', 'url' ),
							),
						),
					),
				),
			),
		),
	)
);
