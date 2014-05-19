<?php get_header(); ?>
<div class="row clearfix">
  <div class="col-md-6 column">
    <?php if (have_posts()) : while (have_posts()) : the_post();
        ?>
        <div class="row">
          <div class="col-md-12">
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
              <h1><?php the_title(); ?></h1>
              <span class="article-cat"><?php $showCat = true; include('snippets/pubinfo.php'); ?></span>
              <?php //the_post_thumbnail('medium'); ?>
              <?php the_content(); ?>
              <?php comments_template(); ?>
            </article>
          </div>  
        </div>  
        <?php
      endwhile;
    endif;
    ?>

    <?php include 'snippets/mainloop.php'; ?>    

  </div>
  <div class="col-md-6 column">
    <div class="row clearfix">
      <?php include('sidebar1.php'); ?>
      <?php include('sidebar2.php'); ?>
    </div>
  </div>
</div>
<?php get_footer(); ?>