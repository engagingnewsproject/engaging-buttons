/**
 * scripts.js
 *
 * Admin Enp Button scripts
 */

jQuery( document ).ready( function( $ ) {

    // count how many buttons we have
    function enp_totalBtns() {
        var total_btns = $('.enp-btn-form').length;
        return total_btns;
    }


    var addBtnHtml = '<div class="enp-add-btn-wrap"><a class="enp-add-btn">Add Button <svg class="icon-add"><use xlink:href="#icon-add"></use></svg></a></div>';
    var removeBtnHtml = '<a class="enp-remove-btn">Remove Button <svg class="icon-remove"><use xlink:href="#icon-remove"></use></svg></a>';
    // add our "Add/remove buttons"

    // last button, so put the "Add Button button in there"
    $(addBtnHtml).insertAfter('.enp-btn-table-wrap:last');

    $('.enp-btn-form').each(function(i, table_obj){
        var total_btns = enp_totalBtns();

        var add_enp_btns = '<tr class="btn-controls-row"><th></th><td class="btn-controls" data-button="'+i+'">';

        // only add the remove button if there's more than one button
        if(1 < total_btns) {
            add_enp_btns = add_enp_btns + removeBtnHtml;
        }

        // remove button and finish off the tr row
        add_enp_btns = add_enp_btns + '</td></tr>';

        // append to the table
        $(this).append(add_enp_btns);
    });


    // Add button
    $('.enp-add-btn').click(function(e){
        e.preventDefault();

        var btn_number = $(this).parent().data('button');

        // clone the last button and increase its numbers by one

        var new_button = $('.enp-btn-form[data-button="'+btn_number+'"]').parent().clone();

        $('.enp-btn-form[data-button="'+btn_number+'"]').parent().after(new_button);

    });


    // Remove button
    $('.enp-remove-btn').click(function(e){
        e.preventDefault();

        var total_btns = enp_totalBtns();

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
