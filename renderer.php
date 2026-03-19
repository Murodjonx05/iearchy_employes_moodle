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
 * Module renderer.
 *
 * @package    mod_iearchy
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Renderer for mod_iearchy.
 */
class mod_iearchy_renderer extends plugin_renderer_base {

    /**
     * Render the directory page.
     *
     * @param \mod_iearchy\output\directory_page $page
     * @return string
     */
    public function render_directory_page(\mod_iearchy\output\directory_page $page): string {
        return $this->render_from_template('mod_iearchy/directory', $page->export_for_template($this));
    }
}
