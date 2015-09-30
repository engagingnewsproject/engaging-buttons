<?php
// Admin side functions for Respect Button Plugin

function enp_registeredContentTypes() {
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



class enpRespectButton {

    var $btn_types;
    var $btn_type_options;
    var $registered_content_types;

    public function __construct() {
        // set our array of button string options
        $this->btn_type_options = ['respect', 'recommend', 'important'];

        // return our values for enp_button_type
        $this->getBtnTypeValues($this->btn_type_options);

        // get active content types
        $this->registered_content_types = enp_registeredContentTypes();
        $registered_content_types = $this->registered_content_types;

        // return our values for enp_button_content_type
        $this->getBtnContentTypeValues($registered_content_types);

        // build our checklists
        $this->buildContentTypeChecklist();

    }


    // get all data stored in get_option('enp_button_type') and make it
    // easily accessible to us
    // USAGE: $enp_respect = new enpRespectButton;
    //        $btn_types = $enp_respect->btn_types;
    //        $btn_types["respect"]; // returns bool(true) or bool(false)
    public function getBtnTypeValues($btn_type_options) {
        // get our data
        $this->btn_types = get_option('enp_button_type');
        // rewrite our variables to make them easier to understand
        $btn_types = $this->btn_types;

        // check if set, and if not, set it as false
        foreach($btn_type_options as $option) {
            if(!empty($btn_types[$option])) {
                // we have a value! Don't worry about it
            } else {
                $btn_types[$option] = false;
            }
        }

        // we have to preface with $this
        // to set the value as the array we just created
        $this->btn_types = $btn_types;
    }

    // get all data stored in get_option('enp_button_content_type') and make it
    // easily accessible to us
    // USAGE: $enp_respect = new enpRespectButton;
    //        $btn_content_types = $enp_respect->btn_content_types;
    //        $btn_content_types["post"]; // returns bool(true) or bool(false)
    public function getBtnContentTypeValues($registered_content_types) {
        // get our data
        $this->btn_content_types = get_option('enp_button_content_type');
        $btn_content_types = $this->btn_content_types;

        // get all active post types in an array


        // we want to set each value so nothing is left empty or unset
        foreach($registered_content_types as $a_type) {

            if(!empty($btn_content_types[$a_type['slug']])) {
                // we have a value! don't do anything
            } else {
                // no value, so set it as false
                $btn_content_types[$a_type['slug']] = false;
            }
        }

        $this->btn_content_types = $btn_content_types;
    }


    // returns html for all registered content types in respect button settings page
    function buildContentTypeChecklist() {
        $registered_content_types = $this->registered_content_types;
        $content_type_values = $this->btn_content_types;
        $checklist_html = '';

        foreach($registered_content_types as $content_type) {
            $checklist_html .= '<label>
                                    <input type="checkbox" name="enp_button_content_type['.$content_type['slug'].']" value="true" '.checked(true, $content_type_values[$content_type['slug']], false).' aria-describedby="enp-button-content-type-description"/> '.$content_type['label_name'].'
                                </label>
                                <br/>';
        }

        return $checklist_html;
    }

    /* example

    public function display() {
        $btn_type_options = $this->btn_type_options;
        foreach($btn_type_options as $option) {

            var_dump($option.' is '.$this->$option);
        }
    }*/


}

?>
