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

require_once($CFG->dirroot.'/theme/klassplace/lib/mobile_detect_lib.php');

if (is_dir($CFG->dirroot.'/local/technicalsignals')) {
    require_once($CFG->dirroot.'/local/technicalsignals/lib.php');
}

/**
 * A login page layout for the klassplace theme.
 *
 * @package   theme_klassplace
 * @copyright 2021 Nicolas Maligue, Valery Fremaux
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$extraclasses = [];

if (is_mobile()) {
    $extraclasses[] = 'is-mobile';
}
if (is_tablet()) {
    $extraclasses[] = 'is-tablet';
}

$extraclasses[] = 'site-layout-'.$PAGE->theme->settings->pagelayout;

$bodyattributes = $OUTPUT->body_attributes($extraclasses);

$footnote = $OUTPUT->footnote();
$pagedoclink = $OUTPUT->page_doc_link();
$coursefooter = $OUTPUT->course_footer();
$logintoken = \core\session\manager::get_login_token();
$authforgotpasswordurl = new moodle_url('/login/forgot_password.php');
if (!empty($CFG->forgottenpasswordurl)) {
    $authforgotpasswordurl = $CFG->forgottenpasswordurl;
}
$hasforgotpassword = preg_match('/forgot_password\\.php/', me());
$forcestandard = preg_match('/local-alternatelogin|login-signup/', $PAGE->pagetype) || !empty($CFG->forcestandardlogin);

// No blocks resolving on login page.

$logintopimageurl = theme_klassplace_get_image_url('logintopimage');
$alerttopimageurl = theme_klassplace_get_image_url('alerttopimage');
$alertimageurl = theme_klassplace_get_image_url('alertimage');

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), 'escape' => false]),
    'output' => $OUTPUT,
    'alertbox' => !empty($PAGE->theme->settings->alertboxmessage) || !empty($PAGE->theme->settings->alertimage),
    'alertboxmessage' => $PAGE->theme->settings->alertboxmessage,
    'alertlink' => $PAGE->theme->settings->alertlink,
    'alertlinkurl' => $PAGE->theme->settings->alertlinkurl,
    'alertimagelinkurl' => $PAGE->theme->settings->alertimagelinkurl,
    'alertimageurl' => $alertimageurl,
    'alerttopimageurl' => $alerttopimageurl,
    'logintopimageurl' => $logintopimageurl,
    'logintoken' => $logintoken,
    'bodyattributes' => $bodyattributes,
    'hasfootnote' => !empty($footnote) && (preg_match('/[a-zA-Z0-9]/', preg_replace('/<\\/?(p|div|span|br)*?>/', '', $footnote))),
    'footnote' => $footnote,
    'custommenupullright' => $PAGE->theme->settings->custommenupullright,
    'hascoursefooter' => !empty($coursefooter) && (preg_match('/[a-z]/', strip_tags($coursefooter))),
    'coursefooter' => $coursefooter,
    'hasdoclink' => !empty($pagedoclink) && (preg_match('/[a-zA-Z0-9]/', strip_tags($pagedoclink))),
    'pagedoclink' => $pagedoclink,
    'loginhelpbuttonurl' => $PAGE->theme->settings->loginhelpbuttonurl,
    'helpbuttontext' => $PAGE->theme->settings->loginhelpbutton,
    'hasloginhelpbutton' => !empty($PAGE->theme->settings->loginhelpbuttonurl),
    'custombgimageurl' => theme_klassplace_get_random_filearea_url('loginimage'),
    'hasfooterelements' => !empty($PAGE->theme->settings->leftfooter) || !empty($PAGE->theme->settings->midfooter) || !empty($PAGE->theme->settings->rightfooter),
    'leftfooter' => @$PAGE->theme->settings->leftfooter,
    'midfooter' => @$PAGE->theme->settings->midfooter,
    'rightfooter' => @$PAGE->theme->settings->rightfooter,
    'showlangmenu' => @$CFG->langmenu,
    'sitealternatename' => @$PAGE->theme->settings->sitealternatename,
    'hasshowtoggle' => @$PAGE->theme->settings->showpasswordbutton,
    'usestandardform' => $hasforgotpassword || $forcestandard,
    'forgotpasswordurl' => $authforgotpasswordurl->out(),

    'useaccessibility' => false,
    'usedyslexicfont' => false,
    'usehighcontrastfont' => false,
    'dyslexicurl' => '',
    'highcontrasturl' => '',
    'dyslexicactive' => '',
    'highcontrastactive' => '',
    'dyslexicactiontitle' => '',
    'highcontrastactiontitle' => '',
    'dynamiccss' => $OUTPUT->get_dynamic_css($PAGE->theme),
    'cansignup' => !empty($CFG->registerauth),
    'canloginasguest' => $CFG->guestloginbutton and !isguestuser(),
    'canloginbyemail' => !empty($CFG->authloginviaemail)
];

theme_klassplace_process_texts($templatecontext);

if (is_dir($CFG->dirroot.'/local/technicalsignals')) {
    $templatecontext['technicalsignals'] = local_print_administrator_message();
}

echo $OUTPUT->render_from_template('theme_klassplace/login', $templatecontext);
