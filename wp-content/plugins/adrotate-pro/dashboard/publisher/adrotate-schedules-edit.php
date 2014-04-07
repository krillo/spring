<?php
/*  
Copyright 2010-2014 Arnan de Gans - AJdG Solutions (email : info@ajdg.net)
*/
?>

<?php if(!$schedule_edit_id) { ?>
	<h3><?php _e('New Schedule', 'adrotate'); ?></h3>
<?php
	$edit_id = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."adrotate_schedule` WHERE `name` = '' AND `starttime` < $now ORDER BY `id` DESC LIMIT 1;");
	if($edit_id == 0) {
		$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => '', 'starttime' => $now, 'stoptime' => $in84days, 'maxclicks' => 0, 'maximpressions' => 0));
	    $edit_id = $wpdb->insert_id;
	}
	$schedule_edit_id = $edit_id;
} else { ?>
	<h3><?php _e('Edit Schedule', 'adrotate'); ?></h3>
<?php
}

$edit_schedule = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."adrotate_schedule` WHERE `id` = $schedule_edit_id;");

list($sday, $smonth, $syear) = explode(" ", date("d m Y", $edit_schedule->starttime));
list($eday, $emonth, $eyear) = explode(" ", date("d m Y", $edit_schedule->stoptime));
$shour = $sminute = $ehour = $eminute = '00';
?>

