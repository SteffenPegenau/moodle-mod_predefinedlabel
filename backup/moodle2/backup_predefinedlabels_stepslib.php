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
 * @package mod_predefinedlabels
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that will be used by the backup_predefinedlabels_activity_task
 */

/**
 * Define the complete predefinedlabels structure for backup, with file and id annotations
 */
class backup_predefinedlabels_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $predefinedlabels = new backup_nested_element('predefinedlabels', array('id'), array(
            'templateid', 'timemodified'));

        // Build the tree
        // (love this)

        // Define sources
        $predefinedlabels->set_source_table('predefinedlabels', array('id' => backup::VAR_ACTIVITYID));

        // Define id annotations
        // (none)

        // Define file annotations
        $predefinedlabels->annotate_files('mod_predefinedlabels', 'intro', null); // This file area hasn't itemid

        // Return the root element (predefinedlabels), wrapped into standard activity structure
        return $this->prepare_activity_structure($predefinedlabels);
    }
}
