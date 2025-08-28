/* Maga Menu */
var menuElement = "#hp-mega-menu .menu-item";
var mobileToggle = '.fl-menu-mobile-toggle';

jQuery(document).ready(function() {
    // exit if bb is running
    if (jQuery('body').hasClass('fl-builder-edit'))
        return true;
    //add the helper parent class to the mega menu for mobile
    jQuery('[id^="mega-"]').first().parent().addClass('mega-mobile-container');
    //set top location of 
    jQuery(window).on('resize megamenurefresh', function() {
        if (isMobile()) // set init value
        {
            //set top value of container
            topValue = jQuery('header').offset().top + jQuery('header').outerHeight() - 20;
            jQuery('.mega-mobile-container').css('top', topValue + 'px');
        } else {
            jQuery('.mega-mobile-container').css('top', '');
            jQuery('.show-mega-mobile').removeClass('show-mega-mobile');
            topValue = 0;
        }
        jQuery(menuElement).each(function() {
            var menuItem = jQuery(this);
            var megaItem = jQuery("#" + "mega-" + menuTitle(menuItem.text()));
            if (megaItem.length) // if its  mega anything
            {
                //set up click listeners for menu items 
                // setInterval(function() {
                //     if (jQuery('#mega-platforms.show-mega').length > 0) {
                //         jQuery('#menu-main-menu li:first-child').addClass('active');
                //     } else {
                //         jQuery('#menu-main-menu li:first-child').removeClass('active');
                //     }
                // }, 100);


                setInterval(function () {
                    if(jQuery('#mega-products.show-mega').length > 0) {
                       var menuitem2 = jQuery('#menu-main-menu  li:first-child').addClass('active');
                           menuitem2.next().removeClass('active');
                            // jQuery('body').addClass('prd-menu');
                      }
                      else if(jQuery('#mega-services.show-mega').length > 0){
                         var menuitem3 = jQuery('#menu-main-menu li:nth-child(2)').addClass('active');
                          menuitem3.prev().removeClass('active'); 
                        //   jQuery('body').addClass('prd-menu');
                      }
                      else if(jQuery('#mega-applications.show-mega').length > 0){
                         var menuitem4 = jQuery('#menu-main-menu li:nth-child(4)').addClass('active');
                          menuitem4.prev().removeClass('active'); 
                        //   jQuery('body').addClass('prd-menu');
                      }
                      else{
                          jQuery('#menu-main-menu li:first-child').removeClass('active');
                          jQuery('#menu-main-menu li:nth-child(2)').removeClass('active');
                         jQuery('#menu-main-menu li:nth-child(4)').removeClass('active');
                        //   jQuery('body').removeClass('prd-menu');
                      }
  
                }, 100);



                menuItem.off('mouseover').mouseover(function(event) {
                    mm = jQuery("#" + "mega-" + menuTitle(jQuery(this).text()));
                    // if it has a mega menu
                    if (mm.length && !isMobile()) {
                        event.preventDefault();
                        event.stopPropagation();
                        if (!mm.hasClass('show-mega')) {
                            jQuery('.show-mega').removeClass('show-mega');
                            mm.addClass('show-mega');
                        } else
                            mm.addClass('show-mega');
                    }
                });

                if (isMobile()) {
                    megaItem.css('top', topValue + 'px');
                    topValue += megaItem.height();
                } else {
                    jQuery('body').removeClass('show-mega-mobile');
                    //topValue = jQuery("header").offset().top - jQuery(window).scrollTop() + jQuery("header").outerHeight();
                    topValue = (parseInt(jQuery('header').css('top')) || 0) + jQuery('header').outerHeight(false);
                    megaItem.css('top', topValue + 'px');
                }
            }
        });
    }).resize();

    // jQuery(.menu-item-platforms).mouseover(function(event){
    //     event.preventDefault(); // stop the inbuilt stuff from stuffin
    //     event.stopPropagation();
    //     jQuery('body').toggleClass('show-mega-mobile');
    // });

    if (jQuery(window).width() < 993) {
        jQuery(document).ready(function($) {
            $('.menu-item-electroporation').prepend($('.mega-mobile-container #mega-products'));
            $('.menu-item-gene-editing').prepend($('.mega-mobile-container #mega-services'));
            $('.menu-item-applications').prepend($('.mega-mobile-container #mega-applications'));
        });
    }


    //close mega menu stuff    
    // escape click    
    jQuery(document).on('keyup', function(e) {
        if (e.key == "Escape") {
            jQuery('.show-mega').removeClass('show-mega');
            jQuery('body').removeClass('show-mega-mobile');
        }
    });
    //click outside of mega menu
    jQuery(document).on('mouseover', function(e) {
        if (jQuery(e.target).closest('[id^="mega-"], .mega-mobile-container').length === 0) {
            if (jQuery('.show-mega, .show-mega-mobile').length) {
                jQuery('[id^="mega-"]').removeClass('show-mega');
                jQuery('body').removeClass('show-mega-mobile');
            }
        }
    });
});

//replace menuItem.text().replace(titleRegex,"-").toLowerCase()
function menuTitle(theText) {
    // clean it up
    theText = theText.replace(/[^0-9a-z]/gi, ' ').trim().replace(" ", "-").replace(/-{2,}/g, '-');
    return theText.toLowerCase();
}

function isMobile() {
    return (FLBuilderLayoutConfig.breakpoints.small > jQuery(window).width());
}

