<?php
/*
Plugin Name: PushBeat Box
Plugin URI: https://pushbeat.net/
Description: <a href="https://pushbeat.net/">PushBeat</a> plugin for WordPress
Author: webware,Inc.
Author URI: https://pushbeat.net
License: GPL2
Version: 1.0
*/
/*
/*  Copyright 2017 webware,Inc.

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

//
// Hooks
//
add_action( 'wp_head', 'hook_javascript' );
add_action( 'admin_menu', 'pushbeat_box_menu' );
add_action( 'admin_init', 'register_pushbeat_box_setting' );
add_action( 'admin_notices', 'pushbeat_box_warning' );

//
// Template
//
$pushbeat_box_script_template = '
<script src="https://pushbeat.net/js/pushbeat-sdk.js" async></script>
<script>
window._pbData = {
	applicationId: "PUSHBEAT_APPLICATION_ID"
};
</script> 
';

//
// Functions
//
function pushbeat_box_menu() {
	add_options_page( 'PushBeat Box', 'PushBeat Box', 'manage_options', 'pushbeat_box_options', 'pushbeat_box_options' );
}

function register_pushbeat_box_setting() {
	register_setting( 'pushbeat_options', 'pushbeat_application' );
}

function hook_javascript() {
	global $pushbeat_box_script_template;
	$application_id = get_option( 'pushbeat_application' );
	if ( $application_id ) {
		echo str_replace( 'PUSHBEAT_APPLICATION_ID', $application_id, $pushbeat_box_script_template );
	}
}

function pushbeat_box_options() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'このページにアクセスする権限がありません' ) );
	}
?>

<div class="wrap">
<h2>PushBeat Box</h2>
<p>
	<?php _e( 'このプラグインは、PUSH通知の受信を設定するボックスを表示するコードを各ページに自動で挿入します。' ) ?></br>
	<?php echo sprintf( __( '%sでこの WordPress サイトを登録して取得したアプリケーションIDを入力してください。' ), '<a target="_blank" href="https://pushbeat.net/">PushBeat</a>' ); ?><br/>
	<?php echo sprintf( __( 'ボックスのカスタマイズは、%s設定画面で行えます。' ), sprintf( '<a target="_blank" href="https://pushbeat.net/console/permission-box/list">%s</a>' , __( 'PUSH許可ポップアップ' ) ) ); ?>
</p>

<form method="post" action="options.php">
	<?php settings_fields( 'pushbeat_options' ); ?>
	<?php do_settings_sections( 'pushbeat_options' ); ?>

	<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php _e( 'アプリケーション ID' ) ?></th>
			<td><input type="text" name="pushbeat_application" value="<?php echo get_option( 'pushbeat_application' ); ?>" class="regular-text" /></td>
		</tr>
	</table>
  	<?php submit_button(); ?>
</form>
</div>
<?php
}

function pushbeat_box_warning() {
	if ( ! is_admin() ) {
		return;
	}

	$application_id = get_option( "pushbeat_application" );
	if ( $application_id ) {
		return;
	}
?>
	<div class='update-nag is-dismissible'><p>
		<?php echo sprintf( __( 'PushBeat Boxを有効にするには%sを入力してください。' ), sprintf( '<a href="' . admin_url( 'options-general.php' ) . '?page=pushbeat_box_options">%s</a>', __( 'アプリケーション ID' ) ) ); ?>
	</p></div>
<?php
}
