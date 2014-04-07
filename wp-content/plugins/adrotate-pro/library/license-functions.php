<?php
/*  
Copyright 2010-2014 Arnan de Gans - AJdG Solutions (email : info@ajdg.net)
dev4: sales@ajdg.net / 104-S-4fc269c5-f56a-4355-a405-90fa4171d551
*/

/*-------------------------------------------------------------
 Name:      AJdG Solutions Licensing Library
 Version:	1.1
-------------------------------------------------------------*/

function adrotate_licensed_update() {
	add_filter('pre_set_site_transient_update_plugins', 'adrotate_update_check');
	add_filter('plugins_api', 'adrotate_get_updatedetails', 10, 3);
}

function adrotate_update_check($checked_data) {
	global $adrotate_api_url;
	
	if(empty($checked_data->checked)) {
		return $checked_data;	
	}

   	$license = get_option('adrotate_activate');
	if($license['status'] == 1) {	
		$request_args = array(
			'slug' => 'adrotate',
			'version' => $checked_data->checked[ADROTATE_FOLDER .'/adrotate.php'],
			'instance' => $license['instance'],
			'platform' => get_option('siteurl'),
		);
		$raw_response = wp_remote_post($adrotate_api_url, adrotate_license_prepare_request('basic_check', adrotate_license_array_to_object($request_args)));
		
		if(!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200)) {
			$response = unserialize($raw_response['body']);	
		}
		
		if (is_object($response) && !empty($response)) { // Feed the update data into WP updater
			$checked_data->response[ADROTATE_FOLDER .'/adrotate.php'] = $response;
		}
	}

	return $checked_data;
}

function adrotate_get_updatedetails($def, $action, $args) {
	global $adrotate_api_url;
	
	if(!isset($args->slug) || $args->slug != 'adrotate') {
		return $def;	
	}

   	$license = get_option('adrotate_activate');
	
	// Get the current version
	$plugin_info = get_site_transient('update_plugins');
	$current_version = $plugin_info->checked[ADROTATE_FOLDER .'/adrotate.php'];
	$args->version = $current_version;
	$args->instance = $license['instance'];
	$args->email = $license['email'];
	$args->platform = get_option('siteurl');

	$request = wp_remote_post($adrotate_api_url, adrotate_license_prepare_request($action, $args));
	
	if(is_wp_error($request)) {
		$response = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
	} else {
		$response = unserialize($request['body']);
		if($response === false) {
			$response = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);		
		}
	}
	
	return $response;
}

function adrotate_license_mail_support() {
	if(wp_verify_nonce($_POST['adrotate_nonce_support'],'adrotate_nonce_support_request')) {
		$author = esc_attr($_POST['adrotate_updater_username']);
		$useremail = esc_attr($_POST['adrotate_updater_email']);
		$version = esc_attr($_POST['adrotate_updater_version']);
		$subject = strip_tags(stripslashes(trim($_POST['adrotate_updater_subject'], "\t\n ")));
		$text = strip_tags(stripslashes(trim($_POST['adrotate_updater_message'], "\t\n ")));
		$a = get_option('adrotate_activate');
	
		if(strlen($text) < 1) {
			adrotate_return('adrotate-support', 505);
		} else {
			$wpurl = get_bloginfo('wpurl');
			$wpversion = get_bloginfo('version');
			$wpcharset = get_bloginfo('charset');
			$wplang	= get_bloginfo('language');
			$license = ($a['type'] != '') ? $a['type'] : 'Not activated';
			$key = ($a['status'] == 1) ? $a['key'] : 'Not eligible for support';
			$text = nl2br($text);
			$pluginurl = "http://www.adrotateplugin.com";
			$to = "support@ajdg.net";

			$headers[] = "Content-Type: text/html; charset=iso-8859-1";
			$headers[] = "From: $author <$useremail>";
				
			$message = "<p>From: $author<br />Website: $wpurl<br />WordPress Version: $wpversion<br />WordPress Language: $wplang<br />WordPress Charset: $wpcharset<br />Plugin Version: $version<br />License Type: $license<br />License Key: $key</p>";	
			$message .= "<p>$text</p>";
	
			wp_mail($to, $subject, $message, $headers);
			adrotate_return('adrotate-support', 701);
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}

function adrotate_license_prepare_request($action, $args) {
	global $wp_version;
	
	return array(
		'body' => array(
			'action' => $action, 
			'request' => serialize($args),
		),
		'user-agent' => 'AdRotate Pro/' . $args->version . '; WordPress/'. $wp_version . '; ' . get_option('siteurl')
	);	
}

function adrotate_license_array_to_object($array = array()) {
    if (empty($array) || !is_array($array))
		return false;
		
	$data = new stdClass;
    foreach ($array as $akey => $aval)
            $data->{$akey} = $aval;
	return $data;
}
?>