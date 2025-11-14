<?php
// Add admin menu and settings page
add_action('admin_menu', function () {
    // Create top-level menu only if not already present
    if (!isset($GLOBALS['menu_slug_enspyred'])) {
        add_menu_page(
            'Enspyred',
            'Enspyred',
            'manage_options',
            'enspyred',
            '', // No callback
            'dashicons-admin-generic',
            26
        );
        $GLOBALS['menu_slug_enspyred'] = true;
    }

    // Add this plugin's submenu
    add_submenu_page(
        'enspyred',
        'Yelp Reviews',
        'Yelp Reviews',
        'manage_options',
        'enspyred-manual-yelp',
        'emy_admin_galleries_router'
    );
}, 9);
