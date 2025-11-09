<?php
// React Scripts
function emy_enqueue_react_assets() {
    try {
        // Use the main plugin file to get the correct plugin root
        $plugin_file = dirname(__DIR__) . '/enspyred-manual-yelp.php';
        $base = plugin_dir_path($plugin_file);
        $uri  = plugin_dir_url($plugin_file);

        $manifest_path = $base . '/build/.vite/manifest.json';
        if ( ! file_exists($manifest_path) ) {
            enspyred_log('React: Manifest missing at ' . $manifest_path);
            return; // build not present yet
        }

        $manifest_raw = file_get_contents($manifest_path);
        $manifest = json_decode($manifest_raw, true);
        if ( ! is_array($manifest) ) {
            enspyred_log('React: Manifest invalid JSON');
            return; // invalid manifest
        }

        $entries = array_values(array_filter($manifest, fn($v) => !empty($v['isEntry'])));
        if ( empty($entries) ) {
            enspyred_log('React: No entry found in manifest');
            return; // nothing to enqueue
        }

        $entry = $entries[0];
        wp_enqueue_script(
            'emy-app',
            $uri . 'build/' . $entry['file'],
            [],
            null,
            [ 'in_footer' => true, 'type' => 'module' ]
        );

        if (!empty($entry['css'])) {
            foreach ($entry['css'] as $css) {
                wp_enqueue_style('emy-style-' . md5($css), $uri . 'build/' . $css, [], null);
            }
        }

        // Provide REST info to JS (for fetching reviews)
        wp_localize_script('emy-app', 'EMY_DATA', [
            'root'  => esc_url_raw( rest_url('enspyred-manual-yelp/v1/') ),
            'nonce' => wp_create_nonce('wp_rest'),
        ]);
    } catch (Throwable $e) {
        enspyred_log('React: Enqueue error - ' . $e->getMessage());
    }
}

add_action('wp_enqueue_scripts', 'emy_enqueue_react_assets');

// Force module type on our app bundle
add_filter('script_loader_tag', function ($tag, $handle, $src) {
    if ($handle === 'emy-app') {
        // If no type attribute, inject it; if present but classic, replace it.
        if (strpos($tag, ' type=') === false) {
            $tag = str_replace('<script ', '<script type="module" ', $tag);
        } else {
            $tag = preg_replace('/type=("|\')text\/javascript\1/i', 'type="module"', $tag);
        }
    }
    return $tag;
}, 10, 3);
