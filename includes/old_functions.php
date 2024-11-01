<?php

function bm_main_settings() {
    $settings = get_option( 'bid_manager_settings' );
    $settings = json_decode( $settings );
    $settings = (array)$settings;

    return $settings;
}

function bm_save_main_settings($new_settings) {
    if ( ! is_array( $new_settings ) ) {
        return '<div class="error"><p>' . _e( 'You must provide an array.' ) . '</p></div>';
    }

    $current = bm_main_settings();

    $new = array_merge( $current, $new_settings );

    $new_settings = json_encode( $new );
    update_option( 'bid_manager_settings', $new_settings );
}