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
* @credits    theme_boost - MoodleHQ
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die();

/* Social Network Settings */
$page = new admin_settingpage($themename.'_footer', get_string('footerheading', 'theme_klassplace'));
$page->add(new admin_setting_heading($themename.'_footer', get_string('footerheadingsub', 'theme_klassplace'), format_text(get_string('footer_desc' , 'theme_klassplace'), FORMAT_MARKDOWN)));

$name = $themename.'/leftfooter';
$title = get_string('leftfooter', 'theme_klassplace');
$description = get_string('leftfooter_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$page->add($setting);

$name = $themename.'/midfooter';
$title = get_string('midfooter', 'theme_klassplace');
$description = get_string('midfooter_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$page->add($setting);

$name = $themename.'/rightfooter';
$title = get_string('rightfooter', 'theme_klassplace');
$description = get_string('rightfooter_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$page->add($setting);

$name = $themename.'/footerlogininfo';
$title = get_string('footerlogininfo', 'theme_klassplace');
$description = get_string('footerlogininfo_desc', 'theme_klassplace');
$default = 0;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$page->add($setting);

$name = $themename.'/footerdataprivacy';
$title = get_string('footerdataprivacy', 'theme_klassplace');
$description = get_string('footerdataprivacy_desc', 'theme_klassplace');
$default = 0;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$page->add($setting);

$name = $themename.'/footermobileapp';
$title = get_string('footermobileapp', 'theme_klassplace');
$description = get_string('footermobileapp_desc', 'theme_klassplace');
$default = 0;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$page->add($setting);

// Footnote setting.
$name = $themename.'/footnote';
$title = get_string('footnote', 'theme_klassplace');
$description = get_string('footnote_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Must add the page after definiting all the settings!
$settings->add($page);