function megaDebug() {
    jQuery(menuElement).each(function() {
        console.log("mega-" + menuTitle(jQuery(this).text()));
    });
}


/* Equal height */
jQuery(document).ready(function($) {
    var maxHt = Math.max.apply(null, $(".case-study-section .uabb-imgicon-wrap").map(function() { return $(this).height(); }).get());
    $("<style type='text/css'> .case-study-section .uabb-imgicon-wrap { min-height:" + (maxHt + 0) + "px;} </style>").appendTo("head");

    var maxHt = Math.max.apply(null, $(".case-study-section .uabb-infobox-title-wrap").map(function() { return $(this).height(); }).get());
    $("<style type='text/css'> .case-study-section .uabb-infobox-title-wrap { min-height:" + (maxHt + 0) + "px;} </style>").appendTo("head");

    var maxHt = Math.max.apply(null, $(".leadership_team h2.uabb-infobox-title").map(function() { return $(this).height(); }).get());
    $("<style type='text/css'> .leadership_team h2.uabb-infobox-title { min-height:" + (maxHt + 0) + "px;} </style>").appendTo("head");

    var maxHt = Math.max.apply(null, $(".event-category-block .uabb-infobox-text").map(function() { return $(this).height(); }).get());
    $("<style type='text/css'> .event-category-block .uabb-infobox-text { min-height:" + (maxHt + 0) + "px;} </style>").appendTo("head");

    var maxHt = Math.max.apply(null, $(".featured-publication-four-col .publication_box .uabb-infobox-content").map(function() { return $(this).height(); }).get());
    $("<style type='text/css'> .featured-publication-four-col .publication_box .uabb-infobox-content{ min-height:" + (maxHt + 0) + "px;} </style>").appendTo("head");

    var maxHt = Math.max.apply(null, $(".electroporation-system .uabb-infobox-text").map(function() { return $(this).height(); }).get());
    $("<style type='text/css'> .electroporation-system .uabb-infobox-text{ min-height:" + (maxHt + 0) + "px;} </style>").appendTo("head");

    var maxHt = Math.max.apply(null, $(".processing-assemlies-box .uabb-infobox-content").map(function() { return $(this).height(); }).get());
    $("<style type='text/css'> .processing-assemlies-box .uabb-infobox-content{ min-height:" + (maxHt + 0) + "px;} </style>").appendTo("head");

    if ($(window).width() > 767) {
        var maxHt = Math.max.apply(null, $(".patients_member .popup_module h3.uabb-infobox-title").map(function() { return $(this).height(); }).get());
        $("<style type='text/css'> .patients_member .popup_module h3.uabb-infobox-title { min-height:" + (maxHt + 0) + "px;} </style>").appendTo("head");

        var maxHt = Math.max.apply(null, $(".patients_member .popup_module .uabb-infobox-text").map(function() { return $(this).height(); }).get());
        $("<style type='text/css'> .patients_member .popup_module .uabb-infobox-text { min-height:" + (maxHt + 10) + "px;} </style>").appendTo("head");
    }
 

});


/**
 * BEGIN -- Set all equal_elements within the collection to have the same height.
 **/
jQuery(document).ready(function($) {

    $.fn.equalHeight = function() {

        var heights = [];
        $.each(this, function(i, equal_element) {
            $equal_element = $(equal_element);
            var equal_element_height;
            // Should we include the equal_elements padding in it's height?
            var includePadding = ($equal_element.css('box-sizing') == 'border-box') || ($equal_element.css('-moz-box-sizing') == 'border-box');
            if (includePadding) {
                equal_element_height = $equal_element.innerHeight();
            } else {
                equal_element_height = $equal_element.height();
            }
            heights.push(equal_element_height);
        });
        this.height(Math.max.apply(window, heights));
        return this;
    };

    /**
     * Create a grid of equal height equal_elements.
     */
    $.fn.equalHeightGrid = function(columns) {
        var $tiles = this;
        $tiles.css('height', 'auto');
        for (var i = 0; i < $tiles.length; i++) {
            if (i % columns == 0) {
                var row = $($tiles[i]);
                for (var n = 1; n < columns; n++) {
                    row = row.add($tiles[i + n]);
                }
                row.equalHeight();
            }
        }
        return this;
    };

    /**
     * Detect how many columns there are in a given layout.
     */
    $.fn.detectGridColumns = function() {
        var offset = 0,
            cols = 0;
        this.each(function(i, elem) {
            var elem_offset = $(elem).offset().top;
            if (offset == 0 || elem_offset == offset) {
                cols++;
                offset = elem_offset;
            } else {
                return false;
            }
        });
        return cols;
    };

    /**
     * Ensure equal heights now, on ready, load and resize.
     */
    $.fn.responsiveEqualHeightGrid = function() {
        var _this = this;

        function syncHeights() {
            var cols = _this.detectGridColumns();
            _this.equalHeightGrid(cols);
        }
        $(window).bind('resize load', syncHeights);
        syncHeights();
        return this;
    };

    // On page load
    jQuery('.equal_element').responsiveEqualHeightGrid();
    jQuery('.popup_module .uabb-infobox-text p').responsiveEqualHeightGrid();
    jQuery('.popup_module .uabb-infobox-title').responsiveEqualHeightGrid();
    jQuery('.electroporation-system .uabb-infobox-title').responsiveEqualHeightGrid();
    jQuery('.electroporation-system .uabb-infobox-text-wrap').responsiveEqualHeightGrid();
    jQuery('.featured-spotlights .uabb-infobox-title').responsiveEqualHeightGrid();
    jQuery('.instrument-decription p').responsiveEqualHeightGrid();
    // jQuery('.mobile-common-section').responsiveEqualHeightGrid();  
    // jQuery('.product-info-box > .fl-col-content, .uabb-new-ib-title').responsiveEqualHeightGrid();  

    // On Ajax load more success
    window.almDone = function() {
        jQuery('.equal_element').responsiveEqualHeightGrid();
    };

});
/**
 * END -- Set all equal_elements within the collection to have the same height.
 **/



 /* Mobile menu click functionality */

