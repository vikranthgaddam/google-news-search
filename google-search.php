<?php
/*
Plugin Name: Google News Search
Description: Display a search form to search Google News XML/RSS Feed with pagination
Version: 1.1
Author: Your Name
*/

// Register the shortcode
add_shortcode('google_news_search', 'google_news_search_shortcode');

// Shortcode function
function google_news_search_shortcode() {
    ob_start();
    ?>
    <div class="google-news-search">
        <form id="google-news-search-form">
            <input type="text" id="search-term" name="search-term" placeholder="Enter search term" required>
            <button type="submit">Search</button>
        </form>
        <div id="search-results"></div>
        <div id="pagination"></div>
    </div>
    <?php
    return ob_get_clean();
}

// Register AJAX action
add_action('wp_ajax_google_news_search', 'google_news_search_ajax');
add_action('wp_ajax_nopriv_google_news_search', 'google_news_search_ajax');

// AJAX callback function
function google_news_search_ajax() {
    check_ajax_referer('google_news_search_nonce', 'nonce');
    
    $search_term = sanitize_text_field($_POST['search_term']);
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $per_page = 10; // Number of results per page

    $rss_url = 'https://news.google.com/news?q=' . urlencode($search_term) . '&output=rss&num=100'; // Fetch 100 items for pagination
    
    $response = wp_remote_get($rss_url);
    
    if (is_wp_error($response)) {
        echo json_encode(['error' => 'Error fetching results. Please try again.']);
    } else {
        $rss = simplexml_load_string(wp_remote_retrieve_body($response));
        
        if ($rss && isset($rss->channel->item)) {
            $items = $rss->channel->item;
            $total_items = count($items);
            $total_pages = ceil($total_items / $per_page);
            $start = ($page - 1) * $per_page;
            $end = min($start + $per_page, $total_items);

            ob_start();
            echo '<ul class="google-news-results">';
            for ($i = $start; $i < $end; $i++) {
                $item = $items[$i];
                echo '<li><a href="' . esc_url($item->link) . '" target="_blank">' . esc_html($item->title) . '</a></li>';
            }
            echo '</ul>';

            $pagination = generate_pagination($page, $total_pages);

            $output = [
                'results' => ob_get_clean(),
                'pagination' => $pagination,
                'total_pages' => $total_pages
            ];

            echo json_encode($output);
        } else {
            echo json_encode(['error' => 'No results found.']);
        }
    }
    
    wp_die();
}

function generate_pagination($current_page, $total_pages) {
    $pagination = '<div class="pagination">Page:';
    
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $current_page) {
            $pagination .= '<span class="current-page">' . $i . '</span>';
        } else {
            $pagination .= '<a href="#" class="page-number" data-page="' . $i . '">' . $i . '</a>';
        }
    }
    
    $pagination .= '</div>';
    
    return $pagination;
}

// Enqueue scripts and styles
add_action('wp_enqueue_scripts', 'google_news_search_enqueue_scripts');

function google_news_search_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_style('google-news-search-style', plugins_url('style.css', __FILE__));
    
    wp_enqueue_script('google-news-search-script', plugins_url('google-news-search.js', __FILE__), array('jquery'), '1.1', true);
    wp_localize_script('google-news-search-script', 'googleNewsSearch', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('google_news_search_nonce')
    ));
}