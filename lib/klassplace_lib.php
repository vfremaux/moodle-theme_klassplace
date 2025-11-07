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
 * FileSettings Lib file.
 *
 * @package    theme_klassplace
 * @author     valery.fremaux <valery.fremaux@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */

defined('MOODLE_INTERNAL') || die();

define('KLASSPLACE_FRONTPAGE_UNCON_SLOTS', 10);
define('KLASSPLACE_FRONTPAGE_CON_SLOTS', 10);
define('KLASSPLACE_MY_SLOTS', 5);
define('KLASSPLACE_INCOURSE_SLOTS', 5);

if (is_dir($CFG->dirroot.'/local/moodlescript')) {
    // If moodlescript engine is installed, get the cross-plugin library for conditions.
    include_once($CFG->dirroot.'/local/moodlescript/xlib.php');
}

function theme_klassplace_get_course_activities() {
    global $CFG, $PAGE, $OUTPUT;

    // A copy of block_activity_modules.
    $course = $PAGE->course;
    $content = new stdClass();
    $modinfo = get_fast_modinfo($course);
    $modfullnames = array();

    $archetypes = array();

    foreach ($modinfo->cms as $cm) {
        // Exclude activities which are not visible or have no link (=label).
        if (!$cm->uservisible or !$cm->has_view()) {
            continue;
        }
        if (array_key_exists($cm->modname, $modfullnames)) {
            continue;
        }
        if (!array_key_exists($cm->modname, $archetypes)) {
            $archetypes[$cm->modname] = plugin_supports('mod', $cm->modname, FEATURE_MOD_ARCHETYPE, MOD_ARCHETYPE_OTHER);
        }
        if ($archetypes[$cm->modname] == MOD_ARCHETYPE_RESOURCE) {
            if (!array_key_exists('resources', $modfullnames)) {
                $modfullnames['resources'] = get_string('resources');
            }
        } else {
            $modfullnames[$cm->modname] = $cm->modplural;
        }
    }
    core_collator::asort($modfullnames);

    return $modfullnames;
}

function theme_klassplace_strip_html_tags($text) {
    $text = preg_replace(
        array(
            // Remove invisible content.
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
            // Add line breaks before and after blocks.
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
            ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0",
            ),
        $text ?? ''
        );
return strip_tags( $text );
}

/**
 * Cut the Course content.
 *
 * @param $str
 * @param $n
 * @param $end_char
 * @return string
 */
function theme_klassplace_course_trim_char($str, $n = 500, $endchar = '&#8230;') {
    if (strlen($str) < $n) {
        return $str;
    }

    $str = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $str));
    if (strlen($str) <= $n) {
        return $str;
    }

    $out = "";
    $small = substr($str, 0, $n);
    $out = $small.$endchar;
    return $out;
}

function theme_klassplace_get_random_filearea_url($filearea) {
    global $PAGE;

    // Process args to randomize on all images in this filearea.
    $fs = get_file_storage();

    $syscontext = context_system::instance();
    $component = 'theme_'.$PAGE->theme->name;

    if ($loginimages = $fs->get_area_files($syscontext->id, $component, $filearea, 0, "itemid, filepath, filename", false)) { // Ignore dirs.
        shuffle($loginimages);
        $image = array_shift($loginimages);

        return moodle_url::make_pluginfile_url($syscontext->id, $component, $filearea, 0, $image->get_filepath(), $image->get_filename(), true);
    }

    return false;
}

/**
 * Get the imageurl of the login top image (in login form)
 */
function theme_klassplace_get_logintopimage_url() {
    global $PAGE;
    $fs = get_file_storage();

    $syscontext = context_system::instance();
    $component = 'theme_'.$PAGE->theme->name;

    if ($fs->is_area_empty($syscontext->id, $component, 'logintopimage', 0)) {
        // No file recorded here.
        return '';
    }

    if ($loginimages = $fs->get_area_files($syscontext->id, $component, 'logintopimage', 0, "itemid, filepath, filename", false)) { // Ignore dirs.
        $image = array_shift($loginimages);
        return moodle_url::make_pluginfile_url($syscontext->id, $component, 'logintopimage', 0, $image->get_filepath(), $image->get_filename(), true);
    }

    return '';
}

/**
 * Get the imageurl of the login alert top image (in login alert box)
 */
