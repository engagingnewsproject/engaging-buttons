<?

// $args is optionally a btn slug so you can do
// get_popular_posts('respect');
function get_popular_posts($args = false, $btn_type = false) {
    $args = enp_process_popular_args($args, $btn_type);
    return get_popular_buttons($args);
}


function get_popular_comments($args = array(), $btn_type = false) {
    $args = enp_process_popular_args($args, $btn_type, true);
    return get_popular_buttons($args);
}

function get_popular_buttons($args = array()) {

    $pop_posts = new Enp_Popular_Buttons($args);

    if(!empty($args['btn_slug'])) {
        return $pop_posts;
    } else {
        // return all popular posts
        return $pop_posts->get_all_popular_buttons($args);
    }
}


// for an easier convention of creating arguments so we can write our functions like:
// get_popular_posts('respect');
// get_popular_comments('recommend', 'post');
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

?>
