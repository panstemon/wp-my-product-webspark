<?php

namespace WPMyProductWebspark\Integrations\Woocommerce;

use WPMyProductWebspark\Integrations\Woocommerce\Endpoints;
use WPMyProductWebspark\Core\Hooks;
use WPMyProductWebspark\Integrations\Woocommerce\Emails\Emails;

class Woocommerce
{

    private Endpoints $endpoints;

    private Emails $emails;

    private ProductCRUD $productCRUD;

    public function __construct()
    {
        $this->endpoints = Hooks::init(new Endpoints());
        $this->emails = Hooks::init(new Emails());
        $this->productCRUD = Hooks::init(new ProductCRUD());
    }

    public function endpoints(): Endpoints
    {
        return $this->endpoints;
    }

    public function emails(): Emails
    {
        return $this->emails;
    }

    public function productCRUD(): ProductCRUD
    {
        return $this->productCRUD;
    }

    public function getMyAccountBase(): string
    {
        $account_page_id = get_option('woocommerce_myaccount_page_id');
        return $account_page_id ? get_post_field('post_name', $account_page_id) : 'my-account';
    }

    /**
     * @filter wc_get_template_part
     */
    private function getTemplatePart($template, $slug, $name): string
    {
        if ($name) {
            $path = wpmpw()->config()->get('woocommerce.templates.path') . "{$slug}-{$name}.php";
        } else {
            $path = wpmpw()->config()->get('woocommerce.templates.path') . "{$slug}.php";
        }
        return file_exists($path) ? $path : $template;
    }

    /**
     * @filter woocommerce_locate_template
     */
    private function locateTemplate($template, $template_name, $template_path): string
    {
        $path = wpmpw()->config()->get('woocommerce.templates.path') . '/' . $template_name;
        return file_exists($path) ? $path : $template;
    }
}