jQuery(function ($) {
  $(document).ready(function () {
    if ($(window).innerWidth() <= 992) {
      $(".mobile_menu_row").on(
        "click",
        "ul.pp-slide-menu__menu li.menu-item-has-children",
        function () {
          $(this).parent().addClass("pp-slide-menu-is-active-parent");
          $(this).children("ul").addClass("pp-slide-menu-is-active");
          var getChildHeight = $(this)
            .find("ul.sub-menu.pp-slide-menu-is-active")
            .height();
          $(this).parent().height(getChildHeight);
        }
      );
    }
  });
});


//GRAVITY FORMS - MATERIAL DSIGN - ADD FOCUS TO LABEL

jQuery(document).ready(function($) {
    $(document).on('form gform_post_render', function(e) {
        $('.gform_wrapper input, .gform_wrapper textarea, .gform_wrapper select').bind('focusin', function() {
            $(this).siblings("label").addClass("focused");
            $(this).parent().siblings("label").addClass("focused");
        });
        $('.gform_wrapper input, .gform_wrapper textarea, .gform_wrapper select').bind('focusout', function() {
            if (!$(this).val() || $(".ginput_container_phone input").val() == "(___) ___-____") {
                $(this).siblings("label").removeClass("focused");
                $(this).parent().siblings("label").removeClass("focused");
            }
        });
        $(".gform_wrapper select").bind('focusout', function() {           
                $(this).siblings("label").addClass("focused");
                $(this).parent().siblings("label").addClass("focused");            
        });
        $(".gform_wrapper select").each(function(index, element) {           
                $(this).siblings("label").addClass("focused");
                $(this).parent().siblings("label").addClass("focused");            
        });
        $(".gform_wrapper input, .gform_wrapper textarea,.gform_wrapper select").each(function(index, element) {
            if ($(this).val()) {
                $(this).siblings("label").addClass("focused");
                $(this).parent().siblings("label").addClass("focused");
            }
        });
        $(document).on("click", "form .gform_button", function() {
            setTimeout(() => {
                if ($("div").find("form .gform_validation_error")) {
                    $(".gform_validation_error input, .gform_validation_error textarea").each(function(index, element) {
                        if ($(this).val()) {
                            $(this).siblings("label").addClass("focused");
                            $(this).parent().siblings("label").addClass("focused");
                        }
                    });
                }
            }, 200);
        });
    });
});

// EndGRAVITY FORMS - MATERIAL DSIGN - ADD FOCUS TO LABEL

jQuery(document).ready(function() {
    jQuery(".view-all-cta").click(function() {
        window.location = jQuery(this).find("a").attr("href");
    });
    jQuery(".view-whole-block").click(function() {
        window.location = jQuery(this).find("a").attr("href");
    });
    jQuery(document).on("click", ".whole-block-clickable", function() {
        var url = jQuery(this).find("a").attr("href");
        window.open(url, '_blank');
        return false;
    });
    jQuery(document).on("click", ".educational-events-slider .fl-slide", function() {
        var url = jQuery(this).find("a").attr("href");
        window.open(url, '_blank');
        return false;
    });
});

