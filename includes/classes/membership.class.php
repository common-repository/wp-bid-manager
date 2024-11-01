<?php

class WPBM_Member
{

	function bm_validate_membership() {

		$settings = new WPBM_Settings();
		$level = $settings->get('membership_level');
		$status = $settings->get('membership_status');

		$acceptable_levels = array(
			'tier_1',
			'tier_2',
			'tier_3'
		);

		if ( in_array( $level, $acceptable_levels ) && $status == 'active' ) {
			return TRUE;
		} else {
			return FALSE;
		}

	}

	function bm_membership_can_run_reports() {

		$settings = new WPBM_Settings();
		$report_capable = $settings->get('report_capabilities');

		return $report_capable;
	}

	function bm_get_membership_tier() {

		$settings = new WPBM_Settings();
		$member_tier = $settings->get('membership_level');

		return $member_tier;
	}

	function bm_get_user_level($user_id = NULL) {

		global $table_prefix;

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		if ( ! $user_id ) {
			return;
		}

		$user_level = get_user_meta( $user_id, $table_prefix . 'user_level', TRUE );

		return (int)$user_level;

	}

	function bm_get_minimum_user_role() {

		$settings = new WPBM_Settings();
		$minimum_level = $settings->get('minimum_user_level');

		return (int)$minimum_level;
	}

	function bm_get_membership_status() {

		$settings = new WPBM_Settings();
		$membership_stat = $settings->get('membership_status');

		return $membership_stat;
	}

	function bm_get_membership_user_total() {

		$settings = new WPBM_Settings();
		$total_users = $settings->get('num_allowed_users');

		return $total_users;
	}

}