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
 * @package theme_essential_barchen
 * @category theme
 * @author valery fremaux (valery.fremaux@gmail.com)
 * @copyright 2008 Valery Fremaux (Edunao.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Page reorganisation service
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class mod_thumb_form extends moodleform {

    public $fileoptions;

    public function definition() {
        global $COURSE, $PAGE;

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $this->fileoptions = array('maxfiles' => 1,
                                   'maxbytes' => $COURSE->maxbytes,
                                   'accepted_types' => array('.jpg', '.gif', '.png', '.svg'));

        $mform->addElement('filepicker', 'thumb', get_string('modthumb', 'theme_'.$PAGE->theme->name), null, $this->fileoptions);

        $mform->addElement('checkbox', 'cleanthumb', '', get_string('cleanthumb', 'theme_'.$PAGE->theme->name));

        $this->add_action_buttons();
    }

    public function set_data($defaults) {

        $context = context_module::instance($this->_customdata['modid']);

        $draftitemid = file_get_submitted_draft_itemid('thumb');
        file_prepare_draft_area($draftitemid, $context->id, 'mod_'.$this->_customdata['modname'], 'modthumb', @$defaults->id,
                                $this->fileoptions);
        $defaults->thumb = $draftitemid;

        parent::set_data($defaults);
    }

}

