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
            /*
              <div style="display: none;"><input type="password" value="">
              <input name="course" type="hidden" value="2399">
              <input name="coursemodule" type="hidden" value="">
              <input name="section" type="hidden" value="0">
              <input name="module" type="hidden" value="51">
              <input name="modulename" type="hidden" value="predefinedlabels">
              <input name="instance" type="hidden" value="">
              <input name="add" type="hidden" value="predefinedlabels">
              <input name="update" type="hidden" value="0">
              <input name="return" type="hidden" value="0">
              <input name="sr" type="hidden" value="0">
              <input name="sesskey" type="hidden" value="2WuAlqQI4z">
              <input name="_qf__mod_predefinedlabels_mod_form" type="hidden" value="1">
              <input name="mform_isexpanded_id_modstandardelshdr" type="hidden" value="1">
              <input name="mform_isexpanded_id_availabilityconditionsheader" type="hidden" value="1">
              </div>
             */

            /*
            $mform->addElement('hidden', 'return', '1')
                    ->setType(PARAM_BOOL);
            
            $mform->addElement('hidden', 'update', '0')
                    ->setType(PARAM_INT);
             
            $mform->addElement('hidden', 'course', $COURSE->id)
                    ->setType(PARAM_INT);
            $mform->addElement('hidden', 'id', $COURSE->id)
                    ->setType(PARAM_INT);
            */
            /*
              $mform->addElement('hidden', 'update', 'cancel')
              ->setType(PARAM_RAW);
              $mform->addElement('hidden', 'modulename', 'predefinedlabels')
              ->setType(PARAM_RAW);
              $mform->addElement('hidden', 'instance', 1)
              ->setType(PARAM_INT);
              $mform->addElement('hidden', 'id', $PAGE->course->id)
              ->setType(PARAM_INT);


              $url = new moodle_url('/course/view.php?id=' . $COURSE->id);
              $mform->setAttributes(array('action' => $url));
             */
            /*
            $url = new moodle_url('/course/view.php?id=' . $COURSE->id);
            $mform->setAttributes(array('action' => $url));
            */
            $mform->addElement('static', 'error_no_templates', get_string('error'), get_string('error_no_templates', 'mod_predefinedlabels'));
            //$mform->addElement('submit', 'cancelbutton', get_string('cancel'));
            //$mform->addElement('cancel');
            $this->standard_hidden_coursemodule_elements();
            //$this->add_action_buttons(true, false, null);
            $mform->addElement('cancel');
        } else {
            $mform->addElement('header', 'generalhdr', get_string('please_chose_template', 'mod_predefinedlabels'));

            $attributes = array();
            $radioarray = array();

            foreach ($templates as $id => $template) {
                $entry = $template->title . "<br />" . $template->body;
                $radioarray[] = & $mform->createElement('radio', 'radioarr', '', $entry, $template->id, $attributes);
            }
            $mform->addGroup($radioarray, 'radioarr', '', '<br />', true);
            $mform->addRule('radioarr', null, 'required', null, 'client');
            /*
              $this->standard_intro_elements(get_string('predefinedlabelstext', 'predefinedlabels'));
             */

            $this->standard_coursemodule_elements();
//-------------------------------------------------------------------------------
// buttons
            $this->add_action_buttons(true, false, null);
        }
    }

}
