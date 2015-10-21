/**
 * scripts.js
 *
 * General Enp Button scripts
 */

jQuery( document ).ready( function( $ ) {


    $('.enp-btn').click(function(e) {
        e.preventDefault();


        if( $(this).hasClass('enp-btn--error') || $(this).hasClass('enp-btn--disabled') || $(this).hasClass('enp-btn--click-wait')) {
            return; // hey! You're not supposed to click me! Wait a second if you've already clicked
        } else {
            // Delay them from clicking over and over without waiting
            $(this).addClass('enp-btn--click-wait');
        }



        // if user is not logged in & it's required, then disable the button
        var enp_btn_clickable = enp_button_params.enp_btn_clickable;

        if(enp_btn_clickable == 0) { // false
            // Button is disabled, return an error message with login links
            enp_pleaseLoginError(this)
            return false;
        }



        // if it's a post, pass the id/slug to an ajax request to update the post_meta for this post
        var id       = $(this).attr( 'data-pid' );
        var nonce    = $(this).attr( 'data-nonce' );
        var btn_slug = $(this).attr( 'data-btn-slug' );
        var btn_type = $(this).attr( 'data-btn-type' );
        var operator = $(this).attr( 'data-operator' );
        var url      = enp_button_params.ajax_url;
        var user_id  = enp_button_params.enp_btn_user_id;

        // assume that our front-end check is enough
        // and increase it by 1 for a super fast response time
        enp_changeCount(this, operator);

        // if it's a comment, pass the id/slug to an ajax request to update the comment_meta for this comment
        // Post to the server
        $.ajax({
            type: 'POST',
            url: url,
            data:  {
                    'action': 'enp_update_button_count',
                    'pid': id,
                    'slug': btn_slug,
                    'type': btn_type,
                    'operator': operator,
                    'user_id' : user_id,
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
                var btn_type = $(xml).find('type').text();
                var pid = $(xml).find('pid').text();
                var btn_slug = $(xml).find('slug').text();
                var btn = $('#'+btn_slug+'_'+btn_type+'_'+pid);
                var response = $(xml).find('response_data').text(); // will === 'success' or 'error'
                var btn_group = $('.enp-btns-'+btn_type+'-'+pid);
                var btn_wrap = $('#enp-btns-wrap-'+btn_type+'-'+pid);

                if(response === 'error') {
                    // there was an error updating the meta key on the server
                    // reset the count back one and let the user know what's up
                    var message = $(xml).find('message').text();
                    // show error message

                    // process the error
                    enp_processError(btn, btn_group, message);

                } else {
                    // switch out the operator data attribute
                    var new_operator = $(xml).find('new_operator').text();
                    btn.attr('data-operator', new_operator);

                    // success! add a btn class so we can style if we want to
                    // if the new operator === '-', then we just added one
                    if(new_operator === '-') {
                        btn.removeClass('enp-btn--decreased');
                        btn.addClass('enp-btn--increased');
                        btn.removeClass('enp-btn--user-has-not-clicked');
                        btn.addClass('enp-btn--user-clicked');
                    } else {
                        btn.removeClass('enp-btn--increased');
                        btn.addClass('enp-btn--decreased');
                        btn.addClass('enp-btn--user-has-not-clicked');
                        btn.removeClass('enp-btn--user-clicked');
                    }

                    var user_clicked_message = $(xml).find('user_clicked_message').text();

                    if($('.enp-user-clicked-hint', btn_wrap).length) {
                        $('.enp-user-clicked-hint', btn_wrap).replaceWith(user_clicked_message);
                    } else {
                        btn_group.after(user_clicked_message);
                    }

                    // remove clicked class so they can try again
                    btn.removeClass('enp-btn--click-wait');

                    // set localStorage
                    enp_setlocalStorage(btn_type, btn_slug, pid);
                }

            },
            error:function(json) {

                var error = $.parseJSON(json.responseText);
                // An error occurred when trying to post, alert an error message
                var message = error.message;
                var pid = error.pid;
                var btn_type = error.btn_type;
                var btn_slug = error.btn_slug;
                // create objects
                var btn = $('#'+btn_slug+'_'+btn_type+'_'+pid);
                var btn_group = $('.enp-btns-'+btn_type+'-'+pid);

                // process the error
                enp_processError(btn, btn_group, message);

                // remove clicked class so they can try again
                btn.removeClass('enp-btn--click-wait');
            }


        });


    });


    // Increase the count by 1
    function enp_changeCount(btn, operator) {
        var curr_count = enp_getBtnCount(btn);
        // if curr_count is 0, then remove the class that hides the 0
        if(curr_count === 0) {
            count.removeClass('enp-btn__count--zero');
        }

        // add one for the click
        if(operator === '+') {
            new_count = curr_count + 1;
        } else {
            new_count = curr_count - 1;
        }

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

    // roll back count on error
    function enp_rollBackCount(btn) {
        var new_count = enp_getBtnCount(btn);
        var roll_back_count = new_count - 1;
        $('.enp-btn__count', btn).text(roll_back_count);
    }

    function enp_errorMessage(obj, message) {
        // append the error message
        obj.append('<p class="enp-btn-error-message">'+message+'</p>');
    }

    function enp_processError(btn, btn_group, message) {
        // roll back count
        enp_rollBackCount(btn);

        // create error message
        enp_errorMessage(btn_group, message);

        // add disabled and error classes to the button
        btn.addClass('enp-btn--disabled enp-btn--error');
    }

    // client side message to please login
    function enp_pleaseLoginError(btn) {
        var enp_login_url = enp_button_params.enp_login_url;
        // if we already have an error message, destroy it
        if($('.enp-btn-error-message').length) {
            $('.enp-btn-error-message').remove();
        }

        var btns_group_wrap_id = $(btn).parent().parent().parent().attr('id');
        // append the button wrap id to the login url
        enp_login_url = enp_login_url+'%2F%23'+btns_group_wrap_id;
        // get the place to append the message
        var btn_group = $(btn).parent().parent();
        var message = 'You must be <a href="'+enp_login_url+'">logged in</a> to click this button. Please <a href="'+enp_login_url+'">log in</a> and try again.';
        enp_errorMessage(btn_group, message);
    }

    /*
    *   localStorage function
    */
    function enp_setlocalStorage(type, slug, id) {
        console.log(type+'_'+slug+'_'+id);
        // localStorage.getItem(type+'_'+slug+'_'+id, 1);
        var values = [enp_getLocalStorage(type)];
        values = [slug+'_'+id];
        localStorage.setItem('enp_button_'+type, values);
        console.log(enp_getLocalStorage(type));
    }

    function enp_getLocalStorage(type) {
        var values = localStorage.getItem('enp_button_'+type);

        return values;
    }


});
