<?php
/*
Plugin Name: Admin Ads
Plugin URI: 
Description:
Author: Andrew Billits
Version: 1.0.7
Author URI:
WDP ID: 6
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

//------------------------------------------------------------------------//
//---Hook-----------------------------------------------------------------//
//------------------------------------------------------------------------//
add_action('admin_menu', 'admin_ads_plug_pages');
add_action('admin_notices', 'admin_ads_output');
//------------------------------------------------------------------------//
//---Functions------------------------------------------------------------//
//------------------------------------------------------------------------//

function admin_ads_output() {
	$admin_ads_data = get_site_option('admin_ads_data');
	if ( !empty($admin_ads_data) && $admin_ads_data != 'empty' ){
		echo stripslashes( $admin_ads_data );
	}
}

function admin_ads_plug_pages() {
	global $wpdb, $wp_roles, $current_user;
	if ( is_site_admin() ) {
		add_submenu_page('ms-admin.php', 'Admin Ads', 'Admin Ads', 10, 'admin-ads', 'admin_ads_page_main_output');
	}
}

//------------------------------------------------------------------------//
//---Page Output Functions------------------------------------------------//
//------------------------------------------------------------------------//

function admin_ads_page_main_output() {
	global $wpdb, $wp_roles, $current_user;
	
	if(!current_user_can('manage_options')) {
		echo "<p>Nice Try...</p>";  //If accessed properly, this message doesn't appear.
		return;
	}
	if (isset($_GET['updated'])) {
		?><div id="message" class="updated fade"><p><?php _e('' . urldecode($_GET['updatedmsg']) . '') ?></p></div><?php
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
			<h2><?php _e('Admin Ads') ?></h2>
            <form method="post" action="ms-admin.php?page=admin-ads&action=update">
            <table class="form-table">
            <tr valign="top">
            <th scope="row"><?php _e('Ad Code') ?></th>
            <td>
            <textarea name="admin_ads_data" type="text" rows="5" wrap="soft" id="admin_ads_data" style="width: 95%"/><?php echo $admin_ads_data; ?></textarea>
            <br /><?php _e('Tip: Use HTML markup around the code to make it centered on the page.') ?></td>
            </tr>
            </table>
            
            <p class="submit">
            <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
			<input type="submit" name="Reset" value="<?php _e('Reset') ?>" />
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
				window.location='ms-admin.php?page=admin-ads&updated=true&updatedmsg=" . urlencode(__('Settings cleared.')) . "';
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
				window.location='ms-admin.php?page=admin-ads&updated=true&updatedmsg=" . urlencode(__('Settings saved.')) . "';
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

?>
