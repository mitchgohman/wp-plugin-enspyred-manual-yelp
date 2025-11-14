<?php
// Settings page for Yelp Reviews plugin

function emy_admin_settings_page() {
    $settings = get_option('emy_settings', []);

    // Show any messages
    settings_errors('emy_settings');
    ?>
    <form method="post" action="">
        <?php wp_nonce_field('emy_admin', 'emy_nonce'); ?>
        <input type="hidden" name="action" value="update_settings">

        <h2>Debug Settings</h2>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="debug_mode">Enable Debug Logging</label>
                </th>
                <td>
                    <input
                        type="checkbox"
                        id="debug_mode"
                        name="debug_mode"
                        value="1"
                        <?php checked($settings['debug_mode'] ?? false); ?>
                    />
                    <label for="debug_mode">Enable console logging for debugging</label>
                    <p class="description">When enabled, the plugin will output debug information to the browser console</p>
                </td>
            </tr>
        </table>

        <?php submit_button('Save Settings'); ?>
    </form>
    <?php
}
