<?php
/**
 * 
 * @author Kristain Erendi
 * @subpackage Template
 * 
 */
get_header();
?>
<div class="row clearfix" >
  <div class="col-sm-6 column">
    <?php include 'snippets/mainloop.php'; ?>
  </div>
  <div class="col-sm-6 column">
    <div class="row clearfix">
      <?php include('sidebar1.php'); ?>
      <?php include('sidebar2.php'); ?>
    </div>
  </div>
</div>
<?php get_footer(); ?>