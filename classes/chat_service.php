<?php
namespace local_activitychat;

use core\http\client;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

class chat_service {

    private $api_endpoint;
    private $api_key;

    public function __construct() {
        $this->api_endpoint = get_config('local_activitychat', 'api_endpoint');
        $this->api_key = get_config('local_activitychat', 'api_key');
    }

    public function send_message($message, $sessionid, $userid, $context) {
        global $DB;

        $cm = get_coursemodule_from_id('', $context->instanceid, 0, true);
        if (!$cm) {
            throw new moodle_exception('error:invalidcoursemodule');
        }

        $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);

        // ADDED: Logic to fetch detailed activity content.
        $activitycontent = $this->get_activity_content($cm);

        $contextdata = [
            'course_name'    => $course->fullname,
            'activity_name'  => $cm->name,
            'activity_type'  => $cm->modname,
            'activity_url'   => (new \moodle_url('/mod/' . $cm->modname . '/view.php', ['id' => $cm->id]))->out(true),
            'activity_content' => $activitycontent,
        ];

        $payload = [
            'message'    => clean_text($message),
            'session_id' => $sessionid,
            'user_id'    => $userid,
            'context'    => $contextdata,
            'timestamp'  => time(),
        ];

        $response = $this->make_api_request($payload);

        return [
            'success' => $response !== false,
            'message' => $response ?: get_string('error_service_unavailable', 'local_activitychat'),
        ];
    }

    // ADDED: New function to get content from common module types.
    private function get_activity_content(\cm_info $cm) {
        global $DB;

        $content = '';
        $modrecord = $DB->get_record($cm->modname, ['id' => $cm->instance]);
        if (!$modrecord) {
            return '';
        }

        // Add cases for different module types you want to support.
        switch ($cm->modname) {
            case 'page':
                if (isset($modrecord->content)) {
                    $content = format_text_for_display($modrecord->content, $modrecord->contentformat);
                }
                break;
            case 'assign':
            case 'quiz':
            case 'forum':
            case 'lesson':
                if (isset($modrecord->intro)) {
                    $content = format_text_for_display($modrecord->intro, $modrecord->introformat);
                }
                break;
        }

        // Strip HTML tags for a cleaner payload. You can adjust this as needed.
        return strip_tags($content);
    }

    // CHANGED: Replaced cURL with Moodle's core\http\client.
    private function make_api_request($payload) {
        try {
            $client = new client();
            $options = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->api_key,
                ],
                'body' => json_encode($payload),
                'timeout' => 30, // 30-second timeout.
            ];

            $response = $client->post($this->api_endpoint, $options);
            $decoded = json_decode($response, true);

            // Assuming your service returns a JSON object like {"message": "..."}
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['message'])) {
                return $decoded['message'];
            }
            return get_string('error_invalid_response', 'local_activitychat');

        } catch (\Exception $e) {
            // Log the actual error for debugging by the administrator.
            error_log('Activity Chat API Error: ' . $e->getMessage());
            return false;
        }
    }
}