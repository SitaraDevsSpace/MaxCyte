<?php
/**
 * @package FishPig_WordPress
 * @author  Ben Tideswell (ben@fishpig.com)
 * @url     https://fishpig.co.uk/magento/wordpress-integration/
 */
// phpcs:ignoreFile -- this file is a WordPress theme file and will not run in Magento
namespace FishPig\WordPress\X;

class Psw
{
    /**
     * @const string
     */
    const RENDERER_PARAM_NAME = 'fishpig_renderer';
    
    /**
     * @const string
     */
    const RENDERER_PATH = 'fishpig/render';
    
    /**
     * @const string
     */
    const SCRIPT_ROUTING_PREFIX = '/fishpig/js/';

    /**
     * @var bool
     */
    private $flipHomeUrl = false;

    /**
     *
     */
    public function __construct()
    {

        $this->fixPathInfo();
        $this->initScriptRouting();

        // Divi
        if (isset($_GET['et_fb'])) {
            $this->flipHomeUrl = true;

            add_filter(
                'fishpig_html_tag',
                function($htmlTag) {
                    if ($htmlTag === 'fishpig:body') {
                        return 'div';
                    }
                    
                    return 'footer';
                }
            );
            
            add_action(
                'wp_footer',
                function() {
                    wp_dequeue_style('wp-auth-check');
                    wp_dequeue_script('wp-auth-check');
                    remove_action('wp_print_footer_scripts', 'et_fb_output_wp_auth_check_html', 5);
                },
                12
            );
        }

        // Elementor Pro License fix
        // This tells Elementor to use the site URL rather than the home URL for license requests
        add_filter('elementor_pro/license/api/use_home_url', '__return_false');

        /**
         * Change the Home URL to the siteurl for certain requests
         * This helps with edit requests for page builders as it puts WordPress out of headless mode
         */
        add_filter(
            'home_url', 
            function($url, $path, $orig_scheme, $blog_id) {
                $isHomeUrlSiteUrl = $this->flipHomeUrl
                    || isset($_GET['fl_builder'])
                    || (isset($_GET['post_type']) && $_GET['post_type'] === 'elementor_library');

                if ($isHomeUrlSiteUrl) {
                    return get_site_url();
                } elseif (!empty($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI'])) {
                    if (!empty($_SERVER["QUERY_STRING"]) && strpos($_SERVER["QUERY_STRING"], 'rest_route=') !== false) {
                        return $url;
                    }
        
                    $currentUrl = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on' ? 'https://' : 'http://')
                                    . $_SERVER['HTTP_HOST'] 
                                    . rtrim($_SERVER['REQUEST_URI'], '/');
                                    
                    if (($qPos = strpos($currentUrl, '?')) !== false) {
                        $currentUrl = rtrim(substr($currentUrl, 0, $qPos), '/');
                    }
        
                    if (get_site_url() . '/index.php' === $currentUrl) {
                        return get_site_url();
                    }
                }
                
                return $url;
            },
            100,
            4 
        );

        // Setup the URL used for rendering WordPress content for use in Magento
        // This is used when the Magento URL does not exist in WordPress. This gives us a valid URL in WordPress
        // to render content, which ensures assets are generated correctly
        add_action(
            'init',
            function() {
                add_filter('query_vars', function($vars) {
                    $vars[] = self::RENDERER_PARAM_NAME;
                    return $vars;
                });

                add_action('template_include', function($template) {
                    $rendererTemplate = __DIR__ . '/psw/renderer-template.php';

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' 
                        && strpos($_SERVER['REQUEST_URI'], '/index.php/' . self::RENDERER_PATH) !== false) {
                        $template = $rendererTemplate; 
                    } elseif ((int)get_query_var(self::RENDERER_PARAM_NAME, 0) === 1) {
                        $template = $rendererTemplate;
                    }

                    return $template;
                });

                add_rewrite_rule('^' . self::RENDERER_PATH. '$', 'index.php?' . self::RENDERER_PARAM_NAME . '=1', 'top');
            }
        );
        
        // Taxonmy filter list
        add_filter(
            'fishpig_api_data_taxonomy_ignore_list',
            function($taxonomies) {
                return array_merge(
                    $taxonomies,
                    [
                        'elementor_library_type',
                        'elementor_library_category',
                        'tribe_events_cat',
                        'elementor_font_type',
                    ]
                );
            }
        );
        
        // Post type filter list
        add_filter(
            'fishpig_api_data_post_type_ignore_list',
            function(array $postTypes) {
                return array_merge(
                    $postTypes,
                    [
                        'tribe_events', 
                        'elementor_library',
                        'e-landing-page'
                    ]
                );
            }
        );
        
        //
        add_filter(
            'elementor/editor/localize_settings',
            [$this, 'convertElementorEditToLocalUrl']
        );
        
        //
        add_filter(
            'fishpig_index_template_after_loop_html',
            function ($html) {
                return $html . $this->getPayloadHtml();
            }
        );
        
        //
        add_filter(
            'fishpig_psw_renderer_template_html_payload',
            function ($html) {
                return $html . $this->getPayloadHtml();
            }
        );    
        
        add_action(
            'elementor/page_templates/header-footer/after_content',
            function() {
                echo $this->getPayloadHtml();
            }
        );
    }
    
