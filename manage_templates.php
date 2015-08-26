<?php

require_once '../../config.php';
require_once __DIR__ . '/add_template_form.php';


global $CFG, $PAGE, $OUTPUT, $DB, $USER;
require_once($CFG->libdir . '/adminlib.php');
admin_externalpage_setup('modpredefinedlabels_managetemplates', '', null, '/mod/predefinedtemplates/manage_templates.php');

// Process changes or deletions
if (!empty($_REQUEST)) {
    if (hasKeyLike($_REQUEST, 'change')) {
        changeTemplate($_REQUEST);
    }

    if (hasKeyLike($_REQUEST, 'delete')) {
        deleteTemplate($_REQUEST);
    }
}

// Process new template
$mform = new add_template_form();
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
} else if ($formdata = $mform->get_data()) {
    $data = array();
    $data["title"] = $formdata->title;
    $data["body"] = $formdata->body['text'];
    $data["timecreated"] = time();
    $data["timemodified"] = time();
    $data["userid"] = (int) $USER->id;
    $data["available"] = (int) $formdata->available;
    $id = $DB->insert_record_raw('predefinedlabels_templates', $data, true);
    redirect($_SERVER['REQUEST_URI']);
}







// Set up the page.
$title = get_string('manage_templates', 'mod_predefinedlabels');
$pagetitle = $title;
$url = new moodle_url("/mod/predefinedlabels/manage_templates.php");
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);


echo $OUTPUT->header();
echo "<h2>" . get_string('templates', 'mod_predefinedlabels') . "</h2>";



$templates = $DB->get_records('predefinedlabels_templates');

if (count($templates) == 0) {
    echo get_string('no_templates', 'mod_predefinedlabels');
} else {
    // DISPLAY EXISTING TEMPLATES
    //printArray($templates);
    foreach ($templates as $template) {
        echo "<hr />";
        displayChangeTemplateForm($template);
    }
}


echo "<hr />";
echo "<h1>" . get_string('add_template', 'mod_predefinedlabels') . "</h1>";
echo "<hr />";
// FORM TO ADD TEMPLATE



//Form processing and displaying is done here

$mform->display();
echo $OUTPUT->footer();

function printArray($data) {
    echo "<pre>" . print_r($data, true) . "</pre>";
}

function displayChangeTemplateForm($template) {
    $id = $template->id;
    $form = new MoodleQuickForm($id, "POST", $_SERVER['REQUEST_URI'], "_self", array("id" => "template" . $template->id));

    $form->addElement('text', 'title' . $id, get_string('title', 'mod_predefinedlabels'))
            ->setValue($template->title); // Add elements to your form
    //$form->setDefault('title'.$id, $template->title); // Add elements to your form
    $form->setType('title' . $id, PARAM_TEXT);

    $form->addElement('editor', 'body' . $id, get_string('content'))
            ->setValue(array('text' => $template->body));
    $form->addElement('selectyesno', 'available'.$id, get_string('available', 'mod_predefinedlabels'))
            ->setValue($template->available);
    $form->addElement('static', 'timecreated' . $id, get_string('timecreated', 'mod_predefinedlabels'), userdate($template->timecreated));
    $form->addElement('static', 'timemodified' . $id, get_string('lastmodified'), userdate($template->timemodified));
    $user = core_user::get_user($template->userid);
    $fullname = $user->firstname . " " . $user->lastname;
    $form->addElement('static', 'user' . $id, get_string('user'), $fullname);

    $buttongroup = array();
    $buttongroup[] = & $form->createElement('submit', 'change' . $id, get_string('savechanges'));
    $buttongroup[] = & $form->createElement('submit', 'delete' . $id, get_string("delete"));
    $form->addGroup($buttongroup, 'buttongroup' . $id, get_string('availablefromdate', 'data'), ' ', false);

    $form->display();
}

function changeTemplate($data) {
    $id = getIDbyREQUESTData($data);
    global $USER, $DB;
    $updatedData = new stdClass();
    $updatedData->id = $id;
    $updatedData->title = $data['title' . $id];
    $updatedData->body = $data['body' . $id]['text'];
    $updatedData->timemodified = time();
    $updatedData->userid = $USER->id;
    $updatedData->available = $data['available' . $id];
    $DB->update_record("predefinedlabels_templates", $updatedData);
    
    rebuildCourseCache($id);
}

/**
 * Rebuild course cache in all courses that use the template
 * 
 * @param int $templateid
 */
function rebuildCourseCache($templateid) {
    GLOBAL $DB;
     $courses = $DB->get_records('predefinedlabels', array("templateid" => $templateid), null, 'course');
    
    foreach ($courses as $courseid => $course) {
        rebuild_course_cache($courseid);
    }
}

function deleteTemplate($data) {
    $id = getIDbyREQUESTData($data);
    global $DB;
    $DB->delete_records("predefinedlabels_templates", array("id" => $id));
    
    rebuildCourseCache($id);
}

function hasKeyLike($array, $pattern) {
    $grep = preg_grep("/" . $pattern . "/i", array_keys($array));
    if (count($grep) > 0) {
        return true;
    } else {
        return false;
    }
}

function getIDbyREQUESTData($request) {
    $title = preg_grep("/title/i", array_keys($request));
    $title = array_shift($title);
    return (int) str_replace("title", "", $title);
}
