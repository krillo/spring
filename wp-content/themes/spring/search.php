<?php get_header(); ?>
<div class="row clearfix">
  <div class="col-md-6 column">
        <!--h1><?php single_cat_title(); ?></h1-->
        <h1>Sökresultat</h1>
        <?php if ( have_posts() ) : ?>
        <?php while (have_posts()) : the_post(); ?>
          <div class="row">
            <article id="post-<?php the_ID(); ?>" class="col-md-12">
              <header>
                <h2><?php the_title(); ?></h2>
                <div class="pub-info"><i class="fa fa-calendar"></i><time pubdate="pubdate"><?php the_modified_date(); ?></time> | <?php the_category(', '); ?></div>
              </header>
              <div class="archive-content"><?php /* echo mb_substr(get_the_content(), 0, 400) . '...'; */ the_excerpt(); ?>
                <a href="<?php the_guid(); ?>"> Läs mer &raquo;</a>
              </div>
            </article>
          </div>
        <?php endwhile; ?>
      <?php else : ?>
          <h2>Sidan finns tyvärr inte!</h2>
              <div class="row search-area-large">
                  <form action="/" method="get" role="search" id="hbg-search">
                    <div class="input-group input-group-lg">
                      <input type="text" class="form-control" placeholder="Vad söker du?" autocomplete="off" type="text" name="s">
                      <span class="input-group-btn">
                        <input class="btn btn-default hbg-btn" type="submit" value="Sök!">
                      </span>
                    </div>
                  </form>
                </div>
      <?php endif; ?>
        <?php
        if (function_exists('bootstrap3_pagination')) {
          bootstrap3_pagination();
        }
        ?>        
      </div>
      <div class="col-md-6 column">
    <div class="row clearfix">
      <?php include('sidebar1.php'); ?>
      <?php include('sidebar2.php'); ?>
    </div>
  </div>
</div>
<?php get_footer(); ?>