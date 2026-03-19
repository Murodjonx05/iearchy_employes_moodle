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
 * Main module view page.
 *
 * @package    mod_iearchy
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
require_once(__DIR__ . '/locallib.php');

use mod_iearchy\local\repository;
use mod_iearchy\output\directory_page;

$id = required_param('id', PARAM_INT);

[$course, $cm, $iearchy, $context] = iearchy_get_from_cmid($id);

require_course_login($course, true, $cm);
require_capability('mod/iearchy:view', $context);

$event = \mod_iearchy\event\course_module_viewed::create([
    'objectid' => $iearchy->id,
    'context' => $context,
]);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('iearchy', $iearchy);
$event->trigger();

$PAGE->set_url('/mod/iearchy/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($iearchy->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_cm($cm, $course, $iearchy);
$PAGE->requires->css(new moodle_url('/mod/iearchy/styles.css'));
$PAGE->requires->js_call_amd('mod_iearchy/directory', 'init');

$headereyebrow = trim((string)$iearchy->headereyebrow);
if ($headereyebrow === '') {
    $headereyebrow = get_string('defaultheadereyebrow', 'iearchy');
}

$headertitle = trim((string)$iearchy->headertitle);
if ($headertitle === '') {
    $headertitle = $iearchy->name ?: get_string('defaultheadertitle', 'iearchy');
}

$page = new directory_page(
    format_string($headereyebrow, true, ['context' => $context]),
    format_string($headertitle, true, ['context' => $context]),
    repository::get_visible_levels_with_employees((int)$iearchy->id, $context),
    has_capability('mod/iearchy:managecontent', $context),
    (int)$cm->id
);

$renderer = $PAGE->get_renderer('mod_iearchy');

echo $OUTPUT->header();
if (trim((string)$iearchy->intro) !== '') {
    echo $OUTPUT->box(format_module_intro('iearchy', $iearchy, $cm->id), 'generalbox mod_introbox', 'iearchyintro');
}
echo $renderer->render($page);
echo $OUTPUT->footer();
