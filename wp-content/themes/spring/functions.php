<?php

/**
 * Spring
 * Author: Kristian Erendi
 * Author URI: http://reptilo.se/
 * Date: 2014-02-07
 * @package WordPress
 * @subpackage spring
 * @since Spring 1.0
 */
include_once 'bin/Prenpuff.php';
include_once 'bin/Omslag.php';
include_once get_template_directory() . "/bin/ReptiloLitteraturtips.php";
include_once get_template_directory() . "/bin/ReptiloCarousel.php";
//include_once get_template_directory() . "/bin/ReptiloFAQ.php";

/* Henric */
add_theme_support("pay_with_a_like_style");
add_image_size('yarpp-thumbnail', 172, 125, true);
add_image_size('blogg-thumbnail', 709, 532, true);

/**
 * custom size for images
 */
if (function_exists('add_image_size')) {
  add_image_size('bokomslag', 45, 70, true);
}

function spring_widgets_init() {
  register_sidebar(array(
      'name' => __('Sidebar 1'),
      'id' => 'sidebar1',
      'before_title' => '<div class="sidebar-header"><i class="fa fa-caret-right"></i>',
      'after_title' => '</div>',
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget' => '</div>',
  ));
  register_sidebar(array(
      'name' => __('Sidebar 1.1'),
      'id' => 'sidebar11',
      'before_title' => '<div class="sidebar-header"><i class="fa fa-caret-right"></i> ',
      'after_title' => '</div>',
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget' => '</div>',
  ));
  register_sidebar(array(
      'name' => __('Sidebar 2'),
      'id' => 'sidebar2',
      'before_title' => '<div class="sidebar-header"><i class="fa fa-caret-right"></i> ',
      'after_title' => '</div>',
      'before_widget' => '<div id="%1$s" class="widget %2$s spring-widget">',
      'after_widget' => '</div>',
  ));
  register_sidebar(array(
      'name' => __('Annons-banner'),
      'id' => 'banner',
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget' => '</div>',
  ));
  register_sidebar(array(
      'name' => __('Annons flöde'),
      'id' => 'ad-loop',
      'before_widget' => '<div class="ad-tag"></div><div id="%1$s" class="widget %2$s adrotate-loop">',
      'after_widget' => '</div>',
  ));
  register_sidebar(array(
      'name' => __('Header Sponsor'),
      'id' => 'header-sponsor',
      'before_title' => '<div class="sidebar-header"><i class="fa fa-caret-right"></i> ',
      'after_title' => '</div>',
  ));
}

add_action('widgets_init', 'spring_widgets_init');

/**
 * Enqueues scripts and styles for frontend.
 */
function spring_enqueue_scripts() {
  wp_enqueue_style('style', get_stylesheet_directory_uri() . '/style.css', null, '2014-06-13');
  wp_enqueue_style('style.henke', get_stylesheet_directory_uri() . '/css/style.henke.css', array('woodojo-social-widgets', 'fbSEOwpcomments', 'fbSEOStylesheet', 'style', 'font_awesome', 'thickbox'), '2014-04-03');
  wp_enqueue_style('style.mashmenu', get_stylesheet_directory_uri() . '/css/style.mashmenu.css', array('woodojo-social-widgets', 'fbSEOwpcomments', 'fbSEOStylesheet', 'style', 'font_awesome', 'thickbox'), '2014-03-26');
  wp_enqueue_style('spring.print', get_stylesheet_directory_uri() . '/css/spring.print.css', array(), '2014-05-29', 'print');
}

add_action('wp_enqueue_scripts', 'spring_enqueue_scripts');

/**
 * Display posts from:
 * 1. post type
 * 2. nbr
 * 3. random order or latest
 *  
 * @global Post $post
 * @param string $posttype
 * @param int $nbr
 * @param boolean $random
 */
function spring_printPostsPerPosttype($posttype = 'litteraturtips', $nbr = 1, $random = false, $nbrDigits = 40) {
  global $post;
  $args = array('post_type' => $posttype, 'posts_per_page' => $nbr);
  if ($random) {
    $args['orderby'] = 'rand';
  }
  $loop = new WP_Query($args);
  if ($loop->have_posts()):
    $i = 0;
    while ($loop->have_posts()) : $loop->the_post();
      if ($i % 2 == 0) {
        $zebra_class = 'zebra';
      } else {
        $zebra_class = '';
      }
      $i++;
      $img = wp_get_attachment_image(get_field('bild'), 'bokomslag');
      $title = mb_substr(get_the_title(), 0, 32) . '..';
      $author = mb_substr(get_field('forfattare'), 0, 32) . '..';
      $text = mb_substr(get_field('text'), 0, 32);
      $text = $text == '' ? $text : $text . '..';
      $url = get_field('isbn');  //notis its is now a link!!
      $readingbox .= <<<RB
        <div class="posttype-container $zebra_class">
          <div class="posttype-img">
            $img
          </div>        
          <div class="posttype-content">
            <b>$title</b><br/>
            $author<br/>
            $text<br/>
            <a href="$url" target="_blank" class="">Läs mer om boken</a>
          </div>
        </div>              
RB;
    endwhile;
  endif;
  wp_reset_query();
  echo $readingbox;
}

