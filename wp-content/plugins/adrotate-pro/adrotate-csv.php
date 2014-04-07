<?php
/*  
Copyright 2010-2014 Arnan de Gans - AJdG Solutions (email : info@ajdg.net)
*/

/*-------------------------------------------------------------
 Name:      adrotate_export_stats

 Purpose:   Export CSV data of given month
 Receive:   -- None --
 Return:    -- None --
 Since:		3.6.11
-------------------------------------------------------------*/
function adrotate_export_stats() {
	global $wpdb;

	if(wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_report_ads') OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_report_blocks') 
	OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_report_groups') OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_report_advertiser') 
	OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_report_global')) {
		$id = $type = $month = $year = '';
		$id	 				= strip_tags(htmlspecialchars(trim($_POST['adrotate_export_id'], "\t\n "), ENT_QUOTES));
		$type	 			= strip_tags(htmlspecialchars(trim($_POST['adrotate_export_type'], "\t\n "), ENT_QUOTES));
		$month	 			= strip_tags(htmlspecialchars(trim($_POST['adrotate_export_month'], "\t\n "), ENT_QUOTES));
		$year	 			= strip_tags(htmlspecialchars(trim($_POST['adrotate_export_year'], "\t\n "), ENT_QUOTES));
	
		$csv_emails = trim($_POST['adrotate_export_addresses']);
		if(strlen($csv_emails) > 0) {
			$csv_emails = explode(',', trim($csv_emails));
			foreach($csv_emails as $csv_email) {
				$csv_email = strip_tags(htmlspecialchars(trim($csv_email), ENT_QUOTES));
				if(strlen($csv_email) > 0) {
						if(preg_match("/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i", $csv_email) ) {
						$clean_advertiser_email[] = $csv_email;
					}
				}
			}
			$emails = array_unique(array_slice($clean_advertiser_email, 0, 3));
		} else {
			$emails = array();
		}
		
		$emailcount = count($emails);
	
		if($month == 0) {
			$from = mktime(0,0,0,1,1,$year);
			$until = mktime(0,0,0,12,31,$year);
		} else {
			$from = mktime(0,0,0,$month,1,$year);
			$until = mktime(0,0,0,$month+1,0,$year);
		}
		$now = time();
		$from_name = date_i18n("M-d-Y", $from);
		$until_name = date_i18n("M-d-Y", $until);
	
		$generated = array("Generated on ".date_i18n("M d Y, H:i"));
	
		if($type == "single" OR $type == "group" OR $type == "block" OR $type == "global") {
			if($type == "single") {
				$ads = $wpdb->get_results($wpdb->prepare("SELECT `thetime`, SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `".$wpdb->prefix."adrotate_stats` WHERE (`thetime` >= '".$from."' AND `thetime` <= '".$until."') AND `ad` = %d GROUP BY `thetime` ASC;", $id), ARRAY_A);
				$title = $wpdb->get_var($wpdb->prepare("SELECT `title` FROM `".$wpdb->prefix."adrotate` WHERE `id` = %d;", $id));
		
				$filename = "Single-ad ID".$id." - ".$from_name." to ".$until_name." - exported ".$now.".csv";
				$topic = array("Report for ad '".$title."'");
				$period = array("Period - From: ".$from_name." Until: ".$until_name);
				$keys = array("Day", "Clicks", "Impressions");
			}
		
			if($type == "group") {
				$ads = $wpdb->get_results($wpdb->prepare("SELECT `thetime`, SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `".$wpdb->prefix."adrotate_stats` WHERE (`thetime` >= '".$from."' AND `thetime` <= '".$until."') AND  `group` = %d GROUP BY `thetime` ASC;", $id), ARRAY_A);
				$title = $wpdb->get_var($wpdb->prepare("SELECT `name` FROM `".$wpdb->prefix."adrotate_groups` WHERE `id` = %d;", $id));
		
				$filename = "Ad Group ID".$id." - ".$from_name." to ".$until_name." - exported ".$now.".csv";
				$topic = array("Report for group '".$title."'");
				$period = array("Period - From: ".$from_name." Until: ".$until_name);
				$keys = array("Day", "Clicks", "Impressions");
			}
		
			if($type == "block") {
				$ads = $wpdb->get_results($wpdb->prepare("SELECT `thetime`, SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `".$wpdb->prefix."adrotate_stats` WHERE (`thetime` >= '".$from."' AND `thetime` <= '".$until."') AND  `block` = %d GROUP BY `thetime` ASC;", $id), ARRAY_A);
				$title = $wpdb->get_var($wpdb->prepare("SELECT `name` FROM `".$wpdb->prefix."adrotate_blocks` WHERE `id` = %d;", $id));
		
				$filename = "Ad Block ID".$id." - ".$from_name." to ".$until_name." - exported ".$now.".csv";
				$topic = array("Report for ad '".$title."'");
				$period = array("Period - From: ".$from_name." Until: ".$until_name);
				$keys = array("Day", "Clicks", "Impressions");
			}
			
			if($type == "global") {
				$ads = $wpdb->get_results($wpdb->prepare("SELECT `thetime`, SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `".$wpdb->prefix."adrotate_stats` WHERE `thetime` >= %d AND `thetime` <= %d GROUP BY `thetime` ASC;", $from, $until), ARRAY_A);
		
				$filename = "Global report - ".$from_name." to ".$until_name." - exported ".$now.".csv";
				$topic = array("Global report");
				$period = array("Period - From: ".$from_name." Until: ".$until_name);
				$keys = array("Day", "Clicks", "Impressions");
			}
	
			$x=0;
			foreach($ads as $ad) {
				// Prevent gaps in display
				if($ad['impressions'] == 0) $ad['impressions'] = 0;
				if($ad['clicks'] == 0) $ad['clicks'] = 0;
		
				// Build array
				$adstats[$x]['day']	= date_i18n("M d Y", $ad['thetime']);
				$adstats[$x]['clicks'] = $ad['clicks'];
				$adstats[$x]['impressions'] = $ad['impressions'];
				$x++;
			}
		}
	
		if($type == "advertiser") {
			$ads = $wpdb->get_results($wpdb->prepare("SELECT `ad` FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `group` = 0 AND `block` = 0 AND `user` = %d ORDER BY `ad` ASC;", $id));

			$x=0;
			foreach($ads as $ad) {
				$title = $wpdb->get_var("SELECT `title` FROM `".$wpdb->prefix."adrotate` WHERE `id` = '".$ad->ad."';");
				$startshow = $wpdb->get_var("SELECT `starttime` FROM `".$wpdb->prefix."adrotate_schedule` WHERE `ad` = '".$ad->ad."' ORDER BY `starttime` ASC LIMIT 1;");
				$endshow = $wpdb->get_var("SELECT `stoptime` FROM `".$wpdb->prefix."adrotate_schedule` WHERE `ad` = '".$ad->ad."' ORDER BY `stoptime` DESC LIMIT 1;");
				$username = $wpdb->get_var($wpdb->prepare("SELECT `display_name` FROM `$wpdb->users` WHERE `ID` = %d ORDER BY `user_nicename` ASC;", $id));
				$stat = adrotate_stats($ad->ad);
				
				// Prevent gaps in display
				if($stat['impressions'] == 0 AND $stat['clicks'] == 0) {
					$ctr = "0";
				} else {
					$ctr = round((100/$stat['impressions']) * $stat['clicks'],2);
				}
	
				// Build array
				$adstats[$x]['title']					= $title;			
				$adstats[$x]['id']						= $ad->ad;			
				$adstats[$x]['startshow']				= date_i18n("M d Y", $startshow);
				$adstats[$x]['endshow']					= date_i18n("M d Y", $endshow);
				$adstats[$x]['clicks']					= $stat['clicks'];
				$adstats[$x]['impressions']				= $stat['impressions'];
				$adstats[$x]['ctr']						= $ctr;
				$x++;
			}
			
			$filename = "Advertiser - ".$username." - export.csv";
			$topic = array("Advertiser report for ".$username);
			$period = array("Period - Not Applicable");
			$keys = array("Title", "Ad ID", "First visibility", "Last visibility", "Clicks", "Impressions", "CTR (%)");
		}
	
	 	if($adstats) {
			if($emailcount > 0) {
				if(!file_exists(WP_CONTENT_DIR . '/reports/')) mkdir(WP_CONTENT_DIR . '/reports/', 0755);
				$fp = fopen(WP_CONTENT_DIR . '/reports/'.$filename, 'w');
			} else {
				header('Content-Type: text/csv');
				header('Content-Disposition: attachment;filename='.$filename);
				$fp = fopen('php://output', 'w');
			}
			
			if($fp) {
				fputcsv($fp, $topic);
				fputcsv($fp, $period);
				fputcsv($fp, $generated);
				fputcsv($fp, $keys);
				foreach($adstats as $stat) {
					fputcsv($fp, $stat);
				}
				
				fclose($fp);

				if($emailcount > 0) {
					$y = count($emails);
		
					$attachments = array(WP_CONTENT_DIR . '/reports/'.$filename);
				 	$siteurl 	= get_option('siteurl');
					$pluginurl	= "http://www.adrotateplugin.com";
					$email 		= get_option('admin_email');
		
				    $headers = "MIME-Version: 1.0\n" .
		      				 	"From: AdRotate Plugin <".$email.">\r\n\n" . 
		      				  	"Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n";
		
					$subject = __('[AdRotate] CSV Report!', 'adrotate');
					
					$message = 	"<p>".__('Hello', 'adrotate').",</p>";
					$message .= "<p>".__('Attached in this email you will find the exported CSV file you generated on ', 'adrotate')." $siteurl.</p>";
					$message .= "<p>".__('Have a nice day!', 'adrotate')."<br />";
					$message .= __('Your AdRotate Notifier', 'adrotate')."<br />";
					$message .= "$pluginurl</p>";
			
					for($i=0;$i<$emailcount;$i++) {
			 			wp_mail($emails[$i], $subject, $message, $headers, $attachments);
			 		}	
	
					if($type == "single")
						adrotate_return('adrotate-ads', 303, array('view' => 'report', 'ad' => $id));
					if($type == "group")
						adrotate_return('adrotate-groups', 303, array('view' => 'report', 'group' => $id));
					if($type == "block")
						adrotate_return('adrotate-blocks', 303, array('view' => 'report', 'block' => $id));
					if($type == "global")
						adrotate_return('adrotate-global-report', 303);
					if($type == "advertiser")
						adrotate_return('adrotate-advertiser', 303, array('view' => 'report', 'ad' => $id));
				} else {
					// for some reason, downloading an attachment requires this exit;
					exit;
				}
			}
		} else {
			if($type == "single")
				adrotate_return('adrotate-ads', 503, array('view' => 'report', 'ad' => $id));
			if($type == "group")
				adrotate_return('adrotate-groups', 503, array('view' => 'report', 'group' => $id));
			if($type == "block")
				adrotate_return('adrotate-blocks', 503, array('view' => 'report', 'block' => $id));
			if($type == "global")
				adrotate_return('adrotate-global-report', 503);
			if($type == "advertiser")
				adrotate_return('adrotate-advertiser', 503);
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_export_ads

 Purpose:   Export CSV data
 Receive:   -- None --
 Return:    -- None --
 Since:		3.8.5
-------------------------------------------------------------*/
function adrotate_export_ads($ids) {
	global $wpdb;

	$all_ads = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."adrotate` ORDER BY `id` ASC;", ARRAY_A);

	$ads = array();
	foreach($all_ads as $single) {
		if(in_array($single['id'], $ids)) {
			$ads[$single['id']] = array(
				'id' => $single['id'],
				'title' => $single['title'],
				'bannercode' => stripslashes(htmlspecialchars_decode($single['bannercode'], ENT_QUOTES)),
				'imagetype' => $single['imagetype'],
				'image' => $single['image'],
				'link' => $single['link'],
				'tracker' => $single['tracker'],
				'timeframe' => $single['timeframe'],
				'timeframelength' => $single['timeframelength'],
				'timeframeclicks' => $single['timeframeclicks'],
				'timeframeimpressions' => $single['timeframeimpressions'],
				'weight' => $single['weight'],
				'cbudget' => $single['cbudget'],
				'ibudget' => $single['ibudget'],
				'crate' => $single['crate'],
				'irate' => $single['irate']
			);
		}
	}

 	if($ads) {
		$filename = "AdRotate_export_".date_i18n("mdYHi").".csv";
		$keys = array("id", "title", "bannercode", "imagetype", "image", "link", "tracker", "timeframe", "timeframelength", "timeframeclicks", "timeframeimpressions", "weight", "cbudget", "ibudget", "crate", "irate");

		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename='.$filename);
		$fp = fopen(WP_CONTENT_DIR . '/reports/'.$filename, 'w');

		if($fp) {
			fputcsv($fp, $keys, ',', '"');
			foreach($ads as $ad) {
				fputcsv($fp, $ad, ',', '"');
			}
			fclose($fp);
		}
			
		adrotate_return('adrotate-ads', 215, array('file' => $filename));
		exit;
	} else {
		adrotate_return('adrotate-ads', 509);
	}
}

