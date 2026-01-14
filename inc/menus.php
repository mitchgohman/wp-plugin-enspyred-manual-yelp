<?php
// Handle form submissions EARLY before any output
add_action('admin_init', function () {
    // Only process if we're on our admin page and have a POST request
    if (!isset($_GET['page']) || $_GET['page'] !== 'enspyred-manual-yelp') {
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['emy_nonce']) && isset($_POST['action']) && wp_verify_nonce(wp_unslash($_POST['emy_nonce']), 'emy_admin')) {
        emy_handle_admin_form_submission();
        // If we reach here, redirect didn't happen (validation error)
    }
});

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
