<?php
/*
Plugin Name: Admin Ads
Plugin URI: http://premium.wpmudev.org/project/admin-ads
Description: Display ads in admin dashboard
Author: S H Mohanjith (Incsub), Andrew Billits (Incsub)
Version: 1.1.0
Tested up to: 3.2.0
Network: true
Author URI: http://premium.wpmudev.org
WDP ID: 6
Text Domain: admin_ads
*/

/* 
Copyright 2007-2009 Incsub (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

global $admin_ads_settings_page, $admin_ads_settings_page_long;

if ( version_compare($wp_version, '3.0.9', '>') ) {
	$admin_ads_settings_page = 'settings.php';
	$admin_ads_settings_page_long = 'network/settings.php';
} else {
	$admin_ads_settings_page = 'ms-admin.php';
	$admin_ads_settings_page_long = 'ms-admin.php';
}

//------------------------------------------------------------------------//
//---Hook-----------------------------------------------------------------//
//------------------------------------------------------------------------//
add_action('init', 'admin_ads_init');
add_action('admin_menu', 'admin_ads_plug_pages');
add_action('network_admin_menu', 'admin_ads_plug_pages');
add_action('admin_notices', 'admin_ads_output');
add_action('network_admin_notices', 'admin_ads_output');
//------------------------------------------------------------------------//
//---Functions------------------------------------------------------------//
//------------------------------------------------------------------------//

function admin_ads_init() {
	if ( !is_multisite() )
		exit( 'The Admin Ads plugin is only compatible with WordPress Multisite.' );
		
	load_plugin_textdomain('admin_ads', false, dirname(plugin_basename(__FILE__)).'/languages');
}

function admin_ads_output() {
	$admin_ads_data = get_site_option('admin_ads_data');
	if ( !empty($admin_ads_data) && $admin_ads_data != 'empty' ){
		echo '<div class="wpmu-notice">'.stripslashes( $admin_ads_data ).'</div>';
	}
}

function admin_ads_plug_pages() {
	global $wpdb, $wp_roles, $current_user, $wp_version, $admin_ads_settings_page, $admin_ads_settings_page_long;
	
	if ( version_compare($wp_version, '3.0.9', '>') ) {
		if ( is_network_admin() ) {
			add_submenu_page($admin_ads_settings_page, __('Admin Ads', 'admin_ads'), __('Admin Ads', 'admin_ads'), 10, 'admin-ads', 'admin_ads_page_main_output');
		}
	} else {
		if ( is_super_admin() ) {
			add_submenu_page($admin_ads_settings_page, __('Admin Ads', 'admin_ads'), __('Admin Ads', 'admin_ads'), 10, 'admin-ads', 'admin_ads_page_main_output');
		}
	}
}

//------------------------------------------------------------------------//
//---Page Output Functions------------------------------------------------//
//------------------------------------------------------------------------//

function admin_ads_page_main_output() {
	global $wpdb, $wp_roles, $current_user, $admin_ads_settings_page, $admin_ads_settings_page_long;
	
	if(!current_user_can('manage_options')) {
		echo "<p>Nice Try...</p>";  //If accessed properly, this message doesn't appear.
		return;
	}
	if (isset($_GET['updated'])) {
		?><div id="message" class="updated fade"><p><?php _e(urldecode($_GET['updatedmsg']), 'admin_ads') ?></p></div><?php
	}
	echo '<div class="wrap">';
	switch( $_GET[ 'action' ] ) {
		//---------------------------------------------------//
		default:
			$admin_ads_data = get_site_option('admin_ads_data');
			if ( $admin_ads_data == 'empty' ) {
				$admin_ads_data = '';
			}
			?>
			<h2><?php _e('Admin Ads', 'admin_ads') ?></h2>
            <form method="post" action="<?php print $admin_ads_settings_page; ?>?page=admin-ads&action=update">
            <table class="form-table">
            <tr valign="top">
            <th scope="row"><?php _e('Ad Code', 'admin_ads') ?></th>
            <td>
            <textarea name="admin_ads_data" type="text" rows="5" wrap="soft" id="admin_ads_data" style="width: 95%"/><?php echo $admin_ads_data; ?></textarea>
            <br /><?php _e('Tip: Use HTML markup around the code to make it centered on the page.', 'admin_ads') ?></td>
            </tr>
            </table>
            
            <p class="submit">
            <input type="submit" name="Submit" value="<?php _e('Save Changes', 'admin_ads') ?>" />
			<input type="submit" name="Reset" value="<?php _e('Reset', 'admin_ads') ?>" />
            </p>
            </form>
			<?php
		break;
		//---------------------------------------------------//
		case "update":
			if ( isset( $_POST[ 'Reset' ] ) ) {
				update_site_option( "admin_ads_data", "empty" );
				echo "
				<SCRIPT LANGUAGE='JavaScript'>
				window.location='{$admin_ads_settings_page}?page=admin-ads&updated=true&updatedmsg=" . urlencode(__('Settings cleared.', 'admin_ads')) . "';
				</script>
				";
			} else {
				$admin_ads_data = $_POST[ 'admin_ads_data' ];
				if ( $admin_ads_data == '' ) {
					$admin_ads_data = 'empty';
				}
				update_site_option( "admin_ads_data", stripslashes($admin_ads_data) );
				echo "
				<SCRIPT LANGUAGE='JavaScript'>
				window.location='{$admin_ads_settings_page}?page=admin-ads&updated=true&updatedmsg=" . urlencode(__('Settings saved.', 'admin_ads')) . "';
				</script>
				";
			}
		break;
		//---------------------------------------------------//
		case "temp":
		break;
		//---------------------------------------------------//
	}
	echo '</div>';
}

if ( !function_exists( 'wdp_un_check' ) ) {
	add_action( 'admin_notices', 'wdp_un_check', 5 );
	add_action( 'network_admin_notices', 'wdp_un_check', 5 );

	function wdp_un_check() {
		if ( !class_exists( 'WPMUDEV_Update_Notifications' ) && current_user_can( 'edit_users' ) )
			echo '<div class="error fade"><p>' . __('Please install the latest version of <a href="http://premium.wpmudev.org/project/update-notifications/" title="Download Now &raquo;">our free Update Notifications plugin</a> which helps you stay up-to-date with the most stable, secure versions of WPMU DEV themes and plugins. <a href="http://premium.wpmudev.org/wpmu-dev/update-notifications-plugin-information/">More information &raquo;</a>', 'wpmudev') . '</a></p></div>';
	}
}
