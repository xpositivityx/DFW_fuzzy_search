<?php 
/**
 * Plugin Name: Fuzzy Search Bar
 * Plugin URI: http://localhost:8000
 * Description: Autocomplete search bar for wordpress
 * Version: 1.0
 * Author: David Williams
 * Author URI: http://no-URL.com
 * License: A short license name. Example: GPL2
 */



add_action("wp_ajax_nopriv_search", "search");
add_action("wp_ajax_search", "search");

function search(){
    global $wpdb;
    $crit = $_GET['crit'];
    if (isset($crit) && $crit != ''){
        $results = $wpdb->get_results(
                "SELECT post_title, post_name FROM $wpdb->posts
                INNER JOIN $wpdb->term_relationships
                ON $wpdb->term_relationships.object_id = $wpdb->posts.id
                INNER JOIN $wpdb->term_taxonomy
                ON $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
                WHERE $wpdb->term_taxonomy.parent in (94,64,98,44,6,96)
                AND post_title LIKE '%$crit%'
                LIMIT 10
                "
        );
        if (count($results) > 0 ){
            $data = array();
            $count = 0;
            foreach($results as $result){
                $data["post_title" . $count] = $result->post_title . ',' . $result->post_name;
                ++$count;
            }
        }
        else{
            $data = array("value" => 'No Results');
        }
    }
    else{
        $data = array("value" => 'No Results');
    }

    header('Content-type:json');
    echo json_encode($data);
    exit;
}

function enqueue_search_scripts(){
    wp_enqueue_style('jquery-ui-style', plugins_url("/css/jquery-ui.min.css", __FILE__));
    wp_enqueue_script('jquery-ui', plugin_dir_url(__FILE__) . "js/jquery-ui.min.js");
    wp_enqueue_script('jquery', plugin_dir_url(__FILE__) . "js/jquery.js");
    wp_enqueue_script('fuzzy_search_bar', plugin_dir_url(__FILE__) . "js/search-bar.js");
    wp_localize_script('fuzzy_search_bar', 'search', array(
        'url' => admin_url() . '/admin-ajax.php?action=search'));
}

add_action('wp_enqueue_scripts', 'enqueue_search_scripts');

function generate_search_bar(){
	ob_start();
	?> 
	<li>
    <form class="navbar-search pull-right">
      <input type="text" class="form-control input-lg" id="search_bar" placeholder="Search">
    </form>
  </li>
	<?php
	$result = ob_get_contents();
	ob_end_clean();

	return $result;
}







?>