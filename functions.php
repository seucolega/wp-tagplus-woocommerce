<?php


date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_MONETARY, 'pt_BR');


// $showErrors = !is_admin()
//     && function_exists('userIsSuperUser') && userIsSuperUser();
// $showErrors = true;
// ini_set('display_startup_errors', $showErrors ? 1 : 0);
// ini_set('display_errors', $showErrors ? 1 : 0);
// error_reporting($showErrors ? -1 : 1);

ini_set('max_execution_time', 600);
// ini_set('max_input_time', 300);


define('BRV_PLUGIN_PATH', __DIR__);


/*
 * PHP Sessions
 * https://silvermapleweb.com/using-the-php-session-in-wordpress/
 */

add_action('init', 'brvStartSession', 1);
add_action('wp_logout', 'brvEndSession');
add_action('wp_login', 'brvEndSession');

function brvStartSession()
{
    if (!session_id()) {
        session_start();
    }
}

function brvEndSession()
{
    session_destroy();
}


/*
 * Composer autoload
 */

require_once __DIR__ . '/vendor/autoload.php';


/*
 * TypeRocket
 */
require 'typerocket/init.php';


/*
 * My Autoload
 */

$files = glob(__DIR__ . '/autoload/*.php');
if ($files) {
    foreach ($files as $file) {
        /* @noinspection PhpIncludeInspection */
        include $file;
    }
}

// if ($showErrors) {
//     $showErrors = false;
//     ini_set('display_startup_errors', $showErrors ? 1 : 0);
//     ini_set('display_errors', $showErrors ? 1 : 0);
//     error_reporting($showErrors ? -1 : 1);
// }
