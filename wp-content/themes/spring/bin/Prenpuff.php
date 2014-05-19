<?php

/**
 * This is the prenpuff.
 * To use it you just have to include this php-file
 */
$spp = new Prenpuff;

/**
 * The ReptiloCarousel class is based on Bootstrap 3.0 
 * 1. It adds a custom posttype called Slideshow
 * 2. Add featured image to them  
 * 3. Call the slideshow like this
 * <?php global $spp; if (method_exists($spp,'printPrenpuff')) $spp->printPrenpuff(); ?>
 * 
 * Author: Kristian Erendi 
 * URI: http://reptilo.se 
 * Date: 2014-03-25 
 */
class Prenpuff {

  function __construct() {
    add_action('init', array($this, 'create_post_type'));
    $this->init_acf_fields();
  }

  /**
   * Crate posttype
   */
  function create_post_type() {
    $labels = array(
        'name' => 'Prenpuff',
        'singular_name' => 'Prenpuff',
        'add_new' => 'Lägg till ny prenpuff',
        'add_new_item' => 'Lägg till ny prenpuff',
        'edit_item' => 'Redigera prenpuff',
        'new_item' => 'Ny prenpuff',
        'all_items' => 'Alla prenpuffar',
        'view_item' => 'Visa prenpuff',
        'search_items' => 'Sök prenpuff',
        'not_found' => 'Inga prenpuffar hittades',
        'not_found_in_trash' => 'Inga prenpuff hittades i soptunnan',
        'parent_item_colon' => '',
        'menu_name' => 'Prenpuff'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'prenpuff'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'thumbnail') //, 'comments' )
    );
    register_post_type('prenpuff', $args);
  }

  function init_acf_fields() {
    if (function_exists("register_field_group")) {
      register_field_group(array(
          'id' => 'acf_prenpuff',
          'title' => 'prenpuff',
          'fields' => array(
              array(
                  'key' => 'field_53735c3b448d1',
                  'label' => 'Välj sida att länka till',
                  'name' => 'page_link',
                  'type' => 'page_link',
                  'post_type' => array(
                      0 => 'page',
                  ),
                  'allow_null' => 0,
                  'multiple' => 0,
              ),
          ),
          'location' => array(
              array(
                  array(
                      'param' => 'post_type',
                      'operator' => '==',
                      'value' => 'prenpuff',
                      'order_no' => 0,
                      'group_no' => 0,
                  ),
              ),
          ),
          'options' => array(
              'position' => 'normal',
              'layout' => 'default',
              'hide_on_screen' => array(
              ),
          ),
          'menu_order' => 0,
      ));
    }
  }

  function printPrenpuff($posttype = 'prenpuff', $nbr = 1) {
    global $post;
    $args = array('post_type' => $posttype, 'posts_per_page' => $nbr);
    $prenloop = new WP_Query($args);
    if ($prenloop->have_posts()):
      while ($prenloop->have_posts()) : $prenloop->the_post();
        $img = get_the_post_thumbnail();
        $page_link = get_field('page_link');
        $out .= <<<OUT
        <div class="prenpuff">
              <a href="$page_link" alt="">$img</a>
        </div>              
OUT;
      endwhile;
    endif;
    wp_reset_query();
    $out .= '<div class="devider-space"></div>';
    echo $out;
  }

}