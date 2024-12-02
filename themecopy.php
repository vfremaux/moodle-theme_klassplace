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
 * Allows cpying settings between theme variants
 * @package theme_klassplace
 * @category theme
 * @author valery fremaux (valery.fremaux@gmail.com)
 * @copyright 2008 Valery Fremaux (Edunao.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Page reorganisation service
 */
require('../../config.php');

// We need this to retrieve original klassplace settings.
require_once($CFG->dirroot.'/theme/klassplace/lib.php');
require_once($CFG->dirroot.'/theme/klassplace/lib/filesettings_lib.php');
// Security.

$context = context_system::instance();

require_login();
// TODO : Should be refined with theme own manage capabilities.
require_capability('moodle/site:config', $context);

$PAGE->set_context($context);
$url = new moodle_url('/theme/klassplace/themecopy.php');
$PAGE->set_url($url); // Defined here to avoid notices on errors etc.
$PAGE->set_heading(get_string('copytheme', 'theme_klassplace'));

// Starts page content.

require_once($CFG->dirroot.'/theme/klassplace/themecopy_form.php');

$variantpaths = glob($CFG->dirroot.'/theme/klassplace*');
if (is_dir($CFG->dirroot.'/theme/klassplace')) {
    // Most of the settings of community klassplace should be compatible, if installed.
    $variants['klassplace'] = get_string('pluginname', 'theme_klassplace').' Origin';
}
foreach ($variantpaths as $path) {
    $path = basename($path);
    $variants[$path] = get_string('pluginname', 'theme_'.$path);
}

$mform = new theme_copy_form($url, array('variants' => $variants));

$returnurl = new moodle_url('/admin/settings.php', array('section' => 'themesettingklassplace'));

if ($mform->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $mform->get_data()) {

    $params = array('plugin' => 'theme_'.$data->themefrom);
    $fromsettings = $DB->get_records('config_plugins', $params);

    foreach ($fromsettings as $setting) {
        if ($setting->name == 'themetitle') {
            // Theme variant title should NOT be changed.
            continue;
        }
        $params = array('plugin' => 'theme_'.$data->themeto, 'name' => $setting->name);
        if ($oldsetting = $DB->get_record('config_plugins', $params)) {
            $oldsetting->value = $setting->value;
            $DB->update_record('config_plugins', $oldsetting);
        } else {
            unset($setting->id);
            $setting->plugin = 'theme_'.$data->themeto;
            $DB->insert_record('config_plugins', $setting);
        }
    }

    if (!empty($data->withfiles)) {

        // Transfer files.
        $fs = get_file_storage();

        // Get all component file... 
        $syscontext = context_system::instance();
        $files = $DB->get_records('files', array('contextid' => $syscontext->id, 'component' => 'theme_'.$data->themefrom));

        // Start purging all files from the destination.
        $fs->delete_area_files($syscontext->id, 'theme_'.$data->themeto);

        if (!empty($files)) {
            foreach ($files as $f) {
                if ($f->filename == '' || $f->filename == '.') {
                    // This is a directory.
                    continue;
                }
                $fromstoredfile = $fs->get_file_by_id($f->id);

                $tofiledesc = new Stdclass;
                $tofiledesc->contextid = $syscontext->id;
                $tofiledesc->component = 'theme_'.$data->themeto;
                $tofiledesc->filearea = $fromstoredfile->get_filearea();
                $tofiledesc->itemid = $fromstoredfile->get_itemid();
                $tofiledesc->filepath = $fromstoredfile->get_filepath();
                $tofiledesc->filename = $fromstoredfile->get_filename();

                $fs->create_file_from_storedfile($tofiledesc, $fromstoredfile);
            }
        }
    }

    cache_helper::invalidate_by_definition('core', 'config', array(), 'theme_'.$data->themeto);

    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('themecopied', 'theme_klassplace'), 'notifysuccess');

    $returnurl = new moodle_url('/admin/settings.php', ['section' => 'themesetting'.$data->themeto]);
    echo $OUTPUT->continue_button($returnurl);
    echo $OUTPUT->footer();
    die;
}

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();