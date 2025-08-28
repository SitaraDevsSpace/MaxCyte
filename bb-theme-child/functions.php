<?php
  
// $session_id = session_id();
// if(empty($session_id))
// {
//  session_start();
//  //session_unset();
// }


// Defines
define( 'FL_CHILD_THEME_DIR', get_stylesheet_directory() );
define( 'FL_CHILD_THEME_URL', get_stylesheet_directory_uri() );

// Classes
require_once 'classes/class-fl-child-theme.php';

// Actions
add_action( 'wp_enqueue_scripts', 'FLChildTheme::enqueue_scripts', 1000 );

function mfnch_enqueue_styles() {
	wp_enqueue_style('custom_responsive', get_stylesheet_directory_uri() . '/css/custom_responsive.css');
	wp_enqueue_style('custom_style', get_stylesheet_directory_uri() . '/css/custom_style.css');
  wp_enqueue_style('spl_style', get_stylesheet_directory_uri() . '/css/spl_custom.css');	
	wp_enqueue_script('custom', get_stylesheet_directory_uri() . '/js/custom.js');

}
add_action('wp_enqueue_scripts', 'mfnch_enqueue_styles', 1001);

function request_quote_button($atts) {
  ob_start();
  echo '<div class="request_a_quote request_a_quote_btn"><a href="javascript:void(0);"  class="add-to-cart-button" role="button" aria-label="Request a Quote"  
  data-product-id="'.$atts['product-code'].'" product_name="'.$atts['product-name'].'" data-quantity="1" data-event="detail-page">
  Request a Quote</a><span class="productloading"></span></div>';
  return ob_get_clean();
}
add_shortcode('product_request_quote_button', 'request_quote_button');



function request_demo_button($atts) {
  ob_start();
  echo '<div class="request_a_quote request_a_quote_btn">
          <a href="/request-a-quote/#contact" class="add-to-cart-button" role="button" aria-label="Request a Demo">
            Request a Demo
          </a>
        </div>';
  return ob_get_clean();
}
add_shortcode('product_request_demo_button', 'request_demo_button');





function table_products($atts, $args) {
 if(!empty($_SESSION)) {
    // $_SESSION is not empty, do something
    $_SESSION['cart'][$product_id] = array('quantity' => $quantity, 'product_name' => $product_name);
    if (empty($_SESSION['cart'])) {
      return ''; // Return empty string if the cart is empty
    }
      if (isset($_SESSION['cart'])) {
        $cart_contents = '';
        $cart_contents ='<div class="cart"><form id="update-form"><table class="request-prd-table"><thead class="heading"><tr><th>PRODUCT CODE</th><th>PRODUCT NAME</th><th>Quantity</th></tr></thead>
        <tbody class="table-data">';
            foreach ($_SESSION['cart'] as $product_id => $product_data) {
              if(!empty($product_id)){
                  $cart_contents .= '<tr><td>' . $product_id . '</td><td>'. $product_data['product_name'] . '</td><td><div class="button-container">
                  <button class="cart-qty-minus" type="button" value="-"><i class="maxcyte-font-minus" aria-hidden="true"></i></button>
                  <input type="hidden" name="product['. $product_id .'][name]"  value="'. $product_data['product_name'].'">
                  <input type="number" name="product['. $product_id .'][qty]"  value="'. $product_data['quantity'] .'" min="0" class="input-text qty">
                  <button class="cart-qty-plus" type="button" value="+"><i class="maxcyte-font-plus" aria-hidden="true"></i></button>
                  </div></td></tr>';
              }
            }  
        } 
        $cart_contents .= '</tbody><tfoot class="table_footer"><tr><td colspan="3"><span class="productloader"></span><button type="submit" class="update_product_btn">Update</button></td></tr></tfoot></table></form></div>';   
  } 
  return $cart_contents;
}
add_shortcode('table_products', 'table_products');



function load_cart_handler()
{
  $return = array(
      "status"=>"success",
      "cart"=>do_shortcode('[table_products]')
    );
  echo json_encode($return);
  die();
}
add_action( 'wp_ajax_load_cart', 'load_cart_handler' );
add_action( 'wp_ajax_nopriv_load_cart', 'load_cart_handler' );


function add_to_cart($args) {
    extract($args);
    $_SESSION['cart'][$product_id] = array('quantity' => $quantity, 'product_name' => $product_name);

    foreach ($_SESSION['cart'] as $product_id => $product_data) {
         if ($product_data['quantity'] == 0) {
           unset($_SESSION['cart'][$product_id]);
        }
    }
    if (empty($_SESSION['cart'])) {
      unset($_SESSION['cart']); 
    }
}

function add_to_cart_callback()
{
  $args = array(
          'product_id'=>$_POST['product_id'],
          'quantity'=>$_POST['quantity'],
          'product_name'=>$_POST['product_name']
  );
  add_to_cart($args);

  $return = array( 
    'success' => true,
    'cart_contents' =>  do_shortcode('[table_products]')
  );
  
  echo json_encode($return);
  die();
}
add_action('wp_ajax_add_to_cart', 'add_to_cart_callback');
add_action('wp_ajax_nopriv_add_to_cart', 'add_to_cart_callback');


function update_to_cart()
{
  $test = $_POST['data'];
  parse_str($_POST['data'], $return);
 

  foreach($return['product'] as $k=>$v)
  {
    $args = array(
      'product_id'=>$k,
      'quantity'=>$v['qty'],
      'product_name'=>$v['name']
   );
   add_to_cart($args);
  }

  $return = array( 
    'success' => true,
    'cart_contents' =>  do_shortcode('[table_products]')
  );
  echo json_encode($return);
  die();
}
add_action('wp_ajax_update_to_cart', 'update_to_cart');
add_action('wp_ajax_nopriv_update_to_cart', 'update_to_cart');




