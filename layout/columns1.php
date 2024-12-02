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
 * A one column layout for the klassplace theme.
 * Central main column and no blocks nor drawers.
 *
 * @package   theme_klassplace
 * @author Nicolas Maligue, Valery Fremaux (activeprolearn.com)
 * @copyright 2016 Nicolas Maligue, Valery Fremaux (activeprolearn.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/theme/klassplace/lib/mobile_detect_lib.php');

if (is_dir($CFG->dirroot.'/local/technicalsignals')) {
    require_once($CFG->dirroot.'/local/technicalsignals/lib.php');
}

if ($PAGE->theme->settings->breadcrumbstyle == '1') {
    $PAGE->requires->js_call_amd('theme_klassplace/jBreadCrumb', 'init');
}

user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
require_once($CFG->libdir . '/behat/lib.php');

$extraclasses = [];
if (is_mobile()) {
    $extraclasses[] = 'is-mobile';
}
if (is_tablet()) {
    $extraclasses[] = 'is-tablet';
}

$extraclasses[] = 'site-layout-'.$PAGE->theme->settings->pagelayout;

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$hasblocks = false;

$hasfpblockregion = isset($PAGE->theme->settings->showblockregions) !== false;

// Navigation
// Menu elements.
$secondarynavigation = false;
$overflow = '';
if ($PAGE->has_secondary_navigation()) {
    $tablistnav = $PAGE->has_tablist_secondary_navigation();
    $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
    $secondarynavigation = $moremenu->export_for_template($OUTPUT);
    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $overflow = $overflowdata->export_for_template($OUTPUT);
    }
}

$primary = new core\navigation\output\primary($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);
$regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();

// Footer elements
if ($OUTPUT instanceof core_renderer_maintenance) {
    $footnote = '';
    $pagedoclink = '';
    $coursefooter = '';
    $dysstate = '';
    $dyslexicurl = '';
    $highcontrasturl = '';
    $hcstate = '';
    $dynamiccss = '';
} else {
    $footnote = $OUTPUT->footnote();
    $pagedoclink = $OUTPUT->page_doc_link();
    $coursefooter = $OUTPUT->course_footer();
    $OUTPUT->check_dyslexic_state();
    $OUTPUT->check_highcontrast_state();
    $dysstate = $OUTPUT->get_dyslexic_state();
    $dyslexicurl = $OUTPUT->get_dyslexic_url();
    $hcstate = $OUTPUT->get_highcontrast_state();
    $highcontrasturl = $OUTPUT->get_highcontrast_url();
    $dynamiccss = $OUTPUT->get_dynamic_css($PAGE->theme);
}

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID) , "escape" => false]) , 
    'searchaction' => '/local/search/query.php',
    'output' => $OUTPUT,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,

    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation ?: false,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),

    'hasfootnote' => !empty($footnote) && (preg_match('/[A-Za-z0-9]/', preg_replace('/<\\/?(p|div|span|br)*?>/', '', $footnote))),
    'custommenupullright' => $PAGE->theme->settings->custommenupullright,
    'footnote' => $footnote,
    'hascoursefooter' => !empty($coursefooter) && (preg_match('/[a-z]/', strip_tags($coursefooter))) && empty($PAGE->layout_options['nocoursefooter']),
    'coursefooter' => $coursefooter,
    'hasdoclink' => (!empty($pagedoclink) && (preg_match('/[a-z]/', strip_tags($pagedoclink)))),
    'pagedoclink' => $pagedoclink,
    'hasfooterelements' => (!empty($PAGE->theme->settings->leftfooter) ||
            !empty($PAGE->theme->settings->midfooter) ||
                    !empty($PAGE->theme->settings->rightfooter)) &&
                            empty($PAGE->layout_options['nofooter']),
    'leftfooter' => @$PAGE->theme->settings->leftfooter,
    'midfooter' => @$PAGE->theme->settings->midfooter,
    'rightfooter' => @$PAGE->theme->settings->rightfooter,
    'showlangmenu' => @$CFG->langmenu,
    'sitealternatename' => @$PAGE->theme->settings->sitealternatename,
    'technicalsignals' => local_print_administrator_message(),

    'useaccessibility' => @$PAGE->theme->settings->usedyslexicfont || @$PAGE->theme->settings->usehighcontrastfont,
    'usedyslexicfont' => @$PAGE->theme->settings->usedyslexicfont,
    'usehighcontrastfont' => @$PAGE->theme->settings->usehighcontrastfont,
    'dyslexicurl' => $dyslexicurl,
    'highcontrasturl' => $highcontrasturl,
    'dyslexicactive' => ($dysstate) ? 'active' : '',
    'highcontrastactive' => ($hcstate) ? 'active' : '',
    'dyslexicactiontitle' => ($dysstate) ? get_string('unsetdys', 'theme_klassplace') : get_string('setdys', 'theme_klassplace'),
    'highcontrastactiontitle' => ($hcstate) ? get_string('unsethc', 'theme_klassplace') : get_string('sethc', 'theme_klassplace'),
    'dynamiccss' => $dynamiccss,
];

if (!($OUTPUT instanceof core_renderer_maintenance)) {
    theme_klassplace_pass_layout_options($templatecontext);
    theme_klassplace_process_texts($templatecontext);
}

if (is_dir($CFG->dirroot.'/local/technicalsignals')) {
    $templatecontext['technicalsignals'] = local_print_administrator_message();
}

$PAGE->requires->jquery();
$PAGE->requires->js_call_amd('theme_klassplace/pagescroll', 'init');
$PAGE->requires->js('/theme/klassplace/javascript/scrollspy.js');
$PAGE->requires->js('/theme/klassplace/javascript/tooltipfix.js');

echo $OUTPUT->render_from_template('theme_klassplace/columns1', $templatecontext);