function theme_klassplace_get_image_url($filearea) {
    global $PAGE;
    $fs = get_file_storage();

    $syscontext = context_system::instance();
    $component = 'theme_'.$PAGE->theme->name;

    if ($fs->is_area_empty($syscontext->id, $component, $filearea, 0)) {
        // No file recorded here.
        return '';
    }

    if ($areaimages = $fs->get_area_files($syscontext->id, $component, $filearea, 0, "timecreated desc", false)) { // Ignore dirs.
        $image = array_shift($areaimages);
        return moodle_url::make_pluginfile_url($syscontext->id, $component, $filearea, 0, $image->get_filepath(), $image->get_filename(), true);
    }

    return '';
}

/**
 * We check we are in a course module or not.
 */
function klassplace_page_location_incourse_themeconfig() {
    GLOBAL $PAGE;

    $course = $PAGE->cm;

    if ($course) {
        return true;
    } else {
        return false;
    }
}

/**
 * Inject some settings in text zones
 */
function theme_klassplace_process_texts(&$templatecontext) {
    global $CFG, $PAGE, $OUTPUT, $SITE, $COURSE;
    static $usertoursloaded = false;

    $textzones = ['footnote', 'leftfooter', 'midfooter', 'rightfooter', 'sitealternatename'];

    foreach ($textzones as $tz) {
        $templatecontext[$tz] = str_replace('{{socialicons}}', $OUTPUT->social_icons($templatecontext, 'footer'), $templatecontext[$tz]);
        $templatecontext[$tz] = str_replace('{{WWWROOT}}', $CFG->wwwroot, $templatecontext[$tz]);
        $templatecontext[$tz] = str_replace('%WWWROOT%', @$CFG->wwwroot, $templatecontext[$tz]);
        $templatecontext[$tz] = str_replace('{{SITE}}', @$SITE->fullname, $templatecontext[$tz]);
        $templatecontext[$tz] = str_replace('%SITE%', @$SITE->fullname, $templatecontext[$tz]);
        $templatecontext[$tz] = str_replace('{{COURSE}}', @$COURSE->fullname, $templatecontext[$tz]);
        $templatecontext[$tz] = str_replace('%COURSE%', @$COURSE->fullname, $templatecontext[$tz]);
        $templatecontext[$tz] = str_replace('{{COURSEID}}', @$COURSE->id, $templatecontext[$tz]);
        $templatecontext[$tz] = str_replace('%COURSEID%', @$COURSE->id, $templatecontext[$tz]);
        if (isloggedin() && !isguestuser()) {
            $tour = \tool_usertours\manager::get_current_tours();
            if ($tour) {
                $link = \html_writer::link('', get_string('resettouronpage', 'tool_usertours'), [
                        'data-action'   => 'tool_usertours/resetpagetour',
                        'data-url' => $PAGE->url,
                        'id' => 'resetpagetour'
                    ]);
                $templatecontext[$tz] = str_replace('{{resettourlink}}', $link, $templatecontext[$tz]);
            } else {
                $templatecontext[$tz] = str_replace('{{resettourlink}}', '', $templatecontext[$tz]);
            }
        } else {
            $templatecontext[$tz] = str_replace('{{resettourlink}}', '', $templatecontext[$tz]);
        }

        if (!$usertoursloaded) {
            $PAGE->requires->js_call_amd('theme_klassplace/usertours', 'init');
            $usertoursloaded = true;
        }
    }
}

/**
 * DEPRECATED
 * Loads all settings variables for the header 2 bar.
 * @param array $templatecontext the global layout context
 */
