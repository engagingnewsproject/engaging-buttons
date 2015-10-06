<?
/*
* Enp_Button Class
* Creates and allows access to the Enp_Button object for use by
* WordPress admin and front-end
*
* since v 0.0.1
*/

class Enp_Button {
    public $btn_slug;
    public $btn_name;
    public $btn_type;
    public $btn_count;
    public $btn_lock;
    public $is_comment;

    public function __construct($slug = false, $is_comment = false) {
        // set our comment flag
        $this->is_comment = $is_comment;

        if($slug === false) {
            // return all buttons if they didn't specify which one
            // USAGE: $enp_btns = new Enp_Button();
            //        $enp_btns = $enp_btns->get_btns();
            $this->get_btns($slug);

        } else {
            // get the one they asked for
            // USAGE: $enp_btn = new Enp_Button('respect');
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
        $this->btn_slug  =  $this->set_btn_slug($enp_btn);
        $this->btn_name  =  $this->set_btn_name($enp_btn);
        $this->btn_type  =  $this->set_btn_type($enp_btn);
        $this->btn_count =  $this->set_btn_count($enp_btn);
        $this->btn_lock  =  $this->set_btn_lock();
    }

    /*
    *   set the button slug for the Enp_Button object
    */
    protected function set_btn_slug($enp_btn) {
        $slug = false;
        if(isset($enp_btn['btn_slug'])) {
            $slug = $enp_btn['btn_slug'];
        }
        //var_dump($slug);
        return $slug;
    }

    /*
    *
    *   set the button name for the Enp_Button object
    *
    */
    protected function set_btn_name($enp_btn) {
        $name = false;
        if(isset($enp_btn['btn_name'])) {
            $name = $enp_btn['btn_name'];
        }

        return $name;
    }


    /*
    *
    *   set the button type for the current Enp_Button object
    *   as an array of types - ie - ['btn_type'] => array('comments' => false, 'posts' => true)
    *
    */
    protected function set_btn_type($enp_btn) {
        $btn_type = false;

        if(isset($enp_btn['btn_type'])) {
            $btn_type = $enp_btn['btn_type'];
        }

        // TODO?
        // If a custom post type gets added, this will throw a PHP notice
        // that $btn_type['custom_post'] is not set
        // The way to set it would be loop through ALL active post types
        // with registeredContentTypes() and set any post types that aren't
        // set as false. It's an extra check for something that's not a big
        // deal though, so I'm not sure if it's worth the resources or not

        return $btn_type;
    }


    /*
    *
    *   Set the btn count value
    *
    */
    protected function set_btn_count($enp_btn) {
        if($this->is_comment === true) {
            global $comment;
            $comment_id = $comment->comment_ID;
            $enp_btn_count = get_comment_meta($comment_id, 'enp_button_'.$enp_btn['btn_slug'], true);
        } else {
            global $post;
            $post_id = $post->ID;

            if($post_id !== false) {
                // individual post button
                $enp_btn_count = get_post_meta($post_id,'enp_button_'.$enp_btn['btn_slug'], true);
            } else {
                // global post button
                $enp_btn_count = get_option('enp_button_'.$enp_btn['btn_slug']);
            }
        }
        // default 0 if nothing is found/posted yet
        $count = 0;

        if(!empty($enp_btn_count)) {
            $count = (int) $enp_btn_count;
        }

        return $count;
    }

    /*
    *
    *   Set the btn lock value. if count is 0 or greater, lock it
    *
    */
    protected function set_btn_lock() {
        $lock = false;
        // if btn_count is greater than 0, lock it
        if($this->btn_count > 0) {
            $lock = true;
        }

        return $lock;
    }


    /*
    *
    *   returns the button slug for the current Enp_Button object
    *   USAGE: $enp_btn = new Enp_Button('respect');
    *          $enp_btn->get_btn_slug; // 'respect'
    *
    */
    public function get_btn_slug() {
        return $this->btn_slug;
    }

    /*
    *
    *   returns the button name for the current Enp_Button object
    *   USAGE: $enp_btn = new Enp_Button('respect');
    *          $enp_btn->get_btn_name; // 'Respect'
    *
    */
    public function get_btn_name() {
        return $this->btn_name;
    }

    public function get_btn_types() {
        return $this->btn_type;
    }

    public function get_btn_count() {
        return $this->btn_count;
    }

    public function get_btn_lock() {
        return $this->btn_lock;
    }

    /*
    *
    *   get an individual button type
    *   returns array of types - ie - array('comments' => false, 'posts' => true)
    *
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
    *
    *   Return all button slugs from enp_button_slugs in an array
    *   Used by function get_btns()
    *
    */
    public function get_btn_slugs() {
        $enp_button_slugs = get_option('enp_button_slugs');

        return $enp_button_slugs;
    }

    /*
    *   Return all buttons as an array of individual objects
    *   (ie- $this->get_btns() = array([0]=> object(Enp_Button){[btn_slug]=>'', [btn_name]=>''},
    *            [1]=> object(Enp_Button){[btn_slug]=>'', [btn_name]=>''});
    *
    *   USAGE
    *   $enp_btns = new Enp_Button();
    *   $enp_btns = $enp_btns->get_btns();
    *   foreach($enp_btns as $enp_btn) {
    *       echo '<h1>'.$enp_btn->get_btn_name().'</h1>';
    *       // Outputs button name (ie- Recommend, Respect, Important, ...)
    *   }
    *
    */
    public function get_btns() {
        $enp_btns = $this->get_btn_slugs();

        foreach($enp_btns as $slug) {
            $enp_btns_obj[] = new Enp_Button($slug, $this->is_comment);
        }

        return $enp_btns_obj;
    }

}



?>
