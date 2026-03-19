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
 * AJAX reorder endpoint.
 *
 * @package    mod_iearchy
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/locallib.php');

use mod_iearchy\local\repository;

$id = required_param('id', PARAM_INT);
$action = required_param('action', PARAM_ALPHA);
$orderedids = required_param('orderedids', PARAM_RAW_TRIMMED);
$levelid = optional_param('levelid', 0, PARAM_INT);

[$course, $cm, $iearchy, $context] = iearchy_get_from_cmid($id);

require_course_login($course, true, $cm);
require_capability('mod/iearchy:managecontent', $context);
require_sesskey();

$ids = array_values(array_filter(array_map('intval', explode(',', $orderedids))));

switch ($action) {
    case 'levels':
        repository::reorder_levels((int)$iearchy->id, $ids);
        break;
    case 'employees':
        repository::reorder_employees((int)$iearchy->id, $levelid, $ids);
        break;
    default:
        throw new moodle_exception('invalidparameter', 'error');
}

header('Content-Type: application/json');
echo json_encode(['ok' => true]);
