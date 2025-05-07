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
 * Main Lib file.
 *
 * @package    theme_klassplace
 * @copyright  2016 Chris Kenniburg
 * @credits    theme_boost - MoodleHQ
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* THEME_klassplace BUILDING NOTES
 * =============================
 * Lib functions have been split into separate files, which are called
 * from this central file. This is to aid ongoing development as I find
 * it easier to work with multiple smaller function-specific files than
 * with a single monolithic lib file. This may be a personal preference
 * and it would be quite feasible to bring all lib functions back into
 * a single central file if another developer prefered to work in that way.
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/theme/klassplace/lib/scss_lib.php');
require_once($CFG->dirroot.'/theme/klassplace/lib/filesettings_lib.php');
require_once($CFG->dirroot.'/theme/klassplace/lib/klassplace_lib.php');
require_once($CFG->dirroot.'/theme/klassplace/lib/dynamicstyles_lib.php');

function theme_klassplace_supports_feature($feature) {

    if ($feature == 'course/modthumbs') {
        // TODO When modthumbs are again supported, reactivate.
        return false; // 4.1 does not support by now course modthumbs... 
    }

    return false;
}

function theme_klassplace_page_init() {
    global $PAGE;

    $PAGE->requires->jquery();
    $PAGE->requires->js_call_amd('local_advancedperfs/perfs_panel', 'init');
}

/**
 * @param bool $checkspblocks
 * @param bool $ismobile are we on a mobile device ?
 */
function theme_klassplace_resolve_drawers($checkspblocks, $ismobile = false) {
    global $PAGE, $COURSE;

    $navdraweropen = false;
    $hasnavdrawer = false;

    $isblockmanagepage = preg_match('/^page-blocks-.*-manage$/', $PAGE->pagetype);
    $isadminpage = preg_match('/admin/', $PAGE->pagetype);
    $isadminpage = $isadminpage || preg_match('/admin/', $PAGE->pagelayout);
    $isindexsys = preg_match('/indexsys/', $_SERVER['PHP_SELF']);
    $isdashboard = preg_match('/my-index|site-index|user-profile/', $PAGE->pagetype);
    $isdashboard = $isdashboard || preg_match('/dashboard/', $PAGE->pagelayout);
    $isbaselayout = preg_match('/base/', $PAGE->pagelayout);
    $ispageformat = preg_match('/format_page/', $PAGE->pagelayout);
    $iscms = preg_match('/local-cms/', $PAGE->pagetype);
    $nonavdrawer = false;
    $isshop = preg_match('/^local-shop/', $PAGE->pagetype);
    $islibrary = preg_match('/^local-sharedresources-explore/', $PAGE->pagetype);

    $nonavdrawer = $isshop;

    if (isloggedin() && !isguestuser()) {
        $hasnavdrawer = isset($PAGE->theme->settings->shownavdrawer) && $PAGE->theme->settings->shownavdrawer == 1 && ($COURSE->format != 'page');
        $hasnavdrawer = $hasnavdrawer && !$nonavdrawer;
        $hasnavdrawer = $hasnavdrawer && !$isdashboard;
        $hasnavdrawer = $hasnavdrawer && !$iscms;

        if ($hasnavdrawer && isset($PAGE->theme->settings->shownavclosed) && $PAGE->theme->settings->shownavclosed == 0) {
            $navdraweropen = (get_user_preferences('drawer-open-index', 'true') == 'true');
        }
    }

    $debug = optional_param('drawerdebug', false, PARAM_BOOL);
    if ($debug) {
        echo "
            <pre>
            Is block manage page : $isblockmanagepage
            Is admin page : $isadminpage
            Is indexsys : $isindexsys
            Is dashboard : $isdashboard
            Is base layout : $isbaselayout
            Is paged formatted : $ispageformat
            Has some blocks : ".!empty($checkspblocks)."
            Is editing : ".!empty($PAGE->user_is_editing())."
            </pre>
        ";
    }

    $spdraweropen = false;

    if ($isshop || $islibrary) {
        // Special cases with no drawers.
        $hasspdrawer = true;
        $spdraweropen = true;
    } else {

        $hasspdrawer = isloggedin() &&
                (!empty($checkspblocks) || !empty($PAGE->user_is_editing())) && 
                        !$isblockmanagepage &&
                                !$isadminpage &&
                                        !$isindexsys &&
                                                !$isbaselayout &&
                                                        !$ispageformat;

        if ($hasspdrawer) {
            $spdraweropen = (get_user_preferences('spdrawer-open-nav', 'true') == 'true');
        }
    }

    return [$hasnavdrawer, $navdraweropen, $hasspdrawer, $spdraweropen];
}

/**
 * Fixes an XSS risk on login form by sanitizing the received token.
 */
function theme_klassplace_after_config() {
    if (array_key_exists('token', $_POST)) {
        $_POST['token'] = clean_param($_POST['token'], PARAM_ALPHANUMEXT);
    }
    if (array_key_exists('token', $_GET)) {
        $_GET['token'] = clean_param($_GET['token'], PARAM_ALPHANUMEXT);
    }
    if (array_key_exists('logintoken', $_POST)) {
        $_POST['logintoken'] = clean_param($_POST['logintoken'], PARAM_ALPHANUMEXT);
        // $_POST['username'] = clean_param($_POST['username'] ?? '', PARAM_ALPHANUMEXT);
        $_POST['password'] = clean_param($_POST['password'] ?? '', PARAM_NOTAGS);
    }
}