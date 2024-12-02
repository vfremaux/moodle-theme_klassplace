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
 * Theme scss config file.
 *
 * @package    theme_klassplace
 * @credits    theme_boost - MoodleHQ
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


global $SCSS;

$SCSS = new StdClass;
$SCSS->sheets = [
    'styles',
    'non_core_plugins',
    'activityicons',
    'stylebreadcrumb',
    'navbar',
    'tabs',
    'dropdown',
    'quiz',
    'responsive',
    'modthumb',
    'buttons',
    'panel_slider',
    'panel_announcement',
    'panel_academics',
    'panel_circles',
    'panel_indicators',
    'panel_clientlogos',
    'panel_events',
    'panel_customsection',
    'panel_social',
    'login'
];
