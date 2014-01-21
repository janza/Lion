<?php
$this->pageTitle = Yii::app()->name . ' - Graph viewer';
$this->breadcrumbs = array(
    'Graph viewer',
);
?>
<div class="post">

    <h2 class="title">Graph data presentation</h2>
    <p class="posted">Lion development team</p>
    <div class="entry">
	<div class="form">
	    <?php
	    $form = $this->beginWidget('CActiveForm', array(
			'id' => 'userGraphViewer-form',
			'enableClientValidation' => true,
			//'enableAjaxValidation' => true,
			'clientOptions' => array(
			    'validateOnSubmit' => true,
			),
		    ));
	    ?>
	    <div class="span-7">

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
				    'url' => CController::createUrl('userGraphViewerDynamicSensors'),
				    'update' => '#UserGraphViewer_' . 'sensor_list'
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
				    'url' => CController::createUrl('userGraphViewerDynamicUnits'),
				    'update' => '#UserGraphViewer_' . 'unit_list'
				)
			    )
		    );
		    ?>
		    <?php echo $form->error($model, 'sensor_list'); ?>
		</div>

		<div class="row">
		    <?php echo $form->labelEx($model, 'unit_list'); ?>
		    <?php
		    echo $form->dropDownList($model, 'unit_list', CHtml::listData(DiUnits::model()->with(array(
					'fSensorTypes' => array(
					    'select' => false,
					    'joinType' => 'INNER JOIN',
					),
				    ))->findAll('"fSensorTypes".sensor_id=:parent_id',
					    array(':parent_id' => $model->selectedSensor)), 'unit_id', 'unit_name'),
			    array(
				'empty' => 'Select',));
		    ?>
		    <?php echo $form->error($model, 'unit_list'); ?>
		</div>

		<?php
		    Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
		    $this->widget('CJuiDateTimePicker', array(
			'model' => $model, //Model object
			'attribute' => 'chosenStartDate', //attribute name
			'mode' => 'datetime', //use "time","date" or "datetime" (default)
			'options' => array("dateFormat" => 'yy/mm/dd'), // jquery plugin options
			'language' => ''
		    ));
		?>

		<?php
		    Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
		    $this->widget('CJuiDateTimePicker', array(
			'model' => $model, //Model object
			'attribute' => 'chosenEndDate', //attribute name
			'mode' => 'datetime', //use "time","date" or "datetime" (default)
			'options' => array("dateFormat" => 'yy/mm/dd'), // jquery plugin options
			'language' => ''
		    ));
		?>
    		<div class="row buttons">
		    <?php echo CHtml::submitButton('Submit', array('name' => 'submit_button')); ?>
		</div>

		<?php
		    if ($model->submitedForm) {
			//ako je ispravno postavljeno sve od strane korisnika, ispisujemo report, u protivnom javljamo gresku
			//and then magic occurs bi bila tocna definicija, ako smo do tud dosli, krecemo sa reportom
			$gsn_row = DiGsn::model()->find(array('select' => 'gsn_name, gsn_ip, city', 'condition' => 'gsn_id=' . $model->selectedGsn));

			echo "GSN server: " . $gsn_row['gsn_name'] . ", from " . $gsn_row['city'];

			$sensor_row = DiSensors::model()->find(array(
				    'select' => 'sensor_user_name, location_x, location_y, sensor_type',
				    'condition' => 'sensor_id = ' . $model->selectedSensor));
			echo "<br/>Sensor: " . $sensor_row['sensor_user_name'] . ", at location<br/>Latitude: " . $sensor_row['location_x'] . "<br/>Longitude: " . $sensor_row['location_y'];
			echo "<br/>Sensor type: " . $sensor_row['sensor_type'];

			Yii::import('application.extensions.EGMap.*');

			$gMap = new EGMap();
			$gMap->setWidth(200);
// it can also be called $gMap->height = 400;
			$gMap->setHeight(200);
			$gMap->zoom = 10;
			$mapTypeControlOptions = array(
			    'position' => EGMapControlPosition::LEFT_BOTTOM,
			    'style' => EGMap::MAPTYPECONTROL_STYLE_DROPDOWN_MENU
			);

			$gMap->mapTypeControlOptions = $mapTypeControlOptions;

			$gMap->setCenter($sensor_row['location_y'], $sensor_row['location_x']);

// Create GMapInfoWindows
			$info_window_a = new EGMapInfoWindow('<div>This is the location of the chosen sensor</div>');
			//$info_window_b = new EGMapInfoWindow('Hey! I am a marker with label!');

			$icon = new EGMapMarkerImage("http://mapicons.nicolasmollet.com/wp-content/uploads/mapicons/shape-default/color-128e4d/shapecolor-color/shadow-1/border-dark/symbolstyle-white/symbolshadowstyle-dark/gradient-no/water.png");

			$icon->setSize(32, 37);
			$icon->setAnchor(16, 16.5);
			$icon->setOrigin(0, 0);

// Create marker
			$marker = new EGMapMarker($sensor_row['location_y'], $sensor_row['location_x'], array('title' => $sensor_row['sensor_user_name'], 'icon' => $icon));
			$marker->addHtmlInfoWindow($info_window_a);
			$gMap->addMarker($marker);

// Create marker with label
			//$marker = new EGMapMarkerWithLabel(39.821089311812094, 2.90165944519042, array('title' => 'Marker With Label'));
//                    $label_options = array(
//                        'backgroundColor' => 'yellow',
//                        'opacity' => '0.75',
//                        'width' => '100px',
//                        'color' => 'blue'
//                    );

			/*
			  // Two ways of setting options
			  // ONE WAY:
			  $marker_options = array(
			  'labelContent'=>'$9393K',
			  'labelStyle'=>$label_options,
			  'draggable'=>true,
			  // check the style ID
			  // afterwards!!!
			  'labelClass'=>'labels',
			  'labelAnchor'=>new EGMapPoint(22,2),
			  'raiseOnDrag'=>true
			  );

			  $marker->setOptions($marker_options);
			 */

// SECOND WAY:
//                    $marker->labelContent = '$425K';
//                    $marker->labelStyle = $label_options;
//                    $marker->draggable = true;
//                    $marker->labelClass = 'labels';
//                    $marker->raiseOnDrag = true;
//
//                    $marker->setLabelAnchor(new EGMapPoint(22, 0));
//
//                    $marker->addHtmlInfoWindow($info_window_b);
			//$gMap->addMarker($marker);
// enabling marker clusterer just for fun
// to view it zoom-out the map
			$gMap->enableMarkerClusterer(new EGMapMarkerClusterer());

			$gMap->renderMap();
		    }
		?>
		<?php $this->endWidget(); ?>
    	    </div>
    	</div>

    	<div class="span-15 last">
	    <?php
		    //$model->displayReport($model->selectedGsn, $model->selectedSensor, $model->selectedDate, $model->submitedForm);

		    if ($model->submitedForm) {
			/*
			  echo "<br/>GSN: ".$model->selectedGsn.
			  "<br/>Sensor: ".$model->selectedSensor.
			  "<br/>Unit: ".$model->selectedUnit.
			  "<br/>StartTime: ".$model->selectedStartDate.
			  "<br/>EndTime: ".$model->selectedEndDate; */

			//ako je ispravno postavljeno sve od strane korisnika, ispisujemo report, u protivnom javljamo gresku
			//and then magic occurs bi bila tocna definicija, ako smo do tud dosli, krecemo sa reportom
			$gsn_row = DiGsn::model()->find(array('select' => 'gsn_name, gsn_ip, city', 'condition' => 'gsn_id=' . $model->selectedGsn));

			echo "GSN server: " . $gsn_row['gsn_name'] . ", from " . $gsn_row['city'];

			$sensor_row = DiSensors::model()->find(array(
				    'select' => 'sensor_user_name, location_x, location_y, sensor_type',
				    'condition' => 'sensor_id = ' . $model->selectedSensor));
			echo "<br/>Sensor: " . $sensor_row['sensor_user_name'] . ", at location => latitude: " . $sensor_row['location_x'] . ", longitude: " . $sensor_row['location_y'];
			echo "<br/>Sensor type: " . $sensor_row['sensor_type'];

			$sensor_units = DiUnits::model()->find(array(
				    'select' => 'unit_name, unit_mark, unit_id',
				    'condition' => 'unit_id = ' . $model->selectedUnit));




			$aggregated_data = Yii::app()->db->createCommand()
					->select('r.value, r.reading_id, r.date_id, r.time_id, r.time_of_the_reading')
					->from('(select * from f_readings union all select * from l_readings) r')
					->where('r.sensor_id=:sensor_id and r.unit_id=:unit_id and time_of_the_reading between :start and :end', array(':sensor_id' => $model->selectedSensor, ':unit_id' => $model->selectedUnit, ':start' => $model->selectedStartDate, 'end' => $model->selectedEndDate))
					->order('r.time_of_the_reading asc')
					->queryAll();

			//array presenting part of the day
			$heading_first = array();
			$values_first = array();
			$data_span = 0;

			foreach ($aggregated_data as $row) {
			    array_push($heading_first, new DateTime($row['time_of_the_reading']));
			    array_push($values_first, (double) number_format($row['value'], 2));
			    //$data_span = max($row['max_value'], $data_span);
			    $data_span++;
			}

			if ($data_span > 20)
			    $data_interval = round($data_span / 20, 0);
			else
			    $data_interval = 1;

			$pom = 1;
			$values = array();
			$heading = array();

			//$values[0] = 0;
			$k = 0;
			array_push($values, $values_first[0]);

			for ($i = 1; $i < $data_span; $i++) {
			    $pom++;
			    if ($pom >= $data_interval) {
				$values[$k]/=$data_interval;
				$pom = 0;
				$k++;
				array_push($heading, $heading_first[$i]->format('Y-m-d H:i:s'));
				array_push($values, 0);
			    }
			    else
				$values[$k] += $values_first[$i];
			}

			if ($pom != 0)
			    $values[$k] /= $pom;

			array_pop($values);
			/*
			  $interval = date_diff($heading[$data_span], $heading[0]);

			  echo "<br/>Interval: ". (int)($interval->format('%s'))."<br/>";
			  echo "<br/>Min: ". $heading[0]->format('Y-m-d H:i:s')."<br/>";
			  echo "<br/>Max: ". $heading[$data_span]->format('Y-m-d H:i:s')."<br/>"; */

			$this->Widget('application.extensions.highcharts.HighchartsWidget', array(
			    'options' => array(
				'theme' => 'dark-blue',
				'chart' => array(
				    'width' => '600',
				    'height' => '400',
				),
				'title' => array('text' => 'Data measured for ' . $sensor_units['unit_name']),
				'xAxis' => array(
				    'categories' => $heading,
				    'labels' => array('rotation' => 40, 'step' => 2, 'y' => 50),
				),
				'yAxis' => array(
				    'title' => array('text' => $sensor_units['unit_name']),
				//'max' => 20,
				),
				'plotOptions' => array(
				    'line' => array(
					'dataLabels' => array('enabled' => false),
				    //'enableMouseTracking' => false
				)),
				'series' => array(
				    array('name' => 'Values',
					'data' => $values)),
			    )
				)
			);

//                        $this->widget('application.extensions.cvisualizewidget.CVisualizeWidget', array(
//                            'data' => array(
//                                'headings' => $day_part,
//                                'data' => array(
//                                   // 'DATA' => array($data_span * 1.2, $data_span * 1.2, $data_span * 1.2, $data_span * 1.2, $data_span * 1.2),
//                                    'Maximum' => $max_values,
//                                    'Minimum' => $min_values,
//                                    'Average' => $avg_values,
//                                )
//                            ),
//                            'options' => array(
//                                'title' => 'Average data for ' . $unit['unit_name'],
//                                'width' => 400,
//                                'height' => 200
//                            )
//                        ));
		    }
	    ?>

	</div><!-- form -->
    </div>
</div>
