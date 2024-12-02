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
 * Heading and course images settings page file.
 *
 * @packagetheme_klassplace
 * @copyright  2016 Chris Kenniburg
 * @creditstheme_boost - MoodleHQ
 * @licensehttp://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$page = new admin_settingpage($themename.'_menusettings', get_string('menusettings', 'theme_klassplace'));

// This is the descriptor for Course Management Panel
$name = $themename.'/coursemanagementinfo';
$heading = get_string('coursemanagementinfo', 'theme_klassplace');
$information = get_string('coursemanagementinfo_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// Show/hide coursemanagement slider toggle.
$name = $themename.'/coursemanagementtoggle';
$title = get_string('coursemanagementtoggle', 'theme_klassplace');
$description = get_string('coursemanagementtoggle_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Frontpage Textbox.
/*$name = $themename.'/coursemanagementtextbox';
$title = get_string('coursemanagementtextbox', 'theme_klassplace');
$description = get_string('coursemanagementtextbox_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);*/

// Frontpage Textbox.
/*$name = $themename.'/studentdashboardtextbox';
$title = get_string('studentdashboardtextbox', 'theme_klassplace');
$description = get_string('studentdashboardtextbox_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);*/

// Show/hide course editing cog.
/*$name = $themename.'/showactivitynav';
$title = get_string('showactivitynav', 'theme_klassplace');
$description = get_string('showactivitynav_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);*/

// Show/hide course editing cog.
/*$name = $themename.'/courseeditingcog';
$title = get_string('courseeditingcog', 'theme_klassplace');
$description = get_string('courseeditingcog_desc', 'theme_klassplace');
$default = 0;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);*/

// Show/hide student grades.
$name = $themename.'/showstudentgrades';
$title = get_string('showstudentgrades', 'theme_klassplace');
$description = get_string('showstudentgrades_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Show/hide student completion.
$name = $themename.'/showstudentcompletion';
$title = get_string('showstudentcompletion', 'theme_klassplace');
$description = get_string('showstudentcompletion_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Toggle show only your Group teachers in student course management panel.
$name = $themename.'/showonlygroupteachers';
$title = get_string('showonlygroupteachers', 'theme_klassplace');
$description = get_string('showonlygroupteachers_desc', 'theme_klassplace');
$default = 0;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Show/hide course settings for students.
$name = $themename.'/showcourseadminstudents';
$title = get_string('showcourseadminstudents', 'theme_klassplace');
$description = get_string('showcourseadminstudents_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for course menu
$name = $themename.'/mycoursesmenuinfo';
$heading = get_string('mycoursesinfo', 'theme_klassplace');
$information = get_string('mycoursesinfo_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// Toggle courses display in custommenu.
$name = $themename.'/displaymycourses';
$title = get_string('displaymycourses', 'theme_klassplace');
$description = get_string('displaymycourses_desc', 'theme_klassplace');
$default = false;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/invertshortfullnameinnavbar';
$title = get_string('invertshortfullnameinnavbar', 'theme_klassplace');
$description = get_string('invertshortfullnameinnavbar_desc', 'theme_klassplace');
$default = 0;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$page->add($setting);

// Toggle courses display in custommenu.
$name = $themename.'/displaythiscourse';
$title = get_string('displaythiscourse', 'theme_klassplace');
$description = get_string('displaythiscourse_desc', 'theme_klassplace');
$default = false;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Set terminology for dropdown course list
$name = $themename.'/mycoursetitle';
$title = get_string('mycoursetitle','theme_klassplace');
$description = get_string('mycoursetitle_desc', 'theme_klassplace');
$default = 'course';
$choices = array(
    'course' => get_string('mycourses', 'theme_klassplace'),
    'module' => get_string('mymodules', 'theme_klassplace'),
    'unit' => get_string('myunits', 'theme_klassplace'),
    'class' => get_string('myclasses', 'theme_klassplace'),
    'training' => get_string('mytraining', 'theme_klassplace'),
    'pd' => get_string('myprofessionaldevelopment', 'theme_klassplace'),
    'cred' => get_string('mycred', 'theme_klassplace'),
    'plan' => get_string('myplans', 'theme_klassplace'),
    'comp' => get_string('mycomp', 'theme_klassplace'),
    'program' => get_string('myprograms', 'theme_klassplace'),
    'lecture' => get_string('mylectures', 'theme_klassplace'),
    'lesson' => get_string('mylessons', 'theme_klassplace'),
    );
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

//Drawer Menu
// This is the descriptor for nav drawer
/*$name = $themename.'/drawermenuinfo';
$heading = get_string('setting_navdrawersettings', 'theme_klassplace');
$information = get_string('setting_navdrawersettings_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = $themename.'/shownavdrawer';
$title = get_string('shownavdrawer', 'theme_klassplace');
$description = get_string('shownavdrawer_desc', 'theme_klassplace');
$default = true;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/shownavclosed';
$title = get_string('shownavclosed', 'theme_klassplace');
$description = get_string('shownavclosed_desc', 'theme_klassplace');
$default = false;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/custommenupullright';
$title = get_string('custommenupullright', 'theme_klassplace');
$description = get_string('custommenupullright_desc', 'theme_klassplace');
$default = false;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$page->add($setting);

$langstyleoptions = [
    'icons' => get_string('langmenustyleicons', 'theme_klassplace'),
    'dropdown' => get_string('langmenustyledropdown', 'theme_klassplace'),
    'dropdownicons' => get_string('langmenustyledropdownicons', 'theme_klassplace'),
];

$name = $themename.'/langmenustyle';
$title = get_string('langmenustyle', 'theme_klassplace');
$description = get_string('langmenustyle_desc', 'theme_klassplace');
$default = 'icons';
$setting = new admin_setting_configselect($name, $title, $description, $default, $langstyleoptions);
$page->add($setting);*/


// Must add the page after definiting all the settings!
$settings->add($page);