add_action( 'gform_pre_submission_3', function ( $form ) {
  
  $sessionValues = '';
  $_SESSION['cart'][$product_id] = array('quantity' => $quantity, 'product_name' => $product_name);

  $send_to_sf = 0;
  $send_to_email = 0;

  foreach ($_SESSION['cart'] as $product_id => $product_data) {
    if(!empty($product_id)) {

      $sessionValues .= "Product Name: ". $product_data['product_name'] .  ", " .
                        "Code: " . $product_id .  ", " . 
                        "Qty: " . $product_data['quantity'] . PHP_EOL;

        $args = array(
          'post_type'      => 'mcproducts',          
          'meta_key'       => 'product_code', 
          'meta_value'     => $product_id, 
          'meta_compare'   => '=',             
          'posts_per_page' => 1,              
        );
        
      $posts = get_posts($args);

# Trello: Request a Quote - Email Triggers
# 1. Instruments only = SF + email
# 2. Instruments + PA = SF + email
# 3. PA only = email only



      if(!empty($posts))
      {
        $post_id = $posts[0]->ID;
        $check_if_sf  = get_field('is_salesforce', $post_id);
        if($check_if_sf == 1)
        {
          # Condition #1 and #2 is satisfied here
          $send_to_sf = 1;
          $send_to_email = 1;
        }
      }

    }
  }
  if( $send_to_sf == 0)
  {
    # Condition #3 satisfied here
    $send_to_email = 1;
  }
  
    $_POST['input_17'] = $send_to_sf;
    $_POST['input_18'] = $send_to_email;
  
  //$_POST['input_20'] =  $send_to_sf;
  //$_POST['input_21'] =  $send_to_email;
  //$_POST['input_7'] .=  PHP_EOL . PHP_EOL ."Inquiry for these products:" .  PHP_EOL . $sessionValues;

  $_POST['input_35'] =  "Inquiry for these products:" .  PHP_EOL . $sessionValues; #Order Detail Description: cart data
  
  // $_POST['input_19'] = $sessionValues;
 //session_unset();
 unset($_SESSION['cart']);
} 
);  


# Validate if the items are added to cart
add_filter( 'gform_validation_3', 'custom_validation' );
function custom_validation( $validation_result ) {
    $form = $validation_result['form'];

    $validation = false;
    if(isset($_SESSION['cart']) and !empty($_SESSION['cart']))
    {
      foreach($_SESSION['cart'] as $product_key => $product_ids)
      {
        if(!empty($product_key))
        {
          $validation = true;
        }
      }
    }
    if($validation == false)
    {
          // set the form validation to false
          $validation_result['is_valid'] = false;

          foreach( $form['fields'] as &$field ) {
              //NOTE: 7 is message field on staging
              if ( $field->id == '7' ) {
                  $field->failed_validation = true;
                  $field->validation_message = 'Your quote cart is currently empty. Please add products to the <a href="#cart">quote cart</a> to submit the request.';
                  break;
              }
          }
    }
  
    //Assign modified $form object back to the validation result
    $validation_result['form'] = $form;
    return $validation_result;
  
}


/* add_filter('gform_pre_submission', function ($form) {
  foreach ($form['fields'] as &$field) { 
    if ($field->type === 'checkbox' && $field->adminLabel === 'SalesforceMultiSelect') {
        $field_id = $field->id;
        $checkbox_values = isset($_POST['input_' . $field_id]) ? $_POST['input_' . $field_id] : [];
        // Convert array to semicolon-separated string
        $_POST['input_hidden_salesforce'] = implode(';', $checkbox_values);
    } 
  }  
    return $form; 
  }); */

/* Request a quote end */



// add_action( 'gform_pre_submission_3', function ( $form ) {
//   $sessionValues = '';
//   $_SESSION['cart'][$product_id] = array('quantity' => $quantity, 'product_name' => $product_name);
//   foreach ($_SESSION['cart'] as $product_id => $product_data) {
//     if(!empty($product_id)){
//       $sessionValues .= "Product Name: ". $product_data['product_name'] .  ", " .
//                         "Code: " . $product_id .  ", " . 
//                         "Qty: " . $product_data['quantity'] . PHP_EOL;
//     }
//   }
//   $_POST['input_7'] .=  PHP_EOL . PHP_EOL ."Inquiry for these products:" .  PHP_EOL . $sessionValues;
//   // $_POST['input_19'] = $sessionValues;
//   session_unset();
// } );

/*** BEGIN - Remove jquery-migrate-deprecation-notice, outdated PHP warnings & ajax load more warning***/
add_action('admin_head', 'hide_migrate_wp_error');
function hide_migrate_wp_error() {
  echo '<style>
    .php-insecure, .alm-err-notice, .jquery-migrate-dashboard-notice, .jquery-migrate-deprecation-notice, #wp-admin-bar-enable-jquery-migrate-helper {display:none !important;}

  </style>';
}
/*** END - Remove  jquery-migrate-deprecation-notice and outdated PHP warnings ***/

