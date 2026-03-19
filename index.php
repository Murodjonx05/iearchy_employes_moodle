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
 * List all iearchy instances in one course.
 *
 * @package    mod_iearchy
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

$id = required_param('id', PARAM_INT);

$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);
require_course_login($course);

$PAGE->set_url('/mod/iearchy/index.php', ['id' => $id]);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title(format_string($course->shortname) . ': ' . get_string('modulenameplural', 'iearchy'));
$PAGE->set_heading(format_string($course->fullname));

$instances = get_all_instances_in_course('iearchy', $course);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('modulenameplural', 'iearchy'));

if (empty($instances)) {
    notice(get_string('thereareno', 'moodle', get_string('modulenameplural', 'iearchy')), new moodle_url('/course/view.php', ['id' => $course->id]));
}

$table = new html_table();
$table->head = [get_string('name'), get_string('description')];
$table->data = [];

foreach ($instances as $instance) {
    $link = html_writer::link(new moodle_url('/mod/iearchy/view.php', ['id' => $instance->coursemodule]), format_string($instance->name));
    $description = '';
    if (!empty($instance->intro)) {
        $description = format_module_intro('iearchy', $instance, $instance->coursemodule, false);
    }

    $table->data[] = [$link, $description];
}

echo html_writer::table($table);
echo $OUTPUT->footer();
