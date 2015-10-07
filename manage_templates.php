
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
        displayChangeTemplateForm($template);
    }
}



// FORM TO ADD TEMPLATE
echo "<h3>" . get_string('add_template', 'mod_predefinedlabels') . "</h3>";



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
    
    $new_availability = $data['available' . $id];
    $db_rec = $DB->get_record('predefinedlabels_templates', array('id' => $id), '*', MUST_EXIST);
    
    availabilityManager($db_rec, $new_availability);
    
    $db_rec->title = $data['title' . $id];
    $db_rec->body = $data['body' . $id]['text'];
    $db_rec->timemodified = time();
    $db_rec->userid = $USER->id;
    $db_rec->available = $new_availability;
    $DB->update_record("predefinedlabels_templates", $db_rec);
        
    rebuildCourseCache($id);
}

/**
 * Checks the old and the new availability of the template and adapts the visibility of all using mod instances
 * 
 * @param type $data A single DB-record out of 'predefinedlabels_templates', Containing the data before change
 * @param type $new_availability 1 if new visibility is set to true, else: 0
 */
function availabilityManager($data, $new_availability) {
    // invisible before, invisible after => nothing to do
    
    // invisible before, visible after
    if ($data->available == 0 && $new_availability == 1) {
        // Set the visibility of all instances that use this template to 1 (not visible)
        changeVisibilityOfAllModInstancesOfTemplate($data->id, 1);        
    }
    // Visible before, invisible after
    else if ($data->available == 1 && $new_availability == 0) {
        // Set the visibility of all instances that use this template to 0 (not visible)
        changeVisibilityOfAllModInstancesOfTemplate($data->id, 0);     
    }
    
    // Visible before, visible after => nothing to do
}

/**
 * Changes visibility of all mod instances that use the template with $id to $visibility
 * 
 * @param int $id template-ID 
 * @param int $visibility 0(for invisible) or 1 (for visible)
 */
function changeVisibilityOfAllModInstancesOfTemplate($templateid, $visibility) {
    global $DB;
    $sql = "
        UPDATE 
            {course_modules}
        SET 
            {course_modules}.visible = ".$visibility."
        WHERE
            {course_modules}.id IN 
            (
                SELECT {course_modules}.id 
                FROM
                        {course_modules},
                        {modules},
                        {predefinedlabels}
                WHERE
                        {course_modules}.instance = {predefinedlabels}.id AND
                        {predefinedlabels}.templateid = ".$templateid." AND
                        {course_modules}.module = {modules}.id AND
                        {modules}.name = 'predefinedlabels'
            )";
    $DB->execute($sql);
}

/**
 * Rebuild course cache in all courses that use the template
 * 
 * @param int $templateid
 */
function rebuildCourseCache($templateid) {
    GLOBAL $DB;
     $courses = $DB->get_records('predefinedlabels', array("templateid" => $templateid), null, 'id, course');
    
    foreach ($courses as $id => $rec) {
        rebuild_course_cache($rec->course);
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
