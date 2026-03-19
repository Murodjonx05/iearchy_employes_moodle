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
 * Manage levels inside one iearchy instance.
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
$levelmoveid = optional_param('levelmoveid', 0, PARAM_INT);

[$course, $cm, $iearchy, $context] = iearchy_get_from_cmid($id);

require_course_login($course, true, $cm);
require_capability('mod/iearchy:managecontent', $context);

$PAGE->set_url('/mod/iearchy/manage_levels.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($iearchy->name) . ': ' . get_string('managelevels', 'iearchy'));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_cm($cm, $course, $iearchy);
$PAGE->requires->css(new moodle_url('/mod/iearchy/styles.css'));
$PAGE->requires->js_call_amd('mod_iearchy/reorder', 'init');

if ($move !== '' && $levelmoveid && confirm_sesskey()) {
    repository::move_level((int)$iearchy->id, $levelmoveid, $move);
    redirect(new moodle_url('/mod/iearchy/manage_levels.php', ['id' => $cm->id]), get_string('levelsaved', 'iearchy'));
}

$addurl = new moodle_url('/mod/iearchy/edit_level.php', ['id' => $cm->id]);
$viewurl = new moodle_url('/mod/iearchy/view.php', ['id' => $cm->id]);
$reorderurl = new moodle_url('/mod/iearchy/reorder.php', ['id' => $cm->id]);

echo $OUTPUT->header();
echo html_writer::start_div('iearchy-admin');
echo $OUTPUT->heading(get_string('managelevels', 'iearchy'));
echo $OUTPUT->single_button($addurl, get_string('addlevel', 'iearchy'));
echo $OUTPUT->single_button($viewurl, get_string('backtodirectory', 'iearchy'));
echo $OUTPUT->notification(get_string('dragdrophelp', 'iearchy'), \core\output\notification::NOTIFY_INFO);

$levels = repository::get_all_levels((int)$iearchy->id);
if (empty($levels)) {
    echo $OUTPUT->notification(get_string('nolevelsmanage', 'iearchy'), \core\output\notification::NOTIFY_INFO);
    echo html_writer::end_div();
    echo $OUTPUT->footer();
    exit;
}

echo html_writer::start_div('iearchy-admin-list', [
    'data-region' => 'sortable-list',
    'data-action' => 'levels',
    'data-cmid' => $cm->id,
    'data-url' => $reorderurl->out(false),
]);

foreach (array_values($levels) as $index => $level) {
    $editurl = new moodle_url('/mod/iearchy/edit_level.php', ['id' => $cm->id, 'levelid' => $level->id]);
    $deleteurl = new moodle_url('/mod/iearchy/delete_level.php', ['id' => $cm->id, 'levelid' => $level->id]);
    $moveupurl = new moodle_url('/mod/iearchy/manage_levels.php', [
        'id' => $cm->id,
        'move' => 'up',
        'levelmoveid' => $level->id,
        'sesskey' => sesskey(),
    ]);
    $movedownurl = new moodle_url('/mod/iearchy/manage_levels.php', [
        'id' => $cm->id,
        'move' => 'down',
        'levelmoveid' => $level->id,
        'sesskey' => sesskey(),
    ]);

    echo html_writer::start_div('iearchy-admin-item', [
        'data-region' => 'sortable-item',
        'data-id' => $level->id,
        'draggable' => 'true',
    ]);
    echo html_writer::div((string)($index + 1), 'iearchy-admin-item__order', ['data-region' => 'order-label']);
    echo html_writer::div('::', 'iearchy-admin-item__handle', ['aria-hidden' => 'true']);
    echo html_writer::start_div('iearchy-admin-item__content');
    echo html_writer::div(s((string)$level->title), 'iearchy-admin-item__title');
    $meta = [];
    $meta[] = get_string('employees', 'iearchy') . ': ' . (int)$level->employeecount;
    $meta[] = $level->visible ? get_string('visibilityyes', 'iearchy') : get_string('visibilityno', 'iearchy');
    echo html_writer::div(implode(' | ', array_filter($meta, static fn($item) => trim((string)$item) !== '')), 'iearchy-admin-item__meta');
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
echo $OUTPUT->footer();
