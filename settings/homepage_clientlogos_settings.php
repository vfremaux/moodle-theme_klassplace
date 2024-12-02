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

$page = new admin_settingpage($themename.'_homepage_clientlogos', get_string('clientlogossection', 'theme_klassplace'));

/******************** Clientlogo settings ********************/

$name = $themename.'_clientlogo';
$heading = get_string('clientlogosettingssub', 'theme_klassplace');
$information = get_string('clientlogosettingssub_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

for ($i = 1; $i < 10; $i++) {
    // clientlogo<n>.
    $name = $themename.'/clientlogo'.$i;
    $title = get_string('clientlogo', 'theme_klassplace', $i);
    $description = get_string('clientlogo_desc', 'theme_klassplace');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'clientlogo'.$i);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // clientlogourl<n>.
    $name = $themename.'/clientlogourl'.$i;
    $title = get_string('clientlogourl', 'theme_klassplace', $i);
    $description = get_string('clientlogourl_desc', 'theme_klassplace');
    $setting = new admin_setting_configtext($name, $title, $description, 'clientlogourl'.$i);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    if (is_dir($CFG->dirroot.'/local/moodlescript')) {
        include_once($CFG->dirroot.'/local/moodlescript/xlib.php');

        local_moodlescript_add_display_condition($page, $themename.'/logosdispcondition'.$i, $i);
    }
}

/******************** End Clientlogo settings ********************/

$ADMIN->add($themename.'_homepage', $page);
