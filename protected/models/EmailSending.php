<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class EmailSending {

    public function sendHtml($subject, $email) {


	$message = new YiiMailMessage;
	$message->view = 'contact';

	$model = new ContactForm;

	$message->setBody(array('model' => $model), 'text/html');

	$message->subject = $subject;
	$message->addTo($email);
	$message->from = Yii::app()->params['adminEmail'];
	Yii::app()->mail->send($message);
    }

    public function emailDailyReport($user_id, $selectedGsn, $selectedSensor, $selectedDate, $email, &$message) {

	exec('xvfb-run -a -s "-screen 0 640x480x16" wkhtmltopdf --dpi 200 --redirect-delay 2000 --page-size A4 "http://161.53.67.224/lion/index.php/webService/dailyReportGenerating?gsn_list=' . $selectedGsn . '&sensor_list=' . $selectedSensor . '&date_list=' . $selectedDate . '" "/home/lpostruzin/cron_new/reports/' . "Daily_report_" . $selectedGsn . "_GSN_" . $selectedSensor . $selectedDate . '.pdf"');

	$user = ProdUsers::model()->findByPk($user_id);

	if (!empty($user)) {
	    $command = 'mutt -s "Daily report for ' . $user['first_name'] . ' ' . $user['last_name'] . ', ' . $selectedDate . '" ' . $email . ' -a ' . '"/home/lpostruzin/cron_new/reports/' . 'Daily_report_' . $selectedGsn . '_GSN_' . $selectedSensor . $selectedDate . '.pdf"';

	    try {
		exec($command);
		exec('rm "/home/lpostruzin/cron_new/reports/' . "Daily_report_" . $selectedGsn . "_GSN_" . $selectedSensor . $selectedDate . '.pdf"');
		return true;
	    } catch (Exception $ae) {
		$message = "<b>ERROR: </b>Problem occured while sending email. Please contact our administrator for support.<br/>Error message: " . $ae->getMessage() . "\r\n";
		return false;
	    }
	} else {
	    $message = "<b>ERROR: </b>Unable to identify you as a user!";
	    return false;
	}
    }

    public function emailMonthlyReport($user_id, $selectedGsn, $selectedSensor, $selectedYear, $email, &$message) {

	try {
	    exec('xvfb-run -a -s "-screen 0 640x480x16" wkhtmltopdf --dpi 200 --redirect-delay 2000 --page-size A4 "http://161.53.67.224/lion/index.php/webService/monthlyReportGenerating?gsn_list=' . $selectedGsn . '&sensor_list=' . $selectedSensor . '&year_list=' . $selectedYear . '" "/home/lpostruzin/cron_new/reports/' . "Monthly_report_" . $selectedGsn . "_GSN_" . $selectedSensor . $selectedYear . '.pdf"');

	    $user = ProdUsers::model()->findByPk($user_id);

	    if (!empty($user)) {
		$command = 'mutt -s "Monthly report for ' . $user['first_name'] . ' ' . $user['last_name'] . ', ' . $selectedYear . '" ' . $email . ' -a ' . '"/home/lpostruzin/cron_new/reports/' . 'Monthly_report_' . $selectedGsn . '_GSN_' . $selectedSensor . $selectedYear . '.pdf"';

		try {
		    exec($command);
		    exec('rm "/home/lpostruzin/cron_new/reports/' . "Monthly_report_" . $selectedGsn . "_GSN_" . $selectedSensor . $selectedYear . '.pdf"');
		    return true;
		} catch (Exception $ae) {
		    $message = "<b>ERROR: </b>Problem occured while sending email. Please contact our administrator for support.<br/>Error message: " . $ae->getMessage() . "\r\n";
		    return false;
		}
	    } else {
		$message = "<b>ERROR: </b>Unable to identify you as a user!";
		return false;
	    }
	} catch (Exception $ae) {
	    $message = "<b>Unexpected error occured</b>: " . $ae->getMessage();
	    return false;
	}
    }

}

?>
