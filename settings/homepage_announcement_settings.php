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
* Social networking settings page file.
*
* @package    theme_klassplace
* @copyright  2020 Nicolas Maligue
* 
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die();

$page = new admin_settingpage($themename.'_homepage_announcement', get_string('announcementsection', 'theme_klassplace'));

/******************** Announcements setting ********************/

$name = $themename.'_announcement';
$heading = get_string('announcementsettingssub', 'theme_klassplace');
$information = get_string('announcementsettingssub_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// announcementheading.
$name = $themename.'/announcementheading';
$title = get_string('announcementheading', 'theme_klassplace');
$description = get_string('announcementheading_desc', 'theme_klassplace');
$default = 'Powerful & easy to use';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// announcementtagline .
$name = $themename.'/announcementtagline';
$title = get_string('announcementtagline', 'theme_klassplace');
$description = get_string('announcementtagline_desc', 'theme_klassplace');
$default = 'Theme For University, School, eLearning and Online circleial';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// buttonreadmore.
$name = $themename.'/buttonreadmore';
$title = get_string('buttonreadmore', 'theme_klassplace');
$description = get_string('buttonreadmore_desc', 'theme_klassplace');
$default = 'Button 1';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// buttonreadmoreurl.
$name = $themename.'/buttonreadmoreurl';
$title = get_string('buttonreadmoreurl', 'theme_klassplace');
$description = get_string('buttonreadmoreurl_desc', 'theme_klassplace');
$default = '#';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// buttonbuynow.
$name = $themename.'/buttonbuynow';
$title = get_string('buttonbuynow', 'theme_klassplace');
$description = get_string('buttonbuynow_desc', 'theme_klassplace');
$default = 'Button 2';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// buttonbuynowurl.
$name = $themename.'/buttonbuynowurl';
$title = get_string('buttonbuynowurl', 'theme_klassplace');
$description = get_string('buttonbuynowurl_desc', 'theme_klassplace');
$default = '#';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

if (is_dir($CFG->dirroot.'/local/moodlescript')) {
    include_once($CFG->dirroot.'/local/moodlescript/xlib.php');

    local_moodlescript_add_display_condition($page, $themename.'/announcementdispcondition');
}

/******************** End Announcements setting ********************/

$ADMIN->add($themename.'_homepage', $page);
