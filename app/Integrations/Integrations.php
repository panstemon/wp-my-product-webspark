<?php

namespace WPMyProductWebspark\Integrations;

use WPMyProductWebspark\Core\Hooks;
use WPMyProductWebspark\Integrations\Woocommerce\Woocommerce;

class Integrations
{
    private Woocommerce $woocommerce;

    public function __construct()
    {
        $this->woocommerce = Hooks::init(new Woocommerce());
    }

    /**
     * @action init
     */
    public function init()
    {
        if (wpmpw()->config()->get('hmr.active')) {
            Hooks::init(new Vite());
        }
    }

    public function woocommerce(): Woocommerce
    {
        return $this->woocommerce;
    }
}
