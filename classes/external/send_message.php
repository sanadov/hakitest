<?php
namespace local_activitychat\external;

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use context_module;
use local_activitychat\chat_service;

defined('MOODLE_INTERNAL') || die();

require_once($GLOBALS['CFG']->libdir . '/externallib.php');

class send_message extends external_api {

    public static function execute_parameters() {
        return new external_function_parameters([
            'message'   => new external_value(PARAM_TEXT, 'The chat message'),
            'sessionid' => new external_value(PARAM_ALPHANUMEXT, 'Session ID'),
            'contextid' => new external_value(PARAM_INT, 'The context ID of the activity'),
        ]);
    }

    public static function execute($message, $sessionid, $contextid) {
        global $USER;

        $params = self::validate_parameters(self::execute_parameters(), [
            'message' => $message,
            'sessionid' => $sessionid,
            'contextid' => $contextid,
        ]);

        $context = context_module::instance($params['contextid']);
        self::validate_context($context);
        require_capability('local/activitychat:use', $context);

        $chatservice = new chat_service();
        $response = $chatservice->send_message(
            $params['message'],
            $params['sessionid'],
            $USER->id,
            $context
        );

        return [
            'success' => $response['success'],
            'message' => $response['message'],
            'timestamp' => time(),
        ];
    }

    public static function execute_returns() {
        return new external_single_structure([
            'success'   => new external_value(PARAM_BOOL, 'Whether the message was sent successfully'),
            'message'   => new external_value(PARAM_RAW, 'Response message from external service'),
            'timestamp' => new external_value(PARAM_INT, 'Response timestamp'),
        ]);
    }
}