function theme_klassplace_load_header2_settings(&$templatecontext) {
    global $PAGE;

    $templatecontext['displayfacebook'] = @$PAGE->theme->settings->displayfacebook;
    $templatecontext['facebook'] = @$PAGE->theme->settings->facebook;
    $templatecontext['displaytwitter'] = @$PAGE->theme->settings->displaytwitter;
    $templatecontext['twitter'] = @$PAGE->theme->settings->twitter;
    $templatecontext['displaygoogleplus'] = @$PAGE->theme->settings->displaygoogleplus;
    $templatecontext['googleplus'] = @$PAGE->theme->settings->googleplus;
    $templatecontext['displaypinterest'] = @$PAGE->theme->settings->displaypinterest;
    $templatecontext['pinterest'] = @$PAGE->theme->settings->pinterest;
    $templatecontext['displayinstagram'] = @$PAGE->theme->settings->displayinstagram;
    $templatecontext['instagram'] = @$PAGE->theme->settings->instagram;
    $templatecontext['displayyoutube'] = @$PAGE->theme->settings->displayyoutube;
    $templatecontext['youtube'] = @$PAGE->theme->settings->youtube;
    $templatecontext['displayflickr'] = @$PAGE->theme->settings->displayflickr;
    $templatecontext['flickr'] = @$PAGE->theme->settings->flickr;
    $templatecontext['displaywhatsapp'] = @$PAGE->theme->settings->displaywhatsapp;
    $templatecontext['whatsapp'] = @$PAGE->theme->settings->whatsapp;
    $templatecontext['displayskype'] = @$PAGE->theme->settings->displayskype;
    $templatecontext['skype'] = @$PAGE->theme->settings->skype;
    $templatecontext['displaylinkedin'] = @$PAGE->theme->settings->displaylinkedin;
    $templatecontext['linkedin'] = @$PAGE->theme->settings->linkedin;
    $templatecontext['displaycontactno'] = @$PAGE->theme->settings->displaycontactno;
    $templatecontext['contactno'] = @$PAGE->theme->settings->contactno;
    $templatecontext['isloggedin'] = @$PAGE->theme->settings->isloggedin;
    $templatecontext['isguestuser'] = @$PAGE->theme->settings->isguestuser;
    $templatecontext['registerauth'] = @$PAGE->theme->settings->registerauth;
    $templatecontext['authplugin'] = @$PAGE->theme->settings->authplugin;
    $templatecontext['contactno'] = @$PAGE->theme->settings->contactno;
}

/**
 * Loads all settings and images fir the slider widget.
 * @param array $templatecontext the global layout context
 * 
 */
function theme_klassplace_load_slider_settings(&$templatecontext) {
    global $CFG, $PAGE;

    $contextid = context_system::instance()->id;
    $component = 'theme_'.$PAGE->theme->name;
    $pathname = '/';

    $keybases = ['sliderheading', 'slidercaption', 'slidercaptionurl'];
    $elmixs = ['one', 'two', 'three', 'four', 'five', 'six'];

    for ($i = 1; $i <= 6; $i++) {

        $slidetpl = new StdClass;
        $slidetpl->elmix = $elmixs[$i - 1];

        if ($i == 1) {
            $slidetpl->isfirst = true;
        }

        foreach ($keybases as $keybase) {
            $key = $keybase.$i;
            $slidetpl->$keybase = @$PAGE->theme->settings->$key;
        }

        $slidetpl->captionclass = '';
        if (empty($slidetpl->slidercaption)) {
            $slidetpl->captionclass = 'emptycaption';
        }

        if (!empty($slidetpl->slidercaptionurl)) {
            if (!theme_klassplace_is_local_url($slidetpl->slidercaptionurl)) {
                $slidetpl->target = '_blank';
            }
        }

        $imagekey = 'sliderimage'.$i;
        $sliderimage = @$PAGE->theme->settings->$imagekey;

        if (!empty($sliderimage)) {
            $slidetpl->sliderimage = moodle_url::make_pluginfile_url($contextid, $component, 'sliderimage'.$i, 0, $pathname, basename($sliderimage), false);
            $templatecontext['slides'][] = $slidetpl;
        }
    }

    $templatecontext['slideshowheight'] = @$PAGE->theme->settings->slideshowheight;
}

/**
 * Loads all settings and images fir the announcepent widget.
 * @param array $templatecontext the global layout context
 */
function theme_klassplace_load_announcement_settings(&$templatecontext) {
    global $PAGE;

    $templatecontext['announcementheading'] = @$PAGE->theme->settings->announcementheading;
    $templatecontext['announcementtagline'] = @$PAGE->theme->settings->announcementtagline;
    $templatecontext['buttonreadmoreurl'] = @$PAGE->theme->settings->buttonreadmoreurl;
    $templatecontext['buttonreadmore'] = @$PAGE->theme->settings->buttonreadmore;
    $templatecontext['buttonbuynowurl'] = @$PAGE->theme->settings->buttonbuynowurl;
    $templatecontext['buttonbuynow'] = @$PAGE->theme->settings->buttonbuynow;
}

