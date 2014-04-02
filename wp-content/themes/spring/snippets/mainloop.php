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
        ad();
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
    <div class="col-sm-12">
      <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <a href="<?php the_permalink(); ?>" ><?php the_post_thumbnail('medium'); ?></a>
        <a href="<?php the_permalink(); ?>" ><h1><?php the_title(); ?> 11</h1></a>
        <p><span class="article-cat"><?php the_category(', '); ?></span>&nbsp;&nbsp;<?php echo get_the_excerpt(); ?>
          <a href="<?php the_permalink(); ?>" ><span class="read-more">Läs mer <i class="fa fa-angle-double-right"></i></span></a>
        </p>
        <?php include('pubinfo.php'); ?>
      </article>
    </div>  
  </div>  
  <?php
}

function small() {
  ?>
  <div class="row">
    <div class="col-sm-12">
      <article id="post-<?php the_ID(); ?>" <?php post_class('article-small'); ?>>
        <a href="<?php the_permalink(); ?>" ><?php the_post_thumbnail('thumbnail'); ?></a>
        <a href="<?php the_permalink(); ?>" ><h1><?php the_title(); ?> 222</h1></a>
        <p><span class="article-cat"><?php the_category(', '); ?></span>&nbsp;&nbsp;<?php echo get_the_excerpt(); ?>
          <a href="<?php the_permalink(); ?>" ><span class="read-more">Läs mer <i class="fa fa-angle-double-right"></i></span></a>
        </p>
        <?php include('pubinfo.php'); ?>
      </article>
    </div>  
  </div>  
  <?php
}

function ad() {
  ?>
  <div class="row">
    <div class="col-sm-12">
        <img alt="" src="http://spring.dev/wp-content/themes/spring/tmp/annons1.png">  
    </div>  
  </div>  
  <?php
}