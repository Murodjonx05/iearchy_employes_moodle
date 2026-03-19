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
 * Level form.
 *
 * @package    mod_iearchy
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_iearchy\form;

use moodleform;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form for iearchy levels.
 */
class level_form extends moodleform {

    /**
     * Define form elements.
     */
    public function definition() {
        $mform = $this->_form;
        $levelid = $this->_customdata['levelid'] ?? 0;

        // Do not use "id" here as it conflicts with the course module id parameter.
        $mform->addElement('hidden', 'levelid', $levelid);
        $mform->setType('levelid', PARAM_INT);

        $mform->addElement('text', 'title', get_string('title', 'iearchy'), ['size' => 48]);
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', null, 'required', null, 'client');

        $mform->addElement('advcheckbox', 'visible', get_string('visible', 'iearchy'));
        $mform->setDefault('visible', 1);

        $this->add_action_buttons(true, get_string('savechanges'));
    }
}
