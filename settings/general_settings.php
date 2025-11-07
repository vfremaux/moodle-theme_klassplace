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

$ADMIN->add($themename, new admin_category($themename.'_homepage', get_string('homepagesettings', 'theme_klassplace')));

$page = new admin_settingpage($themename.'_general_settings', get_string('general_settings', 'theme_klassplace'));

// Layout Info
$name = $themename.'/layoutinfo';
$heading = get_string('layoutinfo', 'theme_klassplace');
$information = get_string('layoutinfo_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// Toggle Page Layout design
$name = $themename.'/pagelayout';
$title = get_string('pagelayout' , 'theme_klassplace');
$description = get_string('pagelayout_desc', 'theme_klassplace');
$pagelayout2 = get_string('pagelayout2', 'theme_klassplace');
$pagelayout3 = get_string('pagelayout3', 'theme_klassplace');
$default = '1';
$choices = array(
    '2' => $pagelayout2,
    '3' => $pagelayout3,
);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Block and Content widths
$name = $themename.'/blockwidth';
$title = get_string('blockwidth', 'theme_klassplace');
$description = get_string('blockwidth_desc', 'theme_klassplace');
$default = '280px';
$choices = array(
        '180px' => '150px',
        '230px' => '200px',
        '280px' => '250px',
        '305px' => '275px',
        '330px' => '300px',
        '355px' => '325px',
        '380px' => '350px',
        '405px' => '375px',
        '430px' => '400px',
        '455px' => '425px',
        '480px' => '450px',
        '20%' => '20%',
        '25%' => '25%',
        '30%' => '30%',
    );
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/learningcontentvoffset';
$title = get_string('learningcontentvoffset', 'theme_klassplace');
$description = get_string('learningcontentvoffset_desc', 'theme_klassplace');
$default = '125px';
$choices = array(
        '0px' => '0px',
        '25px' => '25px',
        '50px' => '50px',
        '75px' => '75px',
        '100px' => '100px',
        '125px' => '125px',
        '150px' => '150px',
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

$name = $themename.'/learningcontenthpadding';
$title = get_string('learningcontenthpadding', 'theme_klassplace');
$description = get_string('learningcontenthpadding_desc', 'theme_klassplace');
$default = '125px';
$choices = array(
        '0px' => '0px',
        '25px' => '25px',
        '50px' => '50px',
        '75px' => '75px',
        '100px' => '100px',
        '125px' => '125px',
        '150px' => '150px',
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

// Header size setting.
$name = $themename.'/headerimagepadding';
$title = get_string('headerimagepadding', 'theme_klassplace');
$description = get_string('headerimagepadding_desc', 'theme_klassplace');
$default = '200px';
$choices = array(
    '0px' => '0px',
    '25px' => '25px',
    '50px' => '50px',
    '75px' => '75px',
    '100px' => '100px',
    '125px' => '125px',
    '150px' => '150px',
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
    '625px' => '625px',
    '650px' => '650px',
    '675px' => '675px',
    '700px' => '700px',
    '725px' => '725px',
    '750px' => '750px',
    '775px' => '775px',
    '800px' => '800px',
    '10%' => '10%',
    '50%' => '50%',
    '75%' => '75%',
    '100%' => '100%',
    );
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// gutter width.
// OBSOLETE in 41
/*
$name = $themename.'/gutterwidth';
$title = get_string('gutterwidth', 'theme_klassplace');
$description = get_string('gutterwidth_desc', 'theme_klassplace');
$default = '4rem';
$choices = array(
        '0rem' => '0rem',
        '1rem' => '1rem',
        '2rem' => '2rem',
        '3rem' => '3rem',
        '4rem' => '4rem',
        '5rem' => '5rem',
        '6rem' => '6rem',
        '7rem' => '7rem',
        '8rem' => '8rem',
        '9rem' => '9rem',
        '10rem' => '10rem',
        '12rem' => '12rem',
        '14rem' => '14rem',
        '16rem' => '16rem',
        '18rem' => '18rem',
        '20rem' => '20rem',
        '22rem' => '22rem',
        '24rem' => '24rem',
    );
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
*/

// New in 4.1
$name = $themename.'/pagecontentmaxwidth';
$title = get_string('pagecontentmaxwidth', 'theme_klassplace');
$description = get_string('pagecontentmaxwidth_desc', 'theme_klassplace');
$default = '830px';
$choices = array(
        '830px' => '830px (Boost standard)',
        '1024px' => '1024px SXVGA',
        '1200px' => '1200px WUXGA',
        '1600px' => '1600px WQXGA',
        '2160px' => '2160px 4KUHD',
    );
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/borderradius';
$title = get_string('borderradius', 'theme_klassplace');
$description = get_string('borderradius_desc', 'theme_klassplace');
$default = '4px';
$choices = array(
        '0' => '0',
        '2px' => '2px',
        '4px' => '4px',
        '8px' => '8px',
        '12px' => '12px',
        '15px' => '15px',
    );
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/borderbigradius';
$title = get_string('borderbigradius', 'theme_klassplace');
$description = get_string('borderbigradius_desc', 'theme_klassplace');
$default = '4px';
$choices = array(
        '0' => '0',
        '10px' => '10px',
        '15px' => '15px',
        '20px' => '20px',
        '25px' => '25px',
        '30px' => '30px',
        '40px' => '40px',
    );
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/drawermenuinfo';
$heading = get_string('setting_navdrawersettings', 'theme_klassplace');
$information = get_string('setting_navdrawersettings_desc', 'theme_klassplace');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = $themename.'/shownavdrawer';
$title = get_string('shownavdrawer', 'theme_klassplace');
$description = get_string('shownavdrawer_desc', 'theme_klassplace');
$default = true;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/shownavclosed';
$title = get_string('shownavclosed', 'theme_klassplace');
$description = get_string('shownavclosed_desc', 'theme_klassplace');
$default = false;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = $themename.'/custommenupullright';
$title = get_string('custommenupullright', 'theme_klassplace');
$description = get_string('custommenupullright_desc', 'theme_klassplace');
$default = false;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$page->add($setting);

$langstyleoptions = [
    'icons' => get_string('langmenustyleicons', 'theme_klassplace'),
    'dropdown' => get_string('langmenustyledropdown', 'theme_klassplace'),
    'dropdownicons' => get_string('langmenustyledropdownicons', 'theme_klassplace'),
];

$name = $themename.'/langmenustyle';
$title = get_string('langmenustyle', 'theme_klassplace');
$description = get_string('langmenustyle_desc', 'theme_klassplace');
$default = 'icons';
$setting = new admin_setting_configselect($name, $title, $description, $default, $langstyleoptions);
$page->add($setting);

// Must add the page after definiting all the settings!
$settings->add($page);