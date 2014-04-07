<?php
/*  
Copyright 2010-2014 Arnan de Gans - AJdG Solutions (email : info@ajdg.net)
*/

/*-------------------------------------------------------------
 Name:      adrotate_shortcode

 Purpose:   Prepare function requests for calls on shortcodes
 Receive:   $atts, $content
 Return:    Function()
 Since:		0.7
-------------------------------------------------------------*/
function adrotate_shortcode($atts, $content = null) {
	global $adrotate_config;

	$banner_id = $group_ids = $block_id = $fallback = $weight = $columns = 0;
	if(!empty($atts['banner'])) $banner_id = trim($atts['banner'], "\r\t ");
	if(!empty($atts['group'])) $group_ids = trim($atts['group'], "\r\t ");
	if(!empty($atts['block'])) $block_id = trim($atts['block'], "\r\t ");
	if(!empty($atts['fallback'])) $fallback	= trim($atts['fallback'], "\r\t "); // Optional for groups (override)
	if(!empty($atts['weight']))	$weight	= trim($atts['weight'], "\r\t "); // Optional for groups (override)

	$output = '';

	if($adrotate_config['w3caching'] == "Y") $output .= '<!-- mfunc -->';

	if($banner_id > 0 AND ($group_ids == 0 OR $group_ids > 0) AND $block_id == 0) { // Show one Ad
		if($adrotate_config['supercache'] == "Y") $output .= '<!--mfunc echo adrotate_ad( $banner_id ) -->';
		$output .= adrotate_ad($banner_id);
		if($adrotate_config['supercache'] == "Y") $output .= '<!--/mfunc-->';
	}

	if($banner_id == 0 AND $group_ids > 0 AND $block_id == 0) { // Show group 
		if($adrotate_config['supercache'] == "Y") $output .= '<!--mfunc echo adrotate_group( $group_ids, $fallback, $weight ) -->';
		$output .= adrotate_group($group_ids, $fallback, $weight);
		if($adrotate_config['supercache'] == "Y") $output .= '<!--/mfunc-->';
	}

	if($banner_id == 0 AND $group_ids == 0 AND $block_id > 0) { // Show block 
		if($adrotate_config['supercache'] == "Y") $output .= '<!--mfunc echo adrotate_block( $block_id, $weight ) -->';
		$output .= adrotate_block($block_id, $weight);
		if($adrotate_config['supercache'] == "Y") $output .= '<!--/mfunc-->';
	}

	if($adrotate_config['w3caching'] == "Y") $output .= '<!-- /mfunc -->';

	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_adminmenu

 Purpose:   Add things to the admin bar
 Receive:   -None-
 Return:    -None-
 Since:		3.8
-------------------------------------------------------------*/
function adrotate_adminmenu() {
    global $wp_admin_bar, $adrotate_config;

	if(!is_super_admin() OR !is_admin_bar_showing())
		return;

    $wp_admin_bar->add_node(array( 'id' => 'adrotate', 'title' => __('AdRotate', 'adrotate'), 'href' => admin_url('/admin.php?page=adrotate')));
    $wp_admin_bar->add_node(array( 'id' => 'adrotate-ads','parent' => 'adrotate', 'title' => __('Manage Adverts', 'adrotate'), 'href' => admin_url('/admin.php?page=adrotate-ads')));
    $wp_admin_bar->add_node(array( 'id' => 'adrotate-ads-new','parent' => 'adrotate', 'title' => __('Add new Advert', 'adrotate'), 'href' => admin_url('/admin.php?page=adrotate-ads&view=addnew')));
    $wp_admin_bar->add_node(array( 'id' => 'adrotate-groups','parent' => 'adrotate', 'title' => __('Manage Groups', 'adrotate'), 'href' => admin_url('/admin.php?page=adrotate-groups')));
    $wp_admin_bar->add_node(array( 'id' => 'adrotate-schedules','parent' => 'adrotate', 'title' => __('Manage Schedules', 'adrotate'), 'href' => admin_url('/admin.php?page=adrotate-schedules')));
    $wp_admin_bar->add_node(array( 'id' => 'adrotate-stats','parent' => 'adrotate', 'title' => __('Statistics', 'adrotate'), 'href' => admin_url('/admin.php?page=adrotate-global-report')));
	if($adrotate_config['enable_advertisers'] == 'Y' AND $adrotate_config['enable_editing'] == 'Y') {
   		$wp_admin_bar->add_node(array( 'id' => 'adrotate-moderate','parent' => 'adrotate', 'title' => __('Moderate Adverts', 'adrotate'), 'href' => admin_url('/admin.php?page=adrotate-moderate')));
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_is_networked

 Purpose:   Determine if AdRotate is network activated
 Receive:   -None-
 Return:    Boolean
 Since:		3.9.8
-------------------------------------------------------------*/
function adrotate_is_networked() {
	$is_networked = get_site_option("adrotate_multisite");
	if(is_multisite() AND is_array($is_networked) AND count($is_networked) > 0) {
		return true;
	}		
	return false;
}

/*-------------------------------------------------------------
 Name:      adrotate_pick_weight

 Purpose:   Sort out and pick a random ad based on weight
 Receive:   $selected
 Return:    $ads[$key]
 Since:		3.8
-------------------------------------------------------------*/
function adrotate_pick_weight($selected) { 
	global $adrotate_debug;

    $ads = array_keys($selected); 
    $weight = array_values($selected);
     
    $sum_of_weight = array_sum($weight)-1; 

    $rnd = rand(0,$sum_of_weight); 
    foreach($ads as $key => $var){ 
        if($rnd<$weight[$key]){ 
            return $ads[$key]; 
        } 
        $rnd  -= $weight[$key]; 
    }
    unset($ads, $weight, $sum_of_weight, $rnd);
} 

/*-------------------------------------------------------------
 Name:      adrotate_filter_schedule

 Purpose:   Weed out ads that are over the limit of their schedule
 Receive:   $selected, $banner
 Return:    $selected
 Since:		3.6.11
-------------------------------------------------------------*/
function adrotate_filter_schedule($selected, $banner) { 
	global $wpdb, $adrotate_config, $adrotate_debug;

	$now = adrotate_now();
	$prefix = $wpdb->prefix;

	if($adrotate_debug['general'] == true) {
		echo "<p><strong>[DEBUG][adrotate_filter_schedule()] Filtering banner</strong><pre>";
		print_r($banner->id); 
		echo "</pre></p>"; 
	}
	
	// Get schedules for advert
	$schedules = $wpdb->get_results("SELECT `".$prefix."adrotate_schedule`.`id`, `starttime`, `stoptime`, `maxclicks`, `maximpressions` FROM `".$prefix."adrotate_schedule`, `".$prefix."adrotate_linkmeta` WHERE `schedule` = `".$prefix."adrotate_schedule`.`id` AND `ad` = ".$banner->id." ORDER BY `starttime` ASC;");

	$current = array();
	foreach($schedules as $schedule) {	
		if($schedule->starttime > $now OR $schedule->stoptime < $now) {
			$current[] = 0;
		} else {
			$current[] = 1;
			if($adrotate_config['enable_stats'] == 'Y') {
				$stat = adrotate_stats($banner->id, $schedule->starttime, $schedule->stoptime);
	
				if($adrotate_debug['general'] == true) {
					echo "<p><strong>[DEBUG][adrotate_filter_schedule] Ad ".$banner->id." - Schedule (id: ".$schedule->id.")</strong><pre>";
					echo "<br />Start: ".$schedule->starttime." (".date("F j, Y, g:i a", $schedule->starttime).")";
					echo "<br />End: ".$schedule->stoptime." (".date("F j, Y, g:i a", $schedule->stoptime).")";
					echo "<br />Clicks this period: ".$stat['clicks'];
					echo "<br />Impressions this period: ".$stat['impressions'];
					echo "</pre></p>";
				}
	
				if($stat['clicks'] >= $schedule->maxclicks AND $schedule->maxclicks > 0 AND $banner->tracker == "Y") $selected = array_diff_key($selected, array($banner->id => 0));
				if($stat['impressions'] >= $schedule->maximpressions AND $schedule->maximpressions > 0) $selected = array_diff_key($selected, array($banner->id => 0));
			}
		}
	}
	
	// Remove advert from array if all schedules are false (0)
	if(!in_array(1, $current)) {
		$selected = array_diff_key($selected, array($banner->id => 0));
	}
	unset($current, $schedules);
	
	return $selected;
} 

/*-------------------------------------------------------------
 Name:      adrotate_filter_timeframe

 Purpose:   Determine the active time and its limits and filter out expired ads
 Receive:  	$selected, $banner
 Return:    $selected
 Since:		3.6.11
-------------------------------------------------------------*/
function adrotate_filter_timeframe($selected, $banner) { 
	global $wpdb, $adrotate_debug;

	$now = adrotate_now();

	// Determine timeframe limits
	if($banner->timeframe == 'hour') {
		$impression_start	= adrotate_date_start('hour'); // Start of hour
		$impression_end		= $impression_start + (3600 * $banner->timeframelength); // End of hour
	} else if($banner->timeframe == 'day') {
		$impression_start	= adrotate_date_start('day'); // Start of day
		$impression_end		= $impression_start + (86400 * $banner->timeframelength); // End of day
	} else if($banner->timeframe == 'week') {
		$impression_start	= adrotate_date_start('week'); // Start of week
		$impression_end		= $impression_start + (604800 * $banner->timeframelength); // End of week
	}

	// Set addition to query
	$timeframe_stat = adrotate_stats($banner->id, $impression_start, $impression_end);

	if($adrotate_debug['general'] == true) {
		echo "<p><strong>[DEBUG][adrotate_filter_timeframe] Ad ".$banner->id."</strong><pre>";
		echo "Timeframe: ".$banner->timeframe;
		echo "<br />Start: ".$impression_start." (".date("F j, Y, g:i a", $impression_start).")";
		echo "<br />End: ".$impression_end." (".date("F j, Y, g:i a", $impression_end).")";
		echo "<br />Clicks this period: ".$timeframe_stat['clicks'];
		echo "<br />Impressions this period: ".$timeframe_stat['impressions'];
		echo "</pre></p>";
	}
	
	if($timeframe_stat) {
		if($banner->timeframeclicks == null) $banner->timeframeclicks = '0';
		if($banner->timeframeimpressions == null) $banner->timeframeimpressions = '0';
		if($timeframe_stat['clicks'] > $banner->timeframeclicks AND $banner->timeframeclicks > 0) {
			$selected = array_diff_key($selected, array($banner->id => 0));
		}
		if($timeframe_stat['impressions'] > $banner->timeframeimpressions AND $banner->timeframeimpressions > 0) {
			$selected = array_diff_key($selected, array($banner->id => 0));
		}
	}

	return $selected;
} 

/*-------------------------------------------------------------
 Name:      adrotate_filter_location

 Purpose:   Determine the users location, the ads geo settings and filter out ads
 Receive:  	$selected, $banner
 Return:    $selected|array
 Since:		3.8.5.1
-------------------------------------------------------------*/
function adrotate_filter_location($selected, $banner) { 
	global $adrotate_debug, $adrotate_geo;

	$geo = $adrotate_geo;
	if(is_array($geo)) {
		$cities = unserialize(stripslashes($banner->cities));
		$countries = unserialize(stripslashes($banner->countries));
		if(!is_array($cities)) $cities = array();
		if(!is_array($countries)) $countries = array();
		
		if($adrotate_debug['general'] == true OR $adrotate_debug['geo'] == true) {
			echo "<p><strong>[DEBUG][adrotate_filter_location] Ad (id: ".$banner->id.")</strong><pre>";
			echo "Geo Response: ".$geo['status'];
			echo "<br />Visitor IP: ".$geo['geo_ip'];
			echo "<br />Actual Visitor IP: ".$geo['orig_ip'];
			echo "<br />Visitor City: ".strtolower($geo['city']);
			echo "<br />Advert Cities (".count($cities)."): ";
			print_r($cities);
			echo "<br />Visitor Country: ".$geo['countrycode']." (".$geo['country'].")";
			echo "<br />Advert Countries (".count($countries)."): ";
			print_r($countries);
			echo "</pre></p>";
		}
	
		if(count($cities) > 0 AND !in_array(strtolower($geo['city']), $cities)) {
			return array_diff_key($selected, array($banner->id => 0));
		}
		if(count($countries) > 0 AND !in_array($geo['countrycode'], $countries)) {
			return array_diff_key($selected, array($banner->id => 0));
		}
	} else {
		if($adrotate_debug['general'] == true OR $adrotate_debug['geo'] == true) {
			echo "<p><strong>[DEBUG][adrotate_filter_location] Ad (id: ".$banner->id.")</strong><pre>";
			echo $geo;
			echo "</pre></p>";
		}
	}

	return $selected;
} 
 
/*-------------------------------------------------------------
 Name:      adrotate_geolocation

 Purpose:   Find the location of the visitor
 Receive:   -None_
 Return:    $array
 Since:		3.8.5
-------------------------------------------------------------*/
function adrotate_geolocation() {
	global $wpdb, $adrotate_config;

	// Geo Requirement
	$geo_required = get_option('adrotate_geo_required');

	if($geo_required > 0) {

		$remote_ip = adrotate_get_remote_ip();

	    $geo_result = array(
	    	'status' => 403, 
			'provider' => 'Forbidden',
	    	'geo_ip' => '', 
	    	'orig_ip' => $remote_ip, 
	    	'city' => '', 
	    	'country' => '', 
	    	'countrycode' => ''
	    );

		if($adrotate_config['enable_geo'] == 2) {
			$raw_response = wp_remote_get('http://www.geobytes.com/IpLocator.htm?GetLocation&template=json.txt&ipaddress='.$remote_ip.'&pt_email='.$adrotate_config["geo_email"].'&pt_password='.$adrotate_config["geo_pass"]);
		    if(!is_wp_error($raw_response)) {	
			    $response = json_decode($raw_response['body'], true);
				$geo_result['status'] = $raw_response['response']['code'];
				$geo_result['provider'] = 'GeoBytes';
				$geo_result['geo_ip'] = $response['geobytes']['ipaddress'];
				$geo_result['orig_ip'] = $remote_ip;
				$geo_result['city'] = $response['geobytes']['city'];
				$geo_result['country'] = $response['geobytes']['country'];
				$geo_result['countrycode'] = $response['geobytes']['iso2'];
			} else {
				$adrotate_config['enable_geo'] == 1;
			}
		}

		if($adrotate_config['enable_geo'] == 1) {
			$raw_response = wp_remote_get('http://freegeoip.net/json/'.$remote_ip);
		    if(!is_wp_error($raw_response)) {	
			    $response = json_decode($raw_response['body'], true);
				$geo_result['status'] = $raw_response['response']['code'];
				$geo_result['provider'] = 'FreegeoIP';
				$geo_result['geo_ip'] = $response['ip']; 
				$geo_result['orig_ip'] = $remote_ip;
				$geo_result['city'] = $response['city'];
				$geo_result['country'] = $response['country_name'];
				$geo_result['countrycode'] = $response['country_code'];
			}
		} 
	    unset($raw_response, $response);
	} else {
	    $geo_result = array(
	    	'status' => 501, 
			'provider' => 'Not implemented',
	    	'geo_ip' => '', 
	    	'orig_ip' => '', 
	    	'city' => '', 
	    	'country' => '', 
	    	'countrycode' => ''
	    );
	}

	return $geo_result;
}

/*-------------------------------------------------------------
 Name:      adrotate_array_unique

 Purpose:   Filter out duplicate records in multidimensional arrays
 Receive:   $array
 Return:    $array|$return
 Since:		3.0
-------------------------------------------------------------*/
function adrotate_array_unique($array) {
	if(count($array) > 0) {
		if(is_array($array[0])) {
			$return = array();
			// multidimensional
			foreach($array as $row) {
				if(!in_array($row, $return)) {
					$return[] = $row;
				}
			}
			return $return;
		} else {
			// not multidimensional
			return array_unique($array);
		}
	} else {
		return $array;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_array_unique

 Purpose:   Generate a random string
 Receive:   $length
 Return:    $result
 Since:		3.8
-------------------------------------------------------------*/
function adrotate_rand($length = 8) {
	$available_chars = "abcdefghijklmnopqrstuvwxyz";	

	$result = '';
	for($i = 0; $i < $length; $i++) {
		$result .= $available_chars[rand(0, 25)];
	}

	return $result;
}

/*-------------------------------------------------------------
 Name:      adrotate_shuffle

 Purpose:   Randomize an array but keep keys intact
 Receive:   $length
 Return:    $result
 Since:		3.8.8.3
-------------------------------------------------------------*/
function adrotate_shuffle($array) { 
	if(!is_array($array)) return $array; 
	$keys = array_keys($array); 
	shuffle($keys); 
	$shuffle = array(); 
	foreach($keys as $key) { 
		$shuffle[$key] = $array[$key]; 
	}
	return $shuffle; 
}

/*-------------------------------------------------------------
 Name:      adrotate_select_categories

 Purpose:   Create scrolling menu of all categories.
 Receive:   $savedcats, $count, $child_of, $parent
 Return:    $output
 Since:		3.8.4
-------------------------------------------------------------*/
function adrotate_select_categories($savedcats, $count = 2, $child_of = 0, $parent = 0) {
	if(!is_array($savedcats)) $savedcats = explode(',', $savedcats);
	$categories = get_categories(array('child_of' => $parent, 'parent' => $parent,  'orderby' => 'id', 'order' => 'asc', 'hide_empty' => 0));

	if(!empty($categories)) {
		$output = '';
		foreach($categories as $category) {
			if($category->parent > 0) {
				if($category->parent != $child_of) { 
					$count = $count + 1;
				}
				$indent = '&nbsp;'.str_repeat('-', $count * 2).'&nbsp;';
			} else {
				$indent = '';
			}
			$output .= '<input type="checkbox" name="adrotate_categories[]" value="'.$category->cat_ID.'"';
			if(in_array($category->cat_ID, $savedcats)) {
				$output .= ' checked';
			}
			$output .= '>&nbsp;&nbsp;'.$indent.$category->name.' ('.$category->category_count.')<br />';
			$output .= adrotate_select_categories($savedcats, $count, $category->parent, $category->cat_ID);
			$child_of = $parent;
		}
		return $output;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_select_pages

 Purpose:   Create scrolling menu of all pages.
 Receive:   $savedpages, $count, $child_of, $parent
 Return:    $output
 Since:		3.8.4
-------------------------------------------------------------*/
function adrotate_select_pages($savedpages, $count = 2, $child_of = 0, $parent = 0) {
	if(!is_array($savedpages)) $savedpages = explode(',', $savedpages);
	$pages = get_pages(array('child_of' => $parent, 'parent' => $parent, 'sort_column' => 'ID', 'sort_order' => 'asc'));

	if(!empty($pages)) {
		$output = '';
		foreach($pages as $page) {
			if($page->post_parent > 0) {
				if($page->post_parent != $child_of) {
					$count = $count + 1;
				}
				$indent = '&nbsp;'.str_repeat('-', $count * 2).'&nbsp;';
			} else {
				$indent = '';
			}
			$output .= '<input type="checkbox" name="adrotate_pages[]" value="'.$page->ID.'"';
			if(in_array($page->ID, $savedpages)) {
				$output .= ' checked';
			}
			$output .= '>&nbsp;&nbsp;'.$indent.$page->post_title.'<br />';
			$output .= adrotate_select_pages($savedpages, $count, $page->post_parent, $page->ID);
			$child_of = $parent;
		}
		return $output;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_countries

 Purpose:   List of countries
 Receive:   -None-
 Return:    array
 Since:		3.8.5.1
-------------------------------------------------------------*/
function adrotate_countries() {
	return array('US' => "United States", 'CA' => "Canada", 'GB' => "United Kingdom", 'AU' => "Australia", 'AT' => "Austria", 'BE' => "Belgium", 'BR' => "Brazil", 'FR' => "France", 'DE' => "Germany", 'IT' => "Italy", 
	'NL' => "Netherlands", 'NZ' => "New Zealand", 'PL' => "Poland", 'JP' => "Japan", 'SA' => "Saudi Arabia", 'ES' => "Spain", 'IE' => "Ireland", 'CH' => "Switzerland", 
	
	'DIVIDER' => "--- The rest is listed alphabetically ---", 'AF' => "Afghanistan", 'AL' => "Albania", 'DZ' => "Algeria", 'AD' => "Andorra", 'AO' => "Angola", 'AG' => "Antigua and Barbuda", 'AR' => "Argentina", 
	'AM' => "Armenia",  'AZ' => "Azerbaijan", 'BS' => "Bahamas", 'BH' => "Bahrain", 'BD' => "Bangladesh", 'BB' => "Barbados", 'BY' => "Belarus", 'BZ' => "Belize", 
	'BJ' => "Benin", 'BT' => "Bhutan", 'BO' => "Bolivia", 'BA' => "Bosnia and Herzegovina", 'BW' => "Botswana", 'BN' => "Brunei", 'BG' => "Bulgaria", 'BF' => "Burkina Faso", 'BI' => "Burundi", 'KH' => "Cambodia", 
	'CM' => "Cameroon", 'CV' => "Cape Verde", 'CF' => "Central African Republic", 'TD' => "Chad", 'CL' => "Chile", 'CN' => "China", 'CO' => "Colombia", 'KM' => "Comoros", 'CG' => "Congo (Brazzaville)", 
	'CD' => "Congo", 'CR' => "Costa Rica", 'CI' => "Cote d'Ivoire", 'HR' => "Croatia", 'CU' => "Cuba", 'CY' => "Cyprus", 'CZ' => "Czech Republic", 'DK' => "Denmark", 'DJ' => "Djibouti", 'DM' => "Dominica", 
	'DO' => "Dominican Republic", 'TL' => "East Timor (Timor Timur)", 'EC' => "Ecuador", 'EG' => "Egypt", 'SV' => "El Salvador", 'GQ' => "Equatorial Guinea", 'ER' => "Eritrea", 'EE' => "Estonia", 'ET' => "Ethiopia", 
	'FJ' => "Fiji", 'FI' => "Finland", 'GA' => "Gabon", 'GM' => "Gambia, The", 'GE' => "Georgia", 'GH' => "Ghana", 'GR' => "Greece", 'GD' => "Grenada", 'GT' => "Guatemala", 
	'GN' => "Guinea", 'GW' => "Guinea-Bissau", 'GY' => "Guyana", 'HT' => "Haiti", 'HN' => "Honduras", 'HU' => "Hungary", 'IS' => "Iceland", 'IN' => "India", 'ID' => "Indonesia", 'IR' => "Iran", 'IQ' => "Iraq", 
	'IS' => "Israel", 'JM' => "Jamaica", 'JO' => "Jordan", 'KZ' => "Kazakhstan", 'KE' => "Kenya", 'KI' => "Kiribati", 'KP' => "Korea, North", 'KR' => "Korea, South", 
	'KW' => "Kuwait", 'KG' => "Kyrgyzstan", 'LA' => "Laos", 'LV' => "Latvia", 'LB' => "Lebanon", 'LS' => "Lesotho", 'LR' => "Liberia", 'LY' => "Libya", 'LI' => "Liechtenstein", 'LT' => "Lithuania", 
	'LU' => "Luxembourg", 'MK' => "Macedonia", 'MG' => "Madagascar", 'MW' => "Malawi", 'MY' => "Malaysia", 'MV' => "Maldives", 'ML' => "Mali", 'MT' => "Malta", 'MH' => "Marshall Islands", 'MR' => "Mauritania", 
	'MU' => "Mauritius", 'MX' => "Mexico", 'FM' => "Micronesia", 'MD' => "Moldova", 'MC' => "Monaco", 'MN' => "Mongolia", 'MA' => "Morocco", 'MZ' => "Mozambique", 'MM' => "Myanmar", 'NA' => "Namibia", 'NR' => "Nauru", 
	'NP' => "Nepal",  'NI' => "Nicaragua", 'NE' => "Niger", 'NG' => "Nigeria", 'NO' => "Norway", 'OM' => "Oman", 'PK' => "Pakistan", 'PW' => "Palau", 'PA' => "Panama", 
	'PG' => "Papua New Guinea", 'PY' => "Paraguay", 'PE' => "Peru", 'PH' => "Philippines", 'PT' => "Portugal", 'QA' => "Qatar", 'RO' => "Romania", 'RU' => "Russia", 'RW' => "Rwanda", 
	'KN' => "Saint Kitts and Nevis", 'LC' => "Saint Lucia", 'VC' => "Saint Vincent", 'WS' => "Samoa", 'SM' => "San Marino", 'ST' => "Sao Tome and Principe", 'SN' => "Senegal", 
	'RS' => "Serbia and Montenegro", 'SC' => "Seychelles", 'SL' => "Sierra Leone", 'SG' => "Singapore", 'SK' => "Slovakia", 'SI' => "Slovenia", 'SB' => "Solomon Islands", 'SO' => "Somalia", 'ZA' => "South Africa", 
	 'LK' => "Sri Lanka", 'SD' => "Sudan", 'SR' => "Suriname", 'SZ' => "Swaziland", 'SE' => "Sweden", 'SY' => "Syria", 'TW' => "Taiwan", 'TJ' => "Tajikistan", 'TZ' => "Tanzania", 
	'TH' => "Thailand", 'TG' => "Togo", 'TO' => "Tonga", 'TT' => "Trinidad and Tobago", 'TN' => "Tunisia", 'TR' => "Turkey", 'TM' => "Turkmenistan", 'TV' => "Tuvalu", 'UG' => "Uganda", 'UA' => "Ukraine", 
	'AE' => "United Arab Emirates",  'UY' => "Uruguay", 'UZ' => "Uzbekistan", 'VU' => "Vanuatu", 'VA' => "Vatican City", 'VE' => "Venezuela", 'VN' => "Vietnam", 
	'YE' => "Yemen", 'ZM' => "Zambia", 'ZW' => "Zimbabwe");
}

/*-------------------------------------------------------------
 Name:      adrotate_select_countries

 Purpose:   Create scrolling menu of all countries.
 Receive:   $savedcountries
 Return:    $output
 Since:		3.8.5.1
-------------------------------------------------------------*/
function adrotate_select_countries($savedcountries) {
	if(!is_array($savedcountries)) $savedcountries = array();
	$countries = adrotate_countries();

	$output = '';
	foreach($countries as $k => $v) {
		if($k != "DIVIDER") {
			$output .= '<input type="checkbox" name="adrotate_geo_countries[]" value="'.$k.'"';
			if(in_array($k, $savedcountries)) {
				$output .= ' checked';
			}
			$output .= '>&nbsp;&nbsp;'.$v.'<br />';
		} else {
			$output .= '<em>'.$v.'</em><br />';
		}
	}
	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_prepare_evaluate_ads

 Purpose:   Initiate evaluations for errors and determine the ad status
 Receive:   $return
 Return:    opt|int
 Since:		3.6.5
-------------------------------------------------------------*/
function adrotate_prepare_evaluate_ads($return = true) {
	global $wpdb;
	
	// Fetch ads
	$ads = $wpdb->get_results("SELECT `id` FROM `".$wpdb->prefix."adrotate` WHERE `type` != 'disabled' AND `type` != 'empty' AND `type` != 'a_empty' AND `type` != 'queue' AND `type` != 'reject' ORDER BY `id` ASC;");

	// Determine error states
	$error = $expired = $expiressoon = $normal = $unknown = 0;
	foreach($ads as $ad) {
		$result = adrotate_evaluate_ad($ad->id);
		if($result == 'error') {
			$error++;
			$wpdb->query("UPDATE `".$wpdb->prefix."adrotate` SET `type` = 'error' WHERE `id` = '".$ad->id."';");
		} 

		if($result == 'expired') {
			$expired++;
			$wpdb->query("UPDATE `".$wpdb->prefix."adrotate` SET `type` = 'expired' WHERE `id` = '".$ad->id."';");
		} 
		
		if($result == '2days') {
			$expiressoon++;
			$wpdb->query("UPDATE `".$wpdb->prefix."adrotate` SET `type` = '2days' WHERE `id` = '".$ad->id."';");
		}
		
		if($result == '7days') {
			$normal++;
			$wpdb->query("UPDATE `".$wpdb->prefix."adrotate` SET `type` = '7days' WHERE `id` = '".$ad->id."';");
		}
		
		if($result == 'active') {
			$normal++;
			$wpdb->query("UPDATE `".$wpdb->prefix."adrotate` SET `type` = 'active' WHERE `id` = '".$ad->id."';");
		}
		
		if($result == 'unknown') {
			$unknown++;
		}
		unset($ad);
	}

	$count = $expired + $expiressoon + $error;
	$result = array('error' => $error,
					'expired' => $expired,
					'expiressoon' => $expiressoon,
					'normal' => $normal,
					'total' => $count,
					'unknown' => $unknown
					);
	update_option('adrotate_advert_status', $result);
	unset($ads, $result);
	if($return) adrotate_return('adrotate-settings', 405);
}

/*-------------------------------------------------------------
 Name:      adrotate_evaluate_ads

 Purpose:   Initiate automated evaluations for errors and determine the ad status
 Receive:   -None-
 Return:    -None-
 Since:		3.8.7.1
-------------------------------------------------------------*/
function adrotate_evaluate_ads() {
	adrotate_prepare_evaluate_ads(false);
}

/*-------------------------------------------------------------
 Name:      adrotate_evaluate_ad

 Purpose:   Evaluates ads for errors
 Receive:   $ad_id
 Return:    boolean
 Since:		3.6.5
-------------------------------------------------------------*/
function adrotate_evaluate_ad($ad_id) {
	global $wpdb;
	
	$now = adrotate_now();
	$in2days = $now + 172800;
	$in7days = $now + 604800;

	// Fetch ad
	$ad = $wpdb->get_row($wpdb->prepare("SELECT `id`, `bannercode`, `tracker`, `link`, `imagetype`, `image`, `cbudget`, `ibudget`, `crate`, `irate` FROM `".$wpdb->prefix."adrotate` WHERE `id` = %d;", $ad_id));
	$advertiser = $wpdb->get_var("SELECT `user` FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = '".$ad->id."' AND `group` = 0 AND `block` = 0 AND `user` > 0 AND `schedule` = 0;");
	$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `".$wpdb->prefix."adrotate_schedule`, `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = '".$ad->id."' AND `schedule` = `".$wpdb->prefix."adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");
	$schedules = $wpdb->get_var("SELECT COUNT(`schedule`) FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = '".$ad->id."' AND `group` = 0 AND `block` = 0 AND `user` = 0;");

	// Determine error states
	if(
		strlen($ad->bannercode) < 1 																	// AdCode empty
		OR ($ad->tracker == 'N' AND $advertiser > 0) 							// Didn't enable click-tracking, didn't provide a link, DID set a advertiser
		OR (!preg_match("/%image%/i", $ad->bannercode) AND $ad->image != '' AND $ad->imagetype != '')	// Didn't use %image% but selected an image
		OR (preg_match("/%image%/i", $ad->bannercode) AND $ad->image == '' AND $ad->imagetype == '')	// Did use %image% but didn't select an image
		OR ($ad->image == '' AND $ad->imagetype != '')													// Image and Imagetype mismatch
		OR $schedules == 0																				// No Schedules for this ad
	) {
		return 'error';
	} else if(
		$stoptime <= $now 																				// Past the enddate
		OR ($ad->crate > 0 AND $ad->cbudget < 1)														// Ad ran out of of click budget
		OR ($ad->irate > 0 AND $ad->ibudget < 1)														// Ad ran out of of impression budget
	){
		return 'expired';
	} else if($stoptime <= $in2days AND $stoptime >= $now){												// Expires in 2 days
		return '2days';
	} else if($stoptime <= $in7days AND $stoptime >= $now){												// Expires in 7 days
		return '7days';
	} else {
		return 'active';
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_prepare_color

 Purpose:   Check if ads are expired and set a color for its end date
 Receive:   $banner_id
 Return:    $result
 Since:		3.0
-------------------------------------------------------------*/
function adrotate_prepare_color($enddate) {
	$now = adrotate_now();
	$in2days = $now + 172800;
	$in7days = $now + 604800;
	
	if($enddate <= $now) {
		return '#CC2900'; // red
	} else if($enddate <= $in2days AND $enddate >= $now) {
		return '#F90'; // orange
	} else if($enddate <= $in7days AND $enddate >= $now) {
		return '#E6B800'; // yellow
	} else {
		return '#009900'; // green
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_ad_is_in_groups

 Purpose:   Build list of groups the ad is in (overview)
 Receive:   $id
 Return:    $output
 Since:		3.8
-------------------------------------------------------------*/
function adrotate_ad_is_in_groups($id) {
	global $wpdb;

	$output = '';
	$groups	= $wpdb->get_results("
		SELECT 
			`".$wpdb->prefix."adrotate_groups`.`name` 
		FROM 
			`".$wpdb->prefix."adrotate_groups`, 
			`".$wpdb->prefix."adrotate_linkmeta` 
		WHERE 
			`".$wpdb->prefix."adrotate_linkmeta`.`ad` = '".$id."'
			AND `".$wpdb->prefix."adrotate_linkmeta`.`group` = `".$wpdb->prefix."adrotate_groups`.`id`
			AND `".$wpdb->prefix."adrotate_linkmeta`.`block` = 0
			AND `".$wpdb->prefix."adrotate_linkmeta`.`user` = 0
		;");
	if($groups) {
		foreach($groups as $group) {
			$output .= $group->name.", ";
		}
	}
	$output = rtrim($output, ", ");
	
	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_get_remote_ip

 Purpose:   Get the remote IP from the visitor
 Receive:   -None-
 Return:    $buffer[0]
 Since:		3.6.2
-------------------------------------------------------------*/
function adrotate_get_remote_ip(){
	if(empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		$remote_ip = $_SERVER["REMOTE_ADDR"];
	} else {
		$remote_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	$buffer = explode(',', $remote_ip, 2);

	return $buffer[0];
}

/*-------------------------------------------------------------
 Name:      adrotate_home_path

 Purpose:   Get the home/base path for a site
 Receive:   -None-
 Return:    $home_path
 Since:		3.8.8
-------------------------------------------------------------*/
function adrotate_home_path() {
	$home = get_option( 'home' );
	$siteurl = get_option( 'siteurl' );
	if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {
		$wp_path_rel_to_home = str_ireplace( $home, '', $siteurl ); /* $siteurl - $home */
		$pos = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $wp_path_rel_to_home ) );
		$home_path = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );
		$home_path = trailingslashit( $home_path );
	} else {
		$home_path = ABSPATH;
	}

	return $home_path;
}

/*-------------------------------------------------------------
 Name:      adrotate_get_sorted_roles

 Purpose:   Returns all roles and capabilities, sorted by user level. Lowest to highest.
 Receive:   -none-
 Return:    $sorted
 Since:		3.2
-------------------------------------------------------------*/
function adrotate_get_sorted_roles() {	
	global $wp_roles;

	$editable_roles = apply_filters('editable_roles', $wp_roles->roles);
	$sorted = array();
	
	foreach($editable_roles as $role => $details) {
		$sorted[$details['name']] = get_role($role);
	}

	$sorted = array_reverse($sorted);

	return $sorted;
}

/*-------------------------------------------------------------
 Name:      adrotate_set_capability

 Purpose:   Grant or revoke capabilities to a role and all higher roles
 Receive:   $lowest_role, $capability
 Return:    -None-
 Since:		3.2
-------------------------------------------------------------*/
function adrotate_set_capability($lowest_role, $capability){
	$check_order = adrotate_get_sorted_roles();
	$add_capability = false;
	
	foreach($check_order as $role) {
		if($lowest_role == $role->name) $add_capability = true;
		if(empty($role)) continue;
		$add_capability ? $role->add_cap($capability) : $role->remove_cap($capability) ;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_remove_capability

 Purpose:   Remove the $capability from the all roles
 Receive:   $capability
 Return:    -None-
 Since:		3.2
-------------------------------------------------------------*/
function adrotate_remove_capability($capability){
	$check_order = adrotate_get_sorted_roles();

	foreach($check_order as $role) {
		$role = get_role($role->name);
		$role->remove_cap($capability);
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_mail_notifications

 Purpose:   Email the manager that his ads need help
 Receive:   -None-
 Return:    -None-
 Since:		3.0
-------------------------------------------------------------*/
function adrotate_mail_notifications() {
	global $adrotate_config, $adrotate_advert_status;
	
	$emails = $adrotate_config['notification_email'];
	$x = count($emails);
	if($x == 0) $emails = array(get_option('admin_email'));
	
	$blogname 		= get_option('blogname');
	$siteurl 		= get_option('siteurl');
	$dashboardurl	= $siteurl."/wp-admin/admin.php?page=adrotate-ads";
	$pluginurl		= "http://www.adrotateplugin.com";

	$data = $adrotate_advert_status;
	for($i=0;$i<$x;$i++) {
		if($data['total'] > 0) {
		    $headers = "Content-Type: text/html; charset=iso-8859-1" . "\r\n" .
		      		  "From: $author <".$emails[$i].">" . "\r\n";

			$subject = __('[AdRotate Alert] Your ads need your help!', 'adrotate');
			
			$message = "<p>".__('Hello', 'adrotate').",</p>";
			$message .= "<p>".__('This notification is send to you from your website', 'adrotate')." '$blogname'.</p>";
			$message .= "<p>".__('You will receive a notification approximately every 24 hours until the issues are resolved.', 'adrotate')."</p>";
			$message .= "<p>".__('Current issues:', 'adrotate')."<br />";
			if($data['error'] > 0) $message .= $data['error']." ".__('ad(s) have configuration errors. This needs your immediate attention!', 'adrotate')."<br />";
			if($data['expired'] > 0) $message .= $data['expired']." ".__('ad(s) expired. This needs your immediate attention!', 'adrotate')."<br />";
			if($data['expiressoon'] > 0) $message .= $data['expiressoon']." ".__('ad(s) will expire in less than 2 days.', 'adrotate')."<br />";
			$message .= "</p>";
			$message .= "<p>".__('A total of', 'adrotate')." ".$data['total']." ".__('ad(s) are in need of your care!', 'adrotate')."</p>";
			$message .= "<p>".__('Access your dashboard here:', 'adrotate')." $dashboardurl</p>";
			$message .= "<p>".__('Have a nice day!', 'adrotate')."</p>";
			$message .= "<p>".__('Your AdRotate Notifier', 'adrotate')."<br />";
			$message .= "$pluginurl</p>";

			wp_mail($emails[$i], $subject, $message, $headers);
		}
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_mail_message

 Purpose:   Send messages to advertiser
 Receive:   -None-
 Return:    -None-
 Since:		3.1
-------------------------------------------------------------*/
function adrotate_mail_message() {
	global $wpdb, $adrotate_config;

	if(wp_verify_nonce($_POST['adrotate_nonce'], 'adrotate_email_advertiser') OR wp_verify_nonce($_POST['adrotate_nonce'], 'adrotate_email_moderator')) {
		$id 			= $_POST['adrotate_id'];
		$request 		= $_POST['adrotate_request'];
		$author 		= $_POST['adrotate_username'];
		$useremail 		= $_POST['adrotate_email'];
		$text	 		= strip_tags(stripslashes(trim($_POST['adrotate_message'], "\t\n ")));
	
		if(strlen($text) < 1) $text = "";
		
		$emails = $adrotate_config['advertiser_email'];
		$x = count($emails);
		if($x == 0) $emails = array(get_option('admin_email'));
		
		$siteurl 		= get_option('siteurl');
		$adurl			= $siteurl."/wp-admin/admin.php?page=adrotate-ads&view=edit&ad=".$id;
		$pluginurl		= "http://www.adrotateplugin.com";
	
		for($i=0;$i<$x;$i++) {
		    $headers 		= "Content-Type: text/html; charset=iso-8859-1" . "\r\n" .
		      				  "From: $author <$useremail>" . "\r\n";
			$now 			= adrotate_now();
			
			if($request == "renew") $subject = __('[AdRotate] An advertiser has put in a request for renewal!', 'adrotate');
			if($request == "remove") $subject = __('[AdRotate] An advertiser wants his ad removed.', 'adrotate');
			if($request == "other") $subject = __('[AdRotate] An advertiser wrote a comment on his ad!', 'adrotate');
			if($request == "issue") $subject = __('[AdRotate] An advertiser has a problem!', 'adrotate');
			
			$message = "<p>Hello,</p>";
		
			if($request == "renew") $message .= "<p>$author ".__('requests ad', 'adrotate')." <strong>$id</strong> ".__('renewed!', 'adrotate')."</p>";
			if($request == "remove") $message .= "<p>$author ".__('requests ad', 'adrotate')." <strong>$id</strong> ".__('removed.', 'adrotate')."</p>";
			if($request == "other") $message .= "<p>$author ".__('has something to say about ad', 'adrotate')." <strong>$id</strong>.</p>";
			if($request == "issue") $message .= "<p>$author ".__('has a problem with AdRotate.', 'adrotate')."</p>";
			
			$message .= "<p>".__('Attached message:', 'adrotate')." $text</p>";
			
			$message .= "<p>".__('You can reply to this message to contact', 'adrotate')." $author.<br />";
			if($request != "issue") $message .= __('Review the ad here:', 'adrotate')." $adurl";
			$message .= "</p>";
			
			$message .= "<p>".__('Have a nice day!', 'adrotate')."<br />";
			$message .= __('Your AdRotate Notifier', 'adrotate')."<br />";
			$message .= "$pluginurl</p>";
		
			wp_mail($emails[$i], $subject, $message, $headers);
		}
	
		adrotate_return('adrotate-advertiser', 300);
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_mail_test

 Purpose:   Send test messages
 Receive:   -None-
 Return:    -None-
 Since:		3.5
-------------------------------------------------------------*/
function adrotate_mail_test() {
	global $wpdb, $adrotate_config;

		if(wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_email_test')) {
		if(isset($_POST['adrotate_notification_test_submit'])) {
			$type = "notification";
			$emails = $adrotate_config['notification_email'];
		}
		
		if(isset($_POST['adrotate_advertiser_test_submit'])) {
			$type = "advertiser";
			$emails = $adrotate_config['advertiser_email'];
		}
		
		$x = count($emails);
		if($x == 0) $emails = array(get_option('admin_email'));
		
		$siteurl 		= get_option('siteurl');
		$pluginurl		= "http://www.adrotateplugin.com";
		$email 			= get_option('admin_email');
		
		for($i=0;$i<$x;$i++) {
			$headers =	"MIME-Version: 1.0\n" .
		      			"From: AdRotate Plugin <".$email.">\r\n\n" . 
		      			"Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n";
			
			if($type == "notification") $subject = __('[AdRotate] This is a test notification!', 'adrotate');
			if($type == "advertiser") $subject = __('[AdRotate] This is a test email.', 'adrotate');
			
			$message = 	"<p>".__('Hello', 'adrotate').",</p>";
		
			$message .= "<p>".__('The administrator of', 'adrotate')." $siteurl ".__('has set your email address to receive', 'adrotate');
			if($type == "notification") $message .= " ".__('notifications from AdRotate. These are to alert you of the state of advertisements posted on this website.', 'adrotate');
			if($type == "advertiser") $message .= " ".__('messages from Advertisers using AdRotate. Your email is not shown to them until you reply to their messages.', 'adrotate');
			$message .= "</p>";
	
			$message .= "<p>".__('If you believe this message to be in error, reply to this email with your complaint!', 'adrotate')."</p>";
					
			$message .= "<p>".__('Have a nice day!', 'adrotate')."<br />";
			$message .= __('Your AdRotate Notifier', 'adrotate')."<br />";
			$message .= "$pluginurl</p>";
		
			wp_mail($emails[$i], $subject, $message, $headers);
		}
	
		if($type == "notification") adrotate_return('adrotate-settings', 407);
		if($type == "advertiser") adrotate_return('adrotate-settings', 408);
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_dashboard_scripts

 Purpose:   Load file uploaded popup
 Receive:   -None-
 Return:	-None-
 Since:		3.6
-------------------------------------------------------------*/
function adrotate_dashboard_scripts() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('raphael', plugins_url('/library/raphael-min.js', __FILE__), array('jquery'));
	wp_enqueue_script('elycharts', plugins_url('/library/elycharts.min.js', __FILE__), array('jquery', 'raphael'));
	wp_enqueue_script('textatcursor', plugins_url('/library/textatcursor.js', __FILE__));
}

/*-------------------------------------------------------------
 Name:      adrotate_dashboard_styles

 Purpose:   Load file uploaded popup
 Receive:   -None-
 Return:	-None-
 Since:		3.6
-------------------------------------------------------------*/
function adrotate_dashboard_styles() {
?>
<style type="text/css" media="screen">
	/* styles for graphs */
	.adrotate-label { font-size: 12px; margin: auto 0; padding:5px; font-weight: bold }
	.adrotate-clicks { color: #5Af; font-weight: normal }
	.adrotate-impressions { color: #F80; font-weight: normal }
	
	/* styles for advert statuses and stats */
	.row_urgent { background-color:#ffebe8; border-color:#c00; }
	.row_error { background-color:#ffffe0; border-color:#e6db55; }
	.row_inactive { background-color:#ebf3fa; border-color:#466f82; }
	.row_active { background-color: #e5faee; border-color: #518257; }
	.stats_large { display: block; margin-bottom: 10px; margin-top: 10px; text-align: center; font-weight: bold; }
	.number_large {	margin: 20px; font-size: 28px; }
	
	/* Fancy select box for group and page injection*/
	.adrotate-select { padding:3px; border:1px solid #ccc; max-width:500px; max-height:100px; overflow-y:scroll; background-color:#fff; }
</style>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_folder_contents

 Purpose:   List folder contents of /wp-content/banners and /wp-content/uploads
 Receive:   $current
 Return:	$output
 Since:		0.4
-------------------------------------------------------------*/
function adrotate_folder_contents($current) {
	global $wpdb, $adrotate_config;

	$output = '';
	$siteurl = get_option('siteurl');

	// Read Banner folder
	$files = array();
	$i = 0;
	if($handle = opendir(adrotate_home_path().'/'.$adrotate_config['banner_folder'])) {
	    while (false !== ($file = readdir($handle))) {
	        if ($file != "." AND $file != ".." AND $file != "index.php") {
	            $files[] = $file;
	        	$i++;
	        }
	    }
	    closedir($handle);

	    if($i > 0) {
			sort($files);
			foreach($files as $file) {
				$fileinfo = pathinfo($file);
		
				if((strtolower($fileinfo['extension']) == "jpg" OR strtolower($fileinfo['extension']) == "gif" OR strtolower($fileinfo['extension']) == "png" 
				OR strtolower($fileinfo['extension']) == "jpeg" OR strtolower($fileinfo['extension']) == "swf" OR strtolower($fileinfo['extension']) == "flv")) {
				    $output .= "<option value='".$file."'";
				    if(($current == $siteurl.'/wp-content/banners/'.$file) OR ($current == $siteurl."/%folder%".$file)) { $output .= "selected"; }
				    $output .= ">".$file."</option>";
				}
			}
		} else {
	    	$output .= "<option disabled>&nbsp;&nbsp;&nbsp;".__('No files found', 'adrotate')."</option>";
		}
	} else {
    	$output .= "<option disabled>&nbsp;&nbsp;&nbsp;".__('Folder not found or not accessible', 'adrotate')."</option>";
	}
	
	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_return

 Purpose:   Internal redirects
 Receive:   $page, $status
 Return:    -none-
 Since:		3.8.5
-------------------------------------------------------------*/
function adrotate_return($page, $status, $args = null) {

	if(adrotate_is_networked()) {
		$file = 'settings';
	} else {
		$file = 'admin';
	}
	
	if(strlen($page) > 0 AND ($status > 0 AND $status < 1000)) {
		$defaults = array(
			'status' => $status
		);
		$arguments = wp_parse_args($args, $defaults);
		$redirect = $file.'.php?page=' . $page . '&'.http_build_query($arguments);
	} else {
		$redirect = $file.'.php?page=adrotate-ads';
	}

	wp_redirect($redirect);
}

/*-------------------------------------------------------------
 Name:      adrotate_status

 Purpose:   Internal redirects
 Receive:   $status
 Return:    -none-
 Since:		3.8.5
-------------------------------------------------------------*/
function adrotate_status($status, $args = null) {

	$defaults = array(
		'ad' => '',
		'group' => '',
		'file' => ''
	);
	$arguments = wp_parse_args($args, $defaults);

	switch($status) {
		// Management messages
		case '200' :
			echo '<div id="message" class="updated"><p>'. __('Ad saved', 'adrotate') .'</p></div>';
		break;

		case '201' :
			echo '<div id="message" class="updated"><p>'. __('Group saved', 'adrotate') .'</p></div>';
		break;

		case '202' :
			echo '<div id="message" class="updated"><p>'. __('Block saved', 'adrotate') .'</p></div>';
		break;

		case '203' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) deleted', 'adrotate') .'</p></div>';
		break;

		case '204' :
			echo '<div id="message" class="updated"><p>'. __('Group deleted', 'adrotate') .'</p></div>';
		break;

		case '205' :
			echo '<div id="message" class="updated"><p>'. __('Block deleted', 'adrotate') .'</p></div>';
		break;

		case '206' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) deleted', 'adrotate') .'</p></div>';
		break;

		case '207' :
			echo '<div id="message" class="updated"><p>'. __('Block deleted', 'adrotate') .'</p></div>';
		break;

		case '208' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) statistics reset', 'adrotate') .'</p></div>';
		break;

		case '209' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) renewed', 'adrotate') .'</p></div>';
		break;

		case '210' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) deactivated', 'adrotate') .'</p></div>';
		break;

		case '211' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) activated', 'adrotate') .'</p></div>';
		break;

		case '212' :
			echo '<div id="message" class="updated"><p>'. __('Email(s) with reports successfully sent', 'adrotate') .'</p></div>';
		break;

		case '213' :
			echo '<div id="message" class="updated"><p>'. __('Group including it\'s Ads deleted', 'adrotate') .'</p></div>';
		break;

		case '214' :
			echo '<div id="message" class="updated"><p>'. __('Weight changed', 'adrotate') .'</p></div>';
		break;

		case '215' :
			echo '<div id="message" class="updated"><p>'. __('Export created', 'adrotate') .'. <a href="' . WP_CONTENT_URL . '/reports/'.$arguments['file'].'">Download</a>.</p></div>';
		break;

		case '216' :
			echo '<div id="message" class="updated"><p>'. __('Ads imported', 'adrotate') .'</div>';
		break;

		case '217' :
			echo '<div id="message" class="updated"><p>'. __('Schedule saved', 'adrotate') .'</div>';
		break;

		case '218' :
			echo '<div id="message" class="updated"><p>'. __('Schedule(s) deleted', 'adrotate') .'</div>';
		break;

		// Advertiser messages
		case '300' :
			echo '<div id="message" class="updated"><p>'. __('Your message has been sent. Someone will be in touch shortly.', 'adrotate') .'</p></div>';
		break;

		case '301' :
			echo '<div id="message" class="updated"><p>'. __('Advert submitted for review', 'adrotate') .'</p></div>';
		break;

		case '302' :
			echo '<div id="message" class="updated"><p>'. __('Advert updated and awaiting review', 'adrotate') .'</p></div>';
		break;

		case '303' :
			echo '<div id="message" class="updated"><p>'. __('Email(s) with reports successfully sent', 'adrotate') .'</p></div>';
		break;

		case '304' :
			echo '<div id="message" class="updated"><p>'. __('Ad approved', 'adrotate') .'</p></div>';
		break;

		case '305' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) rejected', 'adrotate') .'</p></div>';
		break;

		case '306' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) queued', 'adrotate') .'</p></div>';
		break;

		// Settings
		case '400' :
			echo '<div id="message" class="updated"><p>'. __('Settings saved', 'adrotate') .'</p></div>';
		break;

		case '401' :
			echo '<div id="message" class="updated"><p>'. __('AdRotate client role added', 'adrotate') .'</p></div>';
		break;

		case '402' :
			echo '<div id="message" class="updated"><p>'. __('AdRotate client role removed', 'adrotate') .'</p></div>';
		break;

		case '403' :
			echo '<div id="message" class="updated"><p>'. __('Database optimized', 'adrotate') .'</p></div>';
		break;

		case '404' :
			echo '<div id="message" class="updated"><p>'. __('Database repaired', 'adrotate') .'</p></div>';
		break;

		case '405' :
			echo '<div id="message" class="updated"><p>'. __('Ads evaluated and statuses have been corrected where required', 'adrotate') .'</p></div>';
		break;

		case '406' :
			echo '<div id="message" class="updated"><p>'. __('Empty database records removed', 'adrotate') .'</p></div>';
		break;

		case '407' :
			echo '<div id="message" class="updated"><p>'. __('Test notification sent', 'adrotate') .'</p></div>';
		break;

		case '408' :
			echo '<div id="message" class="updated"><p>'. __('Test mailing sent', 'adrotate') .'</p></div>';
		break;

		// (all) Error messages
		case '500' :
			echo '<div id="message" class="error"><p>'. __('Action prohibited', 'adrotate') .'</p></div>';
		break;

		case '501' :
			echo '<div id="message" class="error"><p>'. __('The ad was saved but has an issue which might prevent it from working properly. Review the colored ad.', 'adrotate') .'</p></div>';
		break;

		case '502' :
			echo '<div id="message" class="error"><p>'. __('The ad was saved but has an issue which might prevent it from working properly. Please contact staff.', 'adrotate') .'</p></div>';
		break;

		case '503' :
			echo '<div id="message" class="error"><p>'. __('No data found in selected time period', 'adrotate') .'</p></div>';
		break;

		case '504' :
			echo '<div id="message" class="error"><p>'. __('Database can only be optimized or cleaned once every hour', 'adrotate') .'</p></div>';
		break;

		case '505' :
			echo '<div id="message" class="error"><p>'. __('Form can not be empty!', 'adrotate') .'</p></div>';
		break;

		case '506' :
			echo '<div id="message" class="updated"><p>'. __('No file uploaded.', 'adrotate') .'</p></div>';
		break;

		case '507' :
			echo '<div id="message" class="updated"><p>'. __('The file could not be read.', 'adrotate') .'</p></div>';
		break;

		case '508' :
			echo '<div id="message" class="updated"><p>'. __('Wrong file type or the file is too large. Only CSV files of 4MB or smaller are allowed.', 'adrotate') .'</p></div>';
		break;

		case '509' :
			echo '<div id="message" class="updated"><p>'. __('No ads found.', 'adrotate') .'</p></div>';
		break;

		// Licensing
		case '600' :
			echo '<div id="message" class="error"><p>Invalid request</p></div>';
		break;

		case '601' :
			echo '<div id="message" class="error"><p>No license key or email provided</p></div>';
		break;

		case '602' :
			echo '<div id="message" class="error"><p>No valid response from license server. Contact support.<br />Response code: '.$arguments['error'].'</p></div>';
		break;

		case '603' :
			echo '<div id="message" class="error"><p>The email provided is invalid. If you think this is not true please contact support.</p></div>';
		break;

		case '604' :
			echo '<div id="message" class="error"><p>Invalid license key. If you think this is not true please contact support.</p></div>';
		break;

		case '605' :
			echo '<div id="message" class="error"><p>The purchase matching this product is not complete. Contact support.</p></div>';
		break;

		case '606' :
			echo '<div id="message" class="error"><p>No remaining activations for this license. If you think this is not true please contact support.</p></div>';
		break;

		case '607' :
			echo '<div id="message" class="error"><p>Could not (de)activate key. Contact support.</p></div>';
		break;

		case '608' :
			echo '<div id="message" class="updated"><p>Thank you. Your license is now active</p></div>';
		break;

		case '609' :
			echo '<div id="message" class="updated"><p>Thank you. Your license is now de-activated</p></div>';
		break;

		case '610' :
			echo '<div id="message" class="updated"><p>Thank you. Your licenses have been reset</p></div>';
		break;

		case '611' :
			echo '<div id="message" class="updated"><p>This license can not be activated for networks. Please purchase a Developer or Multisite license.</p></div>';
		break;

		// Support
		case '701' :
			echo '<div id="message" class="updated"><p>Support request sent! You will receive a confirmation email from the ticket system (support@ajdg.net) shortly. Also check your Spam folder!</p></div>';
		break;
		
		default :
			echo '<div id="message" class="updated"><p>'. __('Unexpected error', 'adrotate') .'</p></div>';			
		break;
	}
	
	unset($arguments, $args);
}
?>