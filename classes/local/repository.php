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
 * Repository helpers for iearchy data.
 *
 * @package    mod_iearchy
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_iearchy\local;

use context_module;
use core_text;
use moodle_exception;
use moodle_url;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Repository for iearchy records.
 */
class repository {

    /**
     * Get visible levels with visible employees for a module instance.
     *
     * @param int $iearchyid
     * @param context_module $context
     * @return array
     */
    public static function get_visible_levels_with_employees(int $iearchyid, context_module $context): array {
        global $DB;

        $records = $DB->get_records('iearchy_levels', ['iearchyid' => $iearchyid, 'visible' => 1], 'sortorder ASC, id ASC');
        $levels = [];
        $levelnumber = 1;

        foreach ($records as $record) {
            $employees = $DB->get_records(
                'iearchy_employees',
                ['iearchyid' => $iearchyid, 'levelid' => $record->id, 'visible' => 1],
                'sortorder ASC, id ASC'
            );

            if (empty($employees)) {
                continue;
            }

            $level = [
                'id' => (int)$record->id,
                'level' => $levelnumber,
                'eyebrow' => format_string(
                    $record->eyebrow ?: get_string('levelnumber', 'iearchy', $levelnumber),
                    true,
                    ['context' => $context]
                ),
                'title' => format_string($record->title, true, ['context' => $context]),
                'employees' => [],
            ];

            foreach ($employees as $employee) {
                $fullname = format_string($employee->fullname, true, ['context' => $context]);
                $position = format_string($employee->position, true, ['context' => $context]);
                $description = trim((string)$employee->description);
                $initials = self::normalise_initials($employee->initials);
                $resolvedimageurl = self::get_employee_image_url($employee, $context);
                $hasimage = $resolvedimageurl !== '';

                $level['employees'][] = [
                    'id' => (int)$employee->id,
                    'fullname' => $fullname,
                    'position' => $position,
                    'description' => $description,
                    'imageurl' => $resolvedimageurl,
                    'initials' => $initials,
                    'hasimage' => $hasimage,
                    'cardaria' => get_string('cardaria', 'iearchy', (object)[
                        'fullname' => $fullname,
                        'position' => $position,
                    ]),
                ];
            }

            $levels[] = $level;
            $levelnumber++;
        }

        return $levels;
    }

    /**
     * Return all levels for an instance with employee counts.
     *
     * @param int $iearchyid
     * @return array
     */
    public static function get_all_levels(int $iearchyid): array {
        global $DB;

        $sql = "SELECT l.*,
                       COUNT(e.id) AS employeecount
                  FROM {iearchy_levels} l
             LEFT JOIN {iearchy_employees} e
                    ON e.levelid = l.id
                   AND e.iearchyid = l.iearchyid
                 WHERE l.iearchyid = :iearchyid
              GROUP BY l.id, l.iearchyid, l.sortorder, l.eyebrow, l.title, l.visible, l.timecreated, l.timemodified
              ORDER BY l.sortorder ASC, l.id ASC";

        return array_values($DB->get_records_sql($sql, ['iearchyid' => $iearchyid]));
    }

    /**
     * Return all employees for an instance with level titles.
     *
     * @param int $iearchyid
     * @return array
     */
    public static function get_all_employees(int $iearchyid): array {
        global $DB;

        $sql = "SELECT e.*,
                       l.title AS leveltitle
                  FROM {iearchy_employees} e
                  JOIN {iearchy_levels} l
                    ON l.id = e.levelid
                 WHERE e.iearchyid = :iearchyid
              ORDER BY l.sortorder ASC, l.id ASC, e.sortorder ASC, e.id ASC";

        return array_values($DB->get_records_sql($sql, ['iearchyid' => $iearchyid]));
    }

    /**
     * Return a menu of levels for one instance.
     *
     * @param int $iearchyid
     * @return array
     */
    public static function get_levels_menu(int $iearchyid): array {
        $menu = [];
        foreach (self::get_all_levels($iearchyid) as $level) {
            $menu[$level->id] = format_string($level->title);
        }
        return $menu;
    }

