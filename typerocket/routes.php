<?php
/*
|--------------------------------------------------------------------------
| TypeRocket Routes
|--------------------------------------------------------------------------
|
| Manage your web routes here.
|
*/

add_filter(
    'attachment_link',
    function () {
        return;
    }
);

$homeUrl = function_exists('pll_home_url') ? pll_home_url() : home_url();
$root = parse_url($homeUrl, PHP_URL_PATH);
$root .= '/' . \App\Models\Config::WEBHOOK_ROUTE;

/**
 * Rotas públicas
 */

tr_route()->post("$root/webhook", 'webhook@Webhook');

// if ($_SERVER['SERVER_NAME'] == 'localhost') {
//     flush_rewrite_rules(true);
// }
