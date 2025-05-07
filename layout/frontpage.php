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
 * A two column layout for the klassplace theme.
 *
 * @package     theme_klassplace
 * @author      2022 Valery Fremaux
 * @copyright   2022 Valery Fremaux
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/theme/klassplace/lib/mobile_detect_lib.php');
require_once($CFG->dirroot.'/theme/klassplace/lib.php');

if (is_dir($CFG->dirroot.'/local/technicalsignals')) {
    require_once($CFG->dirroot.'/local/technicalsignals/lib.php');
}

$flag = 'drawer-open-nav';
$PAGE->requires->js_amd_inline("require(['core_user/repository'], function(UserRepo) {
    const flag = '$flag';
    const n = document.querySelector('.block-xp-rocks');
    if (!n) return;
    n.addEventListener('click', function(e) {
        e.preventDefault();
        UserRepo.setUserPreference(flag, true);
        const notice = document.querySelector('.block-xp-notices');
        if (!notice) return;
        notice.style.display = 'none';
    });
});");

$flag = 'drawer-open-index';
$PAGE->requires->js_amd_inline("require(['core_user/repository'], function(UserRepo) {
    const flag = '$flag';
    const n = document.querySelector('.block-xp-rocks');
    if (!n) return;
    n.addEventListener('click', function(e) {
        e.preventDefault();
        UserRepo.setUserPreference(flag, true);
        const notice = document.querySelector('.block-xp-notices');
        if (!notice) return;
        notice.style.display = 'none';
    });
});");

$flag = 'drawer-open-block';
$PAGE->requires->js_amd_inline("require(['core_user/repository'], function(UserRepo) {
    const flag = '$flag';
    const n = document.querySelector('.block-xp-rocks');
    if (!n) return;
    n.addEventListener('click', function(e) {
        e.preventDefault();
        UserRepo.setUserPreference(flag, true);
        const notice = document.querySelector('.block-xp-notices');
        if (!notice) return;
        notice.style.display = 'none';
    });
});");

require_once($CFG->libdir . '/behat/lib.php');

$extraclasses = [];

if (is_mobile()) {
    $extraclasses[] = 'is-mobile';
}
if (is_tablet()) {
    $extraclasses[] = 'is-tablet';
}

$extraclasses[] = 'site-layout-'.$PAGE->theme->settings->pagelayout;

$enrolform = '';
$plugin = enrol_get_plugin('easy');
if ($plugin) {
    $enrolform = $plugin->get_form();
}

// Force block region resolver to check nav, but no blocks.
$hasblocks = false;
$haspostblocks = false;
$hasfpblockregion = false;
$checkpostblocks = false;
list($hasnavdrawer, $navdraweropen, $hasspdrawer, $navspdraweropen) = theme_klassplace_resolve_drawers($checkpostblocks, is_mobile());

$bodyattributes = $OUTPUT->body_attributes($extraclasses);

$headerlogo = $PAGE->theme->setting_file_url('headerlogo', 'headerlogo');
$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = strpos($blockshtml, 'data-block=') !== false;

$hasfpblockregion = isset($PAGE->theme->settings->showblockregions) !== false;

$topheader = $OUTPUT->topheader();
$footnote = $OUTPUT->footnote();
$pagedoclink = $OUTPUT->page_doc_link();
$coursefooter = $OUTPUT->course_footer();

$OUTPUT->check_dyslexic_state();
$OUTPUT->check_highcontrast_state();
$dysstate = $OUTPUT->get_dyslexic_state();
$hcstate = $OUTPUT->get_highcontrast_state();
$homepage = '';

$regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();
$templatecontext = [
    'output' => $OUTPUT,

    'sitename' => format_string($SITE->shortname, true, array('context' => context_course::instance(SITEID))),
    'sitealternatename' => @$PAGE->theme->settings->sitealternatename,
    'homepage' => $homepage,
    'bodyattributes' => $bodyattributes,

    'searchaction' => '/local/search/query.php',
    'headerlogo' => $headerlogo,
    'showlangmenu' => @$CFG->langmenu,

    'haseventurl' => !empty($haseventurl),
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'hasfpblockregion' => $hasfpblockregion,
    'navdraweropen' => $navdraweropen,
    'hasnavdrawer' => $hasnavdrawer,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'enrolform' => $enrolform,
    'custommenupullright' => $PAGE->theme->settings->custommenupullright,

    /* footer control */
    'hasfootnote' => !empty($footnote) && (preg_match('/[A-Za-z0-9]/', preg_replace('/<\\/?(p|div|span|br)*?>/', '', $footnote))),
    'footnote' => $footnote,
    'hascoursefooter' => !empty($coursefooter) && (preg_match('/[a-z]/', strip_tags($coursefooter))),
    'coursefooter' => $coursefooter,
    'hasfooterelements' => !empty($PAGE->theme->settings->leftfooter) || !empty($PAGE->theme->settings->midfooter) || !empty($PAGE->theme->settings->rightfooter),
    'leftfooter' => @$PAGE->theme->settings->leftfooter,
    'midfooter' => @$PAGE->theme->settings->midfooter,
    'rightfooter' => @$PAGE->theme->settings->rightfooter,
    'hasdoclink' => !empty($pagedoclink) && (preg_match('/[a-z]/', strip_tags($pagedoclink))),
    'pagedoclink' => $pagedoclink,

    'useaccessibility' => @$PAGE->theme->settings->usedyslexicfont || @$PAGE->theme->settings->usehighcontrastfont,
    'usedyslexicfont' => @$PAGE->theme->settings->usedyslexicfont,
    'usehighcontrastfont' => @$PAGE->theme->settings->usehighcontrastfont,
    'dyslexicurl' => $OUTPUT->get_dyslexic_url(),
    'highcontrasturl' => $OUTPUT->get_highcontrast_url(),
    'dyslexicactive' => ($dysstate) ? 'active' : '',
    'highcontrastactive' => ($hcstate) ? 'active' : '',
    'dyslexicactiontitle' => ($dysstate) ? get_string('unsetdys', 'theme_klassplace') : get_string('setdys', 'theme_klassplace'),
    'highcontrastactiontitle' => ($hcstate) ? get_string('unsethc', 'theme_klassplace') : get_string('sethc', 'theme_klassplace'),
    'dynamiccss' => $OUTPUT->get_dynamic_css($PAGE->theme),
];

theme_klassplace_process_texts($templatecontext);

theme_klassplace_load_header2_settings($templatecontext);
// Other widget settings are loaded when redering slots.

if (is_dir($CFG->dirroot.'/local/technicalsignals')) {
    $templatecontext['technicalsignals'] = local_print_administrator_message();
}

$PAGE->requires->jquery();
$PAGE->requires->js_call_amd('theme_klassplace/pagescroll', 'init');
$PAGE->requires->js('/theme/klassplace/javascript/tooltipfix.js');
$PAGE->requires->js('/theme/klassplace/javascript/jquery.sldr.js', true);
$PAGE->requires->js_call_amd('theme_klassplace/profileSlider', 'init');
if (is_dir($CFG->dirroot.'/local/my')) {
    // For course areas and categories collapsing.
    $PAGE->requires->js_call_amd('theme_klassplace/local_my_on_site', 'init');
}

if (!isloggedin()) {
    theme_klassplace_render_slots('homepage_structure_uncon', $templatecontext);
} else {
    theme_klassplace_render_slots('homepage_structure_con', $templatecontext);
}

// Moodle 4.0 : flatnav deprecated
// $templatecontext['flatnavigation'] = $PAGE->flatnav;

$rendered = $OUTPUT->render_from_template('theme_klassplace/frontpage', (object) $templatecontext);
echo $rendered;