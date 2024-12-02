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

$tourcontexturl = required_param('url', MOODLE_URL);

require_login();
// We get current $USER logged in.

$moodleurl = new moodle_url($tourcontexturl);

$manager = new tool_usertours\manager();
$tours = $manager->get_matching_tours($moodleurl);

if (!empty($tours)) {
    foreach($tours as $tour) {
        $tourname = 'tool_usertours_tour_completion_time_'.$tour->id;
        $DB->delete_records('user_preferences', ['userid' => $USER->id, 'name' = $tourname]);
    }
}

return true;