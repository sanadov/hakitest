<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_activitychat', get_string('pluginname', 'local_activitychat'));

    $settings->add(new admin_setting_configtext(
        'local_activitychat/api_endpoint',
        get_string('api_endpoint', 'local_activitychat'),
        get_string('api_endpoint_desc', 'local_activitychat'),
        '', // No default URL.
        PARAM_URL
    ));

    $settings->add(new admin_setting_configpasswordunmask(
        'local_activitychat/api_key',
        get_string('api_key', 'local_activitychat'),
        get_string('api_key_desc', 'local_activitychat'),
        ''
    ));

    $ADMIN->add('localplugins', $settings);
}