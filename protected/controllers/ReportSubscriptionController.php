<?php

class ReportSubscriptionController extends Controller {

    public $layout = 'main_after_login';

    function check_email_address($email) {
	// First, we check that there's one @ symbol,
	// and that the lengths are right.
	if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
	    // Email invalid because wrong number of characters
	    // in one section or wrong number of @ symbols.
	    return false;
	}
	// Split it into sections to make life easier
	$email_array = explode("@", $email);
	$local_array = explode(".", $email_array[0]);
	for ($i = 0; $i < sizeof($local_array); $i++) {
	    if
	    (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&
↪'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$",
			    $local_array[$i])) {
		return false;
	    }
	}
	// Check if domain is IP. If not,
	// it should be valid domain name
	if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
	    $domain_array = explode(".", $email_array[1]);
	    if (sizeof($domain_array) < 2) {
		return false; // Not enough parts to domain
	    }
	    for ($i = 0; $i < sizeof($domain_array); $i++) {
		if
		(!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|
↪([A-Za-z0-9]+))$",
				$domain_array[$i])) {
		    return false;
		}
	    }
	}
	return true;
    }

    /**
     * Actions for report generation site for sensors
     */
    public function actionReportsDailyDynamicSensors() {
	$data = DiSensors::model()->findAll('gsn_id=:parent_id',
			array(':parent_id' => (int) $_POST['UserReportsDaily']['gsn_list']));

	$data = CHtml::listData($data, 'sensor_id', 'sensor_user_name');
	echo CHtml::tag('option', array('value' => 0), 'Select', true);
	foreach ($data as $id => $value) {
	    echo CHtml::tag('option', array('value' => $id), CHtml::encode($value), true);
	}
    }

    /**
     * Actions for daily report generation site for dates
     */
    public function actionReportsDailyDynamicDates() {
//        $data = AggDay::model()->findAll('sensor_id=:parent_id',
//                        array(':parent_id' => (int) $_POST['UserReportsMain']['sensor_list']));

	$data = Yii::app()->db->createCommand()
			->selectDistinct('a.sensor_id, a.date_id, d.date')
			->from('agg_day a')
			->join('di_days d', 'a.date_id=d.date_id')
			->where('a.sensor_id=:id', array(':id' => (int) $_POST['UserReportsDaily']['sensor_list']))
			->order('a.date_id desc')
			->queryAll();

	//$data = CHtml::listData($data, 'date_id', 'date_id');

	$data = CHtml::listData($data, 'date_id', 'date');

	echo CHtml::tag('option', array('value' => 0), 'Select', true);
	foreach ($data as $id => $value) {
	    echo CHtml::tag('option', array('value' => $id), CHtml::encode($value), true);
	}
    }

    /**
     * Actions for monthly report generation site for sensors
     */
    public function actionReportsMonthlyDynamicSensors() {
	$data = DiSensors::model()->findAll('gsn_id=:parent_id',
			array(':parent_id' => (int) $_POST['UserReportsMonthly']['gsn_list']));


	$data = CHtml::listData($data, 'sensor_id', 'sensor_user_name');
	echo CHtml::tag('option', array('value' => 0), 'Select', true);
	foreach ($data as $id => $value) {
	    echo CHtml::tag('option', array('value' => $id), CHtml::encode($value), true);
	}
    }

    /**
     * Actions for report generation site for dates
     */
    public function actionReportsMonthlyDynamicYears() {
	$data = Yii::app()->db->createCommand()
			->selectDistinct('a.sensor_id,  a.month_id')
			->from('agg_month_day_part a')
			//->join('di_days d', 'a.date_id=d.date_id')
			->where('a.sensor_id=:id', array(':id' => (int) $_POST['UserReportsMonthly']['sensor_list']))
			->order('a.month_id desc')
			->queryAll();

	$data = CHtml::listData($data, 'month_id', 'month_id');

	echo CHtml::tag('option', array('value' => 0), 'Select', true);
	foreach ($data as $id => $value) {
	    echo CHtml::tag('option', array('value' => $id), CHtml::encode($value), true);
	}
    }

    public function actionReportsMonthlyDynamicMonths() {
	$data = Yii::app()->db->createCommand()
			->selectDistinct('a.sensor_id, a.date_id, d.month, d.name_month')
			->from('agg_month_day_part a')
			->join('di_days d', 'a.month=d.month and a.year = d.year')
			->where('a.sensor_id=:id and d.year=:year', array(':id' => $model->selectedSensor, ':year' => (int) $_POST['UserReportsMonthly']['year_list']))
			//->where('d.year=:year',array(':year'=>(int)$_POST['UserReportsMonthly']['year_list']))
			->order('d.month asc')
			->queryAll();

	$data = CHtml::listData($data, 'month', 'name_month');

	echo CHtml::tag('option', array('value' => 0), 'Select', true);
	foreach ($data as $id => $value) {
	    echo CHtml::tag('option', array('value' => $id), CHtml::encode($value), true);
	}
    }

    /**
     * Displays daily reports generating page
     */
    public function actionUserReportsDaily() {
	if (Yii::app()->user->isGuest){
	    $this->redirect(Yii::app()->homeUrl);
	}

	$model = new UserReportsDaily;

	$model->selectedGsn = (int) (isset($_POST['UserReportsDaily']['gsn_list']) ? ($_POST['UserReportsDaily']['gsn_list']) : "-1");
	$model->selectedSensor = (int) (isset($_POST['UserReportsDaily']['sensor_list']) ? ($_POST['UserReportsDaily']['sensor_list']) : "-1");
	$model->selectedDate = (int) (isset($_POST['UserReportsDaily']['date_list']) ? ($_POST['UserReportsDaily']['date_list']) : "-1");

	$model->submitedForm = (isset($_POST['submit_button']) ? true : false);
	$model->email_report = (isset($_POST['email_sending']) ? true : false);

	if ($model->email_report && $model->selectedGsn != -1 && $model->selectedSensor != -1 && $model->selectedDate != -1) {
	    $model->submitedForm = false;
	    if (isset($_POST['UserReportsDaily']['email']) && $model->email_report) {
		if ($this->check_email_address($_POST['UserReportsDaily']['email'])) {

		    $email_sending = new EmailSending;

		    if ($email_sending->emailDailyReport(Yii::app()->user->id, $model->selectedGsn, $model->selectedSensor, $model->selectedDate, $_POST['UserReportsDaily']['email'], $message)) {
			$model->message = "Your email has been sent to " . $_POST['UserReportsDaily']['email'] . "!";
		    }
		    else
			$model->message = $message;
		}
		else {
		    $model->message = "<b>ERROR: </b>You requested an email delivery without valid email input. Please provide your email first, and then we will send you your report!";
		}
	    } else {
		$model->message = "<b>ERROR: </b>You requested an email delivery without valid email input. Please provide your email first, and then we will send you your report!";
	    }
	}

	$this->render('userReportsDaily', array('model' => $model));
    }

    /**
     * Displays daily reports generating page
     */
    public function actionUserReportsMonthly() {
	if (Yii::app()->user->isGuest){
	    $this->redirect(Yii::app()->homeUrl);
	}
	
	$model = new UserReportsMonthly;

	$model->selectedGsn = (int) (isset($_POST['UserReportsMonthly']['gsn_list']) ? ($_POST['UserReportsMonthly']['gsn_list']) : "-1");
	$model->selectedSensor = (int) (isset($_POST['UserReportsMonthly']['sensor_list']) ? ($_POST['UserReportsMonthly']['sensor_list']) : "-1");
	$model->selectedYear = (int) (isset($_POST['UserReportsMonthly']['year_list']) ? ($_POST['UserReportsMonthly']['year_list']) : "-1");

	$model->submitedForm = (isset($_POST['submit_button']) ? true : false);
	$model->email_report = (isset($_POST['email_sending']) ? true : false);

	if ($model->email_report && $model->selectedGsn != -1 && $model->selectedSensor != -1 && $model->selectedYear != -1) {
	    $model->submitedForm = false;

	    if (isset($_POST['UserReportsMonthly']['email'])) {
		
		if ($this->check_email_address($_POST['UserReportsMonthly']['email'])) {
		    $email_sending = new EmailSending;

		    if ($email_sending->emailMonthlyReport(Yii::app()->user->id, $model->selectedGsn, $model->selectedSensor, $model->selectedYear, $_POST['UserReportsMonthly']['email'], $message)) {
			$model->message = "Your email has been sent to " . $_POST['UserReportsMonthly']['email'] . "!";
		    }
		    else
			$model->message = $message;
		}
		else {
		    $model->message = "<b>ERROR: </b>You requested an email delivery without valid email input. Please provide your email first, and then we will send you your report!";
		}

	    } else {
		$model->message = "<b>ERROR: </b>You requested an email delivery without valid email input. Please provide your email first, and then we will send you your report!";
	    }
	}

	$this->render('userReportsMonthly', array('model' => $model));
    }

    public function actionUserReportsMain() {
	if (Yii::app()->user->isGuest){
	    $this->redirect(Yii::app()->homeUrl);
	}

	$model = new UserReportsMain;

	$this->render('userReportsMain', array('model' => $model));
    }

    /**
     * Actions for generating new report subscription
     */
    public function actionReportsDynamicSensors() {
	$data = DiSensors::model()->findAll('gsn_id=:parent_id',
			array(':parent_id' => (int) $_POST['UserReportsNewSubscription']['gsn_id']));


	$data = CHtml::listData($data, 'sensor_id', 'sensor_user_name');
	echo CHtml::tag('option', array('value' => 0), 'Select', true);
	foreach ($data as $id => $value) {
	    echo CHtml::tag('option', array('value' => $id), CHtml::encode($value), true);
	}
    }

    public function actionUserReportsNewSubscription() {
	if (Yii::app()->user->isGuest){
	    $this->redirect(Yii::app()->homeUrl);
	}

	$model = new UserReportsNewSubscription;

	$model->selectedGsn = (int) (isset($_POST['UserReportsNewSubscription']['gsn_id']) ? ($_POST['UserReportsNewSubscription']['gsn_id']) : "-1");
	$model->selectedSensor = (int) (isset($_POST['UserReportsNewSubscription']['sensor_id']) ? ($_POST['UserReportsNewSubscription']['sensor_id']) : "-1");
	$model->selectedEmail = (isset($_POST['UserReportsNewSubscription']['email']) ? ($_POST['UserReportsNewSubscription']['email']) : "-1");
	$model->selectedType = (int) (isset($_POST['UserReportsNewSubscription']['report_type']) ? ($_POST['UserReportsNewSubscription']['report_type']) : "-1");

	$model->successful = true;
	$model->newSubscription = false;

	if (isset($_POST['submit_button'])) {
	    $text = "";
	    //user has requested new subscription
	    if ($model->selectedGsn != -1 && $model->selectedSensor != -1 && $model->selectedEmail != -1 && $model->selectedType != -1) {
		$model->newSubscription = true;
		if ($model->selectedType == 1) {
		    $rawData = Yii::app()->db->createCommand('SELECT * FROM daily_reports d WHERE gsn_id = ' . $model->selectedGsn . ' and sensor_id = ' . $model->selectedSensor . ' and email = \'' . $model->selectedEmail . '\'')->queryAll();

		    if ($rawData != null)
			$model->successful = false;
		    else
			$model->successful = true;
		}
		else {
		    $rawData = Yii::app()->db->createCommand('SELECT * FROM monthly_reports d WHERE gsn_id = ' . $model->selectedGsn . ' and sensor_id = ' . $model->selectedSensor . ' and email = \'' . $model->selectedEmail . '\'')->queryAll();

		    if ($rawData != null)
			$model->successful = false;
		    else
			$model->successful = true;
		}

		if ($model->successful) {
		    try {
			if ($model->selectedType == 1)
			    $report = new DailyReports();
			else if ($model->selectedType == 2)
			    $report = new MonthlyReports ();
			else {
			    $text = "Type you have chosen does not exist, please try again";
			}
			$report->gsn_id = $model->selectedGsn;
			$report->sensor_id = $model->selectedSensor;
			$report->email = $model->selectedEmail;
			$report->user_id = Yii::app()->user->id;
			$report->is_sending = '1';
			$report->save();
		    } catch (Exception $ae) {

		    }
		}
	    }
	}

	$this->render('userReportsNewSubscription', array('model' => $model));
    }

    public function actionAutomaticReportSubscription() {
	if (Yii::app()->user->isGuest){
	    $this->redirect(Yii::app()->homeUrl);
	}

	$model = new UserReportsNewSubscription;

	$model->selectedGsn = (int) (isset($_POST['gsn_id']) ? ($_POST['gsn_id']) : "-1");
	$model->selectedSensor = (int) (isset($_POST['sensor_id']) ? ($_POST['sensor_id']) : "-1");
	$model->selectedEmail = (isset($_POST['email']) ? ($_POST['email']) : "-1");
	$model->selectedType = (int) (isset($_POST['type']) ? ($_POST['type']) : "-1");

	$model->successful = true;
	$model->newSubscription = false;

	try {
	    if (isset($_POST['automatic'])) {
		//user has requested new subscription
		if ($model->selectedGsn != -1 && $model->selectedSensor != -1 && $model->selectedEmail != -1 && $model->selectedType != -1) {
		    $model->newSubscription = true;
		    if ($model->selectedType == 1) {
			$rawData = Yii::app()->db->createCommand('SELECT * FROM daily_reports d WHERE gsn_id = ' . $model->selectedGsn . ' and sensor_id = ' . $model->selectedSensor . ' and email = \'' . $model->selectedEmail . '\'')->queryAll();

			if ($rawData != null)
			    $model->successful = false;
			else
			    $model->successful = true;
		    }
		    else {
			$rawData = Yii::app()->db->createCommand('SELECT * FROM monthly_reports d WHERE gsn_id = ' . $model->selectedGsn . ' and sensor_id = ' . $model->selectedSensor . ' and email = \'' . $model->selectedEmail . '\'')->queryAll();

			if ($rawData != null)
			    $model->successful = false;
			else
			    $model->successful = true;
		    }

		    if ($model->successful) {
			try {
			    if ($model->selectedType == 1)
				$report = new DailyReports();
			    else if ($model->selectedType == 2)
				$report = new MonthlyReports ();
			    else {
				$model->message = "Type you have chosen does not exist, please try again";
			    }

			    if (!empty($report)) {
				$report->gsn_id = $model->selectedGsn;
				$report->sensor_id = $model->selectedSensor;
				$report->email = $model->selectedEmail;
				$report->user_id = Yii::app()->user->id;
				$report->is_sending = '1';
				if (!$report->save()) {
				    //$model = new UserReportsNewSubscription;
				    $model->message = "<b>MESSAGE: </b>Error occured while saving into database, please try again!";
				    $this->render('userReportsNewSubscription', array('model' => $model));
				} else {
				    if ($model->selectedType == 1) {
					$model_view = new UserReportsDaily;
					$model_view->message = '<b>MESSAGE: </b>Your request has been saved correctly. After administrator approves it, you will be able to see it in action!<br/>';

					$model_view->selectedGsn = $model->selectedGsn;
					$model_view->selectedSensor = $model->selectedSensor;
					$model_view->selectedDate = (isset($_GET['selected_date']) ? $_GET['selected_date'] : date("Ymd", strtotime("yesterday")));
					$model_view->submitedForm = true;

					//$model->pdfGeneration();

					$this->render('userReportsDaily', array('model' => $model_view));
				    } else if ($model->selectedType == 2) {
					$model_view = new UserReportsMonthly;
					$model_view->message = '<b>MESSAGE: </b>Your request has been saved correctly. After administrator approves it, you will be able to see it in action!<br/>';

					$model_view->selectedGsn = $model->selectedGsn;
					$model_view->selectedSensor = $model->selectedSensor;
					$model_view->selectedYear = (isset($_GET['selected_month']) ? $_GET['selected_month'] : date("Ym"));
					//$model_view->selectedDate = (isset($_GET['selected_date']) ? $_GET['selected_date'] : date("Ymd", strtotime("yesterday")));
					$model_view->submitedForm = true;

					//$model->pdfGeneration();

					$this->render('userReportsMonthly', array('model' => $model_view));
				    }
				}
			    } else {
				$model = new UserReportsNewSubscription;
				$model->message = "<b>MESSAGE: </b>Type you have chosen does not exist, please try again!";
				$this->render('userReportsNewSubscription', array('model' => $model));
			    }
			} catch (Exception $ae) {
			    $model = new UserReportsNewSubscription;
			    $model->message = "<b>MESSAGE: </b>Error occured while saving into database, please try again!";
			    $this->render('userReportsNewSubscription', array('model' => $model));
			}
		    } else {
			//$model = new UserReportsNewSubscription;
			$model->message = "<b>MESSAGE: </b>Some information you have provided was already found in the database, so please try again!";
			$this->render('userReportsNewSubscription', array('model' => $model));
		    }
		} else {
		    //$model = new UserReportsNewSubscription;
		    $model->message = "<b>MESSAGE: </b>Some information you have provided was not valid, so please try again!";
		    $this->render('userReportsNewSubscription', array('model' => $model));
		}
	    } else {

		if ($model->selectedType == 1) {
		    $model_view = new UserReportsDaily;
		    $model_view->message = '<b>MESSAGE: </b>Your request was invalid, please try again!<br/>';

		    $model_view->selectedGsn = $model->selectedGsn;
		    $model_view->selectedSensor = $model->selectedSensor;
		    $model_view->selectedDate = (isset($_GET['selected_date']) ? $_GET['selected_date'] : date("Ymd", strtotime("yesterday")));
		    $model_view->submitedForm = true;

		    //$model->pdfGeneration();

		    $this->render('userReportsDaily', array('model' => $model_view));
		} else if ($model->selectedType == 2) {
		    $model_view = new UserReportsMonthly;
		    $model_view->message = '<b>MESSAGE: </b>Your request was invalid, please try again!<br/>';

		    $model_view->selectedGsn = $model->selectedGsn;
		    $model_view->selectedSensor = $model->selectedSensor;
		    $model_view->selectedYear = (isset($_GET['selected_month']) ? $_GET['selected_month'] : date("Ym"));
		    //$model_view->selectedDate = (isset($_GET['selected_date']) ? $_GET['selected_date'] : date("Ymd", strtotime("yesterday")));
		    $model_view->submitedForm = true;

		    //$model->pdfGeneration();

		    $this->render('userReportsMonthly', array('model' => $model_view));
		} else {
		    //$model = new UserReportsNewSubscription;
		    $model->message = "<b>MESSAGE: </b>Unknown report subscription type!";
		    $this->render('userReportsNewSubscription', array('model' => $model));
		}
	    }
	} catch (Exception $ae) {
	    $model->message = "<b>MESSAGE: </b>Enexpected problem occured, " . $ae->getMessage() . "!<br/>Please contact our administrator for further support!";
	    $this->render('userReportsNewSubscription', array('model' => $model));
	}
    }

    public function actionUserReportsSubscription() {
	if (Yii::app()->user->isGuest){
	    $this->redirect(Yii::app()->homeUrl);
	}

	$model = new UserReportsSubscription;

	if (isset($_REQUEST['report_id'])) {
	    try {
		if ($_REQUEST['type_int'] == 1) { //daily report
		    $report = new DailyReports;
		    $report = DailyReports::model()->findByAttributes(array('report_id' => $_REQUEST['report_id']));
		} else if ($_REQUEST['type_int'] == 2) {//monthly report
		    $report = new MonthlyReports;
		    $report = MonthlyReports::model()->findByAttributes(array('report_id' => $_REQUEST['report_id']));
		}

		if (isset($_REQUEST['action'])) {
		    if ($report->delete())
			$text = "Delete of report was successful!";
		    else
			$text = "Delete of report was unseccessful!";
		}
		else {
		    if ($report->is_sending == '1')
			$report->is_sending = '0';
		    else
			$report->is_sending = '1';
		    $report->save();
		}
	    } catch (Exception $e) {
		$message = "We were unable to find your report by its id or proceed with your request!";
	    }
	}

	$this->render('userReportsSubscription', array('model' => $model));
    }

}

?>
