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
 * Manage employees inside one iearchy instance.
 *
 * @package    mod_iearchy
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/locallib.php');

use mod_iearchy\local\repository;

$id = required_param('id', PARAM_INT);
$move = optional_param('move', '', PARAM_ALPHA);
$employeemoveid = optional_param('employeemoveid', 0, PARAM_INT);
$levelid = optional_param('levelid', 0, PARAM_INT);

[$course, $cm, $iearchy, $context] = iearchy_get_from_cmid($id);

require_course_login($course, true, $cm);
require_capability('mod/iearchy:managecontent', $context);

$PAGE->set_url('/mod/iearchy/manage_employees.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($iearchy->name) . ': ' . get_string('manageemployees', 'iearchy'));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_cm($cm, $course, $iearchy);
$PAGE->requires->css(new moodle_url('/mod/iearchy/styles.css'));
$PAGE->requires->js_call_amd('mod_iearchy/reorder', 'init');

if ($move !== '' && $employeemoveid && $levelid && confirm_sesskey()) {
    repository::move_employee((int)$iearchy->id, $levelid, $employeemoveid, $move);
    redirect(new moodle_url('/mod/iearchy/manage_employees.php', ['id' => $cm->id]), get_string('employeesaved', 'iearchy'));
}

$addurl = new moodle_url('/mod/iearchy/edit_employee.php', ['id' => $cm->id]);
$viewurl = new moodle_url('/mod/iearchy/view.php', ['id' => $cm->id]);
$reorderurl = new moodle_url('/mod/iearchy/reorder.php', ['id' => $cm->id]);

echo $OUTPUT->header();
echo html_writer::start_div('iearchy-admin');
echo $OUTPUT->heading(get_string('manageemployees', 'iearchy'));
echo $OUTPUT->single_button($addurl, get_string('addemployee', 'iearchy'));
echo $OUTPUT->single_button($viewurl, get_string('backtodirectory', 'iearchy'));
echo $OUTPUT->notification(get_string('dragdrophelp', 'iearchy'), \core\output\notification::NOTIFY_INFO);

$levels = repository::get_all_levels((int)$iearchy->id);
$employees = repository::get_all_employees((int)$iearchy->id);
if (empty($employees)) {
    echo $OUTPUT->notification(get_string('noemployees', 'iearchy'), \core\output\notification::NOTIFY_INFO);
    echo html_writer::end_div();
    echo $OUTPUT->footer();
    exit;
}

$grouped = [];
foreach ($employees as $employee) {
    $grouped[$employee->levelid][] = $employee;
}

foreach ($levels as $level) {
    if (empty($grouped[$level->id])) {
        continue;
    }

    echo html_writer::start_div('iearchy-admin-group');
    echo html_writer::div(s((string)$level->title), 'iearchy-admin-group__title');
    echo html_writer::start_div('iearchy-admin-list', [
        'data-region' => 'sortable-list',
        'data-action' => 'employees',
        'data-levelid' => $level->id,
        'data-cmid' => $cm->id,
        'data-url' => $reorderurl->out(false),
    ]);

    foreach (array_values($grouped[$level->id]) as $index => $employee) {
        $editurl = new moodle_url('/mod/iearchy/edit_employee.php', ['id' => $cm->id, 'employeeid' => $employee->id]);
        $deleteurl = new moodle_url('/mod/iearchy/delete_employee.php', ['id' => $cm->id, 'employeeid' => $employee->id]);
        $moveupurl = new moodle_url('/mod/iearchy/manage_employees.php', [
            'id' => $cm->id,
            'move' => 'up',
            'levelid' => $level->id,
            'employeemoveid' => $employee->id,
            'sesskey' => sesskey(),
        ]);
        $movedownurl = new moodle_url('/mod/iearchy/manage_employees.php', [
            'id' => $cm->id,
            'move' => 'down',
            'levelid' => $level->id,
            'employeemoveid' => $employee->id,
            'sesskey' => sesskey(),
        ]);

        echo html_writer::start_div('iearchy-admin-item', [
            'data-region' => 'sortable-item',
            'data-id' => $employee->id,
            'draggable' => 'true',
        ]);
        echo html_writer::div((string)($index + 1), 'iearchy-admin-item__order', ['data-region' => 'order-label']);
        echo html_writer::div('::', 'iearchy-admin-item__handle', ['aria-hidden' => 'true']);
        echo html_writer::start_div('iearchy-admin-item__content');
        echo html_writer::div(s((string)$employee->fullname), 'iearchy-admin-item__title');
        $meta = [];
        $meta[] = s((string)$employee->position);
        $meta[] = $employee->visible ? get_string('visibilityyes', 'iearchy') : get_string('visibilityno', 'iearchy');
        echo html_writer::div(implode(' | ', $meta), 'iearchy-admin-item__meta');
        echo html_writer::end_div();
        echo html_writer::div(
            html_writer::link($moveupurl, get_string('moveup', 'iearchy')) . ' | ' .
            html_writer::link($movedownurl, get_string('movedown', 'iearchy')) . ' | ' .
            html_writer::link($editurl, get_string('edit')) . ' | ' .
            html_writer::link($deleteurl, get_string('delete')),
            'iearchy-admin-item__actions'
        );
        echo html_writer::end_div();
    }

    echo html_writer::end_div();
    echo html_writer::end_div();
}
echo html_writer::end_div();
echo $OUTPUT->footer();
