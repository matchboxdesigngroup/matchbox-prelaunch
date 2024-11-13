<?php

/**
 *  Matchbox Prelaunch plugin for WordPress
 *
 * @package           matchbox-prelaunch
 * @link              https://github.com/matchboxdesigngroup/matchbox-prelaunch/
 * @author            Matchbox, Cullen Whitmore
 * @copyright         2024 Matchbox Design Group
 * @license           GPL v2 or later
 * 
 * Plugin Name:       Matchbox Prelaunch
 * Description:       Enable Matchbox testing tools.
 * Version:           0.2.0
 * Plugin URI:        https://github.com/matchboxdesigngroup/matchbox-prelaunch/
 * Author:            Matchbox, Cullen Whitmore
 * Author URI:        https://matchboxdesigngroup.com
 * Text Domain:       matchbox-prelaunch
 * Requires at least: 6.2
 * Requires PHP:      8.0
 * License:           GNU General Public License v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 *
 * This program is free software; you can redistribute it and/or modify it under 
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or( at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 *
 * This file represents the entry point for the Matchbox Prelaunch plugin where it handles
 * the initial setup like defining constants and including the core plugin class. It's
 * responsible for initiating the plugin's functionality by setting up necessary hooks
 * and loading required files.
 */

// Hook into WordPress admin initialization for setting up the settings page.
add_action('admin_init', 'matchbox_prelaunch_settings_init');
add_action('admin_menu', 'matchbox_prelaunch_settings_menu');

/**
 * Initialize Plugin Update Checker for GitHub-hosted updates.
 *
 * This function sets up the Plugin Update Checker (PUC) to check for plugin updates from
 * the specified GitHub repository. It is configured to look for the latest release
 * of the plugin, allowing the plugin to automatically fetch updates when a new version
 * is tagged in GitHub.
 *
 * @link https://github.com/YahnisElsts/plugin-update-checker?tab=readme-ov-file#github-integration
 * @return void
 * @since 0.3.0
 */
function matchbox_prelaunch_initialize_update_checker() {
    // Check if the Plugin Update Checker class exists to prevent potential conflicts.
    if ( !class_exists('YahnisElsts\PluginUpdateChecker\v5\PucFactory') ) {
        require_once plugin_dir_path(__FILE__) . 'includes/plugin-update-checker/plugin-update-checker.php';
    }

    // Initialize the update checker for the GitHub-hosted plugin.
    $updateChecker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
        'https://github.com/matchboxdesigngroup/matchbox-prelaunch',
        __FILE__,
        'matchbox-prelaunch'
    );

    // Configure the update checker to look for GitHub release assets.
    $updateChecker->getVcsApi()->enableReleaseAssets();
}
add_action('plugins_loaded', 'matchbox_prelaunch_initialize_update_checker');


/**
 * Initialize the plugin settings by registering the settings, sections, and fields.
 *
 * This function sets up the necessary components for a settings page within the WordPress admin.
 * It uses the WordPress Settings API to handle data storage and retrieval for the Userback access token.
 * Specifically, it:
 * - Registers a new setting where the access token will be stored.
 * - Adds a new settings section to group any related settings (currently only the access token).
 * - Adds a new settings field for inputting the Userback access token.
 *
 * @link https://developer.wordpress.org/reference/functions/register_setting/ Documentation for register_setting
 * @link https://developer.wordpress.org/reference/functions/add_settings_section/ Documentation for add_settings_section
 * @link https://developer.wordpress.org/reference/functions/add_settings_field/ Documentation for add_settings_field
 * 
 * @return void
 */
function matchbox_prelaunch_settings_init()
{
    // Register a setting for storing the Userback access token.
    register_setting('matchbox-prelaunch', 'matchbox_access_token');

    // Add a section within the settings page to hold various fields.
    add_settings_section(
        'matchbox_prelaunch_settings_section',
        'Userback Access Token Settings',
        'matchbox_prelaunch_settings_section_cb',
        'matchbox-prelaunch'
    );

    // Add a field to the previously defined section for the Userback access token.
    add_settings_field(
        'matchbox_access_token',
        'Access Token',
        'matchbox_prelaunch_access_token_field_cb',
        'matchbox-prelaunch',
        'matchbox_prelaunch_settings_section'
    );
}