function theme_klassplace_load_academics_settings(&$templatecontext) {
    global $CFG, $PAGE;

    $contextid = context_system::instance()->id;
    $component = 'theme_'.$PAGE->theme->name;

    $templatecontext['academicstagline'] = @$PAGE->theme->settings->academicstagline;
    $templatecontext['academicsheading'] = @$PAGE->theme->settings->academicsheading;
    $templatecontext['academicsdescription'] = @$PAGE->theme->settings->academicsdescription;
    $academicsimage = @$PAGE->theme->settings->academicsimage;
    $templatecontext['academicsimage'] = moodle_url::make_pluginfile_url($contextid, $component, 'academicsimage', 0, dirname($academicsimage), basename($academicsimage), false);

    $keybases = [
        'academicslisturl',
        'academicslist'
    ];

    $j = 1; // apparent index.
    for ($i = 1; $i <= 6; $i++) {

        if (is_dir($CFG->dirroot.'/local/moodlescript')) {
            $condkey = 'academicsdispcondition'.$i;
            $cond = @$PAGE->theme->settings->$condkey;
            if (!local_moodlescript_evaluate_expression($cond)) {
                theme_klassplace_debug_trace("$condkey check failed. Hiding theme element...", THEME_KLASSPLACE_TRACE_DEBUG);
                continue;
            }
        }

        $academictpl = new StdClass;
        $academictpl->i = $j; // use apparent index in template.
        foreach ($keybases as $keybase) {
            $key = $keybase.$i;
            $academictpl->$keybase = @$PAGE->theme->settings->$key;
        }
        $templatecontext['academics'][] = $academictpl;
        $j++;
    }
}

function theme_klassplace_load_academics2_settings(&$templatecontext) {
    global $CFG, $PAGE;

    $contextid = context_system::instance()->id;
    $component = 'theme_'.$PAGE->theme->name;

    $templatecontext['academicstagline2'] = @$PAGE->theme->settings->academicstagline2;
    $templatecontext['academicsheading2'] = @$PAGE->theme->settings->academicsheading2;
    $templatecontext['academicsdescription2'] = @$PAGE->theme->settings->academicsdescription2;
    $academicsimage = @$PAGE->theme->settings->academicsimage2;
    $templatecontext['academicsimage2'] = moodle_url::make_pluginfile_url($contextid, $component, 'academicsimage2', 0, dirname($academicsimage), basename($academicsimage), false);

    $j = 1; // apparent index.
    for ($i = 1; $i <= 6; $i++) {

        if (is_dir($CFG->dirroot.'/local/moodlescript')) {
            $condkey = 'academicsdispcondition2_'.$i;
            $cond = @$PAGE->theme->settings->$condkey;
            if (!local_moodlescript_evaluate_expression($cond)) {
                theme_klassplace_debug_trace("$condkey check failed. Hiding theme element...", THEME_KLASSPLACE_TRACE_DEBUG);
                continue;
            }
        }

        $academictpl = new StdClass;
        $key = 'academicslist2_'.$i;
        $academictpl->i = $i; // use apparent index in template.
        $academictpl->academicslist = @$PAGE->theme->settings->$key;
        $key = 'academicslisturl2_'.$i;
        $academictpl->academicslisturl = @$PAGE->theme->settings->$key;
        $templatecontext['academics2'][] = $academictpl;
        $j++;
    }
}

/**
 * Loads layout template context from the settings.
 * @param objectref &$templatecontext
 */
function theme_klassplace_load_indicators_settings(&$templatecontext) {
    global $CFG, $PAGE;

    $contextid = context_system::instance()->id;
    $component = 'theme_'.$PAGE->theme->name;

    $templatecontext['indicatorstagline'] = format_string(@$PAGE->theme->settings->indicatorstagline);
    $templatecontext['indicatorsheading'] = format_string(@$PAGE->theme->settings->indicatorsheading);
    $templatecontext['indicatorsdescription'] = format_text(@$PAGE->theme->settings->indicatorsdescription, FORMAT_MOODLE);

    $indicatorsimage = @$PAGE->theme->settings->indicatorsimage;
    $templatecontext['indicatorsimage'] = moodle_url::make_pluginfile_url($contextid, $component, 'indicatorsimage', 0, dirname($indicatorsimage), basename($indicatorsimage), false);

    $keybases = [
        'indicatortitle',
        'indicatorgraphindex',
        'indicatorgraphindex',
        'indicatoroverride',
        'indicatoralturl'
    ];

    for ($i = 1; $i <= 6; $i++) {
        $graphindexkey = 'indicatorgraphindex'.$i;
        $alturlkey = 'indicatoralturl'.$i;
        if (empty($PAGE->theme->settings->$graphindexkey) && empty($PAGE->theme->settings->$alturlkey)) {
            // Indicator is not properly defined by either a graphindex or an alternate url
            continue;
        }
        $indicatortpl = new StdClass;
        $indicatortpl->i = $i;
        foreach ($keybases as $keybase) {
            $key = $keybase.$i;
            $indicatortpl->$keybase = @$PAGE->theme->settings->$key;
        }
        $indicatortpl->indicatorgraph = theme_klassplace_get_indicator($indicatortpl->indicatorgraphindex, 'graph', $indicatortpl->indicatoroverride);
        $templatecontext['indicators'][] = $indicatortpl;
    }
}

