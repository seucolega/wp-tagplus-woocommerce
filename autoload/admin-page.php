<?php

if (!function_exists('is_admin') || !is_admin()) {
    return;
}

add_action(
    'admin_menu',
    function () {
        add_menu_page(
            \App\Models\Config::NAME,
            'TagPlus',
            'manage_options',
            'tagplus-integracao',
            \App\Models\Config::PREFIX . 'plugin_admin_page_init'
        );
    }
);

function brv_plugin_admin_page_init()
{
    include BRV_PLUGIN_PATH . '/parts/admin-page.php';
}
