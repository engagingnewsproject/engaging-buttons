<?
/*
* Enp_Button Class
* Creates and allows access to the enp_button object for use by
* WordPress admin and front-end
*
* since v 0.0.1
*/

class Enp_Button {
    public $btn_slug;
    public $btn_name;
    public $btn_type;
    // if has more than one click
    public $locked;


    public function __construct() {
        $this->btn_slug = $this->set_btn_slug();
        $this->btn_name = $this->set_btn_name();
        $this->btn_type = $this->set_btn_type();
        $this->locked = false;
    }


    private function set_btn_type() {
        // loop through all the registered post types (and comments)
        $registered_content_types = $this->registeredContentTypes();

        foreach($registered_content_types as $type) {
            // set it to false
            $btn_type[$type['slug']] =  false;
        }

        return $btn_type;
    }

    private function set_btn_slug() {
        $name = '';
        return $name;
    }

    private function set_btn_name() {
        $name = '';
        return $name;
    }


    public function get_btn_name() {
        return $this->btn_name;
    }

    public function get_btn_types() {

        return $this->btn_type;
    }

    public function get_btn_type($type = false) {
        $btn_type = $this->btn_type;
        $get_btn_type = false;

        if($type !== false && isset($btn_type[$type])) {
            $get_btn_type = $btn_type[$type];
        }

        return $get_btn_type;
    }



    /*
    * gets all registered content types (posts, pages, custom posts)
    * Needed to set the btn_type variable initially
    */
    public function registeredContentTypes() {
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


}

?>
