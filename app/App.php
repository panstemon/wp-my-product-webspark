<?php

namespace WPMyProductWebspark;

use WPMyProductWebspark\Core\RequirementsChecker;
use WPMyProductWebspark\Core\Config;
use WPMyProductWebspark\Core\Hooks;
use WPMyProductWebspark\Core\Localization;
use WPMyProductWebspark\Integrations\Integrations;

class App
{
    private Config $config;
    private Integrations $integrations;
    private Localization $localization;
    private RequirementsChecker $requirementsChecker;

    private static ?App $instance = null;

    private function __construct()
    {   
        $this->config = Hooks::init(new Config());
        $this->localization = Hooks::init(new Localization());

        $this->requirementsChecker = Hooks::init(new RequirementsChecker());
        if (!$this->requirementsChecker()->check()) {
            $this->destroy();
            return;
        }

        $this->integrations = Hooks::init(new Integrations());
    }

    public function config(): Config
    {
        return $this->config;
    }

    public function integrations(): Integrations
    {
        return $this->integrations;
    }

    public function localization(): Localization
    {
        return $this->localization;
    }

    public function requirementsChecker(): RequirementsChecker
    {
        return $this->requirementsChecker;
    }

    public function onActivation(): void {
        do_action('wpmpw/activation');
    }

    public function onDeactivation(): void {
        do_action('wpmpw/deactivation');
    }

    private function __clone()
    {
    }

    public function __wakeup(): never
    {
        throw new \Exception('Cannot unserialize a singleton.');
    }

    private function destroy(): void {
        if (!function_exists('deactivate_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        deactivate_plugins(WPMPW_BASENAME);
    }

    public static function get(): App
    {
        if (empty(self::$instance)) {
            self::$instance = new App();
        }

        return self::$instance;
    }
}
