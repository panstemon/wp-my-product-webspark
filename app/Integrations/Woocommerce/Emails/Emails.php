<?php

namespace WPMyProductWebspark\Integrations\Woocommerce\Emails;

class Emails {

    /**
     * @action woocommerce_email_classes
     */
    private function registerEmails( $email_classes): array {
        if ( ! isset( $email_classes['wp_my_product_webspark_new_or_updated_product_email_admin'] ) ) {
            $email_classes['wp_my_product_webspark_new_or_updated_product_email_admin'] = new NewOrUpdatedProductAdminEmail();
        }
        return $email_classes;
    }
}