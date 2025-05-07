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
 * Defines the renderer for the quiz module.
 *
 * @package   mod_quiz
 * @copyright 2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

// CHANGE
if (is_dir($CFG->dirroot.'/blocks/quiz_behaviour')) {
    require_once($CFG->dirroot.'/blocks/quiz_behaviour/xlib.php');
}

/**
 * The renderer for the quiz module.
 *
 * @copyright  2011 The Open University
 * @copyright  2016 Valery Fremaux (modified version)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_klassplace_mod_quiz_renderer extends mod_quiz_renderer {

    protected static $attemptobj;


    /**
     * Renders the main bit of the review page.
     *
     * @param array $summarydata contain row data for table
     * @param int $page current page number
     * @param mod_quiz_display_options $displayoptions instance of mod_quiz_display_options
     * @param $content contains each question
     * @param quiz_attempt $attemptobj instance of quiz_attempt
     * @param bool $showall if true display attempt on one page
     */
    public function review_form($page, $showall, $displayoptions, $content, $attemptobj) {

        $manager = null;
        if (function_exists('get_block_quiz_behaviour_manager')) {
            $manager = get_block_quiz_behaviour_manager();
        }
        $qid = $attemptobj->get_quizid();

        if ($displayoptions->flags != question_display_options::EDITABLE) {
            return $content;
        }

        $output = '<!-- renderer/review_form -->';

        if (!$manager || !$manager->has_behaviour($qid, 'hideflags')) {
            $this->page->requires->js_init_call('M.mod_quiz.init_review_form', null, false,
                    quiz_get_js_module());

            $output .= html_writer::start_tag('form', array('action' => $attemptobj->review_url(null,
                    $page, $showall), 'method' => 'post', 'class' => 'questionflagsaveform'));
        }
        $output .= html_writer::start_tag('div');
        $output .= $content;
        if (!$manager || !$manager->has_behaviour($qid, 'hideflags')) {
            $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey',
                    'value' => sesskey()));
            $output .= html_writer::start_tag('div', array('class' => 'submitbtns'));
            $output .= html_writer::empty_tag('input', array('type' => 'submit',
                    'class' => 'questionflagsavebutton', 'name' => 'savingflags',
                    'value' => get_string('saveflags', 'question')));
            $output .= html_writer::end_tag('div');
        }
        $output .= html_writer::end_tag('div');
        if (!$manager || !$manager->has_behaviour($qid, 'hideflags')) {
            $output .= html_writer::end_tag('form');
        }

        $output .= '<!-- /renderer/review_form -->';
        return $output;
    }

    /**
     * Attempt Page.
     * Bound to : quiz_behaviour
     * Reason : Change layout
     *
     * @param quiz_attempt $attemptobj Instance of quiz_attempt
     * @param int $page Current page number
     * @param quiz_access_manager $accessmanager Instance of quiz_access_manager
     * @param array $messages An array of messages
     * @param array $slots Contains an array of integers that relate to questions
     * @param int $id The ID of an attempt
     * @param int $nextpage The number of the next page
     * @return string HTML to output.
     */
    public function attempt_page($attemptobj, $page, $accessmanager, $messages, $slots, $id,
            $nextpage) {

        $course = $attemptobj->get_course();
        $manager = null;
        if (function_exists('get_block_quiz_behaviour_manager')) {
            $manager = get_block_quiz_behaviour_manager();
        }
        $qid = $attemptobj->get_quizid();

        if (!$manager || !$manager->has_behaviour($qid, 'alternateattemptpage')) {
            // Back to standard rendering.
            return parent::attempt_page($attemptobj, $page, $accessmanager, $messages, $slots, $id, $nextpage);
        }

        self::$attemptobj = $attemptobj;

        $template = new StdClass;
        $template->header = $this->header();
        $template->quiznotices = $this->quiz_notices($messages);
        $template->countdowntimer = $this->countdown_timer($attemptobj, time());

        $template->questionstr = get_string('question');
        $template->questionnum = $attemptobj->get_question_number($page);
        $template->quizprogressindicator = $this->quiz_progress_indicator($attemptobj);
        $template->questionrefs = $this->question_refs($slots, $attemptobj);

        $template->quizcountdown = $this->quiz_countdown($attemptobj);
        $template->attemptform = $this->attempt_form($attemptobj, $page, $slots, $id, $nextpage);
        $template->footer = $this->footer();
        return $this->output->render_from_template('block_quiz_behaviour/attemptpage', $template);
    }

    /**
     * Creates any controls a the page should have.
     *
     * @param quiz_attempt $attemptobj
     */
    public function summary_page_controls($attemptobj) {
        $output = '';

        $course = $attemptobj->get_course();
        $qid = $attemptobj->get_quizid();
        $manager = null;

        // Get quiz_behaviour manager instance.
        if (function_exists('get_block_quiz_behaviour_manager')) {
            $manager = get_block_quiz_behaviour_manager();
        }

        // Return to place button.
        // CHANGE : Make it aware of no-backwards restriction.
        $navmethod = $attemptobj->get_quiz()->navmethod;
        if ($navmethod == "free") {
            if ($attemptobj->get_state() == quiz_attempt::IN_PROGRESS) {
                $button = new single_button(
                        new moodle_url($attemptobj->attempt_url(null, $attemptobj->get_currentpage())),
                        get_string('returnattempt', 'quiz'));
                $output .= $this->container($this->container($this->render($button),
                        'controls'), 'submitbtns mdl-align');
            }
        }
        // CHANGE.

        // Finish attempt button.
        $options = array(
            'attempt' => $attemptobj->get_attemptid(),
            'finishattempt' => 1,
            'timeup' => 0,
            'slots' => '',
            'cmid' => $attemptobj->get_cmid(),
            'sesskey' => sesskey(),
        );

        $button = new single_button(
                new moodle_url($attemptobj->processattempt_url(), $options),
                get_string('submitallandfinish', 'quiz'));
        $button->id = 'responseform';
        if (!$manager || !$manager->has_behaviour($qid, 'alternateattemptpage')) {
            $button->class = 'btn-finishattempt';
            $button->formid = 'frm-finishattempt';
            if ($attemptobj->get_state() == quiz_attempt::IN_PROGRESS) {
                $totalunanswered = 0;
                if ($attemptobj->get_quiz()->navmethod == 'free') {
                    // Only count the unanswered question if the navigation method is set to free.
                    $totalunanswered = $attemptobj->get_number_of_unanswered_questions();
                }
                $this->page->requires->js_call_amd('mod_quiz/submission_confirmation', 'init', [$totalunanswered]);
            }
            $button->primary = true;
        }

        $duedate = $attemptobj->get_due_date();
        $message = '';
        if ($attemptobj->get_state() == quiz_attempt::OVERDUE) {
            $message = get_string('overduemustbesubmittedby', 'quiz', userdate($duedate));

        } else if ($duedate) {
            $message = get_string('mustbesubmittedby', 'quiz', userdate($duedate));
        }

        $output .= $this->countdown_timer($attemptobj, time());
        $output .= $this->container($message . $this->container(
                $this->render($button), 'controls'), 'submitbtns mdl-align');

        return $output;

        // CHANGED

        $button = new single_button(
                new moodle_url($attemptobj->processattempt_url(), $options),
                get_string('submitallandfinish', 'quiz'));
        $button->id = 'responseform';

        if (!$manager || !$manager->has_behaviour($qid, 'alternateattemptpage')) {
            if ($attemptobj->get_state() == quiz_attempt::IN_PROGRESS) {
                $button->add_action(new confirm_action(get_string('confirmclose', 'quiz'), null,
                    get_string('submitallandfinish', 'quiz')));
            }
        }

        $duedate = $attemptobj->get_due_date();
        $message = '';
        if ($attemptobj->get_state() == quiz_attempt::OVERDUE) {
            $message = get_string('overduemustbesubmittedby', 'quiz', userdate($duedate));

        } else if ($duedate) {
            $message = get_string('mustbesubmittedby', 'quiz', userdate($duedate));
        }

        $content = '';
        $content .= $message . $this->container($this->render($button), 'controls');
        $output .= $this->container($content, 'submitbtns mdl-align');
        $output .= '<!-- /renderer/summary_page_controls -->';

        return $output;

    }


    // ADDED functions.

    public function question_num(&$attemptobj) {
        global $DB;

        $page = optional_param('page', 0, PARAM_INT);
        if ($page < 0) {
            $page = 0;
        }

        $str = '';
        $pagenum = $attemptobj->get_num_pages();
        $str .= ' <span class="quiz-question-num">'.($page + 1).' / '.$pagenum.'</span>';

        return $str;
    }

    /**
     * Prints info about the question.
     * @param array $slots
     * @param object $attemptobj
     */
    public function question_refs($slots, $attemptobj) {
        global $DB;

        $template = new StdClass;
        $template->questionreferencestr = get_string('questionreference', 'block_quiz_behaviour');
        $template->questioncategorystr = get_string('questioncategory', 'block_quiz_behaviour');

        foreach ($slots as $slot) {
            $questionreftpl = new StdClass();
            $qa = $attemptobj->get_question_attempt($slot);
            $qcatid = $DB->get_field('question_bank_entries', 'questioncategoryid', ['id' => $qa->get_question()->id]);
            $questionreftpl->qcatname = format_string($DB->get_field('question_categories', 'name', ['id' => $qcatid]));
            $parts = explode('-', $attemptobj->get_question_name($slot));
            $questionreftpl->qname = format_string(array_shift($parts));
            $template->questionrefs[] = $questionreftpl;
        }

        return $this->output->render_from_template('block_quiz_behaviour/questionreferences', $template);
    }

    /**
     * Get a suitable attempt object.
     */
    protected static function get_attemptobj() {
        if (is_null(self::$attemptobj)) {
            $attemptid = required_param('attempt', PARAM_INT);
            $cmid = optional_param('cmid', null, PARAM_INT);
            self::$attemptobj = quiz_create_attempt_handling_errors($attemptid, $cmid);
        }
    }

    /**
     * Used by block quiz_behaviour overrides.
     */
    public function set_attemptobj($attemptobj) {
        self::$attemptobj = $attemptobj;
    }

    /**
     * prints the countdown if enabled.
     */
    public function quiz_countdown($attemptobj) {

        $str = '';
        $str .= '<div id="quiz-countdown">';
        $str .= $this->countdown_timer($attemptobj, time());
        $str .= '</div>';

        return $str;
    }

    /**
     * Prints a simple graphic progress indicator
     * @param quiz_attempt &$attemptobj the current attempt
     */
    public function quiz_progress_indicator(&$attemptobj) {

        $page = optional_param('page', 0, PARAM_INT);
        if ($page < 0) {
            $page = 0;
        }

        $pagenum = $attemptobj->get_num_pages();

        $template = new StdClass;
        $template->ratio = ($pagenum) ? ($page + 1) / $pagenum * 100 : 0;

        return $this->output->render_from_template('block_quiz_behaviour/quizprogressindicator', $template);
    }
}