/*** BEGIN - Check if its a staging website ***/
function staging_notice(  $wp_admin_bar ) {

        $production_domain = "www.host-name.com";

         if( (
                stristr($_SERVER['HTTP_HOST'], 'supremeclients')
                 OR stristr($_SERVER['HTTP_HOST'], 'wpengine')
                 OR stristr($_SERVER['HTTP_HOST'], 'sheldon')
                 OR stristr($_SERVER['HTTP_HOST'], 'stage')
                 OR stristr($_SERVER['HTTP_HOST'], 'staging')
                 OR stristr($_SERVER['HTTP_HOST'], 'dev')
             ) AND (
                $_SERVER['HTTP_HOST'] !== $production_domain
             )
         )
         {
             $wp_admin_bar->add_menu( array(
                'id'    => 'staging_Website',
                'parent' => 'top-secondary',
                'group'  => null,
                'title' => __( '<div class="blink_me" >STAGING WEBSITE</div><style>.blink_me {color: WHITE;background-color:RED;display:block !important;padding-left: 10px !important; padding-right: 10px !important;font-weight: bold !important;animation: blinker 1.2s linear infinite;}@keyframes blinker{50%{opacity:0;}}</style>', 'lmfwppt' ),
                'href'  => '#',
            ) );
         }

}
add_action( 'admin_bar_menu', 'staging_notice', 500 );
/*** END - Check if its a staging website ***/

/*** BEGIN - Refresh Supreme Builder on every publish ***/
add_filter( 'fl_builder_should_refresh_on_publish', '__return_true' );
/*** END -  Refresh Supreme Builder on every publish ***/

# Disable Layout CSS & Javascript menu from BB tools while editing a page
add_filter( 'fl_builder_main_menu', function( $views ) {
    unset( $views['main']['items'][50] ); //Layout CSS & Javascript
    return $views;
});

# Clean Entire BB Cache when WP-Rocket purge cache
if(is_admin())
{
    if(isset($_GET['action']) AND $_GET['action']=='purge_cache' AND ($_GET['type']=='all' OR $_GET['type']=='url'))
    {
        if(class_exists('FLBuilderModel'))
        {
            FLBuilderModel::delete_asset_cache_for_all_posts();
            #die('BB Cache Cleaned');
        }
    }
}



/* custom waves */

function bb_register_custom_shapes() {

  if ( ! class_exists( 'FLBuilder' ) ) {
            return;
  }
  FLBuilder::register_shape(array(
    'label' => __( 'Hero section Wave', 'bb-custom-shapes-boomerang' ),
    'name' => 'herosectionWave',
    'width' => 1680,
    'height' => 176,
    'render' => dirname(__FILE__) . '/Assets/Hero-section-wave.php',
  ));
  FLBuilder::register_shape(array(
    'label' => __( 'Footer section Wave', 'bb-custom-shapes-boomerang' ),
    'name' => 'footersectionWave',
    'width' => 1680,
    'height' => 159,
    'render' => dirname(__FILE__) . '/Assets/Footer-section-wave.php',
  ));
  FLBuilder::register_shape(array(
    'label' => __( 'Footer wide Wave', 'bb-custom-shapes-boomerang' ),
    'name' => 'footerwideWave',
    'width' => 2572,
    'height' => 190,
    'render' => dirname(__FILE__) . '/Assets/Footer-wide-wave.php',
  ));
  FLBuilder::register_shape(array(
    'label' => __( 'Separator1', 'bb-custom-shapes-boomerang' ),
    'name' => 'seperator1',
    'width' => 1680,
    'height' => 84,
    'render' => dirname(__FILE__) . '/Assets/Seperator1.php',
  ));
   FLBuilder::register_shape(array(
    'label' => __( 'Separator2', 'bb-custom-shapes-boomerang' ),
    'name' => 'seperator2',
    'width' => 1680,
    'height' => 127,
    'render' => dirname(__FILE__) . '/Assets/Seperator2.php',
  ));
  FLBuilder::register_shape(array(
    'label' => __( 'Separator3', 'bb-custom-shapes-boomerang' ),
    'name' => 'seperator3',
    'width' => 1680,
    'height' => 129,
    'render' => dirname(__FILE__) . '/Assets/Seperator3.php',
  ));
  FLBuilder::register_shape(array(
    'label' => __( 'Separator4', 'bb-custom-shapes-boomerang' ),
    'name' => 'seperator4',
    'width' => 1680,
    'height' => 128,
    'render' => dirname(__FILE__) . '/Assets/Seperator4.php',
  ));
  FLBuilder::register_shape(array(
    'label' => __( 'Separator white bg', 'bb-custom-shapes-boomerang' ),
    'name' => 'seperator-white-bg',
    'width' => 1680,
    'height' => 129,
    'render' => dirname(__FILE__) . '/Assets/Seperator-white-bg.php',
  ));
  FLBuilder::register_shape(array(
    'label' => __( 'Footer mobile copyright Wave', 'bb-custom-shapes-boomerang' ),
    'name' => 'footermobilecopyrightWave',
    'width' => 374,
    'height' => 39,
    'render' => dirname(__FILE__) . '/Assets/Footer-mobile-copyright-wave.php',
  ));
   FLBuilder::register_shape(array(
    'label' => __( 'Effect Separator5 Wave', 'bb-custom-shapes-boomerang' ),
    'name' => 'effectseperator5Wave',
    'width' => 1685,
    'height' => 226,
    'render' => dirname(__FILE__) . '/Assets/Effect-seperator5.php',
  ));
   FLBuilder::register_shape(array(
    'label' => __( 'Transparent Wave', 'bb-custom-shapes-boomerang' ),
    'name' => 'transparentWave',
    'width' => 1680,
    'height' => 127,
    'render' => dirname(__FILE__) . '/Assets/Transparent-wave.php',
  ));
   FLBuilder::register_shape(array(
    'label' => __( 'PDP Sticky Menu Wave', 'bb-custom-shapes-boomerang' ),
    'name' => 'PDPstickymenuWave',
    'width' => 1680,
    'height' => 30,
    'render' => dirname(__FILE__) . '/Assets/PDP-Sticky-Menu-Wave.php',
  ));
}
add_action('init', 'bb_register_custom_shapes');

// Mobile Menu - Module (offcanvas)
add_action( 'init', 'my_load_module_examples' , 999  );

