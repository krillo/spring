<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$profileimg = get_field('profilbild', 'user_' . $curauth->ID);
$sponsimg1 = get_field('sponsorbild1', 'user_' . $curauth->ID);
$sponsimg2 = get_field('sponsorbild2', 'user_' . $curauth->ID);
$sponsimg3 = get_field('sponsorbild3', 'user_' . $curauth->ID);
$sponsimg4 = get_field('sponsorbild4', 'user_' . $curauth->ID);
$sponsimg5 = get_field('sponsorbild5', 'user_' . $curauth->ID);
$sponsimg6 = get_field('sponsorbild6', 'user_' . $curauth->ID);

$link1 = get_field('link1', 'user_' . $curauth->ID);
$link2 = get_field('link2', 'user_' . $curauth->ID);
$link3 = get_field('link3', 'user_' . $curauth->ID);
$link4 = get_field('link4', 'user_' . $curauth->ID);
$link5 = get_field('link5', 'user_' . $curauth->ID);
$link6 = get_field('link6', 'user_' . $curauth->ID);
?>

<?php if($sponsimg1): ?>
<div class="sidebar-header"><i class="fa fa-caret-right"></i> Huvudsponsorer</div>
<div class="sponsor"><a href="<?php echo $link1; ?>" ><img src="<?php echo $sponsimg1['url']; ?>"></a></div>
<div class="sponsor"><a href="<?php echo $link2; ?>" ><img src="<?php echo $sponsimg2['url']; ?>"></a></div>
<div class="sponsor"><a href="<?php echo $link3; ?>" ><img src="<?php echo $sponsimg3['url']; ?>"></a></div>
<?php endif; ?>
    
<div class="" id="bio">
  <div class="bio-inner">
    <img src="<?php echo $profileimg; ?>">
    <?php the_field('biografi', 'user_' . $curauth->ID); ?>
  </div>
</div>

<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar("sidebar1")) : endif; ?>  
<?php if($sponsimg4): ?>
<div class="sidebar-header"><i class="fa fa-caret-right"></i> Sponsorer</div>
<div class="sponsor"><a href="<?php echo $link4; ?>" ><img src="<?php echo $sponsimg4['url']; ?>"></a></div>
<div class="sponsor"><a href="<?php echo $link5; ?>" ><img src="<?php echo $sponsimg5['url']; ?>"></a></div>
<div class="sponsor"><a href="<?php echo $link6; ?>" ><img src="<?php echo $sponsimg6['url']; ?>"></a></div>
<?php endif; ?>