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
 * Course renderer.
 *
 * @package    theme_klassplace
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_klassplace\output\core;

use core_course_category;

defined('MOODLE_INTERNAL') || die();

use \stdClass;
use \moodle_url;
use \lang_string;
use \coursecat_helper;
use \context_course;
use \context_system;
use \pix_url;
use \html_writer;
use \heading;
use \pix_icon;
use \image_url;
use \single_select;
use \core_text;
use \url_select;

require_once($CFG->dirroot.'/course/renderer.php');
require_once($CFG->dirroot.'/theme/klassplace/lib/filesettings_lib.php');
require_once($CFG->dirroot.'/theme/klassplace/compatlib.php');

global $PAGE;
/**
 * Course renderer class.
 *
 * @package    theme_klassplace
 * @copyright  2021 Valery Fremaux (valery.fremaux@gmail.com, valery@activeprolearn.com), Nicloas Maligue
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

trait course_renderer_commons {

    /**
     * Renders HTML to display one course module in a course section
     *
     * This includes link, content, availability, completion info and additional information
     * that module type wants to display (i.e. number of unread forum posts)
     *
     * This function calls:
     * {@link core_course_renderer::course_section_cm_name()}
     * {@link core_course_renderer::course_section_cm_text()}
     * {@link core_course_renderer::course_section_cm_availability()}
     * {@link core_course_renderer::course_section_cm_completion()}
     * {@link course_get_cm_edit_actions()}
     * {@link core_course_renderer::course_section_cm_edit_actions()}
     *
     * @param stdClass $course
     * @param completion_info $completioninfo
     * @param cm_info $mod
     * @param int|null $sectionreturn
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm($course, &$completioninfo, \cm_info $mod, $sectionreturn, $displayoptions = array()) {
        global $PAGE, $DB;

        $config = get_config('theme_'.$PAGE->theme->name);

        $output = '';
        // We return empty string (because course module will not be displayed at all)
        // if:
        // 1) The activity is not visible to users
        // and
        // 2) The 'availableinfo' is empty, i.e. the activity was
        //     hidden in a way that leaves no info, such as using the
        //     eye icon.
        if (!$mod->is_visible_on_course_page()) {
            return $output;
        }

        $indentclasses = 'mod-indent';
        if (!empty($mod->indent)) {
            $indentclasses .= ' mod-indent-'.$mod->indent;
            if ($mod->indent > 15) {
                $indentclasses .= ' mod-indent-huge';
            }
        }

        $output .= html_writer::start_tag('div');

        if ($this->page->user_is_editing()) {
            $output .= course_get_cm_move($mod, $sectionreturn);
        }

        $output .= html_writer::start_tag('div', array('class' => 'mod-indent-outer'));

        // This div is used to indent the content.
        $output .= html_writer::div('', $indentclasses);

        // Start a wrapper for the actual content to keep the indentation consistent.
        $output .= html_writer::start_tag('div');

        // CHANGE+ : Add thumbed activities.
        $thumb = $this->course_section_cm_thumb($mod);

        // Freshness.
        $freshnesssignal = '';
        if ($course->format != 'page') {
            $freshnesssignal = $this->course_section_cm_freshness($mod);
        }

        $displayoptions['freshness'] = $freshnesssignal;
        if ($thumb) {
            $output .= html_writer::start_tag('div', array('class' => 'cm-name'));
            $output .= $thumb;
            $output .= html_writer::start_tag('div', array('class' => 'cm-label'));
            $cmname = $this->course_section_cm_name_for_thumb($mod, $displayoptions);
        } else {
            // Display the link to the module (or do nothing if module has no url)
            $cmname = $this->course_section_cm_name($mod, $displayoptions);
        }
        // CHANGE-.

        if (!empty($cmname)) {

            // Start the div for the activity title, excluding the edit icons.
            $output .= html_writer::start_tag('div', array('class' => 'activityinstance'));
            $output .= $cmname;


            // Module can put text after the link (e.g. forum unread)
            $output .= $mod->afterlink;

            // Closing the tag which contains everything but edit icons. Content part of the module should not be part of this.
            $output .= html_writer::end_tag('div'); // .activityinstance
        }

        // If there is content but NO link (eg label), then display the
        // content here (BEFORE any icons). In this case cons must be
        // displayed after the content so that it makes more sense visually
        // and for accessibility reasons, e.g. if you have a one-line label
        // it should work similarly (at least in terms of ordering) to an
        // activity.
        $contentpart = $this->course_section_cm_text($mod, $displayoptions);

        // CHANGE+ : manage thumb
        if (!empty($this->thumbfiles[$mod->id])) {
            // Remove the thumb that has already been displayed.
            $pattern = '/<img.*?'.$this->thumbfiles[$mod->id]->get_filename().'".*?>/';
            $contentpart = preg_replace($pattern, '', $contentpart);
        }
        // /CHANGE-.

        $url = $mod->url;
        if (empty($url)) {
            $output .= $contentpart;
        }

        $modicons = '';
        if ($this->page->user_is_editing()) {
            $editactions = course_get_cm_edit_actions($mod, $mod->indent, $sectionreturn);
            // CHANGE + : alter the show or hide action and make it primary.

            $sectionvisible = $mod->get_section_info()->visible;
            // The module on the course page may be in one of the following states:
            // - Available and displayed on the course page ($displayedoncoursepage);
            // - Not available and not displayed on the course page ($unavailable);
            // - Available but not displayed on the course page ($stealth) - this can also be a visible activity in a hidden section.
            $displayedoncoursepage = $mod->visible && $mod->visibleoncoursepage && $sectionvisible;
            $unavailable = !$mod->visible;
            $stealth = $mod->visible && (!$mod->visibleoncoursepage || !$sectionvisible);
            $modshowstr = get_string('modshow');
            $modhidestr = get_string('modhide');
            $baseurl = new moodle_url('/course/mod.php', array('sesskey' => sesskey()));
            if ($displayedoncoursepage) {
                $editactions['hide'] = new \action_menu_link_primary(
                    new \moodle_url($baseurl, array('hide' => $mod->id)),
                    new \pix_icon('t/hide', $modhidestr, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                    $modhidestr,
                    array('class' => 'editing_hide', 'data-action' => 'hide')
                );
            } else if (!$displayedoncoursepage && $sectionvisible) {
                // Offer to "show" only if the section is visible.
                $editactions['show'] = new \action_menu_link_primary(
                    new \moodle_url($baseurl, array('show' => $mod->id)),
                    new \pix_icon('t/show', $modshowstr, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                    $modshowstr,
                    array('class' => 'editing_show', 'data-action' => 'show')
                );
            }

            // Groupmode.
            $modcontext = \context_module::instance($mod->id);
            $hasmanageactivities = has_capability('moodle/course:manageactivities', $modcontext);
            if ($hasmanageactivities && !$mod->coursegroupmodeforce) {
                if (plugin_supports('mod', $mod->modname, FEATURE_GROUPS, 0)) {
                    if ($mod->effectivegroupmode == SEPARATEGROUPS) {
                        $nextgroupmode = VISIBLEGROUPS;
                        $grouptitle = get_string('groupsseparate');
                        $actionname = 'groupsseparate';
                        $nextactionname = 'groupsvisible';
                        $groupimage = 'i/groups';
                    } else if ($mod->effectivegroupmode == VISIBLEGROUPS) {
                        $nextgroupmode = NOGROUPS;
                        $grouptitle = get_string('groupsvisible');
                        $actionname = 'groupsvisible';
                        $nextactionname = 'groupsnone';
                        $groupimage = 'i/groupv';
                    } else {
                        $nextgroupmode = SEPARATEGROUPS;
                        $grouptitle = get_string('groupsnone');
                        $actionname = 'groupsnone';
                        $nextactionname = 'groupsseparate';
                        $groupimage = 'i/groupn';
                    }

                    $editactions[$actionname] = new \action_menu_link_secondary(
                        new \moodle_url($baseurl, array('id' => $mod->id, 'groupmode' => $nextgroupmode)),
                        new \pix_icon($groupimage, $grouptitle, 'moodle', array('class' => 'iconsmall')),
                        $grouptitle,
                        array('class' => 'editing_'. $actionname, 'data-action' => $nextactionname,
                            'aria-live' => 'assertive', 'data-sectionreturn' => $sectionreturn)
                    );
                } else {
                    $actions['nogroupsupport'] = new \action_menu_filler();
                }
            }

            // CHANGE-.
            $modicons .= ' '. $this->course_section_cm_edit_actions($editactions, $mod, $displayoptions);
            $modicons .= $mod->afterediticons;
        }

        $modicons .= $this->course_section_cm_completion($course, $completioninfo, $mod, $displayoptions);

        if (!empty($modicons)) {
            $output .= html_writer::span($modicons, 'actions');
        }

        // Show availability info (if module is not available).
        $output .= $this->course_section_cm_availability($mod, $displayoptions);

        // If there is content AND a link, then display the content here
        // (AFTER any icons). Otherwise it was displayed before
        if (!empty($url)) {
            $output .= $contentpart;
        }

        $output .= html_writer::end_tag('div'); // $indentclasses

        // End of indentation div.
        $output .= html_writer::end_tag('div');

        $output .= html_writer::end_tag('div');
        return $output;
    }

    // Additions for thumbed actitivies layout.
    // CHANGE
    public function course_section_cm_name_for_thumb($mod, $displayoptions) {

        // Accessibility: for files get description via icon, this is very ugly hack!

        $instancename = $mod->get_formatted_name();
        $altname = $mod->modfullname;
        /*
         * Avoid unnecessary duplication: if e.g. a forum name already
         * includes the word forum (or Forum, etc) then it is unhelpful
         * to include that in the accessible description that is added.
         */
        if (false !== strpos(core_text::strtolower($instancename),
                core_text::strtolower($altname))) {
            $altname = '';
        }
        // File type after name, for alphabetic lists (screen reader).
        if ($altname) {
            $altname = get_accesshide(' '.$altname);
        }

        $linkclasses = '';
        $accesstext = '';
        $textclasses = '';
        if ($mod->uservisible) {
            $conditionalhidden = $this->is_cm_conditionally_hidden($mod);
            $accessiblebutdim = (!$mod->visible || $conditionalhidden) &&
                has_capability('moodle/course:viewhiddenactivities', $mod->context);
            if ($accessiblebutdim) {
                $linkclasses .= ' dimmed';
                $textclasses .= ' dimmed_text';
                if ($conditionalhidden) {
                    $linkclasses .= ' conditionalhidden';
                    $textclasses .= ' conditionalhidden';
                }
                // Show accessibility note only if user can access the module himself.
                $accesstext = get_accesshide(get_string('hiddenfromstudents').':'. $mod->modfullname);
            }
        } else {
            $linkclasses .= ' dimmed';
            $textclasses .= ' dimmed_text';
        }

        // Process a possible #hide flag :
        if (preg_match('/^\#hide\s+/', $instancename)) {
            $textclasses .= ' fullhide';
            $linkclasses .= ' fullhide';
            $instancename = preg_replace('/\#hide\s+/', '', $instancename);
        }

        // Get on-click attribute value if specified and decode the onclick - it
        // has already been encoded for display (puke).
        $onclick = htmlspecialchars_decode($mod->onclick, ENT_QUOTES);

        $groupinglabel = $mod->get_grouping_label($textclasses);

        // Display link itself.
        $cmname = '';
        $config = get_config('theme_'.$PAGE->theme->name);

        if (!preg_match('/label$/', $instancename) || !empty($config->allownewsignalonlabels)) {
            // Avoid labels to display cmname.
            $cmname = $accesstext.html_writer::tag('span', $instancename . $altname, array('class' => 'instancename'));
            if (!empty($displayoptions['freshness'])) {
                $cmname .= ' '.$displayoptions['freshness'];
            }
        }
        if ($mod->uservisible) {
            $cmoutput = html_writer::link($mod->url, $cmname, array('class' => $linkclasses, 'onclick' => $onclick)).$groupinglabel;
        } else {
            // We may be displaying this just in order to show information
            // about visibility, without the actual link ($mod->uservisible)
            $cmoutput = html_writer::tag('div', $cmname, array('class' => $textclasses)).$groupinglabel;
        }
        return $cmoutput;
    }

    public function course_section_cm_freshness($mod) {
        global $DB, $PAGE;

        $config = get_config('theme_'.$PAGE->theme->name);
        if (empty($config->signalitemsnewerthan)) {
            return '';
        }

        // TODO check completion to avoid signal bothering student.

        $timemodified = $DB->get_field($mod->modname, 'timemodified', array('id' => $mod->instance));
        $now = time();
        $freshnesssignal = '';
        if ($now < ($mod->added + $config->signalitemsnewerthan * HOURSECS)) {
            $freshnesssignal = html_writer::tag('span', get_string('newitem', 'theme_klassplace'), array('class' => 'new-item'));
        } else if ($now < ($timemodified + $config->signalitemsnewerthan * HOURSECS)) {
            $freshnesssignal = html_writer::tag('span', get_string('updateditem', 'theme_klassplace'), array('class' => 'updated-item'));
        }

        return $freshnesssignal;
    }

    /**
     * Main change integrates freshness signal in module name.
     *
     * If module is unavailable for user but still needs to be displayed
     * in the list, just the name is returned without a link
     *
     * Note, that for course modules that never have separate pages (i.e. labels)
     * this function return an empty string
     *
     * @param cm_info $mod
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm_name_title(\cm_info $mod, $displayoptions = array()) {
        $output = '';
        $url = $mod->url;
        if (!$mod->is_visible_on_course_page() || !$url) {
            // Nothing to be displayed to the user.
            return $output;
        }

        //Accessibility: for files get description via icon, this is very ugly hack!
        $instancename = $mod->get_formatted_name();
        $altname = $mod->modfullname;
        // Avoid unnecessary duplication: if e.g. a forum name already
        // includes the word forum (or Forum, etc) then it is unhelpful
        // to include that in the accessible description that is added.
        if (false !== strpos(core_text::strtolower($instancename),
                core_text::strtolower($altname))) {
            $altname = '';
        }
        // File type after name, for alphabetic lists (screen reader).
        if ($altname) {
            $altname = get_accesshide(' '.$altname);
        }

        list($linkclasses, $textclasses) = $this->course_section_cm_classes($mod);

        // Get on-click attribute value if specified and decode the onclick - it
        // has already been encoded for display (puke).
        $onclick = htmlspecialchars_decode($mod->onclick, ENT_QUOTES);

        $freshnesssignal = $displayoptions['freshness'];

        // Display link itself.
        $activitylink = html_writer::empty_tag('img', array('src' => $mod->get_icon_url(),
                'class' => 'iconlarge activityicon', 'alt' => ' ', 'role' => 'presentation')) .
                html_writer::tag('span', $instancename . $altname, array('class' => 'instancename'));
        if ($mod->uservisible) {
            $output .= html_writer::link($url, $activitylink.$freshnesssignal, array('class' => $linkclasses, 'onclick' => $onclick));
        } else {
            // We may be displaying this just in order to show information
            // about visibility, without the actual link ($mod->is_visible_on_course_page()).
            $output .= html_writer::tag('div', $activitylink.$freshnesssignal, array('class' => $textclasses));
        }
        return $output;
    }

    public function course_section_cm_thumb($mod) {
        global $DB, $COURSE, $PAGE;

        $cmoutput = '';

        // Check existance of module thumb image in the description.
        $fs = get_file_storage();

        $files = $fs->get_area_files($mod->context->id, 'mod_'.$mod->modname, 'modthumb', $mod->id, 'filepath,filename', true);
        if (!empty($files)) {
            $file = array_pop($files);
        }

        if (!empty($file)) {

            $this->thumbfiles[$mod->id] = $file;
            $url = $mod->url;

            if ($COURSE->format == 'page') {
                /*
                 * Divert the resource call to the special page format file wrapper.
                 * Do not change any other thing. The special file wrapper will know how
                 * to get the origin component identity back.
                 */
                $imgurl = moodle_url::make_pluginfile_url($file->get_contextid(), 'format_page',
                                                          $file->get_filearea(), $file->get_itemid(),
                                                          '/', $file->get_filename());
            } else {
                $imgurl = moodle_url::make_pluginfile_url($file->get_contextid(), 'theme_'.$PAGE->theme->name,
                                                          $file->get_filearea(), $file->get_itemid(),
                                                          '/', $file->get_filename());
            }

            // For items which are hidden but available to current user
            // ($mod->uservisible), we show those as dimmed only if the user has
            // viewhiddenactivities, so that teachers see 'items which might not
            // be available to some students' dimmed but students do not see 'item
            // which is actually available to current student' dimmed.
            $linkclasses = '';
            $accesstext = '';
            $textclasses = '';
            if ($mod->uservisible) {
                $conditionalhidden = $this->is_cm_conditionally_hidden($mod);
                $accessiblebutdim = (!$mod->visible || $conditionalhidden) &&
                    has_capability('moodle/course:viewhiddenactivities', $mod->context);
                if ($accessiblebutdim) {
                    $linkclasses .= ' dimmed';
                    $textclasses .= ' dimmed_text';
                    if ($conditionalhidden) {
                        $linkclasses .= ' conditionalhidden';
                        $textclasses .= ' conditionalhidden';
                    }
                    // Show accessibility note only if user can access the module himself.
                    $accesstext = get_accesshide(get_string('hiddenfromstudents').':'. $mod->modfullname);
                }
            } else {
                $linkclasses .= ' dimmed';
                $textclasses .= ' dimmed_text';
            }

            // Get on-click attribute value if specified and decode the onclick - it
            // has already been encoded for display (puke).
            $onclick = htmlspecialchars_decode($mod->onclick, ENT_QUOTES);

            $img = '<img src="'.$imgurl.'">';
            // $modurl = new moodle_url('/mod/'.$mod->modname.'/view.php', array('id' => $mod->id));
            $cmoutput = '<div class="cm-picture"><a href="'.$mod->url.'">'.$img.'</a></div>';

            $imglink = html_writer::link($mod->url, $img, array('class' => $linkclasses, 'onclick' => $onclick));

            $cmoutput = html_writer::tag('div', $imglink, array('class' => 'cm-picture'));
        }

        return $cmoutput;
    }

    public function get_thumbfiles() {
        return $this->thumbfiles;
    }

    /**
     * Renders the list of subcategories in a category
     *
     * @param coursecat_helper $chelper various display options
     * @param coursecat $coursecat
     * @param int $depth depth of the category in the current tree
     * @return string
     */
    protected function coursecat_subcategories(coursecat_helper $chelper, $coursecat, $depth) {

        debug_trace("Klassplace course_renderer : coursecat_subcategories");
        if (!theme_klassplace_get_setting('enablecategoryicon')) {
            return parent::coursecat_subcategories($chelper, $coursecat, $depth);
        }
        global $CFG;
        $subcategories = array();
        if (!$chelper->get_categories_display_option('nodisplay')) {
            $subcategories = $coursecat->get_children($chelper->get_categories_display_options());
        }
        $totalcount = $coursecat->get_children_count();
        if (!$totalcount) {
            // Note that we call coursecat::get_children_count() AFTER coursecat::get_children() to avoid extra DB requests.
            // Categories count is cached during children categories retrieval.
            return '';
        }
        // prepare content of paging bar or more link if it is needed
        $paginationurl = $chelper->get_categories_display_option('paginationurl');
        $paginationallowall = $chelper->get_categories_display_option('paginationallowall');
        if ($totalcount > count($subcategories)) {
            if ($paginationurl) {
                // the option 'paginationurl was specified, display pagingbar
                $perpage = $chelper->get_categories_display_option('limit', $CFG->coursesperpage);
                $page = $chelper->get_categories_display_option('offset') / $perpage;
                $pagingbar = $this->paging_bar($totalcount, $page, $perpage, $paginationurl->out(false, array(
                    'perpage' => $perpage
                )));
                if ($paginationallowall) {
                    $pagingbar .= html_writer::tag('div', html_writer::link($paginationurl->out(false, array(
                        'perpage' => 'all'
                    )) , get_string('showall', '', $totalcount)) , array(
                        'class' => 'paging paging-showall'
                    ));
                }
            }
            else if ($viewmoreurl = $chelper->get_categories_display_option('viewmoreurl')) {
                // the option 'viewmoreurl' was specified, display more link (if it is link to category view page, add category id)
                if ($viewmoreurl->compare(new moodle_url('/course/index.php') , URL_MATCH_BASE)) {
                    $viewmoreurl->param('categoryid', $coursecat->id);
                }
                $viewmoretext = $chelper->get_categories_display_option('viewmoretext', new lang_string('viewmore'));
                $morelink = html_writer::tag('div', html_writer::link($viewmoreurl, $viewmoretext) , array(
                    'class' => 'paging paging-morelink'
                ));
            }
        }
        else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
            // there are more than one page of results and we are in 'view all' mode, suggest to go back to paginated view mode
            $pagingbar = html_writer::tag('div', html_writer::link($paginationurl->out(false, array(
                'perpage' => $CFG->coursesperpage
            )) , get_string('showperpage', '', $CFG->coursesperpage)) , array(
                'class' => 'paging paging-showperpage'
            ));
        }
        // display list of subcategories
        $content = html_writer::start_tag('div', array(
            'class' => 'subcategories d-flex flex-wrap'
        ));
        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }
        foreach ($subcategories as $subcategory) {
            debug_trace("Klassplace course_renderer : calling coursecat_category ");
            $content .= $this->coursecat_category($chelper, $subcategory, $depth + 1);
        }
        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }
        if (!empty($morelink)) {
            $content .= $morelink;
        }
        $content .= html_writer::end_tag('div');
        return $content;
    }

    protected function coursecat_courses(coursecat_helper $chelper, $courses, $totalcount = null) {
        global $CFG;

        if ($totalcount === null) {
            $totalcount = count($courses);
        }
        if (!$totalcount) {
            // Courses count is cached during courses retrieval.
            return '';
        }

        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_AUTO) {

            if ($totalcount <= $CFG->courseswithsummarieslimit) {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
            } else {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_COLLAPSED);
            }
        }

        $paginationurl = $chelper->get_courses_display_option('paginationurl');
        $paginationallowall = $chelper->get_courses_display_option('paginationallowall');
        if ($totalcount > count($courses)) {

            if ($paginationurl) {
                $perpage = $chelper->get_courses_display_option('limit', $CFG->coursesperpage);
                $page = $chelper->get_courses_display_option('offset') / $perpage;
                $pagingbar = $this->paging_bar($totalcount, $page, $perpage, $paginationurl->out(false, array(
                    'perpage' => $perpage
                )));
                if ($paginationallowall) {
                    $pagingbar .= html_writer::tag('div', html_writer::link($paginationurl->out(false, array(
                        'perpage' => 'all'
                    )) , get_string('showall', '', $totalcount)) , array(
                        'class' => 'paging paging-showall'
                    ));
                }
            } else if ($viewmoreurl = $chelper->get_courses_display_option('viewmoreurl')) {
                $viewmoretext = $chelper->get_courses_display_option('viewmoretext', new lang_string('viewmore'));
                $morelink = html_writer::tag('div', html_writer::tag('a', html_writer::start_tag('i', array(
                    'class' => 'fa-graduation-cap' . ' fa fa-fw'
                )) . html_writer::end_tag('i') . $viewmoretext, array(
                    'href' => $viewmoreurl,
                    'class' => 'btn btn-primary coursesmorelink'
                )) , array(
                    'class' => 'paging paging-morelink'
                ));
            }
        } else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
            $pagingbar = html_writer::tag('div', html_writer::link($paginationurl->out(false, array(
                'perpage' => $CFG->coursesperpage
            )) , get_string('showperpage', '', $CFG->coursesperpage)) , array(
                'class' => 'paging paging-showperpage'
            ));
        }

        $attributes = $chelper->get_and_erase_attributes('courses');
        $content = html_writer::start_tag('div', $attributes);

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }
        $categoryid = optional_param('categoryid', 0, PARAM_INT);
        $coursecount = 0;

        $content .= $this->view_available_courses($chelper, $categoryid, $courses, $totalcount);

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }
        if (!empty($morelink)) {
            $content .= $morelink;
        }
        $content .= html_writer::end_tag('div');
        $content .= '<div class="clearfix"></div>';

        return $content;
    }

    /**
     * Delegates to local_my some rendering of  course lists to get things more uniform.
     */
    public function view_available_courses($chelper, $id, $courses = null, $totalcount = null) {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE;

		if (is_dir($CFG->dirroot.'/local/my')) {
	        include_once($CFG->dirroot.'/local/my/classes/modules/module.class.php');
	        include_once($CFG->dirroot.'/local/my/classes/modules/courses_grid.class.php');
	        $config = get_config('local_my');
	        $PAGE->requires->js_call_amd('local_my/local_my', 'init');
	    }

        $template = new StdClass;
        $options = array();
        $options['withcats'] = $config->printcategories;
        $options['gaugewidth'] = 20;
        $options['gaugeheight'] = 20;
        $options['gaugetype'] = $config->progressgaugetype;

        // Reuse a module with an external course list feeding.
        $m = new \local_my\module\courses_grid_module();
        $m->set_courses($courses);
        $m->set_options($options);
        return $m->render('asgrid');
    }

    /**
     * Returns HTML to display the subcategories and courses in the given category
     *
     * This method is re-used by AJAX to expand content of not loaded category
     *
     * @param coursecat_helper $chelper various display options
     * @param coursecat $coursecat
     * @param int $depth depth of the category in the current tree
     * @return string
     */
    protected function coursecat_category(coursecat_helper $chelper, $coursecat, $depth) {
        global $PAGE;

        if (empty($PAGE->theme->settings->enablecategoryicon)) {
            return parent::coursecat_category($chelper, $coursecat, $depth);
        }

        global $CFG, $OUTPUT;

        $classes = array(
            'category'
        );
        if (empty($coursecat->visible)) {
            $classes[] = 'dimmed_category';
        }
        if ($chelper->get_subcat_depth() > 0 && $depth >= $chelper->get_subcat_depth()) {
            $categorycontent = '';
            $classes[] = 'notloaded';
            if ($coursecat->get_children_count() || ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_COLLAPSED && $coursecat->get_courses_count())) {
                $classes[] = 'with_children';
                $classes[] = 'collapsed';
            }
        } else {
            $categorycontent = $this->coursecat_category_content($chelper, $coursecat, $depth);
            $classes[] = 'loaded';
            if (!empty($categorycontent)) {
                $classes[] = 'with_children';
            }
        }

        $totalcount = theme_klassplace_get_category(0)->get_children_count();

        $content = '';
        if ($this->countcategories == 0 || ($this->countcategories % 3) == 0) {
            if (($this->countcategories % 3) == 0 && $totalcount != $this->countcategories) {
                $content .= '</div> </div>';
            }
            if ($totalcount != $this->countcategories || $this->countcategories == 0) {
                $categoryparam = optional_param('categoryid', 0, PARAM_INT);
                if ($categoryparam) {
                    $content .= $OUTPUT->heading(get_string('categories'));
                }
                $content .= '<div class="container-fluid"><div class="row">';
            }
        }

        $classes[] = 'col-md-3 box-class';
        $content = '<div class="' . join(' ', $classes) . '" data-categoryid="' . $coursecat->id . '" data-depth="' . $depth . '" data-showcourses="' . $chelper->get_show_courses() . '" data-type="' . self::COURSECAT_TYPE_CATEGORY . '">';
        $content .= '<div class="cat-icon">';
        $val = $PAGE->theme->settings->catsicon;
        $url = new moodle_url('/course/index.php', array(
            'categoryid' => $coursecat->id
        ));
        $content .= '<a href="' . $url . '">';
        $content .= '<i class="fa fa-5x fa-' . $val . '"></i>';
        $categoryname = $coursecat->get_formatted_name();
        $content .= '<div>';
        $content .= '<div class="info-enhanced">';
        $content .= '<span class="class-category">' . $categoryname . '</span>';
        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_COUNT) {
            $coursescount = $coursecat->get_courses_count();
            $content .= '  <span class="numberofcourses" title="' . get_string('numberofcourses') . '">(' . $coursescount . ')</span>';
        }
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</a>';
        $content .= '</div>';
        $content .= '</div>';
        if ($totalcount == $this->countcategories) {
        }
        ++$this->countcategories;
        // Category name.

        return $content;

    }
}