/*-------------------------------------------------------------
 Name:      rewards_import_credits

 Purpose:   Import credit from CSV file
 Receive:   -None-
 Return:	-None-
 Since:		0.1 
-------------------------------------------------------------*/
function adrotate_import_ads() {
	global $wpdb, $userdata, $current_user;

	$now = current_time('timestamp');

	if(current_user_can('adrotate_ad_manage')) {	
		if($_FILES["adrotate_csv"]["error"] == 4) {
			adrotate_return('adrotate-ads', 506, array('view' => 'import'));
			exit;
		} else {
			if(($_FILES["adrotate_csv"]["type"] == "text/comma-separated-values" OR $_FILES["adrotate_csv"]["type"] == "text/anytext" OR $_FILES["adrotate_csv"]["type"] == "text/csv" OR $_FILES["adrotate_csv"]["type"] == "application/csv" OR $_FILES["adrotate_csv"]["type"] == "application/excel" OR $_FILES["adrotate_csv"]["type"] == "application/vnd.ms-excel" OR $_FILES["adrotate_csv"]["type"] == "application/vnd.msexcel") AND ($_FILES["adrotate_csv"]["size"] < 4096000)) {
				if ($_FILES["adrotate_csv"]["error"] > 0) {
					adrotate_return('adrotate-ads', 507, array('view' => 'import'));
					exit;
				} else {
					$csv_name = "adrotate_import_".date_i18n("mdYHi", $now).".csv";
					move_uploaded_file($_FILES["adrotate_csv"]["tmp_name"], WP_CONTENT_DIR."/reports/".$csv_name);
					$file = WP_CONTENT_URL."/reports/".$csv_name;
				}
			} else {
				adrotate_return('adrotate-ads', 508, array('view' => 'import'));
				exit;
			}
	
			ini_set("auto_detect_line_endings", true);

			if(($handle = fopen($file, "r")) !== FALSE) {
				while(($data = fgetcsv($handle, 0, ',', '"')) !== false) {
					$import[] = $data;
				}
				fclose($handle);
				unset($import[0]);

				foreach($import as $row) {					
					$ad = array(
						'title' => strip_tags(htmlspecialchars(trim($row[1], "\t\n "), ENT_QUOTES)),
						'bannercode' => htmlspecialchars(trim($row[2], "\t\n "), ENT_QUOTES),
						'thetime' => $now,
						'updated' => $now,
						'author' => $current_user->user_login,
						'imagetype' => strip_tags(trim($row[3], "\t\n ")),
						'image' => strip_tags(trim($row[4], "\t\n ")),
						'link' => strip_tags(trim($row[5], "\t\n ")),
						'tracker' => strip_tags(trim($row[6], "\t\n ")),
						'timeframe' => strip_tags(trim($row[7], "\t\n ")),
						'timeframelength' => strip_tags(trim($row[8], "\t\n ")),
						'timeframeclicks' => strip_tags(trim($row[9], "\t\n ")),
						'timeframeimpressions' => strip_tags(trim($row[10], "\t\n ")),
						'type' => 'import',
						'weight' => $row[11],
						'cbudget' => strip_tags(trim($row[12], "\t\n ")),
						'ibudget' => strip_tags(trim($row[13], "\t\n ")),					
						'crate' => strip_tags(trim($row[14], "\t\n ")),
						'irate' => strip_tags(trim($row[15], "\t\n ")),
						'sortorder' => 0
					);
				    $wpdb->insert($wpdb->prefix."adrotate", $ad);

					$schedule = array(
						'ad' => $wpdb->insert_id, 
						'starttime' => $now, 
						'stoptime' => $now + 7257600, 
						'maxclicks' => 0, 
						'maximpressions' => 0
					);
					$wpdb->insert($wpdb->prefix.'adrotate_schedule', $schedule);

					
					unset($row, $ad, $schedule);
				}
				
				adrotate_prepare_evaluate_ads(false);
				unset($columns, $import);

				// return to dashboard
				adrotate_return('adrotate-ads', 216);
				exit;
			}
		}
	} else {
		adrotate_return('adrotate-ads', 500);
	}
}
?>