/**
 * Display posts from a category.
 * Bootstrap 3 style
 * 
 * @global type $post
 * @param type $category  - the slug
 * @param type $nbr - nbr of posts to show
 */
function spring_printPostsPerCat($category = 'aktuellt', $nbr = 1, $offset = 0, $nbrDigits = 100, $extraclass = '', $nbrDigitsTitle = 30) {
  global $post;
  $nbr = $nbr + $offset;
  $args = array('category_name' => $category, 'posts_per_page' => $nbr);
  $loop = new WP_Query($args);
  if ($loop->have_posts()):
    $i = 0;
    while ($loop->have_posts()) : $loop->the_post();
      if ($i >= $offset) {
        $guid = get_permalink();
        if ($extraclass == 'cat-minimum') {  //small version
          if (strlen(get_the_title()) > $nbrDigitsTitle) {
            $title = mb_substr(get_the_title(), 0, $nbrDigitsTitle) . '...';
          } else {
            $title = get_the_title();
          }
          $content = mb_substr(get_the_excerpt(), 0, $nbrDigits) . ' &nbsp;' . '<a href="' . $guid . '" target="" class="">Läs mer...</a>';
        } else {
          $title = get_the_title();
          $content = mb_substr(get_the_excerpt(), 0, $nbrDigits) . '...' . '<br/><a href="' . $guid . '" target="" class="btn btn-default btn-xs">Läs mer</a>';
        }
        $modified_date = get_the_modified_date();
        $author = get_the_author();
        $the_tags = '';
        $posttags = get_the_tags();
        if ($posttags) {
          foreach ($posttags as $tag) {
            $the_tags .= $tag->name . ' ';
          }
        }
        $img = '';
        if (has_post_thumbnail()) {
          $img = get_the_post_thumbnail(null, 'profile-thumb');
        }
        $readingbox .= <<<RB
<div class="cat-container $extraclass">
  <section>
    <h2><a href="$guid">$title</a></h2>
    <div class="pub-info"><i class="fa fa-calendar-o"></i><time pubdate="pubdate">$modified_date</time> <i class="fa fa-tags"></i>$the_tags</div>
    <div class="pub-info-small"><i class="fa fa-calendar-o"></i><time pubdate="pubdate">$modified_date</time></div>
    <div>
      $img
      <div class="cat-content">
        $content
      </div>
    </div>
  </section>
</div>
RB;
      }
      $i++;
    endwhile;
  endif;
  wp_reset_query();
  echo $readingbox;
}

function reptiloGetCategorys() {
  /*
    //use this instead to get a linked list
    $catlist = the_category(',');
    //echo $catlist;
    $catarray = explode(',',$catlist);
    print_r($catarray);
   */
  $categorys = get_the_category();
  //print_r($categorys); 
  $category = $categorys[0]->cat_name;
  if (count($categorys) > 1) {
    $category .= ', ' . $categorys[1]->cat_name;
  }
  return $category;
}

add_action('admin_init', 'my_remove_menu_pages');

function my_remove_menu_pages() {
  // If the user does not have access to publish posts
  if (!current_user_can('delete_plugins')) {
    // Remove the "SEO" menu
    remove_menu_page('tools.php');
    remove_menu_page('admin.php?page=wpcf7');
    remove_menu_page('edit.php?post_type=prenpuff');
    remove_menu_page('edit.php?post_type=omslag');
    remove_menu_page('edit.php?post_type=litteraturtips');
    remove_menu_page('admin.php?page=wpseo_bulk-title-editor');
  }
}

//admin.php?page=wpseo_dashboard


function gpp_jpeg_quality_callback($arg) {
  return (int) 100;
}

add_filter('jpeg_quality', 'gpp_jpeg_quality_callback');


/**
 * Check if $blogg_cats is one of the $categories, 
 * Then it is a blogg listing
 *   
 * @param type $categories
 * @param type $expected_ids
 * @return boolean
 */
function isBloggListing($categories, $blogg_cats ){
  foreach( $categories as $i ){
    if( in_array( intval( $i->category_parent ), $blogg_cats ) ){
      return true;
    }
  }
}

