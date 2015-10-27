<?php

//  Create link to the menu page.
add_action('admin_menu', 'enp_create_menu');
function enp_create_menu() {
    //create new top-level menu
    add_options_page('Engaging Buttons', 'Engaging Buttons', 'manage_options', 'enp_button_page', 'enp_button_page', 'dashicons-megaphone', 100);
}


// Add filters for any fields that need extra work before saving

// We can't register dynamically named variables, so we're going to
// create everything under enp_buttons and parse from there
add_filter( 'pre_update_option_enp_buttons', 'set_enp_buttons_values', 10, 2 );


// Create settings fields.
add_action( 'admin_init', 'enp_button_data' );
function enp_button_data() {

    // button type
    register_setting( 'enp_button_settings', 'enp_buttons' );

    // global enp_button settings
    register_setting( 'enp_button_settings', 'enp_button_must_be_logged_in' );
    register_setting( 'enp_button_settings', 'enp_button_allow_data_tracking' );
    register_setting( 'enp_button_settings', 'enp_button_promote_enp' );
}

// enqueue our scripts
function enp_enqueue_admin_scripts() {
    wp_register_style('enp-admin-styles', plugins_url( 'enp-button/admin/css/enp-admin-styles.css'));
    wp_enqueue_style( 'enp-admin-styles');

    wp_register_script('enp-admin-scripts', plugins_url( 'enp-button/admin/js/enp-admin-scripts.js'), array( 'jquery' ), false, true );
    wp_enqueue_script( 'enp-admin-scripts');
}
add_action( 'admin_enqueue_scripts', 'enp_enqueue_admin_scripts' );


/**
* Step 3: Create the markup for the options page
*/
function enp_button_page() { ?>
<svg style="display: none;">
    <symbol id="icon-cancel" viewBox="0 0 1024 1024">
        <path d="M512 928c-229.76 0-416-186.24-416-416s186.24-416 416-416 416 186.24 416 416-186.24 416-416 416zM512 192c-176.704 0-320 143.296-320 320s143.296 320 320 320c176.736 0 320-143.296 320-320s-143.264-320-320-320zM657.184 625.12l-32.448 32.448c-5.952 5.952-15.616 5.952-21.6 0l-91.84-91.84-91.84 91.84c-5.984 5.952-15.616 5.952-21.6 0l-32.448-32.448c-5.952-5.952-5.952-15.616 0-21.6l91.872-91.808-91.872-91.84c-5.952-5.984-5.952-15.648 0-21.632l32.448-32.416c5.984-5.984 15.616-5.984 21.6 0l91.84 91.84 91.84-91.84c5.984-5.984 15.648-5.984 21.6 0l32.448 32.416c5.952 5.984 5.952 15.648 0 21.632l-91.872 91.84 91.872 91.808c5.952 5.984 5.952 15.68 0 21.6z"></path>
    </symbol>
</svg>

<div class="wrap enp-respect-button-options">

    <form method="post" action="options.php">
        <h1>Engaging Buttons Settings</h1>

        <?php if(isset( $_GET['settings-updated'])) { ?>
        <div class="updated">
            <p>Settings updated successfully</p>
        </div>
        <?php }


        // return all buttons and build off of current options
        $enp_buttons = get_option('enp_buttons');

        // general settings
        $btn_must_be_logged_in = get_option('enp_button_must_be_logged_in');
        $btn_allow_data_tracking = get_option('enp_button_allow_data_tracking');
        $btn_promote_enp = get_option('enp_button_promote_enp');

        // build the buttons form
        $registered_content_types = registeredContentTypes();
        buttonCreateForm($enp_buttons, $registered_content_types);


        // add the general settings
        ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        Engaging Button Plugin Setting
                    </th>
                    <td>
                        <fieldset>
                            <label for="enp_button_must_be_logged-in">
                                <input type="checkbox" name="enp_button_must_be_logged_in" <?php checked(true, $btn_must_be_logged_in);?> value="1" /> Users must be logged in to click the button(s)
                            </label>
                            <label for="enp_button_allow_data_tracking">
                                <input type="checkbox" name="enp_button_allow_data_tracking" aria-describedby="enp-button-allow-data-tracking-description" <?php checked(true, $btn_allow_data_tracking);?> value="1" /> Allow data collection
                                <p id="enp-button-allow-data-tracking-description" class="description">This allows the <a href="http://engagingnewsproject.org">Engaging News Project</a>, an academic nonprofit at the University of Texas at Austin, to record data on the buttons so they can continue to provide free, open-source plugins and research. No personal information is recorded. Learn more about what data is tracked and how it is used here.</p>
                            </label>
                            <label for="enp_button_promote_enp">
                                <input type="checkbox" name="enp_button_promote_enp" aria-describedby="enp-button-promote-enp-description" <?php checked(true, $btn_promote_enp);?> value="1" /> Display "Respect Button Powered by the Engaging News Project"
                                <p id="enp-button-promote-enp-description" class="description">Small text displayed beneath the WordPress comments section.</p>
                            </label>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php settings_fields( 'enp_button_settings' ); ?>
        <?php do_settings_sections( 'enp_button_settings' ); ?>

        <table class="form-table">
            <tbody>
                <tr>
                    <th></th>
                    <td><?php submit_button(); ?></td>
                </tr>
            </tbody>
        </table>
    </form>

    <p>The Respect Button plugin is made by the <a href="http://engagingnewsproject.org">Engaging News Project</a>, a nonprofit at the University of Texas at Austin that researches COMMERCIALLY-VIABLE and DEMOCRATICALLY-BENEFICIAL ways to improve ONLINE NEWS</p>
</div>
<?php
}
