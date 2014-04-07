<?php
/*  
Copyright 2010-2014 Arnan de Gans - AJdG Solutions (email : info@ajdg.net)
*/
?>
<h3><?php _e('Rejected adverts', 'adrotate'); ?></h3>

<form name="banners" id="post" method="post" action="admin.php?page=adrotate-moderate">
	<?php wp_nonce_field('adrotate_bulk_ads_reject','adrotate_nonce'); ?>

	<div class="tablenav">
		<div class="alignleft actions">
			<select name="adrotate_reject_action" id="cat" class="postform">
		        <option value=""><?php _e('Bulk Actions', 'adrotate'); ?></option>
		        <option value="queue"><?php _e('Queue', 'adrotate'); ?></option>
		        <option value="approve"><?php _e('Approve', 'adrotate'); ?></option>
		        <option value="delete"><?php _e('Delete', 'adrotate'); ?></option>
			</select>
			<input type="submit" id="post-action-submit" name="adrotate_action_submit" value="Go" class="button-secondary" />
		</div>
	
		<br class="clear" />
	</div>

	<table class="widefat" style="margin-top: .5em">
		<thead>
		<tr>
			<th scope="col" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
			<th width="4%"><center><?php _e('ID', 'adrotate'); ?></center></th>
			<th width="12%"><?php _e('Show from', 'adrotate'); ?></th>
			<th width="12%"><?php _e('Show until', 'adrotate'); ?></th>
			<th>&nbsp;</th>
			<th width="20%"><?php _e('Advertiser', 'adrotate'); ?></th>
			<th width="5%"><center><?php _e('Weight', 'adrotate'); ?></center></th>
		</tr>
		</thead>
		<tbody>
	<?php
	if ($rejected) {
		$class = $errorclass = '';
		foreach($rejected as $reject) {			
			if($adrotate_debug['dashboard'] == true) {
				echo "<tr><td>&nbsp;</td><td><strong>[DEBUG]</strong></td><td colspan='5'><pre>";
				echo "Ad Specs: <pre>";
				print_r($reject); 
				echo "</pre></td></tr>"; 
			}
						
			$groups	= $wpdb->get_results("
				SELECT 
					`".$wpdb->prefix."adrotate_groups`.`name` 
				FROM 
					`".$wpdb->prefix."adrotate_groups`, 
					`".$wpdb->prefix."adrotate_linkmeta` 
				WHERE 
					`".$wpdb->prefix."adrotate_linkmeta`.`ad` = '".$reject['id']."'
					AND `".$wpdb->prefix."adrotate_linkmeta`.`group` = `".$wpdb->prefix."adrotate_groups`.`id`
					AND `".$wpdb->prefix."adrotate_linkmeta`.`block` = 0
					AND `".$wpdb->prefix."adrotate_linkmeta`.`user` = 0
				;");
			$grouplist = '';
			foreach($groups as $group) {
				$grouplist .= $group->name.", ";
			}
			$grouplist = rtrim($grouplist, ", ");

			$advertiser_id = $wpdb->get_var("SELECT `user` FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = ".$reject['id'].";");
			$advertiser = get_user_by('id', $advertiser_id);
			
			if($class != 'alternate') {
				$class = 'alternate';
			} else {
				$class = '';
			}

			if($reject['lastactive'] <= $in7days) {
				$errorclass = ' row_error';
			} else {
				$errorclass = '';
			}

			?>
		    <tr id='adrotateindex' class='<?php echo $class.$errorclass; ?>'>
				<th class="check-column"><input type="checkbox" name="rejectcheck[]" value="<?php echo $reject['id']; ?>" /></th>
				<td><center><?php echo $reject['id'];?></center></td>
				<td><?php echo date_i18n("F d, Y", $reject['firstactive']);?></td>
				<td><span style="color: <?php echo adrotate_prepare_color($reject['lastactive']);?>;"><?php echo date_i18n("F d, Y", $reject['lastactive']);?></span></td>
				<td><strong><a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-ads&view=edit&ad='.$reject['id']);?>" title="<?php _e('Edit', 'adrotate'); ?>"><?php echo stripslashes(html_entity_decode($reject['title']));?></a></strong><?php if($groups) echo '<br /><em style="color:#999">'.$grouplist.'</em>'; ?></td>
				<td><?php echo $advertiser->display_name; ?></td>
				<td><center><?php echo $reject['weight']; ?></center></td>
			</tr>
			<?php } ?>
		<?php } else { ?>
		<tr id='no-groups'>
			<th class="check-column">&nbsp;</th>
			<td colspan="10"><em><?php _e('No ads in queue yet!', 'adrotate'); ?></em></td>
		</tr>
	<?php } ?>
	</tbody>
</table>

</form>
