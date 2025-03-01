<?php

namespace WPMyProductWebspark\Integrations\Woocommerce;

use WPMyProductWebspark\Integrations\Woocommerce\Emails\NewOrUpdatedProductAdminEmail;

class ProductCRUD
{
    /**
     * @action init
     */
    private function handleFormActions(): void
    {
        if (isset($_POST['wpmpw_action']) && !empty($_POST['wpmpw_action'])) {
            $action = sanitize_text_field($_POST['wpmpw_action']);

            switch ($action) {
                case 'create_product':
                    $this->createOrUpdateProduct();
                    break;
                case 'update_product':
                    $this->createOrUpdateProduct(true);
                    break;
                case 'delete_product':
                    $this->deleteProduct();
                    break;
                default:
                    break;
            }
        }
    }

    private function createOrUpdateProduct($is_update = false): void
    {
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'wpmpw_product_nonce')) {
            return;
        }

        if (!is_user_logged_in()) {
            return;
        }

        $current_user_id = get_current_user_id();

        $product_id = $is_update ? absint($_POST['wpmpw_product_id']) : 0;

        if ($is_update && $product_id) {
            $post_author = (int) get_post_field('post_author', $product_id);
            if ($post_author !== $current_user_id) {
                return;
            }
        }

        $product_name = sanitize_text_field($_POST['wpmpw_product_name'] ?? '');
        $product_price = floatval($_POST['wpmpw_product_price'] ?? 0);
        $product_qty = intval($_POST['wpmpw_product_qty'] ?? 0);
        $product_description = wp_kses_post($_POST['wpmpw_product_description'] ?? '');

        $post_data = [
            'ID' => $product_id,
            'post_title' => $product_name,
            'post_content' => $product_description,
            'post_status' => 'pending',
            'post_type' => 'product',
            'post_author' => $current_user_id,
        ];

        if ($is_update) {
            wp_update_post($post_data);
        } else {
            $product_id = wp_insert_post($post_data);
        }

        if (!$product_id) {
            return;
        }

        update_post_meta($product_id, '_regular_price', $product_price);
        update_post_meta($product_id, '_price', $product_price);
        update_post_meta($product_id, '_stock', $product_qty);
        update_post_meta($product_id, '_manage_stock', 'yes');

        if (!empty($_POST['wpmpw_product_image_id'])) {
            $attachment_id = absint($_POST['wpmpw_product_image_id']);
            set_post_thumbnail($product_id, $attachment_id);
        } else {
            delete_post_thumbnail($product_id);
        }

        $this->triggerNewOrUpdatedProductAdminEmail($product_id);

        wp_safe_redirect(wc_get_account_endpoint_url('my-products'));
        exit;
    }

    private function deleteProduct(): void
    {
        // Перевірка nonce
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'wpmpw_product_nonce')) {
            return;
        }

        if (!is_user_logged_in()) {
            return;
        }

        $current_user_id = get_current_user_id();
        $product_id = absint($_POST['wpmpw_product_id']);

        $post_author = (int) get_post_field('post_author', $product_id);
        if ($post_author === $current_user_id) {
            wp_trash_post($product_id);
        }

        wp_safe_redirect(wc_get_account_endpoint_url('my-products'));
        exit;
    }

    private function triggerNewOrUpdatedProductAdminEmail($product_id): void
    {
        $mailer = WC()->mailer();
        $emails = $mailer->get_emails();

        if (!empty($emails['wp_my_product_webspark_new_or_updated_product_email_admin'])) {
            /** @var NewOrUpdatedProductAdminEmail $admin_email */
            $admin_email = $emails['wp_my_product_webspark_new_or_updated_product_email_admin'];
            $admin_email->trigger($product_id);
        }
    }

    /**
     * @filter user_has_cap
     */
    private function temporarilyAllowUserUploads($allcaps, $caps, $args): mixed
    {

        if (isset($args[0]) && in_array($args[0], ['upload_files', 'edit_posts']) && wp_doing_ajax()) {
            $action = sanitize_text_field($_POST['action'] ?? '');
            $referer = wp_get_referer();
            $my_account_base = wpmpw()->integrations()->woocommerce()->getMyAccountBase();

            if (
                $referer && strpos($referer, $my_account_base . '/add-product') !== false &&
                ($action === 'query-attachments' || $action === 'upload-attachment' && check_ajax_referer('media-form', 'nonce', false))
            ) {
                $allcaps['upload_files'] = true;
                $allcaps['edit_posts'] = true;
            }
        }

        return $allcaps;
    }

    /**
     * @action ajax_query_attachments_args
     */
    private function filterMediaLibrary($query): mixed
    {
        if (!current_user_can('administrator')) {
            $query['author'] = get_current_user_id();
        }
        return $query;
    }
}