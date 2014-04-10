<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="col-md-6 column" id="sidebar1">
  <?php include 'snippets/blogpuffs.php'; ?>   
  
  <div>
<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar("sidebar1")) : endif; ?>
  </div>
  <div class="clearfix"></div>
<?php
global $spp;
if (method_exists($spp, 'printPrenpuff'))
//$spp->printPrenpuff();     
  
  ?>
  <?php include 'snippets/socialtabs.php'; ?>   
  <div>
  <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar("sidebar11")) : endif; ?>
  </div>
  <div class="clearfix"></div>
  <?php include 'snippets/instagram.php'; ?> 

  <div class="clearfix"></div>
</div>