jQuery(document).ready(function() {
    jQuery(".close-prd-btn").click(function() {
        jQuery(".close-prd-btn .uabb-button-text").text((jQuery(".close-prd-btn .uabb-button-text").text() == 'Close Products') ? 'Add Products' : 'Close Products').fadeIn();
        jQuery(this).parent().toggleClass('add-prd-btn');
    });
    jQuery(".collapse-prd-btn").click(function() {
        jQuery(".close-prd-btn .uabb-button-text").text((jQuery(".close-prd-btn .uabb-button-text").text() == 'Close Products') ? 'Add Products' : 'Close Products').fadeIn();
        jQuery(".close-prd-btn").parent().toggleClass('add-prd-btn');
    });
    jQuery(".close-prd-btn, .collapse-prd-btn").click(function() {
        jQuery(".request-accordian-products").slideToggle("slow", function() {});
    });

    /* PDP sticky*/
    jQuery(window).on('scroll', function() {
        var WindowTop = jQuery(window).scrollTop();
        jQuery('.find-div').each(function(i) {
            if (WindowTop > jQuery(this).offset().top - 100 &&
                WindowTop < jQuery(this).offset().top + jQuery(this).outerHeight(true)
            ) {
                jQuery('.pdp-sticky-menu li a').removeClass('active');
                jQuery('.pdp-sticky-menu li').eq(i).find('a').addClass('active');
            }
        });

        jQuery('.find-col').each(function(index, elem) {
            var windowTop = jQuery(window).scrollTop();
            var offsetTop = jQuery(elem).offset().top;
            var outerHeight = jQuery(this).outerHeight(true);
            var positionRlesr = jQuery(this).position().top;
            console.log(positionRlesr);

            if (windowTop > (offsetTop - 110) && windowTop < ((offsetTop - 120) + outerHeight)) {
                var elemId = jQuery(elem).attr('id');
                jQuery(".pdf-sidebar-col .fl-list-item-content-text p a").removeClass('active-menu');
                jQuery(".pdf-sidebar-col .fl-list-item-content-text p a[href='#" + elemId + "']").addClass('active-menu');
            }
        });

    });
    jQuery(document).on('click', '.pdp-sticky-menu li a', function() {
        var elemId = jQuery(this).attr('href');
        var n = jQuery(elemId).offset().top - 80;
        jQuery('html, body').animate({
            scrollTop: n
        }, 50, 'linear');
    });
/* 
    jQuery(document).ready(function($) {
        $(document).on('click', '.contact-tabs a', function(event)  {
            event.preventDefault(); 

            var targetElementId = $(this).attr('href');
            window.location.href = targetElementId; 
        });
        
        var urlHash = window.location.hash; 
        setTimeout(() => {
            if (urlHash) {
                var targetElementId = urlHash;
                
                var offset = 0; 
                var targetPosition = jQuery(targetElementId).offset().top + offset;
                jQuery('html, body').animate({
                    scrollTop: targetPosition
                }, 50, 'linear');
            }
        }, 200);  
    });
 */

    

    jQuery(document).on('click', '.career-cta a', function() {
        var elemId = jQuery(this).attr('href');
        var n = jQuery(elemId).offset().top - 0;
        jQuery('html, body').animate({
            scrollTop: n
        }, 80, 'linear');
    });

    // jQuery(".element-is-not-sticky > .fl-row-content-wrap").hide();
    jQuery(window).scroll(function(event) {
        jQuery("body").find(".element-is-sticky");
        jQuery(".element-is-sticky > .fl-row-content-wrap").show();
    });


    /* PDF to HTML/Crispr Electroporatio*/
    jQuery(document).on('click', '.pdf-sidebar-col .fl-list-item-content-text p a', function(e) {
        var elemId = jQuery(this).attr('href');
        var n = jQuery(elemId).offset().top - 30;
        jQuery('html, body').stop().animate({
            scrollTop: n
        }, 500, 'linear');
    });

});


// jQuery(document).ready(function($) {

//     var incrementPlus;
//     var incrementMinus;

//     var buttonPlus = $(".cart-qty-plus");
//     var buttonMinus = $(".cart-qty-minus");

//     var incrementPlus = buttonPlus.click(function() {
//         var $n = $(this)
//             .parent(".button-container")
//             .find(".qty");
//         $n.val(Number($n.val()) + 1);
//     });

//     var incrementMinus = buttonMinus.click(function() {
//         var $n = $(this)
//             .parent(".button-container")
//             .find(".qty");
//         var amount = Number($n.val());
//         if (amount > 0) {
//             $n.val(amount - 1);
//         }
//     });
// });


jQuery(document).ready(function($) {
    let pillButtonOnText = $('.pill-button-selection_on'),
        pillButtonOffText = $('.pill-button-selection_off'),
        pillButtonHighlight = $('.pill-button-highlight'),
        pillButtonOnTextWidth = pillButtonOnText.outerWidth(),
        pillButtonOffTextWidth = pillButtonOffText.outerWidth(),
        pillButtonOnTextPosition = pillButtonOnText.position(),
        pillButtonOffTextPosition = pillButtonOffText.position(),
        pillButtonInput = $('.pill-button-input');

    $('.pillButtonHighlight').css('width', pillButtonOnTextWidth);

    $('.pill-button-selection').on('click', function() {
        if (!$(this).hasClass('pill-button-selection_active')) {
            $('.pill-button-selection').removeClass('pill-button-selection_active');
            $(this).addClass('pill-button-selection_active');

            if ($(this).hasClass('pill-button-selection_off') && pillButtonInput.prop('checked', true)) {
                pillButtonInput.prop('checked', false);
                pillButtonHighlight.css({
                    'width': pillButtonOffTextWidth,
                    'left': pillButtonOffTextPosition.left
                });
                console.log("Is Checked - OFF");
            } else {
                pillButtonInput.prop('checked', true);
                pillButtonHighlight.css({
                    'width': pillButtonOnTextWidth,
                    'left': pillButtonOnTextPosition.left
                });
                console.log("Is Checked - ON");
            }
        }
    });

    if (pillButtonInput.prop('checked', true)) { // default on cold start
        // console.log('is checked - cold start');
        pillButtonHighlight.css('width', pillButtonOnTextWidth);
    } else {
        //console.log('is not checked - cold start');
        pillButtonHighlight.css('width', pillButtonOffTextWidth);
    }


    $('.pill-button').on('click', function() {
        /*$('.assemblies-list').css("display", "none");
        $('.assemblies-blocks').css("display", "none");*/
        $(this).toggleClass("active-list");
        if ($('.active-list').length) {
            $('#ajax-load-more').addClass('list-active');
        } else {
            $('#ajax-load-more').removeClass('list-active');
        }
    });

    $('.request-accordian-products .uabb-infobox').on('click', function() {
        $(this).toggleClass("active-products");

    });


    //$(window).on('load', function () {
    if ($('#processing-assembly').length > 0) {

        console.log(window.location.href);

        processing_assembly_href = window.location.href;


        var check_if_exists = processing_assembly_href.search("#processing-assembly");

        if (check_if_exists > 0) {

            split_url = processing_assembly_href.split("processing-assembly-");

            console.log(split_url[1]);

            paramValue = split_url[1];

            var ul = $('.tab-' + paramValue).closest("ul");
            var li = $('.tab-' + paramValue).closest("li");

            var selected_tab_index = $(li).data('index');

            $(ul).each(function(i, items_list) {
                $(items_list).find('li').each(function(j, li) {
                    $(li).removeClass('uabb-tab-current');

                    var tab_index = $(li).data('index');

                    if (tab_index == selected_tab_index) {
                        $(li).addClass('uabb-tab-current');
                        $('#section-bar-' + tab_index).addClass('uabb-content-current');
                    } else {
                        $(li).removeClass('uabb-tab-current');

                        $('#section-bar-' + tab_index).removeClass('uabb-content-current');
                    }
                });
            });

            $('html, body').animate({
                scrollTop: $("#processing-assembly").offset().top- 30
            }, 1000);
        }
    }

});

