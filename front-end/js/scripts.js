/**
 * scripts.js
 *
 * General Enp Button scripts
 */

jQuery( document ).ready( function( $ ) {

    console.log('Enp Button scripts enqueued');

    $('.enp-btn').click(function(e) {
        e.preventDefault();
        // get which button was clicked
        var btn_id = $(this).attr('id');
        // if it's a post, pass the id/slug to an ajax request to update the post_meta for this post


        // if it's a comment, pass the id/slug to an ajax request to update the comment_meta for this comment


        // output the new html for the count
        console.log(btn_id);
    })

});
