<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="col-md-6 column" id="sidebar2">
  <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar("sidebar2")) : endif; ?>  
  
  <div class="sidebar-header"><i class="fa fa-caret-right"></i> Boktips</div>
  <?php global $littTips; if (method_exists($littTips,'printLitteraturtips')) $littTips->printLitteraturtips(); ?>
  <?php global $rc; if (method_exists($rc,'rep_carousel')) $rc->rep_carousel('rep-carousel', false); ?>
</div>