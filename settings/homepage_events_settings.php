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

$page = new admin_settingpage($themename.'_homepage_events', get_string('eventsection', 'theme_klassplace'));

/******************** Events setting ********************/

$name = $themename.'_event';
$heading = get_string('eventsettingssub', 'theme_klassplace');
$information = get_string('eventsettingssub_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// eventtagline .
$name = $themename.'/eventtagline';
$title = get_string('eventtagline', 'theme_klassplace');
$description = get_string('eventtagline_desc', 'theme_klassplace');
$default = 'ALL ABOUT EVENTS UPDATES';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// eventheading .
$name = $themename.'/eventheading';
$title = get_string('eventheading', 'theme_klassplace');
$description = get_string('eventheading_desc', 'theme_klassplace');
$default = 'Upcoming Events';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// event background image setting.
$name = $themename.'/eventbkgimage';
$title = get_string('eventbkgimage','theme_klassplace');
$description = get_string('eventbkgimage_desc', 'theme_klassplace');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'eventbkgimage');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// event default image setting for events.
$name = $themename.'/eventdefaultimage';
$title = get_string('eventdefaultimage','theme_klassplace');
$description = get_string('eventdefaultimage_desc', 'theme_klassplace');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'eventdefaultimage');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

for ($i = 1; $i < 10; $i++) {

    $name = 'event'.$i.'hdr';
    $setting = new admin_setting_heading($name, '<hr>', '');
    $page->add($setting);

    // displayevent<n>box setting.
    $name = $themename.'/displayeventbox'.$i;
    $title = get_string('displayeventbox','theme_klassplace', $i);
    $description = get_string('displayeventbox_desc', 'theme_klassplace');
    $default = 1;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $yesnochoices);
    $page->add($setting);

    // event<n>image setting.
    $name = $themename.'/eventimage'.$i;
    $title = get_string('eventimage','theme_klassplace', $i);
    $description = get_string('eventimage_desc', 'theme_klassplace');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'eventimage'.$i);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // event<n>day.
    $name = $themename.'/eventday'.$i;
    $title = get_string('eventday', 'theme_klassplace', $i);
    $description = get_string('eventday_desc', 'theme_klassplace');
    $default = '31';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // event<n>monthyear.
    $name = $themename.'/eventmonthyear'.$i;
    $title = get_string('eventmonthyear', 'theme_klassplace', $i);
    $description = get_string('eventmonthyear_desc', 'theme_klassplace');
    $default = 'Dec 2017';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // event<n>title.
    $name = $themename.'/eventtitle'.$i;
    $title = get_string('eventtitle', 'theme_klassplace', $i);
    $description = get_string('eventtitle_desc', 'theme_klassplace');
    $default = 'Art Open Day';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // event<n>titleurl.
    $name = $themename.'/eventtitleurl'.$i;
    $title = get_string('eventtitleurl', 'theme_klassplace', $i);
    $description = get_string('eventtitleurl_desc', 'theme_klassplace');
    $default = '#';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // event<n>location.
    $name = $themename.'/eventlocation'.$i;
    $title = get_string('eventlocation', 'theme_klassplace', $i);
    $description = get_string('eventlocation_desc', 'theme_klassplace');
    $default = 'Paris, France';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // event<n>description.
    $name = $themename.'/eventdescription'.$i;
    $title = get_string('eventdescription', 'theme_klassplace', $i);
    $description = get_string('eventdescription_desc', 'theme_klassplace');
    $default = 'There are many variations of passages of Lorem Ipsum available…';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // event<n>viewmap.
    $name = $themename.'/eventviewmap'.$i;
    $title = get_string('eventviewmap', 'theme_klassplace', $i);
    $description = get_string('eventviewmap_desc', 'theme_klassplace');
    $default = 'View Map';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // event<n>viewmapurl.
    $name = $themename.'/eventviewmapurl'.$i;
    $title = get_string('eventviewmapurl', 'theme_klassplace', $i);
    $description = get_string('eventviewmapurl_desc', 'theme_klassplace');
    $default = 'https://www.google.com/maps';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // event<n>price.
    $name = $themename.'/eventprice'.$i;
    $title = get_string('eventprice', 'theme_klassplace', $i);
    $description = get_string('eventprice_desc', 'theme_klassplace');
    $default = '$99';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    if (is_dir($CFG->dirroot.'/local/shop')) {
        // event<n>shoplink.
        $name = $themename.'/eventshopurl'.$i;
        $title = get_string('eventshopurl', 'theme_klassplace', $i);
        $description = get_string('eventshopurl_desc', 'theme_klassplace');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $page->add($setting);
    }

    if (is_dir($CFG->dirroot.'/local/moodlescript')) {
        include_once($CFG->dirroot.'/local/moodlescript/xlib.php');

        local_moodlescript_add_display_condition($page, $themename.'/eventdispcondition'.$i, $i);
    }
}

// all event link.
$name = $themename.'/eventlinkurl';
$title = get_string('eventlinkurl', 'theme_klassplace');
$description = get_string('eventlinkurl_desc', 'theme_klassplace');
$default = '#';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

/******************** End Events settings ********************/

$ADMIN->add($themename.'_homepage', $page);
