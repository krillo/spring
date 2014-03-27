<?php
/*
Plugin Name: Gatekeeper
Plugin URI: http://wordpress.org/extend/plugins/gatekeeper
Description: Gatekeeper allows administrators to take a WordPress site offline quickly and easily while leaving it fully accessible to administrators and other authorized users. Site visitors will be shown or redirected to a specified offline page. An optional blacklist can be used for permanent bans.
Version: 1.0
Author: Jamie Wilson
Author URI: http://jamiewilson.net
License: GPL2
  
    Copyright © 2011-2013 Jamie Wilson (email: wpdev@jamiewilson.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// REGISTER HOOKS
register_activation_hook(__FILE__,'gatekeeper_install'); 
register_deactivation_hook( __FILE__, 'gatekeeper_remove' );

// REGISTER ACTIONS
add_action('init', 'gatekeeper_watch_the_gate', 1);
add_action('admin_notices', 'gatekeeper_admin_status');
add_action('admin_head', 'gatekeeper_css_register');
add_action('admin_head', 'gatekeeper_js_register');


// SETUP ADMIN MENU
if (is_admin()) {
	add_action('admin_init', 'gatekeeper_register');
	add_action('admin_menu', 'gatekeeper_menu');
	}

// REGISTER/WHITELIST SETTINGS
function gatekeeper_register() {
	register_setting('gatekeeper_optiongroup', 'gatekeeper_active');
	register_setting('gatekeeper_optiongroup', 'gatekeeper_placeholder_redirect_page');
	register_setting('gatekeeper_optiongroup', 'gatekeeper_placeholder_behavior');
	register_setting('gatekeeper_optiongroup', 'gatekeeper_allowadmins');
	register_setting('gatekeeper_optiongroup', 'gatekeeper_autoprotect_adminip');
	register_setting('gatekeeper_optiongroup', 'gatekeeper_adminip');
	register_setting('gatekeeper_optiongroup', 'gatekeeper_whitelist');
	register_setting('gatekeeper_optiongroup', 'gatekeeper_blacklist_behavior');
	register_setting('gatekeeper_optiongroup', 'gatekeeper_blacklist_redirect_page');
	register_setting('gatekeeper_optiongroup', 'gatekeeper_blacklist');
	}

// INSTALLATION FUNCTIONS
function gatekeeper_install() {
	add_option('gatekeeper_active', 'false', '', 'yes');
	add_option('gatekeeper_placeholder_redirect_page', '', '', 'yes');
	add_option('gatekeeper_placeholder_behavior', '', '', 'yes');
	add_option('gatekeeper_allowadmins', 'true', '', 'yes');
	add_option('gatekeeper_autoprotect_adminip', 'true', '', 'yes');
	add_option('gatekeeper_adminip', $_SERVER['REMOTE_ADDR'], '', 'yes');
	add_option('gatekeeper_whitelist', '', '', 'yes');
	add_option('gatekeeper_blacklist_behavior', '', '', 'yes');
	add_option('gatekeeper_blacklist_redirect_page', '', '', 'yes');
	add_option('gatekeeper_blacklist', '', '', 'yes');
	}

// UNINSTALL FUNCTIONS
function gatekeeper_remove() {
	delete_option('gatekeeper_active');
	delete_option('gatekeeper_placeholder_redirect_page');
	delete_option('gatekeeper_placeholder_behavior');
	delete_option('gatekeeper_allowadmins');
	delete_option('gatekeeper_autoprotect_adminip');
	delete_option('gatekeeper_adminip');
	delete_option('gatekeeper_whitelist');
	delete_option('gatekeeper_blacklist_behavior');
	delete_option('gatekeeper_blacklist_redirect_page');
	delete_option('gatekeeper_blacklist');
}

// REGISTER EXTERNAL CSS
function gatekeeper_css_register() {
	wp_register_style("gatekeeper-css", plugins_url("style.css", __FILE__), false, false);
	wp_enqueue_style("gatekeeper-css");
}

// REGISTER EXTERNAL JAVASCRIPT
function gatekeeper_js_register() {
	wp_register_script("gatekeeper-js", plugins_url("gk-jquery.js", __FILE__) );
	wp_enqueue_script("gatekeeper-js");
}

// ADD MENU LINK
function gatekeeper_menu() {
	add_management_page('Gatekeeper Settings', 'Gatekeeper', 'manage_options', 'gatekeeper', 'gatekeeper_options');
	}
	
// ADMIN STATUS NOTIFICATION
function gatekeeper_admin_status() {
	/* UPPER RIGHT CORNER */

	if (get_option('gatekeeper_active') == 'true') {
		echo "<div id='gk-active-notifier'><a href='" . get_admin_url() . "tools.php?page=gatekeeper' title='Gatekeeper is active'>Offline</a></div>";
	}
}

