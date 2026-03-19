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
 * Delete an employee after confirmation.
 *
 * @package    mod_iearchy
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/locallib.php');

use mod_iearchy\local\repository;

$id = required_param('id', PARAM_INT);
$employeeid = required_param('employeeid', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

[$course, $cm, $iearchy, $context] = iearchy_get_from_cmid($id);

require_course_login($course, true, $cm);
require_capability('mod/iearchy:managecontent', $context);

$url = new moodle_url('/mod/iearchy/delete_employee.php', ['id' => $cm->id, 'employeeid' => $employeeid]);
$manageurl = new moodle_url('/mod/iearchy/manage_employees.php', ['id' => $cm->id]);
$PAGE->set_url($url);
$PAGE->set_title(format_string($iearchy->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_cm($cm, $course, $iearchy);

$record = repository::get_employee($employeeid, (int)$iearchy->id);
if (!$record) {
    throw new moodle_exception('invalidrecord', 'error');
}

if ($confirm && confirm_sesskey()) {
    repository::delete_employee_image_files($employeeid, $context);
    repository::delete_employee($employeeid, (int)$iearchy->id);
    redirect($manageurl, get_string('employeedeleted', 'iearchy'), null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();
echo $OUTPUT->confirm(
    get_string('confirmdeleteemployee', 'iearchy', format_string($record->fullname)),
    new moodle_url('/mod/iearchy/delete_employee.php', ['id' => $cm->id, 'employeeid' => $employeeid, 'confirm' => 1, 'sesskey' => sesskey()]),
    $manageurl
);
echo $OUTPUT->footer();
