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

$page = new admin_settingpage($themename.'_homepage_academics', get_string('academicssection', 'theme_klassplace'));

/******************** Academics setting ********************/

$name = $themename.'_academics';
$heading = get_string('academicssettingssub', 'theme_klassplace');
$information = get_string('academicssettingssub_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// academicstagline.
$name = $themename.'/academicstagline';
$title = get_string('academicstagline', 'theme_klassplace');
$description = get_string('academicstagline_desc', 'theme_klassplace');
$default = 'academics There!';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// academicsheading.
$name = $themename.'/academicsheading';
$title = get_string('academicsheading', 'theme_klassplace');
$description = get_string('academicsheading_desc', 'theme_klassplace');
$default = 'academics to Klassplace';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// academicsdescription
$name = $themename.'/academicsdescription';
$title = get_string('academicsdescription', 'theme_klassplace');
$description = get_string('academicsdescription_desc', 'theme_klassplace');
$default = "There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable.";
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// usenum
$name = $themename.'/academicsusenums';
$title = get_string('academicsusenumlist', 'theme_klassplace');
$description = get_string('academicsusenumlist_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$page->add($setting);

// academicsimage.
$name = $themename.'/academicsimage';
$title = get_string('academicsimage','theme_klassplace');
$description = get_string('academicsimage_desc', 'theme_klassplace');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'academicsimage');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$samples = [
    '20 Years Of Educational Experience',
    '',
    '',
    '',
    '',
    ''
];

for ($i = 1; $i <= 6; $i++) {

    $name = 'academics'.$i.'hdr';
    $setting = new admin_setting_heading($name, '<hr>', '');
    $page->add($setting);

    // academicslist<n>.
    $name = $themename.'/academicslist'.$i;
    $title = get_string('academicslist', 'theme_klassplace', $i);
    $description = get_string('academicslist_desc', 'theme_klassplace');
    $default = $samples[$i - 1];
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // academicslisturl<n>.
    $name = $themename.'/academicslisturl'.$i;
    $title = get_string('academicslisturl', 'theme_klassplace', $i);
    $description = get_string('academicslisturl_desc', 'theme_klassplace');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $page->add($setting);

    if (is_dir($CFG->dirroot.'/local/moodlescript')) {
        include_once($CFG->dirroot.'/local/moodlescript/xlib.php');

        local_moodlescript_add_display_condition($page, $themename.'/academicsdispcondition'.$i, $i);
    }
}

/******************** End Academics settings ********************/

$ADMIN->add($themename.'_homepage', $page);
