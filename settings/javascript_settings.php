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
 * Theme variants settings page file.
 *
 * @packagetheme_klassplace
 * @copyright  2016 Chris Kenniburg
 * @creditstheme_klassplace - MoodleHQ
 * @licensehttp://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$page = new admin_settingpage($themename.'_javascript', get_string('js_settings', 'theme_klassplace'));

$name = $themename.'_jsheading';
$title = get_string('jsheadingsub', 'theme_klassplace');
$description = format_text(get_string('jsheading_desc', 'theme_klassplace'), FORMAT_MARKDOWN);
$headersetting = new admin_setting_heading($name, $title, $description);
$page->add($headersetting);

$name = $themename.'/additionaljs';
$title = get_string('additionaljs', 'theme_klassplace');
$description = get_string('additionaljs_desc', 'theme_klassplace');
$options = ['maxfiles' => -1, 'accepted_types' => ['.js']];
$setting = new admin_setting_configstoredfile($name, $title, $description, 'additionaljs', 0, $options);
$page->add($setting);

$name = $themename.'/pagetyperestrictions';
$title = get_string('pagetyperestrictions', 'theme_klassplace');
$description = get_string('pagetyperestrictions_desc', 'theme_klassplace');
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT, 80);
$page->add($setting);

$name = $themename.'_jsextraheading';
$title = get_string('jsextrabehaviourheadingsub', 'theme_klassplace');
$description = '';
$headersetting = new admin_setting_heading($name, $title, $description);
$page->add($headersetting);

$name = $themename.'/allowblockregionscollapse';
$title = get_string('allowblockregionscollapse', 'theme_klassplace');
$description = get_string('allowblockregionscollapse_desc', 'theme_klassplace');
$default = 0;
$choices = [
    0 => get_string('collapsedisabled', 'theme_klassplace'),
    1 => get_string('collapsefirstvisible', 'theme_klassplace'),
    2 => get_string('collapseallcollapsed', 'theme_klassplace'),
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$page->add($setting);

// Must add the page after definition all the settings!
$settings->add($page);
