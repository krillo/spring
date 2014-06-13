<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$profileimg = get_field('image', 'user_' . $curauth->ID); 
$sponsimg1 = get_field('sponsorbild1', 'user_' . $curauth->ID); 
?>


<div class="" id="bio">
  <img src="<?php echo $profileimg; ?>">
  <?php the_field('biografi', 'user_' . $curauth->ID); ?>
</div>

<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar("sidebar1")) : endif; ?>  
<div class="sidebar-header"><i class="fa fa-caret-right"></i> Sponsorer</div>

<div class="sponsor"><a href="<?php the_permalink(); ?>" ><img src="<?php echo $sponsimg1['url'];?>"></a></div>