<?php
/**
 * Debug Logging Helper
 */
if (!function_exists('enspyred_log')) {
    function enspyred_log($message) {
        if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            // Force logs directly to Docker stderr
            file_put_contents('php://stderr', '[EMY] ' . $message . PHP_EOL);
            // Also use error_log as backup
            error_log('[EMY] ' . $message);
        }
    }
}

// Generate unique slug for galleries
function emy_generate_unique_slug($name, $exclude_slug = null) {
    $base_slug = sanitize_title($name);
    $slug = $base_slug;
    $counter = 1;

    $galleries = get_option('emy_galleries', []);

    while (true) {
        $slug_exists = false;
        foreach ($galleries as $gallery_slug => $gallery) {
            if ($gallery_slug !== $exclude_slug && $gallery_slug === $slug) {
                $slug_exists = true;
                break;
            }
        }

        if (!$slug_exists) {
            break;
        }

        $counter++;
        $slug = $base_slug . '-' . $counter;
    }

    return $slug;
}

// Helper to create new gallery
function emy_create_gallery($name, $config_json = null) {
    $galleries = get_option('emy_galleries', []);

    // Use default config if none provided
    if (!$config_json) {
        $config_json = get_option('emy_gallery_default', '{"reviews":[]}');
    }

    // Generate unique slug
    $slug = emy_generate_unique_slug($name);

    // Store gallery config
    update_option('emy_gallery_config_' . $slug, $config_json, false);

    // Update registry
    $galleries[$slug] = [
        'slug' => $slug,
        'name' => sanitize_text_field($name),
        'created' => current_time('mysql')
    ];
    update_option('emy_galleries', $galleries, false);

    return $slug;
}

// Helper to get gallery config
function emy_get_gallery_config($slug) {
    $config_json = get_option('emy_gallery_config_' . $slug, '{"reviews":[]}');
    return json_decode($config_json, true);
}

// Helper to update gallery config
function emy_update_gallery_config($slug, $config_json) {
    update_option('emy_gallery_config_' . $slug, $config_json, false);
}

// Helper to update gallery name
function emy_update_gallery_name($old_slug, $new_name) {
    $galleries = get_option('emy_galleries', []);

    if (!isset($galleries[$old_slug])) {
        return false;
    }

    // Generate new slug from new name
    $new_slug = emy_generate_unique_slug($new_name, $old_slug);

    // If slug changed, move the config
    if ($old_slug !== $new_slug) {
        $config = get_option('emy_gallery_config_' . $old_slug);
        update_option('emy_gallery_config_' . $new_slug, $config, false);
        delete_option('emy_gallery_config_' . $old_slug);

        // Update registry with new slug
        $gallery_data = $galleries[$old_slug];
        unset($galleries[$old_slug]);
        $galleries[$new_slug] = [
            'slug' => $new_slug,
            'name' => sanitize_text_field($new_name),
            'created' => $gallery_data['created']
        ];
    } else {
        // Just update the name
        $galleries[$old_slug]['name'] = sanitize_text_field($new_name);
    }

    update_option('emy_galleries', $galleries, false);
    return $new_slug;
}

// Helper to delete gallery
function emy_delete_gallery($slug) {
    // Remove gallery config
    delete_option('emy_gallery_config_' . $slug);

    // Remove from registry
    $galleries = get_option('emy_galleries', []);
    unset($galleries[$slug]);
    update_option('emy_galleries', $galleries, false);
}
