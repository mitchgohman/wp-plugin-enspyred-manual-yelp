<?php

// Router for Yelp Reviews admin navigation
function emy_admin_galleries_router() {
    // Handle form submissions
    if ($_POST && isset($_POST['emy_nonce']) && wp_verify_nonce(wp_unslash($_POST['emy_nonce']), 'emy_admin')) {
        emy_handle_admin_form_submission();
    }

    // Check if we're editing a specific gallery
    $editing_gallery = isset($_GET['edit']) ? sanitize_text_field(wp_unslash($_GET['edit'])) : null;
    if ($editing_gallery) {
        emy_admin_edit_gallery_page($editing_gallery);
        return;
    }

    // Otherwise show galleries list
    echo '<div class="wrap">';
    echo '<h1>Yelp Review Galleries</h1>';
    emy_admin_galleries_page();
    echo '</div>';
}

// Handle admin form submissions
function emy_handle_admin_form_submission() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    $action = isset($_POST['action']) ? sanitize_text_field(wp_unslash($_POST['action'])) : '';

    switch ($action) {
        case 'create_gallery':
            $gallery_name = isset($_POST['new_gallery_name']) ? sanitize_text_field(wp_unslash($_POST['new_gallery_name'])) : '';
            if (!empty($gallery_name)) {
                $slug = emy_create_gallery($gallery_name);
                add_settings_error('emy_admin', 'gallery_created', "Gallery '{$gallery_name}' created with slug '{$slug}'.", 'updated');
            }
            break;

        case 'delete_gallery':
            $gallery_slug = isset($_POST['gallery_slug']) ? sanitize_text_field(wp_unslash($_POST['gallery_slug'])) : '';
            if ($gallery_slug) {
                emy_delete_gallery($gallery_slug);
                add_settings_error('emy_admin', 'gallery_deleted', 'Gallery deleted.', 'updated');
            }
            break;

        case 'update_gallery':
            $gallery_slug = isset($_POST['gallery_slug']) ? sanitize_text_field(wp_unslash($_POST['gallery_slug'])) : '';
            $gallery_name = isset($_POST['gallery_name']) ? sanitize_text_field(wp_unslash($_POST['gallery_name'])) : '';
            $config_json = isset($_POST['gallery_config']) ? wp_unslash($_POST['gallery_config']) : '';

            if ($gallery_slug) {
                // Validate JSON
                $decoded = json_decode($config_json, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    // Update config
                    emy_update_gallery_config($gallery_slug, $config_json);

                    // Update name if changed
                    if (!empty($gallery_name)) {
                        $new_slug = emy_update_gallery_name($gallery_slug, $gallery_name);
                        if ($new_slug && $new_slug !== $gallery_slug) {
                            // Redirect to new slug
                            wp_safe_redirect(admin_url('admin.php?page=enspyred-manual-yelp&edit=' . $new_slug . '&updated=1'));
                            exit;
                        }
                    }

                    add_settings_error('emy_admin', 'gallery_updated', 'Gallery updated successfully.', 'updated');
                } else {
                    add_settings_error('emy_admin', 'invalid_json', 'Invalid JSON. Please check your syntax.', 'error');
                }
            }
            break;
    }
}
