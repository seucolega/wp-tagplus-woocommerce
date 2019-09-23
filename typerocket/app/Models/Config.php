<?php
namespace App\Models;

class Config
{
    const NAME = 'TagPlus Integração';
    const PREFIX = 'brv_';
    const WEBHOOK_ROUTE = 'wc_tp';
    const MENU_PAGE_SLUG = 'tagplus-integracao';
    const CRON_INTERVAL = 30 * 60;
    public $menuPageUrl;

    function __construct()
    {
        $this->menuPageUrl = get_admin_url(
            null,
            'admin.php?page=' . 'tagplus-integracao'
        );
    }

    static function getOption($optionName)
    {
        return get_option(self::PREFIX . $optionName);
    }

    static function setOption($optionName, $optionValue)
    {
        update_option(self::PREFIX . $optionName, $optionValue);
    }
}
