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
* Heading and course font settings page file.
*
* @packagetheme_klassplace
* @copyright  2019 Valery Fremaux
* @creditstheme_boost - MoodleHQ
* @licensehttp://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die();

$page = new admin_settingpage($themename.'_fonts', get_string('fontsettings', 'theme_klassplace'));

// General custom font.
$name = $themename.'/usecustomfonts';
$title = get_string('usecustomfonts', 'theme_klassplace');
$description = get_string('usecustomfonts_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Default body font.
$name = $themename.'/generalbodyfont';
$title = get_string ('generalbodyfont', 'theme_klassplace');
$description = get_string ('generalbodyfont_desc', 'theme_klassplace');
$setting = new admin_setting_configstoredfile( $name, $title, $description, 'generalbodyfont', 0,
    array('maxfiles' => 5, 'accepted_types' => array('ttf', 'woff', 'woff2', 'eot')));
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Alternative font (needs explicit styling).
$name = $themename.'/generalaltfont';
$title = get_string ('generalaltfont', 'theme_klassplace');
$description = get_string ('generalaltfont_desc', 'theme_klassplace');
$setting = new admin_setting_configstoredfile( $name, $title, $description, 'generalaltfont', 0,
    array('maxfiles' => 5, 'accepted_types' => array('ttf', 'woff', 'woff2', 'eot')));
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Additional CSS selector for applying alt font.
$name = $themename.'/generalaltccsselector';
$title = get_string ('generalaltccsselector', 'theme_klassplace');
$description = get_string ('generalaltccsselector_desc', 'theme_klassplace');
$setting = new admin_setting_configtextarea( $name, $title, $description, 'generalaltfontcss', 0, ' rows="5" cols="80" ');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Custom Font for titles.
$name = $themename.'/titlefont';
$title = get_string ('titlefont', 'theme_klassplace');
$description = get_string ('titlefont_desc', 'theme_klassplace');
$setting = new admin_setting_configstoredfile( $name, $title, $description, 'titlefont', 0,
    array('maxfiles' => 5, 'accepted_types' => array('ttf', 'woff', 'woff2', 'eot')));
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Propose dyslexic alternative.
$name = $themename.'/usedyslexicfont';
$title = get_string('usedyslexicfont', 'theme_klassplace');
$description = get_string('usedyslexicfont_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$page->add($setting);

// Special alt dyslexic font.
$name = $themename.'/altdyslexicfont';
$title = get_string ('dyslexicfont', 'theme_klassplace');
$description = get_string ('dyslexicfont_desc', 'theme_klassplace');
$setting = new admin_setting_configstoredfile( $name, $title, $description, 'dyslexicfont', 0,
    array('maxfiles' => 5, 'accepted_types' => array('ttf', 'woff', 'woff2', 'eot')));
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Use highcontrast alternative.
$name = $themename.'/usehighcontrastfont';
$title = get_string('usehighcontrastfont', 'theme_klassplace');
$description = get_string('usehighcontrastfont_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$page->add($setting);

// Special high contrast font.
$name = $themename.'/althighcontrastfont';
$title = get_string ('highcontrastfont', 'theme_klassplace');
$description = get_string ('highcontrastfont_desc', 'theme_klassplace');
$setting = new admin_setting_configstoredfile( $name, $title, $description, 'highconstrastfont', 0,
    array('maxfiles' => 5, 'accepted_types' => array('ttf', 'woff', 'woff2', 'eot')));
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Must add the page after definiting all the settings!
$settings->add($page);