// homepage logo
jQuery(document).ready(function($) {
  var content = $('.show-more-section');
  var expandBtn = $('.show-more p');

  $(".show-more p").click(function() {
    if (content.hasClass('expanded')) {
      content.removeClass('expanded');
      expandBtn.text('Show More');
    } else {
      content.addClass('expanded');
      expandBtn.text('Show Less');
      content.addClass('expanded');
    }
  });
 });

jQuery(document).on('click', 'p.img-text-logos, p.text-logo, p.coloured-text-logo', function(){
    let cols = jQuery(this).attr('class').split(" ")[0];
    if(jQuery(this).hasClass('expand')){
        jQuery(`div.${cols}`).removeClass('expand')
        jQuery(this).removeClass('expand')
        jQuery(this).text("show more")
    }else{
        jQuery(`div.${cols}`).addClass('expand')
        jQuery(this).addClass('expand')
        jQuery(this).text("show less")
    }
});

//safari css 
jQuery(document).ready(function() {
  if(navigator.platform.indexOf('Mac') > -1){
    jQuery("body").addClass("macintosh");
  }
  else {
    jQuery("body").addClass("window");
  }
});


/*request a quote */

jQuery(document).ready(function($) {
    $('.request_quote').click(function(e) {
      e.preventDefault();
      var sku =  $(this).attr("data-value");
      var randon_query = Math.random();
      console.log(sku);
      $.ajax({
        type: 'POST',
        url: maxcyte_ajax,
        data: {
          action: 'maxcyte_ajax_handler',
          sku:sku,
          randon_query:randon_query,
        },
        success: function(response) {
          console.log(response);

          if(response.success == true)
          {
            var protocol = window.location.protocol;

            var httpHost = window.location.hostname;
           /* var httpHref = window.location.href;
            console.log(protocol);
            console.log(httpHost);
            console.log(httpHref);*/


            window.location.href = protocol+"//"+httpHost+"/request-a-quote/";


          }
          else if(response.success == false)
          {
            alert('SKU not found, please try again. If the error persists please contact customer support. Error Code: x2001');
          }
          else {
            alert('Something went wrong please try again. If the error persists please contact customer support. Error Code: x2002');
          }
        },
        error: function() {
          alert('Something went wrong please try again. If the error persists please contact customer support. Error Code: x2003');
        }
      });
    });
});


// homepage logo
jQuery(document).ready(function($) {
    var content = $('.show-more-section');
    var expandBtn = $('.show-more p');
jQuery(document).ready(function($) {
    var url = window.location.href;
    if (url.indexOf("#") !== -1) {
    //  var fragment = url.split("#")[1]; // Get the part after the '#'
      var formElement = document.getElementById("form");
        if (formElement) {
            formElement.scrollIntoView({ behavior: 'smooth' }); // Scroll to the form with smooth animation
        }

    } else {
     console.log("URL does not contain '#' symbol");
    }
});
  
    $(".show-more p").click(function() {
      if (content.hasClass('expanded')) {
        content.removeClass('expanded');
        expandBtn.text('Show More');
      } else {
        content.addClass('expanded');
        expandBtn.text('Show Less');
        content.addClass('expanded');
      }
    });
   });
  
