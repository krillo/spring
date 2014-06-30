<?php
get_header();
$categories = get_the_category();
$bloggParentCatIds = array(132, 320);  //hardcoded categories - uggly hack this one! 
if (is_archive() && check_category_family($categories, $bloggParentCatIds)) {
  $mainWidth = 'col-md-8';
  $sidebarWidth = 'col-md-4';
  $blogg = true;
  $curauthID = get_the_author_meta('ID');
} else {
  $mainWidth = 'col-md-6';
  $sidebarWidth = 'col-md-6';
  $blogg = false;
}
?>
<div class="row clearfix">
  <div class="<?php echo $mainWidth; ?> column">
    <?php if (have_posts()) : ?>
      <!--h1><?php //single_cat_title(); ?></h1-->
      <?php while (have_posts()) : the_post(); ?>
        <div class="row">
          <article id="post-<?php the_ID(); ?>" class="col-md-12">
            <header>
              <h2><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h2>
              <span class="article-cat"><?php $showCat = true;
        include('snippets/pubinfo.php'); ?></span>
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
  <div class="<?php echo $sidebarWidth; ?> column">
    <div class="row clearfix">
      <?php if (!$blogg): ?>
        <?php include('sidebar1.php'); ?>
        <?php include('sidebar2.php'); ?>
      <?php else: ?>
        <?php include('sidebar3.php'); ?>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php get_footer(); ?>