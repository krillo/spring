<?php
/**
 * This is the main loop
 * 
 */
$args = array(
    'post_type' => 'post',
    'posts_per_page' => 8,
    'cat' => '-5,-14',
    'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1),
);

query_posts($args);
$i = 0;
if (have_posts()):
  while (have_posts()):
    the_post();
    $i++;
    switch ($i) {
      case 1:
        big();
        break;
      case 4:
        big();
        break;
      case 5:
        ad();
        small();
        break;
      case 8:
        big();
        break;      
      default:
        small();
        break;
    }
  endwhile;
  if (function_exists('bootstrap3_pagination')) {
    bootstrap3_pagination();
  }
endif;



function big() {
  ?>
  <div class="row">
    <div class="col-md-12" id="article-feed">
      <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <a href="<?php the_permalink(); ?>" ><?php the_post_thumbnail('medium'); ?></a>
        <a href="<?php the_permalink(); ?>" ><h1 class="h1-big"><?php the_title(); ?></h1></a>
        <span class="article-cat"><?php $showCat = false; include('pubinfo.php'); ?></span>
        <p><span class="article-cat"><?php echo rep_GetOneCategory(); ?></span>&nbsp;&nbsp;<?php echo get_the_excerpt(); ?>
          <a href="<?php the_permalink(); ?>" ><span class="read-more">Läs mer <i class="fa fa-angle-double-right"></i></span></a>
        </p>
      </article>
    </div>  
  </div>  
  <?php
}

function small() {
  $excerpt = mb_substr(get_the_excerpt(), 0, 100) . '...';
  ?>
  <div class="row">
    <div class="col-md-12">
      <article id="post-<?php the_ID(); ?>" <?php post_class('article-small'); ?>>
        <a href="<?php the_permalink(); ?>" ><?php the_post_thumbnail('thumbnail'); ?></a>
        <a href="<?php the_permalink(); ?>" ><h1><?php the_title(); ?></h1></a>
        <span class="article-cat"><?php $showCat = false; include('pubinfo.php'); ?></span>
        <p><span class="article-cat"><?php echo rep_GetOneCategory(); ?></span>&nbsp;&nbsp;<?php echo $excerpt; ?>
          <a href="<?php the_permalink(); ?>" ><span class="read-more">Läs mer <i class="fa fa-angle-double-right"></i></span></a>
        </p>
      </article>
    </div>  
  </div>  
  <?php
}

function ad() {
  ?>
  <div class="row">
    <div class="col-md-12">
      <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar("ad-loop")) : endif; ?>
    </div>  
  </div>  
  <?php
}
