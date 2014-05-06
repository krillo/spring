<div class="sidebar-header"><i class="fa fa-caret-right"></i> Artiklar</div>

<?php
  $category_name = 'artiklar';
  $nbr = 5;
  $nbrTitle = 40;
  $nbrExerpt = 42;
  global $post;
  //Display posts that have this category and any children of that category
  $args = array('category_name' => $category_name, 'posts_per_page' => $nbr);
  $loop = new WP_Query($args);
  if ($loop->have_posts()):
    while ($loop->have_posts()) : $loop->the_post();
      //$exerpt = get_the_excerpt();
      $exerpt = mb_substr(get_the_excerpt(), 0, $nbrExerpt) . '...';
      $title =  get_the_title();    
      if(mb_strlen($title) > $nbrTitle){
        $title =  mb_substr(get_the_title(), 0, $nbrTitle) . '...';
      }
      $permalink = get_permalink();
      $img = '';
      if (has_post_thumbnail()){
        $img = get_the_post_thumbnail(null, 'thumbnail');
      } 
      $out .= <<<OUT
  <div class="cat-puff">
    <a href="$permalink">$img</a>
    <a href="$permalink"><h3>$title</h3></a>
    <p>$title</o>
  </div>
  <div class="clearfix"></div>
OUT;
    endwhile;
  endif;
  wp_reset_query();
  echo $out;
?>
<div class="devider-space"></div>