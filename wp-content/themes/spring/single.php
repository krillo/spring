<?php get_header(); ?>
<div class="row clearfix">
  <div class="col-sm-6 column">
    <?php if (have_posts()) : while (have_posts()) : the_post();
        ?>
        <div class="row">
          <div class="col-sm-12">
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
              <?php the_post_thumbnail(); ?>
              <h1><?php the_title(); ?></h1>
              <p><span class="article-cat"><?php the_category(', '); ?><?php include('snippets/pubinfo.php'); ?></span> 
            </article>
          </div>  
        </div>  
        <?php
      endwhile;
    endif;
    ?>
  </div>
  <div class="col-sm-6 column">
    <div class="row clearfix">
      <?php include('sidebar1_tmp.php'); ?>
      <?php include('sidebar2_tmp.php'); ?>
    </div>
  </div>
</div>
<?php get_footer(); ?>