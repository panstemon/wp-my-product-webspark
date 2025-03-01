<?php

namespace WPMyProductWebspark\Core;

class Localization {

    /**
     * @action init
     */
    private function loadTextDomain(): void {
        load_plugin_textdomain('wp-my-product-webspark', false, WPMPW_BASEDIR . '/languages');
    }
}