<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$this->pageTitle = Yii::app()->name . ' - Daily reports';
$this->breadcrumbs = array(
    'Daily reports',
);
?>

<div class="post">

    <h2 class="title">Daily reports site</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">

	<p><?php echo (isset($model->message) ? $model->message : ""); ?> </p>
	<div class="form">

	    <?php
	    $form = $this->beginWidget('CActiveForm', array(
			'id' => 'report-form',
			'enableClientValidation' => true,
			//'enableAjaxValidation' => true,
			'clientOptions' => array(
			    'validateOnSubmit' => true,
			),
		    ));
	    ?>

	    <p class="note">Fields with <span class="required">*</span> are required.</p>

	    <?php echo $form->errorSummary($model); ?>

	    <h3>Sensor information</h3>
	    <div class="row">
		<?php echo $form->labelEx($model, 'gsn_list'); ?>
		<?php
		$gsn_order = new CDbCriteria;
		$gsn_order->order = 'gsn_name ASC';

		echo $form->dropDownList($model, 'gsn_list', CHtml::listData(DiGsn::model()->with(array('diGsnPrivileges' => array('select' => false, 'joinType' => 'INNER JOIN', 'condition' => '"diGsnPrivileges".user_id = ' . Yii::app()->user->id)))->findAll($gsn_order), 'gsn_id', 'gsn_name'),
			array(
			    'empty' => 'Select',
			    'options' => array($model->selectedGsn => array('selected' => 'selected')),
			    'ajax' => array(
				'type' => 'POST',
				'url' => CController::createUrl('reportsDailyDynamicSensors'),
				'beforeSend' => 'function(){
				    document.body.style.cursor=\'wait\';
				}',
				'complete' => 'function(){
				    document.body.style.cursor=\'default\';
				}',
				'update' => '#UserReportsDaily_' . 'sensor_list'
			    )
			)
		);
		?>

		<?php echo $form->error($model, 'gsn_list'); ?>
	    </div>

	    <div class="row">
		<?php echo $form->labelEx($model, 'sensor_list'); ?>
		<?php
		$sensor_order = new CDbCriteria;
		$sensor_order->order = 'sensor_name ASC';
		echo $form->dropDownList($model, 'sensor_list', CHtml::listData(DiSensors::model()->with(array('gsn' => array('select' => false, 'joinType' => 'INNER JOIN', 'condition' => '"gsn".gsn_id = ' . (isset($model->selectedGsn) ? $model->selectedGsn : '-1'))))->findAll($sensor_order), 'sensor_id', 'sensor_user_name'),
			array(
			    'empty' => 'Select',
			    'options' => array($model->selectedSensor => array('selected' => 'selected')),
			    'ajax' => array(
				'type' => 'POST',
				'url' => CController::createUrl('reportsDailyDynamicDates'),
				'beforeSend' => 'function(){
				    document.body.style.cursor=\'wait\';
				}',
				'complete' => 'function(){
				    document.body.style.cursor=\'default\';
				}',
				'update' => '#UserReportsDaily_' . 'date_list'
			    )
			)
		);
		?>
		<?php echo $form->error($model, 'sensor_list'); ?>
	    </div>

	    <div class="row">
		<?php echo $form->labelEx($model, 'date_list'); ?>
		<?php
		echo $form->dropDownList($model, 'date_list', CHtml::listData(Yii::app()->db->createCommand()
					->selectDistinct('a.sensor_id, a.date_id, d.date')
					->from('agg_day a')
					->join('di_days d', 'a.date_id=d.date_id')
					->where('a.sensor_id=:id', array(':id' => (isset($model->selectedSensor) ? $model->selectedSensor : '-1')))
					->order('a.date_id desc')
					->queryAll(), 'date_id', 'date'),
			array(
			    'empty' => 'Select',
			    'options' => array($model->selectedDate => array('selected' => 'selected')),
			)
		);
		?>
		<?php echo $form->error($model, 'date_list'); ?>
	    </div>

	    <div class="row">
		<?php echo $form->labelEx($model, 'email'); ?>
		<?php echo $form->textField($model, 'email'); ?>
		<?php echo $form->error($model, 'email'); ?>
	    </div>

	    <div class="row buttons">
		<?php echo CHtml::submitButton('Generate', array('name' => 'submit_button')); ?>
		<?php echo CHtml::submitButton('Email report', array('name' => 'email_sending')); ?>
	    </div>
	</div>

	<?php $this->endWidget(); ?>
	<?php
		if ($model->submitedForm) {

		    $sensor_row = DiSensors::model()->find(array(
				'select' => 'sensor_user_name, location_x, location_y, sensor_type',
				'condition' => 'sensor_id = ' . $model->selectedSensor));

		    $gsn_row = DiGsn::model()->find(array('select' => 'gsn_name, gsn_ip, city,state', 'condition' => 'gsn_id=' . $model->selectedGsn));

		    if (!empty($sensor_row) && !empty($gsn_row)) {
			echo "<br/><h1>" . $sensor_row['sensor_user_name'] . "</h1>";

			echo '<div style="border-top-style:solid;"><br/>';

			echo "<h3>" . $gsn_row['gsn_name'] . "</h3><br/><b>GSN server location: </b>" . $gsn_row['city'] . ', ' . $gsn_row['state'];

			echo "<br/>Location of the sensor: latitude: " . $sensor_row['location_x'] . ", longitude: " . $sensor_row['location_y'];
			echo "<br/>Sensor type: " . $sensor_row['sensor_type'] . '<br/><br/>';

			if ($sensor_row['location_y'] == null or $sensor_row['location_x'] == null) {
			    echo "<b>NOTICE: </b>Location of the sensor is not provided correctly. Please contact administrator for further support.<br/>";
			} else {
			    $google = new googleMaps;

			    $google->sensorMap($model->selectedSensor, $model->selectedGsn);
			}

			//ako je ispravno postavljeno sve od strane korisnika, ispisujemo report, u protivnom javljamo gresku
			//and then magic occurs bi bila tocna definicija, ako smo do tud dosli, krecemo sa reporto

			$sensor_units = DiUnits::model()->with(array('fSensorTypes' => array('select' => false, 'joinType' => 'INNER JOIN', 'condition' => '"fSensorTypes".sensor_id = ' . $model->selectedSensor)))->findAll();

			foreach ($sensor_units as $unit) {
			    $agg_daily = AggDay::model()->find(array('condition' => 'sensor_id = ' . $model->selectedSensor . ' AND date_id = ' . $model->selectedDate . ' AND unit_id = ' . $unit['unit_id']));
			    echo "<br/><br/><h2>" . $unit['unit_name'] . "</h2>";
			    if (!empty($agg_daily)) {
				echo '<ul>';
				echo "<li>Average measured value: " . number_format($agg_daily['avg_value'], 2) . $unit['unit_mark'] . "</li>";
				echo "<li>Maximum measured value: " . number_format($agg_daily['max_value'], 2) . $unit['unit_mark'] . "</li>";
				echo "<li>Minimum measured value: " . number_format($agg_daily['min_value'], 2) . $unit['unit_mark'] . "</li>";
				echo "</ul>";
			    } else {
				echo "<b>INFORMATION: </b>There are no aggregated data for this day. If you believe this to be an error, please contact us as soon as possible. Data loss can occur while having problems with electricity or internet connection on GSN server, or malfunction or battery problem on particular sensor.<br/><br/>";
			    }

			    $aggregated_data = AggDayHourly::model()->findAll(array('condition' => 'sensor_id = ' . $model->selectedSensor . ' AND date_id = ' . $model->selectedDate . ' AND unit_id = ' . $unit['unit_id']));

			    $aggregated_data_before = AggDayHourly::model()->findAll(array('condition' => 'sensor_id = ' . $model->selectedSensor . ' AND date_id = ' . date('Ymd', strtotime(date('Ymd', mktime(0, 0, 0, (($model->selectedDate / 100) % 100), $model->selectedDate % 100, $model->selectedDate / 10000)) . '-1 month')) . ' AND unit_id = ' . $unit['unit_id']));

			    if (!empty($aggregated_data)) {
				//array presenting part of the day
				$day_part = array();
				$max_values = array();
				$min_values = array();
				$avg_values = array();
				$day_part_before = array();
				$avg_values_before = array();

				$data_span = 0;

				foreach ($aggregated_data as $row) {
				    array_push($day_part, $row ['hour']);
				    array_push($max_values, (double) number_format($row['max_value'], 2));
				    array_push($min_values, (double) number_format($row['min_value'], 2));
				    array_push($avg_values, (double) number_format($row['avg_value'], 2));
				    $data_span++;
				    //$data_span = max($row['max_value'], $data_span);
				}

				$max = max(array_filter($max_values)) + abs(max(array_filter($max_values)) * 0.1);
				$min = min(array_filter($min_values)) - abs(min(array_filter($min_values)) * 0.1);

				if ($data_span < 24) {
				    echo "<b>NOTICE: </b>On the chosen date, " . $model->selectedDate . ", we were unable to find readings for all parts of the day!<br/>";
				}

				if (!empty($aggregated_data_before)) {
				    $number = 0;
				    foreach ($aggregated_data_before as $row) {
					array_push($day_part_before, $row['hour']);
					array_push($avg_values_before, (double) number_format($row['avg_value'], 2));
					$number++;
				    }

				    $merge_max = array_merge($max_values, $avg_values_before);
				    $merge_min = array_merge($min_values, $avg_values_before);

				    $max = max(array_filter($merge_max)) + abs(max(array_filter($merge_max)) * 0.1);
				    $min = min(array_filter($merge_min)) - abs(min(array_filter($merge_min)) * 0.1);

				    if ($number < 24)
					echo "We apologize but the data from month before was not available on the whole span!<br/>";

				    //printr($avg_values_before);

				    $this->Widget('application.extensions.highcharts.HighchartsWidget', array(
					'options' => array(
					    'theme' => 'default',
					    'chart' => array(
						'width' => '550',
						'height' => '300',
						'type' => 'spline',
					    ),
					    'title' => array('text' => 'Aggregated data for ' . $unit['unit_name']),
					    'xAxis' => array(
						'categories' => $day_part//array('Apples', 'Bananas', 'Oranges')
					    ),
					    'yAxis' => array(
						'title' => array('text' => $unit['unit_name']),
						'max' => $max,
						'min' => $min,
					    ),
					    'tooltip' => array(
						//'crosshairs' => true,
						//'shared' => true,
						'formatter' => 'js:function() {
						    return this.series.name + ": " + this.y + " at " + this.x + "h";
						}',
					    ),
					    'plotOptions' => array(
						'spline' => array(
						    'lineWidth' => 3,
						    // 'dataLabels' => array('enabled' => true),
						    //'enableMouseTracking' => false
						    'marker' => array(
							'enabled' => true,
							'states' => array(
							    'hover' => array(
								'enabled' => true,
								'symbol' => 'circle',
								'radius' => 5,
								'lineWidth' => 2
							    )
							)
						    )
						)
					    ),
					    'series' => array(
						array('name' => 'Max values', 'data' => $max_values),
						array('name' => 'Min values', 'data' => $min_values),
						array('name' => 'Avg values', 'data' => $avg_values),
						array('name' => 'Avg values month before', 'data' => $avg_values_before))
					)
					    )
				    );
				} else {

				    echo "<b>NOTICE: </b>We apologize but the data from month before was not available!<br/>";


				    $this->Widget('application.extensions.highcharts.HighchartsWidget', array(
					'options' => array(
					    'theme' => 'default',
					    'chart' => array(
						'width' => '550',
						'height' => '300',
						'type' => 'spline',
					    ),
					    'title' => array('text' => 'Aggregated data for ' . $unit['unit_name']),
					    'xAxis' => array(
						'categories' => $day_part//array('Apples', 'Bananas', 'Oranges')
					    ),
					    'yAxis' => array(
						'title' => array('text' => $unit['unit_name']),
						'max' => $max,
						'min' => $min,
					    ),
					    'tooltip' => array(
						//'crosshairs' => true,
						//'shared' => true,
						'formatter' => 'js:function() {
						    return this.series.name + ": " + this.y + " at " + this.x + "h";
						}',
					    ),
					    'plotOptions' => array(
						'spline' => array(
						    'lineWidth' => 3,
						    // 'dataLabels' => array('enabled' => true),
						    //'enableMouseTracking' => false
						    'marker' => array(
							'enabled' => true,
							'states' => array(
							    'hover' => array(
								'enabled' => true,
								'symbol' => 'circle',
								'radius' => 5,
								'lineWidth' => 2
							    )
							)
						    )
						)
					    ),
					    'series' => array(
						array('name' => 'Max values', 'data' => $max_values),
						array('name' => 'Min values', 'data' => $min_values),
						array('name' => 'Avg values', 'data' => $avg_values))
					)
					    )
				    );
				}
			    } else {
				echo "<b>GRAPH: </b>We apologize for the inconvinience, but we are unable to find valid data for this day. Please try some day or sensor. If you believe this to be an error, feel free to contact us as soon as possbile!<br/>";
			    }
			}
			echo "</div>";
			echo '<form method="post" action="automaticReportSubscription">';
			echo 'Email: <input type="text" name="email" /><br/>';
			echo '<input type="hidden" name="gsn_id" value="' . $model->selectedGsn . '"/>';
			echo '<input type="hidden" name="sensor_id" value="' . $model->selectedSensor . '"/>';
			echo '<input type="hidden" name="type" value="1"/>';
			echo '<input type="hidden" name="automatic" value="yes"/>';
			echo '<p>Want to recieve this report daily on your email? Subscribe ' . CHtml::submitButton('here') . ' and start using the benefits of our system!</p>';
			echo "</form>";
		    } else
			echo "<b>NOTICE: </b>We apologize for the inconvinience but this sensor was not found in the database!<br/>";
		}
	?>
    </div>
</div>
