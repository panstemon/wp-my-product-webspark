<?php

namespace WPMyProductWebspark\Core;

class Config
{
    private array $config = [];

    public function __construct()
    {
        $this->config = [
            'version' => wp_get_environment_type() === 'development' ? time() : WPMPW_VERSION,
            'env' => [
                'type' => wp_get_environment_type(),
                'mode' => 'plugin',
            ],
            'resources' => [
                'path' => WPMPW_PATH . '/resources',
            ],
            'woocommerce' => [
                'templates' => [
                    'path' => WPMPW_PATH . '/resources/woocommerce'
                ]
            ]
        ];
    }

    public function get(string $key): mixed
    {
        $value = $this->config;

        foreach (explode('.', $key) as $key) {
            if (isset($value[$key])) {
                $value = $value[$key];
            } else {
                return null;
            }
        }

        return $value;
    }
}
