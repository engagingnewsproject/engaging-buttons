/**
 * scripts.js
 *
 * General Enp Button scripts
 */

jQuery( document ).ready( function( $ ) {

    console.log('Enp Button scripts enqueued');

    $('.enp-btn').click(function(e) {
        e.preventDefault();

        // assume that our front-end check is enough
        // and increase it by 1 for a super fast response time
        increaseCount(this);


        // if it's a post, pass the id/slug to an ajax request to update the post_meta for this post
        var id       = $(this).attr( 'data-pid' );
        var nonce    = $(this).attr( 'data-nonce' );
        var btn_slug = $(this).attr( 'data-btn-slug' );
        var url      = enp_button_params.ajax_url;

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
                // don't do anything!
                // If we update with the xml count, it could be wrong if someone
                // on a different connection has clicked it. Then, it would go up by
                // multiple numbers, instead of just one, and the person seeing that
                // happen would think that their click registered lots of times instead
                // of correctly counting just once

                // here's how to get the new count from the returned xml doc though
                // var count = $(xml).find('count').text();

                var response = $(xml).find('response_data').text(); // will === 'success' or 'error'
                if(response === 'error') {
                    // there was an error updating the meta key on the server
                    // reset the count back one and let the user know what's up
                    var pid = $(xml).find('pid').text();
                    var message = $(xml).find('message').text();
                    $('.enp-btns-'+pid).append('<p class="enp-btn-error-message">'+message+'</p>');
                }

            },
            error:function(xml) {
                // An error occurred when trying to post, alert an error message
                var message = $(xml).find('message').text();
                console.log(message);
            }


        });


    });


    // Increase the count by 1
    function increaseCount(btn) {

        count = $('.enp-btn__count', btn);
        var curr_count = count.text();
        // turn it into an integer
        curr_count = parseInt(curr_count);
        // if curr_count is 0, then remove the class that hides the 0
        if(curr_count === 0) {
            count.removeClass('enp-btn__count--zero');
        }

        // add one for the click
        new_count = curr_count + 1;
        // replace the text with the new number
        count.text(new_count);
    }


});
