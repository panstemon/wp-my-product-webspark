<?php

namespace WPMyProductWebspark\Utils;

class Notices
{
    public static function showAdminNotice(string $message, string $type = 'error'): void
    {
        $hookName = is_multisite() ? 'network_admin_notices' : 'admin_notices';

        add_action($hookName, function () use ($message, $type) {
            echo '<div class="wsa-notice notice notice-' . esc_attr($type) . '"><p>' . wp_kses_post($message) . '</p></div>';
        });
    }
}