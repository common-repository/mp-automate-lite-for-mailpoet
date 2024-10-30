<?php
/**
 * The core plugin class.
 * @since      1.0.0
 * @package    MPA Lite Handler
 * @subpackage mp-automate-lite/includes
 * @author     Lucy Eind
 */
use MailPoet\Models\Segment;
if(!class_exists('Mailpoet_Automate_Handler')){
	
	class MPA_Lite_Handler
	{
		/**
		 * Properties
		 */
        protected $page_name = 'mailpoet_page_mp-automate-lite';
		/**
		 * Initialize the class
		 */
		public static function init()
		{
			$_this_class = new MPA_Lite_Handler();
			return $_this_class;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			// Admin Menu
			add_action('admin_menu', array($this, 'admin_menus'), 33); // run the hook after mailpoet menu load
			add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
			add_action('mpa_settings_tab', array($this, 'render_mpa_lite_settings_tab'));
			add_action('mpa_settings_tab_content', array($this, 'render_mpa_lite_settings_tab_content'));
			add_action('wp_ajax_save_mpa_lite_rules', array($this, 'save_mpa_lite_rules'));
			add_action('wp_ajax_save_mpa_lite_log_settings', array($this, 'save_mpa_lite_log_settings'));
			add_action('wp_ajax_reset_mpa_lite_log', array($this, 'reset_mpa_lite_log'));
		}

		/**
		 * Admin menu
		 */
		public function admin_menus()
		{
			// Mailpoet Automate Menu
			$page_title = __('MP Automate Lite', 'mp-automate-lite');
			add_submenu_page('mailpoet-newsletters', $page_title, $page_title, 'manage_options', 'mp-automate-lite', array($this, 'mpa_lite_handler_page'));

		}

		/**
		 * Mailpoet Automate Handler Page
		 */
		public function mpa_lite_handler_page() {
			global $mpa_lite_active_tab;
			$tab = 'mpa-lite-settings';
			if(isset($_GET['tab']))
			$tab = sanitize_text_field($_GET['tab']);
			
			$mpa_lite_active_tab = $tab; ?>
			<h2 class="nav-tab-wrapper">
			<?php 
				do_action( 'mpa_settings_tab' );
			?>
			</h2>
			<?php
				do_action( 'mpa_settings_tab_content' );
		}

		/**
		 * Display tabs on plugin settings page
		 */
		public function render_mpa_lite_settings_tab() {
			global $mpa_lite_active_tab;
			?>
			<a class="nav-tab <?php echo $mpa_lite_active_tab == 'mpa-lite-settings' || '' ? 'nav-tab-active': ''; ?>" href="<?php echo admin_url('admin.php?page=mp-automate-lite&tab=mpa-lite-settings');?>"><?php _e('Automate Settings','mp-automate-lite'); ?></a>
			<a class="nav-tab <?php echo $mpa_lite_active_tab == 'mpa-lite-log' ? 'nav-tab-active': ''; ?>" href="<?php echo admin_url('admin.php?page=mp-automate-lite&tab=mpa-lite-log');?>"><?php _e('Log','mp-automate-lite'); ?></a>
			<a class="nav-tab <?php echo $mpa_lite_active_tab == 'mpa-lite-upgrade' ? 'nav-tab-active': ''; ?>" href="<?php echo admin_url('admin.php?page=mp-automate-lite&tab=mpa-lite-upgrade');?>"><?php _e('Upgrade','mp-automate-lite'); ?></a>
            <?php
		}

		/**
		 * Display tab content on plugin settings page
		 */
		public function render_mpa_lite_settings_tab_content() {
			global $mpa_lite_active_tab;
			$list_fetched = false;
			
			
			if( $mpa_lite_active_tab == 'mpa-lite-settings' || '' ) {
				$sagments = Segment::where_not_equal('type', Segment::TYPE_WP_USERS)->findArray();
				if(!is_array($sagments)): 
				$sagments = false;
				endif;
				$automation_rules = get_option('mpa_lite_rules');
				$automation_run = get_option('mpa_lite_run');

				include MPA_LITE_ROOT_PATH . 'templates/mpa-lite-settings.php';
			} else if( $mpa_lite_active_tab == 'mpa-lite-log'){
				$mpa_log = get_option('mpa_lite_log');
				$upload = wp_upload_dir();
				$upload_dir = $upload['baseurl'];
				$base_dir = $upload['basedir'];
				$upload_dir = $upload_dir . '/mp-automate-lite';
				
				
				$base_dir = $base_dir . '/mp-automate-lite/mpa-log.txt';
				$time_updated = (filemtime($base_dir));
				$log_file = trailingslashit($upload_dir).'mpa-log.txt?ver='.$time_updated;
				if(!file_exists($base_dir)) {
					$log_file = '';
				} else {
					if(!filesize($base_dir))
						$log_file = '';
				}
				include MPA_LITE_ROOT_PATH . 'templates/mpa-lite-log.php';

			} else if( $mpa_lite_active_tab == 'mpa-lite-upgrade' ) {
				include MPA_LITE_ROOT_PATH . 'templates/mpa-lite-upgrade.php';
			}
		}

		public function enqueue_scripts($page) {
			if($this->page_name == $page) {
				wp_enqueue_style('mpa-lite-css', plugins_url('/assets/css/mp-automate-lite.css', dirname(__FILE__)),MPA_LITE_ASSET_VERSION);
				wp_enqueue_script('mpa-lite-js', plugins_url('/assets/js/mp-automate-lite.js', dirname(__FILE__)), array('jquery'),MPA_LITE_ASSET_VERSION);
				$script_args = array(
					'ajaxurl' => admin_url( 'admin-ajax.php')
				);
				wp_localize_script('mpa-lite-js', 'mpa_lite', $script_args);
			}
		}

		public function save_mpa_lite_rules() {
			parse_str( $_POST['form_data'], $postdata );
			if(isset($postdata['automate_rules'])) {
				$automate_rules = $postdata['automate_rules'];
				//sanitizing rule data
				$sanitized_rules = array();
				foreach($automate_rules as $r_key => $rules) {
					foreach($rules as $key => $rule_data) {
						$sanitized_rules[$r_key][$key] = sanitize_text_field($rule_data);
					}
				}
				update_option('mpa_lite_rules',$sanitized_rules);
			}
			if(isset($postdata['mpa_lite_run'])) {
				$run_freq = sanitize_text_field($postdata['mpa_lite_run']);
				update_option('mpa_lite_run',$run_freq);
			}
		}

		public function save_mpa_lite_log_settings() {
			$form_data = sanitize_text_field($_POST['form_data']);
			parse_str( $form_data, $postdata );
			if(isset($postdata['mpa_lite_log'])) {
				update_option('mpa_lite_log', $postdata['mpa_lite_log']);
			}
			wp_send_json_success();
		}

		public function reset_mpa_lite_log() {
			$upload = wp_upload_dir();
			$upload_dir = $upload['basedir'];
			$upload_dir = $upload_dir . '/mp-automate-lite';
			if(!is_dir($upload_dir)) {
				mkdir($upload_dir,0755);
			}
			$mpa_log_file = trailingslashit($upload_dir).'mpa-log.txt';
			//file_put_contents($mpa_log_file, '');
			$file = fopen($mpa_log_file,"w");
			//file_put_contents($mpa_log_file, PHP_EOL . $log_message, FILE_APPEND);
			fwrite($file,'');
			fclose($file);
			wp_send_json_success();
		}

	}
	
	/**
	 * Instentiate core class
	 */
	MPA_Lite_Handler::init();
}	