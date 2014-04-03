<?php
/**
 * Set $showCat to true to show category 
 */ 
?>
<div class="pub-info">
  <i class="fa fa-calendar-o"></i>
  <time pubdate="pubdate"><?php the_modified_date(); ?></time>
  <?php if($showCat == true): ?><i class="fa fa-thumb-tack"></i><?php the_category(', '); endif;?> 
  <i class="fa fa-tags"></i><?php the_tags(' '); ?>
</div>