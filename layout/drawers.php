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
 * A drawer based layout for the klassplace theme.
 *
 * @package   theme_klassplace
 * @copyright 2022 Valery Fremaux (valery.fremaux@gmail.com)
 * @author Valery Fremaux (valery.fremaux@gmail.com), Florence Labord (info@expertweb.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/theme/klassplace/lib/mobile_detect_lib.php');
require_once($CFG->dirroot.'/theme/klassplace/lib.php');
require_once($CFG->libdir . '/behat/lib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot.'/theme/klassplace/classes/flat_navigation.php');

if (is_dir($CFG->dirroot.'/local/technicalsignals')) {
    require_once($CFG->dirroot.'/local/technicalsignals/lib.php');
}

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton('side-pre');

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

if (isloggedin()) {
    $courseindexopen = (get_user_preferences('drawer-open-index', true) == true);
    $blockdraweropen = (get_user_preferences('drawer-open-block') == true);
} else {
    $courseindexopen = false;
    $blockdraweropen = false;
}

if (defined('BEHAT_SITE_RUNNING')) {
    $blockdraweropen = true;
}

$extraclasses = ['uses-drawers'];
if ($courseindexopen) {
    $extraclasses[] = 'drawer-open-index';
}
$hasmobilenav = false;
if (is_mobile()) {
    $hasmobilenav = true;
    $extraclasses[] = 'is-mobile mobiletheme';
}
if (is_tablet()) {
    $extraclasses[] = 'is-tablet';
}

// Add section layout marker in body.
$extraclasses[] = 'site-layout-'.$PAGE->theme->settings->pagelayout;
$extraclasses[] = 'section-layout-'.$PAGE->theme->settings->sectionlayout;

/*
 * Note about blocks :
 * Moodle trend is to avoid blocks, so it is disengaging progressively from the blocks strategy.
 * The left side of the screen is now dedicated to th navigation contextual tree and cannot hendle blocks anymore.
 * As consequence, there is no more "post blocks area" and all blocks strategy should refer to "pre block area", unless
 * theme decides to tweak strongly the layout 
 */
 

$preblockshtml = $OUTPUT->blocks('side-pre');
$courseindex = '';

// Has blocks if side-pre zone is not empty or we can add blocks.
$hasblocks = (strpos($preblockshtml, 'data-block=') !== false || !empty($addblockbutton));

// Wrap to old 3.9 resolver.
list($hasnavdrawer, $navdraweropen, $hasspdrawer, $spdraweropen) = theme_klassplace_resolve_drawers($hasblocks, is_mobile());

if (!$hasblocks) {
    $blockdraweropen = false;
} else {
    $blockdraweropen = $hasspdrawer && $spdraweropen;
}
$forceblockdraweropen = $spdraweropen;

$courseindexopen = false;
$courseindex = false;
if ($hasnavdrawer) {
    // Navdrawer and courseindex drawer seems being the same thing.
    $courseindex = core_course_drawer();
    $courseindexopen = $navdraweropen;
}

if (!isset($courseindex)) {
    $courseindexopen = false;
    $courseindex = '';
}

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$forceblockdraweropen = $forceblockdraweropen || $OUTPUT->firstview_fakeblocks();
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
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

$footnote = $OUTPUT->footnote();
$pagedoclink = $OUTPUT->page_doc_link();
$coursefooter = $OUTPUT->course_footer();

$flatnav = new \theme_klassplace\flat_navigation($PAGE);
$flatnav->initialise();

$OUTPUT->check_dyslexic_state();
$OUTPUT->check_highcontrast_state();
$dysstate = $OUTPUT->get_dyslexic_state();
$hcstate = $OUTPUT->get_highcontrast_state();

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $preblockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'courseindexopen' => $courseindexopen,
    'blockdraweropen' => $blockdraweropen,
    'courseindex' => $courseindex,
    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'forceblockdraweropen' => $forceblockdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'overflow' => $overflow,
    'headercontent' => $headercontent,
    'forceblockdraweropen' => $forceblockdraweropen,
    'hasmobilenav' => $hasmobilenav,
    'flatnavigation' => $flatnav,

    'addblockbutton' => $addblockbutton,
    'hasfootnote' => !empty($footnote) && (preg_match('/[A-Za-z0-9]/', preg_replace('/<\\/?(p|div|span|br)*?>/', '', $footnote))),
    'footnote' => $footnote,
    'custommenupullright' => $PAGE->theme->settings->custommenupullright,
    'hascoursefooter' => !empty($coursefooter) && (preg_match('/[a-z]/', strip_tags($coursefooter))),
    'coursefooter' => $coursefooter,
    'hasdoclink' => !empty($pagedoclink) && (preg_match('/[a-zA-Z0-9]/', strip_tags($pagedoclink))),
    'pagedoclink' => $pagedoclink,
    'hasfooterelements' => !empty($PAGE->theme->settings->leftfooter) || !empty($PAGE->theme->settings->midfooter) || !empty($PAGE->theme->settings->rightfooter),
    'leftfooter' => $PAGE->theme->settings->leftfooter ?? '',
    'midfooter' => $PAGE->theme->settings->midfooter ?? '',
    'rightfooter' => $PAGE->theme->settings->rightfooter ?? '',
    'showlangmenu' => $CFG->langmenu ?? '',
    'sitealternatename' => $PAGE->theme->settings->sitealternatename ?? '',

    'useaccessibility' => ($PAGE->theme->settings->usedyslexicfont ?? false) || ($PAGE->theme->settings->usehighcontrastfont ?? false),
    'usedyslexicfont' => $PAGE->theme->settings->usedyslexicfont ?? false,
    'usehighcontrastfont' => $PAGE->theme->settings->usehighcontrastfont ?? false,
    'dyslexicurl' => $OUTPUT->get_dyslexic_url(),
    'highcontrasturl' => $OUTPUT->get_highcontrast_url(),
    'dyslexicactive' => ($dysstate) ? 'active' : '',
    'highcontrastactive' => ($hcstate) ? 'active' : '',
    'dyslexicactiontitle' => ($dysstate) ? get_string('unsetdys', 'theme_klassplace') : get_string('setdys', 'theme_klassplace'),
    'highcontrastactiontitle' => ($hcstate) ? get_string('unsethc', 'theme_klassplace') : get_string('sethc', 'theme_klassplace'),
    'dynamiccss' => $OUTPUT->get_dynamic_css($PAGE->theme)
];

if (!($OUTPUT instanceof core_renderer_maintenance)) {
    theme_klassplace_load_social_settings($templatecontext);
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
$PAGE->requires->js('/theme/klassplace/javascript/jquery.sldr.js', true);
$PAGE->requires->js('/theme/klassplace/javascript/blockslider.js');
$PAGE->requires->js('/theme/klassplace/javascript/cardimg.js');

echo $OUTPUT->render_from_template('theme_klassplace/drawers', $templatecontext);
