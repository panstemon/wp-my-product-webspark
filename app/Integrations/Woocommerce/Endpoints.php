<?php

namespace WPMyProductWebspark\Integrations\Woocommerce;

use WPMyProductWebspark\Utils\ArrayUtils;

class Endpoints
{
    /**
     * @filter woocommerce_account_menu_items
     */
    private function addMyAccountMenuItems($items)
    {
        $items = ArrayUtils::insertAfter($items, 'orders', [
            'my-products' => __('My Products', 'wp-my-product-webspark'),
            'add-product' => __('Add Product', 'wp-my-product-webspark')
        ]);

        return $items;
    }

    /**
     * @action init
     * @action wpmpw/activation 5
     */
    private function addRewriteRules()
    {
        $my_account_base = wpmpw()->integrations()->woocommerce()->getMyAccountBase();

        add_rewrite_endpoint('my-products', EP_PAGES);
        add_rewrite_endpoint('add-product', EP_PAGES);

        add_rewrite_rule(
            '^' . $my_account_base . '/my-products/page/([0-9]+)/?$',
            'index.php?pagename=' . $my_account_base . '&my-products=1&paged=$matches[1]',
            'top'
        );
    }

    /**
     * @filter query_vars
     */
    private function addQueryVars($vars)
    {
        $vars[] = 'my-products';
        $vars[] = 'add-product';
        $vars[] = 'paged';

        return $vars;
    }

    /**
     * @action woocommerce_get_query_vars
     */
    private function addWoocommerceQueryVars($query_vars)
    {
        $query_vars['my-products'] = 'my-products';
        $query_vars['add-product'] = 'add-product';
        $query_vars['paged'] = 'paged';

        return $query_vars;
    }

    /**
     * @action wp_enqueue_scripts
     */
    private function enqueueScripts()
    {
        if (is_account_page()) {
            wp_enqueue_media();
        }
    }

