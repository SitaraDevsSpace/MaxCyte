<?php
// AJAX Search Template
if (!defined('ABSPATH')) exit;

$search_term = isset($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';
$paged       = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
$posts_per_page = 10;

$priority_products = ['GTx™', 'ATx™', 'STx™', 'VLx™'];
$trigger_keywords  = ['electroporator',"electroporators", 'product','pro', 'prod', 'electro', 'products', 'electr', 'produc'];
$post_types        = ['page', 'resource', 'webinar', 'events', 'news'];

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
$all_results  = [];

if ($search_query->have_posts()) {
    while ($search_query->have_posts()) {
        $search_query->the_post();

        $title = get_the_title();
        $image_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
        if (!$image_url && function_exists('get_field')) {
            $acf_image = get_field('custom_image_field');
            if (!empty($acf_image['url'])) $image_url = $acf_image['url'];
        }

        $all_results[] = [
            'title'     => $title,
            'permalink' => get_permalink(),
            'excerpt'   => wp_trim_words(get_the_excerpt(), 30, '...'),
            'image'     => $image_url,
        ];
    }
    wp_reset_postdata();
}

// Trigger search logic
$is_trigger_search = false;
foreach ($trigger_keywords as $keyword) {
    if (strpos(strtolower($search_term), strtolower($keyword)) !== false) {
        $is_trigger_search = true;
        break;
    }
}

if ($is_trigger_search) {
    foreach ($priority_products as $prod) {
        $already_in_results = false;
        foreach ($all_results as $res) {
            if (strcasecmp($res['title'], $prod) === 0) { $already_in_results = true; break; }
        }
        if (!$already_in_results) {
            $prod_post = get_page_by_title($prod, OBJECT, $post_types);
            if ($prod_post) {
                $image_url = get_the_post_thumbnail_url($prod_post->ID, 'medium');
                if (!$image_url && function_exists('get_field')) {
                    $acf_image = get_field('custom_image_field', $prod_post->ID);
                    if (!empty($acf_image['url'])) $image_url = $acf_image['url'];
                }
                $all_results[] = [
                    'title'     => get_the_title($prod_post->ID),
                    'permalink' => get_permalink($prod_post->ID),
                    'excerpt'   => wp_trim_words(get_the_excerpt($prod_post->ID), 30, '...'),
                    'image'     => $image_url,
                ];
            }
        }
    }
}

// Sorting
$search_lower = strtolower($search_term);
usort($all_results, function($a, $b) use ($priority_products, $is_trigger_search, $search_lower) {
    $a_priority = in_array($a['title'], $priority_products) ? 0 : 1;
    $b_priority = in_array($b['title'], $priority_products) ? 0 : 1;

    if ($is_trigger_search) return $a_priority <=> $b_priority;

    if ($a_priority === 0 && $b_priority === 0) {
        $a_match = stripos(strtolower($a['title']), $search_lower) !== false ? 0 : 1;
        $b_match = stripos(strtolower($b['title']), $search_lower) !== false ? 0 : 1;
        return $a_match <=> $b_match;
    }

    return $a_priority <=> $b_priority;
});

// Output HTML
if (!empty($all_results)) : ?>
    <?php foreach ($all_results as $result) : ?>
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
    <?php endforeach; ?>

    <!-- AJAX Pagination -->
    <div class="custom-pagination" data-search-term="<?php echo esc_attr($search_term); ?>" data-max-pages="<?php echo $search_query->max_num_pages; ?>">
        <?php for ($i = 1; $i <= $search_query->max_num_pages; $i++) : ?>
            <a href="#" class="ajax-page <?php echo $i === $paged ? 'current' : ''; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>

<?php else : ?>
    <div class="no-results">
        <p>No results found for "<?php echo esc_html($search_term); ?>".</p>
        <p>Try a different search term or <a href="<?php echo esc_url(home_url('/')); ?>">return to the homepage</a>.</p>
    </div>
<?php endif; ?>