    /**
     * @return void
     */
    private function initScriptRouting(): void
    {
        $requestUri = !empty($_SERVER['REQUEST_URI']) ? trim($_SERVER['REQUEST_URI']) : '';

        if (($pos = strpos($requestUri, self::SCRIPT_ROUTING_PREFIX)) !== false) {
            $relativeJsSourceFile = substr($requestUri, $pos + strlen(self::SCRIPT_ROUTING_PREFIX));
        
            if (($pos = strpos($relativeJsSourceFile, '?')) !== false) {
                $relativeJsSourceFile = substr($relativeJsSourceFile, 0, $pos);
            }
            
            if (substr($relativeJsSourceFile, -3) !== '.js') {
                return;
            }
            
            $jsSourceFile = realpath(ABSPATH . $relativeJsSourceFile);
            
            if (!$jsSourceFile || strpos($jsSourceFile, ABSPATH) !== 0) {
                return;
            }
            
            $data = file_get_contents($jsSourceFile);

    
    
            $data = $this->applyFilePatches($jsSourceFile, $data);
            
            $jsTargetFile = ABSPATH . ltrim(self::SCRIPT_ROUTING_PREFIX, '/') . $relativeJsSourceFile;
            $jsTargetPath = dirname($jsTargetFile);
            
            if (!is_dir($jsTargetPath)) {
                mkdir($jsTargetPath, 0755, true);
            }
            
            if (is_dir($jsTargetPath)) {
                file_put_contents($jsTargetFile, $data);
            }
    
            header('Content-Type: application/javascript');
            echo $data;
            exit; // phpcs:ignore
        }
    }
    
    private function applyFilePatches($jsSourceFile, $data)
    {
        $data = str_replace('define.amd',     'define.zyx', $data);
        $data = str_replace('typeof exports', 'typeof exportssdfsdfsdf', $data);
        
        if (strpos($jsSourceFile, 'elementor/assets/js/frontend.min.js')) {
            $find = 'e=e instanceof jQuery?e[0]:e';
            $prepend = "e=typeof e.dispatchEvent==='undefined'&&typeof e[0].dispatchEvent==='function'?e[0]:e;";
            $data = str_replace($find, $prepend . $find, $data);
        }
        
        return $data;
    }
    
