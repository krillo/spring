<?php
get_header();
if (is_single()) {
  $hidePagnination = true;
}
$categories = get_the_category();
$bloggParentCatIds = array(132, 320);  //hardcoded categories -uggly hack this one! 
if (is_single() && check_category_family($categories, $bloggParentCatIds)) {
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
    <?php if (have_posts()) : while (have_posts()) : the_post();
        ?>
        <div class="row">
          <div class="col-md-12">
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
              <h1><?php the_title(); ?></h1>
              <span class="article-cat"><?php
                $showCat = true;
                include('snippets/pubinfo.php');
                ?></span>
              <?php //the_post_thumbnail('medium'); ?>
              <?php the_content(); ?>
              <?php include 'snippets/byline.php';?>
              <?php comments_template(); ?>
            </article>
          </div>  
        </div>  
        <?php
      endwhile;
    endif;
    ?>
    <?php if (!$blogg): ?>
      <?php include 'snippets/mainloop.php'; ?>    
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