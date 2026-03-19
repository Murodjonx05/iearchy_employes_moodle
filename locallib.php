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
 * Local helpers for mod_iearchy.
 *
 * @package    mod_iearchy
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Atto/Tiny editor options for instance content (inline mode, @@PLUGINFILE@@).
 *
 * @param context $context Module context
 * @return array
 */
function iearchy_get_editor_options(context $context): array {
    global $CFG;

    return [
        'subdirs' => 1,
        'maxbytes' => $CFG->maxbytes,
        'maxfiles' => -1,
        'changeformat' => 1,
        'context' => $context,
        'noclean' => 1,
        'trusttext' => 0,
    ];
}

/**
 * Resolve course, cm, instance and context from a course module id.
 *
 * @param int $cmid
 * @return array
 */
function iearchy_get_from_cmid(int $cmid): array {
    global $DB;

    $cm = get_coursemodule_from_id('iearchy', $cmid, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
    $iearchy = $DB->get_record('iearchy', ['id' => $cm->instance], '*', MUST_EXIST);
    $context = context_module::instance($cm->id);

    return [$course, $cm, $iearchy, $context];
}
