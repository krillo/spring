<?php
/*  
Copyright 2010-2014 Arnan de Gans - AJdG Solutions (email : info@ajdg.net)
*/

$banner 		= $wpdb->get_row("SELECT `title`, `tracker` FROM `".$wpdb->prefix."adrotate` WHERE `id` = '$ad_edit_id';");
$stats 			= adrotate_stats($ad_edit_id);
$stats_today 	= adrotate_stats($ad_edit_id, $today);

// Get Click Through Rate
$ctr = adrotate_ctr($stats['clicks'], $stats['impressions']);

if($adrotate_debug['stats'] == true) {
	echo "<p><strong>[DEBUG] Ad Stats (all time)</strong><pre>";
	print_r($stats); 
	echo "</pre></p>"; 
	echo "<p><strong>[DEBUG] Ad Stats (today)</strong><pre>";
	print_r($stats_today); 
	echo "</pre></p>"; 
}	
?>

<h3><?php _e('Statistics for advert', 'adrotate'); ?> '<?php echo $banner->title; ?>'</h3>
<table class="widefat" style="margin-top: .5em">

	<tbody>
  	<tr>
        <td width="20%"><div class="stats_large"><?php _e('Impressions', 'adrotate'); ?><br /><div class="number_large"><?php echo $stats['impressions']; ?></div></div></td>
        <td width="20%"><div class="stats_large"><?php _e('Clicks', 'adrotate'); ?><br /><div class="number_large"><?php if($banner->tracker == "Y") { echo $stats['clicks']; } else { echo '--'; } ?></div></div></td>
        <td width="20%"><div class="stats_large"><?php _e('Impressions today', 'adrotate'); ?><br /><div class="number_large"><?php echo $stats_today['impressions']; ?></div></div></td>
        <td width="20%"><div class="stats_large"><?php _e('Clicks today', 'adrotate'); ?><br /><div class="number_large"><?php if($banner->tracker == "Y") { echo $stats_today['clicks']; } else { echo '--'; } ?></div></div></td>
        <td width="20%"><div class="stats_large"><?php _e('CTR', 'adrotate'); ?><br /><div class="number_large"><?php if($banner->tracker == "Y") { echo $ctr.' %'; } else { echo '--'; } ?></div></div></td>
  	</tr>
	<tbody>

</table>

<h3><?php _e('Monthly overview of clicks and impressions', 'adrotate'); ?></h3>
<table class="widefat" style="margin-top: .5em">

	<tbody>
  	<tr>
        <th colspan="5">
        	<div style="text-align:center;"><?php echo adrotate_stats_nav('ads', $ad_edit_id, $month, $year); ?></div>
        	<?php echo adrotate_stats_graph('ads', $ad_edit_id, 1, $monthstart, $monthend); ?>
        </th>
  	</tr>
	</tbody>

</table>	


<form method="post" action="admin.php?page=adrotate-ads">
<h3><?php _e('Export options', 'adrotate'); ?></h3>
<table class="widefat" style="margin-top: .5em">

	<tbody>
    <tr>
		<th width="10%"><?php _e('Select period', 'adrotate'); ?></th>
		<td width="40%" colspan="4">
			<?php wp_nonce_field('adrotate_report_ads','adrotate_nonce'); ?>
	    	<input type="hidden" name="adrotate_export_id" value="<?php echo $ad_edit_id; ?>" />
			<input type="hidden" name="adrotate_export_type" value="single" />
	        <select name="adrotate_export_month" id="cat" class="postform">
		        <option value="0"><?php _e('Whole year', 'adrotate'); ?></option>
		        <option value="1" <?php if($month == "1") { echo 'selected'; } ?>><?php _e('January', 'adrotate'); ?></option>
		        <option value="2" <?php if($month == "2") { echo 'selected'; } ?>><?php _e('February', 'adrotate'); ?></option>
		        <option value="3" <?php if($month == "3") { echo 'selected'; } ?>><?php _e('March', 'adrotate'); ?></option>
		        <option value="4" <?php if($month == "4") { echo 'selected'; } ?>><?php _e('April', 'adrotate'); ?></option>
		        <option value="5" <?php if($month == "5") { echo 'selected'; } ?>><?php _e('May', 'adrotate'); ?></option>
		        <option value="6" <?php if($month == "6") { echo 'selected'; } ?>><?php _e('June', 'adrotate'); ?></option>
		        <option value="7" <?php if($month == "7") { echo 'selected'; } ?>><?php _e('July', 'adrotate'); ?></option>
		        <option value="8" <?php if($month == "8") { echo 'selected'; } ?>><?php _e('August', 'adrotate'); ?></option>
		        <option value="9" <?php if($month == "9") { echo 'selected'; } ?>><?php _e('September', 'adrotate'); ?></option>
		        <option value="10" <?php if($month == "10") { echo 'selected'; } ?>><?php _e('October', 'adrotate'); ?></option>
		        <option value="11" <?php if($month == "11") { echo 'selected'; } ?>><?php _e('November', 'adrotate'); ?></option>
		        <option value="12" <?php if($month == "12") { echo 'selected'; } ?>><?php _e('December', 'adrotate'); ?></option>
			</select> 
			<input type="text" name="adrotate_export_year" size="10" class="search-input" value="<?php echo date('Y'); ?>" autocomplete="off" />
		</td>
	</tr>
    <tr>
		<th width="10%"><?php _e('Email options', 'adrotate'); ?></th>
		<td width="40%" colspan="4">
  			<input type="text" name="adrotate_export_addresses" size="45" class="search-input" value="" autocomplete="off" /> <em><?php _e('Maximum of 3 email addresses, comma seperated. Leave empty to download the CSV file instead.', 'adrotate'); ?></em>
		</td>
	</tr>
    <tr>
		<th width="10%">&nbsp;</th>
		<td width="40%" colspan="4">
  			<input type="submit" name="adrotate_export_submit" class="button-primary" value="<?php _e('Export', 'adrotate'); ?>" /> <em><?php _e('Download or email your selected timeframe as a CSV file.', 'adrotate'); ?></em>
		</td>
	</tr>
	</tbody>

</table>
</form>
<p><em><strong><?php _e('Note:', 'adrotate'); ?></strong> <em><?php _e('All statistics are indicative. They do not nessesarily reflect results counted by other parties.', 'adrotate'); ?></em></p>