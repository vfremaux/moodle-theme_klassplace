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
 * @copyright  2021 Nicolas Maligue
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$page = new admin_settingpage($themename.'_courses_settings', get_string('courses_settings', 'theme_klassplace'));

$name = $themename.'_showcoursesheader';
$title = get_string('showcoursesheadersub', 'theme_klassplace');
$description = format_text(get_string('showcoursesheader_desc', 'theme_klassplace'), FORMAT_MARKDOWN);
$headersetting = new admin_setting_heading($name, $title, $description);
$page->add($headersetting);


// Toggle topic/weekly Section Layout design
$name = $themename.'/sectionlayout';
$title = get_string('sectionlayout' , 'theme_klassplace');
$description = get_string('sectionlayout_desc', 'theme_klassplace');
$sectionlayout1 = get_string('sectionlayout1', 'theme_klassplace');
$sectionlayout2 = get_string('sectionlayout2', 'theme_klassplace');
$sectionlayout4 = get_string('sectionlayout4', 'theme_klassplace');
$sectionlayout6 = get_string('sectionlayout6', 'theme_klassplace');
$sectionlayout8 = get_string('sectionlayout8', 'theme_klassplace');

$default = '2';
$choices = array(
    '1' => $sectionlayout1,
    '2' => $sectionlayout2,
    '4' => $sectionlayout4,
    '6' => $sectionlayout6,
    '8' => $sectionlayout8);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/activityiconsize';
$title = get_string('activityiconsize', 'theme_klassplace');
$description = get_string('activityiconsize_desc', 'theme_klassplace');
$default = '32px';
$choices = array(
        '24px' => '24px',
        '28px' => '28px',
        '32px' => '32px',
        '36px' => '36px',
        '40px' => '40px',
        '44px' => '44px',
        '48px' => '48px',
        '52px' => '52px',
        '56px' => '56px',
        '60px' => '60px',
        '64px' => '64px',
    );
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Activity customized icon with.
$name = $themename.'/activitycustomiconwidth';
$title = get_string('activitycustomiconwidth', 'theme_klassplace');
$description = get_string('activitycustomiconwidth_desc', 'theme_klassplace');
$default = 0;
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

$name = $themename.'_showcoursesthumbnailheader';
$title = get_string('showcoursesthumbnailheadersub', 'theme_klassplace');
$description = format_text(get_string('showcoursesthumbnailheader_desc', 'theme_klassplace'), FORMAT_MARKDOWN);
$headersetting = new admin_setting_heading($name, $title, $description);
$page->add($headersetting);

$name = $themename.'/titletooltip';
$title = get_string('titletooltip', 'theme_klassplace');
$description = get_string('titletooltip_desc', 'theme_klassplace');
$default = 0;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);


// Signal new items. Not yet supported in 4.1
/*
$name = $themename.'/signalitemsnewerthan';
$title = get_string('signalitemsnewerthan', 'theme_klassplace');
$description = get_string('signalitemsnewerthan_desc', 'theme_klassplace');
$default = 0;
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// Signal new items on labels.
$name = $themename.'/allownewsignalonlabels';
$title = get_string('allownewsignalonlabels', 'theme_klassplace');
$description = get_string('allownewsignalonlabels_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$page->add($setting);
*/

// Initial flex section state.
$name = $themename.'/flexinitialstate';
$title = get_string('flexinitialstate', 'theme_klassplace');
$description = get_string('flexinitialstate_desc', 'theme_klassplace');
$default = 'collapsed';
$options = array(
    'collapsed' => get_string('flexcollapsed', 'theme_klassplace'),
    'expanded' => get_string('flexexpanded', 'theme_klassplace'),
    'reset' => get_string('flexreset', 'theme_klassplace')
);
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$page->add($setting);

// Must add the page after definiting all the settings!
$settings->add($page);