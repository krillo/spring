<?php
/*  
Copyright 2010-2014 Arnan de Gans - AJdG Solutions (email : info@ajdg.net)
*/

/*-------------------------------------------------------------
 Name:      adrotate_insert_input

 Purpose:   Prepare input form on saving new or updated banners
 Receive:   -None-
 Return:	-None-
 Since:		0.1 
-------------------------------------------------------------*/
function adrotate_insert_input() {
	global $wpdb, $adrotate_config;

	if(wp_verify_nonce($_POST['adrotate_nonce'], 'adrotate_save_ad')) {
		// Mandatory
		$id = $author = $title = $bannercode = $active = $sortorder = '';
		if(isset($_POST['adrotate_id'])) $id = $_POST['adrotate_id'];
		if(isset($_POST['adrotate_username'])) $author = $_POST['adrotate_username'];
		if(isset($_POST['adrotate_title'])) $title = strip_tags(htmlspecialchars(trim($_POST['adrotate_title'], "\t\n "), ENT_QUOTES));
		if(isset($_POST['adrotate_bannercode'])) $bannercode = htmlspecialchars(trim($_POST['adrotate_bannercode'], "\t\n "), ENT_QUOTES);
		$thetime = adrotate_now();
		if(isset($_POST['adrotate_active'])) $active = strip_tags(htmlspecialchars(trim($_POST['adrotate_active'], "\t\n "), ENT_QUOTES));
		if(isset($_POST['adrotate_sortorder'])) $sortorder = strip_tags(htmlspecialchars(trim($_POST['adrotate_sortorder'], "\t\n "), ENT_QUOTES));

		// Schedule variables
		$sday = $smonth = $syear = $shour = $sminute = '';
		if(isset($_POST['adrotate_sday'])) $sday = strip_tags(trim($_POST['adrotate_sday'], "\t\n "));
		if(isset($_POST['adrotate_smonth'])) $smonth = strip_tags(trim($_POST['adrotate_smonth'], "\t\n "));
		if(isset($_POST['adrotate_syear'])) $syear = strip_tags(trim($_POST['adrotate_syear'], "\t\n "));
		if(isset($_POST['adrotate_shour'])) $shour = strip_tags(trim($_POST['adrotate_shour'], "\t\n "));
		if(isset($_POST['adrotate_sminute'])) $sminute = strip_tags(trim($_POST['adrotate_sminute'], "\t\n "));

		$eday = $emonth = $eyear = $ehour = $eminute = '';
		if(isset($_POST['adrotate_eday'])) $eday = strip_tags(trim($_POST['adrotate_eday'], "\t\n "));
		if(isset($_POST['adrotate_emonth'])) $emonth = strip_tags(trim($_POST['adrotate_emonth'], "\t\n "));
		if(isset($_POST['adrotate_eyear'])) $eyear = strip_tags(trim($_POST['adrotate_eyear'], "\t\n "));
		if(isset($_POST['adrotate_ehour'])) $ehour = strip_tags(trim($_POST['adrotate_ehour'], "\t\n "));
		if(isset($_POST['adrotate_eminute'])) $eminute = strip_tags(trim($_POST['adrotate_eminute'], "\t\n "));
	
		$maxclicks = $maxshown = '';
		if(isset($_POST['adrotate_maxclicks'])) $maxclicks = strip_tags(trim($_POST['adrotate_maxclicks'], "\t\n "));
		if(isset($_POST['adrotate_maxshown'])) $maxshown = strip_tags(trim($_POST['adrotate_maxshown'], "\t\n "));	
	
		// Schedule and timeframe variables
		$schedules = $timeframe = $timeframelength = $timeframeclicks = $timeframeimpressions = '';
		if(isset($_POST['scheduleselect'])) $schedules = $_POST['scheduleselect'];
		if(isset($_POST['adrotate_timeframe'])) $timeframe = strip_tags(trim($_POST['adrotate_timeframe'], "\t\n "));
		if(isset($_POST['adrotate_timeframelength'])) $timeframelength = strip_tags(trim($_POST['adrotate_timeframelength'], "\t\n "));
		if(isset($_POST['adrotate_timeframeclicks'])) $timeframeclicks = strip_tags(trim($_POST['adrotate_timeframeclicks'], "\t\n "));
		if(isset($_POST['adrotate_timeframeimpressions'])) $timeframeimpressions = strip_tags(trim($_POST['adrotate_timeframeimpressions'], "\t\n "));

		// Advanced options
		$image_field = $image_dropdown = $link = $tracker = $targetclicks = $targetimpressions = '';
		if(isset($_POST['adrotate_image'])) $image_field = strip_tags(trim($_POST['adrotate_image'], "\t\n "));
		if(isset($_POST['adrotate_image_dropdown'])) $image_dropdown = strip_tags(trim($_POST['adrotate_image_dropdown'], "\t\n "));
		if(isset($_POST['adrotate_link'])) $link = strip_tags(trim($_POST['adrotate_link'], "\t\n "));
		if(isset($_POST['adrotate_tracker'])) $tracker = strip_tags(trim($_POST['adrotate_tracker'], "\t\n "));
		if(isset($_POST['adrotate_targetclicks'])) $targetclicks = strip_tags(trim($_POST['adrotate_targetclicks'], "\t\n "));
		if(isset($_POST['adrotate_targetimpressions'])) $targetimpressions = strip_tags(trim($_POST['adrotate_targetimpressions'], "\t\n "));
	
		// GeoTargeting
		$cities = '';
		$countries = array();
		if(isset($_POST['adrotate_geo_cities'])) $cities = trim($_POST['adrotate_geo_cities'], "\t\n ");
		if(isset($_POST['adrotate_geo_countries'])) $countries = $_POST['adrotate_geo_countries'];
	
		// advertiser
		$advertiser = $crate = $irate = $cbudget = $ibudget = '';
		if(isset($_POST['adrotate_advertiser'])) $advertiser = $_POST['adrotate_advertiser'];
		if(isset($_POST['adrotate_crate'])) $crate = strip_tags(trim($_POST['adrotate_crate'], "\t\n "));
		if(isset($_POST['adrotate_irate'])) $irate = strip_tags(trim($_POST['adrotate_irate'], "\t\n "));
		if(isset($_POST['adrotate_cbudget'])) $cbudget = strip_tags(trim($_POST['adrotate_cbudget'], "\t\n "));
		if(isset($_POST['adrotate_ibudget'])) $ibudget = strip_tags(trim($_POST['adrotate_ibudget'], "\t\n "));
		
		// Misc variables
		$groups = $type = $weight = $group_array = '';
		if(isset($_POST['groupselect'])) $groups = $_POST['groupselect'];
		if(isset($_POST['adrotate_type'])) $type = strip_tags(trim($_POST['adrotate_type'], "\t\n "));
		if(isset($_POST['adrotate_weight'])) $weight = $_POST['adrotate_weight'];
	
	
		if(current_user_can('adrotate_ad_manage')) {
			if(strlen($title) < 1) {
				$title = 'Ad '.$id;
			}
	
			// Determine if timeframe is valid
			if(($timeframeclicks == 0 OR $timeframeclicks == '') AND ($timeframeimpressions == 0 OR $timeframeimpressions == '') AND ($timeframelength == 0 OR $timeframelength == '')) $timeframe = ''; // disabled if 0
			if($timeframe == '') $timeframeclicks = $timeframeimpressions = $timeframelength = 0; // disabled if empty
	
			// Validate sort order
			if(strlen($sortorder) < 1 OR !is_numeric($sortorder) AND ($sortorder < 1 OR $sortorder > 99999)) $sortorder = 0;
	
			// Sort out start dates
			if(strlen($smonth) > 0 AND !is_numeric($smonth)) 	$smonth 	= date_i18n('m');
			if(strlen($sday) > 0 AND !is_numeric($sday)) 		$sday 		= date_i18n('d');
			if(strlen($syear) > 0 AND !is_numeric($syear)) 		$syear 		= date_i18n('Y');
			if(strlen($shour) > 0 AND !is_numeric($shour)) 		$shour 		= date_i18n('H');
			if(strlen($sminute) > 0 AND !is_numeric($sminute))	$sminute	= date_i18n('i');
			if(($smonth > 0 AND $sday > 0 AND $syear > 0) AND strlen($shour) == 0) $shour = '00';
			if(($smonth > 0 AND $sday > 0 AND $syear > 0) AND strlen($sminute) == 0) $sminute = '00';
	
			if($smonth > 0 AND $sday > 0 AND $syear > 0) {
				$startdate = mktime($shour, $sminute, 0, $smonth, $sday, $syear);
			} else {
				$startdate = 0;
			}
			
			// Sort out end dates
			if(strlen($emonth) > 0 AND !is_numeric($emonth)) 	$emonth 	= $smonth;
			if(strlen($eday) > 0 AND !is_numeric($eday)) 		$eday 		= $sday;
			if(strlen($eyear) > 0 AND !is_numeric($eyear)) 		$eyear 		= $syear+1;
			if(strlen($ehour) > 0 AND !is_numeric($ehour)) 		$ehour 		= $shour;
			if(strlen($eminute) > 0 AND !is_numeric($eminute)) 	$eminute	= $sminute;
			if(($emonth > 0 AND $eday > 0 AND $eyear > 0) AND strlen($ehour) == 0) $ehour = '00';
			if(($emonth > 0 AND $eday > 0 AND $eyear > 0) AND strlen($eminute) == 0) $eminute = '00';
	
			if($emonth > 0 AND $eday > 0 AND $eyear > 0) {
				$enddate = mktime($ehour, $eminute, 0, $emonth, $eday, $eyear);
			} else {
				$enddate = 0;
			}
			
			// Enddate is too early, reset to default
			if($enddate <= $startdate) $enddate = $startdate + 7257600; // 84 days (12 weeks)
	
			// Sort out click and impressions restrictions
			if(strlen($maxclicks) < 1 OR !is_numeric($maxclicks)) $maxclicks = 0;
			if(strlen($maxshown) < 1 OR !is_numeric($maxshown))	$maxshown = 0;
	
			// Save the schedule to the DB
			if($startdate > 0 AND $enddate > 0) {
				$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => 'Schedule for ad '.$id, 'starttime' => $startdate, 'stoptime' => $enddate, 'maxclicks' => $maxclicks, 'maximpressions' => $maxshown));
				$schedules[] = $wpdb->insert_id;
			}

			// Format the targets for timeframes
			if(strlen($targetclicks) < 1 OR !is_numeric($targetclicks))	$targetclicks	= 0;
			if(strlen($targetimpressions) < 1 OR !is_numeric($targetimpressions)) $targetimpressions	= 0;
	
			// Set tracker value
			if(isset($tracker) AND strlen($tracker) != 0) $tracker = 'Y';
				else $tracker = 'N';
	
			// Format the URL (assume http://)
			if((strlen($link) > 0 OR $link != "") AND stristr($link, "http://") === false AND stristr($link, "https://") === false) $link = "http://".$link;
			
			// Rate and Budget settings
			if(strlen($crate) == 0 OR $crate == "" OR !is_numeric($crate) OR $crate < 0 OR $crate > 100) $crate = $cbudget = 0;
			if(strlen($irate) == 0 OR $irate == "" OR !is_numeric($irate) OR $irate < 0 OR $irate > 100) $irate = $ibudget = 0;
			
			// Determine image settings ($image_field has priority!)
			if(strlen($image_field) > 1) {
				$imagetype = "field";
				$image = $image_field;
			} else if(strlen($image_dropdown) > 1) {
				$imagetype = "dropdown";
				$image = home_url()."/%folder%".$image_dropdown;
			} else {
				$imagetype = "";
				$image = "";
			}
	
			// Geo Targeting
			if(strlen($cities) > 0) {
				$cities = explode(",", strtolower($cities));
				foreach($cities as $key => $value) {
					$cities_clean[] = trim($value);
					unset($value);
				}
				unset($cities);
				$cities = serialize($cities_clean);
			}

			if(count($countries) == 0) {
				$countries = '';
			} else {
				foreach($countries as $key => $value) {
					$countries_clean[] = trim($value);
					unset($value);
				}
				unset($countries);
				$countries = serialize($countries_clean);
			}

			// Save the ad to the DB
			$wpdb->update($wpdb->prefix.'adrotate', array('title' => $title, 'bannercode' => $bannercode, 'updated' => $thetime, 'author' => $author, 'imagetype' => $imagetype, 'image' => $image, 'link' => $link, 'tracker' => $tracker, 'timeframe' => $timeframe, 'timeframelength' => $timeframelength, 'timeframeclicks' => $timeframeclicks, 'timeframeimpressions' => $timeframeimpressions, 'weight' => $weight, 'sortorder' => $sortorder, 'cbudget' => $cbudget, 'ibudget' => $ibudget, 'crate' => $crate, 'irate' => $irate, 'cities' => $cities, 'countries' => $countries), array('id' => $id));

			$action = 200;
			if($active == "active") {
				// Determine status of ad 
				$adstate = adrotate_evaluate_ad($id);
				if($adstate == 'error' OR $adstate == 'expired' OR $adstate == 'expiring') {
					$action = 501;
				}
				$active = $adstate;
			}
		    $wpdb->update($wpdb->prefix."adrotate", array('type' => $active), array('id' => $id));
	
			// Fetch group records for the ad
			$groupmeta = $wpdb->get_results($wpdb->prepare("SELECT `group` FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = %d AND `block` = 0 AND `user` = 0 AND `schedule` = 0;", $id));
			foreach($groupmeta as $meta) {
				$group_array[] = $meta->group;
				unset($meta);
			}
			
			if(!is_array($group_array)) $group_array = array();
			if(!is_array($groups)) $groups = array();

			// Add new groups to this ad
			$insert = array_diff($groups, $group_array);
			foreach($insert as &$value) {
				$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $id, 'group' => $value, 'block' => 0, 'user' => 0, 'schedule' => 0));
			}
			unset($insert, $value);
			
			// Remove groups from this ad
			$delete = array_diff($group_array, $groups);
			foreach($delete as &$value) {
				$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = %d AND `group` = %d AND `block` = 0 AND `user` = 0 AND `schedule` = 0;", $id, $value)); 
			}
			unset($delete, $value, $groupmeta, $group_array);
	
			// Fetch schedules for the ad
			$schedulemeta = $wpdb->get_results($wpdb->prepare("SELECT `schedule` FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = %d AND `group` = 0 AND `block` = 0 AND `user` = 0;", $id));
			foreach($schedulemeta as $meta) {
				$schedule_array[] = $meta->schedule;
				unset($meta);
			}
			
			if(!is_array($schedule_array)) $schedule_array = array();
			if(!is_array($schedules)) $schedules = array();
			
			// Add new schedules to this ad
			$insert = array_diff($schedules, $schedule_array);
			foreach($insert as &$value) {
				$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $id, 'group' => 0, 'block' => 0, 'user' => 0, 'schedule' => $value));
			}
			unset($insert, $value);
			
			// Remove schedules from this ad
			$delete = array_diff($schedule_array, $schedules);
			foreach($delete as &$value) {
				$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = %d AND `group` = 0 AND `block` = 0 AND `user` = 0 AND `schedule` = %d;", $id, $value)); 
			}
			unset($delete, $value, $schedulemeta, $schedule_array);

			// Fetch records for the ad, see if a publisher is set
			$linkmeta = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = %d AND `group` = 0 AND `block` = 0 AND `user` > 0 AND `schedule` = 0;", $id));
	
			// Add/update/remove publisher on this ad
			if($linkmeta == 0 AND $advertiser > 0) $wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $id, 'group' => 0, 'block' => 0, 'user' => $advertiser, 'schedule' => 0));
			if($linkmeta == 1 AND $advertiser > 0) $wpdb->query($wpdb->prepare("UPDATE `".$wpdb->prefix."adrotate_linkmeta` SET `user` = $advertiser WHERE `ad` = %d AND `group` = 0 AND `block` = 0 AND `schedule` = 0;", $id)); 
			if($linkmeta == 1 AND $advertiser == 0) $wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = %d AND `group` = 0 AND `block` = 0 AND `schedule` = 0;", $id)); 

			// Verify all ads
			adrotate_prepare_evaluate_ads(false);

			adrotate_return('adrotate-ads', $action);
			exit;
		} else {
			adrotate_return('adrotate-ads', 500);
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_insert_group

 Purpose:   Save provided data for groups, update linkmeta where required
 Receive:   -None-
 Return:	-None-
 Since:		0.4
-------------------------------------------------------------*/
function adrotate_insert_group() {
	global $wpdb, $adrotate_config;

	if(wp_verify_nonce($_POST['adrotate_nonce'], 'adrotate_save_group')) {
		$action = $id = $name = $modus = '';
		if(isset($_POST['adrotate_action'])) $action = $_POST['adrotate_action'];
		if(isset($_POST['adrotate_id'])) $id = $_POST['adrotate_id'];
		if(isset($_POST['adrotate_groupname'])) $name = strip_tags(trim($_POST['adrotate_groupname'], "\t\n "));
		if(isset($_POST['adrotate_modus'])) $modus = strip_tags(trim($_POST['adrotate_modus'], "\t\n "));

		$rows = $columns = $adwidth = $adheight = $admargin = $adspeed = '';
		if(isset($_POST['adrotate_gridrows'])) $rows = strip_tags(trim($_POST['adrotate_gridrows'], "\t\n "));
		if(isset($_POST['adrotate_gridcolumns'])) $columns = strip_tags(trim($_POST['adrotate_gridcolumns'], "\t\n "));
		if(isset($_POST['adrotate_adwidth'])) $adwidth = strip_tags(trim($_POST['adrotate_adwidth'], "\t\n "));
		if(isset($_POST['adrotate_adheight'])) $adheight = strip_tags(trim($_POST['adrotate_adheight'], "\t\n "));
		if(isset($_POST['adrotate_admargin_top'])) $admargin_top = strip_tags(trim($_POST['adrotate_admargin_top'], "\t\n "));
		if(isset($_POST['adrotate_admargin_bottom'])) $admargin_bottom = strip_tags(trim($_POST['adrotate_admargin_bottom'], "\t\n "));
		if(isset($_POST['adrotate_admargin_left'])) $admargin_left = strip_tags(trim($_POST['adrotate_admargin_left'], "\t\n "));
		if(isset($_POST['adrotate_admargin_right'])) $admargin_right = strip_tags(trim($_POST['adrotate_admargin_right'], "\t\n "));
		if(isset($_POST['adrotate_adspeed'])) $adspeed = strip_tags(trim($_POST['adrotate_adspeed'], "\t\n "));

		$fallback = $ads = $sortorder = '';
		if(isset($_POST['adrotate_fallback'])) $fallback = $_POST['adrotate_fallback'];
		if(isset($_POST['adselect'])) $ads = $_POST['adselect'];
		if(isset($_POST['adrotate_sortorder'])) $sortorder = strip_tags(htmlspecialchars(trim($_POST['adrotate_sortorder'], "\t\n "), ENT_QUOTES));

		$categories = $category_loc = $pages = $page_loc = '';
		if(isset($_POST['adrotate_categories'])) $categories = $_POST['adrotate_categories'];
		if(isset($_POST['adrotate_cat_location'])) $category_loc = $_POST['adrotate_cat_location'];
		if(isset($_POST['adrotate_pages'])) $pages = $_POST['adrotate_pages'];
		if(isset($_POST['adrotate_page_location'])) $page_loc = $_POST['adrotate_page_location'];

		$geo = 0;
		if(isset($_POST['adrotate_geo'])) $geo = 1; 
			else $geo = 0;

		$wrapper_before = $wrapper_after = '';
		if(isset($_POST['adrotate_wrapper_before'])) $wrapper_before = trim($_POST['adrotate_wrapper_before'], "\t\n ");
		if(isset($_POST['adrotate_wrapper_after'])) $wrapper_after = trim($_POST['adrotate_wrapper_after'], "\t\n ");
	
		if(current_user_can('adrotate_group_manage')) {
			if(strlen($name) < 1) $name = 'Group '.$id;

			if($modus < 0 OR $modus > 2) $modus = 0;
			if($adspeed < 0 OR $adspeed > 99999) $adspeed = 6000;
			
			// Sort out block shape
			if($rows < 1 OR $rows == '' OR !is_numeric($rows)) $rows = 2;
			if($columns < 1 OR $columns == '' OR !is_numeric($columns)) $columns = 2;
			if((is_numeric($adwidth) AND $adwidth < 1 OR $adwidth > 9999) OR $adwidth == '' OR (!is_numeric($adwidth) AND $adwidth != 'auto')) $adheight = '125';
			if((is_numeric($adheight) AND $adheight < 1 OR $adheight > 9999) OR $adheight == '' OR (!is_numeric($adheight) AND $adheight != 'auto')) $adheight = '125';
			if($admargin_top < 0 OR $admargin_top > 99 OR $admargin_top == '' OR !is_numeric($admargin_top)) $admargin_top = 0;
			if($admargin_bottom < 0 OR $admargin_bottom > 99 OR $admargin_bottom == '' OR !is_numeric($admargin_bottom)) $admargin_bottom = 0;
			if($admargin_left < 0 OR $admargin_left > 99 OR $admargin_left == '' OR !is_numeric($admargin_left)) $admargin_left = 0;
			if($admargin_right < 0 OR $admargin_right > 99 OR $admargin_right == '' OR !is_numeric($admargin_right)) $admargin_right = 0;

			// Validate sort order
			if(strlen($sortorder) < 1 OR !is_numeric($sortorder) AND ($sortorder < 1 OR $sortorder > 99999)) $sortorder = $id;
	
			// Categories
			if(!is_array($categories)) $categories = array();
			$category = '';
			foreach($categories as $key => $value) {
				$category = $category.','.$value;
			}
			$category = trim($category, ',');
			if(strlen($category) < 1) $category = '';
			if($category_loc < 0 OR $category_loc > 3) $category_loc = 0;
	
			// Pages
			if(!is_array($pages)) $pages = array();
			$page = '';
			foreach($pages as $key => $value) {
				$page = $page.','.$value;
			}
			$page = trim($page, ',');
			if(strlen($page) < 1) $page = '';
			if($page_loc < 0 OR $page_loc > 3) $page_loc = 0;

			if(empty($meta_array)) $meta_array = array();
			if(empty($ads)) $ads = array();
	
			// Fetch records for the group
			$linkmeta = $wpdb->get_results($wpdb->prepare("SELECT `ad` FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `group` = %d AND `block` = 0 AND `user` = 0;", $id));
			foreach($linkmeta as $meta) {
				$meta_array[] = $meta->ad;
			}
			
			// Add new ads to this group
			$insert = array_diff($ads,$meta_array);
			foreach($insert as &$value) {
				$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $value, 'group' => $id, 'block' => 0, 'user' => 0));
			}
			unset($value);
			
			// Remove ads from this group
			$delete = array_diff($meta_array,$ads);
			foreach($delete as &$value) {
				$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = %d AND `group` = %d AND `block` = 0 AND `user` = 0;", $value, $id)); 
			}
			unset($value);
	
			// Update the group itself
			$wpdb->update($wpdb->prefix.'adrotate_groups', array('name' => $name, 'modus' => $modus, 'fallback' => $fallback, 'sortorder' => $sortorder, 'cat' => $category, 'cat_loc' => $category_loc, 'page' => $page, 'page_loc' => $page_loc, 'geo' => $geo, 'wrapper_before' => $wrapper_before, 'wrapper_after' => $wrapper_after, 'gridrows' => $rows, 'gridcolumns' => $columns, 'admargin' => $admargin_top, 'admargin_bottom' => $admargin_bottom, 'admargin_left' => $admargin_left, 'admargin_right' => $admargin_right, 'adwidth' => $adwidth, 'adheight' => $adheight, 'adspeed' => $adspeed), array('id' => $id));

			$geo_count = $wpdb->get_var("SELECT COUNT(*) as `total` FROM `".$wpdb->prefix."adrotate_groups` WHERE `name` != '' AND `geo` = 1;");
			update_option('adrotate_geo_required', $geo_count);

			adrotate_return('adrotate-groups', 201);
			exit;
		} else {
			adrotate_return('adrotate-groups', 500);
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_insert_schedule

 Purpose:   Prepare input form on saving new or updated schedules
 Receive:   -None-
 Return:	-None-
 Since:		3.8.9 
-------------------------------------------------------------*/
function adrotate_insert_schedule() {
	global $wpdb;

	if(wp_verify_nonce($_POST['adrotate_nonce'], 'adrotate_save_schedule')) {
		// Mandatory
		$id = $ad = '';
		if(isset($_POST['adrotate_id'])) $id = esc_attr($_POST['adrotate_id']);
		if(isset($_POST['adrotate_schedulename'])) $name = esc_attr($_POST['adrotate_schedulename']);

		// Schedule and timeframe variables
		$sday = $smonth = $syear = $shour = $sminute = '';
		if(isset($_POST['adrotate_sday'])) $sday = strip_tags(trim($_POST['adrotate_sday'], "\t\n "));
		if(isset($_POST['adrotate_smonth'])) $smonth = strip_tags(trim($_POST['adrotate_smonth'], "\t\n "));
		if(isset($_POST['adrotate_syear'])) $syear = strip_tags(trim($_POST['adrotate_syear'], "\t\n "));
		if(isset($_POST['adrotate_shour'])) $shour = strip_tags(trim($_POST['adrotate_shour'], "\t\n "));
		if(isset($_POST['adrotate_sminute'])) $sminute = strip_tags(trim($_POST['adrotate_sminute'], "\t\n "));

		$eday = $emonth = $eyear = $ehour = $eminute = '';
		if(isset($_POST['adrotate_eday'])) $eday = strip_tags(trim($_POST['adrotate_eday'], "\t\n "));
		if(isset($_POST['adrotate_emonth'])) $emonth = strip_tags(trim($_POST['adrotate_emonth'], "\t\n "));
		if(isset($_POST['adrotate_eyear'])) $eyear = strip_tags(trim($_POST['adrotate_eyear'], "\t\n "));
		if(isset($_POST['adrotate_ehour'])) $ehour = strip_tags(trim($_POST['adrotate_ehour'], "\t\n "));
		if(isset($_POST['adrotate_eminute'])) $eminute = strip_tags(trim($_POST['adrotate_eminute'], "\t\n "));
	
		$maxclicks = $maxshown = $timeframe = $timeframelength = $timeframeclicks = $timeframeimpressions = '';
		if(isset($_POST['adrotate_maxclicks'])) $maxclicks = strip_tags(trim($_POST['adrotate_maxclicks'], "\t\n "));
		if(isset($_POST['adrotate_maxshown'])) $maxshown = strip_tags(trim($_POST['adrotate_maxshown'], "\t\n "));	
	
		if(current_user_can('adrotate_schedule_manage')) {	
			if(strlen($name) < 1) {
				$name = 'Schedule '.$id;
			}
	
			// Sort out start dates
			if(strlen($smonth) > 0 AND !is_numeric($smonth)) 	$smonth 	= date_i18n('m');
			if(strlen($sday) > 0 AND !is_numeric($sday)) 		$sday 		= date_i18n('d');
			if(strlen($syear) > 0 AND !is_numeric($syear)) 		$syear 		= date_i18n('Y');
			if(strlen($shour) > 0 AND !is_numeric($shour)) 		$shour 		= date_i18n('H');
			if(strlen($sminute) > 0 AND !is_numeric($sminute))	$sminute	= date_i18n('i');
			if(($smonth > 0 AND $sday > 0 AND $syear > 0) AND strlen($shour) == 0) $shour = '00';
			if(($smonth > 0 AND $sday > 0 AND $syear > 0) AND strlen($sminute) == 0) $sminute = '00';
	
			if($smonth > 0 AND $sday > 0 AND $syear > 0) {
				$startdate = mktime($shour, $sminute, 0, $smonth, $sday, $syear);
			} else {
				$startdate = 0;
			}
			
			// Sort out end dates
			if(strlen($emonth) > 0 AND !is_numeric($emonth)) 	$emonth 	= $smonth;
			if(strlen($eday) > 0 AND !is_numeric($eday)) 		$eday 		= $sday;
			if(strlen($eyear) > 0 AND !is_numeric($eyear)) 		$eyear 		= $syear+1;
			if(strlen($ehour) > 0 AND !is_numeric($ehour)) 		$ehour 		= $shour;
			if(strlen($eminute) > 0 AND !is_numeric($eminute)) 	$eminute	= $sminute;
			if(($emonth > 0 AND $eday > 0 AND $eyear > 0) AND strlen($ehour) == 0) $ehour = '00';
			if(($emonth > 0 AND $eday > 0 AND $eyear > 0) AND strlen($eminute) == 0) $eminute = '00';
	
			if($emonth > 0 AND $eday > 0 AND $eyear > 0) {
				$enddate = mktime($ehour, $eminute, 0, $emonth, $eday, $eyear);
			} else {
				$enddate = 0;
			}
			
			// Enddate is too early, reset to default
			if($enddate <= $startdate) $enddate = $startdate + 7257600; // 84 days (12 weeks)
	
			// Sort out click and impressions restrictions
			if(strlen($maxclicks) < 1 OR !is_numeric($maxclicks)) $maxclicks = 0;
			if(strlen($maxshown) < 1 OR !is_numeric($maxshown))	$maxshown = 0;

			// Save the schedule to the DB
			$wpdb->update($wpdb->prefix.'adrotate_schedule', array('name' => $name, 'starttime' => $startdate, 'stoptime' => $enddate, 'maxclicks' => $maxclicks, 'maximpressions' => $maxshown), array('id' => $id));
			adrotate_return('adrotate-schedules', 217);
			exit;
		} else {
			adrotate_return('adrotate-schedules', 500);
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_request_action

 Purpose:   Prepare action for banner or group from database
 Receive:   -none-
 Return:    -none-
 Since:		2.2
-------------------------------------------------------------*/
function adrotate_request_action() {
	global $wpdb, $adrotate_config;

	if(wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_bulk_ads_active') 
	OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_bulk_ads_disable') 
	OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_bulk_ads_error') 
	OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_bulk_ads_queue') 
	OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_bulk_ads_reject') 
	OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_bulk_groups') 
	OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_bulk_schedules') 
	OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_bulk_blocks')) {
		$banner_ids = $group_ids = $schedule_ids = $block_ids = '';
		if(!empty($_POST['adrotate_id'])) $banner_ids = array($_POST['adrotate_id']);
		if(!empty($_POST['bannercheck'])) $banner_ids = $_POST['bannercheck'];
		if(!empty($_POST['rejectcheck'])) $banner_ids = $_POST['rejectcheck'];
		if(!empty($_POST['queuecheck'])) $banner_ids = $_POST['queuecheck'];
		if(!empty($_POST['disabledbannercheck'])) $banner_ids = $_POST['disabledbannercheck'];
		if(!empty($_POST['errorbannercheck'])) $banner_ids = $_POST['errorbannercheck'];
		if(!empty($_POST['groupcheck'])) $group_ids = $_POST['groupcheck'];
		if(!empty($_POST['schedulecheck'])) $schedule_ids = $_POST['schedulecheck'];
		if(!empty($_POST['blockcheck'])) $block_ids = $_POST['blockcheck'];
		
		// Determine which kind of action to use
		if(!empty($_POST['adrotate_action'])) {
			// Default action call
			$actions = $_POST['adrotate_action'];
		} else if(!empty($_POST['adrotate_queue_action'])) {
			// Queued ads listing call
			$actions = $_POST['adrotate_queue_action'];
		} else if(!empty($_POST['adrotate_reject_action'])) {
			// Rejected ads listing call
			$actions = $_POST['adrotate_reject_action'];
		} else if(!empty($_POST['adrotate_disabled_action'])) {
			// Disabled ads listing call
			$actions = $_POST['adrotate_disabled_action'];
		} else if(!empty($_POST['adrotate_error_action'])) {
			// Erroneous ads listing call
			$actions = $_POST['adrotate_error_action'];
		} else {
			// If neither, protect user with invalid ID
			$banner_ids = $group_ids = $schedule_ids = $block_ids = '';
		}
		if(preg_match("/-/", $actions)) {
			list($action, $specific) = explode("-", $actions);	
		} else {
		   	$action = $actions;
		}

		if($banner_ids != '') {
			$return = 'adrotate-ads';
			if($action == 'export') {
				if(current_user_can('adrotate_moderate')) {
					adrotate_export($banner_ids);
					$result_id = 215;
				} else {
					adrotate_return($return, 500);
				}
			}
			foreach($banner_ids as $banner_id) {
				if($action == 'deactivate') {
					if(current_user_can('adrotate_ad_manage')) {
						adrotate_active($banner_id, 'deactivate');
						$result_id = 210;
					} else {
						adrotate_return($return, 500);
					}
				}
				if($action == 'activate') {
					if(current_user_can('adrotate_ad_manage')) {
						adrotate_active($banner_id, 'activate');
						$result_id = 211;
					} else {
						adrotate_return($return, 500);
					}
				}
				if($action == 'delete') {
					if(current_user_can('adrotate_ad_delete')) {
						adrotate_delete($banner_id, 'banner');
						$result_id = 203;
					} else {
						adrotate_return($return, 500);
					}
				}
				if($action == 'reset') {
					if(current_user_can('adrotate_ad_delete')) {
						adrotate_reset($banner_id);
						$result_id = 208;
					} else {
						adrotate_return($return, 500);
					}
				}
				if($action == 'renew') {
					if(current_user_can('adrotate_ad_manage')) {
						adrotate_renew($banner_id, $specific);
						$result_id = 209;
					} else {
						adrotate_return($return, 500);
					}
				}
				if($action == 'weight') {
					if(current_user_can('adrotate_ad_manage')) {
						adrotate_weight($banner_id, $specific);
						$result_id = 214;
					} else {
						adrotate_return($return, 500);
					}
				}
				if($action == 'approve') {
					if(current_user_can('adrotate_moderate_approve')) {
						adrotate_approve($banner_id);
						$return = 'adrotate-moderate';
						$result_id = 304;
					} else {
						adrotate_return('adrotate-moderate', 500);
					}
				}
				if($action == 'reject') {
					if(current_user_can('adrotate_moderate')) {
						adrotate_reject($banner_id);
						$return = 'adrotate-moderate';
						$result_id = 305;
					} else {
						adrotate_return('adrotate-moderate', 500);
					}
				}
				if($action == 'queue') {
					if(current_user_can('adrotate_moderate')) {
						adrotate_queue($banner_id);
						$return = 'adrotate-moderate';
						$result_id = 306;
					} else {
						adrotate_return('adrotate-moderate', 500);
					}
				}
			}
			adrotate_prepare_evaluate_ads(false);
		}
		
		if($group_ids != '') {
			$return = 'adrotate-groups';
			foreach($group_ids as $group_id) {
				if($action == 'group_delete') {
					if(current_user_can('adrotate_group_delete')) {
						adrotate_delete($group_id, 'group');
						$result_id = 204;
					} else {
						adrotate_return($return, 500);
					}
				}
				if($action == 'group_delete_banners') {
					if(current_user_can('adrotate_group_delete')) {
						adrotate_delete($group_id, 'bannergroup');
						$result_id = 213;
					} else {
						adrotate_return($return, 500);
					}
				}
			}
		}
	
		if($schedule_ids != '') {
			$return = 'adrotate-schedules';
			foreach($schedule_ids as $schedule_id) {
				if($action == 'schedule_delete') {
					if(current_user_can('adrotate_schedule_delete')) {
						adrotate_delete($schedule_id, 'schedule');
						$result_id = 218;
					} else {
						adrotate_return($return, 500);
					}
				}
			}
		}

		if($block_ids != '') {
			$return = 'adrotate-blocks';
			foreach($block_ids as $block_id) {
				if($action == 'block_delete') {
					if(current_user_can('adrotate_block_delete')) {
						adrotate_delete($block_id, 'block');
						$result_id = 205;
					} else {
						adrotate_return($return, 500);
					}
				}
			}
		 }
		
		adrotate_return($return, $result_id);
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_delete

 Purpose:   Remove banner or group from database
 Receive:   $id, $what
 Return:    -none-
 Since:		0.1
-------------------------------------------------------------*/
function adrotate_delete($id, $what) {
	global $wpdb;

	if($id > 0) {
		if($what == 'banner') {
			$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."adrotate` WHERE `id` = %d;", $id));
			$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = %d;", $id));
			$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."adrotate_stats` WHERE `ad` = %d;", $id));
		} else if ($what == 'group') {
			$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."adrotate_groups` WHERE `id` = %d;", $id));
			$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `group` = %d;", $id));
		} else if ($what == 'schedule') {
			$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."adrotate_schedule` WHERE `id` = %d;", $id));
			$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `schedule` = %d;", $id));
		} else if ($what == 'block') {
			$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."adrotate_blocks` WHERE `id` = %d;", $id));
			$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `block` = %d;", $id));
		} else if ($what == 'bannergroup') {
			$linkmeta = $wpdb->get_results($wpdb->prepare("SELECT `ad` FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `group` = %d AND `block` = '0' AND `user` = '0' AND `schedule` = '0';", $id));
			foreach($linkmeta as $meta) {
				$wpdb->query("DELETE FROM `".$wpdb->prefix."adrotate` WHERE `id` = ".$meta->ad.";");
				$wpdb->query("DELETE FROM `".$wpdb->prefix."adrotate_stats` WHERE `ad` = ".$meta->ad.";");
				$wpdb->query("DELETE FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = ".$meta->ad.";");
			}
			unset($linkmeta);
			$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."adrotate_groups` WHERE `id` = %d;", $id));
			$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `group` = %d;", $id));
			$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."adrotate_stats` WHERE `group` = %d;", $id)); // Perhaps unnessesary
		}
		adrotate_prepare_evaluate_ads(false);
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_active

 Purpose:   Activate or Deactivate a banner
 Receive:   $id, $what
 Return:    -none-
 Since:		0.1
