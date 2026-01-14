<?php

// Router for Yelp Reviews admin navigation
function emy_admin_galleries_router() {
    $tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'galleries';

    // Form submissions are now handled in admin_init hook (see inc/menus.php)
    // This ensures redirects happen before any output

    // Check if we're editing a specific gallery
    $editing_gallery = isset($_GET['edit']) ? sanitize_text_field(wp_unslash($_GET['edit'])) : null;
    if ($editing_gallery) {
        emy_admin_edit_gallery_page($editing_gallery);
        return;
    }

    // Show tab navigation
    echo '<div class="wrap">';
    echo '<h1>Yelp Reviews</h1>';
    echo '<nav class="nav-tab-wrapper">';
    echo '<a href="?page=enspyred-manual-yelp&tab=galleries" class="nav-tab' . ($tab === 'galleries' ? ' nav-tab-active' : '') . '">Galleries</a>';
    echo '<a href="?page=enspyred-manual-yelp&tab=settings" class="nav-tab' . ($tab === 'settings' ? ' nav-tab-active' : '') . '">Settings</a>';
    echo '</nav>';
    echo '<div style="margin-top: 30px;">';

    // Route to appropriate page
    switch ($tab) {
        case 'settings':
            emy_admin_settings_page();
            break;
        default:
            emy_admin_galleries_page();
            break;
    }

    echo '</div>';
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

            if ($gallery_slug && !empty($gallery_name)) {
                // Update gallery name
                $new_slug = emy_update_gallery_name($gallery_slug, $gallery_name);
                if ($new_slug && $new_slug !== $gallery_slug) {
                    // Redirect to new slug
                    wp_safe_redirect(admin_url('admin.php?page=enspyred-manual-yelp&edit=' . $new_slug . '&updated=1'));
                    exit;
                }
                add_settings_error('emy_admin', 'gallery_updated', 'Gallery updated successfully.', 'updated');
            }
            break;

        case 'add_review':
            $gallery_slug = isset($_POST['gallery_slug']) ? sanitize_text_field(wp_unslash($_POST['gallery_slug'])) : '';
            $review_title = isset($_POST['review_title']) ? sanitize_text_field(wp_unslash($_POST['review_title'])) : '';
            $review_id = isset($_POST['review_id']) ? sanitize_text_field(wp_unslash($_POST['review_id'])) : '';

            if ($gallery_slug && !empty($review_title) && !empty($review_id)) {
                // Validate Review ID format
                if (!emy_validate_review_id($review_id)) {
                    add_settings_error('emy_admin', 'invalid_review_id', 'Invalid Review ID format. Only alphanumeric characters, hyphens, and underscores are allowed.', 'error');
                } else {
                    // Add review
                    emy_add_review_to_gallery($gallery_slug, $review_title, $review_id);
                    add_settings_error('emy_admin', 'review_added', 'Review added successfully.', 'updated');

                    // Redirect to refresh page
                    wp_safe_redirect(admin_url('admin.php?page=enspyred-manual-yelp&edit=' . $gallery_slug . '&updated=1'));
                    exit;
                }
            }
            break;

        case 'update_review':
            $gallery_slug = isset($_POST['gallery_slug']) ? sanitize_text_field(wp_unslash($_POST['gallery_slug'])) : '';
            $review_index = isset($_POST['review_index']) ? intval(wp_unslash($_POST['review_index'])) : -1;
            $review_title = isset($_POST['review_title']) ? sanitize_text_field(wp_unslash($_POST['review_title'])) : '';
            $review_id = isset($_POST['review_id']) ? sanitize_text_field(wp_unslash($_POST['review_id'])) : '';

            if ($gallery_slug && $review_index >= 0 && !empty($review_title) && !empty($review_id)) {
                // Validate Review ID format
                if (!emy_validate_review_id($review_id)) {
                    add_settings_error('emy_admin', 'invalid_review_id', 'Invalid Review ID format. Only alphanumeric characters, hyphens, and underscores are allowed.', 'error');
                    // Don't exit - let page render with error
                    break;
                }

                // Update review
                emy_update_review_in_gallery($gallery_slug, $review_index, $review_title, $review_id);

                // Redirect to refresh page (without edit_review parameter)
                wp_safe_redirect(admin_url('admin.php?page=enspyred-manual-yelp&edit=' . $gallery_slug . '&updated=1'));
                exit;
            }
            break;

        case 'delete_review':
            $gallery_slug = isset($_POST['gallery_slug']) ? sanitize_text_field(wp_unslash($_POST['gallery_slug'])) : '';
            $review_index = isset($_POST['review_index']) ? intval(wp_unslash($_POST['review_index'])) : -1;

            if ($gallery_slug && $review_index >= 0) {
                emy_delete_review_from_gallery($gallery_slug, $review_index);
                add_settings_error('emy_admin', 'review_deleted', 'Review deleted successfully.', 'updated');

                // Redirect to refresh page
                wp_safe_redirect(admin_url('admin.php?page=enspyred-manual-yelp&edit=' . $gallery_slug . '&updated=1'));
                exit;
            }
            break;

        case 'reorder_review':
            $gallery_slug = isset($_POST['gallery_slug']) ? sanitize_text_field(wp_unslash($_POST['gallery_slug'])) : '';
            $review_index = isset($_POST['review_index']) ? intval(wp_unslash($_POST['review_index'])) : -1;
            $direction = isset($_POST['direction']) ? sanitize_text_field(wp_unslash($_POST['direction'])) : '';

            if ($gallery_slug && $review_index >= 0 && in_array($direction, ['up', 'down'])) {
                emy_reorder_review_in_gallery($gallery_slug, $review_index, $direction);

                // Redirect to refresh page
                wp_safe_redirect(admin_url('admin.php?page=enspyred-manual-yelp&edit=' . $gallery_slug));
                exit;
            }
            break;

        case 'update_settings':
            $settings = get_option('emy_settings', []);
            $settings['debug_mode'] = !empty($_POST['debug_mode']);
            update_option('emy_settings', $settings, false);
            add_settings_error('emy_settings', 'settings_updated', 'Settings saved successfully.', 'updated');
            break;
    }
}