<form method="post" action="admin.php?page=adrotate-schedules">
	<?php wp_nonce_field('adrotate_save_schedule','adrotate_nonce'); ?>
	<input type="hidden" name="adrotate_id" value="<?php echo $edit_schedule->id;?>" />

	<table class="widefat" style="margin-top: .5em">
		<tbody>
      	<tr>
      		<th><?php _e('ID:', 'adrotate'); ?></th>
	        <td colspan="3">
	        	<?php echo $edit_schedule->id; ?>
			</td>
		</tr>
      	<tr>
      		<th><?php _e('Name:', 'adrotate'); ?></th>
	        <td colspan="3">
	        	<label for="adrotate_schedulename"><input tabindex="1" name="adrotate_schedulename" type="text" class="search-input" size="40" value="<?php echo stripslashes(html_entity_decode($edit_schedule->name)); ?>" autocomplete="off" /> <em><?php _e('Will be visible to Advertisers.', 'adrotate'); ?></em></label>
			</td>
		</tr>
      	<tr>
	        <th><?php _e('Start date (day/month/year):', 'adrotate'); ?></th>
	        <td>
	        	<label for="adrotate_sday">
	        	<input tabindex="21" name="adrotate_sday" class="search-input" type="text" size="4" maxlength="2" value="<?php echo $sday;?>" /> /
				<select tabindex="22" name="adrotate_smonth">
					<option value="01" <?php if($smonth == "01") { echo 'selected'; } ?>><?php _e('January', 'adrotate'); ?></option>
					<option value="02" <?php if($smonth == "02") { echo 'selected'; } ?>><?php _e('February', 'adrotate'); ?></option>
					<option value="03" <?php if($smonth == "03") { echo 'selected'; } ?>><?php _e('March', 'adrotate'); ?></option>
					<option value="04" <?php if($smonth == "04") { echo 'selected'; } ?>><?php _e('April', 'adrotate'); ?></option>
					<option value="05" <?php if($smonth == "05") { echo 'selected'; } ?>><?php _e('May', 'adrotate'); ?></option>
					<option value="06" <?php if($smonth == "06") { echo 'selected'; } ?>><?php _e('June', 'adrotate'); ?></option>
					<option value="07" <?php if($smonth == "07") { echo 'selected'; } ?>><?php _e('July', 'adrotate'); ?></option>
					<option value="08" <?php if($smonth == "08") { echo 'selected'; } ?>><?php _e('August', 'adrotate'); ?></option>
					<option value="09" <?php if($smonth == "09") { echo 'selected'; } ?>><?php _e('September', 'adrotate'); ?></option>
					<option value="10" <?php if($smonth == "10") { echo 'selected'; } ?>><?php _e('October', 'adrotate'); ?></option>
					<option value="11" <?php if($smonth == "11") { echo 'selected'; } ?>><?php _e('November', 'adrotate'); ?></option>
					<option value="12" <?php if($smonth == "12") { echo 'selected'; } ?>><?php _e('December', 'adrotate'); ?></option>
				</select> /
				<input tabindex="23" name="adrotate_syear" class="search-input" type="text" size="4" maxlength="4" value="<?php echo $syear;?>" />&nbsp;&nbsp;&nbsp; 
				</label>
	        </td>
	        <th><?php _e('End date (day/month/year):', 'adrotate'); ?></th>
	        <td>
	        	<label for="adrotate_eday">
	        	<input tabindex="24" name="adrotate_eday" class="search-input" type="text" size="4" maxlength="2" value="<?php echo $eday;?>"  /> /
				<select tabindex="25" name="adrotate_emonth">
					<option value="01" <?php if($emonth == "01") { echo 'selected'; } ?>><?php _e('January', 'adrotate'); ?></option>
					<option value="02" <?php if($emonth == "02") { echo 'selected'; } ?>><?php _e('February', 'adrotate'); ?></option>
					<option value="03" <?php if($emonth == "03") { echo 'selected'; } ?>><?php _e('March', 'adrotate'); ?></option>
					<option value="04" <?php if($emonth == "04") { echo 'selected'; } ?>><?php _e('April', 'adrotate'); ?></option>
					<option value="05" <?php if($emonth == "05") { echo 'selected'; } ?>><?php _e('May', 'adrotate'); ?></option>
					<option value="06" <?php if($emonth == "06") { echo 'selected'; } ?>><?php _e('June', 'adrotate'); ?></option>
					<option value="07" <?php if($emonth == "07") { echo 'selected'; } ?>><?php _e('July', 'adrotate'); ?></option>
					<option value="08" <?php if($emonth == "08") { echo 'selected'; } ?>><?php _e('August', 'adrotate'); ?></option>
					<option value="09" <?php if($emonth == "09") { echo 'selected'; } ?>><?php _e('September', 'adrotate'); ?></option>
					<option value="10" <?php if($emonth == "10") { echo 'selected'; } ?>><?php _e('October', 'adrotate'); ?></option>
					<option value="11" <?php if($emonth == "11") { echo 'selected'; } ?>><?php _e('November', 'adrotate'); ?></option>
					<option value="12" <?php if($emonth == "12") { echo 'selected'; } ?>><?php _e('December', 'adrotate'); ?></option>
				</select> /
				<input tabindex="26" name="adrotate_eyear" class="search-input" type="text" size="4" maxlength="4" value="<?php echo $eyear;?>" />&nbsp;&nbsp;&nbsp; 
				</label>
			</td>
      	</tr>	
      	<tr>
	        <th><?php _e('Start time (hh:mm):', 'adrotate'); ?></th>
	        <td>
	        	<label for="adrotate_sday">
				<input tabindex="27" name="adrotate_shour" class="search-input" type="text" size="2" maxlength="4" value="<?php echo $shour;?>" /> :
				<input tabindex="28" name="adrotate_sminute" class="search-input" type="text" size="2" maxlength="4" value="<?php echo $sminute;?>" />
				</label>
	        </td>
	        <th><?php _e('End time (hh:mm):', 'adrotate'); ?></th>
	        <td>
	        	<label for="adrotate_eday">
				<input tabindex="29" name="adrotate_ehour" class="search-input" type="text" size="2" maxlength="4" value="<?php echo $ehour;?>" /> :
				<input tabindex="30" name="adrotate_eminute" class="search-input" type="text" size="2" maxlength="4" value="<?php echo $eminute;?>" />
				</label>
			</td>
      	</tr>	
		<?php if($adrotate_config['enable_stats'] == 'Y') { ?>
      	<tr>
      		<th><?php _e('Maximum Clicks:', 'adrotate'); ?></th>
	        <td><input tabindex="31" name="adrotate_maxclicks" type="text" size="5" class="search-input" autocomplete="off" value="<?php echo $edit_schedule->maxclicks; ?>" /> <em><?php _e('Leave empty or 0 to skip this.', 'adrotate'); ?></em></td>
		    <th><?php _e('Maximum Impressions:', 'adrotate'); ?></th>
	        <td><input tabindex="32" name="adrotate_maxshown" type="text" size="5" class="search-input" autocomplete="off" value="<?php echo $edit_schedule->maximpressions; ?>" /> <em><?php _e('Leave empty or 0 to skip this.', 'adrotate'); ?></em></td>
		</tr>
		<?php } ?>
		</tbody>	

	</table>
	
	<?php if($adrotate_config['hide_schedules'] == "Y") { ?>
	<p><em><strong><?php _e('Note:', 'adrotate'); ?></strong> <?php _e("Adverts hide schedules that are not used by that advert. Edit the advert and create the schedule from each advert instead.", "adrotate"); ?></em></p>
	<?php } ?>

	<p><em><strong><?php _e('Note:', 'adrotate'); ?></strong> <?php _e('Time uses a 24 hour clock. When you\'re used to the AM/PM system keep this in mind: If the start or end time is after lunch, add 12 hours. 2PM is 14:00 hours. 6AM is 6:00 hours.', 'adrotate'); ?><br /><?php _e('The maximum clicks and impressions are measured over the set schedule only and applies to all adverts using this schedule combined. Every schedule can have it\'s own limit!', 'adrotate'); ?></em></p>

	<p class="submit">
		<input tabindex="38" type="submit" name="adrotate_schedule_submit" class="button-primary" value="<?php _e('Save Schedule', 'adrotate'); ?>" />
		<a href="admin.php?page=adrotate-schedules" class="button"><?php _e('Cancel', 'adrotate'); ?></a>
	</p>
</form>