// DISPLAY THE OPTIONS PAGE	
function gatekeeper_options() {
	
	// DENY ACCESS TO NON-ADMINISTRATORS
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	
	// SET USEFUL VARIABLES
	$GKPATH = WP_PLUGIN_URL . "/gatekeeper/";
	global $current_user;
	?>
	
	<div class="wrap">
		<div style="background: url('<?php echo $GKPATH; ?>images/banner_bg.png') repeat-x; width: 100%; margin-bottom: 14px;"><img src="<?php echo $GKPATH; ?>images/banner_logo.png" /></div>
		<?php if ($_GET['settings-updated']) {
			echo "<div style='color: red;'>Settings saved.</div>"; } ?>
		
		<div id="gk-main">
		<form method="post" action="options.php">
			<?php settings_fields('gatekeeper_optiongroup'); ?>

			<?php /* GATEKEEPER STATUS INDICATOR */ ?>
			<div id="gk-status" class="gk-rounded" style="background: url('<?php echo $GKPATH; ?>images/<?php if (get_option('gatekeeper_active') == "true") { echo "bg_red.jpg"; } else { echo "bg_green.jpg"; } ?>') repeat-x;">
				<?php if (get_option('gatekeeper_active') == "true") { echo "Offline"; } else { echo "Online"; } ?>
			</div>				

			<table class="form-table gk-option-table">
				
				<?php /* SITE STATUS SELECTOR */ ?>
				<tr valign="top">
					<th scope="row"><label for="gatekeeper_active" title="Take the site offline for anyone not whitelisted.">Site Status:</label></th>
					<td>
						<select name="gatekeeper_active">
							<option value="true" <?php if (get_option('gatekeeper_active') == "true" ) { ?>selected="selected"<?php } ?>>Offline</option>
							<option value="false" <?php if (get_option('gatekeeper_active') == "false" ) { ?>selected="selected"<?php } ?>>Online</option>					
						</select>
					</td>
				</tr>
				
				<?php /* OFFLINE BEHAVIOR */ ?>
				<tr valign="top">
					<th scope="row"><label for="gatekeeper_placeholder_behavior" title="Redirect to an offline page or display an offline page without redirecting the page.">Offline Behavior:</label></th>
					<td>
						<input type="radio" name="gatekeeper_placeholder_behavior" value="redirect" <?php if (get_option('gatekeeper_placeholder_behavior') != 'replace') { echo "checked"; } ?> />Redirect (302 Temporary Redirect) <br />
						<input type="radio" name="gatekeeper_placeholder_behavior" value="replace" <?php if (get_option('gatekeeper_placeholder_behavior') == 'replace') { echo "checked"; } ?> />Replace page 					
					</td>				
				</tr>
				
				<?php /* OFFLINE PAGE */ ?>
				<tr valign="top">
					<th scope="row"><label for="gatekeeper_placeholder_redirect_page" title="Display this page when offline. This cannot be a WordPress-managed page.">Offline page:</label></th>
					<td>
						<input name="gatekeeper_placeholder_redirect_page" type="text" style="width: 400px;" value="<?php echo get_option('gatekeeper_placeholder_redirect_page'); ?>" />					
					</td>				
				</tr>
				
			</table>
			
			<?php /* WHITELIST TABLE */ ?>
			<div id="gk-whitelist">
				<div id="gk-whitelist-title" class="gk-section-title">Whitelist</div>
				<table id="gk-whitelist-table" class="form-table gk-option-table" style="margin-top: 20px;">
					
					<tr valign="top">
						<td colspan="2">Allow the following to access the site when it is offline for everyone else.</td>				
					</tr>
					
					<?php /* WHITELIST ADMINS */ ?>
					<tr valign="top">
						<th scope="row"><label for="gatekeeper_allowadmins" title="Don't apply offline status to logged-in administrators.">Whitelist Logged-in Admins:</label></th>
						<td><input name="gatekeeper_allowadmins" type="checkbox" value="true" <?php if (get_option('gatekeeper_allowadmins') == "true") { echo "CHECKED"; } ?> /></td>	
					</tr>
					
					<?php /* AUTO-PROTECT ADMIN IP */ ?>
					<tr valign="top">
						<th scope="row"><label for="gatekeeper_autoprotect_adminip" title="Automatically whitelist your current IP address to prevent accidentally lock-out.">Auto-Protect Admin IP:</label></th>	
						<td>
							<input name="gatekeeper_autoprotect_adminip" type="checkbox" value="true" <?php if (get_option('gatekeeper_autoprotect_adminip') == "true") { echo "CHECKED"; } ?>					
						</td>			
					</tr>
					
					<?php /* ADMIN IP */ ?>
					<tr valign="top">
						<th scope="row"><label for="gatekeeper_adminip" title="Your current IP (will be auto-whitelisted when you save).">Administrator IP:</label></th>
						<td>
							<input name="gatekeeper_adminipDISPLAY" type="text" disabled="disabled" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>" />
							<input type="hidden" name="gatekeeper_adminip" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>" />
						</td>			
					</tr>
					
					<?php /* WHITELIST */ ?>
					<tr valign="top">
						<th scope="row"><label for="gatekeeper_whitelist" title="Whitelist: one per line. Examples: 192.168.10.103, 192.168.10.*, 192.168.10.[0-9]">Whitelist:</label></th>
						<td>
							<textarea name="gatekeeper_whitelist"><?php echo get_option('gatekeeper_whitelist'); ?></textarea>					
						</td>				
					</tr>							
				</table>
			</div>
			
			<?php /* BLACKLIST TABLE */ ?>
			<div id="gk-blacklist">
				<div id="gk-blacklist-title" class="gk-section-title">Blacklist</div>
				<table id="gk-blacklist-table" class="form-table gk-option-table" style="margin-top: 20px;">
					
					<tr valign="top">
						<td colspan="2">Prevent blacklisted IPs from accessing the site at any time, including login and registration pages.</td>				
					</tr>
					
					<?php /* BLACKLIST BEHAVIOR */ ?>
					<tr valign="top">
						<th scope="row"><label for="gatekeeper_blacklist_behavior" title="Redirect to an offline page or display an offline page without redirecting the page.">Offline Behavior:</label></th>
						<td>
							<input type="radio" name="gatekeeper_blacklist_behavior" value="redirect" <?php if (get_option('gatekeeper_blacklist_behavior') != 'replace') { echo "checked"; } ?> />Redirect (301 Moved Permanently) <br />
							<input type="radio" name="gatekeeper_blacklist_behavior" value="replace" <?php if (get_option('gatekeeper_blacklist_behavior') == 'replace') { echo "checked"; } ?> />Replace page 					
						</td>				
					</tr>
					
					<?php /* BLACKLIST PAGE */ ?>
					<tr valign="top">
						<th scope="row"><label for="gatekeeper_blacklist_redirect_page" title="Display this page to blacklisted IPs. This cannot be a WordPress-managed page.">Blacklist page:</label></th>
						<td>
							<input name="gatekeeper_blacklist_redirect_page" type="text" style="width: 400px;" value="<?php echo get_option('gatekeeper_blacklist_redirect_page'); ?>" />					
						</td>				
					</tr>
					
					<?php /* BLACKLIST */ ?>
					<tr valign="top">
						<th scope="row"><label for="gatekeeper_blacklist" title="Blacklist: one per line. Examples: 192.168.10.103, 192.168.10.*, 192.168.10.[0-9]">Blacklist:</label></th>
						<td>
							<textarea name="gatekeeper_blacklist"><?php echo get_option('gatekeeper_blacklist'); ?></textarea>					
						</td>				
					</tr>				
				</table>
			</div>
				
			<p class="submit">
				<input type="submit" class="button-primary" value="Save Changes" />			
			</p>	
		</form>
		</div>
		
		<?php /* SIDEBAR */ ?>
		<div id="gk-sidebar">
		
			<?php /* DONATION LINKS */ ?>
			<div class="gk-container">
				<div class="gk-section-title">Donate Some Caffeine</div>
				<div class="gk-sidebar-content">
					<div style="margin-bottom: 10px;">Has this tool been useful to you?<br />Help fuel the developer.</div>
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="J3ETMQWMPY586">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
					</form>
					<p><a href="http://www.weusecoins.com/" target="_new">Bitcoin</a> donations:<br /><i>14X5hXFJVv9Xty9k4zcSGxoj3MB7ZkTdpy</i></p>
				</div>
			</div>
			
		</div>
		<div style="clear: both;"></div>
	</div>
<?php
}

