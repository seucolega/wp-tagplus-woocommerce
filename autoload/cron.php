<?php

add_filter(
    'cron_schedules',
    function ($schedules) {
        $schedules[\App\Models\Config::PREFIX . 'cron_interval'] = [
            'interval' => \App\Models\Config::CRON_INTERVAL,
            'display' => \App\Models\Config::NAME,
        ];
        return $schedules;
    }
);

register_activation_hook(
    __FILE__,
    function () {
        wp_schedule_event(
            time(),
            \App\Models\Config::PREFIX . 'cron_interval',
            \App\Models\Config::PREFIX . 'cron'
        );
    }
);

register_deactivation_hook(
    __FILE__,
    function () {
        wp_clear_scheduled_hook(\App\Models\Config::PREFIX . 'cron');

        // delete_option(\App\Models\Config::PREFIX . '');
    }
);
