<?php
/**
* @package local_message
* @author vimlesh
* @license https://webii.in
**/

require_once(__DIR__.'/../../config.php');

global $DB, $USER;

$PAGE->set_url(new moodle_url('/local/message/manage.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Manage Message');


$sql = "SELECT lm.id,lm.messagetext,lm.messagetype 
		FROM {local_message}  lm
		LEFT OUTER JOIN {local_message_read} lmr ON lm.id=lmr.messageid
		WHERE lmr.userid <> :userid 
		OR lmr.userid IS NULL;";
$params=[
	'userid'=> $USER->id,
];
$messages=$DB->get_records_sql($sql,$params);


echo $OUTPUT->header();

foreach ($messages as $key => $message) {
	$type=\core\output\notification::NOTIFY_INFO;
	if($message->messagetype==='0')
		$type=\core\output\notification::NOTIFY_WARNING;
	if($message->messagetype==='1')
		$type=\core\output\notification::NOTIFY_SUCCESS;
	if($message->messagetype==='2')
		$type=\core\output\notification::NOTIFY_ERROR;

	\core\notification::add($message->messagetext,$type);	

	$readrecords = new stdClass();
	$readrecords->messageid = $message->id;
	$readrecords->userid = 	$USER->id;
	$readrecords->timeread = time();

	$DB->insert_record('local_message_read',$readrecords);
}


$templatecontext=(object)[
	'texttodisplay' => 'List of all current messages',
	'messages' => array_values($messages) ,
	'editurl' => new moodle_url('/local/message/edit.php')
];
echo $OUTPUT->render_from_template('local_message/manage',$templatecontext);

echo $OUTPUT->footer();
