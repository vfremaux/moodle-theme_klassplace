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

require_once($CFG->dirroot.'/theme/klassplace/lib/klassplace_lib.php');

$page = new admin_settingpage($themename.'_homepage_structure', get_string('homepagestructure', 'theme_klassplace'));

$widgets = [
    '' => '---',
//    'alertbox' => get_string('alertbox', 'theme_klassplace'),
    'announcement' => get_string('announcementsection', 'theme_klassplace'),
    'academics' => get_string('academicssection', 'theme_klassplace').' 1',
    'academics2' => get_string('academicssection', 'theme_klassplace').' 2',
    'circles' => get_string('circlesection', 'theme_klassplace'),
    'clientlogos' => get_string('clientlogossection', 'theme_klassplace'),
    'customsection' => get_string('customsection', 'theme_klassplace'),
    'events' => get_string('eventsection', 'theme_klassplace'),
    'indicators' => get_string('indicatorssection', 'theme_klassplace'),
    'legacy' => get_string('legacymaincontent', 'theme_klassplace'),
//    'featured' => get_string('featuredsection', 'theme_klassplace'),
//    'categories' => get_string('categoriessection', 'theme_klassplace'),
    'slider' => get_string('slidersection', 'theme_klassplace'),
    'social' => get_string('socialsection', 'theme_klassplace'),
    'preblocks' => get_string('preblocks', 'theme_klassplace')
];

$defaults['unconnected'] = [
    'slider',
    'announcement',
    'academics',
    'events',
    'customsection',
    'circles',
    'legacy',
    'clientlogos',
    '',
    '',
];

$defaults['connected'] = [
    'slider',
    'events',
    'academics2',
    'customsection',
    'legacy',
    'clientlogos',
    '',
    '',
    '',
    '',
    '',
];

$defaults['my'] = [
    'legacy',
    'clientlogos',
    'events',
    '',
    '',
];

$defaults['incourse'] = [
    'legacy',
    'events',
    '',
    '',
    ''
];

$key = $themename.'/homepage_structure_uncon_hdr';
$label = get_string('notconnected', 'theme_klassplace');
$description = get_string('notconnected_desc' , 'theme_klassplace');
$page->add(new admin_setting_heading($key, $label, $description));

// unconnected.
for ($i = 1; $i <= KLASSPLACE_FRONTPAGE_UNCON_SLOTS; $i++) {
    $name = $themename.'/homepage_structure_uncon'.$i;
    $title = get_string('slot', 'theme_klassplace', $i);
    $description = '';
    $default = $defaults['unconnected'][$i - 1];
    $setting = new admin_setting_configselect($name, $title, $description, $default, $widgets);
    $page->add($setting);
}

$key = $themename.'/homepage_structure_con_hdr';
$label = get_string('connected', 'theme_klassplace');
$description = get_string('connected_desc' , 'theme_klassplace');
$page->add(new admin_setting_heading($key, $label, $description));

// connected.
for ($i = 1; $i <= KLASSPLACE_FRONTPAGE_CON_SLOTS; $i++) {
    $name = $themename.'/homepage_structure_con'.$i;
    $title = get_string('slot', 'theme_klassplace', $i);
    $description = '';
    $default = $defaults['connected'];
    $default = $defaults['connected'][$i - 1];
    $setting = new admin_setting_configselect($name, $title, $description, $default, $widgets);
    $page->add($setting);
}

$key = $themename.'/homepage_structure_my_hdr';
$label = get_string('my', 'theme_klassplace');
$description = get_string('my_desc' , 'theme_klassplace');
$page->add(new admin_setting_heading($key, $label, $description));

// my.
for ($i = 1; $i <= KLASSPLACE_MY_SLOTS; $i++) {
    $name = $themename.'/homepage_structure_my'.$i;
    $title = get_string('slot', 'theme_klassplace', $i);
    $description = '';
    $default = $defaults['my'][$i - 1];
    $setting = new admin_setting_configselect($name, $title, $description, $default, $widgets);
    $page->add($setting);
}

$key = $themename.'/homepage_structure_incourse_hdr';
$label = get_string('incourse', 'theme_klassplace');
$description = get_string('incourse_desc' , 'theme_klassplace');
$page->add(new admin_setting_heading($key, $label, $description));

// course.
for ($i = 1; $i <= KLASSPLACE_INCOURSE_SLOTS; $i++) {
    $name = $themename.'/homepage_structure_incourse'.$i;
    $title = get_string('slot', 'theme_klassplace', $i);
    $description = '';
    $default = $defaults['incourse'][$i - 1];
    $setting = new admin_setting_configselect($name, $title, $description, $default, $widgets);
    $page->add($setting);
}

$ADMIN->add($themename.'_homepage', $page);
