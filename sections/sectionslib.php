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

defined('MOODLE_INTERNAL') || die();

function sections_get_leaves($courseid) {
    global $DB;

    $sql = "
        SELECT DISTINCT
            s.id,
            cfo.value as parent
        FROM
            {course_format_options} cfo,
            {course_sections} s
        WHERE
            cfo.name = 'parent' AND
            cfo.value = s.section AND
            s.course = cfo.courseid AND
            cfo.courseid = ?
    ";

    $parents = $DB->get_records_sql($sql, array($courseid));

    $leaves = array();
    if ($parents) {
        $parentids = array_keys($parents);

        list($insql, $inparams) = $DB->get_in_or_equal($parentids, SQL_PARAMS_QM, 'param', false, false);

        $select = "
            course = ? AND
            id $insql
        ";

        $params = array_merge(array($courseid), $inparams);

        $leaves = $DB->get_records_select('course_sections', $select, $params);
    }

    return $leaves;
}
