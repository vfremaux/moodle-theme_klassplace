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
 * Indicators settings page file.
 * Dependancies : Indicators panel uses block_course_notification cold form features to get
 * global indicators results. Further developments may allow other sources of global indicators
 * to append values to this panel.
 *
 * @package    theme_klassplace
 * @copyright  2022 Valery Fremaux
 * 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$page = new admin_settingpage($themename.'_homepage_indicators', get_string('indicatorssection', 'theme_klassplace'));

/******************** Indicators setting ********************/

$name = $themename.'_indicators';
$heading = get_string('indicatorssettingssub', 'theme_klassplace');
$information = get_string('indicatorssettingssub_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// tagline.
$name = $themename.'/indicatorstagline';
$title = get_string('indicatorstagline', 'theme_klassplace');
$description = get_string('indicatorstagline_desc', 'theme_klassplace');
$default = 'what they say to us!!!';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// heading.
$name = $themename.'/indicatorsheading';
$title = get_string('indicatorsheading', 'theme_klassplace');
$description = get_string('indicatorsheading_desc', 'theme_klassplace');
$default = 'Learners feedback';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// description
$name = $themename.'/indicatorsdescription';
$title = get_string('indicatorsdescription', 'theme_klassplace');
$description = get_string('indicatorsdescription_desc', 'theme_klassplace');
$default = ".";
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// indicatorsimage.
$name = $themename.'/indicatorsimage';
$title = get_string('indicatorsimage','theme_klassplace');
$description = get_string('indicatorsimage_desc', 'theme_klassplace');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'indicatorsimage');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$indicatorsamples = [
    "Global satisfaction",
    "Course outcomes success",
    "Trained People",
    "Courses delivered",
    "",
    "",
];

$indicatorsampledefaults = [
    98,
    80,
    1000,
    $DB->count_records('course', ['visible' => 1]),
    "",
    "",
];

for ($i = 1; $i <= 6; $i++) {

    $name = 'indicator'.$i.'hdr';
    $setting = new admin_setting_heading($name, '<hr>', '');
    $page->add($setting);

    // list<n>.
    $name = $themename.'/indicatortitle'.$i;
    $title = get_string('indicatortitle', 'theme_klassplace', $i);
    $description = get_string('indicatortitle_desc', 'theme_klassplace');
    $default = $indicatorsamples[$i - 1];
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // graphindex<n>.
    /* 
     * Note : expected syntax : pluginname:numericindex
     * Ex: block_course_notification:1
     * Would fetch the first indicator result through the "standardized" function :
     * /blocks/course_notification/xlib.php#block_course_notification_get_indicator(1);
     */
    $name = $themename.'/indicatorgraphindex'.$i;
    $title = get_string('indicatorgraphindex', 'theme_klassplace', $i);
    $description = get_string('indicatorgraphindex_desc', 'theme_klassplace');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $page->add($setting);

    // override<n>.
    $name = $themename.'/indicatoroverride'.$i;
    $title = get_string('indicatoroverride', 'theme_klassplace', $i);
    $description = get_string('indicatoroverride_desc', 'theme_klassplace');
    $default = $indicatorsampledefaults[$i - 1];
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $page->add($setting);

    // alturl<n>.
    $name = $themename.'/indicatoralturl'.$i;
    $title = get_string('indicatoralturl', 'theme_klassplace', $i);
    $description = get_string('indicatoralturl_desc', 'theme_klassplace');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $page->add($setting);

    if (is_dir($CFG->dirroot.'/local/moodlescript')) {
        include_once($CFG->dirroot.'/local/moodlescript/xlib.php');

        local_moodlescript_add_display_condition($page, $themename.'/indicatordispcondition'.$i, $i);
    }
}

/******************** End Indicators settings ********************/

$ADMIN->add($themename.'_homepage', $page);
