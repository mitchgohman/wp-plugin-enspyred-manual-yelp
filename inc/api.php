<?php

// Register REST API endpoints
add_action('rest_api_init', function () {
    register_rest_route('enspyred-manual-yelp/v1', '/reviews', [
        'methods' => 'GET',
        'callback' => 'emy_api_get_reviews',
        'permission_callback' => '__return_true', // Public endpoint
    ]);
});

function emy_api_get_reviews($request) {
    $gallery = $request->get_param('gallery');
    $limit = $request->get_param('limit');

    if (empty($gallery)) {
        return new WP_Error('missing_gallery', 'Gallery parameter is required', ['status' => 400]);
    }

    // Get gallery config
    $config = emy_get_gallery_config($gallery);

    if (!$config || !isset($config['reviews'])) {
        return new WP_Error('gallery_not_found', 'Gallery not found', ['status' => 404]);
    }

    $reviews = $config['reviews'];

    // Sort by order field
    usort($reviews, function($a, $b) {
        $orderA = isset($a['order']) ? intval($a['order']) : 0;
        $orderB = isset($b['order']) ? intval($b['order']) : 0;
        return $orderA - $orderB;
    });

    // Apply limit if specified
    if (!empty($limit) && is_numeric($limit)) {
        $reviews = array_slice($reviews, 0, intval($limit));
    }

    return [
        'success' => true,
        'gallery' => $gallery,
        'reviews' => $reviews,
        'count' => count($reviews),
    ];
}
