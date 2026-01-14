<?php

// Plugin activation hook - runs when plugin is first activated
register_activation_hook(__FILE__, 'emy_activate_plugin');

function emy_activate_plugin() {
    emy_seed_default_gallery();
}

// Check for plugin updates on init
add_action('init', function () {
    $current_version = '2.0.0';
    $stored_version = get_option('emy_plugin_version', '0.0.0');

    if (version_compare($stored_version, $current_version, '<')) {
        emy_seed_default_gallery();

        // If upgrading from v1.x to v2.x, migrate all galleries
        if (version_compare($stored_version, '2.0.0', '<')) {
            emy_migrate_all_galleries_to_v2();
        }

        update_option('emy_plugin_version', $current_version, false);
    }
});

// Migrate all galleries to v2 format
function emy_migrate_all_galleries_to_v2() {
    $galleries = get_option('emy_galleries', []);

    if (empty($galleries)) {
        enspyred_log('No galleries to migrate');
        return;
    }

    $migrated_count = 0;
    foreach ($galleries as $slug => $gallery) {
        if (emy_migrate_gallery_to_v2($slug)) {
            $migrated_count++;
        }
    }

    if ($migrated_count > 0) {
        enspyred_log("Migrated $migrated_count galleries to v2 format");
    }
}

function emy_seed_default_gallery() {
    // v2 structure - simplified default with empty reviews
    $default_config = [
        'version' => 2,
        'reviews' => []
    ];

    // Store as JSON string
    update_option('emy_gallery_default', json_encode($default_config), false);

    // Initialize empty gallery registry
    if (!get_option('emy_galleries')) {
        $galleries = []; // Empty - users must create galleries
        update_option('emy_galleries', $galleries, false);
    }
}