    /**
     * @return string
     */
    private function getPayloadHtml(): string
    {
        if (!isset($_POST['_render_payload'])) {
            return '';
        }

        $html = [];

        foreach ($_POST['_render_payload'] as $type => $items) {
            if ($type === 'shortcode') {
                foreach ($items as $id => $shortcode) {
                    $html[] = $this->buildTag(
                        'shortcode',
                        $id,
                        do_shortcode(
                            wp_unslash($shortcode) // Required to remove slashes added to $_POST
                        )
                    );
                }
            } elseif ($type === 'widget') {
                foreach ($items as $id => $widgetId) {
                    if (($uPos = strrpos($widgetId, '-')) === false) {
                        continue;
                    }

                    $widgetName = substr($widgetId, 0, $uPos);
                    $widgetIndex = (int)substr($widgetId, $uPos+1);
                    

                    if (!($widgetData = get_option('widget_' . $widgetName))) {
                        continue;
                    }

                    if (empty($widgetData[$widgetIndex])) {
                        continue;
                    }
                    
                    $widgetInstanceOptions = $widgetData[$widgetIndex];

                    $instance = false;

                    global $wp_widget_factory, $wp_registered_widgets;

                    $args = [
                        'widget_id' => $widgetId,
                        'widget_name' => isset($wp_registered_widgets[$widgetId]['name']) ? $wp_registered_widgets[ $widgetId ]['name'] : '',
                        'before_widget' => '<div class="block block-blog">',
                        'after_widget' => '</div>',
                        'before_title' => '<div class="block-title"><strong><span>',
                        'after_title' => '</span></strong></div>'
                    ];

                    if (!empty($widgetInstanceOptions['title'])) {
                        $args['after_title'] .= '<div class="block-content">';
                        $args['after_widget'] = '</div></div>';
                    } else {
                        $args['before_widget'] .= '<div class="block-content">';
                        $args['after_widget'] = '</div></div>';
                    }

                    foreach ($wp_widget_factory->widgets as $key => $value) {

                        if ($value->id === $widgetId || strpos($widgetId, $value->id) === 0) {
                            $instance = $value;
                            $widgetId = $key;
                            break;
                        }
                    }

                    if (!$instance) {
                        continue;
                    }

                    $newInstance = clone $instance;

                    if ($widgetInstanceOptions) {
                        foreach ($widgetInstanceOptions as $option => $value) {
                            $newInstance->$option = $value;
                        }
                    }

                    ob_start();

                    the_widget($widgetId, $newInstance, $args);

                    $output = ob_get_clean();
                    $output = str_replace('<li>', '<li class="item">', $output);

                    $html[] = $this->buildTag('widget', $id, $output);
                }
            } elseif ($type === 'post') {
                $isElementor = defined('ELEMENTOR_VERSION');
                
                if ($isElementor) {
                    $frontend = new \Elementor\Frontend();                
                    $frontend->init();
                }
                
                foreach ($items as $id => $postId) {
                    if ($isElementor && get_post_meta($postId, '_elementor_edit_mode', true) === 'builder') {
                        $output = $frontend->get_builder_content_for_display($postId, true);
                    } else {
                        $tempPost = get_post($postId);
                        $output = $tempPost->post_content;
                        $output = apply_filters('the_content', $output);
                    
                        $output = str_replace(']]>', ']]&gt;', $output);
                    }
                    
                    $html[] = $this->buildTag('post', $postId, $output);
                }
            }
        }
        
        if (count($html) === 0) {
            return '';
        }
        
        return '<fishpig:payload>' . implode("\n", $html) . '</fishpig:payload>';
    }
    
    /**
     * @param  string $type
     * @param  string $id
     * @param  string $content
     * @return string
     */
    private function buildTag($type, $id, $content): string
    {
        return "<fishpig:$type:$id>" . trim($content) . "</fishpig:$type:$id>";
    }
    
    /**
     * @param  array $config
     * @return array
     */    
    public function convertElementorEditToLocalUrl(array $config): array
    {
        $previewUrl = get_site_url() . '/index.php/';
        $previewUrl .= ltrim(
            str_replace(
                get_home_url(),
                '',
                $config['initial_document']['urls']['preview']
            ),
            '/'
        );

        $previewUrl = str_replace('index.php/?', 'index.php?', $previewUrl);
        $config['initial_document']['urls']['preview'] = $previewUrl;
       
        return $config;
    }
    
    /**
     *
     */
    private function fixPathInfo(): void
    {
        if (defined('FISHPIG_PATH_INFO_FIX') && FISHPIG_PATH_INFO_FIX === false) {
            return;
        }

        if (isset($_SERVER['PATH_INFO']) || empty($_SERVER['REQUEST_URI'])) {
            return;
        }

        if (($pos = strpos($_SERVER['REQUEST_URI'], '.php')) !== false) {
            $pathInfo = substr($_SERVER['REQUEST_URI'], $pos+4);;
            
            if (($pos = strpos($pathInfo, '?')) !== false) {
                $pathInfo = substr($pathInfo, 0, $pos);
            }

            $_SERVER['ORIG_PATH_INFO'] = $_SERVER['PATH_INFO'] = $pathInfo;
            $_SERVER['PATH_TRANSLATED'] = ABSPATH . ltrim($pathInfo, '/');
            $_SERVER['PHP_SELF'] = $_SERVER['REQUEST_URI'];
        }
    }
}