/**
 * An API wrapper to those plugins who provide some site levels indicators.
 * Plugins requirement is to have a xlib.php API file and provide the 
 * <pluginname>_get_indicator(int index, string format)
 * 
 * @param string $index // expected syntax : <pluginname>:<numericindex>
 * @param string $format format can be either 'graph' or 'raw'. Raw will return a numeric
 * value, or a pair of (value, rangemax) values. Graph returns a displayable fragment of html with a graph.
 * @param string $override If not null, sends an override value to the indicator that will be used in place 
 * of the internally calculated value.
 */
function theme_klassplace_get_indicator($index, $format = 'graph', $override = null) {
    global $CFG;

    $parts = explode(':', $index);
    if (2 == count($parts)) {
        $pluginfullname = $parts[0];
        $slotindex = $parts[1];

        if (!is_numeric($slotindex)) {
            throw new moodle_exception("Bad slotindex type in $pluginfullname:<slotindex>. Integer required.");
        }

        $parts = explode('_', $pluginfullname);
        $type = array_shift($parts);
        $pluginname = implode('_', $parts);

        $pm = core_plugin_manager::instance();
        $root = $pm->get_plugintype_root($type);
        if (file_exists($root.'/'.$pluginname.'/xlib.php')) {
            include_once($root.'/'.$pluginname.'/xlib.php');

            $funcname = $pluginfullname.'_get_site_indicator';
            // In case of klassplace we override graph title with klassplace indicator titles.
            $indicatorcode = $funcname($slotindex, $format, $override, ['notitle' => true]);
            return $indicatorcode;
        } else {
            theme_klassplace_debug_trace("Indicator xlib.php file not found at : ".$root.'/'.$pluginname.'/xlib.php', THEME_KLASSPLACE_TRACE_DEBUG);
        }
    } else {
        throw new moodle_exception("Bad graphindex syntax. Should be <pluginname>:<index>");
    }

    return '';
}

function theme_klassplace_load_categorie_settings(&$templatecontext) {
    global $PAGE;

    $templatecontext['enablecategoryimage'] = @$PAGE->theme->settings->enablecategoryimage;
}

function theme_klassplace_pass_layout_options(&$templatecontext) {
    global $PAGE;

    $options = $PAGE->layout_options;
    foreach ($options as $key => $value) {
        $templatecontext[$key] = $value;
    }
}

function theme_klassplace_load_customsection_settings(&$templatecontext) {
    global $PAGE;

    $keybases = [
        'customboxheading',
        'customboxicon',
        'customboxdescription'
    ];

    for ($i = 1; $i <= 8; $i++) {
        $customboxtpl = new StdClass;
        foreach ($keybases as $keybase) {
            $key = $keybase.$i;
            $customboxtpl->$keybase = @$PAGE->theme->settings->$key;
        }
        $templatecontext['customsections'][] = $customboxtpl;
    }

    // For customboxes background image, see scss_lib parameter passing.
}

