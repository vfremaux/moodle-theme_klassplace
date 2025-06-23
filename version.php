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
 * Version file.
 *
 * @package    theme_klassplace
 * @copyright  Valery Fremaux valery.fremaux@gmail.com
 * @credits    theme_boost - MoodleHQ / 2016 Chris Kenniburg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2025041400;
$plugin->release  = 'Moodle 4.5 klassplace v4.5 release 1';
$plugin->maturity  = MATURITY_STABLE;
$plugin->requires  = 2022112801;
$plugin->supported = [403, 405];
$plugin->component = 'theme_klassplace';
$plugin->dependencies = array(
    'theme_boost'  => 2018051400,
);

// Non moodle attributes.
$plugin->codeincrement = '4.5.0001';
$plugin->privacy = 'public';
