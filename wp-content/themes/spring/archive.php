<?php get_header(); ?>
<div class="row clearfix">
  <div class="col-md-6 column">
    <?php if (have_posts()) : ?>
        <h1><?php single_cat_title(); ?></h1>
        <?php while (have_posts()) : the_post(); ?>
          <div class="row">
            <article id="post-<?php the_ID(); ?>" class="col-md-12">
              <header>
                <h2><?php the_title(); ?></h2>
                <span class="article-cat"><?php $showCat = true; include('snippets/pubinfo.php'); ?></span>
              </header>
              <div class="archive-content"><?php the_excerpt(); ?>
                <a href="<?php echo get_permalink(); ?>"> Läs mer &raquo;</a>
              </div>
            </article>
          </div>
        <?php endwhile; ?>
        <?php
        if (function_exists('bootstrap3_pagination')) {
          bootstrap3_pagination();
        }
        ?>        
    <?php endif; ?>
  </div>
  <div class="col-md-6 column">
    <div class="row clearfix">
      <?php include('sidebar1.php'); ?>
      <?php include('sidebar2.php'); ?>
    </div>
  </div>
</div>
<?php get_footer(); ?>