<?php
namespace local_activitychat;

defined('MOODLE_INTERNAL') || die();

class observer {
    /**
     * This is a test function to see if Moodle calls this hook.
     * It will stop the entire page from loading if it works.
     */
    public static function inject_chat_interface(\core\hook\output\before_footer_html_generation $hook): void {
        echo '<h1>--- THE OBSERVER IS WORKING ---</h1>';
        die();
    }
}
//
//
//namespace local_activitychat;
//
//defined('MOODLE_INTERNAL') || die();
//
//class observer
//{
//
//    /**
//     * Injects the chat interface before the page footer is rendered.
//     *
//     * @param \core\hook\output\before_footer_html_generation $hook The hook instance.
//     * @return void
//     */
//    public static function inject_chat_interface(\core\hook\output\before_footer_html_generation $hook): void
//    {
//        // Get the Moodle page object from the hook.
//        $page = $hook->get_page();
//
//        // 1. Only show on course module "view" pages.
//        if (!preg_match('/^mod-.*-view$/', $page->pagetype)) {
//            return;
//        }
//
//        // 2. Check for a valid context and the required capability.
//        if (!$page->context || !has_capability('local/activitychat:use', $page->context)) {
//            return;
//        }
//
//        // 3. Add the plugin's CSS and JavaScript to the page.
//        $page->requires->css('/local/activitychat/styles/chat.css');
//        $page->requires->js_call_amd('local_activitychat/chat_manager', 'init', [
//            'contextid' => $page->context->id,
//        ]);
//
//        // 4. Render the chat interface from the Mustache template and add it to the page HTML.
//        $output = $page->get_renderer('local_activitychat');
//        $hook->add_html($output->render_from_template('local_activitychat/chat_interface', new \stdClass()));
//    }
//}
////namespace local_activitychat;
////
////defined('MOODLE_INTERNAL') || die();
////
////class observer {
////
////    public static function inject_chat_interface(\core\hook\output\before_footer_html_generation $hook): void {
////        // This will print a message and stop the page from loading completely.
////        echo '<h1>--- ACTIVITY CHAT HOOK IS RUNNING ---</h1>';
////        die();
////    }
//////    public static function inject_chat_interface(\core\hook\output\before_footer_html_generation $hook): void {
//////        // This method gets the page object from the hook to add assets correctly.
//////        $page = $hook->get_page();
//////
//////        // --- TEST CODE ---
//////        // Temporarily removed page type check to force rendering.
//////        // if (!preg_match('/^mod-.*-view$/', $page->pagetype)) {
//////        //     return;
//////        // }
//////
//////        // Temporarily removed capability check to force rendering.
//////        // if (!$page->context || !has_capability('local/activitychat:use', $page->context)) {
//////        //     return;
//////        // }
//////
//////        // Ensure we have a context before trying to use it.
//////        if (!$page->context) {
//////            return;
//////        }
//////        // --- END TEST CODE ---
//////
//////        // CORRECT WAY: Add CSS and JS to the page object first.
//////        $page->requires->css('/local/activitychat/styles/chat.css');
//////        $page->requires->js_call_amd('local_activitychat/chat_manager', 'init', [
//////            'contextid' => $page->context->id,
//////        ]);
//////
//////        // NOW, add the HTML to the page body.
//////        $hook->add_html($page->get_renderer('local_activitychat')->render_from_template('local_activitychat/chat_interface', new \stdClass()));
//////    }
////
//////    public static function inject_chat_interface(\core\hook\output\before_footer_html_generation $hook): void {
//////        // This method gets the page object from the hook to add assets correctly.
//////        $page = $hook->get_page();
//////
//////        // Only show on course module view pages.
//////        if (!preg_match('/^mod-.*-view$/', $page->pagetype)) {
//////            return;
//////        }
//////
//////        // Check capability for the current context.
//////        if (!$page->context || !has_capability('local/activitychat:use', $page->context)) {
//////            return;
//////        }
//////
//////        // CORRECT WAY: Add CSS and JS to the page object first.
//////        $page->requires->css('/local/activitychat/styles/chat.css');
//////        $page->requires->js_call_amd('local_activitychat/chat_manager', 'init', [
//////            'contextid' => $page->context->id,
//////        ]);
//////
//////        // NOW, add the HTML to the page body.
//////        $hook->add_html($page->get_renderer('local_activitychat')->render_from_template('local_activitychat/chat_interface', new \stdClass()));
//////    }
////}