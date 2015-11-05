<?

// $args is optionally a btn slug so you can do
// get_popular_posts('respect');
function enp_get_popular_posts($args = false, $btn_type = false) {
    $args = enp_process_popular_args($args, $btn_type);
    return enp_get_popular_buttons($args);
}


function enp_get_popular_comments($args = array(), $btn_type = false) {
    $args = enp_process_popular_args($args, $btn_type, true);
    return enp_get_popular_buttons($args);
}

function enp_get_popular_buttons($args = array()) {

    $pop_posts = new Enp_Popular_Loop($args);

    if(!empty($args['btn_slug'])) {
        return $pop_posts;
    } else {
        // return all popular posts
        return $pop_posts->get_all_popular_buttons($args);
    }
}


// for an easier convention of creating arguments so we can write our functions like:
// enp_get_popular_posts('respect');
// enp_get_popular_comments('recommend', 'post');
function enp_process_popular_args($args = false, $btn_type = false, $comments = false) {
    // if there were no args, create an empty array
    // to pass to our popular button class
    if($args === false) {
        $args = array();
    } elseif(is_string($args)) {
        // we have a string! set it as the btn_slug
        $args = array('btn_slug' => $args);
    }
    //
    if($btn_type !== false) {
        $args['btn_type'] = $btn_type;
    }

    if($comments === true) {
        $args['comments'] = true;
    }

    return $args;
}

// append the most popular from each active button
function append_popular_posts($content) {
    if(is_single() || is_singular()) {
        // check the settings
        $enp_append_popular_slugs = get_option('enp_display_popular_slugs');
        if(!empty($enp_append_popular_slugs)) {
            foreach($enp_append_popular_slugs as $slug) {
                $posts = enp_get_popular_posts($slug);
                $content .= $posts->popular_loop();
            }
        }
    }


    return $content;
}
add_filter('the_content', 'append_popular_posts');
?>
