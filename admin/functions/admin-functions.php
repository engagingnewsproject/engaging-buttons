<?
/*
*   admin-functions.php
*   General functions for use with the admin side
*
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
*   Returns an array( [0] => array('slug'=>'comments', 'label_name'=>'Comments'),
*                     [1] => array('slug'=>'posts', 'label_name'=>'Posts'),
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
                        'slug'=>'comments',
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
?>
