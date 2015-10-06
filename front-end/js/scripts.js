/**
 * scripts.js
 *
 * General Enp Button scripts
 */

jQuery( document ).ready( function( $ ) {

    console.log('Enp Button scripts enqueued');

    $('.enp-btn').click(function(e) {
        e.preventDefault();
        if( $(this).hasClass('enp-btn__clicked')||
            $(this).hasClass('enp-btn__success')||
            $(this).hasClass('enp-btn__error')  ||
            $(this).hasClass('enp-btn__disabled')
        ) {
            return; // hey! You're not supposed to click me!
        } else {
            // $(this).addClass('enp-btn__disabled');
            $(this).addClass('enp-btn__clicked');
        }

        // assume that our front-end check is enough
        // and increase it by 1 for a super fast response time
        enp_increaseCount(this);

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

                var pid = $(xml).find('pid').text();
                var btn_slug = $(xml).find('slug').text();
                var btn = $('#'+btn_slug+'_'+pid);
                var response = $(xml).find('response_data').text(); // will === 'success' or 'error'

                if(response === 'error') {
                    // there was an error updating the meta key on the server
                    // reset the count back one and let the user know what's up
                    var message = $(xml).find('message').text();
                    $('.enp-btns-'+pid).append('<p class="enp-btn-error-message">'+message+'</p>');

                    // roll back the count
                    var new_count = enp_getBtnCount(btn);
                    var roll_back_count = new_count - 1;
                    $('.enp-btn__count', btn).text(roll_back_count);

                    // add disabled and error classes to the button
                    btn.addClass('enp-btn__disabled enp-btn__error');

                } else {
                    // success! add a btn class so we can style if we want to
                    btn.addClass('enp-btn__success');
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
    function enp_increaseCount(btn) {
        var curr_count = enp_getBtnCount(btn);
        // if curr_count is 0, then remove the class that hides the 0
        if(curr_count === 0) {
            count.removeClass('enp-btn__count--zero');
        }

        // add one for the click
        new_count = curr_count + 1;
        // replace the text with the new number
        count.text(new_count);
    }


    // get the current count of a button
    function enp_getBtnCount(btn) {
        count = $('.enp-btn__count', btn);
        var curr_count = count.text();
        // turn it into an integer
        curr_count = parseInt(curr_count);

        return curr_count;
    }


});
