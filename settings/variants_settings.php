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
 * Theme variants settings page file.
 *
 * @packagetheme_klassplace
 * @copyright  2016 Chris Kenniburg
 * @creditstheme_klassplace - MoodleHQ
 * @licensehttp://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$page = new admin_settingpage($themename.'_variants', get_string('variant_settings', 'theme_klassplace'));

$name = $themename.'_variantsheading';
$title = get_string('variantsheadingsub', 'theme_klassplace');
$description = format_text(get_string('variantsheading_desc', 'theme_klassplace'), FORMAT_MARKDOWN);
$headersetting = new admin_setting_heading($name, $title, $description);
$page->add($headersetting);

// Theme variant tag.
$name = $themename.'/themetitle';
$title = get_string('themetitle', 'theme_klassplace');
$description = get_string('themetitle_desc', 'theme_klassplace');
$setting = new admin_setting_configtext($name, $title, $description, '');
$page->add($setting);

// Must add the page after definiting all the settings!
$settings->add($page);
