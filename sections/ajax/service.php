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

$sectionid = required_param('sectionid', PARAM_INT);
$hide = required_param('hide', PARAM_BOOL);
$action = optional_param('what', PARAM_BOOL);

if (!$section = $DB->get_record('course_sections', array('id' => $sectionid))) {
    print_error('badsectionid');
}

if (!$course = $DB->get_record('course', array('id' => $section->course))) {
    print_error('coursemisconf');
}

require_login($course);

if ($action == 'init') {
    // Removes all preceding preferences for the user and initializes expanding branches only.
    $select = ' name LIKE ? AND userid = ? ';
    $DB->delete_records_select('user_preferences', $select, array('section_'.$course->format.'\\_%', $USER->id));

    $leaves = flexsection_get_leaves($course->id);
    if ($leaves) {
        foreach($leaves as $leaf) {
            $hidekey = 'section_'.$course->format.'_'.$leaf->id.'_hidden';
            $newrec = new StdClass;
            $newrec->userid = $USER->id;
            $newrec->name = $hidekey;
            $newrec->value = $course->id;
            $DB->insert_record('user_preferences', $newrec);
        }
    }
} else {
    $hidekey = 'section_'.$course->format.'_'.$sectionid.'_hidden';
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