    /**
     * Get a single level record.
     *
     * @param int $id
     * @param int $iearchyid
     * @return stdClass|null
     */
    public static function get_level(int $id, int $iearchyid = 0): ?stdClass {
        global $DB;

        $params = ['id' => $id];
        if ($iearchyid > 0) {
            $params['iearchyid'] = $iearchyid;
        }

        $record = $DB->get_record('iearchy_levels', $params);
        return $record ?: null;
    }

    /**
     * Get a single employee record.
     *
     * @param int $id
     * @param int $iearchyid
     * @return stdClass|null
     */
    public static function get_employee(int $id, int $iearchyid = 0): ?stdClass {
        global $DB;

        $params = ['id' => $id];
        if ($iearchyid > 0) {
            $params['iearchyid'] = $iearchyid;
        }

        $record = $DB->get_record('iearchy_employees', $params);
        return $record ?: null;
    }

    /**
     * Count employees inside a level.
     *
     * @param int $levelid
     * @param int $iearchyid
     * @return int
     */
    public static function count_employees_for_level(int $levelid, int $iearchyid): int {
        global $DB;

        return (int)$DB->count_records('iearchy_employees', ['levelid' => $levelid, 'iearchyid' => $iearchyid]);
    }

    /**
     * Save a level.
     *
     * @param stdClass $data
     * @param int $iearchyid
     * @return int
     */
    public static function save_level(stdClass $data, int $iearchyid): int {
        global $DB;

        $now = time();
        $existing = null;
        $sortorder = self::resolve_level_sortorder($data, $iearchyid);
        $record = (object)[
            'id' => empty($data->id) ? 0 : (int)$data->id,
            'iearchyid' => $iearchyid,
            'sortorder' => $sortorder,
            'eyebrow' => trim((string)($data->eyebrow ?? '')),
            'title' => trim((string)$data->title),
            'visible' => empty($data->visible) ? 0 : 1,
            'timemodified' => $now,
        ];

        if ($record->id) {
            $existing = self::get_level($record->id, $iearchyid);
            if (!$existing) {
                throw new moodle_exception('invalidrecord', 'error');
            }
            $record->timecreated = $existing->timecreated;
            $DB->update_record('iearchy_levels', $record);
            return $record->id;
        }

        $record->timecreated = $now;
        unset($record->id);
        return (int)$DB->insert_record('iearchy_levels', $record);
    }

    /**
     * Save an employee.
     *
     * @param stdClass $data
     * @param int $iearchyid
     * @return int
     */
    public static function save_employee(stdClass $data, int $iearchyid): int {
        global $DB;

        $levelid = (int)$data->levelid;
        if (!self::get_level($levelid, $iearchyid)) {
            throw new moodle_exception('invalidlevel', 'iearchy');
        }

        $now = time();
        $sortorder = self::resolve_employee_sortorder($data, $iearchyid, $levelid);
        $record = (object)[
            'id' => empty($data->id) ? 0 : (int)$data->id,
            'iearchyid' => $iearchyid,
            'levelid' => $levelid,
            'sortorder' => $sortorder,
            'fullname' => trim((string)$data->fullname),
            'position' => trim((string)$data->position),
            'description' => trim((string)($data->description ?? '')),
            'imageurl' => trim((string)($data->imageurl ?? '')),
            'initials' => self::normalise_initials($data->initials ?? ''),
            'visible' => empty($data->visible) ? 0 : 1,
            'timemodified' => $now,
        ];

        if ($record->id) {
            $existing = self::get_employee($record->id, $iearchyid);
            if (!$existing) {
                throw new moodle_exception('invalidrecord', 'error');
            }
            $record->timecreated = $existing->timecreated;
            $DB->update_record('iearchy_employees', $record);
            return $record->id;
        }

        $record->timecreated = $now;
        unset($record->id);
        return (int)$DB->insert_record('iearchy_employees', $record);
    }

