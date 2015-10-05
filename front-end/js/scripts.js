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

        var link = this;
        var id   = $( link ).attr( 'data-id' );
        var nonce = $( link ).attr( 'data-nonce' );
        var btn_slug = $( link ).attr( 'id' );
        var url = enp_button_params.ajax_url;

        // if it's a comment, pass the id/slug to an ajax request to update the comment_meta for this comment
        // Post to the server
        $.ajax({
            type: 'POST',
            url: url,
            data:  {
                    'action': 'enp_update_button_count',
                    'pid': id,
                    'slug': btn_slug,
                    'nonce': nonce
                },
            dataType: 'xml',
            success:function(xml) {
                var count = $(xml).find('count').text();
                // message = $(xml).find('message').text();

                if(count > 0) {
                    console.log('count is greater than 0: ' +count);
                } else {
                    console.log('count is less than 0: ' +count);
                }

            },
            error: function(xml) {
                // An error occurred, alert an error message
                console.log( 'Error: '+$(xml).find('message').text() );
            }


        });

        // output the new html for the count
        console.log(btn_id);
    })

});
