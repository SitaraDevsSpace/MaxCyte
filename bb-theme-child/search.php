<?php
/* Template Name: SearchWP Global Search */
get_header();

// Increase memory limit temporarily if needed (as a fallback, but not primary solution)
ini_set('memory_limit', '256M');

// Ensure database connection is clean
global $wpdb;
$wpdb->flush();

?>

<div class="fl-page-content">
  <div class="fl-row-content fl-row-fixed-width" style="padding-left: 20px; padding-right: 20px;">
    <div class="fl-content">
      <div class="fl-archive-header" role="banner">
        <h1 class="fl-archive-title">Search Results for: <?php echo esc_html(get_search_query()); ?></h1>
      </div>

      <div class="search-results-wrapper">
        <div id="loading-spinner" style="display: none; text-align: center; padding: 20px;">
          <div class="spinner"></div>
        </div>
        <div id="search-results-container">
          <!-- Initial results will be populated by PHP -->
          <?php
          $search_term = strtolower(html_entity_decode(urldecode(sanitize_text_field(get_search_query()))));
          $search_term = str_replace(['–', '—'], '-', $search_term);

          // Priority products and trigger keywords
          $priority_products = ['GTx™', 'ATx™', 'STx™', 'VLx™'];
          $trigger_keywords = ['electroporator', 'electroporators', 'product', 'pro', 'prod', 'produ' , 'produc' , 'electro', 'products', 'electr', 'elect', 'elec', 'produc'];

          $post_types = ['page', 'resource', 'webinar', 'events', 'news'];
          $posts_per_page = 10;
          $paged = max(1, isset($_GET['page']) ? absint($_GET['page']) : 1);

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

          // Combine results with priority products first
          $final_results = array_merge($priority_results, $all_results);

          // Display initial results
          if (!empty($final_results)) {
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
          } else {
            ?>
            <div class="no-results">
              <p>No results found for "<?php echo esc_html($search_term); ?>".</p>
              <p>Try a different search term or <a href="<?php echo esc_url(home_url('/')); ?>">return to the homepage</a>.</p>
            </div>
            <?php
          }
          ?>
        </div>

        <?php if (!empty($final_results)) : ?>
          <div class="custom-pagination" id="pagination-controls">
            <!-- Pagination will be populated by JavaScript -->
          </div>
        <?php endif; ?>
      </div>
    </div>

    <?php if (is_active_sidebar('primary-sidebar')) : ?>
      <div class="fl-sidebar col-md-4">
        <?php get_sidebar(); ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<style>
  header { background: #003b87; position: unset !important; }
  .fl-archive-header .fl-archive-title { font-size: 28px; margin-bottom: 20px; }
  .search-result-item { display: flex; gap: 15px; margin-bottom: 25px; padding: 15px; border-bottom: 1px solid #ddd; background: #CEE6F3; border-radius: 12px; }
  .search-image img { width: 120px; height: auto; border-radius: 8px; object-fit: cover; }
  .search-content { flex: 1; }
  .search-title { font-family: "Barlow", Arial, sans-serif !important; font-style: normal; font-weight: 600; font-size: 24px; line-height: 32px; letter-spacing: -0.01em; transition: all 0.3s ease; color: #021D66 !important; margin-bottom: 8px; display: block; }
  .search-title:hover { text-decoration: underline; }
  .search-link { font-size: 14px; color: #11778E; margin-bottom: 4px; display: inline-block; }
  .search-description { font-family: "Roboto", Arial, sans-serif !important; font-weight: 400 !important; color: #373C44 !important; font-size: 18px !important; line-height: 24px !important; letter-spacing: -0.01em; }
  .search-domain { font-size: 15px; color: #777; margin-bottom: 4px; }
  .custom-pagination { margin-top: 30px; text-align: center; }
  .custom-pagination a, .custom-pagination span { display: inline-block; padding: 8px 14px; margin: 0 3px; border-radius: 5px; background-color: #f0f0f0; color: #333; font-size: 14px; text-decoration: none; transition: background 0.3s; cursor: pointer; }
  .custom-pagination a:hover { background-color: #003b87; color: #fff; }
  .custom-pagination .current { background-color: #003b87; color: #fff; font-weight: bold; }
  .custom-pagination .dots { padding: 8px 14px; margin: 0 3px; }
  .spinner { border: 4px solid #f3f3f3; border-top: 4px solid #003b87; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto; }
  @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
  @media (max-width: 767px) { .search-result-item { flex-direction: column; } .search-description { font-size: 16px !important; line-height: 21px !important; } }
</style>


<script>
  document.addEventListener('DOMContentLoaded', function() {
    const postsPerPage = <?php echo $posts_per_page; ?>;
    const totalPosts = <?php echo $search_query->found_posts; ?>;
    let currentPage = getPageFromURL();
    const totalPages = Math.ceil(totalPosts / postsPerPage);
    const searchTerm = '<?php echo esc_js($search_term); ?>';

    function getPageFromURL() {
      const urlParams = new URLSearchParams(window.location.search);
      return parseInt(urlParams.get('page')) || 1;
    }

    function updateURL(page) {
      const url = new URL(window.location.href);
      url.searchParams.set('page', page);
      window.history.pushState({ page: page }, '', url);
    }

    function createPageLink(page, isCurrent) {
      const link = document.createElement('span');
      link.textContent = page;
      if (isCurrent) {
        link.className = 'current';
      }
      link.onclick = () => loadPage(page);
      return link;
    }

    function createDots() {
      const dots = document.createElement('span');
      dots.textContent = '...';
      dots.className = 'dots';
      return dots;
    }

    function renderPagination() {
      const pagination = document.getElementById('pagination-controls');
      pagination.innerHTML = '';

      // Previous button
      const prev = document.createElement('a');
      prev.textContent = '« Prev';
      prev.style.cursor = currentPage === 1 ? 'not-allowed' : 'pointer';
      prev.style.backgroundColor = currentPage === 1 ? '#ccc' : '#f0f0f0';
      if (currentPage > 1) {
        prev.onclick = () => loadPage(currentPage - 1);
      }
      pagination.appendChild(prev);

      if (totalPages <= 1) return;

      const leftWindowSize = 4;
      const rightWindowSize = 2;

      let leftStart = Math.max(1, currentPage - (leftWindowSize - 1));
      let leftEnd = leftStart + leftWindowSize - 1;

      let rightStart = totalPages - rightWindowSize + 1;
      let rightEnd = totalPages;

      if (leftEnd >= rightStart - 1) {
        leftEnd = totalPages;
        rightStart = totalPages + 1;
      }

      for (let i = leftStart; i <= Math.min(leftEnd, totalPages); i++) {
        pagination.appendChild(createPageLink(i, i === currentPage));
      }

      if (leftEnd < rightStart - 1) {
        pagination.appendChild(createDots());
      }

      if (rightStart <= totalPages) {
        for (let i = rightStart; i <= rightEnd; i++) {
          pagination.appendChild(createPageLink(i, i === currentPage));
        }
      }

      // Next button
      const next = document.createElement('a');
      next.textContent = 'Next »';
      next.style.cursor = currentPage === totalPages ? 'not-allowed' : 'pointer';
      next.style.backgroundColor = currentPage === totalPages ? '#ccc' : '#f0f0f0';
      if (currentPage < totalPages) {
        next.onclick = () => loadPage(currentPage + 1);
      }
      pagination.appendChild(next);
    }

    function loadPage(page) {
      

      const spinner = document.getElementById('loading-spinner');
      const container = document.getElementById('search-results-container');
      const pageTop = document.querySelector('.fl-page-content');

      if (pageTop) {
        pageTop.scrollIntoView({ behavior: 'smooth', block: 'start' });
      } else {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }

      spinner.style.display = 'block';
      spinner.style.opacity = '0';
      setTimeout(() => {
        spinner.style.transition = 'opacity 0.3s';
        spinner.style.opacity = '1';
      }, 10);
      container.style.opacity = '0.5';
      container.style.transition = 'opacity 0.3s';

      fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
  action: 'custom_search_pagination',
  page: page,
  search_term: searchTerm
})
      })
      .then(response => response.json())
      .then(data => {
        spinner.style.opacity = '0';
        setTimeout(() => {
          container.innerHTML = data.html;
          spinner.style.display = 'none';
          container.style.opacity = '1';
          currentPage = page;
         window.history.pushState({}, '', '?s=' + encodeURIComponent(searchTerm) + '&page=' + page);
          renderPagination();
        }, 300);
      })
      .catch(error => {
        console.error('Error:', error);
        spinner.style.opacity = '0';
        setTimeout(() => {
          spinner.style.display = 'none';
          container.style.opacity = '1';
        }, 300);
      });
    }

    if (totalPosts > 0) {
      renderPagination();
    }
  });
</script>


<?php get_footer(); ?>