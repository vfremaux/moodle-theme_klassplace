<?php

namespace theme_klassplace;

use moodle_url;
use pix_icon;
use context_course;
use coding_exception;

/**
 * Class used to generate a collection of navigation nodes most closely related
 * to the current page. Resurection of deprecated core.
 *
 * @package theme_klassplace
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class flat_navigation extends \flat_navigation {
    /** @var moodle_page the moodle page that the navigation belongs to */
    protected $page;

    /**
     * Constructor.
     *
     * @param moodle_page $page
     */
    public function __construct(\moodle_page &$page) {
        if (during_initial_install()) {
            return;
        }
        $this->page = $page;
    }

    /**
     * Build the list of navigation nodes based on the current navigation and settings trees.
     *
     */
    public function initialise() {
        global $PAGE, $USER, $OUTPUT, $CFG;
        if (during_initial_install()) {
            return;
        }

        $current = false;

        $course = $PAGE->course;

        $this->page->navigation->initialise();

        // First walk the nav tree looking for "flat_navigation" nodes.
        if ($course->id > 1) {
            // It's a real course.
            $url = new moodle_url('/course/view.php', array('id' => $course->id));

            $coursecontext = context_course::instance($course->id, MUST_EXIST);
            $displaycontext = \context_helper::get_navigation_filter_context($coursecontext);
            // This is the name that will be shown for the course.
            $coursename = empty($CFG->navshowfullcoursenames) ?
                format_string($course->shortname, true, ['context' => $displaycontext]) :
                format_string($course->fullname, true, ['context' => $displaycontext]);

            $flat = new flat_navigation_node(navigation_node::create($coursename, $url), 0);
            $flat->set_collectionlabel($coursename);
            $flat->key = 'coursehome';
            $flat->icon = new pix_icon('i/course', '');

            $courseformat = course_get_format($course);
            $coursenode = $PAGE->navigation->find_active_node();
            $targettype = navigation_node::TYPE_COURSE;

            // Single activity format has no course node - the course node is swapped for the activity node.
            if (!$courseformat->has_view_page()) {
                $targettype = navigation_node::TYPE_ACTIVITY;
            }

            while (!empty($coursenode) && ($coursenode->type != $targettype)) {
                $coursenode = $coursenode->parent;
            }
            // There is one very strange page in mod/feedback/view.php which thinks it is both site and course
            // context at the same time. That page is broken but we need to handle it (hence the SITEID).
            if ($coursenode && $coursenode->key != SITEID) {
                $this->add($flat);
                foreach ($coursenode->children as $child) {
                    if ($child->action) {
                        $flat = new flat_navigation_node($child, 0);
                        $this->add($flat);
                    }
                }
            }

            $node = $this->page->navigation;
            navigation_node::static_build_flat_navigation_list($node, $this, true, get_string('site'));
        } else {
            $node = $this->page->navigation;
            navigation_node::static_build_flat_navigation_list($node, $this, false, get_string('site'));
        }

        $admin = $PAGE->settingsnav->find('siteadministration', navigation_node::TYPE_SITE_ADMIN);
        if (!$admin) {
            // Try again - crazy nav tree!
            $admin = $PAGE->settingsnav->find('root', navigation_node::TYPE_SITE_ADMIN);
        }
        if ($admin) {
            $flat = new flat_navigation_node($admin, 0);
            $flat->set_showdivider(true, get_string('sitesettings'));
            $flat->key = 'sitesettings';
            $flat->icon = new pix_icon('t/preferences', '');
            $this->add($flat);
        }

        // Add-a-block in editing mode.
        if (isset($this->page->theme->addblockposition) &&
                $this->page->theme->addblockposition == BLOCK_ADDBLOCK_POSITION_FLATNAV &&
                $PAGE->user_is_editing() && $PAGE->user_can_edit_blocks()) {
            $url = new moodle_url($PAGE->url, ['bui_addblock' => '', 'sesskey' => sesskey()]);
            $addablock = navigation_node::create(get_string('addblock'), $url);
            $flat = new flat_navigation_node($addablock, 0);
            $flat->set_showdivider(true, get_string('blocksaddedit'));
            $flat->key = 'addblock';
            $flat->icon = new pix_icon('i/addblock', '');
            $this->add($flat);

            $addblockurl = "?{$url->get_query_string(false)}";

            $PAGE->requires->js_call_amd('core_block/add_modal', 'init',
                [$addblockurl, $this->page->get_edited_page_hash()]);
        }
    }

}
