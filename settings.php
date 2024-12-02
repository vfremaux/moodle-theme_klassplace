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
 * Main settings file.
 *
 * @package    theme_klassplace
 * @credits    theme_boost - MoodleHQ
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* THEME_klassplace BUILDING NOTES
 * =============================
 * Settings have been split into separate files, which are called from
 * this central file. This is to aid ongoing development as I find it
 * easier to work with multiple smaller function-specific files than
 * with a single monolithic settings file.
 * This may be a personal preference and it would be quite feasible to
 * bring all lib functions back into a single central file if another
 * developer prefered to work in that way.
 */

defined('MOODLE_INTERNAL') || die();

$themename = 'theme_klassplace';
$settingscategory = new admin_category($themename, get_string('klassplacehome', $themename));
$ADMIN->add('themes', $settingscategory);

require_once($CFG->dirroot.'/theme/klassplace/classes/admin_settingspage_tabs.php');

// Note new tabs layout for admin settings pages.
$settings = new theme_klassplace_admin_settingspage_tabs('themesettingklassplace', get_string('configtitle', $themename));

$yesnochoices = array(0 => get_string('no'), 1 => get_string('yes'));

if (is_dir($CFG->dirroot.'/theme/klassplace01')) {
    require($CFG->dirroot.'/theme/klassplace/settings/variants_settings.php');
}

require($CFG->dirroot.'/theme/klassplace/settings/general_settings.php');
require($CFG->dirroot.'/theme/klassplace/settings/courses_settings.php');
require($CFG->dirroot.'/theme/klassplace/settings/colours_settings.php');
require($CFG->dirroot.'/theme/klassplace/settings/image_settings.php');
require($CFG->dirroot.'/theme/klassplace/settings/menu_settings.php');
require($CFG->dirroot.'/theme/klassplace/settings/footer_settings.php');
require($CFG->dirroot.'/theme/klassplace/settings/customlogin_settings.php');
require($CFG->dirroot.'/theme/klassplace/settings/font_settings.php');
require($CFG->dirroot.'/theme/klassplace/settings/breadcrumb_settings.php');

require($CFG->dirroot.'/theme/klassplace/settings/javascript_settings.php');
require($CFG->dirroot.'/theme/klassplace/settings/fpicons_settings.php');

// Settings klassplace
require($CFG->dirroot.'/theme/klassplace/settings/homepage_structure_settings.php');
require($CFG->dirroot.'/theme/klassplace/settings/homepage_slider_settings.php');
require($CFG->dirroot.'/theme/klassplace/settings/homepage_social_settings.php');
require($CFG->dirroot.'/theme/klassplace/settings/homepage_announcement_settings.php');
require($CFG->dirroot.'/theme/klassplace/settings/homepage_academics_settings.php');
require($CFG->dirroot.'/theme/klassplace/settings/homepage_academics2_settings.php');
require($CFG->dirroot.'/theme/klassplace/settings/homepage_customsection_settings.php');
require($CFG->dirroot.'/theme/klassplace/settings/homepage_events_settings.php');
require($CFG->dirroot.'/theme/klassplace/settings/homepage_circles_settings.php');
require($CFG->dirroot.'/theme/klassplace/settings/homepage_indicators_settings.php');
require($CFG->dirroot.'/theme/klassplace/settings/homepage_clientlogos_settings.php');

//require($CFG->dirroot.'/theme/klassplace/settings/modchooser_settings.php');

