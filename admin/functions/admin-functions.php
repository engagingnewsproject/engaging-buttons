<?
/*
*   admin-functions.php
*   General functions for use with the admin side
*   Functions for saving the settings
*   Functions for displaying the form
*/



/*
*
*   Process values for updating enp_button_slugs
*
*/
function update_enp_button_slugs($values) {
    $enp_button_slugs = array();

    // loop through the posted array and add JUST the button slugs
    foreach($values as $value) {
        $enp_button_slugs[] = $value['btn_slug'];
    }

    // TODO: Check to see if a slug has a enp_button_$slug entry.
    //       We may not want to overwrite this entirely otherwise

    // TODO: Set Old Buttons Slugs (enp_button_$slug) to active = 0
    // if it's no longer in use

    update_option('enp_button_slugs', $enp_button_slugs);
}



/*
*
*   Save enp_button_$slug to wp_options for use by Enp_Button class to create button object
*
*/
function update_enp_button_slug_entry($values) {

        // loop through each submitted button
        foreach($values as $value) {

            // dynamically named enp_button_$slug so we can access just one
            // field on the front end or for objects, as needed
            ${'enp_button_'.$value['btn_slug']} = array(
                                                        'btn_slug' => $value['btn_slug'],
                                                        'btn_name' => $value['btn_name'],
                                                        'btn_type' => $value['btn_type'],
                                                        // 'locked' => false, // TODO: Set to true if count is more than 0 (has clicks)
                                                  );


            // update the database entry
            update_option('enp_button_'.$value['btn_slug'], ${'enp_button_'.$value['btn_slug']});

        }
}


/*
*
*   If a button type wasn't selected, it doesn't get added to the array as false
*   This function sets all unset values as false so we squash php notices
*
*/
function set_unset_btn_type_values($values) {
    // get all the registered content types as an array
    $registered_content_types = registeredContentTypes();
    $i = 0;
    foreach($values as $value) {
        // check each content type to see if it's set
        // if it's not, then set it to false
        foreach($registered_content_types as $type) {
            // set it to false if it wasn't set
            if(!isset($values[$i]['btn_type'][$type['slug']])) {
                // update the original $values too
                $values[$i]['btn_type'][$type['slug']] = false;
            }
        }
        $i++;
    }

    return $values;
}


/*
*
*   Add name value for enp_button_$slug
*
*/

function add_enp_button_names($values) {
    $i = 0;
    // loop through values and add the btn_name in
    foreach($values as $value) {
        $values[$i]['btn_name'] = ucfirst($values[$i]['btn_slug']);
        $i++;
    }

    return $values;
}




/*
*
*   Gets all registered content types (posts, pages, custom posts)
*   Need to set the btn_type variable before saving
*   Returns an array( [0] => array('slug'=>'comment', 'label_name'=>'Comments'),
*                     [1] => array('slug'=>'post', 'label_name'=>'Posts'),
*                     [2] => array(...)
*                   )
*
*/
function registeredContentTypes() {
    // only get public post types
    $post_type_args = array('public' => 'true');
    // this will get us all post type objects
    $post_types = get_post_types( $post_type_args, 'objects' );
    // set our empty array value
    $registered_content_types = array();

    // add in the comments array
    $registered_content_types[] = array(
                        'slug'=>'comment',
                        'label_name'=>'Comments'
                    );

    // Loop through all active post types
    foreach ( $post_types as $post_type ) {

        // we don't need the attachment (media) post type
        if($post_type->name != 'attachment') {

            // build our array to return the slug and singular name of each active post type
            $registered_content_types[] = array(
                                    "slug" => $post_type->name,
                                    "label_name" => $post_type->labels->name,
                                );
        }
    }

    return $registered_content_types;

}

/*
*
*   Create Settings Button HTML
*
*/
function buttonCreateForm($enp_buttons, $registered_content_types) {
    $formHTML = '';

    if($enp_buttons === false) {
        $formHTML .= buttonCreateFormHTML($enp_buttons, $registered_content_types);
    } else {
        $i = 0;
        foreach($enp_buttons as $enp_button) {
            $args['btn_slug'] = $enp_button['btn_slug'];
            $enp_btn_obj = new Enp_Button($args);
            $formHTML .= buttonCreateFormHTML($enp_buttons, $registered_content_types, $i, $enp_btn_obj);
            $i++;
        }

        // if we want to add buttons later, we'd add more after this loop
        // $formHTML .= buttonCreateFormHTML($enp_buttons, $registered_content_types, $i, $enp_btn_obj);

    }

    echo $formHTML;

}


