<?php
/*
Plugin Name: AdRotate Professional
Plugin URI: http://www.adrotateplugin.com
Description: The very best and most convenient way to publish your ads.
Author: Arnan de Gans of AJdG Solutions
Version: 3.9.8
Author URI: http://www.ajdg.net
License: Limited License (See the readme.html in your account on adrotateplugin.com)
*/

/*  
Copyright 2010-2014 Arnan de Gans - AJdG Solutions (email : info@ajdg.net)
*/

/*--- AdRotate values ---------------------------------------*/
define("ADROTATE_DISPLAY", '3.9.8 Professional');
define("ADROTATE_VERSION", 370);
define("ADROTATE_DB_VERSION", 39);
define("ADROTATE_FOLDER", 'adrotate-pro');
/*-----------------------------------------------------------*/

/*--- Load Files --------------------------------------------*/
include_once(WP_CONTENT_DIR.'/plugins/'.ADROTATE_FOLDER.'/adrotate-setup.php');
include_once(WP_CONTENT_DIR.'/plugins/'.ADROTATE_FOLDER.'/adrotate-manage-publisher.php');
include_once(WP_CONTENT_DIR.'/plugins/'.ADROTATE_FOLDER.'/adrotate-manage-advertiser.php');
include_once(WP_CONTENT_DIR.'/plugins/'.ADROTATE_FOLDER.'/adrotate-functions.php');
include_once(WP_CONTENT_DIR.'/plugins/'.ADROTATE_FOLDER.'/adrotate-statistics.php');
include_once(WP_CONTENT_DIR.'/plugins/'.ADROTATE_FOLDER.'/adrotate-csv.php');
include_once(WP_CONTENT_DIR.'/plugins/'.ADROTATE_FOLDER.'/adrotate-output.php');
include_once(WP_CONTENT_DIR.'/plugins/'.ADROTATE_FOLDER.'/adrotate-widget.php');
/*-----------------------------------------------------------*/

/*--- Check and Load config ---------------------------------*/
load_plugin_textdomain('adrotate', false, basename( dirname( __FILE__ ) ) . '/language' );
adrotate_check_config();
$adrotate_config = get_option('adrotate_config');
$adrotate_crawlers = get_option('adrotate_crawlers');
$adrotate_roles = get_option('adrotate_roles');
$adrotate_version = get_option("adrotate_version");
$adrotate_db_version = get_option("adrotate_db_version");
$adrotate_debug = get_option("adrotate_debug");
$adrotate_advert_status = get_option("adrotate_advert_status");
/*-----------------------------------------------------------*/

/*--- Core --------------------------------------------------*/
register_activation_hook(__FILE__, 'adrotate_activate');
register_deactivation_hook(__FILE__, 'adrotate_deactivate');
register_uninstall_hook(__FILE__, 'adrotate_uninstall');
add_action('adrotate_ad_notification', 'adrotate_mail_notifications');
add_action('adrotate_clean_trackerdata', 'adrotate_clean_trackerdata');
add_action('adrotate_evaluate_ads', 'adrotate_evaluate_ads');
add_action('widgets_init', create_function('', 'return register_widget("adrotate_widgets");'));
/*-----------------------------------------------------------*/

/*--- Front end ---------------------------------------------*/
if(!is_admin()) {
	$adrotate_geo = adrotate_geolocation();
	add_shortcode('adrotate', 'adrotate_shortcode');
	add_action("wp_enqueue_scripts", 'adrotate_custom_scripts');
	add_action('wp_head', 'adrotate_custom_head');
	add_filter('the_content', 'adrotate_inject_posts');
}
if($adrotate_config['adminbar'] == 'Y') add_action('admin_bar_menu', 'adrotate_adminmenu', 100);
/*-----------------------------------------------------------*/

if(is_admin()) {
	/*--- Back end ----------------------------------------------*/
	add_action('admin_init', 'adrotate_check_upgrade');
	add_action('admin_menu', 'adrotate_dashboard');
	add_action("admin_enqueue_scripts", 'adrotate_dashboard_scripts');
	add_action("admin_print_styles", 'adrotate_dashboard_styles');
	add_action('admin_notices', 'adrotate_notifications_dashboard');
	if(is_multisite() AND adrotate_is_networked()) {
		add_action('network_admin_menu', 'adrotate_network_dashboard');
	}
	/*-----------------------------------------------------------*/

	/*--- Update API --------------------------------------------*/
	include_once(WP_CONTENT_DIR.'/plugins/'.ADROTATE_FOLDER.'/library/license-functions.php');
	include_once(WP_CONTENT_DIR.'/plugins/'.ADROTATE_FOLDER.'/library/license-api.php');

	$adrotate_license_domain = 'http://www.adrotateplugin.com';
	$adrotate_api_url = $adrotate_license_domain.'/updates/1.0/';
	add_action('admin_init', 'adrotate_licensed_update');

	if(isset($_POST['adrotate_license_support_submit'])) 		add_action('init', 'adrotate_license_mail_support');
	if(isset($_POST['adrotate_license_activate'])) 				add_action('init', 'adrotate_license_activate');
	if(isset($_POST['adrotate_license_deactivate'])) 			add_action('init', 'adrotate_license_deactivate');
	if(isset($_POST['adrotate_license_network_activate'])) 		add_action('init', 'adrotate_license_activate');
	if(isset($_POST['adrotate_license_network_deactivate'])) 	add_action('init', 'adrotate_license_deactivate');
	if(isset($_POST['adrotate_license_reset'])) 				add_action('init', 'adrotate_license_reset');
	/*-----------------------------------------------------------*/
}

/*--- Internal redirects ------------------------------------*/
if(isset($_POST['adrotate_ad_submit'])) 				add_action('init', 'adrotate_insert_input');
if(isset($_POST['adrotate_group_submit'])) 				add_action('init', 'adrotate_insert_group');
if(isset($_POST['adrotate_schedule_submit'])) 			add_action('init', 'adrotate_insert_schedule');
if(isset($_POST['adrotate_action_submit'])) 			add_action('init', 'adrotate_request_action');
if(isset($_POST['adrotate_disabled_action_submit']))	add_action('init', 'adrotate_request_action');
if(isset($_POST['adrotate_error_action_submit']))		add_action('init', 'adrotate_request_action');
if(isset($_POST['adrotate_support_submit'])) 			add_action('init', 'adrotate_mail_support');
if(isset($_POST['adrotate_options_submit'])) 			add_action('init', 'adrotate_options_submit');
if(isset($_POST['adrotate_request_submit'])) 			add_action('init', 'adrotate_mail_message');
if(isset($_POST['adrotate_notification_test_submit'])) 	add_action('init', 'adrotate_mail_test');
if(isset($_POST['adrotate_advertiser_test_submit'])) 	add_action('init', 'adrotate_mail_test');
if(isset($_POST['adrotate_role_add_submit']))			add_action('init', 'adrotate_prepare_roles');
if(isset($_POST['adrotate_role_remove_submit'])) 		add_action('init', 'adrotate_prepare_roles');
if(isset($_POST['adrotate_db_optimize_submit'])) 		add_action('init', 'adrotate_optimize_database');
if(isset($_POST['adrotate_db_cleanup_submit'])) 		add_action('init', 'adrotate_cleanup_database');
if(isset($_POST['adrotate_evaluate_submit'])) 			add_action('init', 'adrotate_prepare_evaluate_ads');
if(isset($_POST['adrotate_import']))		 			add_action('init', 'adrotate_import_ads');
if(isset($_POST['adrotate_export_submit'])) 			add_action('init', 'adrotate_export_stats');
/*--- Advertiser redirects ----------------------------------*/
if(isset($_POST['adrotate_advertiser_ad_submit'])) 		add_action('init', 'adrotate_advertiser_insert_input');
/*-----------------------------------------------------------*/

