<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Custom post type - Uppdragstagare
 */
function create_prenpuff() {
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

add_action('init', 'create_prenpuff');




/**
 * Custom post type - Uppdragstagare
 */
function create_litteraturtips() {
  $labels = array(
      'name' => 'Litteraturtips',
      'singular_name' => 'Litteraturtips',
      'add_new' => 'Lägg till nytt litteraturtips',
      'add_new_item' => 'Lägg till nytt litteraturtips',
      'edit_item' => 'Redigera litteraturtips',
      'new_item' => 'Nytt litteraturtips',
      'all_items' => 'Alla litteraturtips',
      'view_item' => 'Visa litteraturtips',
      'search_items' => 'Sök litteraturtips',
      'not_found' => 'Inga litteraturtips hittade',
      'not_found_in_trash' => 'Inga litteraturtips hittade i soptunnan',
      'parent_item_colon' => '',
      'menu_name' => 'Litteraturtips'
  );

  $args = array(
      'labels' => $labels,
      'public' => true,
      'publicly_queryable' => true,
      'show_ui' => true,
      'show_in_menu' => true,
      'query_var' => true,
      'rewrite' => array('slug' => 'litteraturtips'),
      'capability_type' => 'post',
      'has_archive' => true,
      'hierarchical' => false,
      'menu_position' => null,
      'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt') //, 'comments' )
  );
  register_post_type('litteraturtips', $args);
}

add_action('init', 'create_litteraturtips');