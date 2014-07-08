<?php
get_header();
$sidebarType = 'standard';
if ($_SERVER[REQUEST_URI] == "/kategori/bloggar/") {
  $sidebarType = 'general_blogg_archive';
}
$categories = get_the_category();
$bloggParentCatIds = array(132, 320);  //hardcoded categories - uggly hack this one! 
if (is_archive() && check_category_family($categories, $bloggParentCatIds)) {
  $sidebarType = 'blogg_archive';
}

switch ($sidebarType) {
  case 'standard':
    $mainWidth = 'col-md-6';
    $sidebarWidth = 'col-md-6';
    ob_start();
    include('sidebar1.php');
    include('sidebar2.php');
    $sidebars = ob_get_clean();
    break;
  case 'general_blogg_archive':
    $mainWidth = 'col-md-8';
    $sidebarWidth = 'col-md-4';
    ob_start();
    include ('snippets/blogpuffs.php');
    if (!function_exists('dynamic_sidebar') || !dynamic_sidebar("sidebar1")) : endif;
    $sidebars = ob_get_clean();
    break;
  case 'blogg_archive':
    $mainWidth = 'col-md-8';
    $sidebarWidth = 'col-md-4';
    $blogg = true;
    $curauthID = get_the_author_meta('ID');
    ob_start();
    include('sidebar3.php');
    $sidebars = ob_get_clean();
    break;
  default:
    break;
}
?>
<div class="row clearfix">
  <div class="<?php echo $mainWidth; ?> column">
    <?php if (have_posts()) : ?>
                <!--h1><?php //single_cat_title();     ?></h1-->
      <?php while (have_posts()) : the_post(); ?>
        <div class="row">
          <article id="post-<?php the_ID(); ?>" class="col-md-12">
            <header>
              <a href="<?php the_permalink(); ?>" ><?php the_post_thumbnail('full'); ?></a>
              <h2><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h2>
              <span class="article-cat"><?php
                $showCat = true;
                include('snippets/pubinfo.php');
                ?></span>
            </header>
            <div class="archive-content"><?php the_excerpt(); ?>
              <a href="<?php echo get_permalink(); ?>"><span class="read-more">Läs mer <i class="fa fa-angle-double-right"></i></span></a>
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
      <?php echo $sidebars; ?>
    </div>
  </div>
</div>
<?php get_footer(); ?>