// Callback function for settings section
function matchbox_prelaunch_settings_section_cb()
{
    echo '<p>Enter your Userback Access Token.</p>';
}

// Callback function for the access token field
function matchbox_prelaunch_access_token_field_cb()
{
    $access_token = get_option('matchbox_access_token');
    echo '<input type="text" id="matchbox_access_token" name="matchbox_access_token" value="' . esc_attr($access_token) . '" />';
}

// Add the settings page to the Tools menu
function matchbox_prelaunch_settings_menu()
{
    add_management_page(
        'Matchbox Prelaunch Settings',
        'Matchbox Prelaunch',
        'manage_options',
        'matchbox-prelaunch',
        'matchbox_prelaunch_options_page'
    );
}

// Settings page content
function matchbox_prelaunch_options_page()
{
?>
    <div class="wrap">
        <h2>Matchbox Prelaunch Settings</h2>
        <form action="options.php" method="post">
            <?php
            settings_fields('matchbox-prelaunch');
            do_settings_sections('matchbox-prelaunch');
            submit_button();
            ?>
        </form>
    </div>
<?php
}

// Load script with dynamic access token
add_action('init', 'matchbox_prelaunch_load_script');

function matchbox_prelaunch_load_script()
{
    if (current_user_can('administrator') || current_user_can('editor')) {
        add_action('wp_footer', 'matchbox_prelaunch_print_script', 100);
        add_action('admin_footer', 'matchbox_prelaunch_print_script', 100);
        add_action('admin_bar_menu', 'matchbox_prelaunch_add_userback_toggle', 100);
    }
}

function matchbox_prelaunch_print_script()
{
    $access_token = get_option('matchbox_access_token', 'default_token_if_not_set');
    echo "<script type='text/javascript'>
        window.Userback = window.Userback || {};
        Userback.access_token = '{$access_token}';
        (function(d) {
            var s = d.createElement('script'); s.async = true;
            s.src = 'https://static.userback.io/widget/v1.js';
            (d.head || d.body).appendChild(s);
        })(document);
    </script>";
}

/**
 * Add a toggle button to the WordPress admin bar for hiding or showing the Userback overlay
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function matchbox_prelaunch_add_userback_toggle($wp_admin_bar)
{
    // Define the SVG icon used by Userback
    $svg_icon = '
        <svg width="16" height="16" viewBox="0 0 30 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M5.25 0.875H8.5V2.5H11.75V5.75H18.25V2.5H21.5V0.875H24.75V4.125H21.5V5.75V7.375H24.75V10.625H26.375V5.75H29.625V13.875H26.375V18.75H23.125V23.625H19.875H16.625V20.375H19.875V18.75H10.125V20.375H13.375V23.625H10.125H6.875V18.75H3.625V13.875H0.375V5.75H3.625V10.625H5.25V7.375H8.5V5.75V4.125H5.25V0.875ZM8.5 15.5H11.75V10.625H8.5V15.5ZM18.25 15.5H21.5V10.625H18.25V15.5Z"></path>
        </svg>';

    // Create the pill toggle markup
    $toggle_markup = '
        <div class="matchbox-pill-toggle" id="matchbox-pill-toggle">
            <div class="toggle-icon">' . $svg_icon . '</div>
        </div>';

    $wp_admin_bar->add_node([
        'id'    => 'matchbox_userback_toggle',
        'title' => $toggle_markup,
        'href'  => '#',
        'meta'  => [
            'onclick' => 'matchboxToggleUserback(); return false;',
            'title'   => 'Show or hide the testing feedback overlay',
        ],
        'parent' => 'top-secondary', // Moves it to the right side of the admin bar near "Howdy, admin"
    ]);
}

// Enqueue the custom JavaScript and CSS files.
add_action('wp_enqueue_scripts', 'matchbox_prelaunch_enqueue_assets');
add_action('admin_enqueue_scripts', 'matchbox_prelaunch_enqueue_assets');

function matchbox_prelaunch_enqueue_assets()
{
    // Enqueue the JS file
    wp_enqueue_script('matchbox-toggle-userback', plugin_dir_url(__FILE__) . 'js/toggle-userback.js', ['jquery'], '1.0', true);

    // Enqueue the CSS file
    wp_enqueue_style('matchbox-toggle-userback-style', plugin_dir_url(__FILE__) . 'css/toggle-userback.css', [], '1.0', 'all');
}