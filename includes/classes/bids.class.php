<?php
class WPBM_Bids {

    function get_past_bids_table($date) {
        global $wpdb;
        $content = '';
        $query   = "SELECT bid_id, job_name, job_street, job_city, job_state, job_zip, date_needed FROM " . BM_BIDS . " WHERE date_needed < %s AND accepted_flag = %d";
        $data    = array(
            $date,
            0
        );
        $query   = $wpdb->prepare( $query, $data );
        $results = $wpdb->get_results( $query );

        if ( $results ) {
            $content .= '<div class="archived_bids blue_table">';

            $content .= '<h2>Past Bids Submitted</h2>';

            $content .= '<table id="conPastBids" class="blue_table" border="1" bordercolor="#000" cellpadding="5" width="100%">';
            $content .= '<thead>';
            $content .= '<tr>';
            $content .= '<th>';
            $content .= 'Bid Id';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'Job';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'Street';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'City';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'State';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'Zip Code';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'Bid Required By';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'Take Action';
            $content .= '</th>';
            $content .= '</tr>';
            $content .= '</thead>';
            $content .= '<tbody>';

            foreach ( $results as $record ) {

                $link = 'admin.php';
                $params = array( 'bmuser_bid_past' => $record->bid_id );
                $link = add_query_arg( $params );
                $link = esc_url($link, '', 'db');

                // $arr_params = array( 'bmuser_bid_past' => $record->bid_id );
                $content .= '<tr>';
                $content .= '<td>' . $record->bid_id . '</td>';
                $content .= '<td>' . stripslashes( $record->job_name ) . '</td>';
                $content .= '<td>' . $record->job_street . '</td>';
                $content .= '<td>' . $record->job_city . '</td>';
                $content .= '<td>' . $record->job_state . '</td>';
                $content .= '<td>' . $record->job_zip . '</td>';
                $content .= '<td>' . date( 'F jS, Y', strtotime( $record->date_needed ) ) . '</td>';
                $content .= '<td><a class="button-primary" href="' . $link . '">View Bid &raquo;</a></td>';
                $content .= '</tr>';
            }

            $content .= '</tbody>';
            $content .= '</table>';

            $content .= '</div>';

        } else {
            $content .= '';
        }

        return $content;
    }

    function get_accepted_bids_table() {
        global $wpdb;
        $content = '';
        $query   = "SELECT * FROM " . BM_BIDS_RESPONSES . " LEFT OUTER JOIN " . BM_BIDS . " ON " . BM_BIDS . ".bid_id=" . BM_BIDS_RESPONSES . ".bid_id WHERE bid_accepted = %d";
        $data    = array(
            1
        );
        $query   = $wpdb->prepare( $query, $data );
        $results = $wpdb->get_results( $query );


        if ( $results ) {
            $content .= '<div class="contractors_bids_accepted blue_table">';

            $content .= '<h2>Bids You Have Accepted</h2>';

            $content .= '<table id="conAccepted" class="blue_table" border="1" bordercolor="#000" cellpadding="5" width="100%">';
            $content .= '<thead>';
            $content .= '<tr>';
            $content .= '<th>';
            $content .= 'Bid Id';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'Name';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'Job';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'Street';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'City';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'State';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'Zip Code';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'Bid Required By';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'Quote Total';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'Take Action';
            $content .= '</th>';
            $content .= '</tr>';
            $content .= '</thead>';
            $content .= '<tbody>';

            foreach ( $results as $record ) {

                $link = get_permalink();
                $params = array( 'bid_accepted' => $record->bid_id, 'response_id' => $record->id );
                $link = add_query_arg( $params, $link );
                if (is_admin()) {
                    $link = 'admin.php';
                    $params = array( 'page' => 'bid_manager_dashboard', 'bid_accepted' => $record->bid_id, 'response_id' => $record->id );
                    $link = add_query_arg( $params, $link );
                    $link = esc_url($link, '', 'db');
                }

                // $arr_params = array( 'bid_accepted' => $record->bid_id, 'responder_id' => $record->responder_id );
                $content .= '<tr>';
                $content .= '<td>' . $record->bid_id . '</td>';
                $content .= '<td>' . stripslashes( $record->responder_busname ) . '</td>';
                $content .= '<td>' . stripslashes( $record->job_name ) . '</td>';
                $content .= '<td>' . $record->job_street . '</td>';
                $content .= '<td>' . $record->job_city . '</td>';
                $content .= '<td>' . $record->job_state . '</td>';
                $content .= '<td>' . $record->job_zip . '</td>';
                $content .= '<td>' . date( 'F jS, Y', strtotime( $record->date_needed ) ) . '</td>';
                $content .= '<td>$ ' . number_format( $record->quoted_total, 2 ) . '</td>';
                $content .= '<td><a class="button-primary" href="' . $link . '">View Bid &raquo;</a></td>';
                $content .= '</tr>';
            }

            $content .= '</tbody>';
            $content .= '</table>';

            $content .= '</div>';
            $content .= '</div>';

        } else {
            $content .= '';
        }

        return $content;
    }

