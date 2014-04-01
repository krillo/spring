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
    <meta name="description" content="">
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
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id))
          return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/sv_SE/all.js#xfbml=1&appId=1384869975122270";
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
    </script>

    <div class="container">
      <div class="row clearfix">
        <div class="col-sm-12 column" id="ad1">
          <img alt="" src="<?php echo get_stylesheet_directory_uri(); ?>/tmp/banner_930.png" />
        </div>
      </div>
      <div class="row clearfix" id="spring-container">
        <div class="col-sm-12 column">
          <div class="col-sm-6 column" id="logo">
            <img alt="" src="<?php echo get_stylesheet_directory_uri(); ?>/img/logo.png" />
          </div>
          <div class="col-sm-6 column " id="omslag-box">
            <div class="row clearfix">
              <div class="col-sm-12 column ">
                <!--img  src="http://spring.dev/wp-content/uploads/2014/03/Spring_1_20141-118x150.jpeg" class="attachment-90x115 wp-post-image" alt="Spring_1_2014" style="max-width:115px;float:left;">
                <h2>Aktuellt nummer</h2>
                <span class="omslag-nummer" style="">Spring / Nr 1 / 2014 </span>
                <ul>    
                  <li><i class="fa fa-caret-right"></i><a href="#">Se hela innehållet</a></li>
                  <li><i class="fa fa-caret-right"></i><a href="#">Prenumerera på Spring</a></li>
                  <li><i class="fa fa-caret-right"></i><a href="#">Läs tidningen digitalt som prenumerant</a></li>
                </ul-->    
                <?php
                global $omslag;
                if (method_exists($omslag, 'printOmslag')) $omslag->printOmslag(); 
                ?>
                <!--img alt="" src="http://spring.dev/wp-content/themes/spring/tmp/top-right-pren-puff.png" /-->  
              </div>
            </div>
          </div>
        </div>






