<?php

require_once("$CFG->libdir/formslib.php");
 
class add_template_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;
 
        $mform = $this->_form; // Don't forget the underscore! 
 
        $mform->addElement('text', 'title', get_string('title', 'mod_predefinedlabels')); // Add elements to your form
        $mform->setType('title', PARAM_TEXT);
        $mform->addElement('editor', 'body', get_string('content'));
        $mform->addElement('selectyesno', 'available', get_string('available', 'mod_predefinedlabels'));
        
        $mform->addElement('submit', 'submitbutton', get_string('savechanges'));
    //Custom validation should be added here
    }
    function validation($data, $files) {
        return array();
    }
}