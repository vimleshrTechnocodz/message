<?php
/**
* @package local_message
* @author vimlesh
* @license https://webii.in
**/

require_once(__DIR__.'/../../config.php');
require_once($CFG->dirroot.'/local/message/classes/form/edit.php');

global $DB;

$PAGE->set_url(new moodle_url('/local/message/edit.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Edit');

//We want to display our form.
$mform = new edit();


if ($mform->is_cancelled()) {
    //Go back to manage page
    redirect($CFG->wwwroot.'/local/message/manage.php','You cancelled the message form');
} else if ($fromform = $mform->get_data()) {
	//Insert data into database table.

	$recordtoinsert = new stdClass();
	$recordtoinsert->messagetext = $fromform->messagetext;
	$recordtoinsert->messagetype = $fromform->messagetype;

	$DB->insert_record('local_message', $recordtoinsert);

	redirect($CFG->wwwroot.'/local/message/manage.php','You created a message with title '.$fromform->messagetext);
} 

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();