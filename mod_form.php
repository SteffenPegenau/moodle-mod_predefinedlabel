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
 * Add predefinedlabels form
 *
 * @package mod_predefinedlabels
 * @copyright  2006 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once ($CFG->dirroot . '/course/moodleform_mod.php');

class mod_predefinedlabels_mod_form extends moodleform_mod {

    function definition() {
        GLOBAL $DB;

        $mform = $this->_form;

        $templates = $DB->get_records('predefinedlabels_templates', array('available' => 1));
        if (count($templates) == 0) {
            $mform->addElement('static', 'error_no_templates', get_string('error'), get_string('error_no_templates', 'mod_predefinedlabels'));
            return;
        } else {
            // Prepare templates for select box
        }

        $mform->addElement('header', 'generalhdr', get_string('please_chose_template', 'mod_predefinedlabels'));

        $attributes = array();
        $radioarray = array();
        
        foreach ($templates as $id => $template) {
            $entry = $template->title."<br />".$template->body;
            $radioarray[] = & $mform->createElement('radio', 'yesno', '', $entry, $template->id, $attributes);
        }
        $mform->addGroup($radioarray, 'radioar', '', '<br />', true);
        /*
          $this->standard_intro_elements(get_string('predefinedlabelstext', 'predefinedlabels'));
         */
        $this->standard_coursemodule_elements();

//-------------------------------------------------------------------------------
// buttons
        $this->add_action_buttons(true, false, null);
    }

}
