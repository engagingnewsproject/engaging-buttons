<?php

//  Create link to the menu page.
add_action('admin_menu', 'enp_create_menu');
function enp_create_menu() {
    //create new top-level menu
    add_menu_page('Respect Button', 'Respect Button', 'manage_options', 'enp_respect_button', 'enp_respect_button_page', 'dashicons-megaphone', 100);
}


// Add filters for any fields that need extra work before saving
add_filter( 'pre_update_option_enp_button_type', 'set_enp_button_type_values', 10, 2 );
function set_enp_button_type_values($value) {
    $enp_respect = new enpRespectButton;
    $btn_type_options = $enp_respect->btn_type_options;

    // we want to set each value so nothing is left empty or unset
    foreach($btn_type_options as $option) {

        if(!empty($value[$option]) && $value[$option] === 'true') {
            // have to return it instaed of save it right there
            $value[$option] = true;

        } else {
            $value[$option] = false;
        }
    }

    return $value;
}


add_filter( 'pre_update_option_enp_button_content_type', 'set_enp_button_content_type_values', 10, 2 );
function set_enp_button_content_type_values($value) {
    // get all active post types in an array
    $registered_content_types = enp_registeredContentTypes();

    // we want to set each value so nothing is left empty or unset
    foreach($registered_content_types as $option) {

        if(!empty($value[$option['slug']]) && $value[$option['slug']] === 'true') {
            // have to return it instaed of save it right there
            $value[$option['slug']] = true;

        } else {
            $value[$option['slug']] = false;
        }
    }

    return $value;
}




// Create settings fields.
add_action( 'admin_init', 'enp_respect_button_data' );
function enp_respect_button_data() {

    // button type
    register_setting( 'enp_respect_button_settings', 'enp_button_type' );
    // content type
    register_setting( 'enp_respect_button_settings', 'enp_button_content_type' );
    // other settings
    register_setting( 'enp_respect_button_settings', 'enp_button_must_be_logged_in' );
    register_setting( 'enp_respect_button_settings', 'enp_button_allow_data_tracking' );
    register_setting( 'enp_respect_button_settings', 'enp_button_promote_enp' );
}

/**
* Step 3: Create the markup for the options page
*/
function enp_respect_button_page() { ?>

<div class="wrap enp-respect-button-options">

    <form method="post" action="options.php">
        <h1>Respect Button Settings</h1>

        <?php if(isset( $_GET['settings-updated'])) { ?>
        <div class="updated">
            <p>Settings updated successfully</p>
        </div>
        <?php }


        $enp_btn = new Enp_Button;
        var_dump($enp_btn);

        $enp_respect = new enpRespectButton;
        // var_dump($enp_respect);
        // which button(s) to use? respect? recommend? important?
        $btn_types = $enp_respect->btn_types;
        // which content types to attach buttons to? comments? posts? pages?
        $btn_content_types = $enp_respect->btn_content_types;
        $btn_must_be_logged_in = get_option('enp_button_must_be_logged_in');
        $btn_allow_data_tracking = get_option('enp_button_allow_data_tracking');
        $btn_promote_enp = get_option('enp_button_promote_enp');

        ?>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="enp-button-type">Buttons</label>
                    </th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="checkbox" name="enp_button_type[respect]" aria-describedby="enp-button-type-description" value="true" <?php checked(true, $btn_types["respect"]);?> /> Respect
                            </label>
                            <br/>
                            <label>
                                <input type="checkbox" name="enp_button_type[recommend]" aria-describedby="enp-button-type-description" value="true" <?php checked(true, $btn_types["recommend"]);?> /> Recommend
                            </label>
                            <br/>
                            <label>
                                <input type="checkbox" name="enp_button_type[important]" aria-describedby="enp-button-type-description" value="true" <?php checked(true, $btn_types["important"]);?>/> Important
                            </label>

                            <p id="enp-button-type-description"class="description">Which buttons do you want to use on your site?</p>

                            <p class="description">Have an idea for other button text options? Let us know! ____@engagingnewsproject.org
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="enp-button-content-type">How to Use the Buttons</label>
                    </th>
                    <td>
                        <fieldset>
                            <? echo $enp_respect->buildContentTypeChecklist();?>
                        </fieldset>
                        <p id="enp-button-content-type-description" class="description">Which kinds of content do you want the button(s) to display for?</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        Respect Button Plugin Setting
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
        <?php settings_fields( 'enp_respect_button_settings' ); ?>
        <?php do_settings_sections( 'enp_respect_button_settings' ); ?>
        <?php submit_button(); ?>
    </form>

    <p>The Respect Button plugin is made by the <a href="http://engagingnewsproject.org">Engaging News Project</a>, a nonprofit at the University of Texas at Austin that researches COMMERCIALLY-VIABLE and DEMOCRATICALLY-BENEFICIAL ways to improve ONLINE NEWS</p>
</div>
<?php
}
