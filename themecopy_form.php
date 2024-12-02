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

class theme_copy_form extends moodleform {

    public $fileoptions;

    public function definition() {
        global $COURSE, $PAGE;

        $mform = $this->_form;

        $mform->addElement('select', 'themefrom', get_string('fromtheme', 'theme_klassplace'), $this->_customdata['variants']);

        $mform->addElement('select', 'themeto', get_string('totheme', 'theme_klassplace'), $this->_customdata['variants']);

        $mform->addElement('checkbox', 'withfiles', get_string('withfiles', 'theme_klassplace'));
        $mform->setDefault('withfiles', 1);

        $this->add_action_buttons(true, get_string('copy', 'theme_klassplace'));
    }

    function validation($data, $files = null) {

        $errors = array();

        if ($data['themefrom'] == $data['themeto']) {
            $errors['themeto'] = get_string('errorcopyonself', 'theme_klassplace');
        }

        return $errors;
    }
}

