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
 * Create or edit an employee.
 *
 * @package    mod_iearchy
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/locallib.php');

use mod_iearchy\form\employee_form;
use mod_iearchy\local\repository;

$id = required_param('id', PARAM_INT);
$employeeid = optional_param('employeeid', 0, PARAM_INT);

[$course, $cm, $iearchy, $context] = iearchy_get_from_cmid($id);

require_course_login($course, true, $cm);
require_capability('mod/iearchy:managecontent', $context);

$url = new moodle_url('/mod/iearchy/edit_employee.php', ['id' => $cm->id, 'employeeid' => $employeeid]);
$manageurl = new moodle_url('/mod/iearchy/manage_employees.php', ['id' => $cm->id]);
$levelsurl = new moodle_url('/mod/iearchy/manage_levels.php', ['id' => $cm->id]);
$PAGE->set_url($url);
$PAGE->set_title(format_string($iearchy->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_cm($cm, $course, $iearchy);

$levels = repository::get_levels_menu((int)$iearchy->id);
$record = null;
if ($employeeid) {
    $record = repository::get_employee($employeeid, (int)$iearchy->id);
    if (!$record) {
        throw new moodle_exception('invalidrecord', 'error');
    }
}

if (empty($levels) && !$record) {
    redirect($levelsurl, get_string('nolevelsforemployee', 'iearchy'), null, \core\output\notification::NOTIFY_WARNING);
}

$heading = $employeeid ? get_string('editemployee', 'iearchy') : get_string('addemployee', 'iearchy');
$form = new employee_form($url, ['employeeid' => $employeeid, 'levels' => $levels]);
$draftitemid = file_get_submitted_draft_itemid('imagefile');
file_prepare_draft_area($draftitemid, $context->id, 'mod_iearchy', 'employeeimage', $employeeid, [
    'subdirs' => 0,
    'maxfiles' => 1,
    'accepted_types' => ['image'],
]);

if ($form->is_cancelled()) {
    redirect($manageurl);
}

if ($data = $form->get_data()) {
    // The form uses employeeid to avoid clashing with the course module id param.
    $data->id = (int)($data->employeeid ?? 0);
    unset($data->employeeid);
    $savedid = repository::save_employee($data, (int)$iearchy->id);
    if (isset($data->imagefile)) {
        file_save_draft_area_files($data->imagefile, $context->id, 'mod_iearchy', 'employeeimage', $savedid, [
            'subdirs' => 0,
            'maxfiles' => 1,
            'accepted_types' => ['image'],
        ]);
    }
    redirect($manageurl, get_string('employeesaved', 'iearchy'), null, \core\output\notification::NOTIFY_SUCCESS);
}

$defaults = $record ? clone $record : new stdClass();
$defaults->employeeid = $employeeid;
$defaults->imagefile = $draftitemid;
$form->set_data($defaults);

echo $OUTPUT->header();
echo $OUTPUT->heading($heading);
$form->display();
echo $OUTPUT->footer();
