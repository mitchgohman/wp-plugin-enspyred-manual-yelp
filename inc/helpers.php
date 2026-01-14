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

    // Use default config if none provided (v2 structure)
    if (!$config_json) {
        $config_json = get_option('emy_gallery_default', '{"version":2,"reviews":[]}');
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

// Validate Yelp Review ID format
function emy_validate_review_id($reviewId) {
    // Check not empty
    if (empty($reviewId) || !is_string($reviewId)) {
        return false;
    }

    // Trim whitespace
    $reviewId = trim($reviewId);

    // Basic format validation - allow alphanumeric, hyphens, underscores
    // Yelp Review IDs typically look like alphanumeric strings with hyphens
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $reviewId)) {
        return false;
    }

    // Check length (reasonable bounds)
    if (strlen($reviewId) < 3 || strlen($reviewId) > 200) {
        return false;
    }

    return true;
}

// Add review to gallery
function emy_add_review_to_gallery($slug, $title, $reviewId) {
    $config = emy_get_gallery_config($slug);

    // Initialize if needed
    if (!isset($config['reviews']) || !is_array($config['reviews'])) {
        $config['reviews'] = [];
    }

    // Determine next order number
    $nextOrder = 0;
    if (!empty($config['reviews'])) {
        $orders = array_map(function($review) {
            return isset($review['order']) ? intval($review['order']) : 0;
        }, $config['reviews']);
        $nextOrder = max($orders) + 1;
    }

    // Add new review
    $config['reviews'][] = [
        'title' => sanitize_text_field($title),
        'reviewId' => sanitize_text_field($reviewId),
        'order' => $nextOrder
    ];

    // Ensure version is set
    $config['version'] = 2;

    // Save
    emy_update_gallery_config($slug, json_encode($config));

    return true;
}

// Delete review from gallery by index
function emy_delete_review_from_gallery($slug, $index) {
    $config = emy_get_gallery_config($slug);

    if (!isset($config['reviews']) || !is_array($config['reviews'])) {
        return false;
    }

    // Check if index exists
    if (!isset($config['reviews'][$index])) {
        return false;
    }

    // Remove review at index
    array_splice($config['reviews'], $index, 1);

    // Reorder remaining reviews
    foreach ($config['reviews'] as $i => $review) {
        $config['reviews'][$i]['order'] = $i;
    }

    // Save
    emy_update_gallery_config($slug, json_encode($config));

    return true;
}

// Update review in gallery
function emy_update_review_in_gallery($slug, $index, $title, $reviewId) {
    $config = emy_get_gallery_config($slug);

    if (!isset($config['reviews']) || !is_array($config['reviews'])) {
        return false;
    }

    // Check if index exists
    if (!isset($config['reviews'][$index])) {
        return false;
    }

    // Update review
    $config['reviews'][$index]['title'] = sanitize_text_field($title);
    $config['reviews'][$index]['reviewId'] = sanitize_text_field($reviewId);

    // Save
    emy_update_gallery_config($slug, json_encode($config));

    return true;
}

// Reorder review in gallery
function emy_reorder_review_in_gallery($slug, $index, $direction) {
    $config = emy_get_gallery_config($slug);

    if (!isset($config['reviews']) || !is_array($config['reviews'])) {
        return false;
    }

    $count = count($config['reviews']);

    // Check if index exists
    if (!isset($config['reviews'][$index])) {
        return false;
    }

    $newIndex = $index;

    if ($direction === 'up' && $index > 0) {
        $newIndex = $index - 1;
    } elseif ($direction === 'down' && $index < $count - 1) {
        $newIndex = $index + 1;
    } else {
        // No movement possible
        return false;
    }

    // Swap reviews
    $temp = $config['reviews'][$index];
    $config['reviews'][$index] = $config['reviews'][$newIndex];
    $config['reviews'][$newIndex] = $temp;

    // Update order values
    $config['reviews'][$index]['order'] = $index;
    $config['reviews'][$newIndex]['order'] = $newIndex;

    // Save
    emy_update_gallery_config($slug, json_encode($config));

    return true;
}

// Migrate gallery from v1 (full JSON) to v2 (Review IDs only)
function emy_migrate_gallery_to_v2($slug) {
    $config = emy_get_gallery_config($slug);

    // Check if already migrated
    if (isset($config['version']) && $config['version'] >= 2) {
        return false;
    }

    // Convert old reviews to new format
    $newReviews = [];
    if (isset($config['reviews']) && is_array($config['reviews'])) {
        foreach ($config['reviews'] as $index => $review) {
            // Extract a title from old data if possible
            $title = 'Review ' . ($index + 1);
            if (isset($review['businessName']) && !empty($review['businessName'])) {
                $title = $review['businessName'];
            } elseif (isset($review['reviewerName']) && !empty($review['reviewerName'])) {
                $title = 'Review by ' . $review['reviewerName'];
            }

            // Use old 'id' field as reviewId if it exists
            $reviewId = isset($review['id']) ? $review['id'] : '';

            $newReviews[] = [
                'title' => sanitize_text_field($title),
                'reviewId' => sanitize_text_field($reviewId),
                'order' => $index
            ];
        }
    }

    $newConfig = [
        'version' => 2,
        'reviews' => $newReviews
    ];

    update_option('emy_gallery_config_' . $slug, json_encode($newConfig), false);

    enspyred_log("Migrated gallery '$slug' to v2 format");

    return true;
}