function my_load_module_examples() {
  if ( class_exists( 'FLBuilder' ) ) {
   require_once 'add-ons/mobile-menu/mobile-menu.php';
   require_once 'add-ons/offcanvas/pp-offcanvas-content.php';
  }
}
/**
 * Enables the display of menu descriptions.
 */
function prefix_nav_description( $item_output, $item, $depth, $args ) {
    if ( !empty( $item->description ) ) {
        $item_output = str_replace($args->link_after.'</a>',
            '<div class="menu-item-description">'.$item->description.'</div>'.$args->link_after.'</a>',
            $item_output
        );
    }

    return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'prefix_nav_description', 10, 4 );
//Menu Discribtion End
// publication showing results count.
add_filter('alm_display_results', function(){
  return 'Showing 1-{post_count} of {total_posts} Publications';
});
add_filter('alm_display_results', function(){
   $url = get_permalink();
    if(str_replace( home_url(), "", $url ) == "/featured-science/" || str_replace( home_url(), "", $url ) == "/peer-reviewed-publications/") {
  return 'Showing 1-{post_count} of {total_posts} Publications';
    }
    elseif (str_replace( home_url(), "", $url ) == "/events/")
       {
      return 'Showing 1-{post_count} of {total_posts} Events';
    }
    elseif (str_replace( home_url(), "", $url ) == "/resource-library/")
       {
      return 'Showing {post_count} of {total_posts} Resources';
    }
    elseif (str_replace( home_url(), "", $url ) == "/maxcyte-minutes/")
       {
      return 'Showing 1-{post_count} of {total_posts} MaxCyte Newsletter';
    }
    elseif (str_replace( home_url(), "", $url ) == "/webinars-presentations/")
       {
      return 'Showing 1-{post_count} of {total_posts} Webinars';
    }
    elseif (str_replace( home_url(), "", $url ) == "/employee-spotlight/")
       {
      return 'Showing 1-{post_count} of {total_posts} Employee Spotlights';
    }
    else {
       return 'Showing 1-{post_count}';
    }
});

//Image Thuumbnail
 add_image_size( 'event-thumbnail', 380, 340, true );
 add_image_size( 'resource-thumbnail', 410, 230, true );
 add_image_size( 'webinar-thumbnail', 410, 230, true );
 add_image_size( 'leadership-thumbnail', 458, 476, true );
 add_image_size( 'processing-posts-thumbnail', 620, 420, true );
 

//Function for Upcoming and Past Event
 function my_past_events($args, $id) {
    global $post;
    $date = date("Y-m-d");
    $args['post_type'] = 'events';
    $args['orderby'] = 'meta_value_num';
    $args['order'] = 'DESC';
    $args['meta_query'] = array(
        'relation'    => 'AND',
         array(
        'relation'    => 'OR',
        array(
            'key' => 'start_date',
            'compare' => "<",
            'type' => "DATE",
            'value' => $date
        ),
        array(
            'key' => 'end_date',
            'compare' => "<",
            'type' => "DATE",
            'value' => $date
        )
    )
    );
    return $args;
}
add_filter( 'alm_query_args_past_events', 'my_past_events', 10, 2);
function my_upcoming_events($args, $id) {
    global $post;
    $date = date("Y-m-d");
    $args['post_type'] = 'events';
    $args['orderby'] = 'meta_value_num';
    $args['order'] = 'ASC';
    $args['meta_query'] = array(
        'relation'    => 'OR',
        array(
            'key' => 'start_date',
            'compare' => ">=",
            'type' => "DATE",
            'value' => $date
        ),
        array(
            'key' => 'end_date',
            'compare' => ">=",
            'type' => "DATE",
            'value' => $date
        )
    );
    return $args;
}
add_filter( 'alm_query_args_upcoming_events', 'my_upcoming_events', 10, 2);


// function list_products_data($atts) {

//     $html .= '<div class="products-details">
//     <div class="prd-name">
//         <div class="prd-title">'.$atts["product_name"].'</div>
//         <div class="prd-decription">'.$atts["product_description"].'</div>
//     </div>
//     <div class="prd-sku"><img src="/wp-content/uploads/2023/01/automatic_barcode_logistics.svg"><span>'.$atts['sku'].'</span></div>
//     <div class="prd-button">
//         <a class="icon-anchor" href="javascript:void(0)" target="_self" data-attr="'.$atts["sku"].'">
//             <i class="maxcyte-font-plus"></i>
//         </a>
//     </div>
// </div>';

// return $html;

// }
// add_shortcode('list-products', 'list_products_data');


// function instruments_data($atts) {$html .= '<div class="instrument-details">
//     <div class="instrument-image"><img src="/wp-content/uploads/2022/12/ATX_01.jpg"></div>
//     <div class="instrument-block">
//         <div class="instrument-title"><h6 class="uabb-infobox-title">ATx</h6></div>
//         <div class="instrument-decription"><p>For small to medium scale transfection</p></div>
//     </div>
//     <div class="instrument-button">
//         <a class="icon-anchor" href="#" target="_self">
//             <i class="maxcyte-font-plus"></i>
//         </a>
//     </div>
// </div>';

// return $html;

// }
// add_shortcode('instrument', 'instruments_data');

