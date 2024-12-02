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

$page = new admin_settingpage($themename.'_homepage_circles', get_string('circlesection', 'theme_klassplace'));

/******************** Circles settings ********************/

$name = $themename.'_circle';
$heading = get_string('circlesettingssub', 'theme_klassplace');
$information = get_string('circlesettingssub_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// circlessectionheading.
$name = $themename.'/circlessectionheading';
$title = get_string('circlessectionheading', 'theme_klassplace');
$description = get_string('circlessectionheading_desc', 'theme_klassplace');
$default = 'Lead Teachers';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// circlessectiontagline.
$name = $themename.'/circlessectiontagline';
$title = get_string('circlessectiontagline', 'theme_klassplace');
$description = get_string('circlessectiontagline_desc', 'theme_klassplace');
$default = 'The names we are proud of';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

if (is_dir($CFG->dirroot.'/local/moodlescript')) {
    include_once($CFG->dirroot.'/local/moodlescript/xlib.php');

    local_moodlescript_add_display_condition($page, $themename.'/circledispcondition', 0);
}

$samples['names'] = [
    'Doris Wilson',
    'John Malcolm',
    'Pete Browning',
    'Sarah Hickock',
    'Dan Arbour',
    'Kobe Kinatwata',
    'Pierre Jordan',
    'Heni Wieldmann',
    'Rose Cannaghan',
    'Sophia Vasquez',
];

for ($i = 1; $i < 10; $i++) {

    $name = 'circle'.$i.'hdr';
    $setting = new admin_setting_heading($name, '<hr>', '');
    $page->add($setting);

    // displaycircle<n> setting.
    $name = $themename.'/displaycircle'.$i;
    $title = get_string('displaycircle','theme_klassplace', $i);
    $description = get_string('displaycircle_desc', 'theme_klassplace');
    $default = 1;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $yesnochoices);
    $page->add($setting);

    // circle<n>image.
    $name = $themename.'/circleimage'.$i;
    $title = get_string('circleimage', 'theme_klassplace', $i);
    $description = get_string('circleimage_desc', 'theme_klassplace');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'circleimage'.$i, 0,
            array('maxfiles' => 1, 'accepted_types' => array('image')));
        $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // circle<n>name.
    $name = $themename.'/circlename'.$i;
    $title = get_string('circlename', 'theme_klassplace', $i);
    $description = get_string('circlename_desc', 'theme_klassplace');
    $default = $samples['names'][$i];
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // circle1url.
    $name = $themename.'circleurl'.$i;
    $title = get_string('circleurl', 'theme_klassplace', $i);
    $description = get_string('circleurl_desc', 'theme_klassplace');
    $default = '#';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // circle<n>designation.
    $name = $themename.'/circledesignation'.$i;
    $title = get_string('circledesignation', 'theme_klassplace', $i);
    $description = get_string('circledesignation_desc', 'theme_klassplace');
    $default = 'Phd, Master';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // circle<n>rating.
    $name = $themename.'/circlerating'.$i;
    $title = get_string('circlerating', 'theme_klassplace', $i);
    $description = get_string('circlerating_desc', 'theme_klassplace');
    $default = '4.9/5';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // circle<n>comment.
    $name = $themename.'/circlecomment'.$i;
    $title = get_string('circlecomment', 'theme_klassplace', $i);
    $description = get_string('circlecomment_desc', 'theme_klassplace');
    $default = 'thank u mam for explaining so well!! and showing ur concern towards me!! :)';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    if (is_dir($CFG->dirroot.'/local/moodlescript')) {
        include_once($CFG->dirroot.'/local/moodlescript/xlib.php');

        local_moodlescript_add_display_condition($page, $themename.'/circledispcondition'.$i, $i);
    }
}

/******************** End circles settings ********************/

$ADMIN->add($themename.'_homepage', $page);
