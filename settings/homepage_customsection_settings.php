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

$page = new admin_settingpage($themename.'_homepage_customsection', get_string('homepagecustomsection', 'theme_klassplace'));


/******************** Customsection settings ********************/

$name = $themename.'_customsection';
$heading = get_string('customsectionsettingssub', 'theme_klassplace');
$information = get_string('customsectionsettingssub_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

//  customsectionsbkg setting.
$name = $themename.'/customsectionsbkg';
$title = get_string('customsectionsbkg','theme_klassplace');
$description = get_string('customsectionsbkg_desc', 'theme_klassplace');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'customsectionsbkg', 0, []);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$yesnochoices = [
    0 => get_string('no'),
    1 => get_string('yes'),
];

$default_icons = [
    'pencil-square',
    'clock-o',
    'check-square',
    'file-text',
];

$samples = [
    'Modern and vibrant design',
    'Interactive and complete communication',
    'Flat, flat, flat...',
    '',
    '',
    '',
    '',
    ''
];

for ($i = 1; $i <= 8; $i++) {

    $name = 'customsection'.$i.'hdr';
    $setting = new admin_setting_heading($name, '<hr>', '');
    $page->add($setting);

    // custombox<n>heading.
    $name = $themename.'/customboxheading'.$i;
    $title = get_string('customboxheading', 'theme_klassplace', $i);
    $description = get_string('customboxheading_desc', 'theme_klassplace');
    $default = $samples[$i - 1];
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $page->add($setting);

    // custombox<n>icon.
    $name = $themename.'/customboxicon'.$i;
    $title = get_string('customboxicon', 'theme_klassplace', $i);
    $description = get_string('customboxicon_desc', 'theme_klassplace');
    $default = @$default_icons[$i - 1];
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $page->add($setting);

    // custombox<n>description.
    $name = $themename.'/customboxdescription'.$i;
    $title = get_string('customboxdescription', 'theme_klassplace', $i);
    $description = get_string('customboxdescription_desc', 'theme_klassplace');
    $default = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod.';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $page->add($setting);

    if (is_dir($CFG->dirroot.'/local/moodlescript')) {
        include_once($CFG->dirroot.'/local/moodlescript/xlib.php');

        local_moodlescript_add_display_condition($page, $themename.'/customsectiondispcondition'.$i, $i);
    }
}

/******************** End Customsection setting ********************/

$ADMIN->add($themename.'_homepage', $page);
