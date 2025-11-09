<?php

function emy_admin_galleries_page() {
    $galleries = get_option('emy_galleries', []);

    // Show any messages
    settings_errors('emy_admin');
    ?>

    <h2>Create New Gallery</h2>
    <form method="post" action="">
        <?php wp_nonce_field('emy_admin', 'emy_nonce'); ?>
        <input type="hidden" name="action" value="create_gallery">
        <table class="form-table">
            <tr>
                <th scope="row"><label for="new_gallery_name">Gallery Name</label></th>
                <td>
                    <input type="text" name="new_gallery_name" id="new_gallery_name" class="regular-text" required>
                    <p class="description">Enter a descriptive name for your review gallery (e.g., "Homepage Reviews", "Baldwin Park").</p>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" class="button button-primary" value="Create Gallery">
        </p>
    </form>

    <hr style="margin: 40px 0;">

    <h2>Existing Galleries</h2>

    <?php if (empty($galleries)): ?>
        <p>No galleries yet. Create your first one above!</p>
    <?php else: ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col" style="width: 25%;">Name</th>
                    <th scope="col" style="width: 20%;">Slug</th>
                    <th scope="col" style="width: 35%;">Shortcode</th>
                    <th scope="col" style="width: 10%;">Created</th>
                    <th scope="col" style="width: 10%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($galleries as $slug => $gallery): ?>
                    <tr>
                        <td><strong><?php echo esc_html($gallery['name']); ?></strong></td>
                        <td><code><?php echo esc_html($slug); ?></code></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <input
                                    type="text"
                                    value='[enspyred_yelp gallery="<?php echo esc_attr($slug); ?>"]'
                                    readonly
                                    onclick="this.select();"
                                    style="flex: 1; font-family: monospace; font-size: 12px;"
                                >
                                <button
                                    type="button"
                                    class="button button-small"
                                    onclick="
                                        var input = this.previousElementSibling;
                                        input.select();
                                        document.execCommand('copy');
                                        this.textContent = 'Copied!';
                                        setTimeout(() => this.textContent = 'Copy', 1500);
                                    "
                                >Copy</button>
                            </div>
                            <p class="description" style="margin-top: 4px;">
                                Optional: Add <code>branch="Name"</code> or <code>limit="5"</code> attributes
                            </p>
                        </td>
                        <td><?php echo esc_html(date('Y-m-d', strtotime($gallery['created']))); ?></td>
                        <td>
                            <a href="?page=enspyred-manual-yelp&edit=<?php echo esc_attr($slug); ?>" class="button button-small">Edit</a>
                            <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this gallery?');">
                                <?php wp_nonce_field('emy_admin', 'emy_nonce'); ?>
                                <input type="hidden" name="action" value="delete_gallery">
                                <input type="hidden" name="gallery_slug" value="<?php echo esc_attr($slug); ?>">
                                <button type="submit" class="button button-small button-link-delete">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php
}
