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
namespace theme_klassplace\output;

use coding_exception;
use html_writer;
use renderer_base;
use tabobject;
use tabtree;
use custom_menu_item;
use custom_menu;
use block_contents;
use navigation_node;
use action_link;
use stdClass;
use moodle_url;
use preferences_groups;
use action_menu;
use help_icon;
use single_button;
use single_select;
use paging_bar;
use url_select;
use context_course;
use pix_icon;
use theme_config;
use context_system;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/course/renderer.php');
require_once($CFG->dirroot.'/course/format/lib.php');
require_once($CFG->dirroot.'/theme/klassplace/compatlib.php');
require_once($CFG->dirroot.'/theme/klassplace/lib/dynamicstyles_lib.php');

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_klassplace
 * @copyright  2012 Bas Brands, www.basbrands.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class core_renderer extends \theme_boost\output\core_renderer {

    public function blocks($blockzone, $classes = array(), $tag = 'aside', $fakeblocksonly = false) {
        return parent::blocks($blockzone, ['d-flex justify-content-between flex-wrap']);
    }

    /**
     * Tells if header background has image or not. There are 4 possible cases :
     * - Case 1 : No header image. (Complies with standard Boost theme)
     * - Case 2 : Header image full width under navbar (between navbar and page content) and may be overriden by local course header image
     * - Case 3 : Header image under navbar, content box floating over.
     * - Case 4 : Header image over navbar (top location, to cope with some other CMS layouts) // Non available for now.
     * - Case 5 : Header image comes inside the main content (course box) and may be overriden by local course header image
     *
     * @return string HTML to display the main header.
     */
    public function headerbkglocation() {
        global $PAGE;

        $theme = theme_config::load($PAGE->theme->name);
        $setting = $theme->settings->pagelayout;
        return ($setting > 1 && $setting <= 3) ? true : false;
    }

    /**
     * Tells if header background lays at top location.
     * - Case 1 : No header image. (Complies with standard Boost theme)
     * - Case 2 : Header image full width under navbar (between navbar and page content) and may be overriden by local course header image
     * - Case 3 : Header image under navbar, content box floating over.
     * - Case 4 : Header image over navbar (to cope with some other CMS layouts)
     * - Case 5 : Header image comes inside the main content (course box) and may be overriden by local course header image
     *
     * @return string HTML to display the main header.
     */
    public function headertoplocation() {
        global $PAGE;

        $theme = theme_config::load($PAGE->theme->name);
        $setting = $theme->settings->pagelayout;
        return $setting == 4 ? true : false;
    }

    public function full_header() {
        global $PAGE, $COURSE, $CFG;

        $theme = theme_config::load($PAGE->theme->name);
        $pagelayout = $theme->settings->pagelayout;

        if (!empty($PAGE->layout_options['navbar']) && ($PAGE->layout_options['navbar'] == 'boost')) {
            // Keep boost page header on those page layouts.
            return parent::full_header();
        }

        $header = new stdClass();

        if ($pagelayout <= 4) {
            $header->headerimagelocation = false;
        }

        if (empty($PAGE->theme->settings->coursemanagementtoggle)) {
            $header->settingsmenu = $this->context_header_settings_menu();
        }
        else if (isset($COURSE->id) && $COURSE->id == 1) {
            $header->settingsmenu = $this->context_header_settings_menu();
        }

        $header->boostimage = $theme->settings->pagelayout == 5;
        $header->contextheader = html_writer::link(new moodle_url('/course/view.php', array('id' => $PAGE->course->id)) , $this->context_header());

        if ($PAGE->pagetype == 'my-index') {
            if (is_dir($CFG->dirroot.'/local/my')) {
                if (is_dir($CFG->dirroot.'/local/my/classes/modules')) {
                    include_once($CFG->dirroot.'/local/my/classes/modules/module.class.php');
                    list($view, $isstudent, $isteacher, $iscoursemanager, $isadmin) = \local_my\module\module::resolve_view();
                    $header->contextheader .= '<div class="page-context-header"><div class="page-header-headings"><h1>'.get_string($view.'pagetitle', 'local_my').'</h1></dir></dir>';
                } else {
                    // Compat with previous version of local_my.
                    include_once($CFG->dirroot.'/local/my/lib.php');
                    list($view, $isstudent, $isteacher, $iscoursemanager, $isadmin) = local_my_resolve_view();
                    $header->contextheader .= '<div class="page-context-header"><div class="page-header-headings"><h1>'.get_string($view.'pagetitle', 'local_my').'</h1></dir></dir>';
                }
            }
        }
        if ($PAGE->pagetype == 'user-profile') {
            $header->contextheader = parent::context_header();
        }

        $header->hasnavbar = empty($PAGE->layout_options['nonavbar']);
        $header->navbar = $this->navbar();
        $header->pageheadingbutton = $this->page_heading_button();
        $header->headeractions = $this->page->get_header_actions();
        $header->courseheader = $this->course_header();
        $header->headerimage = $this->headerimage();

        return $this->render_from_template('theme_klassplace/header', $header);
    }

    /*
    // Check if still needed. 
    */
    public function topheader() {
    }

    public function context_header($headerinfo = null, $headinglevel = 1) : string {
        global $COURSE;

        if ($COURSE->id == SITEID) {
            return '';
        }

        return parent::context_header($headerinfo, $headinglevel);
    }

    /**
     * Invalidates the boost override using the standard version again.
     *
     * @param string $classes A space-separated list of CSS classes
     * @param string $id An optional ID
     * @param array $attributes An array of other attributes to give the box.
     * @return string the HTML to output.
     */
    public function box_start($classes = 'generalbox', $id = null, $attributes = array()) {
        $this->opencontainers->push('box', html_writer::end_tag('div'));
        $attributes['id'] = $id;
        $attributes['class'] = 'box ' . renderer_base::prepare_classes($classes);
        return html_writer::start_tag('div', $attributes);
    }

    public function page_heading_button() {
        global $COURSE;

        $str = parent::page_heading_button();

        if ($COURSE->id > SITEID) {

            $context = context_course::instance($COURSE->id);
            if (has_capability('moodle/course:manageactivities', $context) && $COURSE->visible == 0) {
                $coursepublishurl = new moodle_url('/theme/klassplace/services/opencourse.php', ['id' => $COURSE->id]);
                $str .= get_string('hiddencourse', 'theme_klassplace').'&nbsp;&nbsp;&nbsp; <a class="open-course-button btn btn-secondary edit-btn" href="'.$coursepublishurl.'">'.get_string('publish', 'theme_klassplace').'</a>';
            }
        }
        return $str;
    }

    /**
     * This renders the breadcrumbs
     * @return string $breadcrumbs
     */
    public function navbar() : string {
        global $PAGE, $CFG;

        // Post process navbar.
        $filterednavbar = new \StdClass;
        $systemcontext = \context_system::instance();

        if (!empty($PAGE->theme->settings->breadcrumbstyle)) {
            $breadcrumbstyle = $PAGE->theme->settings->breadcrumbstyle;
            if ($breadcrumbstyle == '4') {
                $breadcrumbstyle = '1'; // Fancy style with no collapse.
            }
            $filterednavbar->styleclass = 'style'.$breadcrumbstyle;
        }

        $namedtypes = [
            0 => 'system',
            10 => 'category',
            11 => 'mycategory',
            20 => 'course',
            30 => 'structure',
            40 => 'activity',
            50 => 'resource',
            60 => 'custom',
            70 => 'setting',
            71 => 'siteadmin',
            80 => 'user',
            90 => 'container',
        ];

        if (is_dir($CFG->dirroot.'/local/my')) {
            include_once($CFG->dirroot.'/local/my/lib.php');
            /*
            $hasteachingcapability = local_my_has_capability_somewhere('local/my:isteacher');
            $haseditingcapability = local_my_has_capability_somewhere('local/my:isauthor');
            */
            global $COURSE;
            $context = context_course::instance($COURSE->id);
            $hasteachingcapability = has_capability('local/my:isteacher', $context);
            $haseditingcapability = has_capability('local/my:isauthor', $context);
        }

        $filterednavbar->get_items = array();
        $allitems = $this->page->navbar->get_items();
        $firstcat = true;
        $lastcatitem = null;
        foreach ($allitems as $item) {

            $item->itemtype = 'type-'.$item->type;
            $item->typeclass = 'node-'.@$namedtypes[$item->type];

            if (!has_capability('moodle/site:config', $systemcontext)) {
                if (!empty($PAGE->theme->settings->breadcrumbskiprootnode)) {
                    if ($item->type == \navigation_node::TYPE_ROOTNODE) {
                        continue;
                    }
                }

                if (in_array($item->type, array(\navigation_node::TYPE_CATEGORY, \navigation_node::TYPE_MY_CATEGORY))) {

                    if (is_dir($CFG->dirroot.'/local/my')) {
                        if (!empty($PAGE->theme->settings->breadcrumbmaskcatsforstudents) &&
                            !$hasteachingcapability) {
                            continue;
                        }
                    }

                    if (!empty($PAGE->theme->settings->breadcrumbmaskfirstcat) &&
                        empty($PAGE->theme->settings->breadcrumbkeeplastcatonly) && $firstcat) {
                        $firstcat = false;
                        continue;
                    }

                    if ($firstcat) {
                        $firstcat = false;
                    }

                    if (!empty($PAGE->theme->settings->breadcrumbkeeplastcatonly)) {
                        $lastcatitem = $item;
                        continue;
                    }
                }

                if (!is_null($lastcatitem)) {
                    $filterednavbar->get_items[] = $lastcatitem;
                    $lastcatitem = null;
                }
            }

            $filterednavbar->get_items[] = $item;
        }
        return $this->render_from_template('core/navbar', $filterednavbar);
    }

    public function image_url($imagename, $component = 'moodle') {
        // Strip -24, -64, -256  etc from the end of filetype icons so we
        // only need to provide one SVG, see MDL-47082.
        $imagename = \preg_replace('/-\d\d\d?$/', '', $imagename);
        return $this->page->theme->image_url($imagename, $component);
    }

    /**
     * Resolves wich image is the good candidate to stand in page global header.
     */
    public function headerimage() {
        global $CFG, $COURSE, $PAGE, $OUTPUT;

        // Get course overview files.
        if (empty($CFG->courseoverviewfileslimit)) {
            return '';
        }

        require_once ($CFG->libdir . '/filestorage/file_storage.php');
        require_once ($CFG->dirroot . '/course/lib.php');

        $fs = get_file_storage();
        $context = context_course::instance($COURSE->id);
        $files = $fs->get_area_files($context->id, 'course', 'overviewfiles', false, 'filename', false);
        if (count($files)) {
            $overviewfilesoptions = course_overviewfiles_options($COURSE->id);
            $acceptedtypes = $overviewfilesoptions['accepted_types'];
            if ($acceptedtypes !== '*') {
                // Filter only files with allowed extensions.
                require_once ($CFG->libdir . '/filelib.php');
                foreach ($files as $key => $file) {
                    if (!file_extension_in_typegroup($file->get_filename() , $acceptedtypes)) {
                        unset($files[$key]);
                    }
                }
            }
            if (count($files) > $CFG->courseoverviewfileslimit) {
                // Return no more than $CFG->courseoverviewfileslimit files.
                $files = array_slice($files, 0, $CFG->courseoverviewfileslimit, true);
            }
        }

        // Get course overview files as images - set $courseimage.
        // Take the first available large width image.
        $courseimage = '';
        foreach ($files as $file) {
            $isimage = $file->is_valid_image();
            if ($isimage) {
                $imageinfo = $file->get_imageinfo();
                if ($imageinfo['width'] > 750) {
                    $imageurl = $CFG->wwwroot.'/pluginfile.php/'.$file->get_contextid();
                    if ($COURSE->format == 'page') {
                        $component = 'format_page';
                        /* $itemid = '/0'; */
                        $itemid = ''; // ???? difficile à déterminer.
                    } else {
                        $component = 'course';
                        $itemid = '';
                    }
                    $imageurl .= '/'.$component.'/'.$file->get_filearea();
                    if (!empty($itemid)) {
                        $imageurl .= '/'.$file->get_itemid();
                    }
                    $imageurl .= $file->get_filepath().$file->get_filename();
                    $courseimage = file_encode_url($imageurl , !$isimage);
                    break;
                }
            }
        }

        $headerbg = $PAGE->theme->setting_file_url('headerdefaultimage', 'headerdefaultimage');
        $headerbgimgurl = $PAGE->theme->setting_file_url('headerdefaultimage', 'headerdefaultimage', true);
        $defaultimgurl = $OUTPUT->image_url('headerbg', 'theme');

        // Create html for header.
        $html = html_writer::start_div('headerbkg not-for-mobile');
        // If course image display it in separate div to allow css styling of inline style.
        if (!empty($PAGE->theme->settings->showcourseheaderimage) && $courseimage) {
            $html .= html_writer::start_div('withimage', array(
                'style' => 'background-image: url("' . $courseimage . '"); background-size: cover; background-position:center;
                width: 100%; height: 100%;'
            ));
            $html .= html_writer::end_div(); // End withimage inline style div.

        } else if (!empty($PAGE->theme->settings->showcourseheaderimage) && !$courseimage && isset($headerbg)) {
            $html .= html_writer::start_div('customimage', array(
                'style' => 'background-image: url("' . $headerbgimgurl . '"); background-size: cover; background-position:center;
                width: 100%; height: 100%;'
            ));
            $html .= html_writer::end_div(); // End withoutimage inline style div.

        } else if ($courseimage && isset($headerbg) && empty($PAGE->theme->settings->showcourseheaderimage)) {
            $html .= html_writer::start_div('customimage', array(
                'style' => 'background-image: url("' . $headerbgimgurl . '"); background-size: cover; background-position:center;
                width: 100%; height: 100%;'
            ));
            $html .= html_writer::end_div(); // End withoutimage inline style div.

        } else if (!$courseimage && isset($headerbg) && empty($PAGE->theme->settings->showcourseheaderimage)) {
            $html .= html_writer::start_div('customimage', array(
                'style' => 'background-image: url("' . $headerbgimgurl . '"); background-size: cover; background-position:center;
                width: 100%; height: 100%;'
            ));
            $html .= html_writer::end_div(); // End withoutimage inline style div.
        } else {
            $html .= html_writer::start_div('default', array(
                'style' => 'background-image: url("' . $defaultimgurl . '"); background-size: cover; background-position:center;
                width: 100%; height: 100%;'
            ));
            $html .= html_writer::end_div(); // End default inline style div.
        }

        $html .= html_writer::end_div();

        return $html;

    }

    /**
     * Provides the edit toggle button in navbar.
     */
    public function edit_button_fhs() {
        global $SITE, $PAGE, $USER, $CFG, $COURSE;

        if (is_dir($CFG->dirroot.'/local/sectioncontexts')) {
            $PAGE->set_other_editing_capability('local/sectioncontexts:caneditsections');
        }

        /*
        // M4 Use this button for all edit toggling needs. 
        if (!$PAGE->user_allowed_editing() || $COURSE->id <= 1) {
            return '';
        }
        */

        if (!$PAGE->user_allowed_editing()) {
            return '';
        }

        // if ($PAGE->pagelayout == 'course' || $PAGE->pagelayout == 'format_page') {
            $url = new moodle_url($PAGE->url);
            $url->param('sesskey', sesskey());
            if ($PAGE->user_is_editing()) {
                $url->param('edit', 'off');
                $btn = 'btn-danger editingbutton';
                $title = get_string('editoff', 'theme_klassplace');
                $icon = 'fa-power-off';
            } else {
                $url->param('edit', 'on');
                $btn = 'btn-success editingbutton';
                $title = get_string('editon', 'theme_klassplace');
                $icon = 'fa-edit';
            }
            return html_writer::tag('a', html_writer::start_tag('i', array(
                'class' => $icon . ' fa fa-fw'
            )) . html_writer::end_tag('i'), array(
                'href' => $url,
                'class' => 'btn edit-btn ' . $btn,
                'data-tooltip' => "tooltip",
                'data-placement' => "bottom",
                'title' => $title,
            ));
            return $output;
        // }
    }

    /**
     * Generates an array of sections and an array of activities for the given course.
     *
     * This method uses the cache to improve performance and avoid the get_fast_modinfo call
     *
     * @param stdClass $course
     * @return array Array($sections, $activities)
     */
    protected function generate_sections_and_activities(stdClass $course) {
        global $CFG;
        require_once($CFG->dirroot.'/course/lib.php');

        $modinfo = get_fast_modinfo($course);
        $sections = $modinfo->get_section_info_all();

        // For course formats using 'numsections' trim the sections list
        $courseformatoptions = course_get_format($course)->get_format_options();
        if (isset($courseformatoptions['numsections'])) {
            $sections = array_slice($sections, 0, $courseformatoptions['numsections'] + 1, true);
        }

        $activities = array();

        foreach ($sections as $key => $section) {
            // Clone and unset summary to prevent $SESSION bloat (MDL-31802).
            $sections[$key] = clone($section);
            unset($sections[$key]->summary);
            $sections[$key]->hasactivites = false;
            if (!array_key_exists($section->section, $modinfo->sections)) {
                continue;
            }
            foreach ($modinfo->sections[$section->section] as $cmid) {
                $cm = $modinfo->cms[$cmid];
                $activity = new stdClass;
                $activity->id = $cm->id;
                $activity->course = $course->id;
                $activity->section = $section->section;
                $activity->name = $cm->name;
                $activity->icon = $cm->icon;
                $activity->iconcomponent = $cm->iconcomponent;
                $activity->hidden = (!$cm->visible);
                $activity->modname = $cm->modname;
                $activity->nodetype = navigation_node::NODETYPE_LEAF;
                $activity->onclick = $cm->onclick;
                $url = $cm->url;
                if (!$url) {
                    $activity->url = null;
                    $activity->display = false;
                } else {
                    $activity->url = $url->out();
                    $activity->display = $cm->is_visible_on_course_page() ? true : false;
                }
                $activities[$cmid] = $activity;
                if ($activity->display) {
                    $sections[$key]->hasactivites = true;
                }
            }
        }

        return array($sections, $activities);
                                                                                                                                                                                                                            
    }

    /*
     * This renders the bootstrap top menu.
     *
     * This renderer is needed to enable the Bootstrap style navigation.
    */

    protected static function timeaccesscompare($a, $b) {
        // Timeaccess is lastaccess entry and timestart an enrol entry.
        if ((!empty($a->timeaccess)) && (!empty($b->timeaccess))) {
            // Both last access.
            if ($a->timeaccess == $b->timeaccess) {
                return 0;
            }
            return ($a->timeaccess > $b->timeaccess) ? -1 : 1;
        }
        else if ((!empty($a->timestart)) && (!empty($b->timestart))) {
            // Both enrol.
            if ($a->timestart == $b->timestart) {
                return 0;
            }
            return ($a->timestart > $b->timestart) ? -1 : 1;
        }
        // Must be comparing an enrol with a last access.
        // -1 is to say that 'a' comes before 'b'.
        if (!empty($a->timestart)) {
            // 'a' is the enrol entry.
            return -1;
        }
        // 'b' must be the enrol entry.
        return 1;
    }

    /*
     * This renders the bootstrap top menu.
     *
     * This renderer is needed to enable the Bootstrap style navigation.
    */
    public function klassplace_build_coursenav_menu() {
        global $CFG, $COURSE, $PAGE, $OUTPUT;

        $context = $this->page->context;

        $menu = new custom_menu();

        $hasdisplaymycourses = (empty($this->page->theme->settings->displaymycourses)) ? false : $this->page->theme->settings->displaymycourses;
        if (isloggedin() && !isguestuser() && $hasdisplaymycourses) {
            $mycoursetitle = $this->page->theme->settings->mycoursetitle;
            if ($mycoursetitle == 'module') {
                $branchtitle = get_string('mymodules', 'theme_klassplace');
                $thisbranchtitle = get_string('thismymodules', 'theme_klassplace');
                $homebranchtitle = get_string('homemymodules', 'theme_klassplace');
            } else if ($mycoursetitle == 'unit') {
                $branchtitle = get_string('myunits', 'theme_klassplace');
                $thisbranchtitle = get_string('thismyunits', 'theme_klassplace');
                $homebranchtitle = get_string('homemyunits', 'theme_klassplace');
            } else if ($mycoursetitle == 'class') {
                $branchtitle = get_string('myclasses', 'theme_klassplace');
                $thisbranchtitle = get_string('thismyclasses', 'theme_klassplace');
                $homebranchtitle = get_string('homemyclasses', 'theme_klassplace');
            } else if ($mycoursetitle == 'training') {
                $branchtitle = get_string('mytraining', 'theme_klassplace');
                $thisbranchtitle = get_string('thismytraining', 'theme_klassplace');
                $homebranchtitle = get_string('homemytraining', 'theme_klassplace');
            } else if ($mycoursetitle == 'pd') {
                $branchtitle = get_string('myprofessionaldevelopment', 'theme_klassplace');
                $thisbranchtitle = get_string('thismyprofessionaldevelopment', 'theme_klassplace');
                $homebranchtitle = get_string('homemyprofessionaldevelopment', 'theme_klassplace');
            } else if ($mycoursetitle == 'cred') {
                $branchtitle = get_string('mycred', 'theme_klassplace');
                $thisbranchtitle = get_string('thismycred', 'theme_klassplace');
                $homebranchtitle = get_string('homemycred', 'theme_klassplace');
            } else if ($mycoursetitle == 'plan') {
                $branchtitle = get_string('myplans', 'theme_klassplace');
                $thisbranchtitle = get_string('thismyplans', 'theme_klassplace');
                $homebranchtitle = get_string('homemyplans', 'theme_klassplace');
            } else if ($mycoursetitle == 'comp') {
                $branchtitle = get_string('mycomp', 'theme_klassplace');
                $thisbranchtitle = get_string('thismycomp', 'theme_klassplace');
                $homebranchtitle = get_string('homemycomp', 'theme_klassplace');
            } else if ($mycoursetitle == 'program') {
                $branchtitle = get_string('myprograms', 'theme_klassplace');
                $thisbranchtitle = get_string('thismyprograms', 'theme_klassplace');
                $homebranchtitle = get_string('homemyprograms', 'theme_klassplace');
            } else if ($mycoursetitle == 'lecture') {
                $branchtitle = get_string('mylectures', 'theme_klassplace');
                $thisbranchtitle = get_string('thismylectures', 'theme_klassplace');
                $homebranchtitle = get_string('homemylectures', 'theme_klassplace');
            } else if ($mycoursetitle == 'lesson') {
                $branchtitle = get_string('mylessons', 'theme_klassplace');
                $thisbranchtitle = get_string('thismylessons', 'theme_klassplace');
                $homebranchtitle = get_string('homemylessons', 'theme_klassplace');
            } else {
                $branchtitle = get_string('mycourses', 'theme_klassplace');
                $thisbranchtitle = get_string('thismycourses', 'theme_klassplace');
                $homebranchtitle = get_string('homemycourses', 'theme_klassplace');
            }

            $branchlabel = $branchtitle;
            $branchurl = new moodle_url('/my/index.php');
            $branchsort = 10000;

            $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);

            $dashlabel = get_string('mymoodle', 'my');
            $dashurl = new moodle_url("/my");
            $dashtitle = $dashlabel;
            $branch->add($dashlabel, $dashurl, $dashtitle);

            if ($courses = enrol_get_my_courses(NULL, 'fullname ASC')) {
                if (!empty($this->page->theme->settings->frontpagemycoursessorting)) {
                $courses = enrol_get_my_courses(null, 'sortorder ASC');
                $nomycourses = '<div class="alert alert-info alert-block">' . get_string('nomycourses', 'theme_klassplace') . '</div>';
                if ($courses) {
                    // We have something to work with.  Get the last accessed information for the user and populate.
                    global $DB, $USER;
                    $lastaccess = $DB->get_records('user_lastaccess', array('userid' => $USER->id) , '', 'courseid, timeaccess');
                    if ($lastaccess) {
                        foreach ($courses as $course) {
                            if (!empty($lastaccess[$course->id])) {
                                $course->timeaccess = $lastaccess[$course->id]->timeaccess;
                            }
                        }
                    }
                    // Determine if we need to query the enrolment and user enrolment tables.
                    $enrolquery = false;
                    foreach ($courses as $course) {
                        if (empty($course->timeaccess)) {
                            $enrolquery = true;
                            break;
                        }
                    }
                    if ($enrolquery) {
                        // We do.
                        $params = array(
                            'userid' => $USER->id
                        );
                        $sql = "SELECT ue.id, e.courseid, ue.timestart
                            FROM {enrol} e
                            JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = :userid)";
                        $enrolments = $DB->get_records_sql($sql, $params, 0, 0);
                        if ($enrolments) {
                            // Sort out any multiple enrolments on the same course.
                            $userenrolments = array();
                            foreach ($enrolments as $enrolment) {
                                if (!empty($userenrolments[$enrolment->courseid])) {
                                    if ($userenrolments[$enrolment->courseid] < $enrolment->timestart) {
                                        // Replace.
                                        $userenrolments[$enrolment->courseid] = $enrolment->timestart;
                                    }
                                } else {
                                    $userenrolments[$enrolment->courseid] = $enrolment->timestart;
                                }
                            }
                            // We don't need to worry about timeend etc. as our course list will be valid for the user from above.
                            foreach ($courses as $course) {
                                if (empty($course->timeaccess)) {
                                    $course->timestart = $userenrolments[$course->id];
                                }
                            }
                        }
                    }
                    uasort($courses, array($this,'timeaccesscompare'));
                } else {
                    return $nomycourses;
                }
                $sortorder = $lastaccess;
            } // SEARCH WHY INDENT
                foreach ($courses as $course) {
                    if ($course->visible) {
                        if (!empty($this->page->theme->settings->invertshortfullnameinnavbar)) {
                            $branch->add(format_string($course->shortname) , new moodle_url('/course/view.php?id=' . $course->id) , format_string($course->fullname));
                        } else {
                            $branch->add(format_string($course->fullname) , new moodle_url('/course/view.php?id=' . $course->id) , format_string($course->shortname));
                        }
                    }
                }
            } else {
                $noenrolments = get_string('noenrolments', 'theme_klassplace');
                $branch->add('<em>' . $noenrolments . '</em>', new moodle_url('/') , $noenrolments);
            }

            $hasdisplaythiscourse = (empty($this->page->theme->settings->displaythiscourse)) ? false : $this->page->theme->settings->displaythiscourse;
            $sections = $this->generate_sections_and_activities($COURSE);
            if ($sections && $COURSE->id > 1 && $hasdisplaythiscourse) {

                $branchlabel = $thisbranchtitle;
                $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
                $course = course_get_format($COURSE)->get_course();

                $coursehomelabel = $homebranchtitle;
                $coursehomeurl = new moodle_url('/course/view.php?', array(
                    'id' => $PAGE->course->id
                ));
                $coursehometitle = $coursehomelabel;
                $branch->add($coursehomelabel, $coursehomeurl, $coursehometitle);

                $callabel = get_string('calendar', 'calendar');
                $calurl = new moodle_url('/calendar/view.php?view=month', array(
                    'course' => $PAGE->course->id
                ));
                $caltitle = $callabel;
                $branch->add($callabel, $calurl, $caltitle);

                $participantlabel = get_string('participants', 'moodle');
                $participanturl = new moodle_url('/user/index.php', array(
                    'id' => $PAGE->course->id
                ));
                $participanttitle = $participantlabel;
                $branch->add($participantlabel, $participanturl, $participanttitle);

                if ($CFG->enablebadges == 1) {
                    $badgelabel = get_string('badges', 'badges');
                    $badgeurl = new moodle_url('/badges/view.php?type=2', array(
                        'id' => $PAGE->course->id
                    ));
                    $badgetitle = $badgelabel;
                    $branch->add($badgelabel, $badgeurl, $badgetitle);
                }

                if (get_config('core_competency', 'enabled')) {
                    $complabel = get_string('competencies', 'competency');
                    $compurl = new moodle_url('/admin/tool/lp/coursecompetencies.php', array(
                        'courseid' => $PAGE->course->id
                    ));
                    $comptitle = $complabel;
                    $branch->add($complabel, $compurl, $comptitle);
                }

                foreach ($sections[0] as $sectionid => $section) {
                    $sectionname = get_section_name($COURSE, $section);
                    if (isset($course->coursedisplay) && $course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                        $sectionurl = '/course/view.php?id=' . $COURSE->id . '&section=' . $sectionid;
                    } else {
                        $sectionurl = '/course/view.php?id=' . $COURSE->id . '#section-' . $sectionid;
                    }
                    $branch->add(format_string($sectionname) , new moodle_url($sectionurl) , format_string($sectionname));
                }
            }
        }
        return $menu;
    }

    /*
     * This renders the bootstrap top menu.
     *
     * This renderer is needed to enable the Bootstrap style navigation.
    */
    public function klassplace_custom_menu() {

        $menu = $this->klassplace_build_coursenav_menu();

        $content = '';
        foreach ($menu->get_children() as $item) {
            $context = $item->export_for_template($this);
            $content .= $this->render_from_template('core/custom_menu_item', $context);
        }

        return $content;
    }

    /**
     * This transforms the navbar menus in flat navigation records
     */
    public function klassplace_export_flatnav_menu(custom_menu $menu) {

        $flatnav = new StdClass;

        foreach ($menu->get_children() as $item) {
            $flatlink = new StdClass();
            $flatlink->showdivider = $item->divider;
            $flatlink->action = $item->url;
            $flatlink->text = $item->text;
        }

    }

    // ############

    /**
     * Renders the custom_menu
     * @param custom_menu $menu
     * @return string $content
     */
    protected function render_custom_menu(custom_menu $menu) {
        /*
        $content = '<ul class="nav navbar-nav">';
        foreach ($menu->get_children() as $item) {
            $content .= $this->render_custom_menu_item($item, 1);
        }
        $content .= '</ul>';
        return $content;
        */

        static $menucount = 0;
        // If the menu has no children return an empty string
        if (!$menu->has_children()) {
            return '';
        }
        // Increment the menu count. This is used for ID's that get worked with
        // in JavaScript as is essential
        $menucount++;
        // Initialise this custom menu (the custom menu object is contained in javascript-static
        $jscode = \js_writer::function_call_with_Y('M.core_custom_menu.init', array('custom_menu_'.$menucount));
        $jscode = "(function(){{$jscode}})";
        $this->page->requires->yui_module('node-menunav', $jscode);
        // Build the root nodes as required by YUI
        $width = '';
        if ($menucount == 1) {
            $width = 'w-100';
        }
        $attrs = [
            'id'=>'custom_menu_'.$menucount,
            'style' => 'display:none',
            'class'=> "yui3-menu yui3-menu-horizontal javascript-disabled custom-menu $width",
        ];
        $content = \html_writer::start_tag('div', $attrs);
        $content .= \html_writer::start_tag('div', array('class'=>'yui3-menu-content'));
        $content .= \html_writer::start_tag('ul', ['id' => 'menu-list-'.$menucount]);
        // Render each child
        foreach ($menu->get_children() as $item) {
            $content .= $this->render_custom_menu_item($item);
        }
        // Close the open tags
        $content .= \html_writer::end_tag('ul');
        $content .= \html_writer::end_tag('div');
        $content .= \html_writer::end_tag('div');
        // Return the custom menu
        return $content;
    }

    /**
     * Renders menu items for the custom_menu
     * @param custom_menu_item $menunode
     * @param int $level
     * @return string $content
     */
    protected function render_custom_menu_item(custom_menu_item $menunode, $level = 0) {
        global $USER, $COURSE, $CFG;

        // Required to ensure we get unique trackable id's
        static $submenucount = 0;

        if ($menunode->has_children()) {
            // If the child has menus render it as a sub menu
            $submenucount++;
            $content = html_writer::start_tag('li', ['tabindex' => 0]);
            if ($menunode->get_url() !== null) {
                $url = $menunode->get_url();
                if ($url != '') {
                    $url = $this->post_process_url_check_access($url, $xs);
                    if (!$url) {
                        return;
                    }
                    if ($xs) {
                        $class .= ' xs-only';
                    }
                }
            } else {
                $url = '#cm_submenu_'.$submenucount;
            }

            // Url context variables replacement if needed in menu.
            $url = str_replace('%25COURSEID%25', $COURSE->id, $url);
            $url = str_replace('%25USERID%25', $USER->id, $url);
            $url = str_replace('%25WWWROOT%25', $CFG->wwwroot, $url);
            $url = str_replace('%COURSEID%', $COURSE->id, $url);
            $url = str_replace('%USERID%', $USER->id, $url);
            $url = str_replace('%WWWROOT%', $CFG->wwwroot, $url);

            $attrs = array('class' => 'yui3-menu-label', 'title' => format_string($menunode->get_title()));
            if (!preg_match('#^'.$CFG->wwwroot.'#', $url)) {
                $attrs['target'] ='_blank';
            }

            $linktext = format_string($menunode->get_text());
            $linktext = $this->post_process_item_label_icons($linktext);
            $content .= html_writer::link($url, $linktext, $attrs);
            $attrs = array(
                'id' => 'cm_submenu_'.$submenucount,
                'class' => 'yui3-menu custom_menu_submenu custom_menu_submenu'.$level
            );
            $content .= html_writer::start_tag('div', $attrs);
            $content .= html_writer::start_tag('div', ['class'=>'yui3-menu-content']);
            $content .= html_writer::start_tag('ul', ['id' => 'submenu-list-'.$submenucount]);
            $subcontent = '';
            foreach ($menunode->get_children() as $menunode) {
                $subcontent .= $this->render_custom_menu_item($menunode, $level + 1);
            }
            if (!empty($subcontent)) {
                $content .= $subcontent;
                $content .= html_writer::end_tag('ul');
                $content .= html_writer::end_tag('div');
                $content .= html_writer::end_tag('div');
                $content .= html_writer::end_tag('li');
            } else {
                $content = '';
            }
        } else {
            // The node doesn't have children so produce a final menuitem.
            // Also, if the node's text matches '####', add a class so we can treat it as a divider.
            $content = '';
            if (preg_match("/^#+$/", $menunode->get_text())) {

                // This is a divider.
                $content = html_writer::start_tag('li', ['class' => 'yui3-menuitem divider']);
            } else {
                $content = html_writer::start_tag('li', ['class' => 'yui3-menuitem', 'tabindex' => 0]);
                if ($menunode->get_url() !== null) {
                    $url = $menunode->get_url();
                    if ($url != '') {
                        $url = $this->post_process_url_check_access($url, $xs);
                        if (!$url) {
                            return;
                        }
                        if ($xs) {
                            $class .= ' xs-only';
                        }
                    }
                } else {
                    $url = '#';
                }

                // Url context variables replacement if needed in menu.
                $url = str_replace('%25COURSEID%25', $COURSE->id, $url);
                $url = str_replace('%25USERID%25', $USER->id, $url);
                $url = str_replace('%25WWWROOT%25', $CFG->wwwroot, $url);
                $url = str_replace('%COURSEID%', $COURSE->id, $url);
                $url = str_replace('%USERID%', $USER->id, $url);
                $url = str_replace('%WWWROOT%', $CFG->wwwroot, $url);

                $linktext = format_string($menunode->get_text());
                $linktext = $this->post_process_item_label_icons($linktext);
                $attrs = array('class' => 'yui3-menuitem-content');
                // $attrs = array('class' => 'yui3-menuitem-content', 'title' => format_string($menunode->get_title()));
                if (!preg_match('#^'.$CFG->wwwroot.'#', $url)) {
                    $attrs['target'] = '_blank';
                    $attrs['aria-label'] = $linktext.' '.get_string('newwindow', 'theme_klassplace');
                }
                $content .= html_writer::link(
                    $url,
                    $linktext,
                    $attrs);
            }
            $content .= html_writer::end_tag('li');
        }
        // Return the sub menu.
        return $content;
    }

    /*
     * Process a catchable icon form.
     * The icon form of an url is:
     * img:<url><optionaldimension>
     * :optionaldimension : &d=<height>x<width>
     */
    function post_process_item_label_icons($label) {
        if (preg_match('/^(.*?)img:(.*?)([&\?]d=w[0-9%px]+xh[0-9%px]+)?$/', $label, $matches)) {
            $textlabel = @$matches[1]; // Textual label part.
            $imgurl = $matches[2]; // Image URL
            $sizeext = @$matches[3]; // Size specifications.
            $sizeattrs = '';
            if (!empty($sizeext)) {
                if ($sizeparts = preg_match('/[&\?]d=w([0-9%px]+)xh([0-9%px]+)/', $sizeext, $szmatches)) {
                    $width = $szmatches[1];
                    $height = $szmatches[2];
                    $sizeattrs = ' width="'.$width.'" height="'.$height.'" ';
                }
            }
            $textattr = 0;
            if (!empty($textlabel)) {
                $textlabel = htmlentities($textlabel, ENT_QUOTES);
                $textattr = ' alt="'.$textlabel.'" title="'.$textlabel.'"  ';
            }

            return '<img src="'.$imgurl.'" '.$sizeattrs.' '.$textattr.'>';
        }
        return $label;
    }

    /**
     * removes and process access markers in URL
     */
    protected function post_process_url_check_access($url, &$xs) {
        global $COURSE, $USER, $DB, $PAGE;

        $coursecontext = context_course::instance($COURSE->id);

        // Allow protected menu items to only logged in.
        if (preg_match('/^([^!]*)!(.*)$/', $url, $matches)) {
            $realurl = $matches[2];
            if ($matches[1] === '') {
                // Single ! marks for loggedin only.
                if (!isloggedin() || isguestuser()) {
                    return false;
                } else {
                    return str_replace('&amp;', '&', $realurl);
                }
            } else {
                if ($matches[1] == '0') {
                    // Single 0! marks for logged out only.
                    if (!isloggedin() || isguestuser()) {
                        return str_replace('&amp;', '&', preg_replace('/^0!/', '', $url));
                    } else {
                        return false;
                    }
                } else if ($matches[1] == 'm') {
                    // Single m! marks for mobile only.
                    $mobile = new Mobile_Detect();
                    if ($mobile->isMobile()) {
                        $xs = true;
                        return str_replace('&amp;', '&', $realurl);
                    } else {
                        return false;
                    }
                } else if ($matches[1] == 'xs') {
                    // Single xs! marks for xtrasmall screens (no conditions, just css markup.
                    $xs = true;
                    return str_replace('&amp;', '&', preg_replace('/^xs!/', '', $url));
                } else if ($matches[1] == '0m') {
                    // Single m! marks for mobile only logged out.
                    $mobile = new Mobile_Detect();
                    if ((!isloggedin() || isguestuser()) && $mobile->isMobile()) {
                        return str_replace('&amp;', '&', $realurl);
                    } else {
                        return false;
                    }
                } else {
                    $condition = $matches[1];
                    $condition = str_replace('user://', 'user:', $condition); // fix weird url-like preformatting.
                    $condition = str_replace('theme://', 'theme:', $condition); // fix weird url-like preformatting.
                    $targeturl = $matches[2];
                    if (preg_match('/^user:(.*?)(=|~)(.*)$/', $condition, $matches)) {
                        $fieldname = $matches[1];
                        $op = $matches[2];
                        $requiredvalue = $matches[3];
                        if (preg_match('/profile_field_/', $fieldname)) {
                            $fieldname = str_replace('profile_field_', '', $fieldname);
                            $field = $DB->get_record('user_info_field', array('shortname' => $fieldname));
                            if (empty($USER) || empty($field)) {
                                return false;
                            }
                            $params = array('userid' => $USER->id, 'fieldid' => $field->id);
                            $fieldvalue = $DB->get_field('user_info_data', 'data', $params);
                        } else {
                            $fieldvalue = @$USER->$fieldname;
                        }
                        if (empty($fieldvalue) ||
                                empty($requiredvalue) ||
                                    (($op == '=') && ($requiredvalue != $fieldvalue)) ||
                                        (($op == '~') && !preg_match("/$requiredvalue/", $fieldvalue))) {
                            return false;
                        }

                        return str_replace('&amp;', '&', $realurl);
                    } else if (preg_match('/^theme:(.*?)$/', $condition, $matches)) {
                        // Filter on effective theme name.
                        $theme = $matches[1];
                        if ($PAGE->theme->name != $theme) {
                            return false;
                        } else {
                            return str_replace('&amp;', '&', $realurl);
                        }
                    } else if (preg_match('/^\^theme:(.*?)$/', $condition, $matches)) {
                        // Negative filter on effective theme name.
                        $theme = $matches[1];
                        if ($PAGE->theme->name == $theme) {
                            return false;
                        } else {
                            return str_replace('&amp;', '&', $realurl);
                        }
                    } else if (preg_match('/(.*)\^$/', $condition, $matches)) {
                        // Exclusive capability check : doanything wont pass.
                        $capability = $matches[1];
                        if (!empty($USER->id) && has_capability($capability, $coursecontext, $USER->id, false)) {
                            return str_replace('&amp;', '&', $realurl);
                        } else {
                            return false;
                        }
                    } else {
                        // Normal capability check.
                        if (has_capability($condition, $coursecontext)) {
                            return str_replace('&amp;', '&', $realurl);
                        } else {
                            return false;
                        }
                    }
                }
            }
        }
        return str_replace('&amp;', '&', $url);
    }

    /**
     * Overrides lang menu
     */
    public function lang_menu() {
        global $OUTPUT, $CFG, $ME;
        static $singlejs = false;

        $lang = current_language();
        $sm = get_string_manager();

        if (!empty($CFG->langlist)) {
            $langlist = $sm->get_list_of_languages($lang);
            $langarr = explode(',', $CFG->langlist);
            $langs = [];
            foreach ($langarr as $l) {
                $langs[$l] = $langlist[$l];
            }
        } else {
            $langs = $sm->get_list_of_translations();
        }

        $template = new Stdclass();

        switch ($this->page->theme->settings->langmenustyle) {
            case 'icons': {
                $template->asmenu = false;
                $template->withicons = true;
                break;
            }

            case 'dropdown': {
                if (!$singlejs) {
                    $this->page->requires->js_call_amd('theme_klassplace/langmenu', 'init');
                    $singlejs = true;
                }
                $template->asmenu = true;
                $template->withicons = false;
                break;
            }

            case 'dropdownicons' : {
                if (!$singlejs) {
                    $this->page->requires->js_call_amd('theme_klassplace/langmenu', 'init');
                    $singlejs = true;
                }
                $template->asmenu = true;
                $template->withicons = true;
            }
        }

        foreach ($langs as $l => $lname) {

            $langtpl = new StdClass;
            $langtpl->langname = $lname;

            $currenturl = $ME;
            // consider some langs have a XX_YY form
            $currenturl = preg_replace('/(\&|\?)lang=[a-z]{2}(_[a-z]{2})?/', '', $currenturl);

            if (strpos($currenturl, '?') === false) {
                $langattr = '?lang='.$l;
            } else {
                $langattr = '&lang='.$l;
            }

            $iconattrs = array('style' => 'width:30px; height:24px;position:relative');
            if ($lang != $l) {
                $langtpl->isotherlang = true;
                $langtpl->langurl = $currenturl.$langattr;
                $iconattrs['class'] = 'shadow';
            } else {
                $langtpl->isotherlang = false;
                $template->currentlang = $lname;
                $template->currentlangicon = $OUTPUT->pix_icon('current_lang_'.$l, $lname, 'theme_klassplace', $iconattrs);
            }
            $langtpl->langicon = $OUTPUT->pix_icon('current_lang_'.$l, get_string('changeto', 'theme_klassplace', strtoupper($l)), 'theme_klassplace', $iconattrs);

            $template->langs[] = $langtpl;
        }
        return $OUTPUT->render_from_template('theme_klassplace/langmenu', $template);
    }

    // #####################

    protected function render_courseactivities_menu(custom_menu $menu) {
        global $CFG;

        $content = '';
        foreach ($menu->get_children() as $item) {
            $context = $item->export_for_template($this);
            $content .= $this->render_from_template('theme_klassplace/activitygroups', $context);
        }

        return $content;
    }

    public function courseactivities_menu() {
        global $PAGE, $COURSE, $OUTPUT, $CFG;
        $menu = new custom_menu();
        $context = $this->page->context;
        if (isset($COURSE->id) && $COURSE->id > 1) {
            $branchtitle = get_string('courseactivities', 'theme_klassplace');
            $branchlabel = $branchtitle;
            $branchurl = new moodle_url('#');
            $branch = $menu->add($branchlabel, $branchurl, $branchtitle, 10002);

            $data = theme_klassplace_get_course_activities();

            foreach ($data as $modname => $modfullname) {
                if ($modname === 'resources') {

                    $branch->add($modfullname, new moodle_url('/course/resources.php', array(
                        'id' => $PAGE->course->id
                    )));
                } else {
                    $branch->add($modfullname, new moodle_url('/mod/' . $modname . '/index.php', array(
                        'id' => $PAGE->course->id
                    )));
                }
            }
        }

        return $this->render_courseactivities_menu($menu);
    }

    /**
     * Used by footer text processing @see lib/klassplace_lib.php theme_klassplace_process_texts()
     *
     */
    public function social_icons($templatecontext, $location) {
        if (!empty($location)) {
            return $this->render_from_template('theme_klassplace/social_links_'.$location, $templatecontext);
        }
        return $this->render_from_template('theme_klassplace/social_links', $templatecontext);
    }

    /**
     * Menu for teachers and power users in navbar.
     */
    public function teacherdashmenu() {
        global $PAGE, $COURSE, $CFG, $DB, $OUTPUT;

        $course = $this->page->course;
        $context = context_course::instance($course->id);
        $showincourseonly = isset($COURSE->id) && ($COURSE->id > 1) && @$PAGE->theme->settings->coursemanagementtoggle && isloggedin() && !isguestuser();
        $haspermission = has_capability('enrol/category:config', $context) && @$PAGE->theme->settings->coursemanagementtoggle && isset($COURSE->id) && $COURSE->id > 1;
        $togglebutton = '';
        $togglebuttonstudent = '';
        $hasteacherdash = '';
        $hasstudentdash = '';
        $globalhaseasyenrollment = enrol_get_plugin('easy');
        $coursehaseasyenrollment = '';

        if ($globalhaseasyenrollment) {
            $coursehaseasyenrollment = $DB->record_exists('enrol', array(
                'courseid' => $COURSE->id,
                'enrol' => 'easy'
            ));
            $easyenrollinstance = $DB->get_record('enrol', array(
                'courseid' => $COURSE->id,
                'enrol' => 'easy'
            ));
        }

        if ($coursehaseasyenrollment && isset($COURSE->id) && $COURSE->id > 1) {
            $easycodetitle = get_string('header_coursecodes', 'enrol_easy');
            $easycodelink = new moodle_url('/enrol/editinstance.php', array(
                'courseid' => $PAGE->course->id,
                'id' => $easyenrollinstance->id,
                'type' => 'easy'
            ));
        }

        if (isloggedin() && isset($COURSE->id) && $COURSE->id > 1) {
            $course = $this->page->course;
            $context = context_course::instance($course->id);
            $hasteacherdash = has_capability('moodle/course:viewhiddenactivities', $context);
            $hasstudentdash = !has_capability('moodle/course:viewhiddenactivities', $context);
            if (has_capability('moodle/course:viewhiddenactivities', $context)) {
                $togglebutton = get_string('coursemanagementbutton', 'theme_klassplace');
            } else {
                $togglebuttonstudent = get_string('studentdashbutton', 'theme_klassplace');
            }
        }
        $siteadmintitle = get_string('siteadminquicklink', 'theme_klassplace');
        $siteadminurl = new moodle_url('/admin/search.php');

        $hasadminlink = is_siteadmin() || has_capability('moodle/site:config', \context_system::instance());

        $course = $this->page->course;

        // Send to template.
        $dashmenu = [
            'showincourseonly' => $showincourseonly,
            'togglebutton' => $togglebutton,
            'togglebuttonstudent' => $togglebuttonstudent,
            'hasteacherdash' => $hasteacherdash,
            'hasstudentdash' => $hasstudentdash,
            'haspermission' => $haspermission,
            'hasadminlink' => $hasadminlink,
            'siteadmintitle' => $siteadmintitle,
            'siteadminurl' => $siteadminurl,
        ];

        // Attach easy enrollment links if active.
        if ($globalhaseasyenrollment && $coursehaseasyenrollment) {
            $dashmenu['dashmenu'][] = array(
                'haseasyenrollment' => $coursehaseasyenrollment,
                'title' => $easycodetitle,
                'url' => $easycodelink
            );

        }

        return $this->render_from_template('theme_klassplace/teacherdashmenu', $dashmenu);
    }

    public function teacherdash() {
        global $PAGE, $COURSE, $CFG, $DB, $OUTPUT, $USER;

        require_once ($CFG->dirroot . '/completion/classes/progress.php');
        $togglebutton = '';
        $togglebuttonstudent = '';
        $hasteacherdash = '';
        $hasstudentdash = '';
        //$haseditcog = @$PAGE->theme->settings->courseeditingcog;
        $editcog = html_writer::div($this->context_header_settings_menu() , 'pull-xs-right context-header-settings-menu');
        if (isloggedin() && ISSET($COURSE->id) && $COURSE->id > 1) {
            $course = $this->page->course;
            $context = context_course::instance($course->id);
            $hasteacherdash = has_capability('moodle/course:viewhiddenactivities', $context);
            $hasstudentdash = !has_capability('moodle/course:viewhiddenactivities', $context);
            if (has_capability('moodle/course:viewhiddenactivities', $context)) {
                $togglebutton = get_string('coursemanagementbutton', 'theme_klassplace');
            } else {
                $togglebuttonstudent = get_string('studentdashbutton', 'theme_klassplace');
            }
        }
        $course = $this->page->course;
        $context = context_course::instance($course->id);
        $coursemanagementmessage = (empty($PAGE->theme->settings->coursemanagementtextbox)) ? false : format_text($PAGE->theme->settings->coursemanagementtextbox);

        $courseactivities = $this->courseactivities_menu();
        $showincourseonly = isset($COURSE->id) && $COURSE->id > 1 && @$PAGE->theme->settings->coursemanagementtoggle && isloggedin() && !isguestuser();
        $globalhaseasyenrollment = enrol_get_plugin('easy');
        $coursehaseasyenrollment = '';
        if ($globalhaseasyenrollment) {
            $coursehaseasyenrollment = $DB->record_exists('enrol', array(
                'courseid' => $COURSE->id,
                'enrol' => 'easy'
            ));
            $easyenrollinstance = $DB->get_record('enrol', array(
                'courseid' => $COURSE->id,
                'enrol' => 'easy'
            ));
        }

        // Link catagories.
        $haspermission = has_capability('enrol/category:config', $context) && @$PAGE->theme->settings->coursemanagementtoggle && isset($COURSE->id) && $COURSE->id > 1;
        $userlinks = get_string('userlinks', 'theme_klassplace');
        $userlinksdesc = get_string('userlinks_desc', 'theme_klassplace');
        $qbank = get_string('qbank', 'theme_klassplace');
        $qbankdesc = get_string('qbank_desc', 'theme_klassplace');
        $badges = get_string('badges', 'theme_klassplace');
        $badgesdesc = get_string('badges_desc', 'theme_klassplace');
        $coursemanage = get_string('coursemanage', 'theme_klassplace');
        $coursemanagedesc = get_string('coursemanage_desc', 'theme_klassplace');
        $coursemanagementmessage = (empty($PAGE->theme->settings->coursemanagementtextbox)) ? false : format_text($PAGE->theme->settings->coursemanagementtextbox, FORMAT_HTML, array(
            'noclean' => true
        ));
        $studentdashboardtextbox = (empty($PAGE->theme->settings->studentdashboardtextbox)) ? false : format_text($PAGE->theme->settings->studentdashboardtextbox, FORMAT_HTML, array(
            'noclean' => true
        ));
        // User links.
        if ($coursehaseasyenrollment && isset($COURSE->id) && $COURSE->id > 1) {
            $easycodetitle = get_string('header_coursecodes', 'enrol_easy');
            $easycodelink = new moodle_url('/enrol/editinstance.php', array(
                'courseid' => $PAGE->course->id,
                'id' => $easyenrollinstance->id,
                'type' => 'easy'
            ));
        }
        $gradestitle = get_string('gradebooksetup', 'grades');
        $gradeslink = new moodle_url('/grade/edit/tree/index.php', array(
            'id' => $PAGE->course->id
        ));
        $gradebooktitle = get_string('gradebook', 'grades');
        $gradebooklink = new moodle_url('/grade/report/grader/index.php', array(
            'id' => $PAGE->course->id
        ));
        $participantstitle = (@$PAGE->theme->settings->studentdashboardtextbox == 1) ? false : get_string('participants', 'moodle');
        $participantslink = new moodle_url('/user/index.php', array(
            'id' => $PAGE->course->id
        ));
        (empty($participantstitle)) ? false : get_string('participants', 'moodle');
        $activitycompletiontitle = get_string('activitiescompleted', 'completion');
        $activitycompletionlink = new moodle_url('/report/progress/index.php', array(
            'course' => $PAGE->course->id
        ));
        $grouptitle = get_string('groups', 'group');
        $grouplink = new moodle_url('/group/index.php', array(
            'id' => $PAGE->course->id
        ));
        $enrolmethodtitle = get_string('enrolmentinstances', 'enrol');
        $enrolmethodlink = new moodle_url('/enrol/instances.php', array(
            'id' => $PAGE->course->id
        ));

        // User reports.
        $logstitle = get_string('logs', 'moodle');
        $logslink = new moodle_url('/report/log/index.php', array(
            'id' => $PAGE->course->id
        ));
        $livelogstitle = get_string('loglive:view', 'report_loglive');
        $livelogslink = new moodle_url('/report/loglive/index.php', array(
            'id' => $PAGE->course->id
        ));
        $participationtitle = get_string('participation:view', 'report_participation');
        $participationlink = new moodle_url('/report/participation/index.php', array(
            'id' => $PAGE->course->id
        ));
        $activitytitle = get_string('outline:view', 'report_outline');
        $activitylink = new moodle_url('/report/outline/index.php', array(
            'id' => $PAGE->course->id
        ));
        $completionreporttitle = get_string('coursecompletion', 'completion');
        $completionreportlink = new moodle_url('/report/completion/index.php', array(
            'course' => $PAGE->course->id
        ));
        // FEL ADDS
        if (is_dir($CFG->dirroot.'/report/trainingsessions')) {
            $trainingsessionstitle = get_string('trainingsessions', 'report_trainingsessions');
            $trainingsessionslink = new moodle_url('/report/trainingsessions/index.php', array(
                'id' => $PAGE->course->id
            ));
        }
        if (is_dir($CFG->dirroot.'/report/learningtimecheck')) {
            if ($DB->get_field('config_plugins', 'value', ['plugin' => 'mod_learningtimecheck', 'name' => 'version'])) {
                // Is LTC properly installed ?
                $hasltcs = false;
                if ($DB->count_records('learningtimecheck', array('course' => $COURSE->id))) {
                    $learningtimechecktitle = get_string('pluginname', 'report_learningtimecheck');
                    $learningtimechecklink = new moodle_url('/report/learningtimecheck/index.php', array(
                        'id' => $PAGE->course->id
                    ));
                    $hasltcs = true;
                }
            }
        }
        // /FEL ADDS

        // Questionbank.
        $qbanktitle = get_string('questionbank', 'question');
        $qbanklink = new moodle_url('/question/edit.php', array(
            'courseid' => $PAGE->course->id
        ));
        $qcattitle = get_string('questioncategory', 'question');
        $qcatlink = new moodle_url('/question/bank/managecategories/category.php', array(
            'courseid' => $PAGE->course->id
        ));
        $qimporttitle = get_string('import', 'question');
        $qimportlink = new moodle_url('/question/bank/importquestions/import.php', array(
            'courseid' => $PAGE->course->id
        ));
        $qexporttitle = get_string('export', 'question');
        $qexportlink = new moodle_url('/question/bank/importquestions/export.php', array(
            'courseid' => $PAGE->course->id
        ));

        // Manage course.
        $courseadmintitle = get_string('courseadministration', 'moodle');
        $courseadminlink = new moodle_url('/course/admin.php', array(
            'courseid' => $PAGE->course->id
        ));
        $coursecompletiontitle = get_string('editcoursecompletionsettings', 'theme_klassplace');
        $coursecompletionlink = new moodle_url('/course/completion.php', array(
            'id' => $PAGE->course->id
        ));

        $competencytitle = get_string('competencies', 'competency');
        $competencyurl = new moodle_url('/admin/tool/lp/coursecompetencies.php', array(
            'courseid' => $PAGE->course->id
        ));
        $courseresettitle = get_string('reset', 'moodle');
        $courseresetlink = new moodle_url('/course/reset.php', array(
            'id' => $PAGE->course->id
        ));
        $coursebackuptitle = get_string('backup', 'moodle');
        $coursebackuplink = new moodle_url('/backup/backup.php', array(
            'id' => $PAGE->course->id
        ));
        $courserestoretitle = get_string('restore', 'moodle');
        $courserestorelink = new moodle_url('/backup/restorefile.php', array(
            'contextid' => $PAGE->context->id
        ));
        $courseimporttitle = get_string('import', 'moodle');
        $courseimportlink = new moodle_url('/backup/import.php', array(
            'id' => $PAGE->course->id
        ));
        $courseedittitle = get_string('editcoursesettings', 'moodle');
        $courseeditlink = new moodle_url('/course/edit.php', array(
            'id' => $PAGE->course->id
        ));

        $badgemanagetitle = get_string('managebadges', 'badges');
        $badgemanagelink = new moodle_url('/badges/index.php?type=2', array(
            'id' => $PAGE->course->id
        ));
        $badgeaddtitle = get_string('newbadge', 'badges');
        $badgeaddlink = new moodle_url('/badges/newbadge.php?type=2', array(
            'id' => $PAGE->course->id
        ));

        $recyclebintitle = get_string('pluginname', 'tool_recyclebin');
        $recyclebinlink = new moodle_url('/admin/tool/recyclebin/index.php', array(
            'contextid' => $PAGE->context->id
        ));
        $filtertitle = get_string('filtersettings', 'filters');
        $filterlink = new moodle_url('/filter/manage.php', array(
            'contextid' => $PAGE->context->id
        ));
        $eventmonitoringtitle = get_string('managesubscriptions', 'tool_monitor');
        $eventmonitoringlink = new moodle_url('/admin/tool/monitor/managerules.php', array(
            'courseid' => $PAGE->course->id
        ));

        // Student Dash.
        if (\core_completion\progress::get_course_progress_percentage($PAGE->course)) {
            $comppc = \core_completion\progress::get_course_progress_percentage($PAGE->course);
            $comppercent = number_format($comppc, 0);
            $hasprogress = true;
        } else {
            $comppercent = 0;
            $hasprogress = false;
        }
        // Progresschart disapeared in 3.6.
        // $progressbarcontext = ['hasprogress' => $hasprogress, 'progress' => $comppercent];
        // $progresschart = $this->render_from_template('block_myoverview/progress-bar', $progressbarcontext);
        $progresschartcontext = ['hasprogress' => $hasprogress, 'progress' => $comppercent];
        $progresschart = $this->render_from_template('theme_klassplace/progress-chart', $progresschartcontext);

        $gradeslinkstudent = new moodle_url('/grade/report/user/index.php', array(
            'id' => $PAGE->course->id
        ));

        $hascourseinfogroup = array(
            'title' => get_string('courseinfo', 'theme_klassplace') ,
            'icon' => 'map'
        );
        $summary = theme_klassplace_strip_html_tags($COURSE->summary);
        $summarytrim = theme_klassplace_course_trim_char($summary, 300);
        $courseinfo = array(
            array(
                'content' => format_text($summarytrim) ,
            )
        );
        $hascoursestaff = array(
            'title' => get_string('coursestaff', 'theme_klassplace') ,
            'icon' => 'users'
        );
        $courseteachers = array();
        $courseother = array();

        $showonlygroupteachers = !empty(groups_get_all_groups($course->id, $USER->id)) && $PAGE->theme->settings->showonlygroupteachers == 1;
        if ($showonlygroupteachers) {
            $groupids = array();
            $studentgroups = groups_get_all_groups($course->id, $USER->id);
            foreach ($studentgroups as $grp) {
                $groupids[] = $grp->id;
            }
        }

        // If you created custom roles, please change the shortname value to match the name of your role.  This is teacher.
        $role = $DB->get_record('role', array(
            'shortname' => 'editingteacher'
        ));
        if ($role) {
            $context = context_course::instance($PAGE->course->id);
            $teachers = get_role_users($role->id, $context, false, 'u.id, u.firstname, u.middlename, u.lastname, u.alternatename,
                    u.firstnamephonetic, u.lastnamephonetic, u.email, u.picture, u.maildisplay,
                    u.imagealt');

            foreach ($teachers as $staff) {
                if ($showonlygroupteachers) {
                    $staffgroups = groups_get_all_groups($course->id, $staff->id);
                    $found = false;
                    foreach ($staffgroups as $grp) {
                        if (in_array($grp->id, $groupids)) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        continue;
                    }
                }
                $picture = $OUTPUT->user_picture($staff, array(
                    'size' => 50
                ));
                $messaging = new moodle_url('/message/index.php', array(
                    'id' => $staff->id
                ));
                $hasmessaging = $CFG->messaging == 1;
                $courseteachers[] = array(
                    'name' => $staff->firstname . ' ' . $staff->lastname . ' ' . $staff->alternatename,
                    'email' => $staff->email,
                    'picture' => $picture,
                    'messaging' => $messaging,
                    'hasmessaging' => $hasmessaging,
                    'hasemail' => $staff->maildisplay
                );
            }
        }

        // If you created custom roles, please change the shortname value to match the name of your role.  This is non-editing teacher.
        $role = $DB->get_record('role', array(
            'shortname' => 'teacher'
        ));
        if ($role) {
            $context = context_course::instance($PAGE->course->id);
            $teachers = get_role_users($role->id, $context, false, 'u.id, u.firstname, u.middlename, u.lastname, u.alternatename,
                    u.firstnamephonetic, u.lastnamephonetic, u.email, u.picture, u.maildisplay,
                    u.imagealt');
            foreach ($teachers as $staff) {
                if ($showonlygroupteachers) {
                    $staffgroups = groups_get_all_groups($course->id, $staff->id);
                    $found = false;
                    foreach ($staffgroups as $grp) {
                        if (in_array($grp->id, $groupids)) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        continue;
                    }
                }
                $picture = $OUTPUT->user_picture($staff, array(
                    'size' => 50
                ));
                $messaging = new moodle_url('/message/index.php', array(
                    'id' => $staff->id
                ));
                $hasmessaging = $CFG->messaging == 1;
                $courseother[] = array(
                    'name' => $staff->firstname . ' ' . $staff->lastname,
                    'email' => $staff->email,
                    'picture' => $picture,
                    'messaging' => $messaging,
                    'hasmessaging' => $hasmessaging,
                    'hasemail' => $staff->maildisplay
                );
            }
        }
        $activitylinkstitle = get_string('activitylinkstitle', 'theme_klassplace');
        $activitylinkstitle_desc = get_string('activitylinkstitle_desc', 'theme_klassplace');
        $mygradestext = get_string('mygradestext', 'theme_klassplace');
        $myprogresstext = get_string('myprogresstext', 'theme_klassplace');
        $studentcoursemanage = get_string('courseadministration', 'moodle');

        // Permissionchecks for teacher access.
        $hasquestionpermission = has_capability('moodle/question:add', $context);
        $hasbadgepermission = has_capability('moodle/badges:awardbadge', $context);
        $hascoursepermission = has_capability('moodle/backup:backupcourse', $context);
        $hasuserpermission = has_capability('moodle/course:viewhiddenactivities', $context);
        $hasgradebookshow = $PAGE->course->showgrades == 1 && $PAGE->theme->settings->showstudentgrades == 1;
        $hascompletionshow = $PAGE->course->enablecompletion == 1 && $PAGE->theme->settings->showstudentcompletion == 1;
        $hascourseadminshow = $PAGE->theme->settings->showcourseadminstudents == 1;
        $hascompetency = get_config('core_competency', 'enabled');

        // Send to template.
        //$haseditcog = $PAGE->theme->settings->courseeditingcog;
        $editcog = html_writer::div($this->context_header_settings_menu() , 'pull-xs-right context-header-settings-menu');
        $dashlinks = [
            'showincourseonly' => $showincourseonly,
            'haspermission' => $haspermission,
            'courseactivities' => $courseactivities,
            'togglebutton' => $togglebutton,
            'togglebuttonstudent' => $togglebuttonstudent,
            'userlinkstitle' => $userlinks,
            'userlinksdesc' => $userlinksdesc,
            'qbanktitle' => $qbank,
            'activitylinkstitle' => $activitylinkstitle,
            'activitylinkstitle_desc' => $activitylinkstitle_desc,
            'qbankdesc' => $qbankdesc,
            'badgestitle' => $badges,
            'badgesdesc' => $badgesdesc,
            'coursemanagetitle' => $coursemanage,
            'coursemanagedesc' => $coursemanagedesc,
            'coursemanagementmessage' => $coursemanagementmessage,
            'progresschart' => $progresschart,
            'gradeslink' => $gradeslink,
            'gradeslinkstudent' => $gradeslinkstudent,
            'hascourseinfogroup' => $hascourseinfogroup,
            'courseinfo' => $courseinfo,
            'hascoursestaffgroup' => $hascoursestaff,
            'courseteachers' => $courseteachers,
            'courseother' => $courseother,
            'myprogresstext' => $myprogresstext,
            'mygradestext' => $mygradestext,
            'studentdashboardtextbox' => $studentdashboardtextbox,
            'hasteacherdash' => $hasteacherdash,
            //'haseditcog' => $haseditcog,
            'editcog' => $editcog,
            'teacherdash' => array(
                'hasquestionpermission' => $hasquestionpermission,
                'hasbadgepermission' => $hasbadgepermission,
                'hascoursepermission' => $hascoursepermission,
                'hasuserpermission' => $hasuserpermission
            ),
            'hasstudentdash' => $hasstudentdash,
            'hasgradebookshow' => $hasgradebookshow,
            'hascompletionshow' => $hascompletionshow,
            'studentcourseadminlink' => $courseadminlink,
            'studentcoursemanage' => $studentcoursemanage,
            'hascourseadminshow' => $hascourseadminshow,
            'hascompetency' => $hascompetency,
            'competencytitle' => $competencytitle,
            'competencyurl' => $competencyurl,
            'dashlinks' => array()
        ];

        // User links section
        if (has_capability('moodle/grade:manage', $context)) {
            $dashlinks['dashlinks'][] = array(
                'hasuserlinks' => $gradestitle,
                'title' => $gradestitle,
                'url' => $gradeslink
            );
        }
        $dashlinks['dashlinks'][] = array(
            'hasuserlinks' => $gradebooktitle,
            'title' => $gradebooktitle,
            'url' => $gradebooklink
        );
        $dashlinks['dashlinks'][] = array(
            'hasuserlinks' => $participantstitle,
            'title' => $participantstitle,
            'url' => $participantslink
        );
        $dashlinks['dashlinks'][] = array(
            'hasuserlinks' => $grouptitle,
            'title' => $grouptitle,
            'url' => $grouplink
        );
        $dashlinks['dashlinks'][] = array(
            'hasuserlinks' => $enrolmethodtitle,
            'title' => $enrolmethodtitle,
            'url' => $enrolmethodlink
        );
        $dashlinks['dashlinks'][] = array(
            'hasuserlinks' => $activitycompletiontitle,
            'title' => $activitycompletiontitle,
            'url' => $activitycompletionlink
        );
        $dashlinks['dashlinks'][] = array(
            'hasuserlinks' => $completionreporttitle,
            'title' => $completionreporttitle,
            'url' => $completionreportlink
        );
        $dashlinks['dashlinks'][] = array(
            'hasuserlinks' => $logstitle,
            'title' => $logstitle,
            'url' => $logslink
        );
        /*
        $dashlinks['dashlinks'][] = array(
            'hasuserlinks' => $livelogstitle,
            'title' => $livelogstitle,
            'url' => $livelogslink
        );
        */
        if (is_dir($CFG->dirroot.'/report/trainingsessions')) {
            $dashlinks['dashlinks'][] = array(
                'hasuserlinks' => $trainingsessionstitle,
                'title' => $trainingsessionstitle,
                'url' => $trainingsessionslink
            );
        }
        if (is_dir($CFG->dirroot.'/report/learningtimecheck') && $hasltcs) {
            $dashlinks['dashlinks'][] = array(
                'hasuserlinks' => $learningtimechecktitle,
                'title' => $learningtimechecktitle,
                'url' => $learningtimechecklink
            );
        }
        $dashlinks['dashlinks'][] = array(
            'hasuserlinks' => $participationtitle,
            'title' => $participationtitle,
            'url' => $participationlink
        );
        /*
        $dashlinks['dashlinks'][] = array(
            'hasuserlinks' => $activitytitle,
            'title' => $activitytitle,
            'url' => $activitylink
        );
        */

        // Question bank
        $dashlinks['dashlinks'][] = array(
            'hasqbanklinks' => $qbanktitle,
            'title' => $qbanktitle,
            'url' => $qbanklink
        );
        $dashlinks['dashlinks'][] = array(
            'hasqbanklinks' => $qcattitle,
            'title' => $qcattitle,
            'url' => $qcatlink
        );
        $dashlinks['dashlinks'][] = array(
            'hasqbanklinks' => $qimporttitle,
            'title' => $qimporttitle,
            'url' => $qimportlink
        );
        $dashlinks['dashlinks'][] = array(
            'hasqbanklinks' => $qexporttitle,
            'title' => $qexporttitle,
            'url' => $qexportlink
        );

        // Course management
        if (has_capability('moodle/course:update', $context)) {
            $dashlinks['dashlinks'][] = array(
                'hascoursemanagelinks' => $courseedittitle,
                'title' => $courseedittitle,
                'url' => $courseeditlink
            );
        }
        $dashlinks['dashlinks'][] = array(
            'hascoursemanagelinks' => $coursecompletiontitle,
            'title' => $coursecompletiontitle,
            'url' => $coursecompletionlink
        );
        if (has_capability('moodle/competency:coursecompetencyview', $context)) {
            $dashlinks['dashlinks'][] = array(
                'hascoursemanagelinks' => $hascompetency,
                'title' => $competencytitle,
                'url' => $competencyurl
            );
        }
        if (has_capability('moodle/course:update', $context)) {
            $dashlinks['dashlinks'][] = array(
                'hascoursemanagelinks' => $courseadmintitle,
                'title' => $courseadmintitle,
                'url' => $courseadminlink
            );
        }
        if (has_capability('moodle/course:reset', $context)) {
            $dashlinks['dashlinks'][] = array(
                'hascoursemanagelinks' => $courseresettitle,
                'title' => $courseresettitle,
                'url' => $courseresetlink
            );
        }
        if (has_capability('moodle/backup:backupcourse', $context)) {
            $dashlinks['dashlinks'][] = array(
                'hascoursemanagelinks' => $coursebackuptitle,
                'title' => $coursebackuptitle,
                'url' => $coursebackuplink
            );
        }
        if (has_capability('moodle/restore:restorecourse', $context)) {
            $dashlinks['dashlinks'][] = array(
                'hascoursemanagelinks' => $courserestoretitle,
                'title' => $courserestoretitle,
                'url' => $courserestorelink
            );
            $dashlinks['dashlinks'][] = array(
                'hascoursemanagelinks' => $courseimporttitle,
                'title' => $courseimporttitle,
                'url' => $courseimportlink
            );
        }
        $dashlinks['dashlinks'][] = array(
            'hascoursemanagelinks' => $recyclebintitle,
            'title' => $recyclebintitle,
            'url' => $recyclebinlink
        );
        $dashlinks['dashlinks'][] = array(
            'hascoursemanagelinks' => $filtertitle,
            'title' => $filtertitle,
            'url' => $filterlink
        );
        $dashlinks['dashlinks'][] = array(
            'hascoursemanagelinks' => $eventmonitoringtitle,
            'title' => $eventmonitoringtitle,
            'url' => $eventmonitoringlink
        );

        // Badge management
        $dashlinks['dashlinks'][] = array(
            'hasbadgelinks' => $badgemanagetitle,
            'title' => $badgemanagetitle,
            'url' => $badgemanagelink
        );
        $dashlinks['dashlinks'][] = array(
            'hasbadgelinks' => $badgeaddtitle,
            'title' => $badgeaddtitle,
            'url' => $badgeaddlink
        );

        // Attach easy enrollment links if active.
        if ($globalhaseasyenrollment && $coursehaseasyenrollment) {
            $dashlinks['dashlinks'][] = array(
                'haseasyenrollment' => $coursehaseasyenrollment,
                'title' => $easycodetitle,
                'url' => $easycodelink
            );
        }
        return $this->render_from_template('theme_klassplace/teacherdash', $dashlinks);
    }

    public function header() {
        global $CFG, $PAGE;

        if ($PAGE->pagelayout != 'embedded') {
            if (file_exists($CFG->dirroot.'/blocks/livedesk/lib.php')) {
                include_once $CFG->dirroot.'/blocks/moodleblock.class.php';
                include_once $CFG->dirroot.'/blocks/livedesk/lib.php';
                block_livedesk_setup_theme_requires();
                block_livedesk_setup_theme_notification();
            }
        }

        // Add additional javascript requires from the theme settings.
        if (!empty($PAGE->theme->settings->pagetyperestrictions)) {
            if (!preg_match('\\b'.$PAGE->pagetype.'\\b', $PAGE->theme->settings->pagetyperestrictions)) {
                return parent::header();
            }
        }

        $fs = get_file_storage();
        $systemcontext = \context_system::instance();
        $contextid = $systemcontext->id;
        $component = 'theme_'.$PAGE->theme->name;
        $filearea = 'additionaljs';
        // Only take the first level js files and entry points.
        $addjsfiles = $fs->get_directory_files($contextid, $component, $filearea, 0, '/', false, false, "filename");
        if (!empty($addjsfiles)) {
            foreach ($addjsfiles as $js) {
                $scripturl = \moodle_url::make_pluginfile_url($contextid, $component, $filearea, 0, '/', $js->get_filename());
                $PAGE->requires->js($scripturl);
            }
        }

        return parent::header();
    }

    /**
     * Outputs the page's footer
     * @return string HTML fragment
     */
    public function footer() {
        global $CFG, $PAGE, $COURSE, $OUTPUT, $DB, $SESSION;

        $output = $this->container_end_all(true);

        $footer = parent::footer();
        @list($footerpart, $endhtml) = explode('</footer>', $footer);

        $perfreport = '';
        if ($PAGE->pagelayout != 'embedded') {
            if (file_exists($CFG->dirroot.'/local/advancedperfs/perflib.php')) {
                include_once($CFG->dirroot.'/local/advancedperfs/perflib.php');
                $pm = \performance_monitor::instance();
                $perfreport = $pm->print_report();
            }
        }

        // Prepare return to course button.

        $returnablemodules = ['resource', 'forum', 'folder', 'page', 'hvp', 'assign',
        'url', 'questionnaire', 'workshop', 'bigbluebuttonbn'];
        $pagetypeexceptions = array('page-mod-quiz-attempt');

        $button = '';

        $cm = $PAGE->cm;
        $me = me();
        if (!empty($cm) && in_array($PAGE->cm->modname, $returnablemodules) && !preg_match('/course\\/modedit\.php/', $me)) {
            if ($PAGE->pagelayout != 'embedded') {
                if (!in_array($PAGE->pagetype, $pagetypeexceptions) && ($COURSE->id != SITEID)) {
                    $sectionsection = $DB->get_field('course_sections', 'section', array('id' => $cm->section));
                    $courseurl = new moodle_url('/course/view.php', array('id' => $COURSE->id));
                    $courseurl->set_anchor('section-'.$sectionsection);
                    $button = '<div class="by-theme-return-button">';
                    $attrs = array('title' => get_string('backtocourse', 'theme_klassplace'), 'class' => 'btn btn-default');
                    $button .= html_writer::link($courseurl, get_string('backtocourse', 'theme_klassplace'), $attrs);
                    $button .= '</div>';
                }
            }
        }

        $session = '';
        if ($CFG->debug == DEBUG_DEVELOPER) {
            if (optional_param('sessionreset', false, PARAM_BOOL)) {
                unset($SESSION);
                $SESSION = new StdClass;
            }
            if (optional_param('sessiondebug', false, PARAM_BOOL)) {
                $session = '<pre>'.print_r($SESSION, true).'</pre>';
            }
        }

        return $button.$output.$footerpart.$perfreport.$session.'</footer>'.$endhtml;
    }

    public function footnote() {
        global $PAGE;

        $footnote = '';
        $footnote = (empty($PAGE->theme->settings->footnote)) ? false : format_text($PAGE->theme->settings->footnote);

        return $footnote;
    }

    /*
    // OBSOLETE : Settings were removed.
    public function brandorganization_footer() {

        $theme = theme_config::load('klassplace');

        $setting = format_string($theme->settings->brandorganization);
        return $setting != '' ? $setting : '';
    }

    public function brandwebsite_footer() {

        $theme = theme_config::load('klassplace');

        $setting = $theme->settings->brandwebsite;
        return $setting != '' ? $setting : '';
    }

    public function brandphone_footer() {

        $theme = theme_config::load('klassplace');

        $setting = $theme->settings->brandphone;
        return $setting != '' ? $setting : '';
    }

    public function brandemail_footer() {

        $theme = theme_config::load('klassplace');

        $setting = $theme->settings->brandemail;
        return $setting != '' ? $setting : '';
    }
    */

    public function logintext_custom() {
        global $PAGE;

        $logintext_custom = '';
        $logintext_custom = (empty($PAGE->theme->settings->fptextboxlogout)) ? false : format_text($PAGE->theme->settings->fptextboxlogout);
        return $logintext_custom;
    }

    /**
     * Possibly DEPRECATED. login form is filled by the layout/login.php
     */
    public function render_login(\core_auth\output\login $form) {
        global $SITE, $PAGE;

        $context = $form->export_for_template($this);

        // Override because rendering is not supported in template yet.
        $context->cookieshelpiconformatted = $this->help_icon('cookiesenabled');
        $context->errorformatted = $this->error_text($context->error);
        $url = $this->get_logo_url();

        // Additional settings.
        $context->hasshowtoggle = !empty($PAGE->theme->settings->showpasswordbutton);

        // Custom logins.
        // $context->logintext_custom = format_text($PAGE->theme->settings->fptextboxlogout);
        $context->logintopimage = $PAGE->theme->setting_file_url('logintopimage', 'logintopimage');
        $context->loginhelpbuttonurl = $PAGE->theme->settings->loginhelpbuttonurl;
        $context->helpbuttontext = $PAGE->theme->settings->loginhelpbutton;
        $context->hasloginhelpbutton = !empty($PAGE->theme->settings->loginhelpbuttonurl);
        $context->alertboxmessage = format_text($PAGE->theme->settings->alertboxmessage, FORMAT_HTML, ['noclean' => true]);
        if ($url) {
            $url = $url->out(false);
        }
        $context->logourl = $url;
        $context->logintoken = theme_klassplace_get_login_token();
        $context->sitename = format_string($SITE->fullname, true, ['context' => context_course::instance(SITEID), "escape" => false]);

        return $this->render_from_template('core/loginform', $context);
    }

    public function favicon() {
        $favicon = $this->page->theme->setting_file_url('favicon', 'favicon');

        if (empty($favicon)) {
            return $this->page->theme->image_url('favicon', 'theme');
        } else {

            if (is_string($favicon)) {
                // Due to special behaviour of setting_file_url();
                $favicon = preg_replace('#.*?(/pluginfile\\.php.*$)#', '\\1', $favicon);
                $favicon = new moodle_url($favicon);
            }

            return $favicon;
        }
    }

    public function display_ilearn_secure_alert() {
        global $DB, $PAGE;

        if (strpos($PAGE->url, '/mod/quiz/view.php') === false) {
            return false;
        }

        $cm = $PAGE->cm;

        if ($cm) {
            $quiz = $DB->get_record('quiz', array(
                'id' => $cm->instance
            ));
            $globalhasilearnsecureplugin = $DB->get_manager()->table_exists('quizaccess_ilearnbrowser') ? true : false;
        }
        // Turn off alert while taking a quiz.
        if (strpos($PAGE->url, '/mod/quiz/attempt.php')) {
            return false;
        }
        if ($cm && $quiz && $globalhasilearnsecureplugin) {
            $quiz_record = $DB->get_record('quizaccess_ilearnbrowser', array(
                'quiz_id' => $quiz->id
            ));
            if ($quiz_record && $quiz_record->browserrequired == 1) {
                return true;
            }
        }
        return false;
    }

    /**
     // DEPRECATED : Setting removed
    public function show_teacher_navbarcolor() {
        global $PAGE;
        $theme = theme_config::load('klassplace');
        $context = $this->page->context;
        $hasteacherrole = has_capability('moodle/course:viewhiddenactivities', $context);

        if ($PAGE->theme->settings->navbarcolorswitch == 1 && $hasteacherrole) {
            return true;
        }
        return false;
    }

    public function show_student_navbarcolor() {
        global $PAGE;
        $theme = theme_config::load('klassplace');
        $context = $this->page->context;
        $hasstudentrole = !has_capability('moodle/course:viewhiddenactivities', $context);

        if ($PAGE->theme->settings->navbarcolorswitch == 1 && $hasstudentrole) {
            return true;
        }
        return false;
    }
    */

    /*
    // DEPRECATED : Setting Removed. Fonts managed through dynamic_styles
    public function headingfont() {
        $theme = theme_config::load('klassplace');
        $setting = $theme->settings->headingfont;
        return $setting != '' ? $setting : '';
    }
    */

    /**
     * This clones the standard renderer of heading_with_help().
     * Traps the 
     */
    public function heading_with_help($text, $helpidentifier, $component = 'moodle', $icon = '', $iconalt = '', $level = 2, $classnames = null) {
        global $CFG, $OUTPUT;

        // Check if special documentation link is needed.
        if (file_exists($CFG->dirroot.'/local/vflibs/vfdoclib.php')) {
            include_once($CFG->dirroot.'/local/vflibs/vfdoclib.php');

            if (empty($helpidentifier)) {
                return parent::heading($text, $level);
            }

            $helpstring = get_string($helpidentifier, $component);

            $docurl = local_vflibs_make_doc_url($component);

            if ($docurl) {
                // If we found a special documentation url.
                $html = '';
                $icon = $OUTPUT->pix_icon('help', $helpstring);
                $text = $text.' <a href="'.$docurl.'" target="_blank">'.$icon.'</a>';
                $html = $OUTPUT->heading($text, $level);
                return $html;
            }
        }

        return parent::heading_with_help($text, $helpidentifier, $component, $icon, $iconalt, $level, $classnames);
    }

    /**
     * Return the site's compact logo URL, if any.
     *
     * @param int $maxwidth The maximum width, or null when the maximum width does not matter.
     * @param int $maxheight The maximum height, or null when the maximum height does not matter.
     * @return moodle_url|false
     */
    public function get_compact_logo_url($maxwidth = 100, $maxheight = 100) {
        global $CFG, $PAGE;

        $defaulturl = parent::get_compact_logo_url($maxwidth, $maxheight);

        $themecomponent = 'theme_'.$PAGE->theme->name;
        $logo = get_config($themecomponent, 'headerlogo');
        if (empty($logo)) {
            return $defaulturl;
        }

        // Hide the requested size in the file path.
        $filepath = ((int) $maxwidth . 'x' . (int) $maxheight) . '/';

        // Use $CFG->themerev to prevent browser caching when the file changes.
        $logourl = \moodle_url::make_pluginfile_url(\context_system::instance()->id, $themecomponent, 'headerlogo', 0, $filepath, $logo);
        return $logourl;
    }

    /**
     * Prints a nice side block with an optional header.
     *
     * @param block_contents $bc HTML for the content
     * @param string $region the region the block is appearing in.
     * @return string the HTML to be output.
     */
    public function block(block_contents $bc, $region) {
        global $COURSE, $PAGE;

        static $firstblockinregion = array();

        if (!array_key_exists($region, $firstblockinregion)) {
            $firstblockinregion[$region] = true;
        }

        $bc = clone($bc); // Avoid messing up the object passed in.
        if (empty($bc->blockinstanceid) || !strip_tags($bc->title)) {
            $bc->collapsible = block_contents::NOT_HIDEABLE;
        }

        $id = !empty($bc->attributes['id']) ? $bc->attributes['id'] : uniqid('block-');
        $context = new stdClass();
        $context->skipid = $bc->skipid;
        $context->blockinstanceid = (empty($bc->blockinstanceid)) ? uniqid() : $bc->blockinstanceid;
        $context->dockable = $bc->dockable;
        $context->id = $id;
        $context->hidden = $bc->collapsible == block_contents::HIDDEN;
        $context->skiptitle = strip_tags($bc->title);
        $context->showskiplink = !empty($context->skiptitle);
        $context->arialabel = $bc->arialabel;
        $context->ariarole = !empty($bc->attributes['role']) ? $bc->attributes['role'] : 'complementary';
        $context->type = $bc->attributes['data-block'];
        $context->title = $bc->title;
        $context->content = $bc->content;
        $context->annotation = $bc->annotation;
        $context->footer = $bc->footer;
        $context->class = $bc->attributes['class'];
        $context->region = $region;
        if ($COURSE->format == 'page' && $context->type == 'page_module') {
            $context->modname = @$bc->modname;
        }
        $context->cancollapse = ($COURSE->format != 'page') &&
                        !empty($PAGE->theme->settings->allowblockregionscollapse);
        $context->hascontrols = !empty($bc->controls);
        if ($context->hascontrols) {
            $context->controls = $this->block_controls($bc->controls, $id);
        }

        // $context->iscollapsible = $bc->collapsible;

        if ($PAGE->theme->settings->allowblockregionscollapse == 0) {
            $context->hidden = false;
        } else if ($PAGE->theme->settings->allowblockregionscollapse == 1) {
            if ($firstblockinregion[$region]) {
                $context->firstblockclass = 'show';
                $firstblockinregion[$region] = false;
                $context->hidden = false;
            } else {
                $context->hidden = true;
            }
        } else {
            $context->hidden = true;
        }

        return $this->render_from_template('theme_klassplace/block', $context);
    }

    /**
     * Renders the skip links for the page.
     * TODO : avoid the standard skip_links to be produced in local/my page.
     * HOW : call a hook in the local_my plugin if installed. (modularity).
     * Or : keep only the first ? 
     *
     * @param array $links List of skip links.
     * @return string HTML for the skip links.
     */
    public function render_skip_links($links) {
        $context = ['links' => []];

        foreach ($links as $url => $text) {
            $context['links'][] = ['url' => $url, 'text' => $text];
        }

        return $this->render_from_template('core/skip_links', $context);
    }

    /**
     * Whether we should display the logo in the navbar.
     *
     * We will when there are no main logos, and we have compact logo.
     *
     * @return bool
     */
    public function should_display_navbar_logo() {
        $logo = $this->get_compact_logo_url();
        return !empty($logo);
    }

    public function check_dyslexic_state() {
        $dyspref = get_user_preferences('dyslexic_helper');
        $dyslexic = optional_param('dys', $dyspref, PARAM_BOOL);

        if ($dyslexic != $dyspref) {
            // Change state if required.
            set_user_preferences(['dyslexic_helper' => $dyslexic]);
            if ($dyslexic) {
                set_user_preferences(['highcontrast_helper' => 0]);
            }
        }
    }

    public function get_dyslexic_state() {
        return get_user_preferences('dyslexic_helper');
    }

    public function check_highcontrast_state() {
        $hcpref = get_user_preferences('highcontrast_helper');
        $hc = optional_param('hc', $hcpref, PARAM_BOOL);

        if ($hc != $hcpref) {
            // Change state if required.
            set_user_preferences(['highcontrast_helper' => $hc]);
            if ($hc) {
                set_user_preferences(['dyslexic_helper' => 0]);
            }
        }
    }

    public function get_highcontrast_state() {
        return get_user_preferences('highcontrast_helper');
    }

    public function get_dyslexic_url() {
        $dyspref = get_user_preferences('dyslexic_helper');
        $url = new moodle_url(qualified_me());
        $url->param('dys', ($dyspref) ? 0 : 1);
        return $url;
    }

    public function get_highcontrast_url() {
        $hcpref = get_user_preferences('highcontrast_helper');
        $url = new moodle_url(qualified_me());
        $url->param('hc', ($hcpref) ? 0 : 1);
        return $url;
    }

    public function get_dynamic_css($theme) {
        return theme_klassplace_get_dynamic_css($theme);
    }

    /**
     * returns index url for home link
     */
    public function index_url() {
        global $CFG;

        $standardhome = get_home_page();

        $localoverride = false;
        if (is_dir($CFG->dirroot.'/local/my')) {
            include_once($CFG->dirroot.'/local/my/lib.php');
            $localoverride = local_my_hide_home();
        }

        if ($localoverride || ($standardhome == HOMEPAGE_MY)) {
            return $CFG->wwwroot.'/my';
        }

        return $CFG->wwwroot.'/index.php?redirect=0';
    }

    /**
     * returns index icon for home link
     */
    public function index_icon() {
        global $CFG;

        $standardhome = get_home_page();

        $localoverride = false;
        if (is_dir($CFG->dirroot.'/local/my')) {
            include_once($CFG->dirroot.'/local/my/lib.php');
            $localoverride = local_my_hide_home();
        }

        if ($localoverride || ($standardhome == HOMEPAGE_MY)) {
            return 'fa-tachometer';
        }

        return 'fa-home';
    }

    /**
     * Overrides standard code considering also local_search lucene legacy plugin.
     *
     * @param  string $id     The search box wrapper div id, defaults to an autogenerated one.
     * @return string         HTML with the search form hidden by default.
     */
    public function search_box($id = false) {
        global $CFG, $SESSION, $OUTPUT;

        $trylegacy = false;
        if (!is_dir($CFG->dirroot.'/local/search')) {
            $trylegacy = true;
        }

        $localsearchconfig = get_config('local_search');

        // Accessing $CFG directly as using \core_search::is_global_search_enabled would
        // result in an extra included file for each site, even the ones where global search
        // is disabled.
        if (empty($localsearchconfig->enable) || !has_capability('local/search:query', context_system::instance())) {
            $trylegacy = true;
        }

        if (empty($CFG->enableglobalsearch)) {
            return '';
        }

        if ($trylegacy) {
            return parent::search_box($id = false);
        }

        if ($id == false) {
            $id = uniqid();
        } else {
            // Needs to be cleaned, we use it for the input id.
            $id = clean_param($id, PARAM_ALPHANUMEXT);
        }
        /*
        $this->page->requires->js_call_amd('core/search-input', 'init', array($id));
        */
        
        $this->page->requires->js_call_amd('theme_klassplace/klassplacesearchmodal', 'init');
        
        $lastsearch = (!empty($SESSION->lastsearch)) ? $SESSION->lastsearch : '';
        $action = new moodle_url('/local/search/index.php');
        
        $template = new StdClass;
        $template->lastsearch = $lastsearch;
        $template->id = $id;
        $template->action = $action;
        return $OUTPUT->render_from_template('theme_klassplace/navsearch', $template);
        /*
        $searchicon = html_writer::tag('div', $this->pix_icon('a/search', get_string('search', 'search'), 'moodle'),
            array('role' => 'button', 'tabindex' => 0));
        $formattrs = array('class' => 'search-input-form', 'action' => $CFG->wwwroot . '/local/search/index.php');
        $inputattrs = array('type' => 'text', 'name' => 'q', 'placeholder' => get_string('search', 'search'),
            'size' => 13, 'tabindex' => -1, 'id' => 'id_q_' . $id, 'value' => $lastsearch);

        $contents = html_writer::tag('label', get_string('enteryoursearchquery', 'search'),
            array('for' => 'id_q_' . $id, 'class' => 'accesshide')) . html_writer::tag('input', '', $inputattrs);
        if ($this->page->context && $this->page->context->contextlevel !== CONTEXT_SYSTEM) {
            $contents .= html_writer::empty_tag('input', ['type' => 'hidden',
                    'name' => 'context', 'value' => $this->page->context->id]);
        }

        $clearmarks = optional_param('clearsearchmarks', 0, PARAM_BOOL);
        if ($clearmarks) {
            unset($SESSION->lastsearch);
            $me = new moodle_url(qualified_me());
            redirect($me);
        }

        if (!empty($SESSION->lastsearch)) {
            $clearurl = new moodle_url(qualified_me());
            $attrs = ['role' => 'button', 'tabindex' => 0, 'href' => $clearurl->out().'&clearsearchmarks=1'];
            $contents .= ' '.html_writer::tag('a', $this->pix_icon('i/hide', get_string('clearmarks', 'local_search'), 'moodle'), $attrs);
        }

        $searchinput = html_writer::tag('form', $contents, $formattrs);

        return html_writer::tag('div', $searchicon . $searchinput, array('class' => 'search-input-wrapper nav-link', 'id' => $id));
        */
    }

/*
    public function dashboard_main_content() {
        <div id="learningcontent">

        {{#headerlogo}}
            <div class="headerlogo">
                <img src="{{{ headerlogo }}}" class="img-fluid" alt="Responsive image">
            </div>
        {{/headerlogo}}
        {{>theme_fordson_fel/blockspanelslider}}
        {{{ output.fp_slideshow }}}
        {{{ output.full_header }}}

        <div id="page-content" class="row pb-3">
            <div id="region-main-box" class="col-12">
                {{#hasregionmainsettingsmenu}}
                <div id="region-main-settings-menu" class="d-print-none {{#hasblocks}}has-blocks{{/hasblocks}}">
                    <div> {{{ output.region_main_settings_menu }}} </div>
                </div>
                {{/hasregionmainsettingsmenu}}
                <section id="region-main" {{#hasblocks}}class="has-blocks mb-3"{{/hasblocks}}>

                            {{#hasregionmainsettingsmenu}}
                                <div class="region_main_settings_menu_proxy"></div>
                            {{/hasregionmainsettingsmenu}}
                            {{{ output.course_content_header }}}
                            {{{ enrolform }}}
                            {{{ output.fp_wonderbox }}}
                            {{{ output.main_content }}}
                            {{{ output.fp_marketingtiles }}}
                            {{{ output.course_content_footer }}}

                </section>
                {{#hasblocks}}
                    <section data-region="blocks-column" class="d-print-none">
                        {{{ sidepreblocks }}}
                    </section>
                {{/hasblocks}}
            </div>
        </div>
        
    }
*/
}

class core_renderer_maintenance extends \core_renderer_maintenance {

    public function footnote() {
        
    }
}