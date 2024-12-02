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

require('../../../../config.php');
require_once($CFG->dirroot.'/theme/klassplace/sections/sectionslib.php');

$action = optional_param('what', '', PARAM_TEXT);

if (in_array($action, array('collapseall', 'map', 'expandall'))) {
    $courseid = required_param('id', PARAM_INT);

    if (!$course = $DB->get_record('course', array('id' => $courseid))) {
        print_error('coursemisconf');
    }

    require_login($course);
}

if ($action == 'map') {
    // Removes all preceding preferences for the user and initializes expanding branches only.
    $select = ' name LIKE ? AND userid = ? AND value = ?';
    $DB->delete_records_select('user_preferences', $select, array('flexsection\\_%', $USER->id, $course->id));

    $leaves = sections_get_leaves($course->id);
    if ($leaves) {
        foreach ($leaves as $leaf) {
            $hidekey = 'flexsection_'.$leaf->id.'_hidden';
            $newrec = new StdClass;
            $newrec->userid = $USER->id;
            $newrec->name = $hidekey;
            $newrec->value = $course->id;
            $DB->insert_record('user_preferences', $newrec);
        }
    }
} else if ($action == 'collapseall') {
    // Renew all section hiding keys.
    $allsections = $DB->get_records('course_sections', array('course' => $course->id));
    if ($allsections) {
        foreach ($allsections as $s) {
            $hidekey = 'flexsection_'.$s->id.'_hidden';
            $params = array('userid' => $USER->id, 'name' => $hidekey);
            if (!$DB->record_exists('user_preferences', $params)) {
                $pref = new StdClass;
                $pref->userid = $USER->id;
                $pref->name = $hidekey;
                $pref->value = $course->id;
                $DB->insert_record('user_preferences', $pref);
            }
        }
    }
} else if ($action == 'expandall') {
    // Remove all hiding keys from that course
    $select = ' name LIKE ? AND userid = ? AND value = ? ';
    $DB->delete_records_select('user_preferences', $select, array('flexsection\\_%', $USER->id, $course->id));
} else {

    $sectionid = required_param('sectionid', PARAM_INT);
    $hide = required_param('hide', PARAM_BOOL);
    if (!$section = $DB->get_record('course_sections', array('id' => $sectionid))) {
        print_error('badsectionid');
    }

    if (!$course = $DB->get_record('course', array('id' => $section->course))) {
        print_error('coursemisconf');
    }

    require_login($course);

    $hidekey = 'flexsection_'.$sectionid.'_hidden';
    $params = array('userid' => $USER->id, 'name' => $hidekey);
    if (!$hide) {
        $DB->delete_records('user_preferences', $params);
    } else {
        if ($oldrec = $DB->get_record('user_preferences', $params)) {
            // We should never have as deleting when showing.
            $oldrec->value = $course->id;
            $DB->update_record('user_preferences', $oldrec);
        } else {
            // Store course id in value to optimise retrieval.
            $newrec = new StdClass;
            $newrec->userid = $USER->id;
            $newrec->name = $hidekey;
            $newrec->value = $course->id;
            $DB->insert_record('user_preferences', $newrec);
        }
    }
}