    function get_bids_with_responses_table($date) {
        global $wpdb;
        $content = '';
        $query   = "SELECT * FROM " . BM_BIDS . " WHERE date_needed > %s AND accepted_flag = %d AND has_response > %d";
        $data    = array(
            $date,
            0,
            0
        );
        $query   = $wpdb->prepare( $query, $data );
        $results = $wpdb->get_results( $query );

        if ( $results ) {
            $content .= '<div class="responder_responses blue_table">';

            $content .= '<h2>Bids With Responses (not accepted)</h2>';

            $content .= '<table id="conSupResponse" class="blue_table" border="1" bordercolor="#000" cellpadding="5" width="100%">';
            $content .= '<thead>';
            $content .= '<tr>';
            $content .= '<th>';
            $content .= 'Bid Id';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'Job Name';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'Street';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'City';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'State';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'Zip Code';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'Bid Required By';
            $content .= '</th>';
            $content .= '<th>';
            $content .= '# of Bids';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'Take Action';
            $content .= '</th>';
            $content .= '</tr>';
            $content .= '</thead>';
            $content .= '<tbody>';

            foreach ( $results as $record ) {
                // $arr_params = array( 'bid_response' => $record->bid_id );
                $content .= '<tr>';
                $content .= '<td>' . $record->bid_id . '</td>';
                $content .= '<td>' . stripslashes( $record->job_name ) . '</td>';
                $content .= '<td>' . $record->job_street . '</td>';
                $content .= '<td>' . $record->job_city . '</td>';
                $content .= '<td>' . $record->job_state . '</td>';
                $content .= '<td>' . $record->job_zip . '</td>';
                $content .= '<td>' . date( 'F jS, Y', strtotime( $record->date_needed ) ) . '</td>';
                $content .= '<td>' . $record->has_response . '</td>';
                if (is_admin()) {
                    $content .= '<td><a class="button-primary" href="' . BM_CDBOARD . '&amp;bid_response=' . $record->bid_id . '">View Bid &raquo;</a></td>';
                } else {
                    $content .= '<td><a class="button-primary" href="' . get_permalink() . '?bid_response=' . $record->bid_id . '">View Bid &raquo;</a></td>';
                }
                $content .= '</tr>';
            }

            $content .= '</tbody>';
            $content .= '</table>';

            $content .= '</div>';

        } else {
            $content .= '';
        }

        return $content;
    }

    function get_active_bids_table($date) {
        global $wpdb;
        $content = '';
        $query = "SELECT * FROM " . BM_BIDS . " WHERE date_needed > %s AND accepted_flag = %d";

        $data = array(
            $date,
            0
        );
        $query = $wpdb->prepare( $query, $data );
        $results = $wpdb->get_results( $query );

        if ( sanitize_text_field( isset($_GET[ 'message' ]) ) == "bid_saved" ) {
            $content .= '<p class="success">Bid saved successfully! &ndash; Click the <em>"View Bid &raquo;"</em> action to invite suppliers to respond.</p>';
        }


        if ( $results ) {
            $content .= '<h1>Bid Manager Dashboard</h1>';

            $content .= '<div class="active_bids blue_table">';
            $content .= '<h2>Active Bid Requests</h2>';

            $content .= '<table id="conActiveBids" class="blue_table" border="1" bordercolor="#000" cellpadding="5" width="100%">';
            $content .= '<thead>';
            $content .= '<tr>';
            $content .= '<th>';
            $content .= 'Bid Id';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'Job';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'Street';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'City';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'State';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'Zip Code';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'Bid Required By';
            $content .= '</th>';
            $content .= '<th>';
            $content .= 'Take Action';
            $content .= '</th>';
            $content .= '</tr>';
            $content .= '</thead>';
            $content .= '<tbody>';

            foreach ( $results as $record ) {
                $content .= '<tr>';
                $content .= '<td>' . $record->bid_id . '</td>';
                $content .= '<td>' . stripslashes( $record->job_name ) . '</td>';
                $content .= '<td>' . $record->job_street . '</td>';
                $content .= '<td>' . $record->job_city . '</td>';
                $content .= '<td>' . $record->job_state . '</td>';
                $content .= '<td>' . $record->job_zip . '</td>';
                $content .= '<td>' . date( 'F jS, Y', strtotime( $record->date_needed ) ) . '</td>';
                if (is_admin()) {
                    $content .= '<td><a class="button-primary" href="' . BM_CDBOARD . '&amp;bmuser_bid_active=' . $record->bid_id . '">View Bid &raquo;</a></td>';
                } else {
                    $content .= '<td><a class="button-primary" href="' . get_permalink() . '?bmuser_bid_active=' . $record->bid_id . '">View Bid &raquo;</a></td>';
                }
                $content .= '</tr>';
            }

            $content .= '</tbody>';
            $content .= '</table>';

            $content .= '</div>';

        } else {
            $content .= '<p>There are no active bids.</p>';
            if (is_admin()) {
                $content .= '<p><a class="button" href="' . BM_CBID . '">Create A Bid &raquo</a></p>';
            }
        }

        return $content;
    }
}