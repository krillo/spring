<?php

/**
 * This is the Omslag.
 * To use it you just have to include this php-file in the functions.php
 */
$omslag = new Omslag;

/**
 * 1. It adds a custom posttype
 * 2. Add featured image to them
 * 3. Use the Call it like this
 * <?php global $omslag; if (method_exists($omslag,'printOmslag')) $omslag->printOmslag(); ?>
 * 
 * Author: Kristian Erendi 
 * URI: http://reptilo.se 
 * Date: 2014-03-25 
 */
class Omslag {

  function __construct() {
    add_action('init', array($this, 'create_post_type'));
  }

  /**
   * Crate posttype
   */
  function create_post_type() {
    $labels = array(
        'name' => 'Omslag',
        'singular_name' => 'Omslag',
        'add_new' => 'Lägg till ny Omslag',
        'add_new_item' => 'Lägg till ny Omslag',
        'edit_item' => 'Redigera Omslag',
        'new_item' => 'Ny Omslag',
        'all_items' => 'Alla Omslagar',
        'view_item' => 'Visa Omslag',
        'search_items' => 'Sök Omslag',
        'not_found' => 'Inga Omslagar hittades',
        'not_found_in_trash' => 'Inga Omslag hittades i soptunnan',
        'parent_item_colon' => '',
        'menu_name' => 'Omslag'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'Omslag'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'thumbnail') //, 'comments' )
    );
    register_post_type('Omslag', $args);
  }

  function printOmslag($posttype = 'omslag', $nbr = 1) {
    global $post;
    $args = array('post_type' => $posttype, 'posts_per_page' => $nbr);
    $loop = new WP_Query($args);
    if ($loop->have_posts()):
      while ($loop->have_posts()) : $loop->the_post();
        $id = $post->ID;
        //$img = get_the_post_thumbnail($id, array(90,115));
        //$img = get_the_post_thumbnail($id, array(90, 'auto'));
        $img = get_the_post_thumbnail($id);
        $number = get_field('nummer');
        $year = get_field('year');
        $out .= <<<OUT
                $img               
                <h2>Aktuellt nummer</h2>
                <span class="omslag-nummer" style="">Spring / Nr $number / $year </span>
                <ul class="omslag-list">    
                  <li><i class="fa fa-caret-right"></i><a href="/innehall/">Innehållet</a></li>
                  <li><i class="fa fa-caret-right"></i><a href="/prenumerera/">Prenumerera</a></li>
                </ul>    
OUT;


      endwhile;
    endif;
    wp_reset_query();
    echo $out;
  }

}