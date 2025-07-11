<?php
defined('MOODLE_INTERNAL') || die();

$functions = array(
    'local_activitychat_send_message' => array(
        'classname'   => 'local_activitychat\external\send_message',
        'methodname'  => 'execute',
        'description' => 'Send a chat message to external service and get a response.',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'local/activitychat:use',
    ),
    'local_activitychat_start_session' => array(
        'classname'   => 'local_activitychat\external\start_session',
        'methodname'  => 'execute',
        'description' => 'Start a new chat session.',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities'=> 'local/activitychat:use',
    ),
);

$services = array(
    'Activity Chat Service' => array(
        'functions' => array(
            'local_activitychat_send_message',
            'local_activitychat_start_session'
        ),
        'restrictedusers' => 0,
        'enabled' => 1,
    ),
);