jQuery(document).ready(function($) {
    reload_cart(); 

    function reload_cart() {       
        setTimeout(() => {
            if ($("div").find(".table-data td input.input-text.qty")) {
                var inputArr = [];
                $('input.input-text.qty').each(function() {
                    var id = $(this).attr('name');
                    var product_id = id.split('[')[1].split(']')[0];
                    if(id !== undefined) {
                    inputArr.push(
                        product_id
                    );
                    }
                });
            }
            if ($("div").find(".add-to-cart-button")) {
                    $('.add-to-cart-button').each(function() {
                    var id = $(this).attr('data-product-id');
                    if (inputArr.includes(id)) {
                        $(this).find(".maxcyte-font-plus").removeClass("maxcyte-font-plus").addClass("maxcyte-font-check-request-quote");
                        $(this).attr("disabled", "disabled").css("pointer-events", "none");
                    } else {
                        $(this).find(".maxcyte-font-check-request-quote").removeClass("maxcyte-font-check-request-quote").addClass("maxcyte-font-plus");
                        $(this).removeAttr("disabled").css("pointer-events", "auto");
                    }
                });
            }
        }, 500);
    }

    load_Cart();
    function load_Cart(){
        reload_cart(); 
        if(page == "/request-a-quote/" ){
            $.ajax({
                    type: 'POST',
                    url: maxcyte_ajax,
                    data: {
                        action: 'load_cart',
                    },
                    success: function(response) {
                        var response = JSON.parse(response);
                        if(response.status == 'success')
                        {
                            $('.cart-contents').html(response.cart);
                        }

                    },
                    error: function(xhr, textStatus, errorThrown) {
                        console.log(xhr.responseText);
                    }
                });
        }
    }
    $('.add-to-cart-button').click(function() {
        reload_cart(); 
        var product_id = $(this).data('product-id');
        var quantity = $(this).data('quantity');
        var product_name = $(this).attr('product_name');
        var flagValue = $(this).attr('data-event') === 'detail-page' ? 1 : 0;
        console.log($(this).attr('data-event'));
        $.ajax({
        type: 'POST',
        url: maxcyte_ajax,
        data: {
            action: 'add_to_cart',
            product_id: product_id,
            quantity: quantity,
            product_name: product_name,
        },
        beforeSend: function() {
            if(flagValue == 1){
                $('.add-to-cart-button').each(function() {
                    if ($(this).attr('product_name') == product_name) {
                      $(this).siblings('span').css("display", "block");
                    }
                });
                // $('.productloading').css("display", "block");
            }
        },
        success: function(response) {
            var response = JSON.parse(response);
            if(response.success == 1)
            {
                if(flagValue == 1){
                    window.location.href = '/request-a-quote/#contact';
                }
                else
                {
                    reload_cart(); 
                    $('.cart-contents').html(response.cart_contents);
                }
            }
        },
        error: function(xhr, textStatus, errorThrown) {
            console.log(xhr.responseText);
        }
        // complete:function() {
        //     $('.productloading').css("display", "none");
        // }
        });
    });
    $(document).on('submit', '#update-form', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: maxcyte_ajax,
            data: {
                action: 'update_to_cart',
                data: formData,
            },
            beforeSend: function() {
                $('.productloader').css("visibility", "visible");
            },
            success: function(response) {
                var response = JSON.parse(response);
                if(response.success == 1)
                {
                    reload_cart();                
                    $('.cart-contents').html(response.cart_contents);
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log(xhr.responseText);
            }
    }); 
    });
    $(document).on("click", ".cart-qty-plus", function() {
        setTimeout(() => {
            if ($("div").find(".button-container .cart-qty-plus")) {
                var $n = $(this).parent(".button-container").find(".qty");
                $n.val(Number($n.val()) + 1);
            }
        }, 200);
    });
    $(document).on("click", ".cart-qty-minus", function() {
        setTimeout(() => {
            if ($("div").find(".button-container .cart-qty-minus")) {
                var $n = $(this).parent(".button-container").find(".qty");
                var amount = Number($n.val());
                if (amount > 0) {
                    $n.val(amount - 1);
                }
            }
        }, 2500);
    }); 
});
jQuery(document).ready(function($) {
    // Initially hide the cart-contents element if $_SESSION['cart'] is empty
    if (typeof $_SESSION !== 'undefined' && $_SESSION['cart'] === undefined) {
        $('.cart-contents').hide();
    }

    // Triggered when the form is submitted
    $(document).on('gform_submit', function(event, formId, currentPage) {
        // Check if $_SESSION['cart'] is empty
        if (typeof $_SESSION !== 'undefined' && $_SESSION['cart'] === undefined) {
            $('.cart-contents').hide();
        } else {
            $('.cart-contents').show();
        }
    });
});


jQuery(document).ready(function($) {
    var url = window.location.href;
    if (url.indexOf("#") !== -1) {
     //var fragment = url.split("#")[1]; // Get the part after the '#'
      var formElement = document.getElementById("form");
        if (formElement) {
            formElement.scrollIntoView({ behavior: 'smooth' }); // Scroll to the form with smooth animation
        }
    } else {
    
    }
});



/***** Begin - UTM by Dev Rahul *****/

    // Dev: Rahul_D
    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + encodeURIComponent(value) + expires + "; path=/";
    }
  
    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
        }
        return null;
    }
  
    function getParameterFromURL(url, parameter) {
        var queryParams = new URLSearchParams(new URL(url).search);
        return queryParams.get(parameter);
    }
  
    var url = window.location.href;
    var url = url.toLowerCase();
  

    // https://maxcyte.supremeclients.com/?utm_source=google&utm_medium=cpc&utm_campaign=spring_sale&utm_term=running+shoes&utm_content=ad_variation_1&gclid=Cj0KCQjwjoH0BRD6ARIsAEWO9DuTsi5JJRtI5
  
    var utm_source = getParameterFromURL(url, "utm_source");
    var utm_medium = getParameterFromURL(url, "utm_medium");
    var utm_campaign = getParameterFromURL(url, "utm_campaign");
    var utm_gclid = getParameterFromURL(url, "gclid");
    var utm_term = getParameterFromURL(url, "utm_term");
    var utm_content = getParameterFromURL(url, "utm_content");

    
    if (utm_source) {
        // Create a JSON object to store all parameters
        var cookieData = {
            utm_source: utm_source,
            utm_medium: utm_medium,
            utm_campaign: utm_campaign,
            utm_gclid: utm_gclid,
            utm_term: utm_term,
            utm_content: utm_content,
        };
        
        // Convert the JSON object to a string and save it as a cookie for 30 days
        setCookie("utm_params", JSON.stringify(cookieData), 30);
    }



    var utm_referrer = getCookie("utm_referrer");
    if(!utm_referrer){

        var utm_referrer = '';
        if(getParameterFromURL(url, "gclid") || getParameterFromURL(url, "gads"))
        {
            utm_referrer = 'Google Ads';
        }
        else if(getParameterFromURL(url, "msclkid") )
        {
            utm_referrer = 'Bing Ads';
        }
        else if(getParameterFromURL(url, "fbclid") )
        {
            utm_referrer = 'Facebook Ads';
        }
        else if(getParameterFromURL(url, "yclid") )
        {
            utm_referrer = 'Yandex';
        }
        else if(getParameterFromURL(url, "crid") )
        {
            utm_referrer = 'Baidu';
        }     
        else {
            var referrer = document.referrer;
            var currentDomain = window.location.hostname;
            if(referrer)
            {
                var referrerDomain = new URL(referrer).hostname;
                if (referrer && currentDomain !== referrerDomain) {
                    utm_referrer = referrer;
                } else {
                // console.log("Referrer is from the current domain or not available.");
                }
            }

        }

        if(utm_referrer)
        {
            //alert(utm_referrer);
            setCookie("utm_referrer", utm_referrer, 30);
        }
    }
    




