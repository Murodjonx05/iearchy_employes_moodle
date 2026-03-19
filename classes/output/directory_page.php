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
 * Renderable for the module directory page.
 *
 * @package    mod_iearchy
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_iearchy\output;

use moodle_url;
use renderable;
use renderer_base;
use templatable;

defined('MOODLE_INTERNAL') || die();

/**
 * Directory page renderable.
 */
class directory_page implements renderable, templatable {

    /** @var string */
    private $headereyebrow;

    /** @var string */
    private $headertitle;

    /** @var array */
    private $levels;

    /** @var bool */
    private $canmanage;

    /** @var int */
    private $cmid;

    /**
     * Constructor.
     *
     * @param string $headereyebrow
     * @param string $headertitle
     * @param array $levels
     * @param bool $canmanage
     * @param int $cmid
     */
    public function __construct(string $headereyebrow, string $headertitle, array $levels, bool $canmanage, int $cmid) {
        $this->headereyebrow = $headereyebrow;
        $this->headertitle = $headertitle;
        $this->levels = $levels;
        $this->canmanage = $canmanage;
        $this->cmid = $cmid;
    }

    /**
     * Export template context.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        return [
            'header_eyebrow' => $this->headereyebrow,
            'header_title' => $this->headertitle,
            'haslevels' => !empty($this->levels),
            'levels' => $this->levels,
            'directoryempty' => get_string('directoryempty', 'iearchy'),
            'canmanage' => $this->canmanage,
            'managelevelsurl' => (new moodle_url('/mod/iearchy/manage_levels.php', ['id' => $this->cmid]))->out(false),
            'manageemployeesurl' => (new moodle_url('/mod/iearchy/manage_employees.php', ['id' => $this->cmid]))->out(false),
            'managelevelslabel' => get_string('managelevels', 'iearchy'),
            'manageemployeeslabel' => get_string('manageemployees', 'iearchy'),
            'openprofile' => get_string('openprofile', 'iearchy'),
            'close' => get_string('close', 'iearchy'),
        ];
    }
}
