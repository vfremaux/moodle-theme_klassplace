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
 * Defines renderer for course format flexsections
 *
 * @package    format_flexsections
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/course/format/flexsections/renderer.php');

/**
 * Renderer for flexsections format.
 *
 * @copyright 2012 Marina Glancy
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_klassplace_format_flexsections_renderer extends format_flexsections_renderer {
    /** @var core_course_renderer Stores instances of core_course_renderer */
    protected $courserenderer = null;

    public $availablestyles;

    /**
     * Current theme config.
     */
    protected $config;

    public function __construct(moodle_page $page, $target) {
        global $PAGE, $COURSE;

        parent::__construct($page, $target);
        static $initialized = false;

        $this->config = get_config('theme_'.$PAGE->theme->name);
        $this->availablestyles = $this->parse_styleconfig();

        if (!$initialized) {
            if ($PAGE->user_is_editing()) {
                $PAGE->requires->js_call_amd('theme_klassplace/flex_section_control', 'init_editing', array($COURSE->id));
            } else {
                $PAGE->requires->js_call_amd('theme_klassplace/flex_section_control', 'init', array($COURSE->id));
            }
            $initialized = true;
        }
    }

    /**
     * Display section and all its activities and subsections (called recursively)
     *
     * @param int|stdClass $course
     * @param int|section_info $section
     * @param int $sr section to return to (for building links)
     * @param int $level nested level on the page (in case of 0 also displays additional start/end html code)
     */
    public function display_section($course, $section, $sr, $level = 0, $astemplate = '', $islastsection = false) {
        global $PAGE, $USER, $DB, $CFG, $OUTPUT;
        static $userstates;

        $sectionnum = @$section->section;

        $template = new \StdClass;
        $template->ismoving = optional_param('moving', false, PARAM_INT);
        $template->preinsertsection = '';
        $template->postinsertsection = '';

        if (is_object($section) && ($sectionnum != $template->ismoving)) {
            if ($section->section != $template->ismoving) {
                $template->preinsertsection = $this->display_insert_section_here($course, $section->parent, $section, $sr);
            }
        }

        if (!isset($userstates)) {

            $userstates = array();

            // Fills the cache of user states at first section called.

            /*
             * If user's preferences never initialized, then hide final leaves and show 
             * everything else
             */
            $params = array('userid' => $USER->id, 'name' => 'flexsection_initialized_'.$course->id);
            if (!$DB->record_exists('user_preferences', $params)) {
                require_once($CFG->dirroot.'/theme/klassplace/sections/sectionslib.php');

                $select = " name LIKE 'flexsection\\_%' AND userid = ? AND value = ? ";
                $DB->delete_records_select('user_preferences', $select, array($USER->id, $course->id));

                // One setting for all variants in master theme config.
                $config = get_config('theme_klassplace');
                $flexinitialstate = (empty($config->flexinitialstate)) ? 'collapsed' : $config->flexinitialstate;

                if ($flexinitialstate == 'reset') {
                    // Open roots and close all leaves.
                    $leaves = sections_get_leaves($course->id);
                } else if ($flexinitialstate == 'collapsed') {
                    // close everything.
                    $leaves = $DB->get_records('course_sections', array('course' => $course->id));
                }
                if ($leaves) {
                    foreach ($leaves as $leaf) {
                        $hidekey = 'flexsection_'.$leaf->id.'_hidden';
                        $newrec = new StdClass;
                        $newrec->userid = $USER->id;
                        $newrec->name = $hidekey;
                        $newrec->value = $course->id;
                        $DB->insert_record('user_preferences', $newrec);
                    }
                }

                // Mark the course as initialized for the user.
                $newrec = new StdClass;
                $newrec->userid = $USER->id;
                $newrec->name = 'flexsection_initialized_'.$course->id;
                $newrec->value = 1;
                $DB->insert_record('user_preferences', $newrec);
            }

            // Request is optimised to the current course scope, using preference value.
            $select = ' userid = :userid AND '.$DB->sql_like('name', ':prefname').' AND value = :value ';
            $params = array('userid' => $USER->id, 'prefname' => 'flexsection\\_%\\_hidden', 'value' => $course->id);
            $flexprefs = $DB->get_records_select('user_preferences', $select, $params);
            if ($flexprefs) {
                // Note flexprefs register only hidden (collpased) sections and have NO records for visible sections.
                foreach ($flexprefs as $prf) {
                    $name = str_replace('flexsection_', '', $prf->name);
                    $name = str_replace('_hidden', '', $name);
                    $userstates[$name] = $prf->value;
                }
            }
        }

        // Start building flexsection section template.

        $course = course_get_format($course)->get_course();
        $section = course_get_format($course)->get_section($section); // This converts $section from int to object.
        $context = context_course::instance($course->id);
        $template->iscontentvisible = true;

        // resolve level to H<n>
        $template->hlevel = $level + 1;
        if ($template->hlevel > 6) {
            $template->hlevel = 6;
        }

        $tosection = $tosection = optional_param('tosection', false, PARAM_INT);
        if ($tosection) {
            // When specifying a 'tosection' argument, we will open the path down to this section.
             if ($section->section == $tosection) {
                // We are the happy candidate.
                $sectiontoopen = clone($section);
                while ($sectiontoopen->parent) {
                    unset($userstates[$sectiontoopen->id]);
                    $hidekey = 'flexsection_'.$sectiontoopen->id.'_hidden';
                    $DB->delete_records('user_preferences', array('userid' => $USER->id, 'name' => $hidekey));
                    $sectiontoopen = course_get_format($course)->get_section($sectiontoopen->parent);
                }
                // Open top level.
                unset($userstates[$sectiontoopen->id]);
                $hidekey = 'flexsection_'.$sectiontoopen->id.'_hidden';
                $DB->delete_records('user_preferences', array('userid' => $USER->id, 'name' => $hidekey));
             }
        }

        if (!$section->uservisible || !course_get_format($course)->is_section_real_available($section)) {
            if ($section->visible && !$section->available && $section->availableinfo) {
                // Still display section but without content.
                $template->iscontentvisible = false;
            } else {
                return '';
            }
        }

        if ($section->section == 0) {
            // General section is always visible.
            $template->iscontentvisible = true;
        }

        $template->sectionnum = $section->section;
        $template->sectionid = $section->id;
        $movingsection = course_get_format($course)->is_moving_section();

        $template->istopfirst = false;
        if ($level === 0) {
            // This is the top section (section 0).
            $template->istopfirst = true;

            if ($template->ismoving) {
                $template->hascancelmovingcontrols = true;
                $cancelmovingcontrols = course_get_format($course)->get_edit_controls_cancelmoving();

                // Fix URL with section return
                $cancelmovingcontrols[0]->url->params(array('section' => $sr));
                $cancelmovingcontrols[0]->url->remove_params('sectionid');

                $template->cancelmovingcontrols = '';
                foreach ($cancelmovingcontrols as $control) {
                    $template->cancelmovingcontrols .= $this->render($control);
                }
            }
            $template->main = 'main';
        } else {
            $template->main = 'sub';
        }

        $children = course_get_format($course)->get_subsections($template->sectionnum);
        $template->isleafclass = (empty($children)) ? 'isleaf' : '';
        $template->ismovingclass = ($movingsection === $template->sectionnum) ? 'ismoving' : '';
        $template->currentclass = (course_get_format($course)->is_section_current($section)) ? ' current' : '';
        $template->level = $level;
        $template->highlight = ($section->section == $tosection) ? 'highlight' : '';
        $template->hassubs = false;

        // Display controls except for expanded/collapsed.
        $controls = course_get_format($course)->get_section_edit_controls($section, $sr);

        // Fix all standard flexsections controls to respect the current section

        foreach ($controls as &$acontrol) {
            // Fix URL with section return.
            if (isset($acontrol->url)) {
                $acontrol->url->params(array('section' => $sr));
                $acontrol->url->remove_params(array('sectionid'));
            }
        }

        // Get available section style overrides from config.
        $this->availablestyles = $this->parse_styleconfig();

        // Theme adds style related additional attribute in format.
        if (!empty($this->availablestyles) && ($section->section > 0) && $PAGE->user_is_editing()) {
            if (has_capability('moodle/course:update', $context)) {
                $contentclassurl = new moodle_url('/theme/klassplace/sections/sectionclass.php', array('id' => $section->id, 'sr' => $sr));
                $text = new lang_string('chooseclass', 'theme_'.$PAGE->theme->name);
                $controls[] = new format_flexsections_edit_control('contentclass', $contentclassurl, $text);
            }
        }

        // Theme adds per section role assign.
        if (is_dir($CFG->dirroot.'/local/sectioncontexts')) {
            if ($PAGE->user_is_editing()) {
                if (has_capability('local/sectioncontexts:assignrole', $context)) {
                    $sectioncontext = context_course_section::instance($section->id);
                    $assignroleurl = new moodle_url('/admin/roles/assign.php', array('contextid' => $sectioncontext->id, 'sesskey' => sesskey()));
                    $text = new lang_string('assignrole', 'role');
                    $controls[] = new format_flexsections_edit_control('assignrole', $assignroleurl, $text);
                }
            }
        }

        /*
        // TODO : At the moment, we do not exactly know how to store this in a context that would be
        // correctly backuped with the course data. So remove it and try to find another way.
        // Theme adds activitynames hiding control.
        if (has_capability('moodle/course:update', $context)) {
            if (!empty($section->hideactivitynames)) {
                $params = array('id' => $section->id, 'sr' => $sr, 'what' => 'showactivitynames');
                $switchurl = new moodle_url('/theme/'.$PAGE->theme->name.'/flexsections/switchactivitynames.php', $params);
                $text = new lang_string('showactivitynames', 'theme_'.$PAGE->theme->name);
                $controls[] = new format_flexsections_edit_control('hideactivitynames', $switchurl, $text);
            } else {
                $params = array('id' => $section->id, 'sr' => $sr, 'what' => 'hideactivitynames');
                $switchurl = new moodle_url('/theme/'.$PAGE->theme->name.'/flexsections/switchactivitynames.php', $params);
                $text = new lang_string('hideactivitynames', 'theme_'.$PAGE->theme->name);
                $controls[] = new format_flexsections_edit_control('hideactivitynames', $switchurl, $text);
            }
        }
        */

        $collapsedcontrol = null;
        // Override add expand// collapse control for all users.
        $hiddenvar = false;
        $template->ariaexpanded = 'false';
        if ($level) {
            if (!array_key_exists($section->id, $userstates)) {
                $text = new lang_string('showcollapsed', 'format_flexsections');
                $handleclass = 'expanded flexcontrol level-'.$level;
                $src = $this->output->image_url('t/expanded');
                $template->collapsedclass = 'expanded';
                $template->ariaexpanded = 'true';
                $template->contentcollapsedclass = 'expanded';
                $hiddenvar = true;
            } else {
                $text = new lang_string('showexpanded', 'format_flexsections');
                $handleclass = 'collapsed flexcontrol level-'.$level;
                $src = $this->output->image_url('t/collapsed');
                $template->collapsedclass = 'collapsed';
                $template->ariaexpanded = 'false';
                $template->contentcollapsedclass = 'collapsed';
            }
            /*
            $attrs = array('src' => $src,
                           'title' => $text,
                           'aria-hidden' => 'true');
           */
            // $collapsedcontrolicon = '<span class="overlay"></span>'.html_writer::tag('img', '', $attrs);
            $collapsedcontrolicon = '<i class="flexsection-handle"></i>';
            $attrs = array('class' => $handleclass,
                           'id' => 'control-'.$section->id.'-section-'.$section->section);
            $collapsedcontrol = html_writer::tag('div', $collapsedcontrolicon, $attrs);
        } else {
            $handleclass = 'flexcontrol level-'.$level;
        }

        $controlsstr = '';
        foreach ($controls as $idxcontrol => $control) {
            if ($control->class === 'expanded' || $control->class === 'collapsed') {
                // Ignore old collapse control mode.
                // $collapsedcontrol = $control;
            } else {
                $controlsstr .= $this->render($control);
            }
        }

        if (!empty($controlsstr)) {
            $template->controls = $controlsstr;
            $template->hascontrols = true;
        }

        $template->hideactivityclass = (!empty($section->hideactivitynames)) ? ' hide-activity-names' : '';

        // Display section name and expanded/collapsed control.
        $template->hastitle = false;
        if ($template->sectionnum && ($title = $this->section_title($template->sectionnum, $course, true))) {
            if (!$PAGE->user_is_editing()) {
                $sectionnameid = 'sectioname-'.$section->id.'-section-'.$section->section;
                $title = '<span id="'.$sectionnameid.'" aria-hidden="true" class="sectioname '.$handleclass.'">'.$title.'</span>';
            }
            $template->hastitle = true;
            if (is_object($collapsedcontrol)) {
                $template->title = $this->render($collapsedcontrol).$title;
            } else {
                $template->title = $collapsedcontrol.$title;
            }

            $attrs = [];

            // Check section style overrides.
            if ($this->availablestyles) {
                $this->add_custom_style($attrs, $section);
                $template->customclasses = @$attrs['class'];
                $template->customstyle = @$attrs['style'];
            }
        }

        // Display section availability.
        $template->sectionavailability = $this->section_availability($section);

        // Display section contents (activities and subsections).
        if ($template->iscontentvisible) {

            $template->summary = $this->format_summary_text($section);

            // Display resources and activities.
            $template->contentstyle = 'display:block;visibility:visible';
            if ($hiddenvar) {
                $template->contentstyle = 'display:none;visibility:hidden';
            }

            $template->coursemodulelist = $this->courserenderer->course_section_cm_list($course, $section, $sr);

            if ($PAGE->user_is_editing()) {
                $template->isediting = true;
                // a little hack to allow use drag&drop for moving activities if the section is empty
                $template->emptysection = empty(get_fast_modinfo($course)->sections[$template->sectionnum]);
                $template->coursemodulecontrols = $this->courserenderer->course_section_add_cm_control($course, $template->sectionnum, $sr);
            }

            // Display subsections.
            if (!empty($children) || $movingsection) {

                if ($template->istopfirst) {
                    // Display collapse/expand/init buttons.
                    $template->globalcontrols = $this->globalcontrols();
                }

                $template->nextlevel = $level + 1;
                if ($hiddenvar && $section->section) {
                    $template->contentnotmovingstyle = 'display:none';
                }

                $isfirst = true;
                if (!empty($children)) {
                    $childcount = count($children);
                    $i = 0;
                    foreach ($children as $num) {
                        $i++;
                        if ($i >= $childcount) {
                            $islast = true;
                        } else {
                            $islast = false;
                        }
                        $childtpl = $this->display_section($course, $num, $sr, $level + 1, 'astemplate', $islast);
                        if (empty($childtpl)) {
                            continue;
                        }
                        $template->subs[] = $childtpl;
                    }
                }
                if (!empty($template->subs)) {
                    $template->hassubs = true;
                } else {
                    $template->hassubs = false;
                    if ($template->ismoving) {
                        // This is when moving.
                        if ($section->section != $template->ismoving) {
                            $template->emptyinsertsection = $this->display_insert_section_here($course, $section->section, null, $sr);
                        }
                    }
                }
            }
            if ($addsectioncontrol = course_get_format($course)->get_add_section_control($template->sectionnum)) {
                $addsectioncontrol->url->params(array('section' => $sr));
                $template->addsectioncontrol = $this->render($addsectioncontrol);
            }
        }
        $template->islastchild = false;
        if ($template->ismoving && $islastsection) {
            // This is when moving.
            $template->islastchild = true;
            if ($level !== 0) {
                // if ($section->section + 1 != $template->ismoving) {
                    $template->postinsertsection = $this->display_insert_section_here($course, $section->parent, null, $sr);
                // }
            }
        }

        if ($astemplate == 'astemplate') {
            // Used for sub templates;
            return $template;
        }

        echo $OUTPUT->render_from_template('theme_klassplace/flexsections_layout', $template);
    }

    /**
     * Displays the target div for moving section (in 'moving' mode only)
     *
     * @param int|stdClass $courseorid current course
     * @param int|section_info $parent new parent section
     * @param null|int|section_info $before number of section before which we want to insert (or null if in the end)
     */
    protected function display_insert_section_here($courseorid, $parent, $before = null, $sr = null) {
        if ($control = course_get_format($courseorid)->get_edit_control_movehere($parent, $before, $sr)) {
            $control->url->params(array('section' => $sr));
            return $this->render($control);
        }
    }

    public function add_custom_style(&$attrs, &$section) {
        global $DB;

        $availableconfigs = $this->availablestyles['configs'];
        $styleoverride = $DB->get_field('course_format_options', 'value', array('sectionid' => $section->id, 'name' => 'styleoverride'));
        $attrs['style'] = '';
        $attrs['class'] = '';
        if ($styleoverride) {
            if (array_key_exists($styleoverride, $availableconfigs)) {
                $styletoapply = $availableconfigs[$styleoverride];
                if (preg_match('/^\\{(.*)\\}/', $styletoapply, $matches)) {
                    // If is a real style rule, apply as style attrribute.
                    $attrs['style'] = $matches[1];
                } else {
                    $attrs['class'] = @$attrs['class'].' '.$styletoapply;
                }
            }
        }
    }

    protected function globalcontrols() {

        $str = '';

        $params = array('type' => 'button',
                        'class' => 'btn flexsection-global-control',
                        'id' => 'flexsections-control-collapseall',
                        'value' => get_string('collapseall', 'theme_klassplace'));
        $str .= html_writer::tag('input', '', $params);

        $params = array('type' => 'button',
                        'class' => 'btn flexsection-global-control',
                        'id' => 'flexsections-control-expandall',
                        'value' => get_string('expandall', 'theme_klassplace'));
        $str .= html_writer::tag('input', '', $params);

        $params = array('type' => 'button',
                        'class' => 'btn flexsection-global-control',
                        'id' => 'flexsections-control-map',
                        'value' => get_string('map', 'theme_klassplace'));
        $str .= html_writer::tag('input', '', $params);

        return $str;
    }

    /**
     * renders HTML for format_flexsections_edit_control
     *
     * @param format_flexsections_edit_control $control
     * @return string
     */
    protected function render_format_flexsections_edit_control(format_flexsections_edit_control $control) {
        if (!$control) {
            return '';
        }

        if ($control->class === 'contentclass') {
            $icon = new pix_icon('contentclass', $control->text, 'format_flexsections', array('class' => 'iconsmall', 'title' => $control->text));
            $action = new action_link($control->url, $icon, null, array('class' => $control->class));
            return $this->render($action);
        }

        return parent::render_format_flexsections_edit_control($control);
    }

    /**
     * Parses the theme configuration flexsectionstyles setting and
     * extracts usable style information for section headings.
     *
     * admitted syntax are : 
     * <stylename>:<stylelabel>:<stylerule>
     * 
     * stylerule can be a class name, or a {<cssrulelist>} real css fragment.
     */
    public function parse_styleconfig() {
        if (!empty($this->config->sectionsstyles)) {
            $rules = explode("\n", $this->config->sectionsstyles);
            foreach ($rules as $r) {
                if (preg_match('/^(#|\\/)/', $r)) {
                    // Ignore commented line.
                    continue;
                }
                if (preg_match('/^[\\s]*$/', $r)) {
                    // Ignore empty or only space lines.
                    continue;
                }
                if (preg_match('/^(.*?):(.*?):(.*)$/', $r, $matches)) {
                    $styleconfigs[$matches[1]] = $matches[3];
                    $stylelabels[$matches[1]] = $matches[2];
                }
            }
            return array('configs' => $styleconfigs, 'labels' => $stylelabels);
        }

        return array('configs' => array(), 'labels' => array());
    }

    /**
     * Displays availability information for the section (hidden, not available unles, etc.)
     * @see course/format/renderer.php format_section_renderer_base
     * @param section_info $section
     * @return string
     */
    public function section_availability($section) {
        $context = context_course::instance($section->course);
        $canviewhidden = has_capability('moodle/course:viewhiddensections', $context);
        return html_writer::div($this->section_availability_message($section, $canviewhidden), 'section_availability');
    }

    /**
     * If section is not visible, display the message about that ('Not available
     * until...', that sort of thing). Otherwise, returns blank.
     *
     * For users with the ability to view hidden sections, it shows the
     * information even though you can view the section and also may include
     * slightly fuller information (so that teachers can tell when sections
     * are going to be unavailable etc). This logic is the same as for
     * activities.
     *
     * @param section_info $section The course_section entry from DB
     * @param bool $canviewhidden True if user can view hidden sections
     * @return string HTML to output
     */
    protected function section_availability_message($section, $canviewhidden) {
        global $CFG;
        $o = '';
        if (!$section->visible) {
            if ($canviewhidden) {
                $o .= $this->courserenderer->availability_info(get_string('hiddenfromstudents'), 'ishidden');
            } else {
                // We are here because of the setting "Hidden sections are shown in collapsed form".
                // Student can not see the section contents but can see its name.
                $o .= $this->courserenderer->availability_info(get_string('notavailable'), 'ishidden');
            }
        } else if (!$section->uservisible) {
            if ($section->availableinfo) {
                // Note: We only get to this function if availableinfo is non-empty,
                // so there is definitely something to print.
                $formattedinfo = \core_availability\info::format_info(
                        $section->availableinfo, $section->course);
                $o .= $this->courserenderer->availability_info($formattedinfo, 'isrestricted');
            }
        } else if ($canviewhidden && !empty($CFG->enableavailability)) {
            // Check if there is an availability restriction.
            $ci = new \core_availability\info_section($section);
            $fullinfo = $ci->get_full_information();
            if ($fullinfo) {
                $formattedinfo = \core_availability\info::format_info(
                        $fullinfo, $section->course);
                $o .= $this->courserenderer->availability_info($formattedinfo, 'isrestricted isfullinfo');
            }
        }
        return $o;
    }

}