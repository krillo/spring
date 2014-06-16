<?php
$authorID = get_the_author_ID();
$bylineimg = get_field('image', 'user_' . $authorID);
?>
<div class="rep-byline">
  <img src="<?php echo $bylineimg; ?>" class="" />  
  <div class="rep-byline-contact">
    <span class="rep-byline-contact-name"><?php echo get_the_author_firstname() . ' '. get_the_author_lastname(); ?></span><br/>
    <a href="mailto:<?php echo get_the_author_email(); ?>"><?php echo get_the_author_email(); ?></a>
  </div>
  <div class="rep-byline-date">
    Skriven <?php echo get_the_date(); ?><br/>
    <i class="fa fa-print"></i><a href="javascript:window.print()">Skriv ut</a>
  </div>
</div>