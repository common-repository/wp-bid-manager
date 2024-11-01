<?php

require_once ("old_functions.php");

function bm_update_09132017() {
    global $wpdb;
    $full = array(); // Initiate an array to build
    $user_id = get_option('bm_free_user');

    /**
     * Get the membership options and turn them into an array then add it to $full
     */
    $membership = get_option('bm_membership');
    $membership = (array)json_decode($membership);

    $full = array_merge($full, $membership);

    /**
     * Get the bid manager settings and merge it
     */

    $settings = get_option('bid_manager_settings');
    $settings = (array)json_decode($settings);

    $full = array_merge($full, $settings);

    /**
     * Get the bid actions for front end setting and merge it
     */
    $actions = get_option('bm_bid_response_actions');
    $actions = array(
        'bm_bid_response_actions' => $actions
    );

    $full = array_merge($full, $actions);

    /**
     * This is a user specific usermeta that is being converted and merged
     */

    $google_api_key = get_user_meta($user_id, 'bm_google_api_key', TRUE);
    $google_api_key = array(
        'bm_google_api_key' => $google_api_key
    );

    $full = array_merge($full, $google_api_key);

    /**
     * Again, the user is assigned the value for the invite
     */

    $bm_invite_pg = get_user_meta($user_id, 'bm_invite_page', TRUE);
    $bm_invite_pg = array(
        'bm_invite_page' => $bm_invite_pg
    );

    $full = array_merge($full, $bm_invite_pg);

    /**
     * Another user blunder, set the email copy
     */

    $email_copy = get_user_meta( $user_id, 'bm_email_content', TRUE );
    $email_copy = array(
        'bm_email_content' => $email_copy
    );

    $full = array_merge($full, $email_copy);

    /**
     * Email subject...
     */

    $email_subj = get_user_meta( $user_id, 'bm_subject_line', TRUE );
    $email_subj = array(
        'bm_subject_line' => $email_subj
    );

    $full = array_merge($full, $email_subj);

    /**
     * Email from...
     */

    $bm_email_from = get_user_meta( $user_id, 'email_from_name', TRUE );
    $bm_email_from = array(
        'email_from_name' => $bm_email_from
    );

    $full = array_merge($full, $bm_email_from);

    /**
     * From email...
     */

    $bm_from_email = get_user_meta( $user_id, 'bm_from_line', TRUE );
    $bm_from_email = array(
        'bm_from_line' => $bm_from_email
    );

    $full = array_merge($full, $bm_from_email);

    /**
     * Last thing to do is wrangle up all the company info
     */

    if ($wpdb->get_results("SHOW TABLES LIKE 'bm_user'")) {
        $query = "SELECT * FROM " . BM_USER . " LIMIT 1";
        $record = $wpdb->get_row($query);

        $company_info = array(
            'bm_company_name' => $record->bmuser_busname,
            'bm_company_poc' => $record->bmuser_poc,
            'bm_company_phone' => $record->bmuser_phone,
            'bm_company_email' => $record->bmuser_email,
            'bm_company_street' => $record->bmuser_street,
            'bm_company_street2' => $record->bmuser_street_two,
            'bm_company_city' => $record->bmuser_city,
            'bm_company_state' => $record->bmuser_state,
            'bm_company_zip' => $record->bmuser_zip,
            'bm_company_lat' => $record->lat,
            'bm_company_lng' => $record->lng
        );

        $full = array_merge($full, $company_info);


        update_option('wpbm_settings', $full);
    }

    delete_option('bm_membership');
    delete_option('bid_manager_settings');
    delete_option('bm_bid_response_actions');
    delete_user_meta($user_id, 'bm_google_api_key');
    delete_user_meta($user_id, 'bm_invite_page');
    delete_user_meta($user_id, 'bm_email_content');
    delete_user_meta($user_id, 'bm_subject_line');
    delete_user_meta($user_id, 'email_from_name');
    delete_user_meta($user_id, 'bm_from_line');
    if ($wpdb->get_results("SHOW TABLES LIKE 'bm_user'")) {
        $query = "DROP TABLE " . BM_USER;
    }
    $wpdb->get_results($query);
}

function bm_update_08312017() {

    $managing_user = get_option( 'bm_free_user' );
    $current_key = get_user_meta( $managing_user, 'bm_google_api_key', TRUE );

    // If there is a managing user set, lets also stuff that into our bid_manager_settings
    if ($managing_user) {
        $more_settings = array(
            'free_user' => $managing_user
        );

        bm_save_main_settings($more_settings);
    }

    // If the user has an API key in their usermeta table lets put it in the options table where it really should be

    if ($current_key) {
        $more_settings = array(
            'bm_google_api_key' => $current_key
        );

        bm_save_main_settings($more_settings);
    }
}

function bm_update_08112017() {
    global $wpdb;

    $query = "SHOW COLUMNS FROM " . BM_BIDS . " LIKE 'bid_notes'";
    $result = $wpdb->get_results($query);

    if (!$result) {
        $query = "ALTER TABLE " . BM_BIDS . " ADD bid_notes VARCHAR( 4500 ) after job_zip";
        $wpdb->get_results($query);
    }

    $query = "SHOW COLUMNS FROM " . BM_BIDS . " LIKE 'bid_options'";
    $result = $wpdb->get_results($query);

    if (!$result) {
        $query = "ALTER TABLE " . BM_BIDS . " ADD bid_options VARCHAR( 1000 ) after has_response";
        $wpdb->get_results($query);
    }

}

// Very first update
function bm_update_02162016() {

    global $wpdb;
    global $table_prefix;

    /*
     * Up until version 1.1.3 we did not include the wp prefix for the tables.
     * Therefore, we have to update all old table names to the new structure
     */


    if ($wpdb->get_results("SHOW TABLES LIKE 'bm_notifications'")) {
        // Change the bm_notifications table to whatever the users prefix is
        $query = "RENAME TABLE `bm_notifications` TO `" . $table_prefix . "bm_notifications`";
        $wpdb->get_results($query);
    }

    if ($wpdb->get_results("SHOW TABLES LIKE 'bm_bids'")) {
        // Change the bm_bids table to whatever the users prefix is
        $query = "RENAME TABLE `bm_bids` TO `" . $table_prefix . "bm_bids`";
        $wpdb->get_results($query);
    }


    if ($wpdb->get_results("SHOW TABLES LIKE 'bm_bids_responses'")) {
        // Change the bm_bids_responses table to whatever the users prefix is
        $query = "RENAME TABLE `bm_bids_responses` TO `" . $table_prefix . "bm_bids_responses`";
        $wpdb->get_results($query);
    }


    if ($wpdb->get_results("SHOW TABLES LIKE 'bm_responder'")) {
        // Change the bm_responder table to whatever the users prefix is
        $query = "RENAME TABLE `bm_responder` TO `" . $table_prefix . "bm_responder`";
        $wpdb->get_results($query);
    }


    if ($wpdb->get_results("SHOW TABLES LIKE 'bm_user'")) {
        // Change the bm_user table to whatever the users prefix is
        $query = "RENAME TABLE `bm_user` TO `" . $table_prefix . "bm_user`";
        $wpdb->get_results($query);
    }


    if ($wpdb->get_results("SHOW TABLES LIKE 'bm_responder_emails'")) {
        // Change the bm_responder_emails table to whatever the users prefix is
        $query = "RENAME TABLE `bm_responder_emails` TO `" . $table_prefix . "bm_responder_emails`";
        $wpdb->get_results($query);
    }

}