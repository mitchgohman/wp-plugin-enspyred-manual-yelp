<?php

// Plugin activation hook - runs when plugin is first activated
register_activation_hook(__FILE__, 'emy_activate_plugin');

function emy_activate_plugin() {
    emy_seed_default_gallery();
}

// Check for plugin updates on init
add_action('init', function () {
    $current_version = '1.0.0';
    $stored_version = get_option('emy_plugin_version', '0.0.0');

    if (version_compare($stored_version, $current_version, '<')) {
        emy_seed_default_gallery();
        update_option('emy_plugin_version', $current_version, false);
    }
});

function emy_seed_default_gallery() {
    $path = plugin_dir_path(dirname(__FILE__)) . 'defaultGallery.json';
    if (!file_exists($path)) {
        return; // silently skip if not present
    }

    $raw = @file_get_contents($path);
    if ($raw === false || $raw === '') {
        return;
    }

    // Validate JSON before storing
    $decoded = json_decode($raw, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
        return; // don't store invalid JSON
    }

    // Store the raw JSON string as default gallery config
    update_option('emy_gallery_default', $raw, false);

    // Initialize empty gallery registry
    if (!get_option('emy_galleries')) {
        $galleries = []; // Empty - users must create galleries
        update_option('emy_galleries', $galleries, false);
    }
}
