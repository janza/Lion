<?php

class UserReportsDaily extends CFormModel {

    //sensor list for user to choose from
    public $sensor_list;
    //gsn list for user to choose from
    public $gsn_list;
    //dates list for report generating
    public $date_list;
    //da znamo sto je korisnik izabrao
    public $selectedGsn;
    public $selectedSensor;
    public $selectedDate;
    public $submitedForm;
    public $email;
    public $message;
    public $email_report;

    function displayReport($gsn, $sensor, $date, $submited) {
	$this->gsn_list = $gsn;
	$this->sensor_list = $sensor;
	$this->date_list = $date;
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

    function pdfGeneration() {
	$pdf = Yii::createComponent('application.extensions.tcpdf.ETcPdf',
			'P', 'cm', 'A4', true, 'UTF-8');
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor("Nicola Asuni");
	$pdf->SetTitle("TCPDF Example 002");
	$pdf->SetSubject("TCPDF Tutorial");
	$pdf->SetKeywords("TCPDF, PDF, example, test, guide");
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetFont("times", "BI", 20);
	$pdf->Cell(0, 10, "Example 002", 1, 1, 'C');
	$pdf->Output("example_002.pdf", "D");
    }

}

?>