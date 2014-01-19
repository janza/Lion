<div class="post">
    <p><?php ?></p>
    <h1 style="position: relative; text-align: center;">Daily report, <?php echo date('d.m.Y', mktime(0, 0, 0, (($model->selectedDate / 100) % 100), $model->selectedDate % 100, $model->selectedDate / 10000)); ?></h1>
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

	    echo "<h3>" . strtoupper($gsn_row['gsn_name']) . "</h3><br/><b>GSN server location: </b>" . $gsn_row['city'] . ', ' . $gsn_row['state'];

	    echo "<br/>Location of the sensor: latitude: " . $sensor_row['location_x'] . ", longitude: " . $sensor_row['location_y'];
	    echo "<br/>Sensor type: " . $sensor_row['sensor_type'] . '<br/><br/>';

	    if ($sensor_row['location_y'] == null or $sensor_row['location_x'] == null) {
		echo "<b>NOTICE: </b>Location of the sensor is not provided correctly. Please contact administrator for further support.<br/>";
	    } else {
		echo '<img src = "http://maps.googleapis.com/maps/api/staticmap?center=' . $sensor_row['location_y'] . ',' . $sensor_row['location_x'] . '&zoom=15&size=700x400&maptype=roadmap&markers=icon:http://161.53.67.224/lion/images/icon_advanced.png?chst=d_map_pin_icon%26chld=cafe%257C996600%7C' . $sensor_row['location_y'] . ',' . $sensor_row['location_x'] . '&sensor=false"/>';
	    }

	    //ako je ispravno postavljeno sve od strane korisnika, ispisujemo report, u protivnom javljamo gresku
	    //and then magic occurs bi bila tocna definicija, ako smo do tud dosli, krecemo sa reporto

	    $sensor_units = DiUnits::model()->with(array('fSensorTypes' => array('select' => false, 'joinType' => 'INNER JOIN', 'condition' => '"fSensorTypes".sensor_id = ' . $model->selectedSensor)))->findAll();

	    foreach ($sensor_units as $unit) {
		echo '<div class="break"></div>';
		$agg_daily = AggDay::model()->find(array('condition' => 'sensor_id = ' . $model->selectedSensor . ' AND date_id = ' . $model->selectedDate . ' AND unit_id = ' . $unit['unit_id']));
		echo '<br/><br/><h2 style="position: relative; text-align: center;">' . $unit['unit_name'] . "</h2>";

		echo '<div style="border-top-style:solid;">';
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
				    'width' => '700',
				    'height' => '400',
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
				    'categories' => $day_part//array('Apples', 'Bananas', 'Oranges')
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