function products_data($atts, $args) {

    $atts = shortcode_atts( array(
            'category' => ''
        ), $atts );

    global $post;
    $post_id = get_the_ID();
    $terms = get_terms( array(
      'taxonomy' => 'product_type',
      'hide_empty' => false,
    ) );
       $args = array(
        'post_type' => 'mcproducts',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'order' => 'ASC',
        'tax_query' => array(
                array(
                    'taxonomy' => 'product_type',
                    'field' => 'slug',
                    'terms' => array($atts['category']),
                )
            )    
        );
       $value = $atts['category'];
       // echo "<pre>";
       //  print_r ($parameter);
       //  echo "</pre>";

// if ( $value == "instruments") {
//     echo "ok";
// }

// else{
//     echo "notok";
// }

$query = new WP_Query($args);
 
$html = '';
   if($query->have_posts()):
   while($query->have_posts()) :      
    $query->the_post();
        $product_description = get_field('product_description', $post->ID);
        $product_code = get_field('product_code', $post->ID);

if ($value == "instruments"){
    $html .= '<div class="instrument-details">
        <div class="instrument-image"><img src="/wp-content/uploads/2022/12/ATX_01.jpg"></div>
        <div class="instrument-block">
            <div class="instrument-title"><h6 class="uabb-infobox-title">'.get_the_title().'</h6></div>
            <div class="instrument-decription"><p>'.$product_description.'</p></div>
        </div>
        <div class="instrument-button">
            <a class="icon-anchor add-to-cart-button" href="javascript:void(0)" target="_self" data-product-id="'.$product_code.'" product_name="'.get_the_title().'" data-quantity="1">
                <i class="maxcyte-font-plus"></i>
            </a>
        </div>
    </div>';
}

else{
    $html .= '<div class="products-details">
        <div class="prd-name">
            <div class="prd-title">'.get_the_title().'</div>
            <div class="prd-decription">'.$product_description.'</div>
        </div>
        <div class="prd-sku"><img src="/wp-content/uploads/2023/01/automatic_barcode_logistics.svg"><span>'.$product_code.'</span></div>
        <div class="prd-button">
            <a class="icon-anchor add-to-cart-button" href="javascript:void(0)" target="_self" data-product-id="'.$product_code.'" product_name="'.get_the_title().'" data-quantity="1">
                <i class="maxcyte-font-plus"></i>
            </a>
        </div>
    </div>';
}                     
    endwhile;
        wp_reset_postdata();    
    endif;
    return $html;


     // $postdata = array (
     //    'product_code' => $post->product_code,
     //   );

     //   return $postdata;
}
add_shortcode('mcproducts', 'products_data');


// shortcode for event category 
 // add_shortcode('event_category', 'getevent_category');


// function getevent_category()
// {
//     $Terms = get_the_terms($post->ID,'resource_type');
//     echo($Terms);
//     // return $Terms;
// }

add_filter( 'gform_required_legend', '__return_empty_string' );


function hook_maxcyteAjax() {
  ?>
      <script>
          var maxcyte_ajax = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
      </script>
  <?php
}
add_action('wp_head', 'hook_maxcyteAjax');



function request_quote_ajax_handler($data) {

 // unset($_SESSION['request_quote_cart']);
  $sku	= isset($_POST['sku'])?trim($_POST['sku']):"";

  if(!empty($sku))
  {

    $total = $_SESSION['request_quote_cart'][$sku]+1;

   // $sku = $sku.rand(1,22222);
    $_SESSION['request_quote_cart'][$sku] = $total;

      // Success - AJAX request here
    $response = array(
      'success' => true,
      'message' => $sku,
      'session' => $_SESSION
    );
  }
  else{
    // Failed - AJAX request here
    $response = array(
      'success' => false,
      'message' => $sku,
    );
  }

  wp_send_json( $response );
}
add_action( 'wp_ajax_maxcyte_ajax_handler', 'request_quote_ajax_handler' );
add_action( 'wp_ajax_nopriv_maxcyte_ajax_handler', 'request_quote_ajax_handler' );



// add_shortcode('request_quote_button', 'callback_request_quote_button');
// function callback_request_quote_button($atts)
// {
//   $html = '
//   <div class="fl-module fl-module-uabb-button fl-node-4d1t6uhlfji0 primary_button" data-node="4d1t6uhlfji0">
//     <div class="fl-module-content fl-node-content">
//       <div class="uabb-module-content uabb-button-wrap uabb-creative-button-wrap uabb-button-width-auto uabb-creative-button-width-auto uabb-button-left uabb-creative-button-left uabb-button-tablet-left uabb-creative-button-tablet-left uabb-button-reponsive-left uabb-creative-button-reponsive-left">
//         <a href="javascript:void(0)" data-value="'.$atts['sku'].'" target="_self" class="uabb-button ast-button uabb-creative-button uabb-creative-default-btn  request_quote " role="button" aria-label="Request a Quote">
//           <span class="uabb-button-text uabb-creative-button-text">Request a Quote</span>
//         </a>
//       </div>
//     </div>
//   </div>';

//   return $html;
// }

add_shortcode('resource_breadcrumb', 'callback_resource_breadcrumb');
function callback_resource_breadcrumb($atts)
{
    $html = '
    <div class="fl-module fl-module-rich-text fl-node-o5kjsa1iv96h breadcrumbs" data-node="o5kjsa1iv96h">
        <div class="fl-module-content fl-node-content">
            <div class="fl-rich-text">
                <p><span><span><a href=https://'.$_SERVER["SERVER_NAME"].'><span class="home_icon"></span></a></span> 
                    <span class="seperator_icon"></span>
                    <span class=""><a href="/resource-library/">Resource</a></span> 
                    <span class="seperator_icon"></span>
                    <span class="breadcrumb_last" aria-current="page">'.get_the_title().'</span>
                </span></p>
            </div>
        </div>
    </div>';
    return $html;
}

