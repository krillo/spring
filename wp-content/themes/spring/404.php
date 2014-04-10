<?php get_header(); ?>
<div class="row clearfix">
  <div class="col-md-6 column">
    <h1>Sidan finns tyvärr inte!</h1>
    <div class="row search-area-large">
      <div class="col-md-8">
        <form action="/" method="get" role="search" id="hbg-search">
          <div class="input-group input-group-lg">
            <input type="text" class="form-control" placeholder="Vad söker du?" autocomplete="off" type="text" name="s">
            <span class="input-group-btn">
              <input class="btn btn-default hbg-btn" type="submit" value="Sök!">
            </span>
          </div>
        </form>
      </div>
    </div>     
  </div>
  <div class="col-md-6 column">
    <div class="row clearfix">
      <?php include('sidebar1.php'); ?>
      <?php include('sidebar2.php'); ?>
    </div>
  </div>
</div>
<?php get_footer(); ?>