<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$profileimg = get_field('profilbild' , 'user_' . $curauthID);
$sponsimg1 = get_field('sponsorbild1', 'user_' . $curauthID);
$sponsimg2 = get_field('sponsorbild2', 'user_' . $curauthID);
$sponsimg3 = get_field('sponsorbild3', 'user_' . $curauthID);
$sponsimg4 = get_field('sponsorbild4', 'user_' . $curauthID);
$sponsimg5 = get_field('sponsorbild5', 'user_' . $curauthID);
$sponsimg6 = get_field('sponsorbild6', 'user_' . $curauthID);

$link1 = get_field('link1', 'user_' . $curauthID);
$link2 = get_field('link2', 'user_' . $curauthID);
$link3 = get_field('link3', 'user_' . $curauthID);
$link4 = get_field('link4', 'user_' . $curauthID);
$link5 = get_field('link5', 'user_' . $curauthID);
$link6 = get_field('link6', 'user_' . $curauthID);
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
    <?php the_field('biografi', 'user_' . $curauthID); ?>
  </div>
</div>

<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar("sidebar1")) : endif; ?>  
<?php if($sponsimg4): ?>
<div class="sidebar-header"><i class="fa fa-caret-right"></i> Sponsorer</div>
<div class="sponsor"><a href="<?php echo $link4; ?>" ><img src="<?php echo $sponsimg4['url']; ?>"></a></div>
<div class="sponsor"><a href="<?php echo $link5; ?>" ><img src="<?php echo $sponsimg5['url']; ?>"></a></div>
<div class="sponsor"><a href="<?php echo $link6; ?>" ><img src="<?php echo $sponsimg6['url']; ?>"></a></div>
<?php endif; ?>