add_shortcode('event_breadcrumb', 'callback_event_breadcrumb');
function callback_event_breadcrumb($atts)
{
    $html = '
    <div class="fl-module fl-module-rich-text fl-node-o5kjsa1iv96h breadcrumbs" data-node="o5kjsa1iv96h">
        <div class="fl-module-content fl-node-content">
            <div class="fl-rich-text">
                <p><span><span><a href=https://'.$_SERVER["SERVER_NAME"].'><span class="home_icon"></span></a></span> 
                    <span class="seperator_icon"></span>
                    <span class=""><a href="/events/">Events</a></span> 
                    <span class="seperator_icon"></span>
                    <span class="breadcrumb_last" aria-current="page">'.get_the_title().'</span>
                </span></p>
            </div>
        </div>
    </div>';
    return $html;
}
add_shortcode('webinar_breadcrumb', 'callback_webinar_breadcrumb');
function callback_webinar_breadcrumb($atts)
{
    $html = '
    <div class="fl-module fl-module-rich-text fl-node-o5kjsa1iv96h breadcrumbs webinar-breadcrumb" data-node="o5kjsa1iv96h">
        <div class="fl-module-content fl-node-content">
            <div class="fl-rich-text">
                <p><span><span><a href=https://'.$_SERVER["SERVER_NAME"].'><span class="home_icon"></span></a></span> 
                    <span class="seperator_icon"></span>
                    <span class=""><a href="/webinars-presentations/">Webinars & Presentations</a></span> 
                    <span class="seperator_icon"></span>
                    <span class="breadcrumb_last" aria-current="page">'.get_the_title().'</span>
                </span></p>
            </div>
        </div>
    </div>';
    return $html;
}
add_shortcode('news_breadcrumb', 'callback_news_breadcrumb');
function callback_news_breadcrumb($atts)
{
    $html = '
    <div class="fl-module fl-module-rich-text fl-node-o5kjsa1iv96h breadcrumbs webinar-breadcrumb" data-node="o5kjsa1iv96h">
        <div class="fl-module-content fl-node-content">
            <div class="fl-rich-text">
                <p><span><span><a href=https://'.$_SERVER["SERVER_NAME"].'><span class="home_icon"></span></a></span> 
                    <span class="seperator_icon"></span>
                    <span class=""><a href="/latest-news/">Latest News</a></span> 
                    <span class="seperator_icon"></span>
                    <span class="breadcrumb_last" aria-current="page">'.get_the_title().'</span>
                </span></p>
            </div>
        </div>
    </div>';
    return $html;
}
# If resource has extenal link or a direct PDF then dont let the users/bots visit the detail page but redirect directly to external resource or PDF
function check_resource_external_links() {
	if (is_singular( 'resource' ) ) {

    global $post;

    if($post->ID > 0)
    {
      $external_link = get_field('external_link', $post->ID );

      $external_link = trim($external_link);
      if(!empty($external_link))
      {
        wp_redirect( $external_link, 301 );
        echo '<meta http-equiv="refresh" content="0;URL='.$external_link.'" />';
        die();
      }
    }

	}
}
add_action( 'template_redirect', 'check_resource_external_links' );

function check_webinar_external_links() {
  if (is_singular('webinar')) {
      global $post;
      
          if ($post->ID > 0) {
              $resource_url = get_field('resource_url', $post->ID);
              $keep_the_gated_page_accessible_directly = get_field('keep_the_gated_page_accessible_directly', $post->ID);

              $resource_url = trim($resource_url);

              if ($keep_the_gated_page_accessible_directly == 1) {
                  // This approach will retain direct access to the gated page while listing the resource URL as ungated.
                  // Don't automatically redirect.
              } elseif (!empty($resource_url)) {
                  wp_redirect($resource_url, 301);
                  echo '<meta http-equiv="refresh" content="0;URL=' . $resource_url . '" />';
                  die();
              }
          }
  }
}
add_action('template_redirect', 'check_webinar_external_links');

add_shortcode('event_date', 'getevent_date');
function getevent_date()
{
    $post_id = get_the_ID();
    $start_date = get_field('start_date', $post->ID); 
    $newdate = date(" M j ", strtotime( $start_date));
    $year = date(" Y ", strtotime( $start_date));
    $end_date =  get_field('end_date', $post->ID);

    $date .= '<span>'.$newdate.'- </span>';

    if(empty('.$end_date.')){
        $date .= '<span>'.$year.'</span>';
    }
    else{
        $date .= '<span>'.$end_date.'</span>';
    }
     return $date;
}


/*wp-search*/
function callback_alm_query_args_resources($args){   
  $engine = 'resource_search'; 

  $args = apply_filters('alm_searchwp', $args, $engine);
  $args['orderby'] = 'date'; 
  return $args;
}
add_filter('alm_query_args_resources', 'callback_alm_query_args_resources');


/***** Begin - UTM by Dev Rahul *****/
add_shortcode('pardot_utm', 'pardot_utm_callback');
function pardot_utm_callback($atts) {
  $atts = shortcode_atts(array(
      'src' => '',
      'width' => '100%',
      'height' => '500px',
      'class' => ' ',

  ), $atts, 'pardot_utm');

  $random = md5(mt_rand().microtime(true));
  //$random = 'some_div';

  $output = '';

  if($atts['src'])
  {
    $output = "
    <div id='pardot_container_".$random."'></div>
    <script>
    jQuery(document).ready(function($) {
            load_pardot_with_utm('".$atts['src']."', 'pardot_container_".$random."', '".$atts['width']."', '".$atts['height']."', '".$atts['class']."');
    });
    </script>";
  }

  return $output;
}
/***** End - UTM by Dev Rahul *****/





