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
 * Defines renderer for course format sections
 *
 * @package    theme_fordson_fel
 * @copyright  2019 Valery Fremaux
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require('../../../config.php');

define('OVERRIDE_APPLIES_TO_SINGLE', 0);
define('OVERRIDE_APPLIES_TO_SIBLINGS', 1);
define('OVERRIDE_APPLIES_TO_SUBTREE', 2);

require_once($CFG->dirroot.'/theme/klassplace/sections/sectionclass_form.php');

$sectionid = required_param('id', PARAM_INT);
$sr = optional_param('sr', false, PARAM_INT);

if (!$section = $DB->get_record('course_sections', array('id' => $sectionid))) {
    print_error('badsectionid');
}

if (!$course = $DB->get_record('course', array('id' => $section->course))) {
    print_error('coursemisconf');
}

// Security.

$context = context_course::instance($course->id);
$PAGE->set_context($context);
$PAGE->requires->js('/theme/klassplace/sections/js/changesectionclass.js');
$PAGE->requires->css('/theme/klassplace/sections/styles.css');

require_login($course);
require_capability('moodle/course:manageactivities', $context);

$PAGE->set_heading(get_string('sectionclass', 'theme_'.$PAGE->theme->name));
$url = new moodle_url('/theme/klassplace/sections/sectionclass.php', array('id' => $sectionid, 'sr' => $sr));
$PAGE->set_url($url);

$renderer = $PAGE->get_renderer('format_'.$course->format);
if (!method_exists($renderer, 'parse_styleconfig')) {
    print_error('Section custom styling is NOT supported in this course format');
}
$config = get_config('theme_'.$PAGE->theme->name);
$availablestyles = $renderer->parse_styleconfig($config);

$params = array('courseid' => $course->id, 'sectionid' => $sectionid, 'name' => 'styleoverride');
$styleoverride = $DB->get_field('course_format_options', 'value', $params);

$mform = new sectionclass_form($url, array('styles' => $availablestyles, 'current' => $styleoverride, 'course' => $course));

if ($mform->is_cancelled()) {
    $courseurl = new moodle_url('/course/view.php', array('id' => $course->id, 'section' => $sr), 'section-'.$section->section);
    redirect($courseurl);
}

if ($data = $mform->get_data()) {
    // weird param masking in quickform (button element).
    $data->overridestyle = clean_param($_POST['overridestyle'], PARAM_TEXT);

    // Records in course_format_options for this section.

    if ($data->applyto == OVERRIDE_APPLIES_TO_SINGLE) {
        $sectionids = array($sectionid);
    } else if ($data->applyto == OVERRIDE_APPLIES_TO_SIBLINGS) {
        if ($course->format == 'flexsections') {
            echo "Flex section scan";
            $sectionids = array($sectionid);
            get_overridable_siblings($course->id, $sectionid, $sectionids);
        } else {
            echo "topics or other section scan";
            $sectionrecs = $DB->get_records_menu('course_sections', array('courseid' => $course->id), 'id,id');
            $sectionids = array_keys($sectionrecs);
        }
    } else if ($data->applyto == OVERRIDE_APPLIES_TO_SUBTREE) {
        $sectionids = array($sectionid);
        get_overridable_section_rec($course->id, $sectionid, $sectionids);
    }

    if (!empty($sectionids)) {
        foreach ($sectionids as $sid) {

            if (!empty($data->overridestyle) && $data->overridestyle != 'none') {
                $params = array('courseid' => $course->id, 'sectionid' => $sid, 'name' => 'styleoverride');
                if (!$oldrec = $DB->get_record('course_format_options', $params)) {
                    $option = new StdClass;
                    $option->courseid = $course->id;
                    $option->sectionid = $sid;
                    $option->format = $course->format;
                    $option->name = 'styleoverride';
                    $option->value = $data->overridestyle;
                    $DB->insert_record('course_format_options', $option);
                } else {
                    $oldrec->value = $data->overridestyle;
                    $DB->update_record('course_format_options', $oldrec);
                }
            } else {
                $params = array('courseid' => $course->id, 'sectionid' => $sid, 'name' => 'styleoverride');
                $DB->delete_records('course_format_options', $params);
            }
        }
    }

    $params = array('id' => $course->id);
    if (!empty($sr)) {
        $params['section'] = $sr;
    }
    $courseurl = new moodle_url('/course/view.php', $params, 'section-'.$section->section);
    redirect($courseurl);
}

echo $OUTPUT->header();

$formdata = new Stdclass;
$formdata->styleoverride = $styleoverride;
$formdata->id = $sectionid;
$mform->set_data($formdata);
$mform->display();

echo $OUTPUT->footer();
exit;

function get_overridable_section_rec($courseid, $sectionid, &$sections) {
    global $DB;

    $section = $DB->get_field('course_sections', 'section', array('id' => $sectionid));
    $params = array($courseid, 'parent', $section, 'flexsections');
    $select = "courseid = ? AND name = ? AND ".$DB->sql_compare_text('value')." = ".$DB->sql_compare_text('?')." AND format = ? ";
    $subs = $DB->get_records_select('course_format_options', $select, $params);
    if ($subs) {
        foreach ($subs as $sub) {
            array_push($sections, $sub->sectionid);
            get_overridable_section_rec($courseid, $sub->sectionid, $sections);
        }
    }
}

function get_overridable_siblings($courseid, $sectionid, &$sections) {
    global $DB;

    $params = array('courseid' => $courseid, 'sectionid' => $sectionid, 'format' => 'flexsections', 'name' => 'parent');
    $parentid = $DB->get_field('course_format_options', 'value', $params);
    // Get all sons of my parent.
    $params = array($courseid, 'parent', $parentid, 'flexsections');
    $select = " courseid = ? AND name = ? AND ".$DB->sql_compare_text('value')." = ".$DB->sql_compare_text('?')." AND format = 'flexsections' ";
    $subs = $DB->get_records_select('course_format_options', $select, $params);
    if ($subs) {
        foreach ($subs as $sub) {
            if ($sub->sectionid != $sectionid) {
                array_push($sections, $sub->sectionid);
            }
        }
    }
}