    /**
     * @action woocommerce_account_my-products_endpoint
     */
    private function endpointMyProducts()
    {
        $user_id = get_current_user_id();

        $paged = max(1, get_query_var('paged'), get_query_var('page'), get_query_var('pagination'));
        $perpage = 5;

        $args = [
            'post_type' => 'product',
            'post_status' => ['publish', 'pending', 'draft'],
            'author' => $user_id,
            'posts_per_page' => $perpage,
            'paged' => $paged,
            'orderby' => 'date',
            'order' => 'DESC',
        ];

        $query = new \WP_Query($args);

        ?>
        <h2><?php _e('My Products', 'wp-my-product-webspark'); ?></h2>

        <?php if ($query->have_posts()): ?>
            <table class="shop_table shop_table_responsive">
                <thead>
                    <tr>
                        <th><?php _e('Product Name', 'wp-my-product-webspark'); ?></th>
                        <th><?php _e('Quantity', 'wp-my-product-webspark'); ?></th>
                        <th><?php _e('Price', 'wp-my-product-webspark'); ?></th>
                        <th><?php _e('Status', 'wp-my-product-webspark'); ?></th>
                        <th><?php _e('Actions', 'wp-my-product-webspark'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($query->have_posts()):
                        $query->the_post();
                        $product_id = get_the_ID();
                        $price = get_post_meta($product_id, '_price', true);
                        $stock = get_post_meta($product_id, '_stock', true);
                        $status = get_post_status($product_id);
                        ?>
                        <tr>
                            <td data-title="<?php _e('Product Name', 'wp-my-product-webspark'); ?>">
                                <?php echo esc_html(get_the_title()); ?>
                            </td>
                            <td data-title="<?php _e('Quantity', 'wp-my-product-webspark'); ?>">
                                <?php echo esc_html($stock); ?>
                            </td>
                            <td data-title="<?php _e('Price', 'wp-my-product-webspark'); ?>">
                                <?php echo wc_price($price); ?>
                            </td>
                            <td data-title="<?php _e('Status', 'wp-my-product-webspark'); ?>">
                                <?php echo esc_html($status); ?>
                            </td>
                            <td data-title="<?php _e('Actions', 'wp-my-product-webspark'); ?>">
                                <a class="button"
                                    href="<?php echo esc_url(wc_get_account_endpoint_url('add-product') . '?edit=' . $product_id); ?>">
                                    <?php _e('Edit', 'wp-my-product-webspark'); ?>
                                </a>
                                <form method="POST" style="display:inline-block;">
                                    <?php wp_nonce_field('wpmpw_product_nonce'); ?>
                                    <input type="hidden" name="wpmpw_action" value="delete_product" />
                                    <input type="hidden" name="wpmpw_product_id" value="<?php echo $product_id; ?>" />
                                    <button type="submit" class="button button-danger"
                                        onclick="return confirm('<?php esc_attr_e('Are you sure to delete this product?', 'wp-my-product-webspark'); ?>');">
                                        <?php _e('Delete', 'wp-my-product-webspark'); ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php
                    endwhile;
                    ?>
                </tbody>
            </table>

            <?php
            $total_pages = $query->max_num_pages;
            if ($total_pages > 1) {
                $current_page = $paged;
                echo paginate_links(array(
                    'base' => user_trailingslashit(wc_get_account_endpoint_url('my-products') . 'page/%#%/'),
                    'format' => '',
                    'current' => $current_page,
                    'total' => $total_pages,
                    'prev_text' => __('Prev', 'wp-my-product-webspark'),
                    'next_text' => __('Next', 'wp-my-product-webspark'),
                ));
            }
            wp_reset_postdata();
        else:
            ?>
            <p><?php _e('No products found.', 'wp-my-product-webspark'); ?></p>
        <?php endif; ?>
    <?php
    }

    /**
     * @action wpmpw/activation
     * @action wpmpw/deactivation
     */
    private function flushRewriteRules() {
        flush_rewrite_rules();
    }

    /**
     * @action woocommerce_account_add-product_endpoint
     */
    private function endpointEditProduct()
    {
        $editing_product_id = isset($_GET['edit']) ? absint($_GET['edit']) : 0;
        $is_edit = $editing_product_id > 0;

        $current_user_id = get_current_user_id();

        if ($is_edit) {
            $post_author = (int) get_post_field('post_author', $editing_product_id);
            if ($post_author !== $current_user_id) {
                $editing_product_id = 0;
                $is_edit = false;
            }
        }

        $product_name = '';
        $product_price = '';
        $product_qty = '';
        $description = '';
        $image_id = 0;

        if ($is_edit) {
            $product_name = get_the_title($editing_product_id);
            $product_price = get_post_meta($editing_product_id, '_price', true);
            $product_qty = get_post_meta($editing_product_id, '_stock', true);
            $description = get_post_field('post_content', $editing_product_id);
            $image_id = get_post_thumbnail_id($editing_product_id);
        }

        ?>
        <h2>
            <?php echo $is_edit ? __('Edit Product', 'wp-my-product-webspark') : __('Add Product', 'wp-my-product-webspark'); ?>
        </h2>

        <form method="POST">
            <?php wp_nonce_field('wpmpw_product_nonce'); ?>

            <p>
                <label for="wpmpw_product_name"><?php _e('Product Name', 'wp-my-product-webspark'); ?></label><br />
                <input type="text" name="wpmpw_product_name" id="wpmpw_product_name"
                    value="<?php echo esc_attr($product_name); ?>" required />
            </p>
            <p>
                <label for="wpmpw_product_price"><?php _e('Price', 'wp-my-product-webspark'); ?></label><br />
                <input type="number" step="0.01" name="wpmpw_product_price" id="wpmpw_product_price"
                    value="<?php echo esc_attr($product_price); ?>" required />
            </p>
            <p>
                <label for="wpmpw_product_qty"><?php _e('Quantity', 'wp-my-product-webspark'); ?></label><br />
                <input type="number" name="wpmpw_product_qty" id="wpmpw_product_qty"
                    value="<?php echo esc_attr($product_qty); ?>" required />
            </p>

            <p>
                <label
                    for="wpmpw_product_description"><?php _e('Product Description', 'wp-my-product-webspark'); ?></label><br />
                <?php
                wp_editor(
                    $description,
                    'wpmpw_product_description',
                    array(
                        'textarea_name' => 'wpmpw_product_description',
                        'media_buttons' => false,
                        'textarea_rows' => 6,
                    )
                );
                ?>
            </p>

            <p>
                <label><?php _e('Product Image', 'wp-my-product-webspark'); ?></label><br />
                <input type="hidden" name="wpmpw_product_image_id" id="wpmpw_product_image_id"
                    value="<?php echo esc_attr($image_id); ?>" />
                <button type="button" class="button" id="wpmpw_upload_image_button">
                    <?php echo $image_id ? __('Change Image', 'wp-my-product-webspark') : __('Select Image', 'wp-my-product-webspark'); ?>
                </button>
                <button type="button" class="button button-secondary" id="wpmpw_remove_image_button" <?php echo !$image_id ? 'style="display:none;"' : ''; ?>>
                    <?php _e('Remove Image', 'wp-my-product-webspark'); ?>
                </button>
            <div id="wpmpw_image_preview" style="margin-top:10px;">
                <?php
                if ($image_id) {
                    echo wp_get_attachment_image($image_id, 'thumbnail');
                }
                ?>
            </div>
            </p>

            <p>
                <input type="hidden" name="wpmpw_action"
                    value="<?php echo $is_edit ? 'update_product' : 'create_product'; ?>" />
                <?php if ($is_edit): ?>
                    <input type="hidden" name="wpmpw_product_id" value="<?php echo $editing_product_id; ?>" />
                <?php endif; ?>
                <button type="submit" class="button button-primary">
                    <?php echo $is_edit ? __('Update Product', 'wp-my-product-webspark') : __('Add Product', 'wp-my-product-webspark'); ?>
                </button>
            </p>
        </form>

        <script>
            jQuery(document).ready(function ($) {
                var frame;
                $('#wpmpw_upload_image_button').on('click', function (e) {
                    e.preventDefault();
                    if (frame) {
                        frame.open();
                        return;
                    }
                    frame = wp.media({
                        title: '<?php _e('Select or Upload an Image', 'wp-my-product-webspark'); ?>',
                        button: { text: '<?php _e('Use this image', 'wp-my-product-webspark'); ?>' },
                        multiple: false
                    });

                    frame.on('select', function () {
                        var attachment = frame.state().get('selection').first().toJSON();
                        $('#wpmpw_product_image_id').val(attachment.id);
                        $('#wpmpw_image_preview').html('<img src="' + attachment.sizes.thumbnail.url + '"/>');
                        $('#wpmpw_remove_image_button').show();
                    });

                    frame.open();
                });

                $('#wpmpw_remove_image_button').on('click', function (e) {
                    e.preventDefault();
                    $('#wpmpw_product_image_id').val('');
                    $('#wpmpw_image_preview').html('');
                    $(this).hide();
                });
            });
        </script>
        <?php
    }
}