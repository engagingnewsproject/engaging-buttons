<?
/*
*   admin-functions.php
*   General functions for use with the admin side
*
*/




/*
*
*   Gets all registered content types (posts, pages, custom posts)
*   Need to set the btn_type variable initially
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
