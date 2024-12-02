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
 * @package theme_essential_barchen
 * @category theme
 * @author valery fremaux (valery.fremaux@gmail.com)
 * @copyright 2008 Valery Fremaux (Edunao.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Page reorganisation service
 */
require('../../config.php');

$id = required_param('id', PARAM_INT); // The course module

if (!$cm = $DB->get_record('course_modules', array('id' => $id))) {
    print_error('badcoursemodule');
}

$modname = $DB->get_field('modules', 'name', array('id' => $cm->module));

if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
    print_error('invalidcourseid');
}

// Security.

require_login($course);

$context = context_course::instance($course->id);
$cmcontext = context_module::instance($id);
require_capability('moodle/course:manageactivities', $context);

$url = new moodle_url('/theme/'.$PAGE->theme->name.'/mod_thumb.php', array('id' => $id));

$PAGE->set_url($url); // Defined here to avoid notices on errors etc.
$PAGE->set_context($context);
$PAGE->set_heading(get_string('editmodthumb', 'theme_'.$PAGE->theme->name));

// Starts page content.

require_once($CFG->dirroot.'/theme/'.$PAGE->theme->name.'/mod_thumb_form.php');
$mform = new mod_thumb_form($url, array('modname' => $modname, 'modid' => $id));

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/course/view.php', array('id' => $course->id)));
}

if ($data = $mform->get_data()) {

    $fs = get_file_storage();

    if (@$data->cleanthumb) {
        $fs->delete_area_files($cmcontext->id, 'mod_'.$modname, 'modthumb', $id);
        redirect(new moodle_url('/course/view.php', array('id' => $course->id)));
    }

    $draftid = file_get_submitted_draft_itemid('thumb');
    file_save_draft_area_files($draftid, $cmcontext->id, 'mod_'.$modname,
                               'modthumb', $id, $mform->fileoptions);

    redirect(new moodle_url('/course/view.php', array('id' => $course->id)));
}

echo $OUTPUT->header();

$formdata = new StdClass;
$formdata->id = $id;
$mform->set_data($formdata);
$mform->display();

echo $OUTPUT->footer();