/*-------------------------------------------------------------
 Name:      adrotate_dashboard

 Purpose:   Add pages to admin menus
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_dashboard() {
	global $adrotate_config;

	add_object_page('AdRotate', 'AdRotate', 'adrotate_ad_manage', 'adrotate', 'adrotate_info');
	add_submenu_page('adrotate', 'AdRotate > '.__('General Info', 'adrotate'), __('General Info', 'adrotate'), 'adrotate_ad_manage', 'adrotate', 'adrotate_info');
	add_submenu_page('adrotate', 'AdRotate > '.__('Manage Ads', 'adrotate'), __('Manage Ads', 'adrotate'), 'adrotate_ad_manage', 'adrotate-ads', 'adrotate_manage');
	add_submenu_page('adrotate', 'AdRotate > '.__('Manage Groups', 'adrotate'), __('Manage Groups', 'adrotate'), 'adrotate_group_manage', 'adrotate-groups', 'adrotate_manage_group');
	add_submenu_page('adrotate', 'AdRotate > '.__('Manage Schedules', 'adrotate'), __('Manage Schedules', 'adrotate'), 'adrotate_schedule_manage', 'adrotate-schedules', 'adrotate_manage_schedules');
	add_submenu_page('adrotate', 'AdRotate > '.__('Manage Blocks', 'adrotate'), __('Manage Blocks', 'adrotate'), 'adrotate_block_manage', 'adrotate-blocks', 'adrotate_manage_block');
	if($adrotate_config['enable_advertisers'] == 'Y' AND $adrotate_config['enable_editing'] == 'Y') {
		add_submenu_page('adrotate', 'AdRotate > '.__('Moderate', 'adrotate'), __('Moderate Adverts', 'adrotate'), 'adrotate_moderate', 'adrotate-moderate', 'adrotate_moderate');
	}
	add_submenu_page('adrotate', 'AdRotate > '.__('Global Reports', 'adrotate'), __('Global Reports', 'adrotate'), 'adrotate_global_report', 'adrotate-global-report', 'adrotate_global_report');
	if(!adrotate_is_networked()) {
		add_submenu_page('adrotate', 'AdRotate > '.__('Ticket Support', 'adrotate'), __('Ticket Support', 'adrotate'), 'manage_options', 'adrotate-support', 'adrotate_request_support');
	}
	add_submenu_page('adrotate', 'AdRotate > '.__('Settings', 'adrotate'), __('Settings', 'adrotate'), 'manage_options', 'adrotate-settings', 'adrotate_options');
	
	if($adrotate_config['enable_advertisers'] == 'Y') {
		add_object_page(__('Advertiser', 'adrotate'), __('Advertiser', 'adrotate'), 'adrotate_advertiser', 'adrotate-advertiser', 'adrotate_advertiser');
		add_submenu_page('adrotate-advertiser', 'AdRotate > '.__('Advertiser', 'adrotate'), __('Advertiser', 'adrotate'), 'adrotate_advertiser', 'adrotate-advertiser', 'adrotate_advertiser');
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_network_dashboard

 Purpose:   Add pages to admin menus if AdRotate is network activated
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_network_dashboard() {
	add_submenu_page('settings.php', 'AdRotate > '.__('License', 'adrotate'), 'AdRotate '.__('License', 'adrotate'), 'manage_network', 'adrotate-license', 'adrotate_network_license');
	add_submenu_page('settings.php', 'AdRotate > '.__('Support', 'adrotate'), 'AdRotate '.__('Support', 'adrotate'), 'manage_network', 'adrotate-support', 'adrotate_request_support');
}

/*-------------------------------------------------------------
 Name:      adrotate_info

 Purpose:   Admin general info page
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_info() {
	global $wpdb, $adrotate_advert_status;

	$a = get_option('adrotate_activate');
	?>

	<div class="wrap">
		<h2><?php _e('AdRotate Info', 'adrotate'); ?></h2>

		<br class="clear" />

		<?php include("dashboard/adrotate-info.php"); ?>

		<br class="clear" />
	</div>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_manage

 Purpose:   Admin management page
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_manage() {
	global $wpdb, $current_user, $userdata, $blog_id, $adrotate_config, $adrotate_debug;

	$status = $file = $view = $ad_edit_id = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['file'])) $file = esc_attr($_GET['file']);
	if(isset($_GET['view'])) $view = esc_attr($_GET['view']);
	if(isset($_GET['ad'])) $ad_edit_id = esc_attr($_GET['ad']);
	$now 			= adrotate_now();
	$today 			= adrotate_date_start('day');
	$in2days 		= $now + 172800;
	$in7days 		= $now + 604800;
	$in84days 		= $now + 7257600;

	if(isset($_GET['month']) AND isset($_GET['year'])) {
		$month = esc_attr($_GET['month']);
		$year = esc_attr($_GET['year']);
	} else {
		$month = date("m");
		$year = date("Y");
	}
	$monthstart = mktime(0, 0, 0, $month, 1, $year);
	$monthend = mktime(0, 0, 0, $month+1, 0, $year);	
	?>
	<div class="wrap">
		<h2><?php _e('Ad Management', 'adrotate'); ?></h2>

		<?php if($status > 0) adrotate_status($status, array('file' => $file)); ?>

		<?php if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."adrotate';") AND $wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."adrotate_groups';") AND $wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."adrotate_schedule';") AND $wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."adrotate_linkmeta';")) { ?>

			<?php
			$allbanners = $wpdb->get_results("SELECT `id`, `title`, `type`, `tracker`, `weight`, `cbudget`, `ibudget`, `crate`, `irate` FROM `".$wpdb->prefix."adrotate` WHERE `type` = 'active' OR `type` = 'error' OR `type` = 'expired' OR `type` = '2days' OR `type` = '7days' OR `type` = 'disabled' ORDER BY `sortorder` ASC, `id` ASC;");
			
			$activebanners = $errorbanners = $disabledbanners = false;
			foreach($allbanners as $singlebanner) {
				$advertiser = '';
				$starttime = $stoptime = 0;
				$starttime = $wpdb->get_var("SELECT `starttime` FROM `".$wpdb->prefix."adrotate_schedule`, `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = '".$singlebanner->id."' AND `schedule` = `".$wpdb->prefix."adrotate_schedule`.`id` ORDER BY `starttime` ASC LIMIT 1;");
				$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `".$wpdb->prefix."adrotate_schedule`, `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = '".$singlebanner->id."' AND  `schedule` = `".$wpdb->prefix."adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");
				if($adrotate_config['enable_advertisers'] == 'Y') {
					$advertiser = $wpdb->get_var("SELECT `user_login` FROM `".$wpdb->prefix."adrotate_linkmeta`, `".$wpdb->prefix."users` WHERE `".$wpdb->prefix."users`.`id` = `".$wpdb->prefix."adrotate_linkmeta`.`user` AND `ad` = '".$singlebanner->id."' AND `group` = '0' AND `block` = '0' AND `schedule` = '0' LIMIT 1;");
				}

				$type = $singlebanner->type;
				if($type == 'active' AND $stoptime <= $now) $type = 'expired'; 
				if($type == 'active' AND $stoptime <= $in2days) $type = '2days';
				if($type == 'active' AND $stoptime <= $in7days) $type = '7days';
				if(($singlebanner->crate > 0 AND $singlebanner->cbudget < 1) OR ($singlebanner->irate > 0 AND $singlebanner->ibudget < 1)) $type = 'expired';

				if($type == 'active' OR $type == '7days') {
					$activebanners[$singlebanner->id] = array(
						'id' => $singlebanner->id,
						'title' => $singlebanner->title,
						'advertiser' => $advertiser,
						'type' => $type,
						'tracker' => $singlebanner->tracker,
						'weight' => $singlebanner->weight,
						'firstactive' => $starttime,
						'lastactive' => $stoptime
					);
				}
				
				if($type == 'error' OR $type == 'expired' OR $type == '2days') {
					$errorbanners[$singlebanner->id] = array(
						'id' => $singlebanner->id,
						'title' => $singlebanner->title,
						'advertiser' => $advertiser,
						'type' => $type,
						'tracker' => $singlebanner->tracker,
						'weight' => $singlebanner->weight,
						'firstactive' => $starttime,
						'lastactive' => $stoptime
					);
				}
				
				if($type == 'disabled') {
					$disabledbanners[$singlebanner->id] = array(
						'id' => $singlebanner->id,
						'title' => $singlebanner->title,
						'advertiser' => $advertiser,
						'type' => $type,
						'tracker' => $singlebanner->tracker,
						'weight' => $singlebanner->weight,
						'firstactive' => $starttime,
						'lastactive' => $stoptime
					);
				}
			}
			?>
			
			<div class="tablenav">
				<div class="alignleft actions">
					<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-ads&view=manage');?>"><?php _e('Manage', 'adrotate'); ?></a> | 
					<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-ads&view=addnew');?>"><?php _e('Add New', 'adrotate'); ?></a> 
					<?php if($ad_edit_id) { ?>
					| <a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-ads&view=report&ad='.$ad_edit_id);?>"><?php _e('Report', 'adrotate'); ?></a>
					<?php } ?>
					| <a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-ads&view=import');?>"><?php _e('Import', 'adrotate'); ?></a>
				</div>
			</div>

	    	<?php 
	    	if ($view == "" OR $view == "manage") {
				// Show list of errorous ads if any			
				if ($errorbanners) {
					include("dashboard/publisher/adrotate-ads-main-error.php");
				}
		
				include("dashboard/publisher/adrotate-ads-main.php");
	
				// Show disabled ads, if any
				if ($disabledbanners) {
					include("dashboard/publisher/adrotate-ads-main-disabled.php");
				}
		   	} else if($view == "addnew" OR $view == "edit") { 
				include("dashboard/publisher/adrotate-ads-edit.php");
			} else if($view == "report") {
				include("dashboard/publisher/adrotate-ads-report.php");
			} else if($view == "import") {
				include("dashboard/publisher/adrotate-ads-import.php");
			}
		} else {
			echo adrotate_error('db_error');
		}
		?>
		<br class="clear" />

		<?php adrotate_credits(); ?>

		<br class="clear" />
	</div>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_manage_group

 Purpose:   Manage groups
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_manage_group() {
	global $wpdb, $adrotate_config, $adrotate_debug;

	$status = $view = $group_edit_id = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['view'])) $view = esc_attr($_GET['view']);
	if(isset($_GET['group'])) $group_edit_id = esc_attr($_GET['group']);

	if(isset($_GET['month']) AND isset($_GET['year'])) {
		$month = esc_attr($_GET['month']);
		$year = esc_attr($_GET['year']);
	} else {
		$month = date("m");
		$year = date("Y");
	}
	$monthstart = mktime(0, 0, 0, $month, 1, $year);
	$monthend = mktime(0, 0, 0, $month+1, 0, $year);	
	$today = adrotate_date_start('day');
	?>
	<div class="wrap">
		<h2><?php _e('Group Management', 'adrotate'); ?></h2>

		<?php if($status > 0) adrotate_status($status); ?>

		<?php if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."adrotate_groups';") AND $wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."adrotate_linkmeta';")) { ?>
			<div class="tablenav">
				<div class="alignleft actions">
					<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-groups&view=manage');?>"><?php _e('Manage', 'adrotate'); ?></a> | 
					<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-groups&view=addnew');?>"><?php _e('Add New', 'adrotate'); ?></a>
					<?php if($group_edit_id) { ?>
					| <a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-groups&view=report&group='.$group_edit_id);?>"><?php _e('Report', 'adrotate'); ?></a>
					<?php } ?>
				</div>
			</div>

	    	<?php if ($view == "" OR $view == "manage") { ?>

				<?php
				include("dashboard/publisher/adrotate-groups-main.php");
				?>

		   	<?php } else if($view == "addnew" OR $view == "edit") { ?>

				<?php
				include("dashboard/publisher/adrotate-groups-edit.php");
				?>

		   	<?php } else if($view == "report") { ?>

				<?php
				include("dashboard/publisher/adrotate-groups-report.php");
				?>

		   	<?php } ?>
		<?php } else { ?>
			<?php echo adrotate_error('db_error'); ?>
		<?php }	?>
		<br class="clear" />

		<?php adrotate_credits(); ?>

		<br class="clear" />
	</div>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_manage_schedules

 Purpose:   Manage schedules for ads
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_manage_schedules() {
	global $wpdb, $adrotate_config, $adrotate_debug;

	$status = $view = $schedule_edit_id = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['view'])) $view = esc_attr($_GET['view']);
	if(isset($_GET['schedule'])) $schedule_edit_id = esc_attr($_GET['schedule']);

	$now 			= adrotate_now();
	$today 			= adrotate_date_start('day');
	$in2days 		= $now + 172800;
	$in84days 		= $now + 7257600;
	?>
	<div class="wrap">
		<h2><?php _e('Schedule Management', 'adrotate'); ?></h2>

		<?php if($status > 0) adrotate_status($status); ?>

		<?php 
		if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."adrotate_schedule';") AND $wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."adrotate_linkmeta';")) {
			$allschedules = $wpdb->get_results("SELECT `id`, `name`, `starttime`, `stoptime`, `maxclicks`, `maximpressions` FROM `".$wpdb->prefix."adrotate_schedule` WHERE `name` != '' ORDER BY `id` ASC;");
			
			$schedules = false;
			foreach($allschedules as $singleschedule) {
				$schedules[$singleschedule->id] = array(
					'id' => $singleschedule->id,
					'name' => $singleschedule->name,
					'start' => $singleschedule->starttime,
					'end' => $singleschedule->stoptime,
					'clicks' => $singleschedule->maxclicks,
					'impressions' => $singleschedule->maximpressions
				);
			}
			?>
			<div class="tablenav">
				<div class="alignleft actions">
					<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-schedules&view=manage');?>"><?php _e('Manage', 'adrotate'); ?></a> | 
					<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-schedules&view=addnew');?>"><?php _e('Add New', 'adrotate'); ?></a>
				</div>
			</div>

	    	<?php if ($view == "" OR $view == "manage") { ?>

				<?php
				include("dashboard/publisher/adrotate-schedules-main.php");
				?>

		   	<?php } else if($view == "addnew" OR $view == "edit") { ?>
		   	
				<?php
				include("dashboard/publisher/adrotate-schedules-edit.php");
				?>

		   	<?php } ?>

		<?php } else { ?>
			<?php echo adrotate_error('db_error'); ?>
		<?php }	?>
		<br class="clear" />

		<?php adrotate_credits(); ?>

		<br class="clear" />
	</div>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_manage_block

 Purpose:   Manage blocks of ads
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_manage_block() {
	global $wpdb, $adrotate_debug;

	$status = $view = $block_edit_id = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['view'])) $view = esc_attr($_GET['view']);
	if(isset($_GET['block'])) $block_edit_id = esc_attr($_GET['block']);

	if(isset($_GET['month']) AND isset($_GET['year'])) {
		$month = esc_attr($_GET['month']);
		$year = esc_attr($_GET['year']);
	} else {
		$month = date("m");
		$year = date("Y");
	}
	$monthstart = mktime(0, 0, 0, $month, 1, $year);
	$monthend = mktime(0, 0, 0, $month+1, 0, $year);	
	$today = adrotate_date_start('day');
	?>
	<div class="wrap">
		<h2><?php _e('Block Management', 'adrotate'); ?></h2>

		<?php if($status > 0) adrotate_status($status); ?>

		<?php if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."adrotate_blocks';") AND $wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."adrotate_linkmeta';")) { ?>
			<div class="tablenav">
				<div class="alignleft actions">
					<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-blocks&view=manage');?>"><?php _e('Manage', 'adrotate'); ?></a> 
					<?php if($block_edit_id) { ?>
					| <a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-blocks&view=report&block='.$block_edit_id);?>"><?php _e('Report', 'adrotate'); ?></a> 
					<?php } ?>
				</div>
			</div>

			<div class="error"><p>Still using blocks? Blocks will be removed in a future update. Please migrate your block to a <a href="<?php echo admin_url('/admin.php?page=adrotate-groups&view=manage');?>">group</a> and set it to "Block Mode". Thank you!<br />If you need help or advise on what to do next, please <a href="http://www.adrotateplugin.com/support/forums/" target="_blank">post on the forum</a>!</p></div>

	    	<?php if ($view == "" OR $view == "manage") { ?>

				<?php
				include("dashboard/publisher/adrotate-blocks-main.php");
				?>

		   	<?php } else if($view == "addnew" OR $view == "edit") { ?>
		   	
				<?php
				include("dashboard/publisher/adrotate-blocks-edit.php");
				?>
	
		   	<?php } else if($view == "report") { ?>

				<?php
				include("dashboard/publisher/adrotate-blocks-report.php");
				?>

		   	<?php } ?>

		<?php } else { ?>
			<?php echo adrotate_error('db_error'); ?>
		<?php }	?>
		<br class="clear" />

		<?php adrotate_credits(); ?>

		<br class="clear" />
	</div>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_moderate

 Purpose:   Moderation queue
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_moderate() {
	global $wpdb, $current_user, $userdata, $adrotate_config, $adrotate_debug;

	$status = $view = $ad_edit_id = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['view'])) $view = esc_attr($_GET['view']);
	if(isset($_GET['ad'])) $ad_edit_id = esc_attr($_GET['ad']);
	$now 			= adrotate_now();
	$today 			= adrotate_date_start('day');
	$in2days 		= $now + 172800;
	$in7days 		= $now + 604800;
	$in84days 		= $now + 7257600;
	?>
	<div class="wrap">
		<h2><?php _e('Moderation queue', 'adrotate'); ?></h2>

		<?php if($status > 0) adrotate_status($status); ?>

		<?php if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."adrotate';") AND $wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."adrotate_groups';") AND $wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."adrotate_schedule';") AND $wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."adrotate_linkmeta';")) { ?>

			<?php
			$allbanners = $wpdb->get_results("SELECT `id`, `title`, `type`, `tracker`, `weight` FROM `".$wpdb->prefix."adrotate` WHERE `type` = 'queue' OR `type` = 'reject' ORDER BY `id` ASC;");
			
			$queued = $rejected = false;
			foreach($allbanners as $singlebanner) {
				
				$starttime = $stoptime = 0;
				$starttime = $wpdb->get_var("SELECT `starttime` FROM `".$wpdb->prefix."adrotate_schedule`, `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = '".$singlebanner->id."' AND `schedule` = `".$wpdb->prefix."adrotate_schedule`.`id` ORDER BY `starttime` ASC LIMIT 1;");
				$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `".$wpdb->prefix."adrotate_schedule`, `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = '".$singlebanner->id."' AND `schedule` = `".$wpdb->prefix."adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");
				
				if($singlebanner->type == 'queue') {
					$queued[$singlebanner->id] = array(
						'id' => $singlebanner->id,
						'title' => $singlebanner->title,
						'type' => $singlebanner->type,
						'tracker' => $singlebanner->tracker,
						'weight' => $singlebanner->weight,
						'firstactive' => $starttime,
						'lastactive' => $stoptime
					);
				}
				
				if($singlebanner->type == 'reject') {
					$rejected[$singlebanner->id] = array(
						'id' => $singlebanner->id,
						'title' => $singlebanner->title,
						'type' => $singlebanner->type,
						'tracker' => $singlebanner->tracker,
						'weight' => $singlebanner->weight,
						'firstactive' => $starttime,
						'lastactive' => $stoptime
					);
				}
			}
			?>

	    	<?php
	    	if ($view == "" OR $view == "manage") {
				// Show list of queued ads			
				include("dashboard/publisher/adrotate-moderation-queue.php");
	
				// Show rejected ads, if any
				if($rejected) {
					include("dashboard/publisher/adrotate-moderation-rejected.php");
				}
			} else if($view == "message") {
				$wpnonceaction = 'adrotate_moderate_'.$request_id;
				if(wp_verify_nonce($_REQUEST['_wpnonce'], $wpnonceaction)) {
					include("dashboard/publisher/adrotate-moderation-message.php");
				} else {
					adrotate_nonce_error();
					exit;
				}
			}
		} else {
			echo adrotate_error('db_error');
		}
		?>
		<br class="clear" />

		<?php adrotate_credits(); ?>

		<br class="clear" />
	</div>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_advertiser

 Purpose:   Advertiser page
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_advertiser() {
	global $wpdb, $current_user, $adrotate_config, $adrotate_debug;
		
	get_currentuserinfo();
	
	$status = $view = $ad_edit_id = $request = $request_id = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['view'])) $view = esc_attr($_GET['view']);
	if(isset($_GET['ad'])) $ad_edit_id = esc_attr($_GET['ad']);
	if(isset($_GET['request'])) $request = esc_attr($_GET['request']);
	if(isset($_GET['id'])) $request_id = esc_attr($_GET['id']);
	$now 			= adrotate_now();
	$today 			= adrotate_date_start('day');
	$in2days 		= $now + 172800;
	$in7days 		= $now + 604800;
	$in84days 		= $now + 7257600;

	if(isset($_GET['month']) AND isset($_GET['year'])) {
		$month = esc_attr($_GET['month']);
		$year = esc_attr($_GET['year']);
	} else {
		$month = date("m");
		$year = date("Y");
	}
	$monthstart = mktime(0, 0, 0, $month, 1, $year);
	$monthend = mktime(0, 0, 0, $month+1, 0, $year);	
	?>
	<div class="wrap">
	  	<h2><?php _e('Advertiser', 'adrotate'); ?></h2>

		<?php if($status > 0) adrotate_status($status); ?>

		<div class="tablenav">
			<div class="alignleft actions">
				<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-advertiser&view=manage');?>"><?php _e('Manage', 'adrotate'); ?></a>
				<?php if($adrotate_config['enable_editing'] == 'Y') { ?>
				 | <a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-advertiser&view=addnew');?>"><?php _e('Add New', 'adrotate'); ?></a> 
				<?php  } ?>
			</div>
		</div>

		<?php 
		$wpnonceaction = 'adrotate_email_advertiser_'.$request_id;
		if($view == "" OR $view == "manage") {
			
			$ads = $wpdb->get_results($wpdb->prepare("SELECT `ad` FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `group` = 0 AND `block` = 0 AND `user` = %d ORDER BY `ad` ASC;", $current_user->ID));

			if($ads) {
				$activebanners = $queuebanners = $disabledbanners = false;
				foreach($ads as $ad) {
					$banner = $wpdb->get_row("SELECT `id`, `title`, `type` FROM `".$wpdb->prefix."adrotate` WHERE (`type` = 'active' OR `type` = '2days' OR `type` = '7days' OR `type` = 'disabled' OR `type` = 'error' OR `type` = 'a_error' OR `type` = 'expired' OR `type` = 'queue' OR `type` = 'reject') AND `id` = '".$ad->ad."';");

					// Skip if no ad
					if(!$banner) continue;
					
					$starttime = $stoptime = 0;
					$starttime = $wpdb->get_var("SELECT `starttime` FROM `".$wpdb->prefix."adrotate_schedule`, `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = '".$banner->id."' AND `schedule` = `".$wpdb->prefix."adrotate_schedule`.`id` ORDER BY `starttime` ASC LIMIT 1;");
					$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `".$wpdb->prefix."adrotate_schedule`, `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = '".$banner->id."' AND `schedule` = `".$wpdb->prefix."adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");
	
					$type = $banner->type;
					if($type == 'active' AND $stoptime <= $in7days) $type = '7days';
					if($type == 'active' AND $stoptime <= $in2days) $type = '2days';
					if($type == 'active' AND $stoptime <= $now) $type = 'expired'; 

					if($type == 'active' OR $type == '2days' OR $type == '7days' OR $type == 'expired') {
						$activebanners[$banner->id] = array(
							'id' => $banner->id,
							'title' => $banner->title,
							'type' => $type,
							'firstactive' => $starttime,
							'lastactive' => $stoptime
						);
					}
	
					if($type == 'disabled') {
						$disabledbanners[$banner->id] = array(
							'id' => $banner->id,
							'title' => $banner->title,
							'type' => $type
						);
					}

					if($type == 'queue' OR $type == 'reject' OR $type == 'error' OR $type == 'a_error') {
						$queuebanners[$banner->id] = array(
							'id' => $banner->id,
							'title' => $banner->title,
							'type' => $type
						);
					}
				}
				
				// Show active ads, if any
				if($activebanners) {
					include("dashboard/advertiser/adrotate-main.php");
				}

				// Show disabled ads, if any
				if($disabledbanners) {
					include("dashboard/advertiser/adrotate-main-disabled.php");
				}

				// Show queued ads, if any
				if($queuebanners) {
					include("dashboard/advertiser/adrotate-main-queue.php");
				}

				// Gather data for summary report
				$summary = adrotate_prepare_advertiser_report($current_user->ID, $activebanners);
				include("dashboard/advertiser/adrotate-main-summary.php");

			} else {
				?>
				<table class="widefat" style="margin-top: .5em">
					<thead>
						<tr>
							<th><?php _e('Notice', 'adrotate'); ?></th>
						</tr>
					</thead>
					<tbody>
					    <tr>
							<td><?php _e('No ads for user.', 'adrotate'); ?></td>
						</tr>
					</tbody>
				</table>
				<?php
			}
		} else if($view == "addnew" OR $view == "edit") { 

			include("dashboard/advertiser/adrotate-edit.php");

		} else if($view == "report") { 

			include("dashboard/advertiser/adrotate-report.php");

		} else if($view == "message") {

			if(wp_verify_nonce($_REQUEST['_wpnonce'], $wpnonceaction)) {
				include("dashboard/advertiser/adrotate-message.php");
			} else {
				adrotate_nonce_error();
				exit;
			}

		}
		?>
		<br class="clear" />

		<?php adrotate_user_notice(); ?>

		<br class="clear" />
	</div>
<?php 
}

/*-------------------------------------------------------------
 Name:      adrotate_global_report

 Purpose:   Admin statistics page
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_global_report() {
	global $wpdb, $adrotate_debug;
	
	$status = $corrected = $converted = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);

	if(isset($_GET['month']) AND isset($_GET['year'])) {
		$month = esc_attr($_GET['month']);
		$year = esc_attr($_GET['year']);
	} else {
		$month = date("m");
		$year = date("Y");
	}
	$monthstart = mktime(0, 0, 0, $month, 1, $year);
	$monthend = mktime(0, 0, 0, $month+1, 0, $year);	
	$today = adrotate_date_start('day');

	$adrotate_stats = adrotate_prepare_global_report();
	
	if($adrotate_stats['tracker'] > 0 OR $adrotate_stats['clicks'] > 0) {
		$clicks = round($adrotate_stats['clicks'] / $adrotate_stats['tracker'], 2);
	} else { 
		$clicks = 0; 
	}
	
	// Get Click Through Rate
	$ctr = adrotate_ctr($adrotate_stats['clicks'], $adrotate_stats['impressions']);						
?>
	<div class="wrap">
	  	<h2><?php _e('Statistics', 'adrotate'); ?></h2>

		<?php if($status > 0) adrotate_status($status); ?>

		<?php include("dashboard/publisher/adrotate-reports-global.php"); ?>
		<br class="clear" />

		<?php adrotate_credits(); ?>

		<br class="clear" />
	</div>
<?php 
}

/*-------------------------------------------------------------
 Name:      adrotate_request_support

 Purpose:   Admin dashboard for beta releases
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_request_support() {
	global $wpdb, $current_user;
	
	$status = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	$a = get_option('adrotate_activate');

	$user = get_userdata($current_user->ID); 
	if(strlen($user->first_name) < 1) $firstname = $user->user_login;
		else $firstname = $user->first_name;
	if(strlen($user->last_name) < 1) $lastname = ''; 
		else $lastname = ' '.$user->last_name;
	?>
	
	<div class="wrap">
	  	<h2><?php _e('Support request', 'adrotate'); ?></h2>

		<?php if($a['status'] == 1) { ?>

			<?php if($status > 0) adrotate_status($status); ?>
		
			<form name="request" id="post" method="post" action="admin.php?page=adrotate-support">
				<?php wp_nonce_field('adrotate_nonce_support_request','adrotate_nonce_support'); ?>
				<input type="hidden" name="adrotate_updater_username" value="<?php echo $firstname." ".$lastname;?>" />
				<input type="hidden" name="adrotate_updater_email" value="<?php echo $user->user_email;?>" />
				<input type="hidden" name="adrotate_updater_version" value="<?php echo ADROTATE_DISPLAY;?>" />
			
				<p><strong><?php _e('Tell me about...', 'adrotate'); ?></strong></p>
				<p>&raquo; <?php _e('What went wrong? (if anything)', 'adrotate'); ?><br />&raquo; <?php _e('What are you trying to do?', 'adrotate'); ?><br />&raquo; <?php _e('Include error messages and/or relevant information.', 'adrotate'); ?><br />&raquo; <?php _e('Try to remember steps or actions you took that might have caused the problem.', 'adrotate'); ?></p>
				<p><em><?php _e('I can only help you if you tell me about your wishes and/or questions!', 'adrotate'); ?><br /><?php _e('Please use english only!', 'adrotate'); ?></em></p>
			
				<p><label for="adrotate_updater_subject"><strong><?php _e('Title / Subject:', 'adrotate'); ?></strong><br /><input tabindex="1" name="adrotate_updater_subject" type="text" class="search-input" size="80" value="" autocomplete="off" /></label></p>
				<p><label for="adrotate_updater_message"><strong><?php _e('Problem description / Question:', 'adrotate'); ?></strong><br /><textarea tabindex="2" name="adrotate_updater_message" cols="100" rows="14"></textarea></label></p>
			
				<p><strong><?php _e('When you send this form the following data will be submitted:', 'adrotate'); ?></strong></p>
				<p><em><?php _e('Your name, Account email address, Your website url, WordPress version and Language.', 'adrotate'); ?><br /><?php _e('We need this information so we know who sent the report what sort of site we can expect.', 'adrotate'); ?><br /><?php _e('This information is treated as confidential and is mandatory.', 'adrotate'); ?></em></p>
			
				<p class="submit">
					<input tabindex="3" type="submit" name="adrotate_license_support_submit" class="button-primary" value="<?php _e('Email Ticket', 'adrotate'); ?>" />
					<a href="admin.php?page=adrotate-support" class="button"><?php _e('Cancel', 'adrotate'); ?></a>
				</p>
			
			</form>

		<?php } else { ?>
	
			<h3><?php _e('Unregistered copy', 'adrotate'); ?></h3>
			<p><?php _e('You can get ticket support and updates after you\'ve activated your license.', 'adrotate'); ?></p>
			<p class="submit">
				<?php if(adrotate_is_networked()) { ?>
					<a href="settings.php?page=adrotate-license" class="button-primary"><?php _e('Register License', 'adrotate'); ?></a>
				<?php } else { ?>
					<a href="admin.php?page=adrotate-settings" class="button-primary"><?php _e('Register License', 'adrotate'); ?></a>				
				<?php } ?>
			</p>
						
		<?php }	?>
	</div>
<?php 
}

/*-------------------------------------------------------------
 Name:      adrotate_options

 Purpose:   Admin options page
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_options() {
	global $wpdb, $wp_roles;

	$adrotate_crawlers	= '';
	$adrotate_config 			= get_option('adrotate_config');
	$adrotate_activate 			= get_option('adrotate_activate');
	$adrotate_crawlers 			= get_option('adrotate_crawlers');
	$adrotate_roles				= get_option('adrotate_roles');
	$adrotate_debug				= get_option('adrotate_debug');
	$adrotate_version			= get_option('adrotate_version');
	$adrotate_db_version		= get_option('adrotate_db_version');
	$adrotate_hide_license		= get_option('adrotate_hide_license');
	$adrotate_advert_status		= get_option("adrotate_advert_status");
	$adrotate_is_networked		= adrotate_is_networked();
	$adrotate_geo 				= adrotate_geolocation();

	$crawlers 			= implode(', ', $adrotate_crawlers);
	$notification_mails	= implode(', ', $adrotate_config['notification_email']);
	$advertiser_mails	= implode(', ', $adrotate_config['advertiser_email']);

	$status = $error = $corrected = $converted = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['error'])) $error = esc_attr($_GET['error']);

	$converted = base64_decode($converted);
	$adevaluate = wp_next_scheduled('adrotate_evaluate_ads');
	$adschedule = wp_next_scheduled('adrotate_ad_notification');
	$adtracker = wp_next_scheduled('adrotate_clean_trackerdata');
?>
	<div class="wrap">
	  	<h2><?php _e('AdRotate Settings', 'adrotate'); ?></h2>

		<?php if($status > 0) adrotate_status($status, array('error' => $error)); ?>
		
	  	<form name="settings" id="post" method="post" action="admin.php?page=adrotate-settings">

			<?php wp_nonce_field('adrotate_email_test','adrotate_nonce'); ?>
			<?php wp_nonce_field('adrotate_settings','adrotate_nonce_settings'); ?>
			<?php wp_nonce_field('adrotate_license','adrotate_nonce_license'); ?>

			<h3><?php _e('AdRotate License', 'adrotate'); ?></h3>
			<span class="description"><?php _e('Activate your AdRotate License here to receive automated updates and enable support via the fast and personal ticket system.', 'adrotate'); ?></span>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('License Type', 'adrotate'); ?></th>
					<td>
						<?php echo ($adrotate_activate['type'] != '') ? $adrotate_activate['type'] : __('Not activated - Not eligible for support and updates.', 'adrotate'); ?>
					</td>
				</tr>
				<?php if($adrotate_hide_license == 0 AND !$adrotate_is_networked) { ?>
				<tr>
					<th valign="top"><?php _e('License Key', 'adrotate'); ?></th>
					<td>
						<input name="adrotate_license_key" type="text" class="search-input" size="50" value="<?php echo $adrotate_activate['key']; ?>" autocomplete="off" <?php echo ($adrotate_activate['status'] == 1) ? 'disabled' : ''; ?> /> <span class="description"><?php _e('You can find the license key in your order email.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('License Email', 'adrotate'); ?></th>
					<td>
						<input name="adrotate_license_email" type="text" class="search-input" size="50" value="<?php echo $adrotate_activate['email']; ?>" autocomplete="off" <?php echo ($adrotate_activate['status'] == 1) ? 'disabled' : ''; ?> /> <span class="description"><?php _e('The email address you used on adrotateplugin.com.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Hide License Details', 'adrotate'); ?></th>
					<td>
						<input type="checkbox" name="adrotate_license_hide" <?php echo ($adrotate_activate['status'] == 1) ? 'disabled' : ''; ?> /> <span class="description"><?php _e('If you have installed AdRotate Pro for a client or in a multisite setup and want to hide the License Key, Email and Mass-deactivation button (Duo, Multi and Developer License) from them.', 'adrotate'); ?></span>
					</td>
				</tr>
				<?php } ?>
				<?php if(!$adrotate_is_networked) { ?>
				<tr>
					<th valign="top">&nbsp;</th>
					<td>
						<?php if($adrotate_activate['status'] == 0) { ?>
						<input type="submit" id="post-role-submit" name="adrotate_license_activate" value="<?php _e('Activate', 'adrotate'); ?>" class="button-secondary" />
						<?php } else { ?>
						<input type="submit" id="post-role-submit" name="adrotate_license_deactivate" value="<?php _e('De-activate', 'adrotate'); ?>" class="button-secondary" />
							<?php if($adrotate_activate['type'] != 'Single' AND $adrotate_hide_license == 0) { ?>
							&nbsp;<input type="submit" id="post-role-submit" name="adrotate_license_reset" value="<?php _e('De-activate all active keys on all sites', 'adrotate'); ?>" class="button-secondary" onclick="return confirm('<?php _e('You are about to de-activate your license on ALL sites currently using your AdRotate License. This can not be reversed!', 'adrotate'); ?>\n\n<?php _e('OK to continue, CANCEL to stop.', 'adrotate'); ?>')" />
							<?php } ?>
						<?php } ?>
					</td>
				</tr>
				<?php } ?>
			</table>

			<h3><?php _e('Access Rights', 'adrotate'); ?></h3>
			<span class="description"><?php _e('Who has access to what? All but the "advertiser page" are usually for admins and moderators.', 'adrotate'); ?></span>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('Advertiser page', 'adrotate'); ?></th>
					<td>
						<select name="adrotate_advertiser">
							<?php wp_dropdown_roles($adrotate_config['advertiser']); ?>
						</select> <span class="description"><?php _e('Role to allow users/advertisers to see their advertisement page.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Global report page', 'adrotate'); ?></th>
					<td>
						<select name="adrotate_global_report">
							<?php wp_dropdown_roles($adrotate_config['global_report']); ?>
						</select> <span class="description"><?php _e('Role to review the global report.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Manage/Add/Edit adverts', 'adrotate'); ?></th>
					<td>
						<select name="adrotate_ad_manage">
							<?php wp_dropdown_roles($adrotate_config['ad_manage']); ?>
						</select> <span class="description"><?php _e('Role to see and add/edit ads.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Delete/Reset adverts', 'adrotate'); ?></th>
					<td>
						<select name="adrotate_ad_delete">
							<?php wp_dropdown_roles($adrotate_config['ad_delete']); ?>
						</select> <span class="description"><?php _e('Role to delete ads and reset stats.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Manage/Add/Edit groups', 'adrotate'); ?></th>
					<td>
						<select name="adrotate_group_manage">
							<?php wp_dropdown_roles($adrotate_config['group_manage']); ?>
						</select> <span class="description"><?php _e('Role to see and add/edit groups.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Delete groups', 'adrotate'); ?></th>
					<td>
						<select name="adrotate_group_delete">
							<?php wp_dropdown_roles($adrotate_config['group_delete']); ?>
						</select> <span class="description"><?php _e('Role to delete groups.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Manage/Add/Edit blocks', 'adrotate'); ?></th>
					<td>
						<select name="adrotate_block_manage">
							<?php wp_dropdown_roles($adrotate_config['block_manage']); ?>
						</select> <span class="description"><?php _e('Role to see and add/edit blocks.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Delete blocks', 'adrotate'); ?></th>
					<td>
						<select name="adrotate_block_delete">
							<?php wp_dropdown_roles($adrotate_config['block_delete']); ?>
						</select> <span class="description"><?php _e('Role to delete blocks.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Manage/Add/Edit schedules', 'adrotate'); ?></th>
					<td>
						<select name="adrotate_schedule_manage">
							<?php wp_dropdown_roles($adrotate_config['schedule_manage']); ?>
						</select> <span class="description"><?php _e('Role to see and add/edit schedules.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Delete schedules', 'adrotate'); ?></th>
					<td>
						<select name="adrotate_schedule_delete">
							<?php wp_dropdown_roles($adrotate_config['schedule_delete']); ?>
						</select> <span class="description"><?php _e('Role to delete schedules.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Moderate new adverts', 'adrotate'); ?></th>
					<td>
						<select name="adrotate_moderate">
							<?php wp_dropdown_roles($adrotate_config['moderate']); ?>
						</select> <span class="description"><?php _e('Role to approve ads submitted by advertisers.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Approve/Reject adverts in Moderation Queue', 'adrotate'); ?></th>
					<td>
						<select name="adrotate_moderate_approve">
							<?php wp_dropdown_roles($adrotate_config['moderate_approve']); ?>
						</select> <span class="description"><?php _e('Role to approve or reject ads submitted by advertisers.', 'adrotate'); ?></span>
					</td>
				</tr>

				<?php if($adrotate_debug['userroles'] == true) { ?>
				<tr>
					<td colspan="2">
						<?php 
						echo "<p><strong>[DEBUG] AdRotate Advertiser role enabled? (0 = no, 1 = yes)</strong><pre>"; 
						print_r($adrotate_roles); 
						echo "</pre></p>"; 
						echo "<p><strong>[DEBUG] Current User Capabilities</strong><pre>"; 
						print_r($wp_roles); 
						echo "</pre></p>"; 
						?>
					</td>
				</tr>
				<?php } ?>
			</table>

			<h3><?php _e('Advertisers', 'adrotate'); ?></h3>
			<span class="description"><?php _e('Enable advertisers so they can review and manage their own ads.', 'adrotate'); ?></span>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('Enable Advertisers', 'adrotate'); ?></th>
					<td>
						<input type="checkbox" name="adrotate_enable_advertisers" <?php if($adrotate_config['enable_advertisers'] == 'Y') { ?>checked="checked" <?php } ?> /> <span class="description"><?php _e('Allow adverts to be coupled to users (Advertisers).', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Edit/update adverts', 'adrotate'); ?></th>
					<td>
						<input type="checkbox" name="adrotate_enable_editing" <?php if($adrotate_config['enable_editing'] == 'Y') { ?>checked="checked" <?php } ?> /> <span class="description"><?php _e('Allow advertisers to add new or edit their adverts.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Advertiser role', 'adrotate'); ?></th>
					<td>
						<?php if($adrotate_roles == 0) { ?>
						<input type="submit" id="post-role-submit" name="adrotate_role_add_submit" value="<?php _e('Create Role', 'adrotate'); ?>" class="button-secondary" />
						<?php } else { ?>
						<input type="submit" id="post-role-submit" name="adrotate_role_remove_submit" value="<?php _e('Remove Role', 'adrotate'); ?>" class="button-secondary" onclick="return confirm('<?php _e('You are about to remove the AdRotate Clients role.', 'adrotate'); ?>\n\n<?php _e('This may lead to users not being able to access their ads statistics!', 'adrotate'); ?>\n\n<?php _e('OK to continue, CANCEL to stop.', 'adrotate'); ?>')" />
						<?php } ?><br />
						<span class="description"><?php _e('This role has no capabilities unless you assign them using the above options. Obviously you should use this with care.', 'adrotate'); ?><br />
						<?php _e('This type of user is NOT required to use AdRotate or any of it\'s features. It merely helps you to seperate advertisers from regular subscribers without giving them too much access to your dashboard.', 'adrotate'); ?></span>
					</td>
				</tr>
			</table>

			<?php
			if($adrotate_debug['dashboard'] == true) {
				echo "<p><strong>[DEBUG] Globalized Config</strong><pre>"; 
				print_r($adrotate_config); 
				echo "</pre></p>"; 
			}
			?>

			<h3><?php _e('Banner Folder', 'adrotate'); ?></h3>
			<span class="description"><?php _e('Activate your AdRotate License here to receive support via the fast and personal ticket system.', 'adrotate'); ?></span>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('Where are your banner ads?', 'adrotate'); ?></th>
					<td>
						<?php echo home_url(); ?>/<input name="adrotate_banner_folder" type="text" class="search-input" size="50" value="<?php echo $adrotate_config['banner_folder']; ?>" autocomplete="off" /><br />
						<span class="description"><?php _e('Set a location where your banner images will be stored. (Default: wp-content/banners/).', 'adrotate'); ?><br />
						<?php _e('To try and trick ad blockers you could set the folder to something crazy like:', 'adrotate'); ?> "/wp-content/<?php echo adrotate_rand(12); ?>/".<br />
						<?php _e("This folder will not be automatically created if it doesn't exist. AdRotate will show errors when the folder is missing.", 'adrotate'); ?></span>
					</td>
				</tr>
			</table>

			<h3><?php _e('Email Notifications', 'adrotate'); ?></h3>
			<span class="description"><?php _e('Set up who gets email notifications if ads need your attention.', 'adrotate'); ?></span>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('Notifications', 'adrotate'); ?></th>
					<td>
						<textarea name="adrotate_notification_email" cols="90" rows="3"><?php echo $notification_mails; ?></textarea><br />
						<span class="description"><?php _e('A comma separated list of email addresses. Maximum of 5 addresses. Keep this list to a minimum!', 'adrotate'); ?><br />
						<?php _e('Messages are sent once every 24 hours when needed. If this field is empty the function will be disabled.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top"><?php _e('Test', 'adrotate'); ?></th>
					<td>
						<input type="submit" name="adrotate_notification_test_submit" class="button-secondary" value="<?php _e('Test', 'adrotate'); ?>" /> 
						<span class="description"><?php _e('This sends a test notification. Before you test, for example, with a new email address. Save the options first!', 'adrotate'); ?></span>
					</td>
				</tr>
			</table>

			<h3><?php _e('Advertiser Messages', 'adrotate'); ?></h3>
			<span class="description"><?php _e('Configure who gets email from advertisers.', 'adrotate'); ?></span>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('Advertiser Messages', 'adrotate'); ?></th>
					<td>
						<textarea name="adrotate_advertiser_email" cols="90" rows="2"><?php echo $advertiser_mails; ?></textarea><br />
						<span class="description"><?php _e('Maximum of 2 addresses. Comma seperated. This field cannot be empty!', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top"><?php _e('Test', 'adrotate'); ?></th>
					<td>
						<input type="submit" name="adrotate_advertiser_test_submit" class="button-secondary" value="<?php _e('Test', 'adrotate'); ?>" /> 
						<span class="description"><?php _e('This sends a test message. Before you test, for example, with a new email address. Save the options first!', 'adrotate'); ?></span>
					</td>
				</tr>
			</table>
			
			<h3><?php _e('Statistics', 'adrotate'); ?></h3></td>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('Enable stats', 'adrotate'); ?></th>
					<td>
						<input type="checkbox" name="adrotate_enable_stats" <?php if($adrotate_config['enable_stats'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Track clicks and impressions.', 'adrotate'); ?><br /><span class="description"><?php _e('Disabling this also disables click and impression limits on schedules and disables timeframes.', 'adrotate'); ?></span><br />
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Logged in impressions', 'adrotate'); ?></th>
					<td>
						<input type="checkbox" name="adrotate_enable_loggedin_impressions" <?php if($adrotate_config['enable_loggedin_impressions'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Track impressions from logged in users (Recommended).', 'adrotate'); ?><br /><span class="description"><?php _e('Has no effect when click and impression tracking is disabled.', 'adrotate'); ?></span><br />
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Logged in clicks', 'adrotate'); ?></th>
					<td>
						<input type="checkbox" name="adrotate_enable_loggedin_clicks" <?php if($adrotate_config['enable_loggedin_clicks'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Track clicks from logged in users.', 'adrotate'); ?><br /><span class="description"><?php _e('Has no effect when click and impression tracking is disabled.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Impressions timer', 'adrotate'); ?></th>
					<td>
						<input name="adrotate_impression_timer" type="text" class="search-input" size="5" value="<?php echo $adrotate_config['impression_timer']; ?>" autocomplete="off" /> <?php _e('Seconds.', 'adrotate'); ?><br />
						<span class="description"><?php _e('Default: 10. Set to 0 to disable this timer.', 'adrotate'); ?><br /><?php _e('This number may not be empty, negative or exceed 3600 (1 hour).', 'adrotate'); ?></span>
					</td>
				</tr>
			</table>
	
			<h3><?php _e('Bot filter', 'adrotate'); ?></h3></td>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('User-Agent Filter', 'adrotate'); ?></th>
					<td>
						<textarea name="adrotate_crawlers" cols="90" rows="15"><?php echo $crawlers; ?></textarea><br />
						<span class="description"><?php _e('A comma separated list of keywords. Filter out bots/crawlers/user-agents. To prevent impressions and clicks counted on them.', 'adrotate'); ?><br />
						<?php _e('Keep in mind that this might give false positives. The word \'google\' also matches \'googlebot\', but not vice-versa. So be careful!', 'adrotate'); ?>. <?php _e('Keep your list up-to-date', 'adrotate'); ?> <a href="http://www.robotstxt.org/db.html" target="_blank">robotstxt.org/db.html</a>.<br />
						<?php _e('Use only words with alphanumeric characters, [ - _ ] are allowed too. All other characters are stripped out.', 'adrotate'); ?><br />
						<?php _e('Additionally to the list specified here, empty User-Agents are blocked as well.', 'adrotate'); ?> (<?php _e('Learn more about', 'adrotate'); ?> <a href="http://en.wikipedia.org/wiki/User_agent" title="User Agents" target="_blank"><?php _e('user-agents', 'adrotate'); ?></a>.)</span>
					</td>
				</tr>
			</table>

			<h3><?php _e('Geo Targeting', 'adrotate'); ?></h3>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('Geographic Tracking', 'adrotate'); ?></th>
					<td>
						<select name="adrotate_enable_geo">
							<option value="0" <?php if($adrotate_config['enable_geo'] == 0) { echo 'selected'; } ?>><?php _e('Disabled', 'adrotate'); ?></option>
							<option value="1" <?php if($adrotate_config['enable_geo'] == 1) { echo 'selected'; } ?>>FreegeoIP</option>
							<option value="2" <?php if($adrotate_config['enable_geo'] == 2) { echo 'selected'; } ?>>GeoSelect (With FreegeoIP as fallback)</option>
						</select><br />
						<span class="description"><strong>FreegeoIP;</strong> <?php _e('Consider making a donation to', 'adrotate'); ?> <a href="http://www.freegeoip.net" target="_blank">Freegeoip</a> <?php _e('to keep their services free!', 'adrotate'); ?><br /><strong>GeoSelect;</strong> <?php _e('This service requires a pay-as-you-go subscription using a thing called MapBytes as currency. Get your MapBytes here:', 'adrotate'); ?> <a href="https://secure.geobytes.com/buy.htm" target="_blank">secure.geobytes.com/buy.htm</a>.</span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Email/Username', 'adrotate'); ?></th>
					<td><input name="adrotate_geo_email" type="text" class="search-input" size="50" value="<?php echo $adrotate_config['geo_email']; ?>" autocomplete="off" /> <span class="description"><?php _e('Only for premium/paid geo services.', 'adrotate'); ?></span></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Password/API Key', 'adrotate'); ?></th>
					<td><input name="adrotate_geo_pass" type="text" class="search-input" size="50" value="<?php echo $adrotate_config['geo_pass']; ?>" autocomplete="off" /> <span class="description"><?php _e('Only for premium/paid geo services.', 'adrotate'); ?></span></td>
				</tr>
				<tr>
					<th valign="top">&nbsp;</th>
					<td><span class="description"><?php _e('AJdG Solutions is not affiliated with FreegeoIP or GeoSelect and will receive no commission or monetary compensation from these services.', 'adrotate'); ?></span></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Advertisers', 'adrotate'); ?></th>
					<td>
						<input type="checkbox" name="adrotate_enable_geo_advertisers" <?php if($adrotate_config['enable_geo_advertisers'] == 1) { ?>checked="checked" <?php } ?> /> <?php _e('Allow advertisers to specify where their ads will show.', 'adrotate'); ?>
					</td>
				</tr>
				<?php if($adrotate_debug['dashboard'] == true OR $adrotate_debug['geo'] == true) { ?>
				<tr>
					<td colspan="2">
						<?php
						echo "<p><strong>[DEBUG] Geo Location Data (You!)</strong><pre>";
						print_r($adrotate_geo); 
						echo "</pre></p>"; 
						?>
					</td>
				</tr>
				<?php } ?>
			</table>

			<h3><?php _e('Miscellaneous', 'adrotate'); ?></h3>
			<table class="form-table">			
				<tr>
					<th valign="top"><?php _e('Widget alignment', 'adrotate'); ?></th>
					<td><input type="checkbox" name="adrotate_widgetalign" <?php if($adrotate_config['widgetalign'] == 'Y') { ?>checked="checked" <?php } ?> /> <span class="description"><?php _e('Check this box if your widgets do not align in your themes sidebar. (Does not always help!)', 'adrotate'); ?></span></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Widget padding', 'adrotate'); ?></th>
					<td><input type="checkbox" name="adrotate_widgetpadding" <?php if($adrotate_config['widgetpadding'] == 'Y') { ?>checked="checked" <?php } ?> /> <span class="description"><?php _e('Enable this to remove the padding (blank space) around ads in widgets. (Does not always work!)', 'adrotate'); ?></span></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Admin Bar', 'adrotate'); ?></th>
					<td><input type="checkbox" name="adrotate_adminbar" <?php if($adrotate_config['adminbar'] == 'Y') { ?>checked="checked" <?php } ?> /> <span class="description"><?php _e('Enable the AdRotate Quickmenu in the Admin Bar', 'adrotate'); ?></span></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Dashboard Notifications', 'adrotate'); ?></th>
					<td><input type="checkbox" name="adrotate_dashboard_notifications" <?php if($adrotate_config['dashboard_notifications'] == 'Y') { ?>checked="checked" <?php } ?> /> <span class="description"><?php _e('Disable Dashboard Notifications about advert statuses.', 'adrotate'); ?></span></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Hide Schedules', 'adrotate'); ?></th>
					<td><input type="checkbox" name="adrotate_hide_schedules" <?php if($adrotate_config['hide_schedules'] == 'Y') { ?>checked="checked" <?php } ?> /> <span class="description"><?php _e('When editing adverts; Hide schedules that are not in use by that advert.', 'adrotate'); ?></span></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('W3 Total Caching', 'adrotate'); ?></th>
					<td><input type="checkbox" name="adrotate_w3caching" <?php if($adrotate_config['w3caching'] == 'Y') { ?>checked="checked" <?php } ?> /> <span class="description"><?php _e('Check this box if you use W3 Total Caching on your site.', 'adrotate'); ?></span></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('WP Super Cache', 'adrotate'); ?></th>
					<td><input type="checkbox" name="adrotate_supercache" <?php if($adrotate_config['supercache'] == 'Y') { ?>checked="checked" <?php } ?> /> <span class="description"><?php _e('Check this box if you use WP Super Cache on your site.', 'adrotate'); ?></span></td>
				</tr>
				<tr>
					<th valign="top">&nbsp;</th>
					<td><span class="description"><?php _e('It may take a while for the ad to start rotating. The caching plugin needs to refresh the cache. This can take up to a week if not done manually.', 'adrotate'); ?><br /><?php _e('Caching support only works for [shortcodes] and the AdRotate Widget. If you use a PHP Snippet you need to wrap your PHP in the exclusion code yourself.', 'adrotate'); ?><br /><?php _e('Check the manual of your caching plugin to see how this works.', 'adrotate'); ?></span></td>
				</tr>
			</table>

			<h3><?php _e('Javascript', 'adrotate'); ?></h3>
			<table class="form-table">			
				<tr>
					<th valign="top"><?php _e('Load jQuery', 'adrotate'); ?></th>
					<td><input type="checkbox" name="adrotate_jquery" <?php if($adrotate_config['jquery'] == 'Y') { ?>checked="checked" <?php } ?> /> <span class="description"><?php _e('jQuery is required for Dynamic Groups. Enable if your theme does not load jQuery already.', 'adrotate'); ?></span></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Load jQuery ShowOff', 'adrotate'); ?></th>
					<td><input type="checkbox" name="adrotate_jshowoff" <?php if($adrotate_config['jshowoff'] == 'Y') { ?>checked="checked" <?php } ?> /> <span class="description"><?php _e('The jQuery Showoff Library (0.1.2+) is required for Dynamic Groups. Disable if other plugins or themes already load this.', 'adrotate'); ?></span></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Load in footer?', 'adrotate'); ?></th>
					<td><input type="checkbox" name="adrotate_jsfooter" <?php if($adrotate_config['jsfooter'] == 'Y') { ?>checked="checked" <?php } ?> /> <span class="description"><?php _e('Enable if you want to load your Javascripts in the footer. Your theme needs to call wp_footer() for this to work.', 'adrotate'); ?></span></td>
				</tr>
			</table>

			<h3><?php _e('Maintenance', 'adrotate'); ?></h3>
			<span class="description"><?php _e('NOTE: The below functions are intented to be used to OPTIMIZE your database. They only apply to your ads/groups/blocks and stats. Not to other settings or other parts of WordPress! Always always make a backup! These functions are to be used when you feel or notice your database is slow, unresponsive and sluggish.', 'adrotate'); ?></span>
			<table class="form-table">			
				<tr>
					<th valign="top"><?php _e('Optimize Database', 'adrotate'); ?></th>
					<td>
						<input type="submit" id="post-role-submit" name="adrotate_db_optimize_submit" value="<?php _e('Optimize Database', 'adrotate'); ?>" class="button-secondary" onclick="return confirm('<?php _e('You are about to optimize the AdRotate database.', 'adrotate'); ?>\n\n<?php _e('Did you make a backup of your database?', 'adrotate'); ?>\n\n<?php _e('This may take a moment and may cause your website to respond slow temporarily!', 'adrotate'); ?>\n\n<?php _e('OK to continue, CANCEL to stop.', 'adrotate'); ?>')" /><br />
						<span class="description"><?php _e('Cleans up overhead data in the AdRotate tables.', 'adrotate'); ?><br />
						<?php _e('Overhead data is accumulated garbage resulting from many changes you\'ve made. This can vary from nothing to hundreds of KiB of data.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Clean-up Database', 'adrotate'); ?></th>
					<td>
						<input type="submit" id="post-role-submit" name="adrotate_db_cleanup_submit" value="<?php _e('Clean-up Database', 'adrotate'); ?>" class="button-secondary" onclick="return confirm('<?php _e('You are about to clean up your database. This may delete expired schedules and older statistics.', 'adrotate'); ?>\n\n<?php _e('Are you sure you want to continue?', 'adrotate'); ?>\n\n<?php _e('This might take a while and may slow down your site during this action!', 'adrotate'); ?>\n\n<?php _e('OK to continue, CANCEL to stop.', 'adrotate'); ?>')" /><br />
						<label for="adrotate_db_cleanup_schedules"><input type="checkbox" name="adrotate_db_cleanup_schedules" /> <?php _e('Delete old (expired) schedules (Optional).', 'adrotate'); ?></label><br />
						<label for="adrotate_db_cleanup_statistics"><input type="checkbox" name="adrotate_db_cleanup_statistics" /> <?php _e('Delete stats older than 356 days (Optional).', 'adrotate'); ?></label><br />
						<span class="description"><?php _e('AdRotate creates empty records when you start making ads, groups or schedules. In rare occasions these records are faulty.', 'adrotate'); ?><br /><?php _e('If you made an ad, group or schedule that does not save when you make it use this button to delete those empty records.', 'adrotate'); ?><br /><?php _e('Additionally you can clean up old schedules and/or statistics. This will improve the speed of your site.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Re-evaluate Ads', 'adrotate'); ?></th>
					<td>
						<input type="submit" id="post-role-submit" name="adrotate_evaluate_submit" value="<?php _e('Re-evaluate all ads', 'adrotate'); ?>" class="button-secondary" onclick="return confirm('<?php _e('You are about to check all ads for errors.', 'adrotate'); ?>\n\n<?php _e('This might take a while and may slow down your site during this action!', 'adrotate'); ?>\n\n<?php _e('OK to continue, CANCEL to stop.', 'adrotate'); ?>')" /><br />
						<span class="description"><?php _e('This will apply all evaluation rules to all ads to see if any error slipped in. Normally you should not need this feature.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<td colspan="2"><span class="description"><?php _e('DISCLAIMER: If for any reason your data is lost, damaged or otherwise becomes unusable in any way or by any means in whichever way I will not take responsibility. You should always have a backup of your database. These functions do NOT destroy data. If data is lost, damaged or unusable, your database likely was beyond repair already. Claiming it worked before clicking these buttons is not a valid point in any case.', 'adrotate'); ?></span></td>
				</tr>
			</table>

			<h3><?php _e('Troubleshooting', 'adrotate'); ?></h3>
			<table class="form-table">			
				<tr>
					<td><?php _e('Current version:', 'adrotate'); ?> <?php echo $adrotate_version['current']; ?></td>
					<td><?php _e('Previous version:', 'adrotate'); ?> <?php echo $adrotate_version['previous']; ?></td>
				</tr>
				<tr>
					<td><?php _e('Current database version:', 'adrotate'); ?> <?php echo $adrotate_db_version['current']; ?></td>
					<td><?php _e('Previous database version:', 'adrotate'); ?> <?php echo $adrotate_db_version['previous']; ?></td>
				</tr>
				<tr>
					<td><?php _e('Ad evaluation next run:', 'adrotate'); ?></td>
					<td><?php if(!$adevaluate) _e('Not scheduled!', 'adrotate'); else echo date_i18n(get_option('date_format')." H:i", $adevaluate); ?></td>
				</tr>
				<tr>
					<td><?php _e('Ad Notifications next run:', 'adrotate'); ?></td>
					<td><?php if(!$adschedule) _e('Not scheduled!', 'adrotate'); else echo date_i18n(get_option('date_format')." H:i", $adschedule); ?></td>
				</tr>
				<tr>
					<td><?php _e('Clean Trackerdata next run:', 'adrotate'); ?></td>
					<td><?php if(!$adtracker) _e('Not scheduled!', 'adrotate'); else echo date_i18n(get_option('date_format')." H:i", $adtracker); ?></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Current status of adverts', 'adrotate'); ?></th>
					<td><?php _e('Normal'); ?>: <?php echo $adrotate_advert_status['normal']; ?>, <?php _e('Error'); ?>: <?php echo $adrotate_advert_status['error']; ?>, <?php _e('Expired'); ?>: <?php echo $adrotate_advert_status['expired']; ?>, <?php _e('Expires Soon'); ?>: <?php echo $adrotate_advert_status['expiressoon']; ?>, <?php _e('Unknown Status'); ?>: <?php echo $adrotate_advert_status['unknown']; ?>.</td>
				</tr>
				<tr>
					<td colspan="2"><span class="description"><?php _e('NOTE: The below options are not meant for normal use and are only there for developers to review saved settings or how ads are selected. These can be used as a measure of troubleshooting upon request but for normal use they SHOULD BE LEFT UNCHECKED!!', 'adrotate'); ?></span></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Developer Debug', 'adrotate'); ?></th>
					<td>
						<input type="checkbox" name="adrotate_debug" <?php if($adrotate_debug['general'] == true) { ?>checked="checked" <?php } ?> /> General - <span class="description"><?php _e('Troubleshoot ads and how (if) they are selected, has front-end output.', 'adrotate'); ?></span><br />
						<input type="checkbox" name="adrotate_debug_dashboard" <?php if($adrotate_debug['dashboard'] == true) { ?>checked="checked" <?php } ?> /> Dashboard - <span class="description"><?php _e('Show all settings, dashboard routines and related values.', 'adrotate'); ?></span><br />
						<input type="checkbox" name="adrotate_debug_userroles" <?php if($adrotate_debug['userroles'] == true) { ?>checked="checked" <?php } ?> /> User Roles - <span class="description"><?php _e('Show array of all userroles and capabilities.', 'adrotate'); ?></span><br />
						<input type="checkbox" name="adrotate_debug_userstats" <?php if($adrotate_debug['userstats'] == true) { ?>checked="checked" <?php } ?> /> Userstats - <span class="description"><?php _e('Review saved advertisers! Visible to advertisers.', 'adrotate'); ?></span><br />
						<input type="checkbox" name="adrotate_debug_stats" <?php if($adrotate_debug['stats'] == true) { ?>checked="checked" <?php } ?> /> Stats - <span class="description"><?php _e('Review global stats, per ad/group/block stats. Visible only to publishers.', 'adrotate'); ?></span><br />
						<input type="checkbox" name="adrotate_debug_geo" <?php if($adrotate_debug['geo'] == true) { ?>checked="checked" <?php } ?> /> Geo Location - <span class="description"><?php _e('Output retrieved Geo data or errors related to the retrieving of Geo Services.', 'adrotate'); ?></span><br />
						<input type="checkbox" name="adrotate_debug_timers" <?php if($adrotate_debug['timers'] == true) { ?>checked="checked" <?php } ?> /> Clicktracking - <span class="description"><?php _e('Disable timers for clicks and impressions and enable a alert window for clicktracking.', 'adrotate'); ?></span><br />
						<input type="checkbox" name="adrotate_debug_track" <?php if($adrotate_debug['track'] == true) { ?>checked="checked" <?php } ?> /> Tracking Encryption - <span class="description"><?php _e('Temporarily disable encryption on the redirect url.', 'adrotate'); ?></span><br />
					</td>
				</tr>
	    	</table>
	    	
		    <p class="submit">
		      	<input type="submit" name="adrotate_options_submit" class="button-primary" value="<?php _e('Update Options', 'adrotate'); ?>" />
		    </p>
		</form>
	</div>
<?php 
}

/*-------------------------------------------------------------
 Name:      adrotate_network_license

 Purpose:   Network activated license dashboard
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_network_license() {
	global $wpdb, $adrotate_advert_status;

	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	$adrotate_activate = get_option('adrotate_activate');
	?>

	<div class="wrap">
	  	<h2><?php _e('AdRotate Network License', 'adrotate'); ?></h2>

		<?php if($status > 0) adrotate_status($status, array('error' => $error)); ?>
		
	  	<form name="settings" id="post" method="post" action="admin.php?page=adrotate-network-settings">
			<input type="hidden" name="adrotate_license_network" value="1" />

			<?php wp_nonce_field('adrotate_license','adrotate_nonce_license'); ?>

			<span class="description"><?php _e('Activate your AdRotate License here to receive automated updates and enable support via the fast and personal ticket system.', 'adrotate'); ?><br />
			<?php _e('For network activated setups like this you need a Network or Developer License.', 'adrotate'); ?></span>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('License Type', 'adrotate'); ?></th>
					<td>
						<?php echo ($adrotate_activate['type'] != '') ? $adrotate_activate['type'] : __('Not activated - Not eligible for support and updates.', 'adrotate'); ?>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('License Key', 'adrotate'); ?></th>
					<td>
						<input name="adrotate_license_key" type="text" class="search-input" size="50" value="<?php echo $adrotate_activate['key']; ?>" autocomplete="off" <?php echo ($adrotate_activate['status'] == 1) ? 'disabled' : ''; ?> /> <span class="description"><?php _e('You can find the license key in your order email.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('License Email', 'adrotate'); ?></th>
					<td>
						<input name="adrotate_license_email" type="text" class="search-input" size="50" value="<?php echo $adrotate_activate['email']; ?>" autocomplete="off" <?php echo ($adrotate_activate['status'] == 1) ? 'disabled' : ''; ?> /> <span class="description"><?php _e('The email address you used on adrotateplugin.com.', 'adrotate'); ?></span>
					</td>
				</tr>

				<tr>
					<th valign="top">&nbsp;</th>
					<td>
						<?php if($adrotate_activate['status'] == 0) { ?>
						<input type="submit" id="post-role-submit" name="adrotate_license_network_activate" value="<?php _e('Activate', 'adrotate'); ?>" class="button-primary" />
						<?php } else { ?>
						<input type="submit" id="post-role-submit" name="adrotate_license_network_deactivate" value="<?php _e('De-activate', 'adrotate'); ?>" class="button-secondary" />
						<?php } ?>
					</td>
				</tr>
			</table>
		</form>
	</div>
<?php
}
?>