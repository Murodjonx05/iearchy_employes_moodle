<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Mandatory public API for mod_iearchy.
 *
 * @package    mod_iearchy
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/locallib.php');

/** Display as a separate activity page (like mod_page). */
define('IEARCHY_DISPLAY_PAGE', 'page');

/** Display HTML on the course page in the section (like mod_label). */
define('IEARCHY_DISPLAY_INLINE', 'inline');

/**
 * Normalise display mode (backward compatible when displaymode is missing).
 *
 * @param stdClass|null $instance
 * @return string IEARCHY_DISPLAY_PAGE or IEARCHY_DISPLAY_INLINE
 */
function iearchy_get_display_mode(?stdClass $instance): string {
    // Variant A: keep DB field for compatibility, but always behave as PAGE.
    return IEARCHY_DISPLAY_PAGE;
}

/**
 * Describe supported module features.
 *
 * @param string $feature
 * @return mixed
 */
function iearchy_supports($feature) {
    return match ($feature) {
        FEATURE_MOD_ARCHETYPE => MOD_ARCHETYPE_RESOURCE,
        FEATURE_GROUPS => false,
        FEATURE_GROUPINGS => false,
        FEATURE_MOD_INTRO => true,
        FEATURE_GRADE_HAS_GRADE => false,
        FEATURE_GRADE_OUTCOMES => false,
        FEATURE_BACKUP_MOODLE2 => false,
        FEATURE_SHOW_DESCRIPTION => true,
        FEATURE_MOD_PURPOSE => MOD_PURPOSE_CONTENT,
        default => null,
    };
}

/**
 * Add a new iearchy instance.
 *
 * @param stdClass $data
 * @param moodleform_mod $mform
 * @return int
 */
function iearchy_add_instance($data, $mform) {
    global $DB;

    $cmid = $data->coursemodule;

    $record = new stdClass();
    $record->course = $data->course;
    $record->name = trim((string)$data->name);
    $record->intro = $data->intro ?? '';
    $record->introformat = $data->introformat ?? FORMAT_HTML;
    $record->headereyebrow = trim((string)($data->headereyebrow ?? ''));
    $record->headertitle = trim((string)($data->headertitle ?? ''));
    $record->displaymode = IEARCHY_DISPLAY_PAGE;

    $content = '';
    $contentformat = FORMAT_HTML;
    $draftitemid = 0;
    if ($mform && isset($data->content_editor)) {
        $content = $data->content_editor['text'];
        $contentformat = (int)$data->content_editor['format'];
        $draftitemid = (int)($data->content_editor['itemid'] ?? 0);
    }
    $record->content = $content;
    $record->contentformat = $contentformat;
    $record->timemodified = time();

    $id = (int)$DB->insert_record('iearchy', $record);

    $DB->set_field('course_modules', 'instance', $id, ['id' => $cmid]);
    $context = context_module::instance($cmid);

    if ($mform && $draftitemid) {
        $record->id = $id;
        $record->content = file_save_draft_area_files(
            $draftitemid,
            $context->id,
            'mod_iearchy',
            'content',
            0,
            iearchy_get_editor_options($context),
            $record->content
        );
        $DB->update_record('iearchy', $record);
    }

    return $id;
}

/**
 * Update an iearchy instance.
 *
 * @param stdClass $data
 * @param moodleform_mod $mform
 * @return bool
 */
function iearchy_update_instance($data, $mform) {
    global $DB;

    $cmid = $data->coursemodule;
    $draftitemid = 0;
    if (!empty($data->content_editor) && isset($data->content_editor['itemid'])) {
        $draftitemid = (int)$data->content_editor['itemid'];
    }

    $record = new stdClass();
    $record->id = $data->instance;
    $record->course = $data->course;
    $record->name = trim((string)$data->name);
    $record->intro = $data->intro ?? '';
    $record->introformat = $data->introformat ?? FORMAT_HTML;
    $record->headereyebrow = trim((string)($data->headereyebrow ?? ''));
    $record->headertitle = trim((string)($data->headertitle ?? ''));
    $record->displaymode = IEARCHY_DISPLAY_PAGE;

    if ($mform && isset($data->content_editor)) {
        $record->content = $data->content_editor['text'];
        $record->contentformat = (int)$data->content_editor['format'];
    }

    $record->timemodified = time();

    $DB->update_record('iearchy', $record);

    $context = context_module::instance($cmid);
    if ($mform && $draftitemid) {
        $record->content = file_save_draft_area_files(
            $draftitemid,
            $context->id,
            'mod_iearchy',
            'content',
            0,
            iearchy_get_editor_options($context),
            $record->content
        );
        $DB->update_record('iearchy', $record);
    }

    return true;
}

/**
 * Delete an iearchy instance and its related records.
 *
 * @param int $id
 * @return bool
 */
function iearchy_delete_instance($id) {
    global $DB;

    if (!$DB->record_exists('iearchy', ['id' => $id])) {
        return false;
    }

    if ($cm = get_coursemodule_from_instance('iearchy', $id)) {
        $context = context_module::instance($cm->id);
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'mod_iearchy', 'employeeimage');
        $fs->delete_area_files($context->id, 'mod_iearchy', 'content');
    }

    $DB->delete_records('iearchy_employees', ['iearchyid' => $id]);
    $DB->delete_records('iearchy_levels', ['iearchyid' => $id]);
    $DB->delete_records('iearchy', ['id' => $id]);

    return true;
}

/**
 * Serve module files.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function iearchy_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_login($course, true, $cm);
    require_capability('mod/iearchy:view', $context);

    if ($filearea === 'content') {
        $itemid = 0;
        $filename = array_pop($args);
        $filepath = $args ? '/' . implode('/', $args) . '/' : '/';

        $fs = get_file_storage();
        $file = $fs->get_file($context->id, 'mod_iearchy', 'content', $itemid, $filepath, $filename);
        if (!$file || $file->is_directory()) {
            return false;
        }

        send_stored_file($file, 0, 0, $forcedownload, $options);
    }

    if ($filearea !== 'employeeimage') {
        return false;
    }

    if (count($args) < 2) {
        return false;
    }

    $itemid = (int)array_shift($args);
    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'mod_iearchy', $filearea, $itemid, $filepath, $filename);
    if (!$file || $file->is_directory()) {
        return false;
    }

    send_stored_file($file, 0, 0, $forcedownload, $options);
}

/**
 * Return module info for course pages.
 *
 * @param cm_info $coursemodule
 * @return cached_cm_info|null
 */
function iearchy_get_coursemodule_info($coursemodule) {
    global $DB, $OUTPUT;

    $record = $DB->get_record('iearchy', ['id' => $coursemodule->instance], 'id, name, intro, introformat, displaymode');
    if (!$record) {
        return null;
    }

    $info = new cached_cm_info();
    $info->name = $record->name;
    // Ensure Moodle can always resolve the activity icon (monologo/icon).
    // Some core renderers request the monologo icon using the short modname as component.
    // Providing an explicit icon URL avoids missing icons across themes.
    $info->iconurl = $OUTPUT->image_url('monologo', 'mod_iearchy');

    if ($coursemodule->showdescription) {
        $info->content = format_module_intro('iearchy', $record, $coursemodule->id, false);
    }

    return $info;
}

/**
 * Course-module dynamic data callback.
 *
 * @param cm_info $cm
 */
function mod_iearchy_cm_info_dynamic(cm_info $cm) {
}

/**
 * Course-module view data callback.
 *
 * @param cm_info $cm
 */
function mod_iearchy_cm_info_view(cm_info $cm) {
}
