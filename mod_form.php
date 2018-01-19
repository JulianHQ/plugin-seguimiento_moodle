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
 * The main seguimiento configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_seguimiento
 * @copyright  2018 Jeyson Vega <jeysonvegaromero@gmail.com> - Julian Hernandez <juliher.094@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');


class mod_seguimiento_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('seguimientoname', 'seguimiento'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'seguimientoname', 'seguimiento');


        $mform->addElement('textarea', 'mensaje',get_string('seguimientomensaje', 'seguimiento'),'wrap="virtual" rows="10" cols="66"');
        $mform->addRule('mensaje', null, 'required', null, 'client');
        $mform->addHelpButton('mensaje', 'seguimientomensaje', 'seguimiento');

        $mform->addElement('text', 'minscore',get_string('minscore', 'seguimiento'),array('size' => '64'));
        $mform->addRule('minscore', null, 'required', null, 'client');
        $mform->addHelpButton('minscore', 'minscore', 'seguimiento');

        $mform->addElement('text', 'maxscore',get_string('maxscore', 'seguimiento'),array('size' => '64'));
        $mform->addRule('maxscore', null, 'required', null, 'client');
        $mform->addHelpButton('maxscore', 'maxscore', 'seguimiento');

        // Add standard grading elements.
        //$this->standard_grading_coursemodule_elements();

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }
}
