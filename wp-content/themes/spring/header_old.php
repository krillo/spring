<?php
/**
 * The Header for Theme Egenutgivare.
 * WordPress theme based on Reptilo which is based on Bootstrap 3.0.3, http://getbootstrap.com/
 *
 * @package WordPress
 * @subpackage Egenutgivare
 * @since 2014-01
 */
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <meta name="author" content="">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo get_stylesheet_directory_uri(); ?>/img/favicon.ico">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
    
    <title><?php wp_title('|', true, 'right'); ?></title>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <?php wp_head(); ?>
  </head>

  <body <?php body_class(); ?> >
    <div id="hbg-top">
      <div class="container">
        <div class="row">
          <nav class="navbar navbar-default" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="container">
              <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php bloginfo('url'); ?>">
                  <i class="fa fa-home"></i>
                </a>
              </div>

              <?php
              wp_nav_menu(array(
                  'menu' => 'primary',
                  'theme_location' => 'primary',
                  'depth' => 2,
                  'container' => 'div',
                  'container_class' => 'collapse navbar-collapse navbar-ex1-collapse',
                  'menu_class' => 'nav navbar-nav',
                  'fallback_cb' => 'wp_bootstrap_navwalker::fallback',
                  'walker' => new wp_bootstrap_navwalker())
              );
              ?>
            </div>
          </nav>
        </div>
      </div>
    </div>

    <div class="container" id="">
      <div class="row">
        <div class="col-sm-12">
          <div id="rep-splash-area">
            <div id="logo"><a class="" href="<?php bloginfo('url'); ?>">Egenutgivare.nu</a></div>
            <!--p>< ?php echo get_bloginfo('description'); ? ></p-->
          </div>
        </div>
      </div>
    </div>
    <div id="headline-puff">
      <div class="container">
        <div class="row">
          <div class="col-sm-12">
            Här finner du idéer och inspiration som hjälper dig i din professionella egenutgivning. <a href="/blogg/">Läs om författarkollegornas erfarenheter</a>
          </div>
        </div>
      </div>
    </div>







