<?php
/**
 * Mailpoet Automate Run
 * @since      1.0.0
 * @package    Run Automate Rules
 * @subpackage mp-automate-lite/includes
 * @author     Lucy Eind
 */
use MailPoet\Models\Segment;
class MPA_Lite_Run {
	public function perform_automate_rules() {

		$automation_rules = get_option('mpa_lite_rules');
		$mpa_lite_log = get_option('mpa_lite_log');
		$mpa_lite_log = $mpa_lite_log === 'yes' ? true : false;
		$mpa_lists = Segment::where_not_equal('type', Segment::TYPE_WP_USERS)->findArray();
		if(!is_array($mpa_lists)) {
			if($mpa_lite_log) 
			mpa_lite_log('No lists found');
			return false;
		}
		if(is_array($automation_rules)) {
			foreach($mpa_lists as $key => $value) {
				$lists[$value['id']] = $value['name'];
			}
			foreach($automation_rules as $rule) {
				$action = $rule['action'];
				$list1 = $rule['list1'];
				$list2 = $rule['list2'];
				if($mpa_lite_log) {
					if($action === 'remove') {
						mpa_lite_log('Automate Rule Start: Remove subscribers from '.$lists[$list1].' when subscribed to '.$lists[$list2]);
					} else {
						mpa_lite_log('Automate Rule Start: Adding subscribers to '.$lists[$list1].' when subscribed to '.$lists[$list2]);
					}	
				}
				if($rule['active'] === 'yes') {
					if($mpa_lite_log)
					mpa_lite_log('Automation rule is active');
					$common_subscribers = $this->get_common_subscribers($list1, $list2, $action);
					
					if($common_subscribers) {
						if($mpa_lite_log)
						mpa_lite_log(count($common_subscribers) . ' found');
						if($action === 'remove') {
							foreach($common_subscribers as $subsc_id) {
								
								try {
									if($mpa_lite_log) {
										mpa_lite_log('Unsubscribing Subscriber ID '.$subsc_id.' from '.$lists[$list1]);
									}
									$unsubscribe_subscriber = \MailPoet\API\API::MP('v1')->unsubscribeFromList($subsc_id,$list1);
									if($mpa_lite_log) {
										mpa_lite_log('Subscriber unsubscribed successfully from '.$lists[$list1]);
									}
								} catch(Exception $exception) {
									if($mpa_lite_log) {
										mpa_lite_log('Subscriber not unsubscribed successfully from '.$lists[$list1]);
										mpa_lite_log('Error message '.$exception->getMessage());
									}
								}
							}
						} else {
							foreach($common_subscribers as $subsc_id) {
								
								try {
									if($mpa_lite_log) {
										mpa_lite_log('Subscribing Subscriber ID '.$subsc_id.' to '.$lists[$list1]);
									}
									$subscribe_subscriber = \MailPoet\API\API::MP('v1')->subscribeToList($subsc_id,$list1);
									if($mpa_lite_log) {
										mpa_lite_log('Subscriber subscribed successfully to ' .$lists[$list1]);
									}
								} catch(Exception $exception) {
									if($mpa_lite_log) {
										mpa_lite_log('Subscriber not subscribed successfully to ' .$lists[$list1]);
										mpa_lite_log('Error message '.$exception->getMessage());
									}
								}
							}
						}
					} else {
						if($mpa_lite_log)
						mpa_lite_log('No subscribers found');
					}
				} else {
					if($mpa_lite_log)
					mpa_lite_log('Automation rule is inactive');
				}

				if($mpa_lite_log) {
					if($action === 'remove') {
						mpa_lite_log('Automate Rule End: Remove subscribers from '.$lists[$list1].' when subscribed to '.$lists[$list2]);
					} else {
						mpa_lite_log('Automate Rule End: Adding subscribers to '.$lists[$list1].' when subscribed to '.$lists[$list2]);
					}	
				}
				
				
			}
		}
	}

	public function get_common_subscribers($list1, $list2, $action) {
		
		global $wpdb;
		$mpa_segment_table = $wpdb->prefix.'mailpoet_subscriber_segment';
		if($action === 'add') { 
			$query2 = $wpdb->prepare("SELECT subscriber_id FROM $mpa_segment_table WHERE segment_id = %d and status = 'subscribed'",$list1);
			$subscribed_subscribers = $wpdb->get_results($query2,'ARRAY_A');
			if(!empty($subscribed_subscribers))
			{
				$subscribed_subscribers = wp_list_pluck($subscribed_subscribers, 'subscriber_id');
			}
			
			$query = $wpdb->prepare("SELECT subscriber_id FROM $mpa_segment_table WHERE segment_id = %d and status = 'subscribed'",$list2);
			$subscribers = $wpdb->get_results($query,'ARRAY_A');
			if(!empty($subscribers)) {
				$subscribers = wp_list_pluck($subscribers, 'subscriber_id');
			}
			$final_subscribers = array_diff($subscribers, $subscribed_subscribers);
			$subscribers = $final_subscribers;
		} else {
			$s1 = $s2 = "subscribed";
			
			$query = $wpdb->prepare("SELECT v1.subscriber_id FROM $mpa_segment_table v1 
			INNER JOIN $mpa_segment_table v2 
			ON (v1.subscriber_id = v2.subscriber_id) 
			WHERE v1.segment_id = %d AND v2.segment_id = %d
			AND v1.status = %s AND v2.status = %s",$list1,$list2,$s1,$s2);
			$subscribers = $wpdb->get_results($query,'ARRAY_A');
			if(!empty($subscribers))
			$subscribers = wp_list_pluck($subscribers, 'subscriber_id');
		}

		return $subscribers;
	}
}