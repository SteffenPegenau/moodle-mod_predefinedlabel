<?php

require_once '../../config.php';
require_once __DIR__ . '/add_template_form.php';
global $CFG, $PAGE, $OUTPUT, $DB, $USER;

require_once($CFG->libdir.'/adminlib.php');
admin_externalpage_setup('modpredefinedlabels_managetemplates', '', null, '/mod/predefinedtemplates/manage_templates.php');

// Set up the page.
$title = get_string('manage_templates', 'mod_predefinedlabels');
$pagetitle = $title;
$url = new moodle_url("/mod/predefinedlabels/manage_templates.php");
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);


echo $OUTPUT->header();
echo "<h2>".get_string('templates', 'mod_predefinedlabels')."</h2>";



$templates = $DB->get_records('predefinedlabels_templates');

if(count($templates) == 0) {
    echo get_string('no_templates', 'mod_predefinedlabels');
} else{
    // DISPLAY EXISTING TEMPLATES
    //printArray($templates);
    foreach($templates as $template) {
        printArray($template);
        $form = new add_template_form();
        $form->set_data($template);
        $form->display();
    }
}



// FORM TO ADD TEMPLATE
echo "<h3>".get_string('add_template', 'mod_predefinedlabels')."</h3>";


$mform = new add_template_form();
//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
} else if ($formdata = $mform->get_data()) {
    $data = array();
    $data[title] = $formdata->title;
    $data[body] = $formdata->body['text'];
    $data[timecreated] = time();
    $data[timemodified] = time();
    $data[userid] = (int) $USER->id;
    $data[available] = (int) $formdata->available;
    printArray($data);
    $id = $DB->insert_record_raw('predefinedlabels_templates', $data, true);
    printArray($id);
} else {
  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
  // or on the first display of the form.
  //displays the form
  $mform->display();
}

echo $OUTPUT->footer();

function printArray($data) {
    echo "<pre>" . print_r($data, true) . "</pre>";
}
