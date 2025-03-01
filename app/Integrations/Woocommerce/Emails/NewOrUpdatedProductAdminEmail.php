<?php

namespace WPMyProductWebspark\Integrations\Woocommerce\Emails;

class NewOrUpdatedProductAdminEmail extends \WC_Email
{
    public function __construct()
    {
        $this->id = 'wp_my_product_webspark_new_or_updated_product_email_admin';
        $this->title = __('New/Updated Product (Custom)', 'wp-my-product-webspark');
        $this->description = __('Sent when a user creates or edits a product through My Account.', 'wp-my-product-webspark');

        $this->template_html = 'emails/wp_my_product_webspark_new_or_updated_product_email_admin.php';
        $this->template_plain = 'emails/plain/wp_my_product_webspark_new_or_updated_product_email_admin.php';
        $this->template_base = wpmpw()->config()->get('woocommerce.templates.path') . '/';

        $this->subject = __('Product [[{product_name}]] - pending review', 'wp-my-product-webspark');
        $this->heading = __('New/Updated Product', 'wp-my-product-webspark');

        $this->init_form_fields();
        $this->init_settings();

        $this->enabled = $this->get_option('enabled');
        $this->subject = $this->get_option('subject');
        $this->heading = $this->get_option('heading');

        $this->recipient = get_option('admin_email');


        parent::__construct();
    }
    
    
    public function get_content_type($default_content_type = '') {
        return 'text/html';
    }

    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'wp-my-product-webspark'),
                'type' => 'checkbox',
                'label' => __('Enable this email notification', 'wp-my-product-webspark'),
                'default' => 'yes',
            ),
            'subject' => array(
                'title' => __('Subject', 'wp-my-product-webspark'),
                'type' => 'text',
                'description' => __('This controls the email subject line.', 'wp-my-product-webspark'),
                'placeholder' => '',
                'default' => __('Product [[{product_name}]] - pending review', 'wp-my-product-webspark'),
            ),
            'heading' => array(
                'title' => __('Email Heading', 'wp-my-product-webspark'),
                'type' => 'text',
                'description' => __('This controls the main heading contained in the email.', 'wp-my-product-webspark'),
                'placeholder' => '',
                'default' => __('New/Updated Product', 'wp-my-product-webspark'),
            ),
        );
    }

    public function trigger($product_id)
    {
        if (!$this->is_enabled()) {
            return;
        }

        $this->setup_locale();

        $this->object = get_post($product_id);

        if ($this->object) {
            $product_title = $this->object->post_title;
            $product_author = $this->object->post_author;

            $this->placeholders['{product_name}'] = $product_title;

            $this->product_author_link = admin_url('user-edit.php?user_id=' . $product_author);
            $this->product_edit_link = admin_url('post.php?post=' . $product_id . '&action=edit');
        }

        $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());

        $this->restore_locale();
    }

    public function get_content_html()
    {
        ob_start();
        wc_get_template(
            $this->template_html,
            array(
                'email_heading' => $this->get_heading(),
                'product_post' => $this->object,
                'product_author_link' => $this->product_author_link,
                'product_edit_link' => $this->product_edit_link,
                'email' => $this,
            ),
            '',
            $this->template_base
        );
        return ob_get_clean();
    }

    public function get_content_plain()
    {
        ob_start();
        wc_get_template(
            $this->template_plain,
            array(
                'email_heading' => $this->get_heading(),
                'product_post' => $this->object,
                'product_author_link' => $this->product_author_link,
                'product_edit_link' => $this->product_edit_link,
                'email' => $this,
            ),
            '',
            $this->template_base
        );
        return ob_get_clean();
    }
}