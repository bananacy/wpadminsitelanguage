<?php
/*
Plugin Name: WP Site Language Per User
Description: A WordPress plugin to set the dashboard "site" language based on user preference.
Version: 1.0
Author: Bananacy
*/



// Add custom field to user profile
function add_dashboard_language_field($user) {
    $dashboard_language = get_user_meta($user->ID, 'dashboard_language', true);
?>
    <h3><?php _e('Dashboard Site Language', 'dashboard-language-plugin'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="dashboard_language"><?php _e('Select Language', 'dashboard-language-plugin'); ?></label></th>
            <td>
                <select name="dashboard_language" id="dashboard_language">
                    <?php
                 $languages = wp_get_available_translations();

// Add English (United States) manually
echo '<option value="en_US" ' . selected($dashboard_language, 'en_US', false) . '>English (United States)</option>';

// Display other available languages
foreach ($languages as $code => $language) {
    // Skip English (United States) since it's already added manually
    if ($code === 'en_US') {
        continue;
    }

    $label = $language['native_name'];

    echo '<option value="' . esc_attr($code) . '" ' . selected($dashboard_language, $code, false) . '>' . esc_html($label) . '</option>';
}

                    ?>
                </select>
            </td>
        </tr>
    </table>
<?php
}

add_action('show_user_profile', 'add_dashboard_language_field');
add_action('edit_user_profile', 'add_dashboard_language_field');

// Save custom field when user profile is updated
function save_dashboard_language_field($user_id) {
    if (current_user_can('edit_user', $user_id)) {
        update_user_meta($user_id, 'dashboard_language', sanitize_text_field($_POST['dashboard_language']));
    }
}

add_action('personal_options_update', 'save_dashboard_language_field');
add_action('edit_user_profile_update', 'save_dashboard_language_field');

// Set site language in the dashboard
function set_dashboard_language() {
    if (is_admin()) {
        $dashboard_language = get_user_meta(get_current_user_id(), 'dashboard_language', true);
        if ($dashboard_language && function_exists('switch_to_locale')) {
            switch_to_locale($dashboard_language);
        }
    }
}

add_action('init', 'set_dashboard_language');
