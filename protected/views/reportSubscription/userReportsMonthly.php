<?php
$this->pageTitle = Yii::app()->name . ' - Monthly reports';
$this->breadcrumbs = array(
    'Monthly reports',
);
?>

<div class="post">
    <p class="date"><?php echo date("M"); ?><b><?php echo date("j"); ?></b></p>
    <h2 class="title">Monthly reports site</h2>
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
				'url' => CController::createUrl('reportsMonthlyDynamicSensors'),
				'beforeSend' => 'function(){
				    document.body.style.cursor=\'wait\';
				}',
				'complete' => 'function(){
				    document.body.style.cursor=\'default\';
				}',
				'update' => '#UserReportsMonthly_' . 'sensor_list'
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
		echo $form->dropDownList($model, 'sensor_list', /* array(), */CHtml::listData(DiSensors::model()->with(array('gsn' => array('select' => false, 'joinType' => 'INNER JOIN', 'condition' => '"gsn".gsn_id = ' . $model->selectedGsn)))->findAll($sensor_order), 'sensor_id', 'sensor_user_name'),
			array(
			    'empty' => 'Select',
			    'options' => array($model->selectedSensor => array('selected' => 'selected')),
			    'ajax' => array(
				'type' => 'POST',
				'url' => CController::createUrl('reportsMonthlyDynamicYears'),
				'beforeSend' => 'function(){
				    document.body.style.cursor=\'wait\';
				}',
				'complete' => 'function(){
				    document.body.style.cursor=\'default\';
				}',
				'update' => '#UserReportsMonthly_' . 'year_list'
			    )
			)
		);
		?>
		<?php echo $form->error($model, 'sensor_list'); ?>
	    </div>

	    <div class="row">
		<?php echo $form->labelEx($model, 'year_list'); ?>
		<?php
		echo $form->dropDownList($model, 'year_list', /* array(), */ CHtml::listData(Yii::app()->db->createCommand()
					->selectDistinct('a.sensor_id, a.month_id')
					->from('agg_month_day_part a')
					//->join('di_days d', 'a.date_id=d.date_id')
					->where('a.sensor_id=:id', array(':id' => $model->selectedSensor))
					->order('a.month_id desc')
					->queryAll(), 'month_id', 'month_id'),
			array(
			    'empty' => 'Select',
			    'options' => array($model->selectedYear => array('selected' => 'selected')),
			)
		);
		?>
		<?php echo $form->error($model, 'year_list'); ?>
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
	    <?php $this->endWidget(); ?>
    	</div>

	<?php
		//$model->displayReport($model->selectedGsn, $model->selectedSensor, $model->selectedDate, $model->submitedForm);

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

			$sensor_units = DiUnits::model()->with(array('fSensorTypes' => array('select' => false, 'joinType' => 'INNER JOIN', 'condition' => '"fSensorTypes".sensor_id = ' . $model->selectedSensor)))->findAll();

			foreach ($sensor_units as $unit) {
			    echo "<br/><br/><h2>" . $unit['unit_name'] . "</h2>";

			    $aggregated_data = AggMonthDayPart::model()->findAll(array('condition' => 'sensor_id = ' . $model->selectedSensor . ' AND month_id = ' . $model->selectedYear . ' AND unit_id = ' . $unit['unit_id'], 'order' => 'day_part_id'));

			    //array presenting part of the day
			    $day_part = array("morning", "forenoon", "afternoon", "evening", "night");
			    $max_values = array();
			    $min_values = array();
			    $avg_values = array();
			    $num = 0;

			    foreach ($aggregated_data as $row) {
				//array_push($day_part, $row->dayPart['day_part_name']);
				if (strcmp($day_part[$num], $row->dayPart['day_part_name']) == 0) {
				    array_push($max_values, (double) number_format($row['max_value'], 2));
				    array_push($min_values, (double) number_format($row['min_value'], 2));
				    array_push($avg_values, (double) number_format($row['avg_value'], 2));
				    //$data_span = max($row['max_value'], $data_span);
				} else {
				    array_push($max_values, null);
				    array_push($min_values, null);
				    array_push($avg_values, null);
				}
				$num++;
			    }

			    $rows_array = array($max_values, $min_values, $avg_values);

			    // echo "<canvas>";
			    echo '<table id="hor-minimalist-b">';
			    echo "<thead>";
			    echo "<tr><th colspan=\"6\" style=\"text-align: center;\">Aggregated data for " . $unit['unit_name'] . " for the whole month</th></tr>";
			    echo "<tr>
                                    <th></th>";
			    foreach ($day_part as $part)
				echo "<th>" . $part . "</th>";

			    echo "  </tr>
                             </thead>";
			    echo "<tbody>
                                <tr>
                                    <td>Max</td>";
			    foreach ($max_values as $max)
				echo "<td>" . $max . "</td>";
			    echo "  </tr>";
			    echo "  <tr>
                                    <td>Min</td>";
			    foreach ($min_values as $min)
				echo "<td>" . $min . "</td>";

			    echo "  </tr>";
			    echo "  <tr>
                                    <td>Avg</td>";
			    foreach ($avg_values as $avg)
				echo "<td>" . $avg . "</td>";

			    echo "</tr></tbody>";
			    echo "</table>";
			    //echo "</canvas>";

			    $aggregated_data = AggDay::model()->findAll(array('condition' => 'sensor_id = ' . $model->selectedSensor . ' AND date_id between ' . $model->selectedYear . '01 and ' . $model->selectedYear . '31 AND unit_id = ' . $unit['unit_id'], 'order' => 'date_id'));
			    $aggregated_data_before = AggDay::model()->findAll(array('condition' => 'sensor_id = ' . $model->selectedSensor . ' AND date_id between ' . date('Ym', strtotime(date('Ymd', mktime(0, 0, 0, ($model->selectedYear % 100), 1, (int) $model->selectedYear / 100)) . '-1 month')) . '01 and ' . date('Ym', strtotime(date('Ymd', mktime(0, 0, 0, ($model->selectedYear % 100), 1, $model->selectedYear / 100)) . '-1 month')) . '31' . ' AND unit_id = ' . $unit['unit_id'], 'order' => 'date_id'));

			    //echo date('Ym', strtotime(date('Ymd', mktime(0, 0, 0, ($model->selectedYear % 100), 0, (int) $model->selectedYear / 100)) . '-1 month')) . '01';

			    if (!empty($aggregated_data)) {
				//array presenting part of the day
				$max_values = array();
				$min_values = array();
				$avg_values = array();
				$day_part_before = array();
				$avg_values_before = array();
				$day_id = array();

				$data_span = 0;
				$num_readings = 0;
				$num_before = 0;

				$all_days = DiDays::model()->findall(array('condition' => 'date_id between ' . $model->selectedYear . '01 and ' . $model->selectedYear . ((date("Ym") == $model->selectedYear) ? date("d") : 31)));

				foreach ($all_days as $day) {
				    array_push($day_id, $day['date_id']);
				    if ($day['date_id'] == (isset($aggregated_data[$num_readings]['date_id']) ? $aggregated_data[$num_readings]['date_id'] : 19000101)) {
					array_push($max_values, (double) number_format($aggregated_data[$num_readings]['max_value'], 2));
					//array_push($max_values, (double) number_format($aggregated_data[$data_span]->max_value));
					array_push($min_values, (double) number_format($aggregated_data[$num_readings]['min_value'], 2));
					array_push($avg_values, (double) number_format($aggregated_data[$num_readings]['avg_value'], 2));
					$num_readings++;
				    } else {
					array_push($max_values, null);
					array_push($min_values, null);
					array_push($avg_values, null);
				    }

				    if (!empty($aggregated_data_before)) {
					if (date('Ymd', strtotime(date('Ymd', mktime(0, 0, 0, floor(($day['date_id'] / 100) % 100), ($day['date_id'] % 100), floor($day['date_id'] / 10000))) . '-1 month')) == (isset($aggregated_data_before[$num_before]['date_id']) ? $aggregated_data_before[$num_before]['date_id'] : 19000101)) {
					    array_push($avg_values_before, (double) number_format($aggregated_data_before[$num_before]['avg_value'], 2));
					    $num_before++;
					} else {
					    array_push($avg_values_before, null);
					}
				    }

				    $data_span++;
				}

				$merge_max = array_merge($max_values,$avg_values_before);
				$merge_min = array_merge($min_values,$avg_values_before);

				$max = max(array_filter($merge_max)) + abs(max(array_filter($merge_max)) * 0.1);
				$min = min(array_filter($merge_min)) - abs(min(array_filter($merge_min)) * 0.1);
				//echo "MAX: ".$max. " MIN: ".min(array_filter($min_values))."<br/>";
				//echo "NUM: " . $num_readings . " DATA SPAN: " . $data_span . " BEFORE: " . $num_before;

				if ($num_readings < cal_days_in_month(CAL_GREGORIAN, $model->selectedYear % 100, (int) $model->selectedYear / 100)) {
				    echo "<b>NOTICE: </b>For the chosen month, " . $model->selectedYear . ", we were unable to find readings for all days!<br/>";
				}

				if (!empty($aggregated_data_before)) {

				    if ($num_before < cal_days_in_month(CAL_GREGORIAN, date('m', strtotime(date('Ymd', mktime(0, 0, 0, ($model->selectedYear % 100), 1, (int) $model->selectedYear / 100)) . '-1 month')) % 100, date('Y', strtotime(date('Ymd', mktime(0, 0, 0, ($model->selectedYear % 100), 0, $model->selectedYear / 100)) . '-1 month')) / 100))
					echo "<b>NOTICE: </b>Data from month before, " . date('Ym', strtotime(date('Ymd', mktime(0, 0, 0, ($model->selectedYear % 100), 1, (int) $model->selectedYear / 100)) . '-1 month')) . " are not avaliable for all days!<br/>";
				    echo "<br/>";
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
						'categories' => $day_id,
						'labels' => array('rotation' => 60, 'step' => 2, 'y' => 30),
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
						    return this.series.name + ": " + this.y + " at " + this.x;
						}',
					    ),
					    'plotOptions' => array(
						'spline' => array(
						    'lineWidth' => 3,
						    // 'dataLabels' => array('enabled' => true),
						    'enableMouseTracking' => true,
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
					    /*
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
					      ), */
					    'series' => array(
						array('name' => 'Max values', 'data' => $max_values),
						array('name' => 'Min values', 'data' => $min_values),
						array('name' => 'Avg values', 'data' => $avg_values),
						array('name' => 'Avg values month before', 'data' => $avg_values_before))
					)
					    )
				    );
				} else {

				    echo "<b>NOTICE: </b>We apologize but the data from month before was not available!<br/><br/>";

				    $this->Widget('application.extensions.highcharts.HighchartsWidget', array(
					'options' => array(
					    'theme' => 'default',
					    'chart' => array(
						'width' => '500',
						'height' => '300',
						'type' => 'spline',
					    ),
					    'title' => array('text' => 'Aggregated data for ' . $unit['unit_name']),
					    'xAxis' => array(
						'categories' => $day_id,
						'labels' => array('rotation' => 60, 'step' => 2, 'y' => 30),
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
						    return this.series.name + ": " + this.y + " at " + this.x;
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
			echo '<input type="hidden" name="type" value="2"/>';
			echo '<input type="hidden" name="automatic" value="yes"/>';
			echo '<p>Want to recieve this report daily on your email? Subscribe ' . CHtml::submitButton('here') . ' and start using the benefits of our system!</p>';
			echo "</form>";
		    } else
			echo "<b>NOTICE: </b>We apologize for the inconvinience but this sensor was not found in the database!<br/>";
		}
	?>
    </div>
</div>