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

function pop_comments_title($html, $comment_id, $btn_count){
    $html = '<h3>Comment '.$comment_id.' has '.$btn_count.' clicks!</h3>'.$html;
    return $html;
}
add_filter('enp_popular_comment_html', 'pop_comments_title', 10, 3);

/*function pop_comments_wrap($html, $this){
    $html = '<div style="color: red;">'.$html.'</div>';
    return $html;
}
add_filter('enp_popular_comments_loop_wrap', 'pop_comments_wrap', 10, 2);
add_filter('enp_popular_posts_loop_wrap', 'pop_comments_wrap', 10, 2);*/

function pop_posts_section_title($html, $pop_posts_obj){
    $html = '<h3>Most Respected Posts</h3>'.$html;
    return $html;
}
add_filter('enp_popular_posts_loop_before_html', 'pop_posts_section_title', 10, 2);

function append_popular_posts($content) {
    $posts = enp_get_popular_posts('respect');
    $content .= $posts->popular_loop();
    return $content;
}
add_filter('the_content', 'append_popular_posts');


?>
