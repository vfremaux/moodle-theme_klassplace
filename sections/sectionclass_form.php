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
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class sectionclass_form extends moodleform {

    public function definition() {

        $mform = $this->_form;

        $mform->addElement('hidden', 'overridestyle');
        $mform->setType('overridestyle', PARAM_TEXT);

        $label = get_string('sectionstyleoverride', 'theme_klassplace');
        $mform->addElement('header', 'styleoverridehdr', $label);

        $group = array();
        $group[] = &$mform->createElement('radio', 'applyto', '', ' '.get_string('applytothiselementonly', 'theme_klassplace'), OVERRIDE_APPLIES_TO_SINGLE);
        if ($this->_customdata['course']->format != 'flexsections') {
            $group[] = &$mform->createElement('radio', 'applyto', '', ' '.get_string('applytoallsections', 'theme_klassplace'), OVERRIDE_APPLIES_TO_SIBLINGS);
        } else {
            $group[] = &$mform->createElement('radio', 'applyto', '', ' '.get_string('applytoallsiblings', 'theme_klassplace'), OVERRIDE_APPLIES_TO_SIBLINGS);
            $group[] = &$mform->createElement('radio', 'applyto', '', ' '.get_string('applytowholesubtree', 'theme_klassplace'), OVERRIDE_APPLIES_TO_SUBTREE);
        }
        $mform->addGroup($group, 'applytogroup', get_string('applyto', 'theme_klassplace'), array(' '), false);

        $attrs = array('data-value' => 'none',
                       'name' => 'overridestyle_default');
        if (empty($this->_customdata['current'])) {
            $attrs['class'] = 'btn apply-section-style currentchoice';
        } else {
            $attrs['class'] = 'btn apply-section-style';
        }
        $btnlabel = get_string('nostyleoverride', 'theme_klassplace', '');
        $btn = html_writer::tag('button', $btnlabel, $attrs)."<br/><br/>";
        $mform->addElement('html', $btn);
        // $mform->addElement('button', 'styleoverride_none', $btnlabel, $attrs);

        if (!empty($this->_customdata['styles']['labels'])) {
            foreach ($this->_customdata['styles']['labels'] as $name => $label) {

                $btncurrentclass = ($name == $this->_customdata['current']) ? 'btn currentchoice' : 'btn';
                $currentclass = ($name == $this->_customdata['current']) ? 'currentchoice' : '';

                $attrs = array('data-value' => $name,
                               'name' => 'overridestyle_'.$name);
                if (!empty($this->_customdata['current']) && ($this->_customdata['current'] == $name)) {
                    $attrs['class'] = 'btn apply-section-style currentchoice';
                } else {
                    $attrs['class'] = 'btn apply-section-style';
                }
                $btnlabel = get_string('activatestyle', 'theme_klassplace').' > '.$label;
                $btn = html_writer::tag('button', $btnlabel, $attrs);
                $mform->addElement('html', $btn);

                $attrs = array('class' => 'sectionname');

                if (preg_match('/^\\{(.*)\\}/', $this->_customdata['styles']['configs'][$name], $matches)) {
                    // True style.
                    $attrs['style'] = $matches[1];
                } else {
                    $attrs['class'] .= ' '.$this->_customdata['styles']['configs'][$name].' '.$currentclass;
                }
                $sectionsample = '<span class="sample-label">'.get_string('sample', 'theme_klassplace').'</span><br/>';
                $sectionsample .= html_writer::tag('h3', get_string('section'), $attrs);
                $html = '<div class="sectionstyle-sample-wrapper">
                    <div class="sectionstyle-sample '.$currentclass.'">'.$sectionsample.'</div>
                </div>';
                $mform->addElement('static', 'stylesample', '', $html);

            }
        }

        $mform->addElement('cancel', get_string('cancel'));
    }
}