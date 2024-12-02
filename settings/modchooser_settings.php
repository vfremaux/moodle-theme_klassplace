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
* Presets settings page file.
*
* @package    theme_klassplace
* @copyright  2017 OCJ
* @credits    theme_boost - MoodleHQ
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die();

$page = new admin_settingpage($themename.'_presets', get_string('presets_settings', 'theme_klassplace'));

// modchooser settings tab.
$page = new admin_settingpage($themename.'_modchooser', get_string('modchoosersettingspage', 'theme_klassplace'));

// Custom Menu label
/*$name = $themename.'/modchoosercustomlabel';
$title = get_string('modchoosercustomlabel', 'theme_klassplace');
$description = get_string('modchoosercustomlabel_desc', 'theme_klassplace');
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);*/

/*$name = $themename.'/commonlyused';
$title = get_string('commonlyused', 'theme_klassplace');
$description = get_string('commonlyused_desc', 'theme_klassplace');
$setting = new admin_setting_configtextarea($name, $title, $description, '', PARAM_RAW);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// show only custom activities/resource
$name = $themename.'/showonlycustomactivities';
$title = get_string('showonlycustomactivities', 'theme_klassplace');
$description = get_string('showonlycustomactivities_desc', 'theme_klassplace');
$default = 0;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);*/

// show only custom activities/resource
/*$name = $themename.'/showalltomanager';
$title = get_string('showalltomanager', 'theme_klassplace');
$description = get_string('showalltomanager_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);*/

// Integration Info.
/*$name = $themename.'/integrationinfo';
$heading = get_string('integrationinfo', 'theme_klassplace');
$information = get_string('integrationinfo_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// Collapsible Topic Course Format https://moodle.org/plugins/format_collapsibletopics.
$name = $themename.'/integrationcollapsibletopics';
$title = get_string('collapsibletopics' , 'theme_klassplace');
$description = get_string('collapsibletopics_desc', 'theme_klassplace');
$integration_on = get_string('integrationon', 'theme_klassplace');
$integration_off = get_string('integrationoff', 'theme_klassplace');
$default = '2';
$choices = array(
    '1' => $integration_on,
    '2' => $integration_off
);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Collapsible Topic Course Format https://moodle.org/plugins/format_collapsibletopics.
$name = $themename.'/easyenrollmentintegration';
$title = get_string('easyenrollmentintegration' , 'theme_klassplace');
$description = get_string('easyenrollmentintegration_desc', 'theme_klassplace');
$integration_on = get_string('integrationon', 'theme_klassplace');
$default = '1';
$choices = array('1'=>$integration_on);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);*/

$settings->add($page);