function buttonCreateFormHTML($enp_buttons, $registered_content_types, $i = 0, $enp_btn_obj = false ) {
    $formHTML = '<table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="enp-button-type">Button</label>
                        </th>
                        <td>
                            <fieldset>'
                                .buttonCreateSlug($enp_buttons, $i, $enp_btn_obj).
                            '</fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="enp-button-content-type">Where to Use this Button</label>
                        </th>
                        <td>
                            <fieldset>'.
                                buttonCreateBtnType($enp_buttons, $i, $registered_content_types)
                            .'</fieldset>
                            <p id="enp-button-content-type-description" class="description">Which kinds of content do you want the button(s) to display for?</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <hr>';

    return $formHTML;
}



function buttonCreateSlug($enp_buttons, $i = 0, $enp_btn_obj) {
    $buttonSlugHTML = '';

    $buttonSlugHTML .= buttonCreateSlugHTML($enp_buttons, $i, $enp_btn_obj);

    return $buttonSlugHTML;
}

function buttonCreateSlugHTML($enp_buttons, $i = 0, $enp_btn_obj) {
    // if there's no object or there are
    if($enp_btn_obj === false || $enp_btn_obj->btn_lock === false) {
        $buttonSlugHTML ='<label>
                            <input type="radio" name="enp_buttons['.$i.'][btn_slug]" aria-describedby="enp-button-slug-description" value="respect" '.checked('respect', $enp_buttons[$i]["btn_slug"], false).' /> Respect
                        </label>
                        <br/>
                        <label>
                            <input type="radio" name="enp_buttons['.$i.'][btn_slug]" aria-describedby="enp-button-slug-description" value="recommend" '.checked('recommend', $enp_buttons[$i]["btn_slug"], false).' /> Recommend
                        </label>
                        <br/>
                        <label>
                            <input type="radio" name="enp_buttons['.$i.'][btn_slug]" aria-describedby="enp-button-slug-description" value="important" '.checked('important', $enp_buttons[$i]["btn_slug"], false).' /> Important
                        </label>
                        <p id="enp-button-slug-description"class="description">Which button do you want to use on your site?</p>
                        <p class="description">Have an idea for other button text options? Let us know! ____@engagingnewsproject.org';
    } else {
        // the button object exists and it's locked, so we can't let people change it
        // without resetting everything to 0
        $buttonSlugHTML =  '<label>
                                <input type="radio" name="enp_buttons['.$i.']['.$enp_btn_obj->get_btn_slug().']" aria-describedby="enp-button-slug-description" value="respect" '.checked('respect', $enp_buttons[$i]["btn_slug"], false).' /> '.$enp_btn_obj->get_btn_name()
                          .'</label>
                          <p class="description">This button is locked because people have already clicked on it.</p>
                          <p class="description">You have to delete it and create a new button to change the button name.</p>';
    }


    return $buttonSlugHTML;
}


function buttonCreateBtnType($enp_buttons, $i, $registered_content_types) {
    $checklist_html = '';

    foreach($registered_content_types as $content_type) {
        $checklist_html .= buttonCreateBtnTypeHTML($enp_buttons, $i, $content_type);
    }

    return $checklist_html;
}

function buttonCreateBtnTypeHTML($enp_buttons, $i, $content_type) {
    $checklist_html ='';

    $name = 'enp_buttons['.$i.'][btn_type]['.$content_type['slug'].']';

    // set our default value to false
    $checked_val = false;
    // this is absurdly convoluted, but it works... Improvements are welcome
    if(isset($enp_buttons[$i]['btn_type'][$content_type['slug']])) {
        // set the value
        $checked_val = $enp_buttons[$i]['btn_type'][$content_type['slug']];
    }

    $checklist_html .= '<label>
                            <input type="checkbox" name="'.$name.'" value="1" '.checked(true, $checked_val, false).' aria-describedby="enp-button-content-type-description"/> '.$content_type['label_name'].'
                        </label>
                        <br/>';

    return $checklist_html;
}


?>
