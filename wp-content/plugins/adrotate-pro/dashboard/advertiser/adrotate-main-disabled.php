<?php
/*  
Copyright 2010-2014 Arnan de Gans - AJdG Solutions (email : info@ajdg.net)
*/
?>

<h3><?php _e('Disabled Ads', 'adrotate'); ?></h3>
<p><em><?php _e('The ads in here are disabled but kept for reference or later use.', 'adrotate'); ?></em></p>

<table class="widefat" style="margin-top: .5em">
	<thead>
	<tr>
		<th width="2%"><center><?php _e('ID', 'adrotate'); ?></center></th>
		<th><?php _e('Title', 'adrotate'); ?></th>
		<th width="5%"><center><?php _e('Impressions', 'adrotate'); ?></center></th>
		<th width="5%"><center><?php _e('Clicks', 'adrotate'); ?></center></th>
		<th width="5%"><center><?php _e('CTR', 'adrotate'); ?></center></th>
		<th width="15%"><?php _e('Contact publisher', 'adrotate'); ?></th>
	</tr>
	</thead>
	
	<tbody>
<?php
	foreach($disabledbanners as $ad) {
		$stat = adrotate_stats($ad['id']);
		$ctr = adrotate_ctr($stat['clicks'], $stat['impressions']);						

		$wpnonceaction = 'adrotate_email_advertiser_'.$ad['id'];
		$nonce = wp_create_nonce($wpnonceaction);
		
		$class = ('alternate' != $class) ? 'alternate' : '';
		$expiredclass = ($ad['lastactive'] <= $now OR $ad['lastactive'] <= $in2days) ? ' row_error' : '';
?>
	    <tr id='banner-<?php echo $ad['id']; ?>' class='<?php echo $class.$expiredclass; ?>'>
			<td><center><?php echo $ad['id'];?></center></td>
			<td><strong><a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-advertiser&view=edit&ad='.$ad['id']);?>" title="<?php _e('Edit', 'adrotate'); ?>"><?php echo stripslashes(html_entity_decode($ad['title']));?></a></strong></td>
			<td><center><?php echo $stat['impressions']; ?></center></td>
			<td><center><?php echo $stat['clicks']; ?></center></td>
			<td><center><?php echo $ctr; ?> %</center></td>
			<td><a href="admin.php?page=adrotate-advertiser&view=message&request=renew&id=<?php echo $ad['id']; ?>&_wpnonce=<?php echo $nonce; ?>"><?php _e('Renew', 'adrotate'); ?></a> - <a href="admin.php?page=adrotate-advertiser&view=message&request=remove&id=<?php echo $ad['id']; ?>&_wpnonce=<?php echo $nonce; ?>"><?php _e('Remove', 'adrotate'); ?></a> - <a href="admin.php?page=adrotate-advertiser&view=message&request=other&id=<?php echo $ad['id']; ?>&_wpnonce=<?php echo $nonce; ?>"><?php _e('Other', 'adrotate'); ?></a></td>
		</tr>
		<?php } ?>
	</tbody>
</table>