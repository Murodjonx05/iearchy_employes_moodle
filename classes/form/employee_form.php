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
 * Employee form.
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
 * Form for iearchy employees.
 */
class employee_form extends moodleform {

    /**
     * Define form fields.
     */
    public function definition() {
        $mform = $this->_form;
        $employeeid = $this->_customdata['employeeid'] ?? 0;
        $levels = $this->_customdata['levels'] ?? [];

        // Do not use "id" here as it conflicts with the course module id parameter.
        $mform->addElement('hidden', 'employeeid', $employeeid);
        $mform->setType('employeeid', PARAM_INT);

        $mform->addElement('select', 'levelid', get_string('level', 'iearchy'), $levels);
        $mform->setType('levelid', PARAM_INT);

        $mform->addElement('text', 'fullname', get_string('fullname', 'iearchy'), ['size' => 48]);
        $mform->setType('fullname', PARAM_TEXT);
        $mform->addRule('fullname', null, 'required', null, 'client');

        $mform->addElement('text', 'position', get_string('position', 'iearchy'), ['size' => 48]);
        $mform->setType('position', PARAM_TEXT);
        $mform->addRule('position', null, 'required', null, 'client');

        $mform->addElement('textarea', 'description', get_string('description', 'iearchy'), 'rows="6" cols="60"');
        $mform->setType('description', PARAM_TEXT);

        $mform->addElement('text', 'imageurl', get_string('imageurl', 'iearchy'), ['size' => 72]);
        $mform->setType('imageurl', PARAM_RAW_TRIMMED);
        $mform->addHelpButton('imageurl', 'imageurl', 'iearchy');

        $mform->addElement('filemanager', 'imagefile', get_string('imagefile', 'iearchy'), null, [
            'subdirs' => 0,
            'maxfiles' => 1,
            'accepted_types' => ['image'],
        ]);
        $mform->addHelpButton('imagefile', 'imagefile', 'iearchy');

        $mform->addElement('text', 'initials', get_string('initials', 'iearchy'), ['size' => 8, 'maxlength' => 4]);
        $mform->setType('initials', PARAM_TEXT);
        $mform->addHelpButton('initials', 'initials', 'iearchy');

        $mform->addElement('advcheckbox', 'visible', get_string('visible', 'iearchy'));
        $mform->setDefault('visible', 1);

        $this->add_action_buttons(true, get_string('savechanges'));
    }

    /**
     * Validate submitted data.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        global $CFG;
        $errors = parent::validation($data, $files);

        $levels = $this->_customdata['levels'] ?? [];
        if (empty($data['levelid']) || !array_key_exists((int)$data['levelid'], $levels)) {
            $errors['levelid'] = get_string('invalidlevel', 'iearchy');
        }

        $hasdraftfile = false;
        if (!empty($data['imagefile'])) {
            $draftinfo = file_get_draft_area_info($data['imagefile']);
            $hasdraftfile = !empty($draftinfo['filecount']);
        }

        $imageurl = trim((string)($data['imageurl'] ?? ''));
        if ($imageurl !== '') {
            $candidate = $imageurl;
            if (str_starts_with($candidate, '/')) {
                $candidate = rtrim($CFG->wwwroot, '/') . $candidate;
            }

            $parts = parse_url($candidate);
            $scheme = strtolower((string)($parts['scheme'] ?? ''));
            $path = (string)($parts['path'] ?? '');
            $host = strtolower((string)($parts['host'] ?? ''));
            $wwwrootparts = parse_url($CFG->wwwroot);
            $wwwroothost = strtolower((string)($wwwrootparts['host'] ?? ''));

            // External URLs: allow http(s). Local Moodle file URLs must be pluginfile/draftfile.
            $valid = in_array($scheme, ['http', 'https'], true);
            if ($valid && $host !== '' && $wwwroothost !== '' && $host === $wwwroothost) {
                $valid = str_contains($path, '/pluginfile.php')
                    || str_contains($path, '/draftfile.php')
                    || str_contains($path, '/webservice/pluginfile.php');
            }

            if (!$valid) {
                $errors['imageurl'] = get_string('invalidimageurl', 'iearchy');
            }
        }

        if (trim((string)$data['imageurl']) === '' && trim((string)$data['initials']) === '' && !$hasdraftfile) {
            $errors['initials'] = get_string('missingavatarfallback', 'iearchy');
        }

        return $errors;
    }
}