function theme_klassplace_load_events_settings(&$templatecontext) {
    global $CFG, $PAGE;

    $contextid = context_system::instance()->id;
    $component = 'theme_'.$PAGE->theme->name;

    $keybases = [
        'displayeventbox',
        'eventday',
        'eventmonthyear',
        'eventtitle',
        'eventtitleurl',
        'eventlocation',
        'eventdescription',
        'eventviewmapurl',
        'eventviewmap',
        'eventprice'
    ];

    if (is_dir($CFG->dirroot.'/local/shop')) {
        $keybases[] = 'eventshopurl';
    }

    $templatecontext['eventtagline'] = @$PAGE->theme->settings->eventtagline;
    $templatecontext['eventheading'] = @$PAGE->theme->settings->eventheading;
    $actualdate = date("Y-m-d");
    $templatecontext['haseventdate'] = true;
    $counthaseventurl = 0;

    $fs = get_file_storage();

    for ($i = 1; $i < 10; $i++) {
        $eventtpl = new StdClass;

        foreach ($keybases as $keybase) {
            $key = $keybase.$i;
            $eventtpl->$keybase = @$PAGE->theme->settings->$key;
        }

        if (is_dir($CFG->dirroot.'/local/moodlescript')) {
            $condkey = 'eventdispcondition'.$i;
            $cond = @$PAGE->theme->settings->$condkey;
            if (!local_moodlescript_evaluate_expression($cond)) {
                theme_klassplace_debug_trace("$condkey check failed. Hiding theme element...", THEME_KLASSPLACE_TRACE_DEBUG);
                continue;
            }
        }

        if ($eventtpl->displayeventbox === "1") {
            $counthaseventurl++;
        }

        if ('eventmonthyear'.$i < $actualdate) {
            $templatecontext['haseventdate'.$i] = false;
        }

        $eventtpl->eventimage = '';
        if (!$fs->is_area_empty($contextid, $component, 'eventdefaultimage', 0)) {
            $eventimage = @$PAGE->theme->settings->eventdefaultimage;
            $eventtpl->eventimage = moodle_url::make_pluginfile_url($contextid, $component, 'eventdefaultimage', 0,
                    dirname($eventimage), basename($eventimage), false);
        }

        if (!$fs->is_area_empty($contextid, $component, 'eventimage'.$i, 0)) {
            $imagekey = 'eventimage'.$i;
            $eventimage = @$PAGE->theme->settings->$imagekey;
            $eventtpl->eventimage = moodle_url::make_pluginfile_url($contextid, $component, 'eventimage'.$i, 0,
                    dirname($eventimage), basename($eventimage), false);
        }

        $templatecontext['events'][] = $eventtpl;
        $templatecontext['haseventurl'] = true;
    }
}

/**
 * Circle slider
 */
function theme_klassplace_load_circles_settings(&$templatecontext) {
    global $CFG, $PAGE, $OUTPUT;

    $fs = get_file_storage();

    $contextid = context_system::instance()->id;
    $component = 'theme_'.$PAGE->theme->name;

    $templatecontext['circlessectionheading'] = @$PAGE->theme->settings->circlessectionheading;
    $templatecontext['circlessectiontagline'] = @$PAGE->theme->settings->circlessectiontagline;

    $keybases = [
        'displaycircle',
        'circlecomment',
        'circleurl',
        'circlename',
        'circledesignation',
        'circlerating',
    ];

    for ($i = 1; $i < 10; $i++) {

        if (is_dir($CFG->dirroot.'/local/moodlescript')) {
            $condkey = 'circledispcondition'.$i;
            $cond = @$PAGE->theme->settings->$condkey;
            if (!local_moodlescript_evaluate_expression($cond)) {
                theme_klassplace_debug_trace("$condkey check failed. Hiding theme element...", THEME_KLASSPLACE_TRACE_DEBUG);
                continue;
            }
        }

        $displaycircletpl = new Stdclass;
        $displaycircletpl->id = $i;
        foreach ($keybases as $keybase) {
            $key = $keybase.$i;
            $displaycircletpl->$keybase = @$PAGE->theme->settings->$key;
        }

        $imagekey = 'circleimage'.$i;
        $circleimage = @$PAGE->theme->settings->$imagekey;
        if (!$fs->is_area_empty($contextid, $component, 'circleimage'.$i, 0)) {
            $displaycircletpl->circleimage = moodle_url::make_pluginfile_url($contextid, $component, 'circleimage'.$i, 0, dirname($circleimage), basename($circleimage), false);
        } else {
            $displaycircletpl->circleimage = $OUTPUT->image_url('defaultcircle', 'theme_klassplace');
        }

        $templatecontext['displaycircles'][] = $displaycircletpl;
    }
    $PAGE->requires->js('/theme/klassplace/javascript/profileSlider.js');
}

