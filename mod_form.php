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
 * Module settings form.
 *
 * @package    mod_iearchy
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/iearchy/lib.php');

/**
 * Form definition for mod_iearchy.
 */
class mod_iearchy_mod_form extends moodleform_mod {

    /**
     * Define the module form.
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'), ['size' => '48']);
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');

        $this->standard_intro_elements();

        // Variant A: always operate in PAGE mode.
        $mform->addElement('hidden', 'displaymode', IEARCHY_DISPLAY_PAGE);
        $mform->setType('displaymode', PARAM_ALPHA);

        $mform->addElement('text', 'headereyebrow', get_string('headereyebrow', 'iearchy'), ['size' => '48']);
        $mform->setType('headereyebrow', PARAM_TEXT);
        $mform->setDefault('headereyebrow', get_string('defaultheadereyebrow', 'iearchy'));

        $mform->addElement('text', 'headertitle', get_string('headertitle', 'iearchy'), ['size' => '48']);
        $mform->setType('headertitle', PARAM_TEXT);

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    /**
     * Prepare draft area for the content editor.
     *
     * @param array $defaultvalues
     */
    public function data_preprocessing(&$defaultvalues) {
        // No preprocessing needed in PAGE-only mode.
    }

    /**
     * Validate display mode value.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Force PAGE mode.
        if (($data['displaymode'] ?? IEARCHY_DISPLAY_PAGE) !== IEARCHY_DISPLAY_PAGE) {
            $errors['displaymode'] = get_string('invaliddisplaymode', 'iearchy');
        }

        return $errors;
    }
}
