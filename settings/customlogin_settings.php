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
* @copyright  2016 Chris Kenniburg
* 
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die();

// Icon Navigation);
$page = new admin_settingpage($themename.'_customlogin', get_string('customloginheading', 'theme_klassplace'));

$name = $themename.'/customlogininfo';
$title = get_string('customlogininfo', 'theme_klassplace');
$description = get_string('customlogininfo_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $title, $description);
$page->add($setting);

// Show password button.
$name = $themename.'/showpasswordbutton';
$title = get_string('showpasswordbutton', 'theme_klassplace');
$description = get_string('showpasswordbutton_desc', 'theme_klassplace');
$default = 0;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$page->add($setting);

// login helpbutton
$name = $themename.'/loginhelpbutton';
$title = get_string('loginhelpbutton', 'theme_klassplace');
$description = get_string('loginhelpbutton_desc', 'theme_klassplace');
$default = get_string('helpbuttondefaulttext', 'theme_klassplace');
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

$name = $themename.'/loginhelpbuttonurl';
$title = get_string('loginhelpbuttonurl', 'theme_klassplace');
$description = get_string('loginhelpbutton_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// Top image.
$name = $themename.'/logintopimage';
$title = get_string('logintopimage', 'theme_klassplace');
$description = get_string('logintopimage_desc', 'theme_klassplace');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'logintopimage');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Login form color.
$name = $themename.'/fploginformbkg';
$title = get_string('fploginformbkg', 'theme_klassplace');
$description = get_string('fploginformbkg_desc', 'theme_klassplace');
$default = '#ffffff';
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Login form color.
$name = $themename.'/fploginformfg';
$title = get_string('fploginformfg', 'theme_klassplace');
$description = get_string('fploginformfg_desc', 'theme_klassplace');
$default = '#000000';
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Content Info.
$name = $themename.'/textcontentinfo';
$title = get_string('textcontentinfo', 'theme_klassplace');
$description = get_string('textcontentinfo_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $title, $description);
$page->add($setting);


// Alert setting.
$name = $themename.'/alertbox';
$title = get_string('alertbox', 'theme_klassplace');
$description = '';
$setting = new admin_setting_heading($name, $title, $description);
$page->add($setting);

// Alert setting.
$name = $themename.'/alertboxmessage';
$title = get_string('alertboxmessage', 'theme_klassplace');
$description = get_string('alertboxmessage_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);


$name = $themename.'/alertlink';
$title = get_string('alertlink', 'theme_klassplace');
$description = get_string('alertlink_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

$name = $themename.'/alertlinkurl';
$title = get_string('alertlinkurl', 'theme_klassplace');
$description = get_string('alertlinkurl_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// Default background image.
$name = $themename.'/alertimage';
$title = get_string('alertimage', 'theme_klassplace');
$description = get_string('alertimage_desc', 'theme_klassplace');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'alertimage');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/alertimagelinkurl';
$title = get_string('alertimagelinkurl', 'theme_klassplace');
$description = get_string('alertimagelinkurl_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);


// Must add the page after definiting all the settings!
$settings->add($page);
