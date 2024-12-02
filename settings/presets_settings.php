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
 * @copyright  2016 Chris Kenniburg
 * @credits    theme_boost - MoodleHQ
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$page = new admin_settingpage($themename.'_presets', get_string('presets_settings', 'theme_klassplace'));

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

$name = $themename.'/presetinfo';
$title = get_string('presetinfosub', 'theme_klassplace');
$description = format_text(get_string('presetinfo_desc', 'theme_klassplace'), FORMAT_MARKDOWN);
$headersetting = new admin_setting_heading($name, $title, $description);
$page->add($headersetting);

// Preset.
$name = $themename.'/preset';
$title = get_string('preset', 'theme_klassplace');
$description = get_string('preset_desc', 'theme_klassplace');
$presetchoices = ['' => ''];
// Add preset files from theme preset folder.
$iterator = new DirectoryIterator($CFG->dirroot . '/theme/klassplace/scss/preset/');
foreach ($iterator as $presetfile) {
    if (!$presetfile->isDot()) {
        $presetname = substr($presetfile, 0, strlen($presetfile) - 5); // Name - '.scss'.
        $presetchoices[$presetname] = ucfirst($presetname);
    }
}

// Add preset files uploaded.
$context = context_system::instance();
$fs = get_file_storage();
$files = $fs->get_area_files($context->id, 'theme_klassplace', 'preset', 0, 'itemid, filepath, filename', false);
foreach ($files as $file) {
    $pname = substr($file->get_filename(), 0, strlen($file->get_filename()) - 5); // Name - '.scss'.
    $presetchoices[$pname] = ucfirst($pname);
}
// Sort choices.
natsort($presetchoices);
$default = 'default';
$setting = new admin_setting_configselect($name, $title, $description, $default, $presetchoices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Preset files setting.
$name = $themename.'/presetfiles';
$title = get_string('presetfiles', 'theme_klassplace');
$description = get_string('presetfiles_desc', 'theme_klassplace');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
array('maxfiles' => 20, 'accepted_types' => array('.scss')));
$page->add($setting);

// Must add the page after definiting all the settings!
$settings->add($page);
