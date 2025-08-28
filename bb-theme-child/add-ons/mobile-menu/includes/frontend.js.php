;(function($) {

	new PPSlidingMenus({
		id: '<?php echo $id; ?>',
		linknav: '<?php echo $settings->link_navigation; ?>',
		backtext: '<?php echo ! empty( $settings->back_text ) ? $settings->back_text : esc_html__( 'Back' ); ?>',
	});

})(jQuery);
