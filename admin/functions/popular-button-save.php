<?
/*
*   popular-button-save.php
*   Functions for processing and saving the popular buttons arrays to wp_options table
*
*   since v 0.0.3
*/


// Main wrapper function for building all the popular button data
function enp_popular_button_save() {

    // get all our active slugs
    $enp_button_slugs = get_option('enp_button_slugs');

    // Quit the call if there are no settings
    if($enp_button_slugs === false) {
        return false;
    }

    // loop through each active button slug
    foreach($enp_button_slugs as $btn_slug) {
        // set flag to see if we should process comments
        $process_comments = false;

        // generate the meta_key we want
        $meta_key = 'enp_button_'.$btn_slug;

        // get our active button types
        $btn_info = get_option($meta_key);

        // loop through each button type
        foreach($btn_info['btn_type'] as $key=>$value) {

            // check if the button type is active
            if($value === '1') {

                $popular_args = array(
                    'meta_key'      => $meta_key,
                    'orderby'       => 'meta_value_num',
                    'order'         => 'DESC'
                );

                // if it's a comment, we need to process it differently
                if($key === 'comment') {
                    $process_comments = true;
                } else {
                    // reset the array with the new post type
                    $popular_post_args = array('post_type' => $key);
                    $popular_post_args = array_merge($popular_args, $popular_post_args);
                    enp_popular_posts_save($btn_slug, $popular_post_args);
                }

            } // end if

        } // end foreach btn_type

        if($process_comments === true) {
            // strip out popular args post type
            enp_popular_comments_save($btn_slug, $btn_info['btn_type'], $popular_args);
        }
    } // end foreach btn_slug

}

//
function enp_popular_comments_save($btn_slug, $post_types, $args) {

    // all comments by btn slug (combines pages, posts, etc. anywhere the button is shown)
    $comment_args = array(
            'fields' => 'ids',
            'status' => 'approve',
        );

    $args = array_merge($comment_args, $args);

    $comments_query = new WP_Comment_Query;
    $comments = $comments_query->query( $args );


    $popular_comments = enp_build_popular_array($btn_slug, $comments, 'comment');

    update_option('enp_button_popular_'.$btn_slug.'_comments', $popular_comments);


    // Loop through all the passed post_types and
    // save all comments by post type
    // ex: enp_button_popular_respect_page_comments
    foreach($post_types as $key=>$value) {
        // check if the button type is active
        if($value === '1' && $key !== 'comment') {
            // build the arguments
            $post_type_args = array('post_type'=>$key);
            $post_type_args = array_merge($args, $post_type_args);
            // generate the query
            $comments_query = new WP_Comment_Query;
            $comments = $comments_query->query( $post_type_args );
            // build the array of popular ids and counts
            $popular_comments = enp_build_popular_array($btn_slug, $comments, 'comment');
            // save the array
            update_option('enp_button_popular_'.$btn_slug.'_'.$key.'_comments', $popular_comments);

        }
    }
}


/*
*   Loop through the returned comment_ids, get the count, and return
*   an array of arrays of ids + button count in order of button count from greatest to least
*/
function enp_popular_posts_save($btn_slug, $args) {

    // TODO: Override this via Admin option
    $args['posts_per_page']= 20; // limit to 20.

    $pop_posts = get_posts( $args );
    $post_type = $args['post_type'];

    $popular_posts = enp_build_popular_array($btn_slug, $pop_posts, $post_type);

    update_option('enp_button_popular_'.$btn_slug.'_'.$post_type, $popular_posts);

    // Restore original Post Data
    wp_reset_postdata();
}

/*
*   Loop through the returned ids, get the count, and return
*   an array of arrays of ids + button count in order of button count from greatest to least
*/
function enp_build_popular_array($btn_slug, $pop_posts, $post_type) {
    $popular_array = array();

    foreach ($pop_posts as $pop) {
        if($post_type === 'comment') {
            $id = $pop;
            $label = 'comment';
        } else {
            $id = $pop->ID;
            $label = 'post';
        }

        $btn_count_args = array('post_id' => $id,'btn_slug' => $btn_slug,'btn_type' => $post_type);
        $btn_count = get_single_btn_count($btn_count_args);

        $popular_array[] = array(
                                $label.'_id' => $id,
                                'btn_count' => $btn_count
                            );
    }

    return $popular_array;
}

?>
