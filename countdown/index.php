<?php
$startdate = date("Y-m-d H:i:s");
$enddate = "2014-03-13 14:56:00";

$diff = strtotime($enddate) - strtotime($startdate);
$temp = $diff / 86400; // 60 sec/min*60 min/hr*24 hr/day=86400 sec/day 
$days = floor($temp);
if(strlen($days) == 1){
  $days = '0'.$days;
}
$temp = 24 * ($temp - $days);
// hours 
$hours = floor($temp);
if(strlen($hours) == 1){
  $hours = '0'.$hours;
}
$temp = 60 * ($temp - $hours);
// minutes 
$minutes = floor($temp);
if(strlen($minutes) == 1){
  $minutes = '0'.$minutes;
}
$temp = 60 * ($temp - $minutes);
// seconds 
$seconds = floor($temp);
if(strlen($seconds) == 1){
  $seconds = '0'.$seconds;
}
$startTime = "{$days}:{$hours}:{$minutes}:{$seconds}";
?>  
<!DOCTYPE html>
<html>
  <head>
    <title>Spring</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <script src="http://code.jquery.com/jquery.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="js/jquery.countdown.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript">
      $(function() {
        $('#counter').countdown({
          image: 'img/digits.png',
          startTime: '<?php echo $startTime; ?>'
        });


      });
    </script>
    <style type="text/css">
      br { clear: both; }
      .cntSeparator {
        font-size: 54px;
        margin: 10px 7px;
        color: #000;
      }
      .desc { margin: 7px 3px; }
      .desc div {
        float: left;
        font-family: Arial;
        width: 70px;
        margin-right: 65px;
        font-size: 13px;
        font-weight: bold;
        color: #000;
      }
    </style>    
  </head>
  <body>
    <div>
      <h1>Spring!</h1>

      <div id="counter" style="height: 77px; overflow: hidden;"></div>    
    </div>
  </body>
</html>





<!--div class="container">
	<div class="row clearfix">
		<div class="col-md-12 column">
		</div>
	</div>
	<div class="row clearfix">
		<div class="col-md-12 column">
		</div>
	</div>
	<div class="row clearfix">
		<div class="col-md-12 column">
		</div>
	</div>
</div-->