<?php
namespace local_activitychat\external;

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use context_module;

defined('MOODLE_INTERNAL') || die();

require_once($GLOBALS['CFG']->libdir . '/externallib.php');

class start_session extends external_api {

    public static function execute_parameters() {
        return new external_function_parameters([
            'contextid' => new external_value(PARAM_INT, 'The context ID of the activity'),
        ]);
    }

    public static function execute($contextid) {
        global $USER;

        $params = self::validate_parameters(self::execute_parameters(), [
            'contextid' => $contextid,
        ]);

        $context = context_module::instance($params['contextid']);
        self::validate_context($context);
        require_capability('local/activitychat:use', $context);

        // Using a more robust random session ID.
        $sessionid = 'chat_' . bin2hex(random_bytes(16));

        return [
            'sessionid' => $sessionid,
            'userid'    => $USER->id,
            'username'  => fullname($USER),
        ];
    }

    public static function execute_returns() {
        return new external_single_structure([
            'sessionid' => new external_value(PARAM_ALPHANUMEXT, 'New session ID'),
            'userid'    => new external_value(PARAM_INT, 'Current Moodle user ID'),
            'username'  => new external_value(PARAM_TEXT, 'Current user full name'),
        ]);
    }
}