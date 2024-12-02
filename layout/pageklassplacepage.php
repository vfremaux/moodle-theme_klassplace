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
 * A one column layout for course format page in klassplace theme.
 *
 * @package   theme_klassplace
 * @author Nicolas Maligue, Valery Fremaux, Florence Labord (activeprolearn.com)
 * @copyright 2016 Nicolas Maligue, Valery Fremaux (activeprolearn.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/theme/klassplace/lib/mobile_detect_lib.php');

if (is_dir($CFG->dirroot.'/local/technicalsignals')) {
    require_once($CFG->dirroot.'/local/technicalsignals/lib.php');
}

use format_page\course_page;

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

// Force block region resolver to check nav, but no blocks.
$hasblocks = false;
$haspostblocks = false;
$checkpostblocks = false;
list($hasnavdrawer, $navdraweropen, $hasspdrawer, $navspdraweropen) = theme_klassplace_resolve_drawers($checkpostblocks, is_mobile());

$footnote = $OUTPUT->footnote();
$pagedoclink = $OUTPUT->page_doc_link();
$coursefooter = $OUTPUT->course_footer();

$page = course_page::get_current_page($COURSE->id);
$OUTPUT->check_dyslexic_state();
$OUTPUT->check_highcontrast_state();
$dysstate = $OUTPUT->get_dyslexic_state();
$hcstate = $OUTPUT->get_highcontrast_state();

$templatecontext = [
    'output' => $OUTPUT,

    /* generals */
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID) , "escape" => false]),
    'sitealternatename' => @$PAGE->theme->settings->sitealternatename,
    'bodyattributes' => $bodyattributes,
    'sectionid' => $page->get_section_id(),

    'searchaction' => '/local/search/query.php',
    'custommenupullright' => $PAGE->theme->settings->custommenupullright,

    'showbacktotop' => isset($PAGE->theme->settings->showbacktotop) && $PAGE->theme->settings->showbacktotop == 1,
    'navdraweropen' => $navdraweropen,
    'hasnavdrawer' => $hasnavdrawer,
    'hasspdrawer' => $checkpostblocks || $PAGE->user_is_editing(),
    'navspdraweropen' => $navspdraweropen && ($checkpostblocks || $PAGE->user_is_editing()),

    /* Footer control */
    'hasfootnote' => !empty($footnote) && (preg_match('/[a-z]/', preg_replace('/<\\/?(p|div|span|br)*?>/', '', $footnote))),
    'footnote' => $footnote,
    'hascoursefooter' => !empty($coursefooter) && (preg_match('/[a-zA-Z0-9]/', strip_tags($coursefooter))),
    'coursefooter' => $coursefooter,
    'hasfooterelements' => !empty($PAGE->theme->settings->leftfooter) || !empty($PAGE->theme->settings->midfooter) || !empty($PAGE->theme->settings->rightfooter),
    'leftfooter' => @$PAGE->theme->settings->leftfooter,
    'midfooter' => @$PAGE->theme->settings->midfooter,
    'rightfooter' => @$PAGE->theme->settings->rightfooter,
    'hasdoclink' => (!empty($pagedoclink) && (preg_match('/[a-zA-Z0-9]/', strip_tags($pagedoclink)))),
    'pagedoclink' => $pagedoclink,

    'showlangmenu' => @$CFG->langmenu,

    'useaccessibility' => @$PAGE->theme->settings->usedyslexicfont || @$PAGE->theme->settings->usehighcontrastfont,
    'usedyslexicfont' => @$PAGE->theme->settings->usedyslexicfont,
    'usehighcontrastfont' => @$PAGE->theme->settings->usehighcontrastfont,
    'dyslexicurl' => $OUTPUT->get_dyslexic_url(),
    'highcontrasturl' => $OUTPUT->get_highcontrast_url(),
    'dyslexicactive' => ($dysstate) ? 'active' : '',
    'highcontrastactive' => ($hcstate) ? 'active' : '',
];

theme_klassplace_process_texts($templatecontext);

if (is_dir($CFG->dirroot.'/local/technicalsignals')) {
    $templatecontext['technicalsignals'] = local_print_administrator_message();
}

$PAGE->requires->jquery();
$PAGE->requires->js_call_amd('theme_klassplace/pagescroll', 'init');
$PAGE->requires->js('/theme/klassplace/javascript/scrollspy.js');

echo $OUTPUT->render_from_template('theme_klassplace/pageklassplacepage', $templatecontext);

