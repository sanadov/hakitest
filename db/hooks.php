<?php
defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'hookname' => \core\hook\output\before_footer_html_generation::class,
        'callback' => \local_activitychat\observer::class . '::inject_chat_interface',
    ],
];