<?php

class UserReportsMonthly extends CFormModel {

    //sensor list for user to choose from
    public $sensor_list;
    //gsn list for user to choose from
    public $gsn_list;
    //dates list for report generating
    public $year_list; // this actually is not a year, it presents month_id (yyyymm format)
    //da znamo sto je korisnik izabrao
    public $selectedGsn;
    public $selectedSensor;
    public $selectedYear;
    public $email;
    public $submitedForm;
    public $email_report;

    public $message;

    public function displayReport($gsn, $sensor, $date, $submited) {
	$this->gsn_list = $gsn;
	$this->sensor_list = $sensor;
	$this->month_list = $date;
	$this->submitedForm = $submited;

	//samo testni ispis da vidimo jel se sve dobro prenjelo
	//echo $this->sensor_list . " " . $this->gsn_list . " " . $this->date_list;

	if ($this->submitedForm) {
	    if ($this->gsn_list != "" && $this->sensor_list != "" && $this->date_list != "") {
		//ako je ispravno postavljeno sve od strane korisnika, ispisujemo report, u protivnom javljamo gresku
		//and then magic occurs bi bila tocna definicija, ako smo do tud dosli, krecemo sa reportom
		$gsn_row = DiGsn::model()->find(array('select' => 'gsn_name, gsn_ip, city', 'condition' => 'gsn_id=' . $this->selectedGsn));

		echo "GSN server: " . $gsn_row['gsn_name'] . ", from " . $gsn_row['city'];

		$sensor_row = DiSensors::model()->find(array(
			    'select' => 'sensor_user_name, location_x, location_y, sensor_type',
			    'condition' => 'sensor_id = ' . $this->selectedSensor));
		echo "<br/>Sensor: " . $sensor_row['sensor_user_name'] . ", at location => latitude: " . $sensor_row['location_x'] . ", longitude: " . $sensor_row['location_y'];
		echo "<br/>Sensor type: " . $sensor_row['sensor_type'];

		$sensor_units = DiUnits::model()->with(array('fSensorTypes' => array('select' => false, 'joinType' => 'INNER JOIN', 'condition' => '"fSensorTypes".sensor_id = ' . $this->selectedSensor)))->findAll();

		foreach ($sensor_units as $unit) {
		    echo "<br/>" . (string) $unit['unit_id'] . " => " . (string) $unit['unit_name'];
		}

		foreach ($sensor_units as $unit) {
		    $aggregated_data = AggDay::model()->find(array('condition' => 'sensor_id = ' . $this->selectedSensor . ' AND date_id = ' . $this->selectedDate . ' AND unit_id = ' . $unit['unit_id']));

		    echo "<br/>Measured unit " . (string) $unit['unit_name'];
		    echo "<br/>Average value measured: " . $aggregated_data['avg_value'];
		    echo "<br/>Minimum value measured: " . $aggregated_data['min_value'];
		    echo "<br/>Maximum value measured: " . $aggregated_data['max_value'];
		    echo "<br/>Amplitude: " . $aggregated_data['amplitude'] . " " . $unit['unit_mark'];
		}
	    }
	    else
		echo "Invalid entry. We apologize for inconvinience, please check your input and try again!";
	}
    }

    public function ColoredTable($header, $data) {
	// Colors, line width and bold font
	$this->SetFillColor(255, 0, 0);
	$this->SetTextColor(255);
	$this->SetDrawColor(128, 0, 0);
	$this->SetLineWidth(0.3);
	$this->SetFont('', 'B');
	// Header
	$w = array(40, 35, 40, 45);
	$num_headers = count($header);
	for ($i = 0; $i < $num_headers; ++$i) {
	    $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
	}
	$this->Ln();
	// Color and font restoration
	$this->SetFillColor(224, 235, 255);
	$this->SetTextColor(0);
	$this->SetFont('');
	// Data
	$fill = 0;
	foreach ($data as $row) {
	    $this->Cell($w[0], 6, $row[0], 'LR', 0, 'L', $fill);
	    $this->Cell($w[1], 6, $row[1], 'LR', 0, 'L', $fill);
	    $this->Cell($w[2], 6, number_format($row[2]), 'LR', 0, 'R', $fill);
	    $this->Cell($w[3], 6, number_format($row[3]), 'LR', 0, 'R', $fill);
	    $this->Cell($w[4], 6, number_format($row[4]), 'LR', 0, 'R', $fill);
	    $this->Ln();
	    $fill = !$fill;
	}
	$this->Cell(array_sum($w), 0, '', 'T');
    }

