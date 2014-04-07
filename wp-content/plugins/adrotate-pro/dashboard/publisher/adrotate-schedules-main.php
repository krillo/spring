<?php
/*  
Copyright 2010-2014 Arnan de Gans - AJdG Solutions (email : info@ajdg.net)
*/
?>
<h3><?php _e('Manage Schedules', 'adrotate'); ?></h3>

<form name="banners" id="post" method="post" action="admin.php?page=adrotate-schedules">
	<?php wp_nonce_field('adrotate_bulk_schedules','adrotate_nonce'); ?>

	<div class="tablenav top">
		<div class="alignleft actions">
			<select name="adrotate_action" id="cat" class="postform">
		        <option value=""><?php _e('Bulk Actions', 'adrotate'); ?></option>
		        <option value="schedule_delete"><?php _e('Delete', 'adrotate'); ?></option>
			</select> <input type="submit" id="post-action-submit" name="adrotate_action_submit" value="Go" class="button-secondary" />
		</div>	
		<br class="clear" />
	</div>

	<table class="widefat" style="margin-top: .5em">
		<thead>
		<tr>
			<th scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></th>
			<th width="4%"><center><?php _e('ID', 'adrotate'); ?></center></th>
			<th width="17%"><?php _e('Start', 'adrotate'); ?> / <?php _e('End', 'adrotate'); ?></th>
	        <th width="4%"><center><?php _e('Ads', 'adrotate'); ?></center></th>
			<th>&nbsp;</th>
	        <th width="12%"><center><?php _e('Max Clicks', 'adrotate'); ?></center></th>
	        <th width="12%"><center><?php _e('Max Impressions', 'adrotate'); ?></center></th>
		</tr>
		</thead>
		<tbody>
	<?php
	if ($schedules) {
		$class = '';
		foreach($schedules as $schedule) {
			$schedulesmeta = $wpdb->get_results("SELECT `ad` FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `group` = 0 AND `block` = 0 AND `user` = 0 AND `schedule` = ".$schedule['id'].";");
			$ads_use_schedule = '';
			if($schedulesmeta) {
				foreach($schedulesmeta as $meta) {
					$ads_use_schedule[] = $meta->ad;
					unset($meta);
				}
			}
			if($schedule['clicks'] == 0) $schedule['clicks'] = 'unlimited';
			if($schedule['impressions'] == 0) $schedule['impressions'] = 'unlimited';

			($class != 'alternate') ? $class = 'alternate' : $class = '';
			if($schedule['end'] < $in2days) $class = 'row_urgent';
			if($schedule['end'] < $now) $class = 'row_inactive';
			?>
		    <tr id='adrotateindex' class='<?php echo $class; ?>'>
				<th class="check-column"><input type="checkbox" name="schedulecheck[]" value="<?php echo $schedule['id']; ?>" /></th>
				<td><center><?php echo $schedule['id'];?></center></td>
				<td><?php echo date_i18n("F d, Y H:i", $schedule['start']);?><br /><span style="color: <?php echo adrotate_prepare_color($schedule['end']);?>;"><?php echo date_i18n("F d, Y H:i", $schedule['end']);?></span></td>
		        <td><center><?php echo count($schedulesmeta); ?></center></td>
				<td><a href="<?php echo admin_url('/admin.php?page=adrotate-schedules&view=edit&schedule='.$schedule['id']);?>"><?php echo stripslashes(html_entity_decode($schedule['name'])); ?></a></td>
		        <td><center><?php echo $schedule['clicks']; ?></center></td>
		        <td><center><?php echo $schedule['impressions']; ?></center></td>
			</tr>
			<?php } ?>
		<?php } else { ?>
		<tr id='no-schedules'>
			<th class="check-column">&nbsp;</th>
			<td colspan="7"><em><?php _e('No schedules created yet!', 'adrotate'); ?></em></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<p><center>
	<span style="border: 1px solid #c00; height: 12px; width: 12px; background-color: #ffebe8">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Expires soon.", "adrotate"); ?>
	&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #466f82; height: 12px; width: 12px; background-color: #8dcede">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Has expired.", "adrotate"); ?>
</center></p>
</form>
