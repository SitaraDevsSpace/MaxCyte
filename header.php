<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	
<?php do_action( 'fl_head_open' ); ?>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<?php echo apply_filters( 'fl_theme_viewport', "<meta name='viewport' content='width=device-width, initial-scale=1.0' />\n" ); ?>
<?php echo apply_filters( 'fl_theme_xua_compatible', "<meta http-equiv='X-UA-Compatible' content='IE=edge' />\n" ); ?>
<link rel="profile" href="https://gmpg.org/xfn/11" />
<?php

wp_head();

FLTheme::head();

$post_id = get_queried_object_id();
$istransparentfooter = $post_id ? get_field('transparent_wave_footer', $post_id) : '';

  $class = '';
  if($istransparentfooter == 1){
    $class = 'transparent_footer';
  }
  else{
	$class = 'color_wave_footer';
  }
  if ( is_single()  || is_404() ) {
   $class = 'transparent_footer';
	}


?>

<script>
	var page = "<?php echo !empty($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : $_SERVER['REQUEST_URI']; ?>";

  // show searchbar in the mobile menu
  document.addEventListener("DOMContentLoaded", function () {
    const submenu = document.querySelector("#menu-1-7c4w1phx6bka");
    if (submenu) {
      
      const div = document.createElement("div");
       
      div.className = "search";
      div.style.marginLeft = "15px";
      div.style.marginRight = "15px";
      div.style.marginBottom = "15px";

      div.innerHTML = `<?php echo do_shortcode('[searchwp_form id=1]'); ?>`;

      submenu.prepend(div);
    }
  });
</script>

<!-- Begin - bioz -->

<!--
	widget_mini_obj
	v_widget
-->

<link rel="stylesheet" href="https://cdn.bioz.com/assets/font-awesome-reg.css">
<link rel="stylesheet" type="text/css" href="https://cdn.bioz.com/assets/tooltipster.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.bioz.com/assets/tooltipster-theme2.css" />
<script src="https://cdn.bioz.com/assets/bioz-w-api-2.5.min.js"></script>
<link rel="stylesheet" href="https://cdn.bioz.com/assets/v_widget-2.5.css">
<script >
	var $ = jQuery.noConflict();
</script>
<script src="https://cdn.bioz.com/assets/v_widget-2.5.maxcyte.js"></script>
<script type="text/javascript" src="https://cdn.bioz.com/assets/tooltipster.js"></script> 
<script>
    var _type = "commercial";
    var _company = "4657";
    var _vendor = "MaxCyte Inc";
    var _vendor_partner_color = "#021d66";
    var _form_url = "https://back-badge-8.bioz.com";
    var _key = "";
    var _form_mobile = false;
</script>
<!-- End - bioz -->


</head>
<body <?php body_class($class); ?><?php FLTheme::print_schema( ' itemscope="itemscope" itemtype="https://schema.org/WebPage"' ); ?>>
<?php

FLTheme::header_code();

do_action( 'fl_body_open' );

?>
<div class="fl-page">
	<?php

	do_action( 'fl_page_open' );

	FLTheme::fixed_header();

	do_action( 'fl_before_top_bar' );

	FLTheme::top_bar();

	do_action( 'fl_after_top_bar' );
	do_action( 'fl_before_header' );

	FLTheme::header_layout();

	do_action( 'fl_after_header' );
	do_action( 'fl_before_content' );

	?>
	<div id="fl-main-content" class="fl-page-content" itemprop="mainContentOfPage" role="main">

		<?php do_action( 'fl_content_open' ); ?>
