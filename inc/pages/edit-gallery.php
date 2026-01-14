<?php

function emy_admin_edit_gallery_page($slug) {
    $galleries = get_option('emy_galleries', []);

    if (!isset($galleries[$slug])) {
        echo '<div class="wrap"><h1>Gallery Not Found</h1>';
        echo '<p>The requested gallery does not exist. <a href="?page=enspyred-manual-yelp">Back to galleries</a></p></div>';
        return;
    }

    $gallery = $galleries[$slug];
    $config = emy_get_gallery_config($slug);

    // Ensure we have the reviews array
    $reviews = isset($config['reviews']) && is_array($config['reviews']) ? $config['reviews'] : [];

    // Check if we're editing a specific review
    $editing_index = isset($_GET['edit_review']) ? intval($_GET['edit_review']) : -1;

    // Show any messages
    settings_errors('emy_admin');

    if (isset($_GET['updated'])) {
        echo '<div class="notice notice-success is-dismissible"><p>Gallery updated successfully!</p></div>';
    }
    ?>

    <div class="wrap">
        <h1>Edit Review Set: <?php echo esc_html($gallery['name']); ?></h1>
        <p><a href="?page=enspyred-manual-yelp">&larr; Back to Review Sets</a></p>

        <!-- Gallery Name Form -->
        <div class="card" style="max-width: 800px; margin-top: 20px;">
            <h2>Review Set Settings</h2>
            <form method="post" action="">
                <?php wp_nonce_field('emy_admin', 'emy_nonce'); ?>
                <input type="hidden" name="action" value="update_gallery">
                <input type="hidden" name="gallery_slug" value="<?php echo esc_attr($slug); ?>">

                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="gallery_name">Review Set Name</label></th>
                        <td>
                            <input type="text" name="gallery_name" id="gallery_name" value="<?php echo esc_attr($gallery['name']); ?>" class="regular-text" required>
                            <p class="description">Current slug: <code><?php echo esc_html($slug); ?></code> (will be regenerated if name changes)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Shortcode</th>
                        <td>
                            <input type="text" value="[enspyred_yelp gallery=&quot;<?php echo esc_attr($slug); ?>&quot;]" readonly class="regular-text" onclick="this.select();" style="background: #f0f0f0;">
                            <p class="description">Copy this shortcode to display reviews on any page or post</p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <input type="submit" class="button button-primary" value="Update Review Set">
                </p>
            </form>
        </div>

        <!-- Reviews List -->
        <div class="card" style="max-width: 1200px; margin-top: 30px;">
            <h2>Reviews</h2>

            <?php if (empty($reviews)): ?>
                <p style="color: #666; font-style: italic;">No reviews yet. Add your first review below.</p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Order</th>
                            <th>Title</th>
                            <th style="width: 250px;">Review ID</th>
                            <th style="width: 250px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $index => $review): ?>
                            <tr>
                                <?php if ($editing_index === $index): ?>
                                    <!-- Edit Mode -->
                                    <td style="text-align: center; font-weight: bold;">#<?php echo ($index + 1); ?></td>
                                    <td colspan="3">
                                        <form method="post" style="margin: 0;">
                                            <?php wp_nonce_field('emy_admin', 'emy_nonce'); ?>
                                            <input type="hidden" name="action" value="update_review">
                                            <input type="hidden" name="gallery_slug" value="<?php echo esc_attr($slug); ?>">
                                            <input type="hidden" name="review_index" value="<?php echo esc_attr($index); ?>">

                                            <div style="display: flex; gap: 10px; align-items: flex-end;">
                                                <div style="flex: 1;">
                                                    <label style="font-size: 11px; color: #666; display: block; margin-bottom: 3px;">Title</label>
                                                    <input type="text" name="review_title" value="<?php echo esc_attr($review['title']); ?>" class="regular-text" required style="width: 100%;">
                                                </div>
                                                <div style="flex: 1;">
                                                    <label style="font-size: 11px; color: #666; display: block; margin-bottom: 3px;">Review ID</label>
                                                    <input type="text" name="review_id" value="<?php echo esc_attr($review['reviewId']); ?>" class="regular-text" required style="width: 100%;">
                                                </div>
                                                <div>
                                                    <button type="submit" class="button button-primary button-small">Save</button>
                                                    <a href="?page=enspyred-manual-yelp&edit=<?php echo esc_attr($slug); ?>" class="button button-small">Cancel</a>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                <?php else: ?>
                                    <!-- View Mode -->
                                    <td style="text-align: center; font-weight: bold;">#<?php echo ($index + 1); ?></td>
                                    <td><?php echo esc_html($review['title']); ?></td>
                                    <td><code style="font-size: 12px;"><?php echo esc_html($review['reviewId']); ?></code></td>
                                    <td>
                                        <!-- Edit -->
                                        <a href="?page=enspyred-manual-yelp&edit=<?php echo esc_attr($slug); ?>&edit_review=<?php echo esc_attr($index); ?>" class="button button-small">Edit</a>

                                        <!-- Reorder Up -->
                                        <?php if ($index > 0): ?>
                                            <form method="post" style="display: inline-block;">
                                                <?php wp_nonce_field('emy_admin', 'emy_nonce'); ?>
                                                <input type="hidden" name="action" value="reorder_review">
                                                <input type="hidden" name="gallery_slug" value="<?php echo esc_attr($slug); ?>">
                                                <input type="hidden" name="review_index" value="<?php echo esc_attr($index); ?>">
                                                <input type="hidden" name="direction" value="up">
                                                <button type="submit" class="button button-small" title="Move Up">↑</button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- Reorder Down -->
                                        <?php if ($index < count($reviews) - 1): ?>
                                            <form method="post" style="display: inline-block;">
                                                <?php wp_nonce_field('emy_admin', 'emy_nonce'); ?>
                                                <input type="hidden" name="action" value="reorder_review">
                                                <input type="hidden" name="gallery_slug" value="<?php echo esc_attr($slug); ?>">
                                                <input type="hidden" name="review_index" value="<?php echo esc_attr($index); ?>">
                                                <input type="hidden" name="direction" value="down">
                                                <button type="submit" class="button button-small" title="Move Down">↓</button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- Delete -->
                                        <form method="post" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this review?');">
                                            <?php wp_nonce_field('emy_admin', 'emy_nonce'); ?>
                                            <input type="hidden" name="action" value="delete_review">
                                            <input type="hidden" name="gallery_slug" value="<?php echo esc_attr($slug); ?>">
                                            <input type="hidden" name="review_index" value="<?php echo esc_attr($index); ?>">
                                            <button type="submit" class="button button-small button-link-delete" title="Delete">Delete</button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Add Review Form -->
        <div class="card" style="max-width: 800px; margin-top: 30px;">
            <h2>Add New Review</h2>
            <p class="description" style="margin-bottom: 20px;">
                <strong>How to find a Yelp Review ID:</strong><br>
                1. Visit the review on Yelp.com<br>
                2. Look at the URL - it will contain the Review ID (usually a long alphanumeric string with hyphens)<br>
                3. Example: <code>https://www.yelp.com/biz/business-name/REVIEW-ID-HERE</code><br>
                4. Copy the Review ID and paste it below
            </p>

            <form method="post" action="">
                <?php wp_nonce_field('emy_admin', 'emy_nonce'); ?>
                <input type="hidden" name="action" value="add_review">
                <input type="hidden" name="gallery_slug" value="<?php echo esc_attr($slug); ?>">

                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="review_title">Review Title</label></th>
                        <td>
                            <input type="text" name="review_title" id="review_title" class="regular-text" required placeholder="e.g., Great Service, Excellent Experience">
                            <p class="description">A friendly name to help you identify this review in the list</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="review_id">Yelp Review ID</label></th>
                        <td>
                            <input type="text" name="review_id" id="review_id" class="regular-text" required placeholder="e.g., abc123-def456-ghi789">
                            <p class="description">The unique ID from the Yelp review URL</p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <input type="submit" class="button button-primary button-large" value="Add Review">
                    <a href="?page=enspyred-manual-yelp" class="button button-large">Cancel</a>
                </p>
            </form>
        </div>
    </div>

    <script>
    // Auto-submit the edit form when user presses Enter in input fields
    document.addEventListener('DOMContentLoaded', function() {
        var editInputs = document.querySelectorAll('input[name="review_title"], input[name="review_id"]');
        editInputs.forEach(function(input) {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    var form = input.closest('tr').querySelector('form[action*="update_review"]');
                    if (!form) {
                        form = input.closest('tr').querySelector('button[type="submit"]').closest('form');
                    }
                    if (form) {
                        form.submit();
                    }
                }
            });
        });
    });
    </script>

    <?php
}