function theme_klassplace_load_clientlogos_settings(&$templatecontext) {
    global $CFG, $PAGE;

    $fs = get_file_storage();

    $contextid = context_system::instance()->id;
    $component = 'theme_'.$PAGE->theme->name;

    for ($i = 1; $i < 10 ; $i++) {

        if (is_dir($CFG->dirroot.'/local/moodlescript')) {
            $condkey = 'logosdispcondition'.$i;
            $cond = @$PAGE->theme->settings->$condkey;
            if (!local_moodlescript_evaluate_expression($cond)) {
                theme_klassplace_debug_trace("$condkey check failed. Hiding theme element...", THEME_KLASSPLACE_TRACE_DEBUG);
                continue;
            }
        }

        $settingkey = 'clientlogo'.$i;
        $settingkey2 = 'clientlogourl'.$i;
        $clientlogotpl = new StdClass;
        $clientlogo = @$PAGE->theme->settings->$settingkey; // A moodle file setting.
        if (!empty($clientlogo)) {
            if ($fs->is_area_empty($contextid, $component, 'clientlogo'.$i, 0)) {
                continue;
            }
            $clientlogotpl->ix = $i;
            $clientlogotpl->clientlogo = moodle_url::make_pluginfile_url($contextid, $component,
                    'clientlogo'.$i, 0, dirname($clientlogo),
                    basename($clientlogo), false);
            $clientlogotpl->clientlogourl = @$PAGE->theme->settings->$settingkey2;
            $templatecontext['clientlogos'][] = $clientlogotpl;
        }
    }
}

/**
 * Loads settings for social panel. As social icons may be loaded for header location
 * the $loaded static will avoid processing twice the settings.
 * @param array &$templatecontext Template data stub for the layout template.
 */
function theme_klassplace_load_social_settings(&$templatecontext) {
    global $CFG, $PAGE, $OUTPUT;
    static $loaded = false;

    if ($loaded) {
        return;
    }

    $contextid = context_system::instance()->id;
    $component = 'theme_'.$PAGE->theme->name;

    $socials = ['facebook', 'tumblr', 'twitter', 'googleplus', 'pinterest', 'instagram', 'youtube', 'flicker', 'whatsapp', 'skype', 'linkedin', 'github'];
    $extras = ['social1', 'social2', 'social3'];

    /*
     * reason no more heading/tagline
    $templatecontext['showsocialinheader'] = @$PAGE->theme->settings->showsocialinheader;
    $templatecontext['socialheading'] = format_string(@$PAGE->theme->settings->socialheading);
    $templatecontext['socialtagline'] = format_text(@$PAGE->theme->settings->socialtagline, FORMAT_MARKDOWN);
    */

    $templatecontext['displaycontactno'] = format_string(@$PAGE->theme->settings->displaycontactno);
    $templatecontext['contactno'] = format_string(@$PAGE->theme->settings->contactno);

    foreach ($socials as $social) {
        $displaysettingkey = 'display'.$social;
        $urlsettingkey = $social;
        if (!empty(@$PAGE->theme->settings->$displaysettingkey) && 
                !empty(@$PAGE->theme->settings->$urlsettingkey)) {
            $socialtpl = new StdClass;
            if ($social == 'googleplus') {
                $social = 'google-plus';
            }
            $socialtpl->name = $social;
            $socialtpl->display = @$PAGE->theme->settings->$displaysettingkey;
            $socialtpl->url = @$PAGE->theme->settings->$urlsettingkey;
            $templatecontext['social'][] = $socialtpl;
        }
    }

    // Add extra custom social defines.
    for ($i = 0; $i < 3; $i++) {
        $displaysettingkey = 'display'.$extras[$i];
        $urlsettingkey = $extras[$i];
        $iconsettingkey = 'icon'.$extras[$i];
        if (!empty(@$PAGE->theme->settings->$displaysettingkey) && 
                !empty(@$PAGE->theme->settings->$urlsettingkey)) {
            $socialtpl = new StdClass;
            $name = @$PAGE->theme->settings->$iconsettingkey;
            if (@$PAGE->theme->settings->$iconsettingkey == 'googleplus') {
                $name = $PAGE->theme->settings->$iconsettingkey = 'google-plus';
            }
            $socialtpl->name = $name;
            $socialtpl->display = @$PAGE->theme->settings->$displaysettingkey;
            $socialtpl->url = @$PAGE->theme->settings->$urlsettingkey;
            $templatecontext['social'][] = $socialtpl;
        }
    }

    $loaded = true;
}

