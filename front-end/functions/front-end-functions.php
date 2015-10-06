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
function enp_append_post_btns( $content ) {
    global $post;
    $post_id = $post->ID;

    $content .= enp_btns_HTML($post_id);

    return $content;
}
add_filter( 'the_content', 'enp_append_post_btns' );


function enp_append_comment_btns( $content ) {
    global $comment;
    $comment_id = $comment->comment_ID;
    $is_comment = true;

    $content .= enp_btns_HTML($comment_id, $is_comment);

    return $content;
}
add_filter( 'comment_text', 'enp_append_comment_btns' );



/*
*
*   Generate enp button html and return it
*
*/
function enp_btns_HTML($id, $is_comment = false) {
    $classes = ["enp-btns"];

    if($is_comment === true) {
        $classes[] = 'enp-btns-comment-'.$id;
    } else {
        $classes[] = 'enp-btns-post-'.$id;
    }

    // don't specify a button slug so we can get all buttons
    $enp_btns = enp_get_all_btns();

    if(!empty($enp_btns[0])) {
        $enp_btn_HTML = '<ul class="';
        foreach($classes as $class) {
            $enp_btn_HTML .= $class.' ';
        }
        $enp_btn_HTML .= '">';

        foreach($enp_btns as $enp_btn) {

            $enp_btn_HTML .= enp_btn_append_btn_HTML($enp_btn, $id, $is_comment);
            var_dump($enp_btn);
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
function enp_btn_append_btn_HTML($enp_btn, $post_id, $is_comment = false) {
    // Create a nonce for this action
    if($is_comment === true) {
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

        // for posts/pages/cpt: get post meta and update it
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
