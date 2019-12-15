<?php if (!defined('ABSPATH')) exit(); ?>

<div class="wrap">
    <h2><?php _e('Automatic Upload Images Settings', 'automatic-upload-images'); ?></h2>

    <?php if (isset($message["success"])) : ?>

            <div id="setting-error-settings_updated" class="updated settings-error">
                <p><strong><?php echo $message["success"]; ?></strong></p>
            </div>

    <?php endif; ?>
    <?PHP
    if (isset($message['error'])){
        foreach ($message['error'] as $item) {
        ?>
            <div class="error notice">
                <p><?=$item?></p>
            </div>
    <?PHP
        }
    }
    ?>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content" style="position: relative">
                <div class="stuffbox" style="padding: 0 20px">
                    <form method="POST">
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">
                                    <label for="base_url">
                                        <?php _e('Base URL:', 'auto-upload-images'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input type="text" name="base_url" value="<?php echo esc_url(self::getOption('base_url')); ?>" class="regular-text" dir="ltr" />
                                    <p class="description"><?php _e('If you need to choose a new base URL for the images that will be automatically uploaded. Ex:', 'auto-upload-images'); ?> <code>https://iranimij.com</code>, , <code>/</code></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <label for="image_name">
                                        <?php _e('Image Name:', 'auto-upload-images'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input type="text" name="image_name" value="<?php echo esc_html(self::getOption('image_name')); ?>" class="regular-text" dir="ltr" />
                                    <p class="description">
                                        <?php printf(__('Choose a custom filename for the new images will be uploaded. You can also use these shortcodes %s.', 'auto-upload-images'), '<code dir="ltr">%filename%</code>, <code dir="ltr">%image_alt%</code>, <code dir="ltr">%url%</code>, <code dir="ltr">%date%</code>, <code dir="ltr">%year%</code>, <code dir="ltr">%month%</code>, <code dir="ltr">%day%</code>, <code dir="ltr">%random%</code>, <code dir="ltr">%timestamp%</code>, <code dir="ltr">%postname%</code>, <code dir="ltr">%post_id%</code>') ?>
                                    </p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <label for="alt_name">
                                        <?php _e('Alt Name:', 'auto-upload-images'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input type="text" name="alt_name" value="<?php echo esc_html(self::getOption('alt_name')); ?>" class="regular-text" dir="ltr" />
                                    <p class="description">
                                        <?php printf(__('Choose a custom alt name for the new images will be uploaded. You can also use these shortcodes %s.', 'auto-upload-images'), '<code dir="ltr">%filename%</code>, <code dir="ltr">%image_alt%</code>, <code dir="ltr">%url%</code>, <code dir="ltr">%date%</code>, <code dir="ltr">%year%</code>, <code dir="ltr">%month%</code>, <code dir="ltr">%day%</code>, <code dir="ltr">%random%</code>, <code dir="ltr">%timestamp%</code>, <code dir="ltr">%postname%</code>, <code dir="ltr">%post_id%</code>') ?>
                                    </p>
                                </td>
                            </tr>
                            <?php if (function_exists('image_make_intermediate_size')) : ?>
                                <?php $editor_supports = wp_image_editor_supports(); ?>
                                <tr valign="top" <?= !$editor_supports ? 'style="background-color:#dedede;color:#6d6d6d;opacity:.8;"' : '' ?>>
                                    <th scope="row">
                                        <label <?= !$editor_supports ? 'style="color:#6d6d6d;"' : '' ?>><?php _e('Image Size:', 'auto-upload-images'); ?></label>
                                        <?php if (!$editor_supports) : ?>
                                        <small style="color:#6d6d6d;"><?php _e('(Inactive)', 'auto-upload-images') ?></small>
                                        <?php endif; ?>
                                    </th>
                                    <td>
                                        <label for="max_width"><?php _e('Max Width', 'auto-upload-images'); ?></label>
                                        <input name="max_width" type="number" step="5" min="0" id="max_width" placeholder="600" class="small-text" value="<?php echo esc_html(self::getOption('max_width')); ?>" <?php echo !$editor_supports ? 'disabled' : '' ?>>
                                        <label for="max_height"><?php _e('Max Height', 'auto-upload-images'); ?></label>
                                        <input name="max_height" type="number" step="5" min="0" id="max_height" placeholder="400" class="small-text" value="<?php echo esc_html(self::getOption('max_height')); ?>" <?php echo !$editor_supports ? 'disabled' : '' ?>>
                                        <p class="description"><?php _e('You can choose max width and height for images uploaded by this plugin on your site. If you leave empty each one of fields by default use the original size of the image.', 'auto-upload-images'); ?></p>
                                        <?php if (!$editor_supports) : ?>
                                        <p style="color:#535353;font-weight: bold;"><?php _e('To activate this feature please enable Gd or Imagick extensions of PHP.', 'auto-upload-images') ?></p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <tr valign="top">
                                <th scope="row">
                                    <label for="exclude_post_types">
                                        <?php _e('Exclude Post Types:', 'auto-upload-images'); ?>
                                    </label>
                                </th>
                                <td>
                                    <p>
                                        <?php $excludePostTypes = self::getOption('exclude_post_types'); ?>
                                        <?php foreach (get_post_types() as $post_type): ?>
                                            <label>
                                                <input type="checkbox" name="exclude_post_types[]" value="<?php echo $post_type ?>" <?php echo is_array($excludePostTypes) && in_array($post_type, $excludePostTypes, true) ? 'checked' : ''; ?>> <?php echo $post_type ?>
                                                <br>
                                            </label>
                                        <?php endforeach; ?>
                                    </p>
                                    <p class="description"><?php _e('Select the Post Types that you want exclude from automatic uploading', 'auto-upload-images'); ?></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <label for="exclude_urls">
                                        <?php _e('Exclude Domains:', 'auto-upload-images'); ?>
                                    </label>
                                </th>
                                <td>
                                    <p><?php _e('Enter the domains you wish to be excluded from uploading images: (One domain per line)', 'auto-upload-images'); ?></p>
                                    <p><textarea name="exclude_urls" rows="10" cols="50" id="exclude_urls" class="large-text code" placeholder="https://iranimij.com"><?php echo self::getOption('exclude_urls'); ?></textarea></p>
                                </td>
                            </tr>
                        </table>
                        <p class="submit">
                            <?php submit_button(null, 'primary', 'submit', false); ?>
                            <?php submit_button(__('Reset Options', 'auto-upload-images'), 'small', 'reset', false, array(
                                'onclick' => 'return confirm("'. __('Are you sure to reset all options to defaults?', 'auto-upload-images') .'");'
                            )) ?>
                        </p>
                    </form>
                </div>
            </div>
            <div id="postbox-container-1" class="postbox-container">

            </div>
        </div>
        <br class="clear">
    </div>
</div>