/**
 * Checks if an url is local (absoloutely or relative).
 * @param string $url
 */
function theme_klassplace_is_local_url($url) {
    global $CFG;

    if (preg_match('#'.$CFG->wwwroot.'#', $url)) {
        return true;
    }
    if (preg_match('#^\/#', $url)) {
        return true;
    }
    return false;
}

function theme_klassplace_render_slots($pagekey, &$templatecontext) {
    global $PAGE, $OUTPUT, $CFG;

    $themename = 'theme_'.$PAGE->theme->name;

    // pagekey must match settings keys.
    switch($pagekey) {
        case 'homepage_structure_uncon': {
            $count = KLASSPLACE_FRONTPAGE_UNCON_SLOTS;
            break;
        }
        case 'homepage_structure_con':  {
            $count = KLASSPLACE_FRONTPAGE_CON_SLOTS;
            break;
        }
        case 'homepage_structure_my': {
            $count = KLASSPLACE_MY_SLOTS;
            break;
        }
        case 'homepage_structure_incourse': {
            $count = KLASSPLACE_INCOURSE_SLOTS;
            break;
        }
    }

    $hasmain = false;
    $lastrenderered = 1;

    for ($i = 1; $i <= $count; $i++) {
        $slotkey = $pagekey.$i;
        $slotdef = @$PAGE->theme->settings->$slotkey;
        if (!empty($slotdef)) {
            if ($slotdef == 'legacy') {
                $hasmain = true;
                $templatecontext['slot'.$i] = '<!-- '.$themename.'/main_content_slot -->'.$OUTPUT->main_content().'<!-- /'.$themename.'/main_content_slot -->';
            } else {
                if (is_dir($CFG->dirroot.'/local/moodlescript')) {
                    $condkey = $slotdef.'dispcondition';
                    $cond = @$PAGE->theme->settings->$condkey;
                    if (!local_moodlescript_evaluate_expression($cond)) {
                        theme_klassplace_debug_trace("$condkey check failed. Hiding theme section $slotdef...", THEME_KLASSPLACE_TRACE_DEBUG);
                        continue;
                    }
                }

                // Load settings in templatecontext.
                $settingsfunc = $themename.'_load_'.$slotdef.'_settings';
                if (function_exists($settingsfunc)) {
                    // All widget MAY NOT have. e.g. preblocks.
                    $settingsfunc($templatecontext);
                }
                // Render. Must call base theme name.

                $templatecontext['slot'.$i] = $OUTPUT->render_from_template('theme_klassplace/panel_'.$slotdef, $templatecontext);
            }
            $lastrenderered = $i;
        }
    }

    // Security : If nothing is correctly configured, ensure that output.main_content() has been output at least once.
    if (!$hasmain) {
        $lastrenderered += 1;
        $templatecontext['slot'.$lastrenderered] = '<!-- '.$themename.'/main_content_default -->'.$OUTPUT->main_content().'<!-- /'.$themename.'/main_content_default -->';
    }
}

/**
 * Sets in template context indicators data from several sources.
 * At the moment only block_course_notification if installed.
 */
function theme_klassplace_add_login_indicators(&$templatecontext) {
    global $CFG;

    $indicators = [];

    // Get indicator providing plugins from cache.
    
    $indicatingplugins = [];

    // Faked waiting for complete implementation of a cached discovering function.
    $iplugin = new StdClass;
    $iplugin->path = '/blocks/course_notification';
    $iplugin->name = 'block_course_notification';
    $indicatingplugins[] = $iplugin;

    // Scan indicator providing plugins and ask them for indicators.

    foreach ($indicatingplugins as $iplugin) {
        if (is_dir($CFG->dirroot.$iplugin->path)) {
            require_once($CFG->dirroot.$iplugin->path.'/xlib.php');
            $func = $iplugin->name.'_get_site_indicators';
            $notificationindicators = $func();
            if (is_array($notificationindicators)) {
                // Aggregate array members to indicators.
                $indicators += $notificationindicators;
            } else {
                // Add the scalar content.
                $indicators[] = $notificationindicators;
            }
        }
    }

    if (!empty($indicators)) {
        $templatecontext->hasindicators = true;
        $indicatorsstr = implode('</div><div class="siteindicator">', $indicators);
        $templatecontext->indicatorsboxcontent = '<div class="siteindicator">'.$indicatorsstr.'<div>';
    }
}