if (!empty($PAGE->theme->settings->coursetilestyle) && $PAGE->theme->settings->coursetilestyle < 10) {
    class course_renderer
    extends \core_course_renderer {
        use course_renderer_commons;

        protected $thumbfiles = [];

        protected $countcategories = 0;

        public function frontpage_available_courses($id = 0) {
            /* available courses */
            global $CFG, $OUTPUT, $PAGE;

            $chelper = new coursecat_helper();
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->set_courses_display_options(array(
                'recursive' => true,
                'limit' => $CFG->frontpagecourselimit,
                'viewmoreurl' => new moodle_url('/course/index.php') ,
                'viewmoretext' => new lang_string('fulllistofcourses')
            ));

            $chelper->set_attributes(array(
                'class' => 'frontpage-course-list-all'
            ));
            $courses = theme_klassplace_get_category($id)->get_courses($chelper->get_courses_display_options());
            $totalcount = theme_klassplace_get_category($id)->get_courses_count($chelper->get_courses_display_options());

            $rcourseids = array_keys($courses);
            $acourseids = array_chunk($rcourseids, 3);
            $newcourse = get_string('availablecourses');

            $header = '
                <div id="category-course-list">
                    <div class="courses category-course-list-all">
                    <hr>
                    <div class="class-list">
                        <h4>' . $newcourse . '</h4>
                    </div>';

            $content = '';

            $footer = '
                    </div>
                </div>';

            if (count($rcourseids) > 0) {
                foreach ($acourseids as $courseids) {
                    $content .= '<div class="container-fluid"> <div class="row">';
                    $rowcontent = '';

                    foreach ($courseids as $courseid) {
                        $course = get_course($courseid);

                        $trimtitlevalue = $PAGE->theme->settings->trimtitle;
                        $trimsummaryvalue = $PAGE->theme->settings->trimsummary;

                        $trimtitle = theme_klassplace_course_trim_char($course->fullname, $trimtitlevalue);

                        $summary = theme_klassplace_strip_html_tags($course->summary);
                        $summary = theme_klassplace_course_trim_char($summary, $trimsummaryvalue);

                        $noimgurl = $OUTPUT->image_url('noimg', 'theme');
                        $courseurl = new moodle_url('/course/view.php', array(
                            'id' => $courseid
                        ));

                        if ($course instanceof stdClass) {
                            $course = theme_klassplace_course_in_list($course);
                        }

                        // Load from config if usea a img from course summary file if not exist a img then a default one ore use a fa-icon.
                        $imgurl = '';
                        $context = context_course::instance($course->id);

                        foreach ($course->get_course_overviewfiles() as $file) {
                            $isimage = $file->is_valid_image();
                            $imgurl = file_encode_url("$CFG->wwwroot/pluginfile.php", '/' . $file->get_contextid() . '/' . $file->get_component() . '/' . $file->get_filearea() . $file->get_filepath() . $file->get_filename() , !$isimage);
                            if (!$isimage) {
                                $imgurl = $noimgurl;
                            }
                        }
                        if (empty($imgurl)) {
                            $imgurl = $PAGE->theme->setting_file_url('headerdefaultimage', 'headerdefaultimage', true);
                            if (!$imgurl) {
                                $imgurl = $noimgurl;
                            }
                        }
                        if ($PAGE->theme->settings->coursetilestyle == 1) {
                            $rowcontent .= '
                        <div class="col-md-4">';
                            $rowcontent .= html_writer::start_tag('div', array(
                                'class' => $course->visible ? '' : 'coursedimmed1'
                            ));
                            $rowcontent .= '
                            <div class="class-box">
                                ';

                            if ($PAGE->theme->settings->titletooltip) {
                                $tooltiptext = 'data-placement= "top" title="' . $course->fullname . '"';
                            }
                            else {
                                $tooltiptext = '';
                            }

                            $rowcontent .= '
                                    <a ' . $tooltiptext . ' href="' . $courseurl . '">
                                    <div class="courseimagecontainer">
                                    <div class="course-image-view" style="background-image: url(' . $imgurl . ');background-repeat: no-repeat;background-size:cover; background-position:center;">
                                    </div>
                                    <div class="course-overlay">
                                    <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                                    </div>
                                    
                                    </div>
                                    <div class="course-title">
                                    <h4>' . $trimtitle . '</h4>
                                    </div>
                                    </a>
                                    <div class="course-summary">
                                    ';
                            if ($course->has_course_contacts()) {

                                $rowcontent .= html_writer::start_tag('ul', array(
                                    'class' => 'teacherscourseview'
                                ));
                                foreach ($course->get_course_contacts() as $userid => $coursecontact) {

                                    $name = $coursecontact['rolename'] . ': ' . $coursecontact['username'];
                                    $rowcontent .= html_writer::tag('li', $name);
                                }
                                $rowcontent .= html_writer::end_tag('ul'); // .teachers
                                
                            }
                            $rowcontent .= '
                                    </div>
                                </div>
                        </div>
                        </div>';
                        }
                        if ($PAGE->theme->settings->coursetilestyle == 2) {
                            // Display course contacts. See course_in_list::get_course_contacts().
                            $enrollbutton = get_string('enrollcoursecard', 'theme_klassplace');
                            $rowcontent .= '
                    <div class="col-md-4">
                        ';
                            $rowcontent .= '
                    <div class="tilecontainer">
                            <figure class="coursestyle2">
                                <div class="class-box-courseview" style="background-image: url(' . $imgurl . ');background-repeat: no-repeat;background-size:cover; background-position:center;">
                                ';
                            if ($PAGE->theme->settings->titletooltip) {
                                $tooltiptext = 'data-placement= "top" title="' . $course->fullname . '"';
                            }
                            else {
                                $tooltiptext = '';
                            }
                            $rowcontent .= html_writer::start_tag('div', array(
                                'class' => $course->visible ? '' : 'coursedimmed2'
                            ));
                            $rowcontent .= '
                                <figcaption>
                                    <h3>' . $trimtitle . '</h3>
                                    <div class="course-card">
                                    <button type="button" class="btn btn-primary btn-sm coursestyle2btn">' . $enrollbutton . '   <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button>
                                    ';
                            if ($course->has_course_contacts()) {

                                $rowcontent .= html_writer::start_tag('ul', array(
                                    'class' => 'teacherscourseview'
                                ));
                                foreach ($course->get_course_contacts() as $userid => $coursecontact) {

                                    $name = $coursecontact['rolename'] . ': ' . $coursecontact['username'];
                                    $rowcontent .= html_writer::tag('li', $name);
                                }
                                $rowcontent .= html_writer::end_tag('ul'); 
                                
                            }
                            $rowcontent .= '
                                </div>
                                </div>

                                </figcaption>
                                    <a ' . $tooltiptext . ' href="' . $courseurl . '" class="coursestyle2url"></a>
                                </div>
                            </figure>
                    </div>
                    </div>
                        ';
                        }
                        if ($PAGE->theme->settings->coursetilestyle == 3) {
                            if ($PAGE->theme->settings->titletooltip) {
                                $tooltiptext = 'data-placement= "top" title="' . $course->fullname . '"';
                            }
                            else {
                                $tooltiptext = '';
                            }
                            $rowcontent .= '
                        <div class="col-md-4">
                        <div class="tilecontainer">
                            <div class="class-box-fp" style="background-image: url(' . $imgurl . ');background-repeat: no-repeat;background-size:cover; background-position:center;">
                                <a ' . $tooltiptext . ' href="' . $courseurl . '" class="coursestyle3url">';
                            $rowcontent .= html_writer::start_tag('div', array(
                                'class' => $course->visible ? '' : 'coursedimmed3'
                            ));
                            $rowcontent .= '
                                    <div class="course-title">
                                    <h4><a href="' . $courseurl . '">' . $trimtitle . '</a></h4>
                                    </div>
                                    </div>
                                    </a>
                                </div>
                               </div> 
                        </div>';
                        }
                        if ($PAGE->theme->settings->coursetilestyle == 4) {
                            $rowcontent .= '
                        <div class="col-md-4">';
                            $rowcontent .= html_writer::start_tag('div', array(
                                'class' => $course->visible ? '' : 'coursedimmed1'
                            ));
                            $rowcontent .= '
                            <div class="class-box">
                                ';

                            if ($PAGE->theme->settings->titletooltip) {
                                $tooltiptext = 'data-placement= "top" title="' . $course->fullname . '"';
                            } else {
                                $tooltiptext = '';
                            }

                            $rowcontent .= '
                                    <a ' . $tooltiptext . ' href="' . $courseurl . '">
                                        <div class="courseimagecontainer">
                                            <div class="course-image-view" style="background-image: url(' . $imgurl . ');background-repeat: no-repeat;background-size:cover; background-position:center;">
                                            </div>
                                            <div class="course-overlay">
                                            <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                        <div class="course-title">
                                            <h4>' . $trimtitle . '</h4>
                                        </div>
                                    </a>
                                    <div class="course-summary">
                                    ' . $summary . '
                                    ';
                            if ($course->has_course_contacts()) {

                                $rowcontent .= html_writer::start_tag('ul', array(
                                    'class' => 'teacherscourseview'
                                ));
                                foreach ($course->get_course_contacts() as $userid => $coursecontact) {

                                    $name = $coursecontact['rolename'] . ': ' . $coursecontact['username'];
                                    $rowcontent .= html_writer::tag('li', $name);
                                }
                                $rowcontent .= html_writer::end_tag('ul'); // .teachers
                                
                            }
                            $rowcontent .= '
                                    </div>
                                </div>
                        </div>
                        </div>';
                        }

                    if ($PAGE->theme->settings->coursetilestyle == 5) {
                            $rowcontent .= html_writer::start_tag('div', array(
                                'class' => $course->visible ? 'col-12 d-flex flex-sm-row flex-column class-fullbox coursevisible' : 'col-12 d-flex flex-sm-row flex-column class-fullbox coursedimmed1'
                            ));
                            
                            if ($PAGE->theme->settings->titletooltip) {
                                $tooltiptext = 'data-placement= "top" title="' . $course->fullname . '"';
                            } else {
                                $tooltiptext = '';
                            }

                            $rowcontent .= '
                            <div class="col-md-2">
                                <a ' . $tooltiptext . ' href="' . $courseurl . '">
                                   <img src="' . $imgurl . '" class="img-fluid" alt="Responsive image" width="200px">
                                </a>
                            </div>';
                            $rowcontent .='
                            <div class="col-md-4">';
                            $rowcontent .='
                                <a ' . $tooltiptext . ' href="' . $courseurl . '">
                                    <div class="course-title-fullbox">
                                        <h4>' . $trimtitle . '</h4>
                                </a>
                                </div>';
                            if ($course->has_course_contacts()) {
                                $rowcontent .= html_writer::start_tag('ul', array(
                                    'class' => 'teacherscourseview'
                                ));
                                foreach ($course->get_course_contacts() as $userid => $coursecontact) {

                                    $name = $coursecontact['rolename'] . ': ' . $coursecontact['username'];
                                    $rowcontent .= html_writer::tag('li', $name);
                                }
                                $rowcontent .= html_writer::end_tag('ul');
                            }
                            
                            $rowcontent .= '</div>';
                            $rowcontent .= '<div class="col-md-6">
                                    <div class="course-summary">
                                    ' . $summary . '
                                    </div> 
                                    </div> ';

                            $rowcontent .= html_writer::end_tag('div');
                        }

                        if ($PAGE->theme->settings->coursetilestyle == 6) {
                            if ($PAGE->theme->settings->titletooltip) {
                                $tooltiptext = 'data-placement= "top" title="' . $course->fullname . '"';
                            } else {
                                $tooltiptext = '';
                            }
                            $rowcontent .= '
                        <div class="col-md-12">
                            <div class="class-fullbox" style="background-image: url(' . $imgurl . ');background-repeat: no-repeat;background-size:cover; background-position:center;">
                                <div class="fullbox">
                                ';
                            $rowcontent .= html_writer::start_tag('div', array(
                                'class' => $course->visible ? 'coursevisible' : 'coursedimmed3'
                            ));
                            $rowcontent .= '
                            
                                <div class="course-info-inner">

                                    <div class="course-title-fullboxbkg">
                                    <h4><a href="' . $courseurl . '">' . $trimtitle . '</a></h4>
                                    </div>
                                    
                                    </div>
                                    
                                ';
                            $rowcontent .= '<div class="d-flex flex-sm-row flex-column coursedata">';
                            if ($course->has_course_contacts()) {
                                $rowcontent .= '<div class="col-md-6">';
                                $rowcontent .= html_writer::start_tag('ul', array(
                                    'class' => 'teacherscourseview'
                                ));
                                foreach ($course->get_course_contacts() as $userid => $coursecontact) {

                                    $name = $coursecontact['rolename'] . ': ' . $coursecontact['username'];
                                    $rowcontent .= html_writer::tag('li', $name);
                                }
                                $rowcontent .= html_writer::end_tag('ul');
                                $rowcontent .= '</div>';
                            }

                        $rowcontent .= '<div class="col-md-6">
                                    <div class="course-summary">
                                    ' . $summary . '
                                    </div> 
                                    </div> </div></div>';
                        $rowcontent .='
                                        </div>
                                    
                                </div>
                        </div>';
                        } 
                    if ($PAGE->theme->settings->coursetilestyle == 7) {
                            if ($PAGE->theme->settings->titletooltip) {
                                $tooltiptext = 'data-placement= "top" title="' . $course->fullname . '"';
                            } else {
                                $tooltiptext = '';
                            }
                            $rowcontent .= '
                        <div class="col-md-12">
                            <div class="class-fullbox7" style="background-image: url(' . $imgurl . ');background-repeat: no-repeat;background-size:cover; background-position:center; background-color: rgba(0,0,0,0.3);
    background-blend-mode: overlay;">
                                <div class="fullbox7">
                                ';
                            
                            $rowcontent .= '<div class="course-info-inner">';
                            $rowcontent .= html_writer::start_tag('div', array(
                                'class' => $course->visible ? 'coursevisible course-title-fullboxbkg7 d-flex flex-sm-row flex-column' : 'course-title-fullboxbkg coursedimmed3 d-flex flex-sm-row flex-column'
                            ));
                            $rowcontent .= '<div class="col-md-6">
                                    <h4><a href="' . $courseurl . '">' . $trimtitle . '</a></h4>
                                    </div>';
                                    if ($course->has_course_contacts()) {
                                $rowcontent .= '<div class="col-md-6">';
                                $rowcontent .= html_writer::start_tag('ul', array(
                                    'class' => 'teacherscourseview'
                                ));
                                foreach ($course->get_course_contacts() as $userid => $coursecontact) {

                                    $name = $coursecontact['rolename'] . ': ' . $coursecontact['username'];
                                    $rowcontent .= html_writer::tag('li', $name);
                                }
                                $rowcontent .= html_writer::end_tag('ul');
                                $rowcontent .= '</div>';
                            }
                            $rowcontent .= '</div>
                                     </div>
                                    
                                    </div>
                                </div>
                        </div>';
                        }

                    }

                    $content .= $rowcontent;
                    $content .= '</div> </div>';
                }
            }

            $coursehtml = $header . $content . $footer;
            if ($id == 0) {
                echo $coursehtml;

            }
            else {
                $coursehtml .= '<br/><br/>';
                return $coursehtml;
            }

        }

        protected static function timeaccesscompare($a, $b) {
            // Timeaccess is lastaccess entry and timestart an enrol entry.
            if ((!empty($a->timeaccess)) && (!empty($b->timeaccess))) {
                // Both last access.
                if ($a->timeaccess == $b->timeaccess) {
                    return 0;
                }
                return ($a->timeaccess > $b->timeaccess) ? -1 : 1;
            } else if ((!empty($a->timestart)) && (!empty($b->timestart))) {
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

        public function frontpage_my_courses() {
            global $USER, $CFG, $DB;

            if (!isloggedin() or isguestuser()) {
                return '';
            }

            $nomycourses = '<div class="alert alert-info alert-block">' . get_string('nomycourses', 'theme_klassplace') . '</div>';
            $lastaccess = '';
            $output = '';

            if (!empty($PAGE->theme->settings->frontpagemycoursessorting)) {
                $courses = enrol_get_my_courses(null, 'sortorder ASC');
                if ($courses) {
                    // We have something to work with.  Get the last accessed information for the user and populate.
                    global $DB, $USER;
                    $lastaccess = $DB->get_records('user_lastaccess', array('userid' => $USER->id), '', 'courseid, timeaccess');
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
                    uasort($courses, array($this, 'timeaccesscompare'));
                } else {
                    return $nomycourses;
                }
                $sortorder = $lastaccess;
            } else if (!empty($CFG->navsortmycoursessort)) {
                // sort courses the same as in navigation menu
                $sortorder = 'visible DESC,'.$CFG->navsortmycoursessort.' ASC';
                $courses = enrol_get_my_courses('summary, summaryformat', $sortorder);
                if (!$courses) {
                    return $nomycourses;
                }
            } else {
                $sortorder = 'visible DESC,sortorder ASC';
                $courses = enrol_get_my_courses('summary, summaryformat', $sortorder);
                if (!$courses) {
                    return $nomycourses;
                }
            }

            $rhosts = array();
            $rcourses = array();
            if (!empty($CFG->mnet_dispatcher_mode) && $CFG->mnet_dispatcher_mode === 'strict') {
                $rcourses = get_my_remotecourses($USER->id);
                $rhosts = get_my_remotehosts();
            }

            if (!empty($courses) || !empty($rcourses) || !empty($rhosts)) {

                $chelper = new coursecat_helper();
                if (count($courses) > $CFG->frontpagecourselimit) {
                    // There are more enrolled courses than we can display, display link to 'My courses'.
                    $totalcount = count($courses);
                    $courses = array_slice($courses, 0, $CFG->frontpagecourselimit, true);
                    $chelper->set_courses_display_options(array(
                        'viewmoreurl' => new moodle_url('/my/'),
                        'viewmoretext' => new lang_string('mycourses')
                    ));
                } else {
                    // All enrolled courses are displayed, display link to 'All courses' if there are more courses in system.
                    $chelper->set_courses_display_options(array(
                        'viewmoreurl' => new moodle_url('/course/index.php'),
                        'viewmoretext' => new lang_string('fulllistofcourses')
                    ));
                    $totalcount = $DB->count_records('course') - 1;
                }
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->set_attributes(array(
                    'class' => 'frontpage-course-list-enrolled'
                ));
                $output .= $this->coursecat_courses($chelper, $courses, $totalcount);

                // MNET
                if (!empty($rcourses)) {
                    // at the IDP, we know of all the remote courses
                    $output .= html_writer::start_tag('div', array(
                        'class' => 'courses'
                    ));
                    foreach ($rcourses as $course) {
                        $output .= $this->frontpage_remote_course($course);
                    }
                    $output .= html_writer::end_tag('div'); // .courses
                } else if (!empty($rhosts)) {
                    // non-IDP, we know of all the remote servers, but not courses
                    $output .= html_writer::start_tag('div', array(
                        'class' => 'courses'
                    ));
                    foreach ($rhosts as $host) {
                        $output .= $this->frontpage_remote_host($host);
                    }
                    $output .= html_writer::end_tag('div'); // .courses
                }
            }
            return $output;
        }

        public function course_modchooser() {

            // This HILLBROOK function is overridden here to refer to the local theme's copy of modchooser to render a modified.
            // Activity chooser for Hillbrook.
            if (!$this->page->requires->should_create_one_time_item_now('core_course_modchooser')) {
                return '';
            }

            $modchooser = new \theme_klassplace\output\modchooser();
            return $this->render($modchooser);
        }


        /**
         * Renders HTML for displaying the sequence of course module editing buttons
         *
         * @see course_get_cm_edit_actions()
         *
         * @param action_link[] $actions Array of action_link objects
         * @param cm_info $mod The module we are displaying actions for.
         * @param array $displayoptions additional display options:
         *     ownerselector => A JS/CSS selector that can be used to find an cm node.
         *         If specified the owning node will be given the class 'action-menu-shown' when the action
         *         menu is being displayed.
         *     constraintselector => A JS/CSS selector that can be used to find the parent node for which to constrain
         *         the action menu to when it is being displayed.
         *     donotenhance => If set to true the action menu that gets displayed won't be enhanced by JS.
         * @return string
         */
        public function course_section_cm_edit_actions($actions, \cm_info $mod = null, $displayoptions = array()) {
            global $CFG, $PAGE;

            if (empty($actions)) {
                return '';
            }

            /*
             * This works only for standard section based course formats. Some formats
             * may need a local implementation.
             * @see course/format/page/blocklib.php for format page.
             */
            $baseurl = '/theme/klassplace/mod_thumb.php';
            $editthumbstr = get_string('editmodthumb', 'theme_klassplace');
            $actions['thumb'] = new \action_menu_link_primary(
                new moodle_url($baseurl, array('id' => $mod->id)),
                new pix_icon('editthumb', $editthumbstr, 'theme', array('class' => 'iconsmall', 'width' => '16')),
                $editthumbstr,
                array('class' => 'editing_thumb', 'data-action' => 'thumb', 'aria-live' => 'assertive')
            );

            return parent::course_section_cm_edit_actions($actions, $mod, $displayoptions);
        }

        /**
         * @param object $course the current course
         * @param int $section the section num
         * @param int $sectionreturn
         * @param array $displayoptions some options altering the way the link is displayed.
         */
        function course_section_add_cm_control($course, $section, $sectionreturn = null, $displayoptions = []) {
            global $CFG, $USER, $PAGE;

            // USEREQUIPMENT HOOK
            $ueconfig = null;
            if (is_dir($CFG->dirroot.'/local/userequipment')) {
                include_once($CFG->dirroot.'/local/userequipment/lib.php');
                $ueconfig = get_config('local_userequipment');
                $uemanager = get_ue_manager();
            }

            // TODO : prefiltering uefilters on activities above is even NOT necessary.
            // This code could be moved much upper and does the fitering by itself.
            if (!empty($ueconfig->useenhancedmodchooser) && $this->page->user_is_editing()) {
                global $DB;

                $uerenderer = $PAGE->get_renderer('local_userequipment');
                $str = html_writer::start_tag('div', array('class' => 'section_add_menus', 'id' => 'add_menus-section-'. $section));
                $sectionid = $DB->get_field('course_sections', 'id', ['course' => $course->id, 'section' => $section]);
                $str .= $uerenderer->render_modchooser_link($sectionid, $section);
                $str .= $uerenderer->render_modchooser();
                $str .= html_writer::end_tag('div');
                return $str;
            }
            // /USEREQUIPMENT HOOK

            // The returned control HTML can be one of the following:
            // - Only the non-ajax control (select menus of activities and resources) with a noscript fallback for non js clients.
            // - Only the ajax control (the link which when clicked produces the activity chooser modal). No noscript fallback.
            // - [Behat only]: The non-ajax control and optionally the ajax control (depending on site settings). If included, the link
            // takes priority and the non-ajax control is wrapped in a <noscript>.
            // Behat requires the third case because some features run with JS, some do not. We must include the noscript fallback.
            $behatsite = defined('BEHAT_SITE_RUNNING');
            $nonajaxcontrol = '';
            $ajaxcontrol = '';
            $courseajaxenabled = course_ajax_enabled($course);
            $userchooserenabled = get_user_preferences('usemodchooser', $CFG->modchooserdefault);

            // Decide what combination of controls to output:
            // During behat runs, both controls can be used in conjunction to provide non-js fallback.
            // During normal use only one control or the other will be output. No non-js fallback is needed.
            $rendernonajaxcontrol = $behatsite || !$courseajaxenabled || !$userchooserenabled || $course->id != $this->page->course->id;
            $renderajaxcontrol = $courseajaxenabled && $userchooserenabled && $course->id == $this->page->course->id;

            // The non-ajax control, which includes an entirely non-js (<noscript>) fallback too.
            if ($rendernonajaxcontrol) {
                $vertical = !empty($displayoptions['inblock']);

                // Check to see if user can add menus.
                if (!has_capability('moodle/course:manageactivities', context_course::instance($course->id))
                    || !$this->page->user_is_editing()) {
                    return '';
                }

                // Retrieve all modules with associated metadata.
                $contentitemservice = \core_course\local\factory\content_item_service_factory::get_content_item_service();
                $urlparams = ['section' => $section];
                if (!is_null($sectionreturn)) {
                    $urlparams['sr'] = $sectionreturn;
                }
                $modules = $contentitemservice->get_content_items_for_user_in_course($USER, $course, $urlparams);

                // Return if there are no content items to add.
                if (empty($modules)) {
                    return '';
                }

                // We'll sort resources and activities into two lists.
                $activities = array(MOD_CLASS_ACTIVITY => array(), MOD_CLASS_RESOURCE => array());

                foreach ($modules as $module) {
                    // Note : ue config may be null !
                	// PATCH+ : User equipment : Loop on modules and filter them.
                    if (!empty($ueconfig->enabled)) {
                        if (!$uemanager->check_user_equipment('mod', $module->name)) {
                            unset($modules[$module->name]);
                            continue;
                        }
                    }
                	// PATCH-.
                    $activityclass = MOD_CLASS_ACTIVITY;
                    if ($module->archetype == MOD_ARCHETYPE_RESOURCE) {
                        $activityclass = MOD_CLASS_RESOURCE;
                    } else if ($module->archetype === MOD_ARCHETYPE_SYSTEM) {
                        // System modules cannot be added by user, do not add to dropdown.
                        continue;
                    }
                    $link = $module->link;
                    $activities[$activityclass][$link] = $module->title;
                }

                $straddactivity = get_string('addactivity');
                $straddresource = get_string('addresource');
                $sectionname = get_section_name($course, $section);
                $strresourcelabel = get_string('addresourcetosection', null, $sectionname);
                $stractivitylabel = get_string('addactivitytosection', null, $sectionname);

                $template = new Stdclass;
                $template->straddactivity = $straddactivity;
                $template->straddresource = $straddresource;
                $template->sectionname = $sectionname;
                $template->strresourcelabel = $strresourcelabel;
                $template->stractivitylabel = $stractivitylabel;
                $template->section = $section;
                $template->vertical = $vertical;
                $template->activitiesResources = $activities[MOD_CLASS_RESOURCE];
                $template->activitiesActivity = $activities[MOD_CLASS_ACTIVITY];

                $nonajaxcontrol = html_writer::start_tag('div', array('class' => 'section_add_menus', 'id' => 'add_menus-section-'
                    . $section));

                if (!$vertical) {
                    $nonajaxcontrol .= html_writer::start_tag('div', array('class' => 'horizontal'));
                }

                if (!empty($activities[MOD_CLASS_RESOURCE])) {
                    $select = new url_select($activities[MOD_CLASS_RESOURCE], '', array('' => $straddresource), "ressection$section");
                    $select->set_help_icon('resources');
                    $select->set_label($strresourcelabel, array('class' => 'accesshide'));
                    $nonajaxcontrol .= $this->output->render($select);
                }

                if (!empty($activities[MOD_CLASS_ACTIVITY])) {
                    $select = new url_select($activities[MOD_CLASS_ACTIVITY], '', array('' => $straddactivity), "section$section");
                    $select->set_help_icon('activities');
                    $select->set_label($stractivitylabel, array('class' => 'accesshide'));
                    $nonajaxcontrol .= $this->output->render($select);
                }

                if (!$vertical) {
                    $nonajaxcontrol .= html_writer::end_tag('div');
                }

                $nonajaxcontrol .= html_writer::end_tag('div');
            }

            // The ajax control - the 'Add an activity or resource' link.
            if ($renderajaxcontrol) {
                // The module chooser link.
                $straddeither = get_string('addresourceoractivity');
                $ajaxcontrol = html_writer::start_tag('div', array('class' => 'mdl-right'));
                $ajaxcontrol .= html_writer::start_tag('div', array('class' => 'section-modchooser'));
                $icon = $this->output->pix_icon('t/add', '');
                $span = html_writer::tag('span', $straddeither, array('class' => 'section-modchooser-text'));
                $ajaxcontrol .= html_writer::tag('button', $icon . $span, [
                        'class' => 'section-modchooser-link btn btn-link from-klassplace-1589',
                        'data-action' => 'open-chooser',
                        'data-sectionid' => $section,
                        'data-sectionreturnid' => $sectionreturn,
                    ]
                );
                $ajaxcontrol .= html_writer::end_tag('div');
                $ajaxcontrol .= html_writer::end_tag('div');

                // Load the JS for the modal.
                $this->course_activitychooser($course->id);
            }

            // Behat only: If both controls are being included in the HTML,
            // show the link by default and only fall back to the selects if js is disabled.
            if ($behatsite && $renderajaxcontrol) {
                $nonajaxcontrol = html_writer::tag('div', $nonajaxcontrol, array('class' => 'hiddenifjs addresourcedropdown'));
                $ajaxcontrol = html_writer::tag('div', $ajaxcontrol, array('class' => 'visibleifjs addresourcemodchooser'));
            }

            // If behat is running, we should have the non-ajax control + the ajax control.
            // Otherwise, we'll have one or the other.
            return $ajaxcontrol . $nonajaxcontrol;
        }

    }
} else {
    class course_renderer extends \core_course_renderer {
        use course_renderer_commons;

        /**
         * module thumbs.
         */
        public $thumbfiles = [];

        public function course_modchooser() {
            global $COURSE;

            //M4
        	throw new coding_exception('course_modchooser() can not be used anymore, please use course_activitychooser() instead.');

            // This HILLBROOK function is overridden here to refer to the local theme's copy of modchooser to render a modified.
            // Activity chooser for Hillbrook.
            if (!$this->page->requires->should_create_one_time_item_now('core_course_modchooser')) {
                return '';
            }

            $modchooser = new \theme_klassplace\output\modchooser($COURSE, []);
            return $this->render($modchooser);
        }

        /**
         * Based on standard course_renderer::search_courses.
         * Adds Customlabel coursedata additional search capability.
         *
         * @param array $searchcriteria may contain elements: search, blocklist, modulelist, tagid
         * @return string
         */
        public function search_courses($searchcriteria) {
            global $CFG;
            $content = '';
            if (!empty($searchcriteria)) {
                // print search results

                $displayoptions = array('sort' => array('displayname' => 1));
                // take the current page and number of results per page from query
                $perpage = optional_param('perpage', 0, PARAM_RAW);
                if ($perpage !== 'all') {
                    $displayoptions['limit'] = ((int)$perpage <= 0) ? $CFG->coursesperpage : (int)$perpage;
                    $page = optional_param('page', 0, PARAM_INT);
                    $displayoptions['offset'] = $displayoptions['limit'] * $page;
                }
                // options 'paginationurl' and 'paginationallowall' are only used in method coursecat_courses()
                $displayoptions['paginationurl'] = new moodle_url('/course/search.php', $searchcriteria);
                $displayoptions['paginationallowall'] = true; // allow adding link 'View all'

                $class = 'course-search-result';
                foreach ($searchcriteria as $key => $value) {
                    if (!empty($value)) {
                        $class .= ' course-search-result-'. $key;
                    }
                }
                $chelper = new coursecat_helper();
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT)->
                        set_courses_display_options($displayoptions)->
                        set_search_criteria($searchcriteria)->
                        set_attributes(array('class' => $class));

                $courses = core_course_category::search_courses($searchcriteria, $chelper->get_courses_display_options());
                $totalcount = core_course_category::search_courses_count($searchcriteria);

                if (is_dir($CFG->dirroot.'/mod/customlabel')) {
                    require_once($CFG->dirroot.'/mod/customlabel/xlib.php');
                    $morecourses = customlabel_search_courses($searchcriteria);
                    foreach ($morecourses as $cid => $course) {
                        if (!in_array($cid, $courses)) {
                            $courses[$cid] = $courses;
                        }
                    }
                }

                $courseslist = $this->coursecat_courses($chelper, $courses, $totalcount);

                if (!$totalcount) {
                    if (!empty($searchcriteria['search'])) {
                        $content .= $this->heading(get_string('nocoursesfound', '', $searchcriteria['search']));
                    } else {
                        $content .= $this->heading(get_string('novalidcourses'));
                    }
                } else {
                    $content .= $this->heading(get_string('searchresults'). ": $totalcount");
                    $content .= $courseslist;
                }

                if (!empty($searchcriteria['search'])) {
                    // print search form only if there was a search by search string, otherwise it is confusing
                    $content .= $this->box_start('generalbox mdl-align');
                    $content .= $this->course_search_form($searchcriteria['search']);
                    $content .= $this->box_end();
                }
            } else {
                // just print search form
                $content .= $this->box_start('generalbox mdl-align');
                $content .= $this->course_search_form();
                $content .= $this->box_end();
            }
            return $content;
        }

        /**
         * @param object $course the current course
         * @param object $section the section object
         * @param int $sectionreturn
         * @param array $displayoptions some options altering the way the link is displayed.
         */
        function course_section_add_cm_control($course, $section, $sectionreturn = null, $displayoptions = array()) {
            global $CFG, $USER;

            // PATCH+ : User equipment : Loop on modules and filter them.
            $ueconfig = null;
            if (is_dir($CFG->dirroot.'/local/userequipment')) {
                include_once($CFG->dirroot.'/local/userequipment/lib.php');
                $ueconfig = get_config('local_userequipment');
                $uemanager = get_ue_manager();
            }

            // TODO : prefiltering uefilters on activities above is even NOT necessary.
            // This code could be moved much upper and does the fitering by itself.
            if (!empty($ueconfig->useenhancedmodchooser)) {
                global $DB, $PAGE;

                $uerenderer = $PAGE->get_renderer('local_userequipment');
                $str = html_writer::start_tag('div', array('class' => 'section_add_menus', 'id' => 'add_menus-section-'. $section));
                $sectionid = $DB->get_field('course_sections', 'id', ['course' => $course->id, 'section' => $section]);
                $str .= $uerenderer->render_modchooser_link($sectionid, $section);
                $str .= $uerenderer->render_modchooser();
                $str .= html_writer::end_tag('div');
                return $str;
            }

            // The returned control HTML can be one of the following:
            // - Only the non-ajax control (select menus of activities and resources) with a noscript fallback for non js clients.
            // - Only the ajax control (the link which when clicked produces the activity chooser modal). No noscript fallback.
            // - [Behat only]: The non-ajax control and optionally the ajax control (depending on site settings). If included, the link
            // takes priority and the non-ajax control is wrapped in a <noscript>.
            // Behat requires the third case because some features run with JS, some do not. We must include the noscript fallback.
            $behatsite = defined('BEHAT_SITE_RUNNING');
            $nonajaxcontrol = '';
            $ajaxcontrol = '';
            $courseajaxenabled = course_ajax_enabled($course);
            $userchooserenabled = get_user_preferences('usemodchooser', $CFG->modchooserdefault);

            // Decide what combination of controls to output:
            // During behat runs, both controls can be used in conjunction to provide non-js fallback.
            // During normal use only one control or the other will be output. No non-js fallback is needed.
            $rendernonajaxcontrol = $behatsite || !$courseajaxenabled || !$userchooserenabled || $course->id != $this->page->course->id;
            $renderajaxcontrol = $courseajaxenabled && $userchooserenabled && $course->id == $this->page->course->id;

            // The non-ajax control, which includes an entirely non-js (<noscript>) fallback too.
            if ($rendernonajaxcontrol) {
                $vertical = !empty($displayoptions['inblock']);

                // Check to see if user can add menus.
                if (!has_capability('moodle/course:manageactivities', context_course::instance($course->id))
                    || !$this->page->user_is_editing()) {
                    return '';
                }

                // Retrieve all modules with associated metadata.
                $contentitemservice = \core_course\local\factory\content_item_service_factory::get_content_item_service();
                $urlparams = ['section' => $section];
                if (!is_null($sectionreturn)) {
                    $urlparams['sr'] = $sectionreturn;
                }
                $modules = $contentitemservice->get_content_items_for_user_in_course($USER, $course, $urlparams);

                // Return if there are no content items to add.
                if (empty($modules)) {
                    return '';
                }

                // We'll sort resources and activities into two lists.
                $activities = array(MOD_CLASS_ACTIVITY => array(), MOD_CLASS_RESOURCE => array());

                foreach ($modules as $module) {
                    // Note : ue config may be null !
                    if (!empty($ueconfig->enabled)) {
                        if (!$uemanager->check_user_equipment('mod', $module->name)) {
                            unset($modules[$module->name]);
                            continue;
                        }
                    }
                    $activityclass = MOD_CLASS_ACTIVITY;
                    if ($module->archetype == MOD_ARCHETYPE_RESOURCE) {
                        $activityclass = MOD_CLASS_RESOURCE;
                    } else if ($module->archetype === MOD_ARCHETYPE_SYSTEM) {
                        // System modules cannot be added by user, do not add to dropdown.
                        continue;
                    }
                    $link = $module->link;
                    $activities[$activityclass][$link] = $module->title;
                }
                // PATCH-.

                $straddactivity = get_string('addactivity');
                $straddresource = get_string('addresource');
                $sectionname = get_section_name($course, $section);
                $strresourcelabel = get_string('addresourcetosection', null, $sectionname);
                $stractivitylabel = get_string('addactivitytosection', null, $sectionname);

                $template = new Stdclass;
                $template->straddactivity = $straddactivity;
                $template->straddresource = $straddresource;
                $template->sectionname = $sectionname;
                $template->strresourcelabel = $strresourcelabel;
                $template->stractivitylabel = $stractivitylabel;
                $template->section = $section;
                $template->vertical = $vertical;
                $template->activitiesResources = $activities[MOD_CLASS_RESOURCE];
                $template->activitiesActivity = $activities[MOD_CLASS_ACTIVITY];

                $nonajaxcontrol = html_writer::start_tag('div', array('class' => 'section_add_menus', 'id' => 'add_menus-section-'
                    . $section));

                if (!$vertical) {
                    $nonajaxcontrol .= html_writer::start_tag('div', array('class' => 'horizontal'));
                }

                if (!empty($activities[MOD_CLASS_RESOURCE])) {
                    $select = new url_select($activities[MOD_CLASS_RESOURCE], '', array('' => $straddresource), "ressection$section");
                    $select->set_help_icon('resources');
                    $select->set_label($strresourcelabel, array('class' => 'accesshide'));
                    $nonajaxcontrol .= $this->output->render($select);
                }

                if (!empty($activities[MOD_CLASS_ACTIVITY])) {
                    $select = new url_select($activities[MOD_CLASS_ACTIVITY], '', array('' => $straddactivity), "section$section");
                    $select->set_help_icon('activities');
                    $select->set_label($stractivitylabel, array('class' => 'accesshide'));
                    $nonajaxcontrol .= $this->output->render($select);
                }

                if (!$vertical) {
                    $nonajaxcontrol .= html_writer::end_tag('div');
                }

                $nonajaxcontrol .= html_writer::end_tag('div');
            }

            // The ajax control - the 'Add an activity or resource' link.
            if ($renderajaxcontrol) {
                // The module chooser link.
                $straddeither = get_string('addresourceoractivity');
                $ajaxcontrol = html_writer::start_tag('div', array('class' => 'mdl-right'));
                $ajaxcontrol .= html_writer::start_tag('div', array('class' => 'section-modchooser'));
                $icon = $this->output->pix_icon('t/add', '');
                $span = html_writer::tag('span', $straddeither, array('class' => 'section-modchooser-text'));
                $ajaxcontrol .= html_writer::tag('button', $icon . $span, [
                        'class' => 'section-modchooser-link btn btn-link from-klassplace-1858',
                        'data-action' => 'open-chooser',
                        'data-sectionid' => $section,
                        'data-sectionreturnid' => $sectionreturn,
                    ]
                );
                $ajaxcontrol .= html_writer::end_tag('div');
                $ajaxcontrol .= html_writer::end_tag('div');

                // Load the JS for the modal.
                $this->course_activitychooser($course->id);
            }

            // Behat only: If both controls are being included in the HTML,
            // show the link by default and only fall back to the selects if js is disabled.
            if ($behatsite && $renderajaxcontrol) {
                $nonajaxcontrol = html_writer::tag('div', $nonajaxcontrol, array('class' => 'hiddenifjs addresourcedropdown'));
                $ajaxcontrol = html_writer::tag('div', $ajaxcontrol, array('class' => 'visibleifjs addresourcemodchooser'));
            }

            // If behat is running, we should have the non-ajax control + the ajax control.
            // Otherwise, we'll have one or the other.
            return $ajaxcontrol . $nonajaxcontrol;
        }
    }
}