-------------------------------------------------------------*/
function adrotate_active($id, $what) {
	global $wpdb;

	if($id > 0) {
		if($what == 'deactivate') {
			$wpdb->update($wpdb->prefix.'adrotate', array('type' => 'disabled'), array('id' => $id));
		}
		if ($what == 'activate') {
			// Determine status of ad 
			$adstate = adrotate_evaluate_ad($id);
			if($adstate == 'error' OR $adstate == 'expired') $adtype = 'error';
				else $adtype = 'active';
			$wpdb->update($wpdb->prefix.'adrotate', array('type' => $adtype), array('id' => $id));
		}
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_reset

 Purpose:   Reset statistics for a banner
 Receive:   $id
 Return:    -none-
 Since:		2.2
-------------------------------------------------------------*/
function adrotate_reset($id) {
	global $wpdb;

	if($id > 0) {
		$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."adrotate_stats` WHERE `ad` = %d", $id));
		$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."adrotate_tracker` WHERE `bannerid` = %d", $id));
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_renew

 Purpose:   Renew the end date of a banner with a new schedule starting where the last ended
 Receive:   $id, $howlong
 Return:    -none-
 Since:		2.2
-------------------------------------------------------------*/
function adrotate_renew($id, $howlong = 2592000) {
	global $wpdb;

	if($id > 0) {
		$schedule_id = $wpdb->get_var($wpdb->prepare("SELECT `schedule` FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = %d AND `group` = 0 AND `block` = 0 AND `user` = 0 ORDER BY `id` DESC LIMIT 1;", $id)); 
		if($schedule_id > 0) {
			$starttime = $wpdb->get_row($wpdb->prepare("SELECT `id`, `stoptime` FROM `".$wpdb->prefix."adrotate_schedule` WHERE `id` = %d ORDER BY `id` DESC LIMIT 1;", $schedule_id));
			$stoptime = $starttime->stoptime + $howlong;
			$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => 'Schedule for ad '.$id, 'starttime' => $starttime->stoptime, 'stoptime' => $stoptime, 'maxclicks' => 0, 'maximpressions' => 0));
		} else {
			$now = adrotate_now();
			$stoptime = $now + $howlong;
			$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => 'Schedule for ad '.$id, 'starttime' => $now, 'stoptime' => $stoptime, 'maxclicks' => 0, 'maximpressions' => 0));
		}
		$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $id, 'group' => 0, 'block' => 0, 'user' => 0, 'schedule' => $wpdb->insert_id));
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_weight

 Purpose:   Renew the end date of a banner
 Receive:   $id, $weight
 Return:    -none-
 Since:		3.6
