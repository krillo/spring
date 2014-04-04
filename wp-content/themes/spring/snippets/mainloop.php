<?php
/**
 * This is the main loop
 * 
 */
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
        ad(); //notice the ad function calls big(); after showing ad, not to spoil the loop
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
    $categorys = get_the_category(); 
    $category = $categorys[0]->cat_name;
    if(count($categorys) > 1){
      $category .= ', '.$categorys[1]->cat_name;
    }
  ?>
  <div class="row">
    <div class="col-md-12" id="article-feed">
      <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <a href="<?php the_permalink(); ?>" ><?php the_post_thumbnail('medium'); ?></a>
        <a href="<?php the_permalink(); ?>" ><h1 class="h1-big"><?php the_title(); ?></h1></a>
        <?php include('pubinfo.php'); ?>
        <p><span class="article-cat"><?php echo $category; ?></span>&nbsp;&nbsp;<?php echo get_the_excerpt(); ?>
          <a href="<?php the_permalink(); ?>" ><span class="read-more">Läs mer <i class="fa fa-angle-double-right"></i></span></a>
        </p>
      </article>
    </div>  
  </div>  
  <?php
}

function small() {
  $category = get_the_category(); 
  $first_category = $category[0]->cat_name;
  ?>
  <div class="row">
    <div class="col-md-12">
      <article id="post-<?php the_ID(); ?>" <?php post_class('article-small'); ?>>
        <a href="<?php the_permalink(); ?>" ><?php the_post_thumbnail('thumbnail'); ?></a>
        <a href="<?php the_permalink(); ?>" ><h1><?php the_title(); ?></h1></a>
        <?php include('pubinfo.php'); ?>
        <p><span class="article-cat"><?php echo $first_category; ?></span>&nbsp;&nbsp;<?php echo mb_substr(get_the_excerpt(), 0, 100) . '...'; ?>
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
  small();
}