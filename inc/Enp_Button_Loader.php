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
    *   Register and enqueue style sheet.
    *
    */
    public function enp_btn_register_scripts() {
        wp_register_style( 'enp-button-style', plugins_url( 'enp-button/front-end/css/enp-button-style.css' ));
        wp_enqueue_style( 'enp-button-style' );
    }
}

$init = new Enp_Button_Loader();

?>
