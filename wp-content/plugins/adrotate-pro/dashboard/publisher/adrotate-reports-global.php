<?php
/*  
Copyright 2010-2014 Arnan de Gans - AJdG Solutions (email : info@ajdg.net)
*/
?>

<h3><?php _e('Global statistics', 'adrotate'); ?></h3>
<table class="widefat" style="margin-top: .5em">

	<tbody>
	<?php if($adrotate_debug['stats'] == true) { ?>
	<tr>
		<td colspan="2">
			<?php 
			echo "<p><strong>Globalized Statistics from cache</strong><pre>"; 
			print_r($adrotate_stats); 
			echo "</pre></p>"; 
			?>
		</td>
	</tr>
	<?php } ?>

    <tr>
		<th width="25%"><?php _e('General', 'adrotate'); ?></th>
		<td><?php echo $adrotate_stats['banners']; ?> <?php _e('ads, sharing a total of', 'adrotate'); ?> <?php echo $adrotate_stats['impressions']; ?> <?php _e('impressions.', 'adrotate'); ?> <?php echo $adrotate_stats['tracker']; ?> <?php _e('ads have tracking enabled.', 'adrotate'); ?></td>
	</tr>
    <tr>
		<th><?php _e('Average clicks on all ads', 'adrotate'); ?></th>
		<td><?php echo $clicks; ?></td>
	</tr>
    <tr>
		<th><?php _e('Click-Through-Rate', 'adrotate'); ?></th>
		<td><?php echo $ctr; ?>%, <?php _e('based on', 'adrotate'); ?> <?php echo $adrotate_stats['impressions']; ?> <?php _e('impressions and', 'adrotate'); ?> <?php echo $adrotate_stats['clicks']; ?> <?php _e('clicks.', 'adrotate'); ?></td>
	</tr>
	</tbody>

</table>

<h3><?php _e('Monthly overview of clicks and impressions', 'adrotate'); ?></h3>
<table class="widefat" style="margin-top: .5em">

	<tbody>
  	<tr>
        <th colspan="2">
        	<div style="text-align:center;"><?php echo adrotate_stats_nav('global-report', 0, $month, $year); ?></div>
        	<?php echo adrotate_stats_graph('global-report', 0, 1, $monthstart, $monthend); ?>
        </th>
  	</tr>
	</tbody>

</table>

<form method="post" action="admin.php?page=adrotate-global-report">
<h3><?php _e('Export options', 'adrotate'); ?></h3>
<table class="widefat" style="margin-top: .5em">

	<tbody>
    <tr>
		<th><?php _e('Select period', 'adrotate'); ?></th>
		<td>
			<?php wp_nonce_field('adrotate_report_global','adrotate_nonce'); ?>
	    	<input type="hidden" name="adrotate_export_id" value="0" />
			<input type="hidden" name="adrotate_export_type" value="global" />
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
		<th><?php _e('Email options', 'adrotate'); ?></th>
		<td>
  			<input type="text" name="adrotate_export_addresses" size="45" class="search-input" value="" autocomplete="off" /> <em><?php _e('Maximum of 3 email addresses, comma seperated. Leave empty to download the CSV file instead.', 'adrotate'); ?></em>
		</td>
	</tr>
    <tr>
		<th>&nbsp;</th>
		<td>
  			<input type="submit" name="adrotate_export_submit" class="button-primary" value="<?php _e('Export', 'adrotate'); ?>" /> <em><?php _e('Download or email your selected timeframe as a CSV file.', 'adrotate'); ?></em>
		</td>
	</tr>
	</tbody>
</table>
</form>

<h3><?php _e('The last 50 clicks in the past 24 hours', 'adrotate'); ?></h3>
<?php if($adrotate_stats['lastclicks']) { ?>
<table class="widefat" style="margin-top: .5em">

	<thead>
	<tr>
		<th width="7%"><?php _e('Date', 'adrotate'); ?></th>
		<th width="15%"><?php _e('Advert', 'adrotate'); ?></th>
		<th><?php _e('User-Agent', 'adrotate'); ?></th>
		<th width="10%"><?php _e('Country', 'adrotate'); ?></th>
		<th width="10%"><?php _e('City', 'adrotate'); ?></th>
	</tr>
	</thead>

	<tbody>
	<?php
		foreach($adrotate_stats['lastclicks'] as $last) {
			$useragent = $country = $city = 'n/a';
			
			$thetime = $last['timer'];
			$bannertitle = $wpdb->get_var("SELECT `title` FROM `".$wpdb->prefix."adrotate` WHERE `id` = '$last[bannerid]'");
			if(!empty($last['useragent'])) $useragent = $last['useragent'];
			if(!empty($last['country'])) $country = $last['country'];
			if(!empty($last['city'])) $city = $last['city'];
	?>
		<tr>
			<td><?php echo '<strong>'.date_i18n('d-m-Y', $thetime) .'</strong>'; ?></td>
			<td><?php echo $bannertitle; ?></td>
			<td><?php echo $useragent; ?></td>
			<td><?php echo $country; ?></td>
			<td><?php echo $city; ?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<p><em><strong><?php _e('Note:', 'adrotate'); ?></strong> <?php _e('All statistics are indicative. They do not nessesarily reflect results counted by other parties.', 'adrotate'); ?></em></p>
<?php
} else {
	echo '<em>'.__('No recent clicks', 'adrotate').'</em>';
}
?>
