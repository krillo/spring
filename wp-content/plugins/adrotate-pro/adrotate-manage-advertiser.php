<?php
/*  
Copyright 2010-2014 Arnan de Gans - AJdG Solutions (email : info@ajdg.net)
*/

/*-------------------------------------------------------------
 Name:      adrotate_advertiser_insert_input

 Purpose:   Prepare input form on saving new or updated banners
 Receive:   -None-
 Return:	-None-
 Since:		0.1 
-------------------------------------------------------------*/
function adrotate_advertiser_insert_input() {
	global $wpdb, $adrotate_config;

	if(wp_verify_nonce($_POST['adrotate_nonce'], 'adrotate_save_ad')) {
		// Mandatory
		$id = $author = $title = $bannercode = '';
		if(isset($_POST['adrotate_id'])) $id = $_POST['adrotate_id'];
		if(isset($_POST['adrotate_username'])) $author = $_POST['adrotate_username'];
		if(isset($_POST['adrotate_title'])) $title = strip_tags(htmlspecialchars(trim($_POST['adrotate_title'], "\t\n "), ENT_QUOTES));
		if(isset($_POST['adrotate_bannercode'])) $bannercode = htmlspecialchars(trim($_POST['adrotate_bannercode'], "\t\n "), ENT_QUOTES);
		$thetime = adrotate_now();

		// Schedule and timeframe variables
		$schedules = '';
		if(isset($_POST['scheduleselect'])) $schedules = $_POST['scheduleselect'];
	
		// GeoTargeting
		$cities = '';
		$countries = array();
		if(isset($_POST['adrotate_geo_cities'])) $cities = trim($_POST['adrotate_geo_cities'], "\t\n ");
		if(isset($_POST['adrotate_geo_countries'])) $countries = $_POST['adrotate_geo_countries'];
	
		// Ad options
		$link = $adrotate_image_current = $type = $weight = '';
		if(isset($_POST['adrotate_link'])) $link = strip_tags(htmlspecialchars(trim($_POST['adrotate_link'], "\t\n "), ENT_QUOTES));
		if(isset($_POST['adrotate_image_current'])) $adrotate_image_current = strip_tags(htmlspecialchars(trim($_POST['adrotate_image_current'], "\t\n "), ENT_QUOTES));
		if(isset($_POST['adrotate_type'])) $type = strip_tags(htmlspecialchars(trim($_POST['adrotate_type'], "\t\n "), ENT_QUOTES));
		if(isset($_POST['adrotate_weight'])) $weight = $_POST['adrotate_weight'];

		if(current_user_can('adrotate_advertiser')) {
			if(strlen($title) < 1) {
				$title = 'Ad '.$id;
			}

			if($_FILES["adrotate_image"]["size"] > 0) {
				$allowedExts = array("jpg", "jpeg", "gif", "png", "flv", "swf");
				$filename = sanitize_file_name(strtolower($_FILES["adrotate_image"]["name"]));
				$extension = end(explode(".", $_FILES["adrotate_image"]["name"]));
				$image_path = adrotate_home_path().$adrotate_config['banner_folder'];

				if(($_FILES["adrotate_image"]["type"] == "image/gif"
					OR $_FILES["adrotate_image"]["type"] == "image/jpeg" 
					OR $_FILES["adrotate_image"]["type"] == "image/pjpeg"
					OR $_FILES["adrotate_image"]["type"] == "image/jpg" 
					OR $_FILES["adrotate_image"]["type"] == "image/png"
					OR $_FILES["adrotate_image"]["type"] == "application/x-shockwave-flash/swf"
					OR $_FILES["adrotate_image"]["type"] == "video/x-flv/flv"
					OR $_FILES["adrotate_image"]["size"] <= 512000)
					AND in_array($extension, $allowedExts)
				) {
					if ($_FILES["adrotate_image"]["error"] > 0) {
						if($_FILES["adrotate_image"]["error"] == 1 OR $_FILES["adrotate_image"]["error"] == 2) $errorcode = __("File size exceeded.", "adrotate");
						else if($_FILES["adrotate_image"]["error"] == 3) $errorcode = __("Upload incomplete.", "adrotate");
						else if($_FILES["adrotate_image"]["error"] == 4) $errorcode = __("No file uploaded.", "adrotate");
						else if($_FILES["adrotate_image"]["error"] == 6 OR $_FILES["adrotate_image"]["error"] == 7) $errorcode = __("Could not write file to server.", "adrotate");
						else $errorcode = __("An unknown error occured, contact staff.", "adrotate");
						wp_die("<h3>".__("Something went wrong!", "adrotate")."</h3><p>".__("Go back and try again. If the error persists, contact staff.", "adrotate")."</p><p style='color: #f00;'>".$errorcode."</p>");
					} else {
						$image_name = $id."-".$author."-".$thetime."-".$filename;
						move_uploaded_file($_FILES["adrotate_image"]["tmp_name"], $image_path . $image_name);
					}
				} else {
					wp_die("<h3>".__("Something went wrong!", "adrotate")."</h3><p>".__("Go back and try again. If the error persists, contact staff.", "adrotate")."</p><p style='color: #f00;'>".__("The file was either too large or not in the right format.", "adrotate")."</p>");
				}
			} else {
				$image_name = $adrotate_image_current;
			}
			
			// Force image location
			$image = home_url()."/%folder%".$image_name;

			// Format the URL (assume http://)
			if((strlen($link) > 0 OR $link != "") AND stristr($link, "http://") === false AND stristr($link, "https://") === false) $link = "http://".$link;
			
			// Determine image settings ($image_field has priority!)
			if(strlen($image_name) > 0) {
				$imagetype = "dropdown";
				$image = home_url()."/%folder%".$image_name;
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
	
			// Save the ad to the DB
			$wpdb->update($wpdb->prefix.'adrotate', array('title' => $title, 'bannercode' => $bannercode, 'updated' => $thetime, 'author' => $author, 'imagetype' => $imagetype, 'image' => $image, 'link' => $link, 'weight' => $weight, 'cities' => $cities, 'countries' => $countries), array('id' => $id));

			// Determine status of ad 
			$adstate = adrotate_evaluate_ad($id);
			if($adstate == 'error' OR $adstate == 'expired') {
				$action = 502;
				$active = 'a_error';
			} else {
				$action = 306;
				$active = 'queue';
			}
			$wpdb->update($wpdb->prefix.'adrotate', array('type' => $active), array('id' => $id));

			// Fetch records for the ad, see if a publisher is set
			$linkmeta = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = %d AND `group` = 0 AND `block` = 0 AND `user` > 0;", $id));
			$advertiser = wp_get_current_user();

			// Add/update publisher on this ad
			if($linkmeta == 0 AND $advertiser->ID > 0) $wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $id, 'group' => 0, 'block' => 0, 'user' => $advertiser->ID, 'schedule' => 0));
			if($linkmeta == 1 AND $advertiser->ID > 0) $wpdb->query($wpdb->prepare("UPDATE `".$wpdb->prefix."adrotate_linkmeta` SET `user` = $advertiser->ID WHERE `ad` = %d AND `group` = 0 AND `block` = 0 AND `schedule` = 0;", $id)); 
	
			adrotate_return('adrotate-advertiser', $action);
			exit;
		} else {
			adrotate_return('adrotate-advertiser', 500);
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}
?>