// Add inputName as class to hidden fields
add_filter( 'gform_field_input', 'hidden_field_input', 10, 5 );
function hidden_field_input( $input, $field, $value, $lead_id, $form_id ){ 
	if( $field->type == 'hidden' ){
    		$input = '<input name="input_'.$field->id.'" id="input_'.$form_id.'_'.$field->id.'" type="hidden" class="gform_hidden '.$field->label.'" aria-invalid="false" value="">';
	}
	return $input;
}

  add_action( 'template_redirect', 'nonsense_redirects' );
  function nonsense_redirects()
  {
    if ( is_tax( 'product_type' ) OR is_singular( 'mcproducts' )   ) {
      $home = home_url();
      wp_redirect( $home, 301 );
      echo '<meta http-equiv="refresh" content="0;URL='.$home.'" />';
      die();
    }

    if(is_singular( 'news' ))
    {
      global $post;

      $external_link = get_field('external_link', $post->ID);
      if(!empty($external_link))
      {
        wp_redirect( $external_link, 301 );
        echo '<meta http-equiv="refresh" content="0;URL='.$external_link.'" />';
        die();
      }

    }

  }
  
  /** 
 * Enables the HTTP Strict Transport Security (HSTS) header
 */
function tg_enable_strict_transport_security_hsts_header_wordpress() {
    header( 'Strict-Transport-Security: max-age=31536000' );
}
add_action( 'send_headers', 'tg_enable_strict_transport_security_hsts_header_wordpress' );





/***** Begin - Eloqua UTM by Dev Rahul *****/
add_shortcode('eloqua_utm', 'eloqua_utm_callback');
function eloqua_utm_callback($atts) {
  $atts = shortcode_atts(array(
      'src' => '',
      'width' => '100%',
      'height' => '700px',
      'class' => ' ',

  ), $atts, 'eloqua_utm');

  $random = md5(mt_rand().microtime(true));
  //$random = 'some_div';

  $output = '';

  if($atts['src'])
  {
    $output = "
    <div id='eloqua_container_".$random."'></div>
    <script>
    jQuery(document).ready(function($) {
            load_eloqua_with_utm('".$atts['src']."', 'eloqua_container_".$random."', '".$atts['width']."', '".$atts['height']."', '".$atts['class']."');
    });
    </script>";
  }

  return $output;
}
/***** End - Eloqua UTM by Dev Rahul *****/

add_filter( 'wpseo_breadcrumb_output', 'override_home_icon' );
function override_home_icon( string $original_breadcrumbs ): string {
    $new_home = \str_replace( '>Home<', ' aria-label="Home"><span class="home_icon"></span><', $original_breadcrumbs );

    return $new_home;
}




# Application areas of interest choices : Combine choiscs and prepare it for one SF field
add_filter("gform_pre_submission", function ($form) {
  $checkbox_values = [];

  foreach ($form["fields"] as &$field) {
      // Handle Checkbox Fields
      if ($field->adminLabel == "application_areas_of_interest_choices") {
          $checked_options = [];

          // Gravity Forms stores checkboxes as individual inputs: input_ID.X
          foreach ($field->inputs as $input) {
              $input_key = "input_" . str_replace(".", "_", $input["id"]); // Correct GF input key structure
              if (!empty($_POST[$input_key])) {
                  $checked_options[] = sanitize_text_field(
                      $_POST[$input_key]
                  );
              }
          }
      }
  }

  // Find the hidden field and store the values
  foreach ($form["fields"] as &$field) {
    if ($field->type === "hidden" && $field->label == "application_areas_of_interest_sf" ) {
        // Assign the selected checkbox values to the hidden field
        $_POST["input_" . $field->id] = implode(";", $checked_options);
    }
  }

  return $form;
});



/**
 * Sends Gravity Form ID 3 (Request a Quote) data to Eloqua (RequestaQuote-Gravity form handler)
 * - Only runs if field 17 (send to salesforce) equals "1"
 * - Maps Gravity Form fields to Eloqua field names
 * - Logs the payload to entry notes 
 * - https://trello.com/c/b1dpFat4/662-request-a-quote-gravity-form-to-eloqua
 */
add_action('gform_entry_post_save', 'send_eloqua_after_save', 10, 2);
function send_eloqua_after_save($entry, $form) {
    if ((int)$form['id'] !== 3) {
        return $entry;
    }

    # Only if Send to Salesforce is marked 1
    #if (rgar($entry, '17') !== '1') {
    #    return $entry;
    #}
    # Send all entries to Eloqua

    $eloqua_url = 'https://s66132.t.eloqua.com/e/f2';

    $checkIfHasInstrumentsInCart = rgar($entry, '17');
    if(empty($checkIfHasInstrumentsInCart))
    {
      $checkIfHasInstrumentsInCart = "0";
    }


    $data = array(
        'elqFormName'   => 'RequestaQuote-Gravity',
        'elqSiteId'     => '66132',
        'firstName'     => rgar($entry, '8.3'),
        'lastName'      => rgar($entry, '8.6'),
        'emailAddress'  => rgar($entry, '9'),
        'busPhone'      => rgar($entry, '10'),
        'title'         => rgar($entry, '29'),
        'organization'  => rgar($entry, '5'),
        'country'       => rgar($entry, '11'),
        'stateProv'     => rgar($entry, '20'),
        'zipPostal'     => rgar($entry, '33'),
        'Comments'      => rgar($entry, '7'),
        'orderDetailDescription1'      => rgar($entry, '35'),
       // 'HasInstrumentsInCart'   => rgar($entry, '17'),
        'Applications'  => rgar($entry, '34'),
        'utm_source'    => rgar($entry, '21'),
        'utm_medium'    => rgar($entry, '27'),
        'utm_campaign'  => rgar($entry, '26'),
        'utm_term'      => rgar($entry, '24'),
        'utm_content'   => rgar($entry, '23'),
        'utm_referrer'  => rgar($entry, '28'),
        'utm_gclid'     => rgar($entry, '25'),
    );

    wp_remote_post($eloqua_url, array(
        'method'  => 'POST',
        'timeout' => 20,
        'body'    => $data,
    ));

    GFAPI::add_note(
        $entry['id'],
        0,
        'System',
        //'Form entry was submitted to Eloqua.'.PHP_EOL.' Payload: '. PHP_EOL . json_encode($data).PHP_EOL.json_encode($entry).PHP_EOL.json_encode($_POST)
        'Form entry was submitted to Eloqua.'.PHP_EOL.' Payload: '. PHP_EOL . json_encode($data)
    );

    return $entry;

}


