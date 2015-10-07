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

function enp_get_all_btns($args) {
    // we don't want to get any buttons that aren't a part of that post type
    // rather, we ONLY want ones that ARE a part of that post type

    $enp_btns = new Enp_Button($args);
    $enp_btns = $enp_btns->get_btns($args);

    return $enp_btns;
}

/*
*
*   Get a button by slug. Doesn't really save any time, but
*   could be useful for adding a filter or hook later
*
*/
function enp_get_btn($args) {
    $enp_btns = new Enp_Button($args);

    return $enp_btns;
}


/*
*
*   Get all Buttons, and append them to appropriate content
*
*/
function enp_append_post_btns( $content ) {
    global $post;
    $post_id = $post->ID;
    $post_type = get_post_type( $post );

    $args = array(
                'post_id' => $post_id,
                'btn_type' => $post_type
            );

    $content .= enp_btns_HTML($args);

    return $content;
}
add_filter( 'the_content', 'enp_append_post_btns' );


function enp_append_comment_btns( $content ) {
    global $comment;
    $comment_id = $comment->comment_ID;

    $args = array(
                'post_id' => $comment_id,
                'btn_type' => 'comment'
            );

    $content .= enp_btns_HTML($args);

    return $content;
}
add_filter( 'comment_text', 'enp_append_comment_btns' );



/*
*
*   Generate enp button html and return it
*
*/
function enp_btns_HTML($args) {
    $enp_btn_HTML = '';

    $classes = ["enp-btns"];

    if($args['btn_type'] === 'comment') {
        $classes[] = 'enp-btns-comment-'.$args['post_id'];
    } else {
        $classes[] = 'enp-btns-post-'.$args['post_id'];
    }

    $enp_btns = enp_get_all_btns($args);


    if(!empty($enp_btns[0]) ) {
        $enp_btn_HTML = '<ul class="';
        foreach($classes as $class) {
            $enp_btn_HTML .= $class.' ';
        }
        $enp_btn_HTML .= '">';

        foreach($enp_btns as $enp_btn) {
            if(enp_button_exists($enp_btn) === true) {
                $enp_btn_HTML .= enp_btn_append_btn_HTML($enp_btn, $args);
            }

        }

        $enp_btn_HTML .= '</ul>';

    }

    return $enp_btn_HTML;
}



/*
*
*   ENP Btn HTML for displaying on front-end
*
*/
function enp_btn_append_btn_HTML($enp_btn, $args) {

    $post_id = $args['post_id'];
    // Create a nonce for this action
    if($args['btn_type'] === 'comment') {
        $type = 'comment';
    } else {
        $type = 'post';
    }

    $nonce = wp_create_nonce( 'enp_button_'.$type.'_'.$enp_btn->get_btn_slug().'_' . $post_id );
    // Get link to admin page to trash the post and add nonces to it
    $link_data = '<a href="?action=enp_update_button_count&slug='.$enp_btn->get_btn_slug().'&type='.$type.'&pid='. $post_id .'&nonce=' .$nonce .'"
            id="'.$enp_btn->get_btn_slug().'_'.$type.'_'. $post_id.'" class="enp-btn enp-btn--'.$enp_btn->get_btn_slug().' enp-btn--'.$type.'" data-nonce="'. $nonce .'" data-pid="'. $post_id .'" data-btn-type="'.$type.'" data-btn-slug="'.$enp_btn->get_btn_slug().'">';

    // while hard to read, this format is necessary with no breaks between span tags.
    // otherwise, WordPress's filter will add <br/>'s there. No good.
    $enp_btn_HTML = '<li id="'.$enp_btn->get_btn_slug().'-wrap" class="enp-btn-wrap enp-btn-wrap--'.$enp_btn->get_btn_slug().'">'.$link_data.'<span class="enp-btn__name enp-btn__name--'.$enp_btn->get_btn_slug().'">'
                                        .$enp_btn->get_btn_name().
                                    '</span><span class="enp-btn__count enp-btn__count--'.$enp_btn->get_btn_slug().($enp_btn->get_btn_count() > 0 ? '' : ' enp-btn__count--zero').'">'
                                        .$enp_btn->get_btn_count().'</span></a>
                            </li>';

    return $enp_btn_HTML;
}

/*
*
*   Basic check to make sure that our object isn't full of null values
*
*/
function enp_button_exists($enp_btn) {

    if($enp_btn->get_btn_slug() === NULL) {
        return false;
    } else {
        return true;
    }
}


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
add_action( 'wp_ajax_nopriv_enp_update_button_count', 'enp_update_button_count' );

function enp_update_button_count() {
    // Get the Post ID from the URL
    $pid = $_REQUEST['pid'];
    $btn_slug = $_REQUEST['slug'];
    $btn_type = $_REQUEST['type'];

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
