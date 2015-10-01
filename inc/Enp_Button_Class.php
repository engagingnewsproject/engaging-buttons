<?
/*
* Enp_Button Class
* Creates and allows access to the Enp_Button object for use by
* WordPress admin and front-end
*
* since v 0.0.1
*/

class Enp_Button {
    // TODO: Move save code from respect-button-settings.php into this class
    public $btn_slug;
    public $btn_name;
    public $btn_type;
    // if has more than one click
    public $locked;


    public function __construct($slug = false) {

        if(empty($slug)) {
            // return all buttons if they didn't specify which one
            $this->get_btns();
        } else {
            // get the one they asked for
            $enp_btn = $this->set_btn($slug);
        }



    }

    public function get_btn($slug) {
        $enp_btn_values = get_option('enp_buttons_'.$slug);
        return $this->set_btn($enp_btn_values);
    }


    protected function set_btn($slug) {
        // get the data from wp_options
        $enp_btn = get_option('enp_button_'.$slug);

        // set all the attributes
        $this->btn_slug = $this->set_btn_slug($enp_btn);
        $this->btn_name = $this->set_btn_name($enp_btn);
        $this->btn_type = 'button type!';
        $this->locked   = false;

    }

    /*
    *   set the button slug for the Enp_Button object
    */
    private function set_btn_slug($enp_btn) {
        $slug = false;
        if(isset($enp_btn['btn_slug'])) {
            $slug = $enp_btn['btn_slug'];
        }
        //var_dump($slug);
        return $slug;
    }

    /*
    *   set the button name for the Enp_Button object
    */
    private function set_btn_name($enp_btn) {
        $name = false;
        if(isset($enp_btn['btn_name'])) {
            $name = $enp_btn['btn_name'];
        }

        return $name;
    }

    private function set_btn_type($enp_btn) {
        // loop through all the registered post types (and comments)
        $registered_content_types = $this->registeredContentTypes();

        foreach($registered_content_types as $type) {
            // set it to false
            $btn_type[$type['slug']] =  false;
        }

        return $btn_type;
    }


    public function get_btn_slug() {
        return $this->btn_slug;
    }


    public function get_btn_name() {
        return $this->btn_name;
    }

    public function get_btn_types() {
        return $this->btn_type;
    }

    /*
    * get an individual button type
    */
    public function get_btn_type($type = false) {
        $btn_type = $this->btn_type;
        $get_btn_type = false;

        if($type !== false && isset($btn_type[$type])) {
            $get_btn_type = $btn_type[$type];
        }

        return $get_btn_type;
    }


    /*
    * Return all button slugs from enp_button_slugs
    *
    */
    public function get_btn_slugs() {
        $enp_button_slugs = get_option('enp_button_slugs');

        return $enp_button_slugs;
    }

    /*
    / Return all buttons as an array of individual objects
    / (ie- $this->get_btns() = array([0]=> object(Enp_Button){[btn_slug]=>'', [btn_name]=>''},
    /            [1]=> object(Enp_Button){[btn_slug]=>'', [btn_name]=>''});
    /
    */
    public function get_btns() {
        $enp_btns = $this->get_btn_slugs();
        foreach($enp_btns as $slug) {

            $enp_btns_obj[] = new Enp_Button($slug);
        }

        return $enp_btns_obj;
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