    public function pdfGeneration() {
	$pdf = Yii::createComponent('application.extensions.tcpdf.ETcPdf',
			'P', 'cm', 'A4', true, 'UTF-8');
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor("Matija Renić & Luka Postružin");

////set margins
//        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
//
////set image scale factor
//        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
//set some language-dependent strings
//$pdf->setLanguageArray($l);
// ---------------------------------------------------------
// set font
	$pdf->SetFont('helvetica', '', 12);

	$gsn_row = DiGsn::model()->find(array('select' => 'gsn_name, gsn_ip, city', 'condition' => 'gsn_id=' . $this->selectedGsn));

	$sensor_row = DiSensors::model()->find(array(
		    'select' => 'sensor_user_name, location_x, location_y, sensor_type',
		    'condition' => 'sensor_id = ' . $this->selectedSensor));

//        $pdf->SetTitle("Monthly report for " . $sensor_row['sensor_user_name']);
//        //$pdf->SetSubject("TCPDF Tutorial");
//        $pdf->SetKeywords("sensor, monthly, report");
//        $pdf->setPrintHeader(false);
//        $pdf->setPrintFooter(false);


	$pdf->AddPage();
	$pdf->SetMargins(2, 2, 2);
	$pdf->SetFont('times', 'B', 24);
	$pdf->SetY(12);
	$pdf->Cell(0, 1, 'ColdWatch', 0, 1, 'R', false);
	$pdf->Cell(0, 1, 'Monthly report', 0, 1, 'R', false);

	$pdf->AddPage();
	$pdf->SetFont('times', 'B', 14);
	$pdf->Cell(0, 1, '1.   Disclaimer', 0, 1, 'L', false);
	$pdf->SetFont('times', '', 1);
	//$pdf->MultiCell(0,5,'This report has been sent to you because of your subscription from 10.7.2011. If you want to cancel your subscription, please contact us on ',0,'L',false);
	$pdf->Cell(85, 5, 'This report has been downloaded on ' . date("d.m.Y.", strtotime("today")) . ' from ', 0, 0, 'L', false);
	$pdf->SetTextColor(0, 0, 255);
	$pdf->SetFont('', 'U');

	$pdf->Ln();
	$pdf->SetFont('times', 'B', 14);
	$pdf->SetTextColor(0, 0, 0);

	$pdf->Cell(0, 1, '2.   ' . $sensor_row['sensor_user_name'], 0, 1, 'L', false);
	$pdf->SetFont('times', '', 10);
	$pdf->Cell(0, 1, 'This sensor is called ' . $sensor_row['sensor_user_name'] . ' and is located on coordinates:', 0, 1, 'L', false);
	$pdf->Cell(0, 2, '   *  X: ' . sprintf("%f", $sensor_row['location_x']) . '', 0, 1, 'L', false);
	$pdf->Cell(0, 2, '   *  Y: ' . sprintf("%f", $sensor_row['location_y']) . '', 0, 1, 'L', false);
	$pdf->Cell(0, 1, 'This sensor belongs to GSN server "' . $gsn_row['gsn_name'] . '"', 0, 1, 'L', false);



//
	$pdf->AddPage();
//        $pdf->SetFont("times", "BI", 20);
//
	$html = '
        <h1>Monthly report</h1>
        <h2>' . $sensor_row['sensor_user_name'] . '</h2>
        <i>Sensor is located at server: ' . $gsn_row['gsn_name'] . ', ' . $gsn_row['city'] . '</i>
        <p>Type of sensor: ' . $sensor_row['sensor_type'] . '</p>
        <p>Location of sensor: <br/>Latitude: ' . $sensor_row['location_x'] . '<br/>Longitude: ' . $sensor_row['location_y'] . '</p>
        ';

	$pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
	//$pdf->AddPage();
	$sensor_units = DiUnits::model()->with(array('fSensorTypes' => array('select' => false, 'joinType' => 'INNER JOIN', 'condition' => '"fSensorTypes".sensor_id = ' . $this->selectedSensor)))->findAll();

	foreach ($sensor_units as $unit) {
	    $agg_daily = AggMonthDayPart::model()->find(array('condition' => 'sensor_id = ' . $this->selectedSensor . ' AND month_id = ' . $this->selectedYear . ' AND unit_id = ' . $unit['unit_id']));
	    $html = "<br/><br/><h3>" . $unit['unit_name'] . "</h3>";
	    $html.= '<ul>';
	    $html.= "<li>Average measured value: " . number_format($agg_daily['avg_value'], 2) . $unit->unit_mark . "</li>";
	    $html.= "<li>Maximum measured value: " . number_format($agg_daily['max_value'], 2) . $unit->unit_mark . "</li>";
	    $html.= "<li>Minimum measured value: " . number_format($agg_daily['min_value'], 2) . $unit->unit_mark . "</li>";
	    $html.= "</ul>";
	    $pdf->writeHTMLCell($w = 0, $h = 0, $x = 1, $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);

	    $aggregated_data = AggMonthDayPart::model()->findAll(array('condition' => 'sensor_id = ' . $this->selectedSensor . ' AND month_id = ' . $this->selectedYear . ' AND unit_id = ' . $unit['unit_id']));

	    //array presenting part of the day
	    $day_part = array();
	    array_push($day_part, "");
	    $max_values = array();
	    array_push($max_values, "Maximum / " . $unit->unit_mark);
	    $min_values = array();
	    array_push($min_values, "Minimum / " . $unit->unit_mark);
	    $avg_values = array();
	    array_push($avg_values, "Average / " . $unit->unit_mark);
	    //$data_span = 0;

	    foreach ($aggregated_data as $row) {
		array_push($day_part, ucfirst($row->dayPart['day_part_name']));
		array_push($max_values, (double) number_format($row['max_value'], 2));
		array_push($min_values, (double) number_format($row['min_value'], 2));
		array_push($avg_values, (double) number_format($row['avg_value'], 2));
		//$data_span = max($row['max_value'], $data_span);
	    }

	    $rows_array = array($max_values, $min_values, $avg_values);

	    //header for each unit table
	    $html = "<br/><br/><b>Aggregated data for " . $unit['unit_name'] . "</b>";
	    $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'C', $autopadding = true);

	    $pdf->SetFillColor(255, 0, 0);
	    $pdf->SetTextColor(255);
	    $pdf->SetDrawColor(128, 0, 0);
	    $pdf->SetLineWidth(0.1);
	    $pdf->SetFont('', 'B');
	    $w = array(3, 3, 3, 3, 3, 3);

	    $i = 0;
	    foreach ($day_part as $part) {
		$pdf->Cell($w[$i], 0.5, $part, 1, 0, 'C', 1, '', 0);
		$i = $i + 1;
	    }

	    $pdf->Ln();
	    $pdf->SetFillColor(224, 235, 255);
	    $pdf->SetTextColor(0);
	    $pdf->SetFont('');

	    $fill = 0;

	    $i = 0;
	    //Max values
	    foreach ($max_values as $max) {
		$pdf->Cell($w[$i], 0.5, $max, 'LR', 0, 'L', 1, '', 0);
		$i = $i + 1;
	    }
	    $pdf->Ln();
	    $fill = !$fill;

	    $i = 0;
	    //Min values
	    foreach ($min_values as $min) {
		$pdf->Cell($w[$i], 0.5, $min, 'LR', 0, 'L', 1, '', 0);
		$i = $i + 1;
	    }
	    $pdf->Ln();
	    $fill = !$fill;

	    $i = 0;
	    //Avg values
	    foreach ($avg_values as $avg) {
		$pdf->Cell($w[$i], 0.5, $avg, 'LR', 0, 'L', 1, '', 0);
		$i = $i + 1;
	    }
	    $pdf->Ln();
	    $fill = !$fill;
	    $pdf->Cell(array_sum($w), 0, '', 'T');
	}

//        $pdf->Cell(0, 2, $sensor_row['sensor_user_name'], 1, 1, 'L');
//        $pdf->Cell(0, 10, "Sensor is located on GSN: ".$gsn_row['gsn_name']." in ".$gsn_row['city'], 1, 1, 'L');
	$pdf->Output("Monthly_report_" . $sensor_row['sensor_user_name'] . "_" . $this->selectedYear . ".pdf", "D");
    }

    public function reportSending(&$message) {
	try {
	    exec('xvfb-run -a -s "-screen 0 640x480x16" wkhtmltopdf --dpi 200 --redirect-delay 1000 --page-size A4 "http://161.53.67.224/lion/index.php/webService/monthlyReportGenerating?gsn_list=' . $this->selectedGsn . '&sensor_list=' . $this->selectedSensor . '&year_list=' . $this->selectedYear . '" "/home/lpostruzin/cronjobs/reports/' . "Monthly_report_" . $this->selectedGsn . "_GSN_" . $this->selectedSensor . $this->selectedYear . '.pdf"');
	    exec('mutt -s "Monthly report report for ' . Yii::app()->user->username . ', ' . $this->selectedYear . '" ' . $this->email . ' -a ' . '/home/lpostruzin/cronjobs/reports/' . "Monthly_report_" . $this->selectedGsn . "_GSN_" . $this->selectedSensor . $this->selectedYear . ".pdf");
	} catch (Exception $ae) {
	    $message = "ERROR occured during generating and sending report! Please try again and if the problem persists contact our administrator for more information";
	    return false;
	}
	$message = "Your monthly report has successfuly been sent on provided email: ".$this->email."";
	return true;
    }

}

?>