<div class="sidebar-header"><i class="fa fa-caret-right"></i> Bloggar</div>
<?php
$latestblogs = array();
$blogusers = get_users('blog_id=1&orderby=nicename&role=author');
foreach ($blogusers as $user) {
  $recent = get_posts(array(
      'author' => $user->ID,
      'orderby' => 'date',
      'order' => 'desc',
      'numberposts' => 1,
      'post_status' => 'publish'
  ));
  if ($recent) {
    $blogpuff = new stdClass;
    $blogpuff->user_id = $user->ID;
    $blogpuff->name = $user->display_name;
    $blogpuff->post_date = $recent[0]->post_date;
    $blogpuff->permalink = get_permalink($recent[0]->ID);
    $blogpuff->title = get_the_title($recent[0]->ID);
    $blogpuff->img = get_field('image', 'user_'.$user->ID);
    $latestblogs[$blogpuff->post_date] = $blogpuff;
  }
}
krsort($latestblogs);
foreach ($latestblogs as $blogpuffx) :
  ?>
  <div class="blogg-puff">
    <img src="<?php echo $blogpuffx->img; ?>" class="img-circle" />
    
    <h3><?php echo $blogpuffx->name; ?></h3>
    <p><?php echo $blogpuffx->title; ?></p>
    <a href="<?php echo $blogpuffx->permalink ?>">Läs senaste inlägg &raquo;</a>
  </div>
<?php endforeach; ?>
<div class="devider-space"></div>