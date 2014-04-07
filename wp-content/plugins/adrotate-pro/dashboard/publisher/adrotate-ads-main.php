<?php
/*  
Copyright 2010-2014 Arnan de Gans - AJdG Solutions (email : info@ajdg.net)
*/
?>
<h3><?php _e('Active Ads', 'adrotate'); ?></h3>

<form name="banners" id="post" method="post" action="admin.php?page=adrotate-ads">
	<?php wp_nonce_field('adrotate_bulk_ads_active','adrotate_nonce'); ?>

	<div class="tablenav top">
		<div class="alignleft actions">
			<select name="adrotate_action" id="cat" class="postform">
		        <option value=""><?php _e('Bulk Actions', 'adrotate'); ?></option>
		        <option value="deactivate"><?php _e('Deactivate', 'adrotate'); ?></option>
		        <option value="delete"><?php _e('Delete', 'adrotate'); ?></option>
		        <option value="reset"><?php _e('Reset stats', 'adrotate'); ?></option>
		        <option value="export"><?php _e('Export to CSV', 'adrotate'); ?></option>
		        <option value="" disabled><?php _e('-- Renew --', 'adrotate'); ?></option>
		        <option value="renew-31536000"><?php _e('For 1 year', 'adrotate'); ?></option>
		        <option value="renew-5184000"><?php _e('For 180 days', 'adrotate'); ?></option>
		        <option value="renew-2592000"><?php _e('For 30 days', 'adrotate'); ?></option>
		        <option value="renew-604800"><?php _e('For 7 days', 'adrotate'); ?></option>
		        <option value="" disabled><?php _e('-- Weight --', 'adrotate'); ?></option>
		        <option value="weight-2">2 - <?php _e('Barely visible', 'adrotate'); ?></option>
		        <option value="weight-4">4 - <?php _e('Less than average', 'adrotate'); ?></option>
		        <option value="weight-6">6 - <?php _e('Normal coverage', 'adrotate'); ?></option>
		        <option value="weight-8">8 - <?php _e('More than average', 'adrotate'); ?></option>
		        <option value="weight-10">10 - <?php _e('Best visibility', 'adrotate'); ?></option>
			</select> <input type="submit" id="post-action-submit" name="adrotate_action_submit" value="Go" class="button-secondary" />
		</div>	
		<br class="clear" />
	</div>

	<table class="widefat" style="margin-top: .5em">
		<thead>
			<tr>
			<th scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></th>
			<th width="2%"><center><?php _e('ID', 'adrotate'); ?></center></th>
			<th width="12%"><?php _e('Show from', 'adrotate'); ?></th>
			<th width="12%"><?php _e('Show until', 'adrotate'); ?></th>
			<th><?php _e('Title', 'adrotate'); ?></th>
			<th width="5%"><center><?php _e('Weight', 'adrotate'); ?></center></th>
			<th width="5%"><center><?php _e('Shown', 'adrotate'); ?></center></th>
			<th width="5%"><center><?php _e('Today', 'adrotate'); ?></center></th>
			<th width="5%"><center><?php _e('Clicks', 'adrotate'); ?></center></th>
			<th width="5%"><center><?php _e('Today', 'adrotate'); ?></center></th>
			<th width="7%"><center><?php _e('CTR', 'adrotate'); ?></center></th>
		</tr>
		</thead>
		<tbody>
	<?php
	if ($activebanners) {
		$class = '';
		foreach($activebanners as $banner) {
			$stats = adrotate_stats($banner['id']);
			$stats_today = adrotate_stats($banner['id'], $today);
			$grouplist = adrotate_ad_is_in_groups($banner['id']);

			$ctr = adrotate_ctr($stats['clicks'], $stats['impressions']);						
			
			if($adrotate_debug['dashboard'] == true) {
				echo "<tr><td>&nbsp;</td><td><strong>[DEBUG]</strong></td><td colspan='9'><pre>";
				echo "Ad Specs: <pre>";
				print_r($banner); 
				echo "</pre>"; 
				echo "Stats: <pre>";
				print_r($stats); 
				echo "</pre>"; 
				echo "Stats today: <pre>";
				print_r($stats_today); 
				echo "</pre></td></tr>"; 
			}
						
			if($class != 'alternate') {
				$class = 'alternate';
			} else {
				$class = '';
			}

			?>
		    <tr id='adrotateindex' class='<?php echo $class; ?>'>
				<th class="check-column"><input type="checkbox" name="bannercheck[]" value="<?php echo $banner['id']; ?>" /></th>
				<td><center><?php echo $banner['id'];?></center></td>
				<td><?php echo date_i18n("F d, Y", $banner['firstactive']);?></td>
				<td><span style="color: <?php echo adrotate_prepare_color($banner['lastactive']);?>;"><?php echo date_i18n("F d, Y", $banner['lastactive']);?></span></td>
				<td>
					<strong><a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-ads&view=edit&ad='.$banner['id']);?>" title="<?php _e('Edit', 'adrotate'); ?>"><?php echo stripslashes(html_entity_decode($banner['title']));?></a></strong> - <a href="<?php echo admin_url('/admin.php?page=adrotate-ads&view=report&ad='.$banner['id']);?>" title="<?php _e('Stats', 'adrotate'); ?>"><?php _e('Stats', 'adrotate'); ?></a>
					<span style="color:#999;">
						<?php if(strlen($grouplist) > 0) echo '<br /><span style="font-weight:bold;">Groups:</span> '.$grouplist; ?>
						<?php if(strlen($banner['advertiser']) > 0 AND $adrotate_config['enable_advertisers'] == 'Y') echo '<br /><span style="font-weight:bold;">Advertiser:</span> '.$banner['advertiser']; ?>
					</span>
				</td>
				<td><center><?php echo $banner['weight']; ?></center></td>
				<td><center><?php echo $stats['impressions']; ?></center></td>
				<td><center><?php echo $stats_today['impressions']; ?></center></td>
				<?php if($banner['tracker'] == "Y") { ?>
				<td><center><?php echo $stats['clicks']; ?></center></td>
				<td><center><?php echo $stats_today['clicks']; ?></center></td>
				<td><center><?php echo $ctr; ?> %</center></td>
				<?php } else { ?>
				<td><center>--</center></td>
				<td><center>--</center></td>
				<td><center>--</center></td>
				<?php } ?>
			</tr>
			<?php } ?>
		<?php } else { ?>
		<tr id='no-groups'>
			<th class="check-column">&nbsp;</th>
			<td colspan="10"><em><?php _e('No ads created yet!', 'adrotate'); ?></em></td>
		</tr>
	<?php } ?>
	</tbody>
</table>

</form>
