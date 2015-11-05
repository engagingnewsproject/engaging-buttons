<?
/*
*   Filters for interacting with the Enp_Popular_Loop class and displaying
*   our popular posts. You can copy/paste this file into your theme and run
*   the enp_remove_popular_posts_filters() function at the top to override all
*   HTML.
*   since v.0.0.5
*/

// remove all the filters on this file
function enp_remove_popular_posts_filters() {

}

// Basic formatting for any popular posts loop
function enp_custom_popular_posts_loop() {

}


function pop_comments_title($html, $comment_id, $btn_count){
    $html = '<h3>Comment '.$comment_id.' has '.$btn_count.' clicks!</h3>'.$html;
    return $html;
}
add_filter('enp_popular_comment_html', 'pop_comments_title', 10, 3);
/*
function pop_comments_wrap($html, $this){
    $html = '<div style="color: red;">'.$html.'</div>';
    return $html;
}
add_filter('enp_popular_comments_loop_wrap', 'pop_comments_wrap', 10, 2);
add_filter('enp_popular_posts_loop_wrap', 'pop_comments_wrap', 10, 2);*/

function pop_posts_section_title($html, $pop_posts_obj){
    $html = '<h3>Most '.$pop_posts_obj->btn_past_tense_name.' Posts</h3>'.$html;
    return $html;
}
add_filter('enp_popular_posts_loop_before_html', 'pop_posts_section_title', 10, 2);

?>