// GATEKEEPER (SENTRY) FUNCTIONS
function gatekeeper_watch_the_gate() {
	
	# if this IP is on the blacklist, deal with it immediately
	if (gk_checkip('blacklist')) {
		update_option('gatekeeper_stats_blacklist_blocks', get_option('gatekeeper_stats_blacklist_blocks') + 1);
		$gk_blklstloc = get_option("gatekeeper_blacklist_redirect_page");
		if (get_option("gatekeeper_blacklist_behavior") == "replace") {
			die(gk_curlGet($gk_blklstloc));
		} else {
			if (!headers_sent()) {
				Header( "HTTP/1.1 301 Moved Permanently" ); #301 permanent redirect
				header("Location:$gk_blklstloc");
			} else {
				die(gk_curlGet($gk_blklstloc)); #IF HEADERS ALREADY SENT, FALL BACK TO THIS
			}
		}		
	}	
	
	# check to see if Gatekeeper is active. If not, there's no need to continue (except for blacklists).
	if (get_option('gatekeeper_active') != 'true') {
		return;
	}
	
	# don't apply to admin pages
	if (is_admin()) {
		return;
		}
		
	# don't apply to login or logout pages
	if ($GLOBALS['pagenow'] == 'wp-login.php') {
		return;
		}
	
	# don't apply to logged in administrators
	if (get_option('gatekeeper_allowadmins') == "true" && current_user_can('manage_options'))  {
		update_option('gatekeeper_stats_whitelist_views', get_option('gatekeeper_stats_whitelist_views') + 1);
		return;
	}	
	
	# don't apply to admin ip
	if (get_option('gatekeeper_autoprotect_adminip') == "true" && $_SERVER['REMOTE_ADDR'] == get_option('gatekeeper_adminip')) {
		update_option('gatekeeper_stats_whitelist_views', get_option('gatekeeper_stats_whitelist_views') + 1);
		return;
		} 
	
	# don't apply to matching IP addresses (if any) #preg_match?
	# if line contains letters or dashes, consider it a hostname
	if (gk_checkip('whitelist')) {
		update_option('gatekeeper_stats_whitelist_views', get_option('gatekeeper_stats_whitelist_views') + 1);
		return;
		}
		
	# redirect any IP that has made it this far
	update_option('gatekeeper_stats_offline_redirects', get_option('gatekeeper_stats_offline_redirects') + 1);
	$gk_phloc = get_option("gatekeeper_placeholder_redirect_page");
	if (get_option("gatekeeper_placeholder_behavior") == "replace") {
		die(gk_curlGet($gk_phloc));
	} else {
		if (!headers_sent()) {
			header("Location:$gk_phloc", 302); #302 temporary redirect
		} else {
			die(gk_curlGet($gk_phloc)); # if headers already sent, fall back to this
		}
	}
}

function gk_checkip($list) {
	$remote_ip = $_SERVER['REMOTE_ADDR'];
	
	// LOAD THE REQUESTED LIST
	if (strtolower($list) == "whitelist") {
		$gk_list = get_option('gatekeeper_whitelist');
	} elseif (strtolower($list) == "blacklist") {
		if ($remote_ip == get_option('gatekeeper_adminip')) {
			return false; # never blacklist the admin ip or else they won't be able to login!
		}
		$gk_list = get_option('gatekeeper_blacklist');
	} else {
		return false;
	}

	$gk_exploded_ips = explode("\n", trim($gk_list));
	foreach ($gk_exploded_ips as $gk_addr) {
		$gk_addr = str_replace(".", "\.", $gk_addr);
		$gk_addr = str_replace("*", "[0-9\.]*", $gk_addr);
		$gk_addr = "/^" . trim($gk_addr) . "$/";
		if (preg_match($gk_addr, $remote_ip)) {
			return true;
		}
	}
	return false;
}

// CURL FUNCTION (USED TO LOAD EXTERNAL PAGES)
function gk_curlGet($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    return curl_exec($ch);
} 
?>
