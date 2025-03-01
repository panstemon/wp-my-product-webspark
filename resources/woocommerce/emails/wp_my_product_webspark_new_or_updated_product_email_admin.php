<?php
if (!defined('ABSPATH')) {
    exit; 
}

do_action('woocommerce_email_header', $email_heading, $email);
?>

<p>
    <?php
    printf(
        __('New or updated product: <strong>%s</strong>', 'wp-my-product-webspark'),
        esc_html($product_post->post_title)
    );
    ?>
</p>

<p>
    <?php _e('Author profile link:', 'wp-my-product-webspark'); ?><br />
    <a href="<?php echo esc_url($product_author_link); ?>">
        <?php echo esc_html($product_author_link); ?>
    </a>
</p>

<p>
    <?php _e('Edit product link:', 'wp-my-product-webspark'); ?><br />
    <a href="<?php echo esc_url($product_edit_link); ?>">
        <?php echo esc_html($product_edit_link); ?>
    </a>
</p>

<?php do_action('woocommerce_email_footer', $email); ?>