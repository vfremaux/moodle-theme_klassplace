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
 * A layout for the klassplace theme for page format.
 *
 * @package   theme_klassplace
 * @author Nicolas Maligue, Valery Fremaux, Florence Labord (activeprolearn.com)
 * @copyright 2016 Nicolas Maligue, Valery Fremaux (activeprolearn.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/theme/klassplace/lib/mobile_detect_lib.php');
require_once($CFG->dirroot.'/theme/klassplace/lib.php');

if (is_dir($CFG->dirroot.'/local/technicalsignals')) {
    require_once($CFG->dirroot.'/local/technicalsignals/lib.php');
}

use format_page\course_page;

if (@$PAGE->theme->settings->breadcrumbstyle == '1') {
    $PAGE->requires->js_call_amd('theme_klassplace/jBreadCrumb', 'init');
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

require_once($CFG->libdir . '/behat/lib.php');

$extraclasses = [];

if (is_mobile()) {
    $extraclasses[] = 'is-mobile';
}
if (is_tablet()) {
    $extraclasses[] = 'is-tablet';
}

$extraclasses[] = 'site-layout-'.$PAGE->theme->settings->pagelayout;

// Force block region resolver to check nav, but no blocks.
$hasblocks = false;
$haspostblocks = false;
$hasfpblockregion = false;
$checkpostblocks = false;
list($hasnavdrawer, $navdraweropen, $hasspdrawer, $navspdraweropen) = theme_klassplace_resolve_drawers($checkpostblocks, is_mobile());

$bodyattributes = $OUTPUT->body_attributes($extraclasses);

$footnote = $OUTPUT->footnote();
$pagedoclink = $OUTPUT->page_doc_link();
$coursefooter = $OUTPUT->course_footer();

$regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();
$page = course_page::get_current_page($COURSE->id);
$OUTPUT->check_dyslexic_state();
$OUTPUT->check_highcontrast_state();
$dysstate = $OUTPUT->get_dyslexic_state();
$hcstate = $OUTPUT->get_highcontrast_state();

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID) , "escape" => false]),
    'searchaction' => '/local/search/query.php',
    'output' => $OUTPUT,
    'sectionid' => $page->get_section_id(),
    'showbacktotop' => isset($PAGE->theme->settings->showbacktotop) && $PAGE->theme->settings->showbacktotop == 1,
    'hasfpblockregion' => $hasfpblockregion,
    'hasblocks' => $hasblocks,
    'haspostblocks' => $haspostblocks,
    'bodyattributes' => $bodyattributes,
    'navdraweropen' => $navdraweropen,
    'hasnavdrawer' => $hasnavdrawer,
    'hasspdrawer' => false,
    'navspdraweropen' => $navspdraweropen && ($checkpostblocks || $PAGE->user_is_editing()),
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'custommenupullright' => $PAGE->theme->settings->custommenupullright,
    'hascoursefooter' => !empty($coursefooter) && (preg_match('/[a-zA-Z0-9]/', strip_tags($coursefooter))),
    'coursefooter' => $coursefooter,
    'hasdoclink' => !empty($pagedoclink) && (preg_match('/[a-zA-Z0-9]/', strip_tags($pagedoclink))),
    'pagedoclink' => $pagedoclink,

    /* Footer control */
    'hasfootnote' => !empty($footnote) && (preg_match('/[A-Za-z0-9]/', preg_replace('/<\\/?(p|div|span|br)*?>/', '', $footnote))),
    'footnote' => $footnote,
    'hasfooterelements' => !empty($PAGE->theme->settings->leftfooter) || !empty($PAGE->theme->settings->midfooter) || !empty($PAGE->theme->settings->rightfooter),
    'leftfooter' => @$PAGE->theme->settings->leftfooter,
    'midfooter' => @$PAGE->theme->settings->midfooter,
    'rightfooter' => @$PAGE->theme->settings->rightfooter,

    'showlangmenu' => @$CFG->langmenu,
    'sitealternatename' => @$PAGE->theme->settings->sitealternatename,

    /* Accessibility and dynamic fonts */
    'useaccessibility' => @$PAGE->theme->settings->usedyslexicfont || @$PAGE->theme->settings->usehighcontrastfont,
    'usedyslexicfont' => @$PAGE->theme->settings->usedyslexicfont,
    'usehighcontrastfont' => @$PAGE->theme->settings->usehighcontrastfont,
    'dyslexicurl' => $OUTPUT->get_dyslexic_url(),
    'highcontrasturl' => $OUTPUT->get_highcontrast_url(),
    'dyslexicactive' => ($dysstate) ? 'active' : '',
    'highcontrastactive' => ($hcstate) ? 'active' : '',
    'dynamiccss' => $OUTPUT->get_dynamic_css($PAGE->theme),
];

theme_klassplace_process_texts($templatecontext);

if (is_dir($CFG->dirroot.'/local/technicalsignals')) {
    $templatecontext['technicalsignals'] = local_print_administrator_message();
}

$PAGE->requires->jquery();
$PAGE->requires->js_call_amd('theme_klassplace/pagescroll', 'init');
$PAGE->requires->js('/theme/klassplace/javascript/scrollspy.js');
$PAGE->requires->js('/theme/klassplace/javascript/tooltipfix.js');
$PAGE->requires->js('/theme/klassplace/javascript/jquery.sldr.js', true);
$PAGE->requires->js('/theme/klassplace/javascript/blockslider.js');

// Moodle 4.0 : flatnav deprecated
// $templatecontext['flatnavigation'] = $PAGE->flatnav;

echo $OUTPUT->render_from_template('theme_klassplace/pageklassplace', $templatecontext);

