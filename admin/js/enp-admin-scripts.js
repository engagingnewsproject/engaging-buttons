/**
 * scripts.js
 *
 * Admin Enp Button scripts
 */

jQuery( document ).ready( function( $ ) {

    // count how many buttons we have
    var total_btns = $('.enp-btn-form').length;

    var addBtnHtml = '<a class="button button-primary enp-add-btn">Add Button</a> ';

    // add our "Add/remove buttons"
    $('.enp-btn-form').each(function(i, table_obj){


        var addBtn = '<tr class="btn-controls-row"><th></th><td class="btn-controls" data-button="'+i+'">';
        // last button, so put the "Add Button button in there"
        if(i === (total_btns - 1)) {
            addBtn = addBtn + addBtnHtml;
        }

        // remove button and finish off the tr row
        addBtn = addBtn + '<a class="button enp-remove-btn">Remove Button</a></td></tr>';

        // append to the table
        $(this).append(addBtn);
    });


    // Remove button
    $('.enp-remove-btn').click(function(e){
        e.preventDefault();
        var btn_number = $(this).parent().data('button');

        // Remove the form from the clicked button
        $('.enp-btn-form[data-button="'+btn_number+'"]').parent().fadeOut('300', function() { $(this).remove(); });

        // reindex numbers after this one if it's not the last one
        if(btn_number !== (total_btns - 1)) {
            $('.enp-btn-form').each(function() {
                form_number = $(this).attr('data-button');

                // if the clicked button is less than the current form loop index,
                // then we need to reduce all the following forms by one
                if(btn_number < form_number) {
                    var new_index;
                    new_index = form_number - 1;

                    // reindex all the attributes
                    enp_reIndexForm(form_number, new_index, this);
                }
            });
        } else {
            // if the remove button clicked was the last one,
            // we need to append a new add button button to the previous form
            prev_btn_controls = $('.enp-btn-form').find("[data-button='" + (btn_number - 1) + "']");
            $(prev_btn_controls).prepend(addBtnHtml);
        }

    });


    // Reindex option
    function enp_reIndexForm(prev_index, new_index, form) {
        var prev_index_str = prev_index.toString();
        var new_index_str = new_index.toString();

        // reduce the index by one on the table data attribute
        $(form).attr('data-button', new_index);
        // reduce the index by one on the btn control data attribute
        $('.btn-controls', form).attr('data-button', new_index);

        $('input', form).each(function() {
            var input_name = $(this).attr('name');
            // replace the old value with the new value
            var new_input_name = input_name.replace('['+prev_index_str+']', '['+new_index_str+']');
            // set the new name as the new value
            $(this).attr('name', new_input_name);
        });




    }

});
