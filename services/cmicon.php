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

define('AJAX_SCRIPT', true);

include('../../../config.php');

$cmid = required_param('cmid', PARAM_INT);

$cm = $DB->get_record('course_module', ['id' => $cmid]);

if (!$cm) {
    $response = new StdClass;
    $response->error = true;
    echo json_encode($response);
    exit(0);
}

$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
require_login($course, $cm, true);

$modinfo = get_fast_modinfo($course);
$mod = $modinfo->get_cm($cm->id);

$courserenderer = $PAGE->get_renderer('core', 'course');
$response = $courserenderer->course_section_cm_thumb($mod);
$response->error = false;

echo json_encode($response);