<div class="post">
    <p><?php ?></p>
    <h1 style="position: relative; text-align: center;">Monthly report, <?php echo date('m.Y', mktime(0, 0, 0, ($model->selectedYear % 100), 1, $model->selectedYear / 100)); ?></h1>
    <p class="posted" style="position: relative; text-align: center;">Lion development team</p>
    <div class="entry">

	<?php
	echo "<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>";
	$sensor_row = DiSensors::model()->find(array(
		    'select' => 'sensor_user_name, location_x, location_y, sensor_type',
		    'condition' => 'sensor_id = ' . $model->selectedSensor));

	$gsn_row = DiGsn::model()->find(array('select' => 'gsn_name, gsn_ip, city,state', 'condition' => 'gsn_id=' . $model->selectedGsn));

	if (!empty($sensor_row) && !empty($gsn_row)) {
	    echo '<h1 style="position: relative; text-align: center;">' . $sensor_row['sensor_user_name'] . "</h1>";

	    echo '<div style="border-width:5px; border-top-style:inset;"><br/>';

	    echo "<h1>" . strtoupper($gsn_row['gsn_name']) . "</h1><br/><b>GSN server location: </b>" . $gsn_row['city'] . ', ' . $gsn_row['state'];

	    echo "<br/>Location of the sensor: latitude: " . $sensor_row['location_x'] . ", longitude: " . $sensor_row['location_y'];
	    echo "<br/>Sensor type: " . $sensor_row['sensor_type'] . '<br/><br/>';

	    if ($sensor_row['location_y'] == null or $sensor_row['location_x'] == null) {
		echo "<b>NOTICE: </b>Location of the sensor is not provided correctly. Please contact administrator for further support.<br/>";
	    } else {
		echo '<img src = "http://maps.googleapis.com/maps/api/staticmap?center=' . $sensor_row['location_y'] . ',' . $sensor_row['location_x'] . '&zoom=15&size=700x400&maptype=roadmap&markers=icon:http://161.53.67.224/lion/images/icon_advanced.png?chst=d_map_pin_icon%26chld=cafe%257C996600%7C' . $sensor_row['location_y'] . ',' . $sensor_row['location_x'] . '&sensor=false"/>';
	    }

	    $sensor_units = DiUnits::model()->with(array('fSensorTypes' => array('select' => false, 'joinType' => 'INNER JOIN', 'condition' => '"fSensorTypes".sensor_id = ' . $model->selectedSensor)))->findAll();

	    foreach ($sensor_units as $unit) {
		echo '<div class="break"></div>';
		echo '<br/><br/><h2 style="position: relative; text-align: center;">' . $unit['unit_name'] . "</h2>";

		echo '<div style="border-top-style:solid;">';

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

		    $all_days = DiDays::model()->findall(array('condition' => 'date_id between ' . $model->selectedYear . '01 and ' . $model->selectedYear . ((date("Ym") == $model->selectedYear) ? date("d") : '31')));

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

		    //echo "NUM: " . $num_readings . " DATA SPAN: " . $data_span . " BEFORE: " . $num_before;

		    if ($num_readings < cal_days_in_month(CAL_GREGORIAN, $model->selectedYear % 100, (int) $model->selectedYear / 100)) {
			echo "<b>NOTICE: </b>For the chosen month, " . $model->selectedYear . ", we were unable to find readings for all days!<br/>";
		    }

		    $merge_max = array_merge($max_values, $avg_values_before);
		    $merge_min = array_merge($min_values, $avg_values_before);

		    $max = max(array_filter($merge_max)) + abs(max(array_filter($merge_max)) * 0.1);
		    $min = min(array_filter($merge_min)) - abs(min(array_filter($merge_min)) * 0.1);

		    if (!empty($aggregated_data_before)) {

			if ($num_before < cal_days_in_month(CAL_GREGORIAN, date('m', strtotime(date('Ymd', mktime(0, 0, 0, ($model->selectedYear % 100), 1, (int) $model->selectedYear / 100)) . '-1 month')) % 100, date('Y', strtotime(date('Ymd', mktime(0, 0, 0, ($model->selectedYear % 100), 0, $model->selectedYear / 100)) . '-1 month')) / 100))
			    echo "<b>NOTICE: </b>Data from month before, " . date('Ym', strtotime(date('Ymd', mktime(0, 0, 0, ($model->selectedYear % 100), 1, (int) $model->selectedYear / 100)) . '-1 month')) . " are not avaliable for all days!<br/>";

			$this->Widget('application.extensions.highcharts.HighchartsWidget', array(
			    'options' => array(
				'theme' => 'default',
				'chart' => array(
				    'width' => '700',
				    'height' => '400',
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
				'plotOptions' => array(
				    'spline' => array(
					'lineWidth' => 3,
					// 'dataLabels' => array('enabled' => true),
					'enableMouseTracking' => false,
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
				    'width' => '700',
				    'height' => '400',
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
				'plotOptions' => array(
				    'spline' => array(
					'lineWidth' => 3,
					// 'dataLabels' => array('enabled' => true),
					'enableMouseTracking' => false,
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
		echo "</div>";
	    }
	    echo "</div>";
	}
	else
	    echo "Some invalid information was passed to the system, please contact administrator for further support!";
	?>
    </div>
</div>