<?
/*
*   Front End Display Functions
*   Add functionality to the front-end of the website
*
*   since v0.0.1
*/





/*
*
*   Get all buttons. Doesn't really save much time, but
*   could be useful for adding a filter or hook later
*
*/

function enp_get_all_btns() {
    $enp_btns = new Enp_Button();
    $enp_btns = $enp_btns->get_btns();

    return $enp_btns;
}

/*
*
*   Get a button by slug. Doesn't really save any time, but
*   could be useful for adding a filter or hook later
*
*/
function enp_get_btn($slug) {
    $enp_btns = new Enp_Button($slug);

    return $enp_btns;
}


/*
*
*   Get all Buttons, and append them to appropriate content
*
*/
function enp_btn_append_btns( $content ) {
    // don't specify a button slug so we can get all buttons
    $enp_btns = enp_get_all_btns();

    if(!empty($enp_btns[0])) {
        $enp_btn_HTML = '<ul class="enp-btns">';

        foreach($enp_btns as $enp_btn) {
            $enp_btn_HTML .= enp_btn_append_btn_HTML($enp_btn);
        }

        $enp_btn_HTML .= '</ul>';

        $content .= $enp_btn_HTML;
    }

    return $content;
}
add_filter( 'the_content', 'enp_btn_append_btns' );



/*
*
*   ENP Btn HTML for displaying on front-end
*
*/
function enp_btn_append_btn_HTML($enp_btn) {
    global $post;
    var_dump($enp_btn);
    // Create a nonce for this action
    $nonce = wp_create_nonce( 'enp_button_'.$enp_btn->get_btn_slug().'_' . $post->ID );
    // Get link to admin page to trash the post and add nonces to it
    $link_data = '<a href="?action=enp_update_button_count&slug='.$enp_btn->get_btn_slug().'&pid='. $post->ID .'&nonce=' .$nonce .'"
            id="'.$enp_btn->get_btn_slug().'_'. $post->ID.'" class="enp-btn enp-btn--'.$enp_btn->get_btn_slug().'" data-nonce="'. $nonce .'" data-pid="'. $post->ID .'" data-btn-slug="'.$enp_btn->get_btn_slug().'">';

    $enp_btn_HTML = '<li id="'.$enp_btn->get_btn_slug().'-wrap" class="enp-btn-wrap enp-btn-wrap--'.$enp_btn->get_btn_slug().'">
                                '.$link_data.'
                                    <span class="enp-btn__name enp-btn__name--'.$enp_btn->get_btn_slug().'">'
                                        .$enp_btn->get_btn_name().
                                    '</span> '
                                    .( $enp_btn->get_btn_count() > 0 ? '<span class="enp-btn__count enp-btn__count--'.$enp_btn->get_btn_count().'">'
                                                                         .$enp_btn->get_btn_count().
                                                                      '</span>'
                                                                      : '').
                                '</a>
                            </li>';

    return $enp_btn_HTML;
}


/*
*
*   Ajax increase button count on click
*
*/

add_action( 'wp_ajax_enp_update_button_count', 'enp_update_button_count' );
add_action( 'wp_ajax_nopriv_enp_update_button_count', 'enp_update_button_count' );

function enp_update_button_count() {
    // Get the Post ID from the URL
    $pid = $_REQUEST['pid'];
    $btn_slug = $_REQUEST['slug'];

    // Instantiate WP_Ajax_Response
    $response = new WP_Ajax_Response;

    // Verify Nonces
    if(wp_verify_nonce( $_REQUEST['nonce'], 'enp_button_'.$btn_slug.'_' . $pid )) {
        global $wpdb;

        $prev_clicks = get_post_meta( $pid, 'enp_button_'.$btn_slug.'_'.$pid, true);

        $prev_clicks = (int)$prev_clicks;
        if($prev_clicks !== false) {
            $new_clicks = $prev_clicks + 1;
        } else {
            $new_clicks = 1;
        }

        // update the meta
        update_post_meta( $pid, 'enp_button_'.$btn_slug.'_'.$pid, $new_clicks );

        $response->add( array(
            'data'  => 'success',
            'supplemental' => array(
                'pid' => $pid,
                'slug' => $btn_slug,
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
                'message' => 'Error increasing post count ('. $pid .')',
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