add_action('template_redirect', function() {
    if (isset($_GET['swp_form']['form_id']) && isset($_GET['s'])) {
        $search_query = sanitize_text_field($_GET['s']);
        wp_redirect(home_url("/?s=$search_query"));
        exit;
    }
});

// Fix custom post type results in search page pagination 
function include_pods_post_types_in_search($query) {
  if ($query->is_search() && $query->is_main_query() && !is_admin()) {
    $query->set('post_type', ['post', 'page', 'resource', 'webinar', 'events', 'news']);
    $query->set('posts_per_page', 10);
  }
}
add_action('pre_get_posts', 'include_pods_post_types_in_search');



// search

add_action('wp_ajax_custom_search_pagination', 'custom_search_pagination_callback');
add_action('wp_ajax_nopriv_custom_search_pagination', 'custom_search_pagination_callback');

function custom_search_pagination_callback() {
  // Ensure database connection is clean
  global $wpdb;
  $wpdb->flush();

  $search_term = isset($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';
  $search_term = strtolower(html_entity_decode(urldecode($search_term)));
  $search_term = str_replace(['–', '—'], '-', $search_term);

  $paged = isset($_POST['page']) ? absint($_POST['page']) : 1;
  $posts_per_page = 10;
  $post_types = ['page', 'resource', 'webinar', 'events', 'news'];
  $priority_products = ['GTx™', 'ATx™', 'STx™', 'VLx™'];
  $trigger_keywords = ['electroporator', 'electroporators', 'product', 'pro', 'prod', 'electro', 'products', 'electr', 'produc'];

  // SearchWP Query
  $args = [
    'post_type'      => $post_types,
    'post_status'    => 'publish',
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    's'              => $search_term,
    'orderby'        => 'relevance',
  ];

  $search_query = new WP_Query($args);
  $all_results = [];
  $priority_results = [];

  if ($search_query->have_posts()) {
    while ($search_query->have_posts()) {
      $search_query->the_post();
      $title = get_the_title();

      // Image logic
      $image_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
      if (!$image_url && function_exists('get_field')) {
        $acf_image = get_field('custom_image_field');
        if (!empty($acf_image['url'])) {
          $image_url = $acf_image['url'];
        }
      }

      $result = [
        'title'     => $title,
        'permalink' => get_permalink(),
        'excerpt'   => wp_trim_words(get_the_excerpt(), 30, '...'),
        'image'     => $image_url,
      ];

      if ($paged == 1 && in_array($title, $priority_products)) {
        $priority_results[] = $result;
      } else {
        $all_results[] = $result;
      }
    }
    wp_reset_postdata();
  }

  // Check if search contains trigger keywords
  $is_trigger_search = false;
  foreach ($trigger_keywords as $keyword) {
    if (strpos($search_term, strtolower($keyword)) !== false) {
      $is_trigger_search = true;
      break;
    }
  }

  // If trigger search, add missing priority products only on page 1
  if ($paged == 1 && $is_trigger_search) {
    foreach ($priority_products as $prod) {
      $already_in_results = false;
      foreach (array_merge($priority_results, $all_results) as $res) {
        if (strcasecmp($res['title'], $prod) === 0) {
          $already_in_results = true;
          break;
        }
      }
      if (!$already_in_results) {
        $prod_post = get_page_by_title($prod, OBJECT, $post_types);
        if ($prod_post) {
          $image_url = get_the_post_thumbnail_url($prod_post->ID, 'medium');
          if (!$image_url && function_exists('get_field')) {
            $acf_image = get_field('custom_image_field', $prod_post->ID);
            if (!empty($acf_image['url'])) {
              $image_url = $acf_image['url'];
            }
          }
          $priority_results[] = [
            'title'     => get_the_title($prod_post->ID),
            'permalink' => get_permalink($prod_post->ID),
            'excerpt'   => wp_trim_words(get_the_excerpt($prod_post->ID), 30, '...'),
            'image'     => $image_url,
          ];
        }
      }
    }
  }

  // Combine results
  $final_results = array_merge($priority_results, $all_results);

  // Generate HTML
  ob_start();
  foreach ($final_results as $result) {
    ?>
    <div class="search-result-item">
      <?php if (!empty($result['image'])) : ?>
        <div class="search-image">
          <a href="<?php echo esc_url($result['permalink']); ?>">
            <img src="<?php echo esc_url($result['image']); ?>" alt="<?php echo esc_attr($result['title']); ?>">
          </a>
        </div>
      <?php endif; ?>
      <div class="search-content">
        <a href="<?php echo esc_url($result['permalink']); ?>" class="search-title">
          <?php echo esc_html($result['title']); ?>
        </a>
        <p class="search-description"><?php echo esc_html($result['excerpt']); ?></p>
        <div class="search-domain">
          <?php echo esc_html(parse_url($result['permalink'], PHP_URL_HOST)); ?>
        </div>
        <a href="<?php echo esc_url($result['permalink']); ?>" class="search-link">
          <?php echo esc_url($result['permalink']); ?>
        </a>
      </div>
    </div>
    <?php
  }
  $html = ob_get_clean();

  wp_send_json(['html' => $html]);
  wp_die();
}