jQuery(document).ready(function($) {

    // Check if a Gravity form is loaded
  $(document).on('gform_post_render', function(event, form_id, current_page) {
    
    var utmParamsCookie = getCookie("utm_params");

    if (utmParamsCookie) {
        var utmParams = JSON.parse(utmParamsCookie);
        if (typeof utmParams.utm_campaign === 'undefined') {
            // The variable is undefined
            //console.log('No UTM available to track');
        } else {

            // console.log(utmParams);

            var hiddenInputs = $('input[type="hidden"]');
            hiddenInputs.each(function() {

                if ($(this).hasClass('utm_source')) {
                    $(this).val(utmParams.utm_source);
                }
                if ($(this).hasClass('utm_medium')) {
                    $(this).val(utmParams.utm_medium);
                }
                if ($(this).hasClass('utm_campaign')) {
                    $(this).val(utmParams.utm_campaign);
                }
                if ($(this).hasClass('utm_gclid')) {
                    $(this).val(utmParams.utm_gclid);
                }
                if ($(this).hasClass('utm_term')) {
                    $(this).val(utmParams.utm_term);
                }
                if ($(this).hasClass('utm_content')) {
                    $(this).val(utmParams.utm_content);
                }
               
            });

        }

    }


    var utm_referrer = getCookie("utm_referrer");

    if(utm_referrer)
    {
        var hiddenInputs = $('input[type="hidden"]');
        hiddenInputs.each(function() {
            if ($(this).hasClass('utm_referrer')) {
                $(this).val(utm_referrer);
            }
        });
    }



});
});


    function load_pardot_with_utm(pardot_src, pardot_container, width="100%", height="500px", iframe_class="")
    {
        var utmParams = getCookie("utm_params");
        utmParams = JSON.parse(utmParams);

        let utm = [];
        let url_query_string;

        if (utmParams ) {
            if('utm_source' in utmParams && utmParams.utm_source!==null) { utm.push("utm_source=" + utmParams.utm_source); }
            if('utm_medium' in utmParams && utmParams.utm_medium!==null) { utm.push("utm_medium=" + utmParams.utm_medium); }
            if('utm_campaign' in utmParams && utmParams.utm_campaign!==null) { utm.push("utm_campaign=" + utmParams.utm_campaign); }
            if('utm_gclid' in utmParams && utmParams.utm_gclid!==null) { utm.push("utm_gclid=" + utmParams.utm_gclid); }
            if('utm_content' in utmParams && utmParams.utm_content!==null) { utm.push("utm_content=" + utmParams.utm_content); }
            if('utm_term' in utmParams && utmParams.utm_term!==null) { utm.push("utm_term=" + utmParams.utm_term); }
        }

        // explicit only for UTM Referrer
        var utm_referrer = getCookie("utm_referrer");
        if(utm_referrer && utm_referrer!==null && utm_referrer!=='undefined') { utm.push("utm_referrer=" + utm_referrer); } 
        if(utm) { url_query_string = utm.join('&'); }

        // Combine url_query_string and attach with Pardot URL
        if( url_query_string !== undefined && url_query_string!==null)
        {
            pardot_src += "?"+url_query_string;
        }

        console.log(url_query_string);     
        
        if( pardot_src !== undefined && pardot_src!==null)
        {
            var pardot_iframe = '<iframe class="'+iframe_class+'" src="'+pardot_src+'" width="'+width+'" height="'+height+'" type="text/html" frameborder="0" allowTransparency="true" style="border: 0"></iframe>';
            var container = document.getElementById(pardot_container);
            container.innerHTML = pardot_iframe;
        }


      // console.log(pardot_iframe);

    }
    
    function adjustIframeHeight(event) {
        if (event.origin === 'https://info.maxcyte.com') { 
            var iframe = document.getElementById('redeem_offer');
            iframe.style.height = event.data + 'px';
        }
    }

    window.addEventListener('message', adjustIframeHeight, false);
    

    /*

    [pardot_utm src="https://info.maxcyte.com/l/299722/2023-11-29/kqslt" height="730px"]

    <div id="pardot_container_112"></div>
    <script>
    jQuery(document).ready(function($) {
            load_pardot_with_utm('https://info.maxcyte.com/l/299722/2023-11-29/kqslt','pardot_container_112', '100%', '730px','some_class');
    });
    </script>
    */



    
    function load_eloqua_with_utm(eloqua_src, eloqua_container, width="100%", height="700px", iframe_class="")
    {
        var utmParams = getCookie("utm_params");
        utmParams = JSON.parse(utmParams);

        let utm = [];
        let url_query_string;

        if (utmParams ) {
            if('utm_source' in utmParams && utmParams.utm_source!==null) { utm.push("utm_source=" + utmParams.utm_source); }
            if('utm_medium' in utmParams && utmParams.utm_medium!==null) { utm.push("utm_medium=" + utmParams.utm_medium); }
            if('utm_campaign' in utmParams && utmParams.utm_campaign!==null) { utm.push("utm_campaign=" + utmParams.utm_campaign); }
            if('utm_gclid' in utmParams && utmParams.utm_gclid!==null) { utm.push("utm_gclid=" + utmParams.utm_gclid); }
            if('utm_content' in utmParams && utmParams.utm_content!==null) { utm.push("utm_content=" + utmParams.utm_content); }
            if('utm_term' in utmParams && utmParams.utm_term!==null) { utm.push("utm_term=" + utmParams.utm_term); }
        }

        // explicit only for UTM Referrer
        var utm_referrer = getCookie("utm_referrer");
        if(utm_referrer && utm_referrer!==null && utm_referrer!=='undefined') { utm.push("utm_referrer=" + utm_referrer); } 
        if(utm) { url_query_string = utm.join('&'); }

        // Combine url_query_string and attach with Eloqua URL
        if( url_query_string !== undefined && url_query_string!==null)
        {
            eloqua_src += "?"+url_query_string;
        }

        console.log(url_query_string);     
        
        if( eloqua_src !== undefined && eloqua_src!==null)
        {
            var eloqua_iframe = '<iframe class="'+iframe_class+' eloqua_iframe_iframe" src="'+eloqua_src+'" width="'+width+'" height="'+height+'" type="text/html" frameborder="0" allowTransparency="true" style="border: 0"></iframe>';
            var container = document.getElementById(eloqua_container);
            container.innerHTML = eloqua_iframe;
        }


      // console.log(eloqua_iframe);

    }


