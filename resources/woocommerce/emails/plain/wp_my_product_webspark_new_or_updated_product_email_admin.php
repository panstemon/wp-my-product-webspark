<?php
if (!defined('ABSPATH')) {
    exit;
}

echo "==== " . esc_html($email_heading) . " ====\n\n";

echo sprintf(
    __('New or updated product: %s', 'wp-my-product-webspark'),
    esc_html($product_post->post_title)
) . "\n\n";

echo __('Author profile link:', 'wp-my-product-webspark') . "\n";
echo esc_url($product_author_link) . "\n\n";

echo __('Edit product link:', 'wp-my-product-webspark') . "\n";
echo esc_url($product_edit_link) . "\n\n";

echo "----------------------------------------\n\n";

do_action('woocommerce_email_footer', $email);
