<?php get_header(); ?>
<div class="row clearfix">
  <div class="col-md-8 column">
    <?php if (have_posts()) : ?>
      <?php
      $curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
      ?>
      <?php
      while (have_posts()) : the_post();
        ?>
        <div class="row">
          <div class="col-md-12" id="article-feed">
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
              <a href="<?php the_permalink(); ?>" ><?php the_post_thumbnail('full'); ?></a>
              <a href="<?php the_permalink(); ?>" ><h1 class="h1-big"><?php the_title(); ?></h1></a>
              <span class="article-cat"><?php
                $showCat = false;
                include('pubinfo.php');
                ?></span>
              <p><span class="article-cat"><?php echo rep_getOneCategory(); ?></span>&nbsp;&nbsp;<?php echo get_the_excerpt(); ?>
                <a href="<?php the_permalink(); ?>" ><span class="read-more">Läs mer <i class="fa fa-angle-double-right"></i></span></a>
              </p>
            </article>
          </div>  
        </div>  
      <?php endwhile; ?>
      <?php
      if (function_exists('bootstrap3_pagination')) {
        bootstrap3_pagination();
      }
      ?>        
<?php endif; ?>
  </div>
  <div class="col-md-4 column">
    <div class="row clearfix">
      <?php include('sidebar3.php'); ?>
    </div>
  </div>
</div>
<?php get_footer(); ?>