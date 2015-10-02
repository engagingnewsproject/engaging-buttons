<?
/*
*   ENP_Button_Loader Class
*   For loading assets needed to run the Engaging Button
*
*/

class Enp_Button_Loader {

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enp_btn_register_scripts'));
    }

    /*
    *
    *   Register and enqueue style sheet & scripts.
    *
    */
    public function enp_btn_register_scripts() {
        wp_register_style( 'enp-button-style', plugins_url( 'enp-button/front-end/css/enp-button-style.css' ));
        wp_enqueue_style( 'enp-button-style' );

        wp_register_script( 'enp-button-scripts', plugins_url( 'enp-button/front-end/js/scripts.js' ));
        wp_enqueue_script( 'enp-button-scripts' );
    }
}

// fire up our styles and scripts
$init = new Enp_Button_Loader();

?>
