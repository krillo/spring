<?php 
/**
 * @package WP Admin Custom Page
 * @author Dario Bassanesi
 * @version 1.5.0	
 */
/*
Plugin Name: WP Admin Custom Page
Plugin URI: http://www.thunderbax.com/?page_id=767
Description: This plugin add a custom page in your admin dashboard and you can edit the html text directly in a box in option page
Author: Dario Bassanesi
Version: 1.5.0
Author URI: http://www.thunderbax.com
*/
/**
License:
==============================================================================
Copyright 2011 Dario Bassanesi  (email : dario@thunderbax.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

Requirements:
==============================================================================
This plugin requires WordPress >= 2.6 and tested with PHP Interpreter >= 5.3.1
*/

add_action('admin_menu','thund_custom_admin_menu');
add_action('admin_menu','thund_mgr_admin_menu');
add_action('admin_head','add_tinymce_head');

//Show page for editors with the html code written in Admin Page

function thund_custom_admin_menu() {

	$opt_title_name = 'thund_screen_title_name';
	$opt_title_val = str_replace("\\","",(get_option( $opt_title_name )));
	
	if ($opt_title_val==""){
		$opt_title_val = "Sample Admin Page";
		}
	
		
	
	if (function_exists('add_dashboard_page')) {
		add_dashboard_page(__($opt_title_val),__($opt_title_val),5,basename(__FILE__),'custom_user_option_page');
	}
}
//This function write in head file the scripts for implement tinymce editor
function add_tinymce_head() {

	$wac_path = plugins_url('tinymce/jscripts/tiny_mce/tiny_mce.js',__FILE__);
	echo '<script type="text/javascript" src="';
	echo $wac_path.'"></script>
<script type="text/javascript">
	tinyMCE.init({
		// General options
		mode : "exact",
		elements: "wacp-txtarea",
		theme : "advanced",
		skin : "o2k7",
		plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups,autosave",

		// Theme options
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example word content CSS (should be your site CSS) this one removes paragraph margins
		content_css : "css/word.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});
</script>
<!-- /TinyMCE -->';
}
//Show page for Admin with title text and code textarea
function thund_mgr_admin_menu() {
	
	if (function_exists('add_options_page')) {
		add_options_page(__('Admin Custom Page'),__('Admin Custom Page'),10,basename(__FILE__),'custom_thund_option_page');
	}
}
//Code for Admin Page
function custom_user_option_page(){

    $opt_title_name = 'thund_screen_title_name';
	$opt_html_name = 'thund_screen_html_name';
	
	//Load default value if variable is not set
	
	if ($opt_html_val==""){
		$opt_html_val = "
		<div>
			<h1>Sample WP Admin Custom Page Plugin</h1>
			<p><strong>Note:</strong> If you want to edit this page with your html code please click on 'Admin Custom Page' under
			Options Menu</p>
		</div>
		
		"	;
	}
	
	
	// Read in existing option value from database
    $opt_title_val = get_option( $opt_title_name );
	$opt_html_val = get_option( $opt_html_name );
	
	echo str_replace("\\","",$opt_html_val);
	
}
function custom_thund_option_page(){
	
	// variables for the field and option names 
    $opt_title_val = 'thund_screen_title';
	$opt_html_val = 'thund_screen_html';
    $hidden_field_name = 'mt_submit_hidden';
    $opt_title_name = 'thund_screen_title_name';
	$opt_html_name = 'thund_screen_html_name';
	


    // Read in existing option value from database
    $opt_title_val = get_option( $opt_title_name );
	$opt_html_val = get_option( $opt_html_name );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_title_val = $_POST[ $opt_title_name ];
		$opt_html_val = $_POST[ $opt_html_name ];
        // Save the posted value in the database
        update_option( $opt_title_name, $opt_title_val );
		update_option( $opt_html_name, $opt_html_val );

        // Put an settings updated message on the screen

?>
<div class="updated"><p><strong><?php _e('settings saved.', 'menu-test' ); ?></strong></p></div>
<?php

    }

    // Now display the settings editing screen

    echo '<div class="wrap">';
    // header
    echo "<h2>" . __( 'Admin Custom Page', 'menu-test' ) . "</h2>";
    // settings form
    ?>

<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("Menu Title:", 'menu-test' ); ?> 
	<input type="text" name="<?php echo $opt_title_name; ?>" value="<?php echo $opt_title_val; ?>" size="20"> <br/><br/>
	<textarea id="wacp-txtarea" name="<?php echo $opt_html_name; ?>" cols="100" rows="20"><?php echo str_replace("\\","",$opt_html_val); ?></textarea>
</p><hr />

<p class="submit">
	<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

</form>
</div>

<?php
 
}
?>