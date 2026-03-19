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
 * Upgrade steps for mod_iearchy.
 *
 * @package    mod_iearchy
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute upgrade steps.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_iearchy_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2026032000) {
        $table = new xmldb_table('iearchy');

        $displaymode = new xmldb_field('displaymode', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'page', 'headertitle');
        if (!$dbman->field_exists($table, $displaymode)) {
            $dbman->add_field($table, $displaymode);
        }

        $content = new xmldb_field('content', XMLDB_TYPE_TEXT, 'big', null, null, null, null, 'displaymode');
        if (!$dbman->field_exists($table, $content)) {
            $dbman->add_field($table, $content);
        }

        $contentformat = new xmldb_field('contentformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1', 'content');
        if (!$dbman->field_exists($table, $contentformat)) {
            $dbman->add_field($table, $contentformat);
        }

        upgrade_mod_savepoint(true, 2026032000, 'iearchy');
    }

    return true;
}
