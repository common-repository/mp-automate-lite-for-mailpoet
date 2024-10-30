<?php
/**
 * Action hook for daily cron job
 * @return [type] [description]
 */
function mpa_daily_hook() {
	$mpa_run = get_option('mpa_lite_run');

	if($mpa_run != 'daily') {
		return false;
	}

	$mpa_lite_log = get_option('mpa_lite_log');
	if('yes' === $mpa_lite_log)
	mpa_lite_log("Executing Daily Cron Job");

	$mpa_automate = new MPA_Lite_Run();
	$mpa_automate->perform_automate_rules();

}
add_action('mpa_lite_daily_worker','mpa_daily_hook');

/**
 * Action hook for monthly cron job
 * @return [type] [description]
 */
function mpa_monthly_hook() {
	$mpa_run = get_option('mpa_lite_run');

	if($mpa_run != 'month') {
		return false;
	}

	$mpa_lite_log = get_option('mpa_lite_log');
	if('yes' === $mpa_lite_log)
	mpa_lite_log("Executing Monthly Cron Job");

	$mpa_automate = new MPA_Lite_Run();
	$mpa_automate->perform_automate_rules();

}
add_action('mpa_lite_monthly_worker','mpa_monthly_hook');

/**
 * Custom WP_Cron Identifier
 */
function mpa_cron_schedules($schedules) {
	if(!isset($schedules["month"])){
    	$schedules["month"] = array(
            'interval' => 30 * 60 * MINUTE_IN_SECONDS,
            'display' => __('Once every month','mp-automate-lite'));
    }
	return $schedules;
}
add_filter('cron_schedules','mpa_cron_schedules');

if ( !wp_next_scheduled( 'mpa_lite_daily_worker' )) {
	wp_schedule_event(time(), 'daily', 'mpa_lite_daily_worker');
}

if ( !wp_next_scheduled( 'mpa_lite_monthly_worker' )) {
	wp_schedule_event(time(), 'month', 'mpa_lite_monthly_worker');
}

