<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="col-sm-6 column" id="sidebar1">
  <!--div class="sidebar-header">
    <i class="fa fa-caret-right"></i> Tjoho
  </div-->
  <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar("sidebar1")) : endif; ?>
  <?php global $spp; if (method_exists($spp,'printPrenpuff')) $spp->printPrenpuff(); ?>
<!-- tabs -->
	<div id="social-tab">
        <div class="tabbable">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#facebook-tab" data-toggle="tab">Facebook</a></li>
            <li><a href="#twitter-tab" data-toggle="tab">Twitter</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="facebook-tab">

<div class="fb-like-box" data-href="https://www.facebook.com/magasinspring" data-width="100%" data-height="540" data-colorscheme="light" data-show-faces="true" data-header="false" data-stream="true" data-show-border="true"></div>


             </div>
            <div class="tab-pane" id="twitter-tab">
              	<a class="twitter-timeline" href="https://twitter.com/magasinspring" data-widget-id="448785162663243776">Tweets av @magasinspring</a>
                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
            </div>
           </div>
        </div>
	</div>
        <!-- /tabs --> 
<div id="instagram-feed">
<a href="http://instagram.com/magasinspring?ref=badge" class="ig-b- ig-b-v-24"><img src="//badges.instagram.com/static/images/ig-badge-view-24.png" alt="Instagram" /></a>
    <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar("sidebar11")) : endif; ?>
</div>
</div>