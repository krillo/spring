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
        $img = get_the_post_thumbnail($id, array(90, 'auto'));
        //$img = get_the_post_thumbnail($id);
        $number = get_field('nummer');
        $year = get_field('year');
/*        
        $out .= <<<OUT
        <div class="omslag">
          $img
        </div>              
        <div class="omslag-text">
          <h2>Aktuellt nummer</h2>
          <div class="omslag-nummer">Spring / Nr $number / $year </div>
            <ul>    
              <li><i class="fa fa-caret-right"></i><a href="#">Se hela innehållet</a></li>
              <li><i class="fa fa-caret-right"></i><a href="#">Prenumerera på Spring</a></li>
              <li><i class="fa fa-caret-right"></i><a href="#">Läs tidningen digitalt som prenumerant</a></li>
            </ul>    
        </div>              
OUT;
*/        
        
        
        $out .= <<<OUT
                $img               
 <!--img  src="http://spring.dev/wp-content/uploads/2014/03/Spring_1_20141-118x150.jpeg" class="attachment-90x115 wp-post-image" alt="Spring_1_2014" style="max-width:115px;float:left;"-->
                <h2>Aktuellt nummer</h2>
                <span class="omslag-nummer" style="">Spring / Nr $number / $year </span>
                <ul>    
                  <li><i class="fa fa-caret-right"></i><a href="#">Se hela innehållet</a></li>
                  <li><i class="fa fa-caret-right"></i><a href="#">Prenumerera på Spring</a></li>
                  <li><i class="fa fa-caret-right"></i><a href="#">Läs tidningen digitalt som prenumerant</a></li>
                </ul>    
OUT;
 
                
      endwhile;
    endif;
    wp_reset_query();
    echo $out;
  }

}