-------------------------------------------------------------*/
function adrotate_weight($id, $weight = 6) {
	global $wpdb;

	if($id > 0) {
		$wpdb->update($wpdb->prefix.'adrotate', array('weight' => $weight), array('id' => $id));
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_approve

 Purpose:   Approve a queued banner
 Receive:   $id
 Return:    -none-
 Since:		3.8.4
-------------------------------------------------------------*/
function adrotate_approve($id) {
	global $wpdb;

	if($id > 0) {
		$wpdb->update($wpdb->prefix.'adrotate', array('type' => 'active'), array('id' => $id));
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_reject

 Purpose:   Reject a queued banner
 Receive:   $id
 Return:    -none-
 Since:		3.8.4
-------------------------------------------------------------*/
function adrotate_reject($id) {
	global $wpdb;

	if($id > 0) {
		$wpdb->update($wpdb->prefix.'adrotate', array('type' => 'reject'), array('id' => $id));
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_queue

 Purpose:   Queue a rejected banner
 Receive:   $id
 Return:    -none-
 Since:		3.8.4
-------------------------------------------------------------*/
function adrotate_queue($id) {
	global $wpdb;

	if($id > 0) {
		$wpdb->update($wpdb->prefix.'adrotate', array('type' => 'queue'), array('id' => $id));
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_export

 Purpose:   Export selected banners
 Receive:   $id
 Return:    -none-
 Since:		3.8.5
-------------------------------------------------------------*/
function adrotate_export($ids) {
	global $wpdb;

	if(is_array($ids)) {
		adrotate_export_ads($ids);
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_options_submit

 Purpose:   Save options from dashboard
 Receive:   $_POST
 Return:    -none-
 Since:		0.1
-------------------------------------------------------------*/
function adrotate_options_submit() {
	if(wp_verify_nonce($_POST['adrotate_nonce_settings'],'adrotate_settings')) {
		// Set and save user roles
		adrotate_set_capability($_POST['adrotate_advertiser'], "adrotate_advertiser");
		adrotate_set_capability($_POST['adrotate_global_report'], "adrotate_global_report");
		adrotate_set_capability($_POST['adrotate_ad_manage'], "adrotate_ad_manage");
		adrotate_set_capability($_POST['adrotate_ad_delete'], "adrotate_ad_delete");
		adrotate_set_capability($_POST['adrotate_group_manage'], "adrotate_group_manage");
		adrotate_set_capability($_POST['adrotate_group_delete'], "adrotate_group_delete");
		adrotate_set_capability($_POST['adrotate_block_manage'], "adrotate_block_manage");
		adrotate_set_capability($_POST['adrotate_block_delete'], "adrotate_block_delete");
		adrotate_set_capability($_POST['adrotate_schedule_manage'], "adrotate_schedule_manage");
		adrotate_set_capability($_POST['adrotate_schedule_delete'], "adrotate_schedule_delete");
		adrotate_set_capability($_POST['adrotate_moderate'], "adrotate_moderate");
		adrotate_set_capability($_POST['adrotate_moderate_approve'], "adrotate_moderate_approve");
		$config['advertiser'] 			= $_POST['adrotate_advertiser'];
		$config['global_report']	 	= $_POST['adrotate_global_report'];
		$config['ad_manage'] 			= $_POST['adrotate_ad_manage'];
		$config['ad_delete'] 			= $_POST['adrotate_ad_delete'];
		$config['group_manage'] 		= $_POST['adrotate_group_manage'];
		$config['group_delete'] 		= $_POST['adrotate_group_delete'];
		$config['block_manage'] 		= $_POST['adrotate_block_manage'];
		$config['block_delete'] 		= $_POST['adrotate_block_delete'];
		$config['schedule_manage'] 		= $_POST['adrotate_schedule_manage'];
		$config['schedule_delete'] 		= $_POST['adrotate_schedule_delete'];
		$config['moderate'] 			= $_POST['adrotate_moderate'];
		$config['moderate_approve'] 	= $_POST['adrotate_moderate_approve'];

		//Advertisers
		if(isset($_POST['adrotate_enable_advertisers'])) $config['enable_advertisers'] = 'Y';
			else $config['enable_advertisers'] = 'N';

		if(isset($_POST['adrotate_enable_editing'])) $config['enable_editing'] = 'Y';
			else $config['enable_editing'] = 'N';

		if(isset($_POST['adrotate_enable_stats'])) $config['enable_stats'] = 'Y';
			else $config['enable_stats'] = 'N';

		if(isset($_POST['adrotate_enable_loggedin_impressions'])) $config['enable_loggedin_impressions'] = 'Y';
			else $config['enable_loggedin_impressions'] = 'N';

		if(isset($_POST['adrotate_enable_loggedin_clicks'])) $config['enable_loggedin_clicks'] = 'Y';
			else $config['enable_loggedin_clicks'] = 'N';

		// GeoLocation
		if(isset($_POST['adrotate_enable_geo'])) $config['enable_geo'] = 1;
			else $config['enable_geo'] = 0;

		// Filter and format the banner folder, reset if empty
		$geo_email = trim($_POST['adrotate_geo_email']);
		if(strlen($geo_email) > 0) {
			$config['geo_email'] = $geo_email;
		} else {
			$config['geo_email'] = '';
		}

		$geo_pass = trim($_POST['adrotate_geo_pass']);
		if(strlen($geo_pass) > 0) {
			$config['geo_pass'] = $geo_pass;
		} else {
			$config['geo_pass'] = '';
		}

		if(isset($_POST['adrotate_enable_geo_advertisers'])) $config['enable_geo_advertisers'] = 1;
			else $config['enable_geo_advertisers'] = 0;

		// Filter and format the banner folder, reset if empty
		$banner_folder = trim($_POST['adrotate_banner_folder']);
		if(strlen($banner_folder) > 0) {
			$banner_folder = strtolower($banner_folder);
			$config['banner_folder'] = preg_replace('/[^a-zA-Z0-9\/\\\.,:\-_]/', '', $banner_folder);
		} else {
			$config['banner_folder'] = "wp-content/banners/";
		}

		// Filter and validate notification addresses, if not set, turn option off.
		$notification_emails = $_POST['adrotate_notification_email'];
		if(strlen($notification_emails) > 0) {
			$notification_emails = explode(',', trim($notification_emails));
			foreach($notification_emails as $notification_email) {
				$notification_email = trim($notification_email);
				if(strlen($notification_email) > 0) {
	  				if(preg_match("/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i", $notification_email) ) {
						$clean_notification_email[] = $notification_email;
					}
				}
			}
			$config['notification_email_switch'] 	= 'Y';
			$config['notification_email'] = array_unique(array_slice($clean_notification_email, 0, 5));
		} else {
			$config['notification_email_switch'] 	= 'N';
			$config['notification_email'] = array();
		}
	
		// Filter and validate advertiser addresses
		$advertiser_emails = $_POST['adrotate_advertiser_email'];
		if(strlen($advertiser_emails) > 0) {
			$advertiser_emails = explode(',', trim($advertiser_emails));
			foreach($advertiser_emails as $advertiser_email) {
				$advertiser_email = trim($advertiser_email);
				if(strlen($advertiser_email) > 0) {
	  				if(preg_match("/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i", $advertiser_email) ) {
						$clean_advertiser_email[] = $advertiser_email;
					}
				}
			}
			$config['advertiser_email'] = array_unique(array_slice($clean_advertiser_email, 0, 2));
		} else {
			$config['advertiser_email'] = array(get_option('admin_email'));
		}
	
		// Set up impression tracker timer
		$impression_timer = trim($_POST['adrotate_impression_timer']);
		if(strlen($impression_timer) >= 0 AND (is_numeric($impression_timer) AND $impression_timer >= 0 AND $impression_timer <= 3600)) {
			$config['impression_timer'] = $impression_timer;
		} else {
			$config['impression_timer'] = 10;
		}
	
		// Miscellaneous Options
		if(isset($_POST['adrotate_widgetalign'])) {
			$config['widgetalign'] = 'Y';
		} else {
			$config['widgetalign'] = 'N';
		}

		if(isset($_POST['adrotate_widgetpadding'])) {
			$config['widgetpadding'] = 'Y';
		} else {
			$config['widgetpadding'] = 'N';
		}

		if(isset($_POST['adrotate_adminbar'])) {
			$config['adminbar'] = 'Y';
		} else {
			$config['adminbar'] = 'N';
		}

		if(isset($_POST['adrotate_dashboard_notifications'])) {
			$config['dashboard_notifications'] = 'Y';
		} else {
			$config['dashboard_notifications'] = 'N';
		}

		if(isset($_POST['adrotate_hide_schedules'])) {
			$config['hide_schedules'] = 'Y';
		} else {
			$config['hide_schedules'] = 'N';
		}

		if(isset($_POST['adrotate_w3caching'])) {
			$config['w3caching'] = 'Y';
		} else {
			$config['w3caching'] = 'N';
		}

		if(isset($_POST['adrotate_supercache'])) {
			$config['supercache'] = 'Y';
		} else {
			$config['supercache'] = 'N';
		}

		if(isset($_POST['adrotate_jquery'])) {
			$config['jquery'] = 'Y';
		} else {
			$config['jquery'] = 'N';
		}

		if(isset($_POST['adrotate_jshowoff'])) {
			$config['jshowoff'] = 'Y';
		} else {
			$config['jshowoff'] = 'N';
		}

		if(isset($_POST['adrotate_jsfooter'])) {
			$config['jsfooter'] = 'Y';
		} else {
			$config['jsfooter'] = 'N';
		}

		update_option('adrotate_config', $config);
	
		// Sort out crawlers
		$crawlers = explode(',', trim($_POST['adrotate_crawlers']));
		$clean_crawler = array();
		foreach($crawlers as $crawler) {
			$crawler = preg_replace('/[^a-zA-Z0-9\[\]\-_:; ]/i', '', trim($crawler));
			if(strlen($crawler) > 0) $clean_crawler[] = $crawler;
		}
		update_option('adrotate_crawlers', $clean_crawler);
	
		// Debug option
		if(isset($_POST['adrotate_debug'])) 				$debug['general'] 		= true;
			else 											$debug['general']		= false;
		if(isset($_POST['adrotate_debug_dashboard'])) 		$debug['dashboard'] 	= true;
			else 											$debug['dashboard']		= false;
		if(isset($_POST['adrotate_debug_userroles'])) 		$debug['userroles'] 	= true;
			else 											$debug['userroles']		= false;
		if(isset($_POST['adrotate_debug_userstats'])) 		$debug['userstats'] 	= true;
			else 											$debug['userstats']		= false;
		if(isset($_POST['adrotate_debug_stats'])) 			$debug['stats'] 		= true;
			else 											$debug['stats']			= false;
		if(isset($_POST['adrotate_debug_geo'])) 			$debug['geo'] 			= true;
			else 											$debug['geo']			= false;
		if(isset($_POST['adrotate_debug_timers'])) 			$debug['timers'] 		= true;
			else 											$debug['timers']		= false;
		if(isset($_POST['adrotate_debug_track'])) 			$debug['track'] 		= true;
			else 											$debug['track']			= false;
		update_option('adrotate_debug', $debug);

		// Return to dashboard
		adrotate_return('adrotate-settings', 400);
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_prepare_roles

 Purpose:   Prepare user roles for WordPress
 Receive:   -None-
 Return:    $action
 Since:		3.0
-------------------------------------------------------------*/
function adrotate_prepare_roles() {
	
	if(isset($_POST['adrotate_role_add_submit'])) {
		$action = 401;
		adrotate_add_roles();		
		update_option('adrotate_roles', '1');
	} 
	if(isset($_POST['adrotate_role_remove_submit'])) {
		$action = 402;
		adrotate_remove_roles();
		update_option('adrotate_roles', '0');
	} 

	adrotate_return('adrotate-settings', $action);
}

/*-------------------------------------------------------------
 Name:      adrotate_add_roles

 Purpose:   Add User roles and capabilities
 Receive:   -None-
 Return:    -None-
 Since:		3.0
-------------------------------------------------------------*/
function adrotate_add_roles() {

	add_role('adrotate_advertiser', __('AdRotate Advertiser', 'adrotate'), array('read' => 1));

}

/*-------------------------------------------------------------
 Name:      adrotate_remove_roles

 Purpose:   Remove User roles and capabilities
 Receive:   -None-
 Return:    -None-
 Since:		3.0
-------------------------------------------------------------*/
function adrotate_remove_roles() {
	global $wp_roles;
	
	// Current
	remove_role('adrotate_advertiser');

	// Remove in version 4 or so (also remove global!)
	remove_role('adrotate_clientstats'); 
	$wp_roles->remove_cap('administrator','adrotate_clients');
	$wp_roles->remove_cap('editor','adrotate_clients');
	$wp_roles->remove_cap('author','adrotate_clients');
	$wp_roles->remove_cap('contributor','adrotate_clients');
	$wp_roles->remove_cap('subscriber','adrotate_clients');
	$wp_roles->remove_cap('adrotate_advertisers','adrotate_clients');
	$wp_roles->remove_cap('adrotate_clientstats','adrotate_clients');
	// End remove
}
?>