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
$page = new admin_settingpage($themename.'_iconnavheading', get_string('iconnavheading', 'theme_klassplace'));

// This is the descriptor for icon One
/*$name = $themename.'/iconwidthinfo';
$title = get_string('iconwidthinfo', 'theme_klassplace');
$description = get_string('iconwidthinfo_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $title, $description);
$page->add($setting);*/

// Icon width setting.
$name = $themename.'/iconwidth';
$title = get_string('iconwidth', 'theme_klassplace');
$description = get_string('iconwidth_desc', 'theme_klassplace');
$default = '100px';
$choices = array(
    '75px' => '75px',
    '85px' => '85px',
    '95px' => '95px',
    '100px' => '100px',
    '105px' => '105px',
    '110px' => '110px',
    '115px' => '115px',
    '120px' => '120px',
    '125px' => '125px',
    '130px' => '130px',
    '135px' => '135px',
    '140px' => '140px',
    '145px' => '145px',
    '150px' => '150px',
);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/sliderinfo';
$title = get_string('sliderinfo', 'theme_klassplace');
$description = get_string('sliderinfo_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $title, $description);
$page->add($setting);

// Creator Icon
$name = $themename.'/slideicon';
$title = get_string('navicon', 'theme_klassplace');
$description = get_string('naviconslide_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/slideiconbuttontext';
$title = get_string('naviconbuttontext', 'theme_klassplace');
$description = get_string('naviconbuttontext_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Slide Textbox.
$name = $themename.'/slidetextbox';
$title = get_string('slidetextbox', 'theme_klassplace');
$description = get_string('slidetextbox_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon One
$name = $themename.'/navicon1info';
$title = get_string('navicon1', 'theme_klassplace');
$description = get_string('navicon_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $title, $description);
$page->add($setting);

// icon One
$name = $themename.'/nav1icon';
$title = get_string('navicon', 'theme_klassplace');
$description = get_string('navicon_desc', 'theme_klassplace');
$default = 'home';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/nav1buttontext';
$title = get_string('naviconbuttontext', 'theme_klassplace');
$description = get_string('naviconbuttontext_desc', 'theme_klassplace');
$default = get_string('naviconbutton1textdefault', 'theme_klassplace');
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/nav1buttonurl';
$title = get_string('naviconbuttonurl', 'theme_klassplace');
$description = get_string('naviconbuttonurl_desc', 'theme_klassplace');
$default =  $CFG->wwwroot.'/my/';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon One
$name = $themename.'/navicon2info';
$title = get_string('navicon2', 'theme_klassplace');
$description = get_string('navicon_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $title, $description);
$page->add($setting);

$name = $themename.'/nav2icon';
$title = get_string('navicon', 'theme_klassplace');
$description = get_string('navicon_desc', 'theme_klassplace');
$default = 'calendar';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/nav2buttontext';
$title = get_string('naviconbuttontext', 'theme_klassplace');
$description = get_string('naviconbuttontext_desc', 'theme_klassplace');
$default = get_string('naviconbutton2textdefault', 'theme_klassplace');
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/nav2buttonurl';
$title = get_string('naviconbuttonurl', 'theme_klassplace');
$description = get_string('naviconbuttonurl_desc', 'theme_klassplace');
$default =  $CFG->wwwroot.'/calendar/view.php?view=month';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon three
$name = $themename.'/navicon3info';
$title = get_string('navicon3', 'theme_klassplace');
$description = get_string('navicon_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $title, $description);
$page->add($setting);

$name = $themename.'/nav3icon';
$title = get_string('navicon', 'theme_klassplace');
$description = get_string('navicon_desc', 'theme_klassplace');
$default = 'bookmark';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/nav3buttontext';
$title = get_string('naviconbuttontext', 'theme_klassplace');
$description = get_string('naviconbuttontext_desc', 'theme_klassplace');
$default = get_string('naviconbutton3textdefault', 'theme_klassplace');
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/nav3buttonurl';
$title = get_string('naviconbuttonurl', 'theme_klassplace');
$description = get_string('naviconbuttonurl_desc', 'theme_klassplace');
$default =  $CFG->wwwroot.'/badges/mybadges.php';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon four
$name = $themename.'/navicon4info';
$title = get_string('navicon4', 'theme_klassplace');
$description = get_string('navicon_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $title, $description);
$page->add($setting);

$name = $themename.'/nav4icon';
$title = get_string('navicon', 'theme_klassplace');
$description = get_string('navicon_desc', 'theme_klassplace');
$default = 'book';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/nav4buttontext';
$title = get_string('naviconbuttontext', 'theme_klassplace');
$description = get_string('naviconbuttontext_desc', 'theme_klassplace');
$default = get_string('naviconbutton4textdefault', 'theme_klassplace');
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/nav4buttonurl';
$title = get_string('naviconbuttonurl', 'theme_klassplace');
$description = get_string('naviconbuttonurl_desc', 'theme_klassplace');
$default =  $CFG->wwwroot.'/course/';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon four
$name = $themename.'/navicon5info';
$title = get_string('navicon5', 'theme_klassplace');
$description = get_string('navicon_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $title, $description);
$page->add($setting);

$name = $themename.'/nav5icon';
$title = get_string('navicon', 'theme_klassplace');
$description = get_string('navicon_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/nav5buttontext';
$title = get_string('naviconbuttontext', 'theme_klassplace');
$description = get_string('naviconbuttontext_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/nav5buttonurl';
$title = get_string('naviconbuttonurl', 'theme_klassplace');
$description = get_string('naviconbuttonurl_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon six
$name = $themename.'/navicon6info';
$title = get_string('navicon6', 'theme_klassplace');
$description = get_string('navicon_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $title, $description);
$page->add($setting);

$name = $themename.'/nav6icon';
$title = get_string('navicon', 'theme_klassplace');
$description = get_string('navicon_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/nav6buttontext';
$title = get_string('naviconbuttontext', 'theme_klassplace');
$description = get_string('naviconbuttontext_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/nav6buttonurl';
$title = get_string('naviconbuttonurl', 'theme_klassplace');
$description = get_string('naviconbuttonurl_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon seven
$name = $themename.'/navicon7info';
$title = get_string('navicon7', 'theme_klassplace');
$description = get_string('navicon_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $title, $description);
$page->add($setting);

$name = $themename.'/nav7icon';
$title = get_string('navicon', 'theme_klassplace');
$description = get_string('navicon_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/nav7buttontext';
$title = get_string('naviconbuttontext', 'theme_klassplace');
$description = get_string('naviconbuttontext_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/nav7buttonurl';
$title = get_string('naviconbuttonurl', 'theme_klassplace');
$description = get_string('naviconbuttonurl_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon eight
$name = $themename.'/navicon8info';
$title = get_string('navicon8', 'theme_klassplace');
$description = get_string('navicon_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $title, $description);
$page->add($setting);

$name = $themename.'/nav8icon';
$title = get_string('navicon', 'theme_klassplace');
$description = get_string('navicon_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/nav8buttontext';
$title = get_string('naviconbuttontext', 'theme_klassplace');
$description = get_string('naviconbuttontext_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/nav8buttonurl';
$title = get_string('naviconbuttonurl', 'theme_klassplace');
$description = get_string('naviconbuttonurl_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/enhancedmydashboard';
$title = get_string('enhancedmydashboard', 'theme_klassplace');
$description = get_string('enhancedmydashboard_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Must add the page after definiting all the settings!
$settings->add($page);
