<?
/*
*   Ajax Save Functions
*   Receive, process, and save data from Ajax Requests
*
*   since v0.0.1
*/


/*
*
*   Increase the click count by one before saving
*
*/
function increaseClickCount($prev_clicks) {
    $prev_clicks = (int)$prev_clicks;
    if($prev_clicks !== false) {
        $new_clicks = $prev_clicks + 1;
    } else {
        $new_clicks = 1;
    }

    return $new_clicks;
}


/*
*
*   Ajax increase button count on click
*
*/

add_action( 'wp_ajax_enp_update_button_count', 'enp_update_button_count' );
add_action( 'wp_ajax_nopriv_enp_update_button_count', 'enp_update_button_count_not_logged_in' );

function enp_update_button_count_not_logged_in() {
    // check if logged in is set
    $require_logged_in = enp_require_logged_in();
    $is_logged_in = is_user_logged_in();

    if($require_logged_in === true && $is_logged_in === false) {
        // throw an error
        $btn_slug = $_REQUEST['slug'];
        $pid = $_REQUEST['pid'];
        $btn_type = $_REQUEST['type'];

        // redirect url
        if($btn_type == 'post') {
            $login_url = wp_login_url( get_permalink($pid) );
        } else {
            // TODO: for comments, we need to redirect to the parent post,
            // plus append the comment ID

            // get parent post ID from the comment with a WordPess functino

            $login_url = site_url();
        }


        // return response
        die(json_encode(
            array(
                'response_status'=>'error',
                'pid' => $pid,
                'btn_type'=> $btn_type,
                'btn_slug'=> $btn_slug,
                'message' => 'You must be <a href="'.$login_url.'">logged in</a> to click this button. Please <a href="'.$login_url.'">Log In</a> and try again.'
            )
        ));

    } elseif($require_logged_in === false && $is_logged_in === false) {
        // they're not logged in, and we're not requiring logged in, so we can run this
        enp_update_button_count();
    }

}


function enp_update_button_count() {
    $pid = $_REQUEST['pid'];
    $btn_slug = $_REQUEST['slug'];
    $btn_type = $_REQUEST['type']; // post or comment? We don't need the specific post type

    enp_process_update_button_count($pid, $btn_slug, $btn_type);
}



function enp_process_update_button_count($pid, $btn_slug, $btn_type) {
    // Instantiate WP_Ajax_Response
    $response = new WP_Ajax_Response;

    // Verify Nonces
    if(wp_verify_nonce( $_REQUEST['nonce'], 'enp_button_'.$btn_type.'_'.$btn_slug.'_' . $pid )) {
        global $wpdb;

        if($btn_type === 'post') {
            // set our function names
            $get_meta = 'get_post_meta';
            $update_meta = 'update_post_meta';
        } elseif($btn_type === 'comment') {
            // set our function names
            $get_meta = 'get_comment_meta';
            $update_meta = 'update_comment_meta';
        } else {
            // wait, what kind of post is it then?
            return;
        }

        // get post or comment meta and update it
        $prev_clicks = $get_meta( $pid, 'enp_button_'.$btn_slug, true);

        // increase the click by one
        $new_clicks = increaseClickCount($prev_clicks);

        // update the post or comment meta
        $update_meta( $pid, 'enp_button_'.$btn_slug, $new_clicks );


        $response->add( array(
            'data'  => 'success',
            'supplemental' => array(
                'pid' => $pid,
                'slug' => $btn_slug,
                'type' => $btn_type,
                'message' => 'The click on '.$pid.' has been registered!',
                'count' => $new_clicks,
                'old_count' => $prev_clicks
                ),
            )
        );
    } else {
        $response->add( array(
            'data'  => 'error',
            'supplemental' => array(
                'pid' => $pid,
                'slug' => $btn_slug,
                'type' => $btn_type,
                'message' => 'We couldn\'t update the '.ucfirst($btn_slug).' button count. Reload this page and try again.',
                ),
            )
        );
    }
    // Send the response back
    $response->send();

    // Always end with an exit on ajax
    exit();
}

?>
