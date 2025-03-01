<?php

namespace WPMyProductWebspark\Core;

use WPMyProductWebspark\Utils\Notices;

class RequirementsChecker
{
    private bool $lowPHPVersion = false;
    private bool $lowWPVersion = false;
    private array $missingPlugins = [];

    public function check(): bool
    {
        return $this->checkPHPVersion() && $this->checkWPVersion() && $this->checkRequiredPlugins();
    }

    private function checkPHPVersion(): bool
    {
        if (version_compare(phpversion(), WPMPW_MIN_PHP_VERSION, '<')) {
            $this->lowPHPVersion = true;
            return false;
        }

        return true;
    }

    private function checkWPVersion(): bool
    {
        if (version_compare(get_bloginfo('version'), WPMPW_MIN_WP_VERSION, '<')) {
            $this->lowWPVersion = true;
            return false;
        }

        return true;
    }

    private function checkRequiredPlugins(): bool
    {
        $plugins = WPMPW_REQUIRED_PLUGINS;
        $active_plugins = apply_filters('active_plugins', get_option('active_plugins'));

        $valid = true;

        if (is_multisite()) {

            if (is_network_admin()) {

                $active_plugins = [];
                $active_sitewide_plugins = get_site_option('active_sitewide_plugins');

                foreach ($active_sitewide_plugins as $path => $item) {
                    $active_plugins[] = $path;
                }

            } else {

                $active_plugins = get_blog_option(get_current_blog_id(), 'active_plugins');
            }
        }

        foreach ($plugins as $path => $name) {
            if (!in_array($path, $active_plugins)) {
                $this->missingPlugins[$path] = $name;
                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * @action admin_init
     */
    private function showAdminNotices()
    {
        if ($this->lowPHPVersion) {
            Notices::showAdminNotice(sprintf(
                __('Your server must be running at least PHP %s. Please upgrade.', 'wp-my-product-webspark'),
                WPMPW_MIN_PHP_VERSION,
            ));
        }

        if ($this->lowWPVersion) {
            Notices::showAdminNotice(sprintf(
                __('Your site must be running at least WordPress %s. Please upgrade.', 'wp-my-product-webspark'),
                WPMPW_MIN_WP_VERSION,
            ));
        }

        foreach ($this->missingPlugins as $pluginName) {
            Notices::showAdminNotice(sprintf(
                __('%s requires %s plugin to be installed and active.', 'wp-my-product-webspark'),
                "<b>WP My Product Webspark</b>",
                "<b>{$pluginName}</b>"
            ), 'error');
        }
    }
}