    /**
     * Delete a level.
     *
     * @param int $id
     * @param int $iearchyid
     * @return void
     */
    public static function delete_level(int $id, int $iearchyid): void {
        global $DB;

        // Defensive delete: avoid foreign key constraint violations if employees exist for the level.
        $DB->delete_records('iearchy_employees', ['levelid' => $id, 'iearchyid' => $iearchyid]);
        $DB->delete_records('iearchy_levels', ['id' => $id, 'iearchyid' => $iearchyid]);
    }

    /**
     * Delete an employee.
     *
     * @param int $id
     * @param int $iearchyid
     * @return void
     */
    public static function delete_employee(int $id, int $iearchyid): void {
        global $DB;

        $DB->delete_records('iearchy_employees', ['id' => $id, 'iearchyid' => $iearchyid]);
    }

    /**
     * Delete uploaded image files for one employee.
     *
     * @param int $employeeid
     * @param context_module $context
     * @return void
     */
    public static function delete_employee_image_files(int $employeeid, context_module $context): void {
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'mod_iearchy', 'employeeimage', $employeeid);
    }

    /**
     * Reorder levels for one iearchy instance.
     *
     * @param int $iearchyid
     * @param array $orderedids
     * @return void
     */
    public static function reorder_levels(int $iearchyid, array $orderedids): void {
        global $DB;

        $existing = array_map(static fn($level) => (int)$level->id, self::get_all_levels($iearchyid));
        self::validate_reorder_ids($existing, $orderedids);

        foreach (array_values($orderedids) as $index => $levelid) {
            $DB->update_record('iearchy_levels', (object)[
                'id' => (int)$levelid,
                'sortorder' => $index + 1,
                'timemodified' => time(),
            ]);
        }
    }

    /**
     * Reorder employees inside one level.
     *
     * @param int $iearchyid
     * @param int $levelid
     * @param array $orderedids
     * @return void
     */
    public static function reorder_employees(int $iearchyid, int $levelid, array $orderedids): void {
        global $DB;

        $records = $DB->get_records('iearchy_employees', ['iearchyid' => $iearchyid, 'levelid' => $levelid], 'sortorder ASC, id ASC', 'id');
        $existing = array_map(static fn($employee) => (int)$employee->id, array_values($records));
        self::validate_reorder_ids($existing, $orderedids);

        foreach (array_values($orderedids) as $index => $employeeid) {
            $DB->update_record('iearchy_employees', (object)[
                'id' => (int)$employeeid,
                'sortorder' => $index + 1,
                'timemodified' => time(),
            ]);
        }
    }

    /**
     * Move a level one step up or down.
     *
     * @param int $iearchyid
     * @param int $levelid
     * @param string $direction
     * @return void
     */
    public static function move_level(int $iearchyid, int $levelid, string $direction): void {
        $levels = array_values(self::get_all_levels($iearchyid));
        self::move_record_in_list('iearchy_levels', $levels, $levelid, $direction);
    }

    /**
     * Move an employee one step up or down inside a level.
     *
     * @param int $iearchyid
     * @param int $levelid
     * @param int $employeeid
     * @param string $direction
     * @return void
     */
    public static function move_employee(int $iearchyid, int $levelid, int $employeeid, string $direction): void {
        global $DB;

        $employees = array_values($DB->get_records(
            'iearchy_employees',
            ['iearchyid' => $iearchyid, 'levelid' => $levelid],
            'sortorder ASC, id ASC'
        ));

        self::move_record_in_list('iearchy_employees', $employees, $employeeid, $direction);
    }

    /**
     * Normalise placeholder initials.
     *
     * @param string $initials
     * @return string
     */
    private static function normalise_initials(string $initials): string {
        $initials = core_text::strtoupper(trim($initials));
        if ($initials === '') {
            return '?';
        }

        return core_text::substr($initials, 0, 4);
    }

    /**
     * Resolve the preferred employee image URL.
     *
     * @param stdClass $employee
     * @param context_module $context
     * @return string
     */
    private static function get_employee_image_url(stdClass $employee, context_module $context): string {
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'mod_iearchy', 'employeeimage', $employee->id, 'itemid, filepath, filename', false);

        if (!empty($files)) {
            $file = reset($files);
            return moodle_url::make_pluginfile_url(
                $context->id,
                'mod_iearchy',
                'employeeimage',
                $employee->id,
                $file->get_filepath(),
                $file->get_filename()
            )->out(false);
        }

        return trim((string)$employee->imageurl);
    }

    /**
     * Resolve sort order for levels.
     *
     * @param stdClass $data
     * @param int $iearchyid
     * @return int
     */
    private static function resolve_level_sortorder(stdClass $data, int $iearchyid): int {
        global $DB;

        if (!empty($data->id)) {
            $existing = self::get_level((int)$data->id, $iearchyid);
            if (!$existing) {
                throw new moodle_exception('invalidrecord', 'error');
            }

            if (property_exists($data, 'sortorder') && (int)$data->sortorder > 0) {
                return (int)$data->sortorder;
            }

            return (int)$existing->sortorder;
        }

        if (property_exists($data, 'sortorder') && (int)$data->sortorder > 0) {
            return (int)$data->sortorder;
        }

        return (int)$DB->count_records('iearchy_levels', ['iearchyid' => $iearchyid]) + 1;
    }

    /**
     * Resolve sort order for employees.
     *
     * @param stdClass $data
     * @param int $iearchyid
     * @param int $levelid
     * @return int
     */
    private static function resolve_employee_sortorder(stdClass $data, int $iearchyid, int $levelid): int {
        global $DB;

        if (!empty($data->id)) {
            $existing = self::get_employee((int)$data->id, $iearchyid);
            if (!$existing) {
                throw new moodle_exception('invalidrecord', 'error');
            }

            if (property_exists($data, 'sortorder') && (int)$data->sortorder > 0) {
                return (int)$data->sortorder;
            }

            if ((int)$existing->levelid === $levelid) {
                return (int)$existing->sortorder;
            }
        }

        if (property_exists($data, 'sortorder') && (int)$data->sortorder > 0) {
            return (int)$data->sortorder;
        }

        return (int)$DB->count_records('iearchy_employees', ['iearchyid' => $iearchyid, 'levelid' => $levelid]) + 1;
    }

    /**
     * Ensure the reordered ids match the stored set.
     *
     * @param array $existing
     * @param array $ordered
     * @return void
     */
    private static function validate_reorder_ids(array $existing, array $ordered): void {
        sort($existing);
        $ordered = array_map('intval', $ordered);
        sort($ordered);

        if ($existing !== $ordered) {
            throw new moodle_exception('invalidrecord', 'error');
        }
    }

    /**
     * Move a record within an ordered list and rewrite sortorder values.
     *
     * @param string $table
     * @param array $records
     * @param int $recordid
     * @param string $direction
     * @return void
     */
    private static function move_record_in_list(string $table, array $records, int $recordid, string $direction): void {
        global $DB;

        $index = null;
        foreach ($records as $key => $record) {
            if ((int)$record->id === $recordid) {
                $index = $key;
                break;
            }
        }

        if ($index === null) {
            throw new moodle_exception('invalidrecord', 'error');
        }

        $targetindex = $direction === 'up' ? $index - 1 : $index + 1;
        if (!isset($records[$targetindex])) {
            return;
        }

        $current = $records[$index];
        $records[$index] = $records[$targetindex];
        $records[$targetindex] = $current;

        foreach (array_values($records) as $sortorder => $record) {
            $DB->update_record($table, (object)[
                'id' => (int)$record->id,
                'sortorder' => $sortorder + 1,
                'timemodified' => time(),
            ]);
        }
    }
}