<?php

/**
Plugin Name: Custom Footer Message
Plugin URI: https://github.com/washington-chekata/Custom-Footer-Message
Description: A simple plugin that adds a custom message to the footer
Version: 1.0.0
Author: Washington Chekata
Author URI: https://github.com/washington-chekata
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

//Add menu item to the wordpress admin dashboard
function cfm_add_admin_menu()
{

    add_options_page(
        'Footer Message Settings',
        'Footer Message',
        'manage_options',
        'custom-footer-message',
        'cfm_settings_page'
    );
}

add_action('admin_menu', 'cfm_add_admin_menu');

//Register settings for the plugin
function cfm_register_settings()
{
    register_setting('cfm_settings_group', 'cfm_footer_message');
    register_setting('cfm_settings_group', 'cfm_message_enabled');
    register_setting('cfm_settings_group', 'cfm_text_color');
    register_setting('cfm_settings_group', 'cfm_bg_color');
    register_setting('cfm_settings_group', 'cfm_font_family');
    register_setting('cfm_settings_group', 'cfm_font_size');
    register_setting('cfm_settings_group', 'cfm_text_align');
}

add_action('admin_init', 'cfm_register_settings');


// Define the array of Google Fonts
function cfm_get_google_fonts()
{
    return [
        'Roboto' => 'Roboto:wght@400;700',
        'Open Sans' => 'Open+Sans:wght@400;700',
        'Lobster' => 'Lobster',
        'Montserrat' => 'Montserrat:wght@400;700',
        'Arial' => 'Arial', // Default web-safe fonts (no need for Google CDN)
        'Georgia' => 'Georgia',
        'Times New Roman' => 'Times+New+Roman',
        'Courier New' => 'Courier+New',
        'Verdana' => 'Verdana'
    ];
}

// Enqueue Google Fonts dynamically
function cfm_enqueue_google_fonts()
{
    $fonts = cfm_get_google_fonts();
    $google_fonts = array_filter($fonts, function ($value) {
        // Filter out system fonts that don't need to be loaded via Google CDN
        return strpos($value, '+') !== false || strpos($value, 'wght') !== false;
    });

    $google_fonts_url = 'https://fonts.googleapis.com/css2?' . implode('&family=', $google_fonts) . '&display=swap';
    wp_enqueue_style('cfm-google-fonts', $google_fonts_url, false);
}

//create the settings page
function cfm_settings_page()
{
?>
    <div class="wrap">
        <h1>Footer Message Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('cfm_settings_group');
            do_settings_sections('cfm_settings_group');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable Footer Message</th>
                    <td>
                        <input type="checkbox" name="cfm_message_enabled" value="1" <?php checked(1, get_option('cfm_message_enabled', 1)); ?> />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Footer Message</th>
                    <td>
                        <textarea name="cfm_footer_message" rows="4" cols="50"><?php echo esc_attr(get_option('cfm_footer_message', 'Thank you for visiting!')); ?></textarea>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Text Color</th>
                    <td>
                        <input type="color" name="cfm_text_color" value="<?php echo esc_attr(get_option('cfm_text_color', '#000000')); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Background Color</th>
                    <td>
                        <input type="color" name="cfm_bg_color" value="<?php echo esc_attr(get_option('cfm_bg_color', '#ffffff')); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Font Family
                    <td>
                        <select name="cfm_font_family">
                            <?php
                            $fonts = cfm_get_google_fonts();
                            foreach ($fonts as $font_name => $font_value) {
                                $selected = get_option('cfm_font_family') == $font_name ? 'selected="selected"' : '';
                                echo '<option value="' . esc_attr($font_name) . '" ' . esc_attr($selected) . '>' . esc_html($font_name) . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    </th>
                </tr>
                <tr valign="top">
                    <th scope="row">Font Size (px)</th>
                    <td>
                        <input type="number" name="cfm_font_size" value="<?php echo esc_attr(get_option('cfm_font_size', '14')); ?>" min="10" max="50" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Text Alignment</th>
                    <td>
                        <select name="cfm_text_align">
                            <option value="left" <?php selected(get_option('cfm_text_align'), 'left'); ?>>Left</option>
                            <option value="center" <?php selected(get_option('cfm_text_align'), 'center'); ?>>Center</option>
                            <option value="right" <?php selected(get_option('cfm_text_align'), 'right'); ?>>Right</option>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <h2>Preview</h2>
        <div style="padding: 20px; text-align: <?php echo esc_attr(get_option('cfm_text_align', 'center')); ?>; background-color: <?php echo esc_attr(get_option('cfm_bg_color', '#ffffff')); ?>; color: <?php echo esc_attr(get_option('cfm_text_color', '#000000')); ?>; font-family: <?php echo esc_attr(get_option('cfm_font_family', 'Arial')); ?>; font-size: <?php echo esc_attr(get_option('cfm_font_size', '14')); ?>px;">
            <?php echo esc_html(get_option('cfm_footer_message', 'Thank you for visiting!')); ?>
        </div>
    </div>

<?php
}

//Display the footer message
function display_custom_footer_message()
{

    //check if the message is enabled 
    if (get_option('cfm_message_enabled')) {
        $footer_message = esc_html(get_option('cfm_footer_message', 'Thank you for visiting!'));
        $text_color = esc_attr(get_option('cfm_text_color', '#000000'));
        $bg_color = esc_attr(get_option('cfm_bg_color', '#ffffff'));
        $font_family = esc_attr(get_option('cfm_font_family', 'Arial'));
        $font_size = esc_attr(get_option('cfm_font_size', '14'));
        $text_align = esc_attr(get_option('cfm_text_align', 'center'));

        echo '<div style="padding: 20px; text-align: ' . esc_attr($text_align) . '; background-color: ' . esc_attr($bg_color) . '; color: ' . esc_attr($text_color) . '; font-family: ' . esc_attr($font_family) . '; font-size: ' . esc_attr($font_size) . 'px;">';
        echo esc_html($footer_message);
        echo '</div>';
    }
}

//Add the function to wordpress
add_action('wp_footer', 'display_custom_footer_message')

?>