<?php
if(!defined('ABSPATH')) exit;
/**
 * Plugin Name:       MP Automate Lite
 * Description:       MP Automate Lite is an add-on for MailPoet 3 to automatically manage subscribers across multiple lists.
 * Version:           1.0.0
 * Author:            MP Automate
 * Author URI:        https://mailpoetautomate.com
 * Text Domain:       mp-automate-lite
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if(!defined( 'WPINC' )){
	die;
}

if(!defined('ABSPATH')){
	exit;
}

define( 'MPA_LITE_VERSION', '1.0' );
define( 'MPA_LITE_ASSET_VERSION', '1.0' );

function mpa_load_text_domain() {

	load_plugin_textdomain( 'mp-automate-lite', false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'mpa_load_text_domain' );

/**
 * Define root path
 */
if(!defined('MPA_LITE_ROOT_PATH')){
	$mpa_root = plugin_dir_path(__FILE__);
	define('MPA_LITE_ROOT_PATH', $mpa_root);
}

/**
 * Define Mailpoet Root url
 */
if(!defined('MPA_LITE_MAILPOET_ROOT_URL')){
	$mpa_mailpoet_root_url = plugins_url().'/mailpoet';
	define('MPA_LITE_MAILPOET_ROOT_URL', $mpa_mailpoet_root_url);
}

/**
 * If php version is lower
 */
if(version_compare(phpversion(), '5.6.0', '<')){
	function mailpoet_mpa_php_version_notice(){
		?>
		<div class="error">
			<p><?php _e('MailPoet plugin requires PHP version 5.6.0 or newer, Please upgrade your PHP.', 'mp-automate-lite'); ?></p>
		</div>
		<?php
	}
	add_action('admin_notices', 'mailpoet_mpa_php_version_notice');
	return;
}

/**
 * Include plugin.php to detect plugin.
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
 * Check MailPoet active
 */
if(!is_plugin_active('mailpoet/mailpoet.php')){
	add_action('admin_notices', function(){
		?>
		<div class="error">
			<p><?php _e('MP Automate Lite plugin requires MailPoet plugin, please activate MailPoet first to use MP Automate Lite.', 'mp-automate-lite'); ?></p>
		</div>
		<?php
	});
	return;	// If not then return
}

/**
 * The core plugin class
 * that is used to define Admin page and settings.
 */
require_once MPA_LITE_ROOT_PATH . 'includes/class-mpa-lite-handler.php';

/**
 * The plugin class
 * that is used to process automation rules
 */
require_once MPA_LITE_ROOT_PATH . 'includes/class-mpa-lite-run.php';

/**
 * Cron job scheduler file.
 */
require_once MPA_LITE_ROOT_PATH . 'includes/mpa-lite-scheduler.php';

function mpa_lite_activate() {
	$upload = wp_upload_dir();
	$upload_dir = $upload['basedir'];
	$upload_dir = $upload_dir . '/mp-automate-lite';
	if(!is_dir($upload_dir)) {
		mkdir($upload_dir,0755);
	}
	$mpa_log_file = trailingslashit($upload_dir).'mpa-log.txt';
	if(!file_exists($mpa_log_file)) {
		$file = fopen($mpa_log_file,"a+");
		fwrite($file,'');
		fclose($file);
		//file_put_contents($mpa_log_file, '', FILE_APPEND);

	}
}
register_activation_hook( __FILE__, 'mpa_lite_activate' );

function mpa_lite_log($message) {
	$time_server_info = '['.date('D Y-m-d h:i:s A').'] ';
	$log_message = $time_server_info . $message;
	$upload = wp_upload_dir();
	$upload_dir = $upload['basedir'];
	$upload_dir = $upload_dir . '/mp-automate-lite';
	if(!is_dir($upload_dir)) {
		mkdir($upload_dir,0755);
	}
	$mpa_log_file = trailingslashit($upload_dir).'mpa-log.txt';
	$file = fopen($mpa_log_file,"a+");
	//file_put_contents($mpa_log_file, PHP_EOL . $log_message, FILE_APPEND);
	fwrite($file,PHP_EOL . $log_message);
	fclose($file);

}