/***** End - UTM by Dev Rahul *****/

jQuery(document).on('click', '.testimonial-controls .uabb-testimonial-photo', function(){
    jQuery(this).closest(".uabb-testimonial").find("a.uabb-infobox-cta-link span").trigger("click")
});


// jQuery(document).ready(function() {
//     var isBeaverBuilderActive = jQuery('body').hasClass('fl-builder-edit'); // Beaver Builder typically adds a class like this when active

//     if (!isBeaverBuilderActive) {
//         window.addEventListener('resize', function() {
//             if (window.innerWidth >= 992) {
//                 window.location.reload();
//             }
//         });
//     }
//     console.log(window.innerWidth);
// });
    



jQuery(document).ready(function() {


    var utmParams = getCookie("utm_params");
    utmParams = JSON.parse(utmParams);

    let utm = [];
    let url_query_string;

    if (utmParams ) {
        if('utm_source' in utmParams && utmParams.utm_source!==null) { utm.push("utm_source=" + utmParams.utm_source); }
        if('utm_medium' in utmParams && utmParams.utm_medium!==null) { utm.push("utm_medium=" + utmParams.utm_medium); }
        if('utm_campaign' in utmParams && utmParams.utm_campaign!==null) { utm.push("utm_campaign=" + utmParams.utm_campaign); }
        if('utm_gclid' in utmParams && utmParams.utm_gclid!==null) { utm.push("utm_gclid=" + utmParams.utm_gclid); }
        if('utm_content' in utmParams && utmParams.utm_content!==null) { utm.push("utm_content=" + utmParams.utm_content); }
        if('utm_term' in utmParams && utmParams.utm_term!==null) { utm.push("utm_term=" + utmParams.utm_term); }
    }

    // explicit only for UTM Referrer
    var utm_referrer = getCookie("utm_referrer");
    if(utm_referrer && utm_referrer!==null && utm_referrer!=='undefined') { utm.push("utm_referrer=" + utm_referrer); } 

    // Flag
    utm.push("checked_utm=1");

    if(utm) { url_query_string = utm.join('&'); }




var iframe = jQuery('iframe.uabb-content-iframe'); // Target only iframes with the class 'uabb-content-iframe'

// Listen for iframe load event
iframe.on('load', function() {

    var iframeSrc = jQuery(this).attr('src'); // Get the src attribute of the current iframe
   // console.log('Before : ' + iframeSrc);

    // Check if UTM parameters are missing
    if (iframeSrc.includes('go.maxcyte.com') && !iframeSrc.includes('checked_utm')) {
        // Define UTM parameters to append

        console.log('Inside : ' + iframeSrc);

        // Combine url_query_string and attach with Eloqua URL
        if( url_query_string !== undefined && url_query_string!==null)
        {
              // Check if there is already a query string in the src
         var newSrc = iframeSrc.includes('?') ? iframeSrc + '&' + url_query_string : iframeSrc + '?' + url_query_string;

         console.log('After : ' + newSrc);
        }
        
        // Set the new src with UTM parameters
        jQuery(this).attr('src', newSrc);
       
    }
});
});

