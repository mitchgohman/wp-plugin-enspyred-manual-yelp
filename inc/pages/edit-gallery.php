<?php

function emy_admin_edit_gallery_page($slug) {
    $galleries = get_option('emy_galleries', []);

    if (!isset($galleries[$slug])) {
        echo '<div class="wrap"><h1>Gallery Not Found</h1>';
        echo '<p>The requested gallery does not exist. <a href="?page=enspyred-manual-yelp">Back to galleries</a></p></div>';
        return;
    }

    $gallery = $galleries[$slug];
    $config_json = get_option('emy_gallery_config_' . $slug, '{"reviews":[]}');

    // Pretty print JSON for editing
    $decoded = json_decode($config_json, true);
    $pretty_json = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    // Show any messages
    settings_errors('emy_admin');

    if (isset($_GET['updated'])) {
        echo '<div class="notice notice-success is-dismissible"><p>Gallery updated successfully!</p></div>';
    }
    ?>

    <div class="wrap">
        <h1>Edit Gallery: <?php echo esc_html($gallery['name']); ?></h1>
        <p><a href="?page=enspyred-manual-yelp">&larr; Back to Galleries</a></p>

        <form method="post" action="">
            <?php wp_nonce_field('emy_admin', 'emy_nonce'); ?>
            <input type="hidden" name="action" value="update_gallery">
            <input type="hidden" name="gallery_slug" value="<?php echo esc_attr($slug); ?>">

            <table class="form-table">
                <tr>
                    <th scope="row"><label for="gallery_name">Gallery Name</label></th>
                    <td>
                        <input type="text" name="gallery_name" id="gallery_name" value="<?php echo esc_attr($gallery['name']); ?>" class="regular-text" required>
                        <p class="description">Current slug: <code><?php echo esc_html($slug); ?></code> (will be regenerated if name changes)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="gallery_config">Reviews JSON</label></th>
                    <td>
                        <p class="description" style="margin-bottom: 10px;">
                            Edit the JSON configuration for your reviews. Each review should include business info, reviewer details, rating, and more.
                            <a href="#json-example" onclick="document.getElementById('json-example').style.display='block'; return false;">Show example format</a>
                        </p>
                        <div id="json-example" style="display:none; background:#f0f0f0; padding:15px; margin-bottom:15px; border-left:4px solid #0073aa;">
                            <strong>Example Review JSON Structure:</strong>
                            <pre style="margin:10px 0; overflow-x:auto; font-size:12px;">{
  "reviews": [
    {
      "id": "unique-id-1",
      "businessName": "Lawrence 24 Hour Door Service",
      "businessRating": 3.0,
      "businessReviewCount": 6,
      "businessUrl": "https://www.yelp.com/biz/...",
      "reviewerName": "Mr. Mansfield G.",
      "reviewerPhotoUrl": "https://s3-media.../o.jpg",
      "reviewerFriendsCount": 0,
      "reviewerReviewCount": 2,
      "rating": 5,
      "date": "7/22/2020",
      "text": "These guys are great. Had our warehouse...",
      "yelpUrl": "https://www.yelp.com/...",
      "branch": "Baldwin Park",
      "featured": true
    }
  ]
}</pre>
                            <button type="button" class="button" onclick="document.getElementById('json-example').style.display='none';">Hide example</button>
                        </div>
                        <textarea name="gallery_config" id="gallery_config" rows="20" class="large-text code" style="font-family: monospace; font-size: 13px;"><?php echo esc_textarea($pretty_json); ?></textarea>
                        <p class="description">
                            <button type="button" class="button" onclick="emy_validate_json()">Validate JSON</button>
                            <button type="button" class="button" onclick="emy_format_json()">Format JSON</button>
                            <span id="json-status" style="margin-left: 10px;"></span>
                        </p>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" class="button button-primary button-large" value="Save Gallery">
                <a href="?page=enspyred-manual-yelp" class="button button-large">Cancel</a>
            </p>
        </form>
    </div>

    <script>
    function emy_validate_json() {
        const textarea = document.getElementById('gallery_config');
        const status = document.getElementById('json-status');

        try {
            JSON.parse(textarea.value);
            status.innerHTML = '<span style="color: green;">✓ Valid JSON</span>';
            setTimeout(() => status.innerHTML = '', 3000);
            return true;
        } catch (e) {
            status.innerHTML = '<span style="color: red;">✗ Invalid JSON: ' + e.message + '</span>';
            return false;
        }
    }

    function emy_format_json() {
        const textarea = document.getElementById('gallery_config');
        const status = document.getElementById('json-status');

        try {
            const parsed = JSON.parse(textarea.value);
            textarea.value = JSON.stringify(parsed, null, 2);
            status.innerHTML = '<span style="color: green;">✓ Formatted</span>';
            setTimeout(() => status.innerHTML = '', 2000);
        } catch (e) {
            status.innerHTML = '<span style="color: red;">✗ Cannot format invalid JSON</span>';
        }
    }

    // Auto-validate on blur
    document.getElementById('gallery_config').addEventListener('blur', function() {
        emy_validate_json();
    });
    </script>

    <?php
}
