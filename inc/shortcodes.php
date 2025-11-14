<?php
// Add shortcode to render the React mount point for Yelp reviews.
// Safe for use inside the_content: returns a string (no echo) and supports multiple instances.
add_shortcode('enspyred_yelp', function ($atts = []) {
    static $instance = 0;
    $instance++;

    $atts = shortcode_atts([
        'id'      => '',            // optional custom id suffix
        'class'   => '',            // optional extra class names (space-delimited)
        'gallery' => '',            // gallery slug (required)
        'branch'  => '',            // optional filter by branch
        'limit'   => '',            // optional limit number of reviews
    ], $atts, 'enspyred_yelp');

    // Gallery is required
    if (empty($atts['gallery'])) {
        return '<div style="border: 1px solid #dc3545; background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px;">' .
               '<strong>Yelp Reviews Error:</strong> No gallery specified. Please use [enspyred_yelp gallery="your-gallery-slug"].' .
               '</div>';
    }

    // Verify gallery exists
    $galleries = get_option('emy_galleries', []);
    if (!isset($galleries[$atts['gallery']])) {
        return '<div style="border: 1px solid #dc3545; background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px;">' .
               '<strong>Yelp Reviews Error:</strong> Gallery "' . esc_html($atts['gallery']) . '" not found.' .
               '</div>';
    }

    // Build a unique, predictable id
    $suffix = $atts['id'] !== '' ? sanitize_title_with_dashes($atts['id']) : (string) $instance;
    $dom_id = 'enspyred-manual-yelp-' . $suffix;

    // Sanitize multiple class names while preserving spaces
    $extra_classes = trim(preg_replace('/\s+/', ' ', $atts['class']));
    $class_tokens  = array_filter(explode(' ', $extra_classes));
    $safe_tokens   = array_map('sanitize_html_class', $class_tokens);
    $classes = trim('enspyred-manual-yelp emy-root ' . implode(' ', $safe_tokens));

    // Return the mount point markup (no echo), so it renders correctly inside the_content
    $html  = '<div'
          .  ' id="' . esc_attr($dom_id) . '"'
          .  ' class="' . esc_attr($classes) . '"'
          .  ' data-emy-instance="' . esc_attr($suffix) . '"'
          .  ' data-emy-gallery="' . esc_attr($atts['gallery']) . '"'
          .  ' data-emy-branch="' . esc_attr($atts['branch']) . '"'
          .  ' data-emy-limit="' . esc_attr($atts['limit']) . '">'
          .  '</div>';

    return $html;
});
