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
 * Resource module admin settings and defaults
 *
 * @package    mod_predefinedlabels
 * @copyright  2013 Davo Smith, Synergy Learning
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$ADMIN->add('modsettings', new admin_category('modpredefinedlabelsfolder', new lang_string('pluginname', 'mod_predefinedlabels'), $module->is_enabled() === false));

$settings = new admin_settingpage($section, get_string('plugin_settings', 'mod_predefinedlabels'), 'moodle/site:config', $module->is_enabled() === false);

if ($ADMIN->fulltree) {
 
    $settings->add(new admin_setting_configcheckbox('predefinedlabels/dndmedia',
        get_string('dndmedia', 'mod_predefinedlabels'), get_string('configdndmedia', 'mod_predefinedlabels'), 1));

    $settings->add(new admin_setting_configtext('predefinedlabels/dndresizewidth',
        get_string('dndresizewidth', 'mod_predefinedlabels'), get_string('configdndresizewidth', 'mod_predefinedlabels'), 400, PARAM_INT, 6));

    $settings->add(new admin_setting_configtext('predefinedlabels/dndresizeheight',
        get_string('dndresizeheight', 'mod_predefinedlabels'), get_string('configdndresizeheight', 'mod_predefinedlabels'), 400, PARAM_INT, 6));
}

$ADMIN->add('modpredefinedlabelsfolder', $settings);
// Tell core we already added the settings structure.
$settings = null;

    
$template_manager = new admin_externalpage('modpredefinedlabels_managetemplates', get_string('manage_templates', 'mod_predefinedlabels'), '/mod/predefinedlabels/manage_templates.php', 'moodle/site:config', $module->is_enabled() === false);   
//$template_manager =  new admin_settingpage('modpredefinedlabels_managetemplates', get_string('manage_templates', 'mod_predefinedlabels'), 'moodle/site:config', $module->is_enabled() === false);
//$template_manager->add(new admin_setting_heading('templates', get_string('settings_templates_header', 'mod_predefinedlabels'), get_string('settings_templates_header_information', 'mod_predefinedlabels')));




$ADMIN->add('modpredefinedlabelsfolder', $template_manager);
