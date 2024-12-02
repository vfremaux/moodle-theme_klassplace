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

$page = new admin_settingpage($themename.'_homepage_slider', get_string('slidersection', 'theme_klassplace'));

/******************** Slider setting ********************/

$name = $themename.'_slider';
$heading = get_string('slidersettingssub', 'theme_klassplace');
$information = get_string('slidersettingssub_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// Header size setting.
$name = $themename.'/slideshowheight';
$title = get_string('slideshowheight', 'theme_klassplace');
$description = get_string('slideshowheight_desc', 'theme_klassplace');
$default = '250px';
$choices = array(
        '175px' => '175px',
        '200px' => '200px',
        '225px' => '225px',
        '250px' => '250px',
        '275px' => '275px',
        '300px' => '300px',
        '325px' => '325px',
        '350px' => '350px',
        '375px' => '375px',
        '400px' => '400px',
        '425px' => '425px',
        '450px' => '450px',
        '475px' => '475px',
        '500px' => '500px',
        '525px' => '525px',
        '550px' => '550px',
        '575px' => '575px',
        '600px' => '600px',
    );
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

if (is_dir($CFG->dirroot.'/local/moodlescript')) {
    include_once($CFG->dirroot.'/local/moodlescript/xlib.php');

    local_moodlescript_add_display_condition($page, $themename.'/sliderdispcondition', $i);
}


$samples['caption'] = [
    'Campus Is An Online Platform',
    'Get all your courses available ever',
    'Learn, interact, become stronger',
    '',
    '',
    '',
    '',
    '',
    '',
    ''
];

$samples['heading'] = [
    'Designed to Make Learning',
    'All time, from everywhere',
    'Take power in the digital world',
    '',
    '',
    '',
    '',
    '',
    '',
    ''
];

for ($i = 1; $i <= 6; $i++) {

    $name = 'slider'.$i.'hdr';
    $setting = new admin_setting_heading($name, '<hr>', '');
    $page->add($setting);

    // sliderimage<n> setting.
    $name = $themename.'/sliderimage'.$i;
    $title = get_string('sliderimage','theme_klassplace', $i);
    $description = get_string('sliderimage_desc', 'theme_klassplace');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'sliderimage'.$i);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // slidercaption<n>.
    $name = $themename.'/slidercaption'.$i;
    $title = get_string('slidercaption', 'theme_klassplace', $i);
    $description = get_string('slidercaption_desc', 'theme_klassplace');
    $default = @$samples['caption'][$i - 1];
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // slidercaptionurl<n>.
    $name = $themename.'/slidercaptionurl'.$i;
    $title = get_string('slidercaptionurl', 'theme_klassplace', $i);
    $description = get_string('slidercaptionurl_desc', 'theme_klassplace');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // sliderheading<n>.
    $name = $themename.'/sliderheading'.$i;
    $title = get_string('sliderheading', 'theme_klassplace', $i);
    $description = get_string('sliderheading_desc', 'theme_klassplace');
    $default = $samples['heading'][$i - 1];
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    if (is_dir($CFG->dirroot.'/local/moodlescript')) {
        include_once($CFG->dirroot.'/local/moodlescript/xlib.php');

        local_moodlescript_add_display_condition($page, $themename.'/sliderdispcondition'.$i, $i);
    }
}

/******************** End Slider setting ********************/

$ADMIN->add($themename.'_homepage', $page);
