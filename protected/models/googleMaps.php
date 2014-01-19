<?php

class googleMaps {

    public function allSensorsMap($width = 550, $height = 500, $center_x =45.817, $center_y = 15.983) {
	echo '<h3>Google map</h3>
	Here you can see a list of currently active sensors!<br/>';
	try {
	    Yii::import('application.extensions.EGMap.*');

	    $gMap = new EGMap();
	    $gMap->setHeight(400);
	    $gMap->setWidth(550);

	    $gMap->zoom = 7;
	    $mapTypeControlOptions = array(
		'position' => EGMapControlPosition::LEFT_BOTTOM,
		'style' => EGMap::MAPTYPECONTROL_STYLE_DROPDOWN_MENU
	    );

	    $gMap->mapTypeControlOptions = $mapTypeControlOptions;
//centar je zagreb
	    $gMap->setCenter(45.817, 15.983);

	    $all_user_sensors = Yii::app()->db->createCommand()
			    ->selectDistinct('s.*')
			    ->from('di_sensors s')
			    ->join('di_gsn_privileges p', 'p.gsn_id=s.gsn_id')
			    ->where('p.user_id=:id', array(':id' => Yii::app()->user->id))
			    ->queryAll();

	    foreach ($all_user_sensors as $sensor) {
		$info_window_a = new EGMapInfoWindow('<div>Sensor name ' . $sensor['sensor_user_name'] . '</div>');
		// Create marker for every sensor user can see
		$icon = new EGMapMarkerImage("http://161.53.67.224/lion/images/icon_advanced.png");

		$icon->setSize(40, 40);
		$icon->setAnchor(16, 16.5);
		$icon->setOrigin(0, 0);

		$marker = new EGMapMarker($sensor['location_y'], $sensor['location_x'], array('title' => "Sensor " . $sensor['sensor_user_name'], 'icon' => $icon));

		$marker->addHtmlInfoWindow($info_window_a);
		$gMap->addMarker($marker);
	    }

// enabling marker clusterer just for fun
// to view it zoom-out the map
//$gMap->enableMarkerClusterer(new EGMapMarkerClusterer());

	    $gMap->renderMap();

	    return true;
	} catch (Exception $ae) {
	    echo "ERROR occured while loading google map! If you see this error again, please contact our administrator for further support!<br/>";
	    return false;
	}
    }

    public function allSensorsWithReadingsMap($width = 550, $height = 500, $center_x =45.817, $center_y = 15.983) {
	echo '<h3>Google map</h3>
	Here you can see a list of currently active sensors with the last sensor reading we have in our local database!<br/>';

	try {
	    Yii::import('application.extensions.EGMap.*');

	    $gMap = new EGMap();
	    $gMap->setHeight($height);
	    $gMap->setWidth($width);

	    $gMap->zoom = 7;
	    $mapTypeControlOptions = array(
		'position' => EGMapControlPosition::LEFT_BOTTOM,
		'style' => EGMap::MAPTYPECONTROL_STYLE_DROPDOWN_MENU
	    );

	    $gMap->mapTypeControlOptions = $mapTypeControlOptions;
	    //centar je zagreb
	    $gMap->setCenter($center_x, $center_y);

	    $all_user_sensors = Yii::app()->db->createCommand()
			    ->selectDistinct('s.*')
			    ->from('di_sensors s')
			    ->join('di_gsn_privileges p', 'p.gsn_id=s.gsn_id')
			    ->where('p.user_id=:id', array(':id' => Yii::app()->user->id))
			    ->queryAll();

	    foreach ($all_user_sensors as $sensor) {
		$last_measured_data = Yii::app()->db->createCommand(
				'SELECT
				u.unit_name
			     ,	r.value
			     ,	r.time_of_the_reading
			     FROM
			     (
			     SELECT z.*, row_number() over (partition by z.sensor_id, z.unit_id order by time_of_the_reading desc) as rang
			     FROM
			     (
				SELECT *
				FROM l_readings
			      ) z
			      ) r
			      JOIN di_sensors s ON r.sensor_id = s.sensor_id
			      JOIN di_units u ON r.unit_id = u.unit_id
			      WHERE
				1 = 1
			      and r.rang = 1
			      and s.sensor_id = ' . $sensor['sensor_id']
			)->queryAll();
		$data = "";
		foreach ($last_measured_data as $measured_data) {
		    $data .= "<br/>" . $measured_data['unit_name'] . ": " . $measured_data['value'] . ", at " . $measured_data['time_of_the_reading'];
		}

		$info_window_a = new EGMapInfoWindow('<div>Sensor name ' . $sensor['sensor_user_name'] . $data . '</div>');
		// Create marker for every sensor user can see
		$icon = new EGMapMarkerImage("http://161.53.67.224/lion/images/icon_advanced.png");

		$icon->setSize(40, 40);
		$icon->setAnchor(16, 16.5);
		$icon->setOrigin(0, 0);

		$marker = new EGMapMarker($sensor['location_y'], $sensor['location_x'], array('title' => "Sensor " . $sensor['sensor_user_name'], 'icon' => $icon));

		$marker->addHtmlInfoWindow($info_window_a);
		$gMap->addMarker($marker);
	    }

	    // enabling marker clusterer just for fun
	    // to view it zoom-out the map
	    //$gMap->enableMarkerClusterer(new EGMapMarkerClusterer());

	    $gMap->renderMap();
	    return true;
	} catch (Exception $ae) {
	    echo "ERROR occured while loading google map! If you see this error again, please contact our administrator for further support!<br/>";
	    return false;
	}
    }

    public function sensorMap($sensor_id, $gsn_id, $width = 550, $height = 200) {
//ako je ispravno postavljeno sve od strane korisnika, ispisujemo report, u protivnom javljamo gresku
	//and then magic occurs bi bila tocna definicija, ako smo do tud dosli, krecemo sa reportom
	$gsn_row = DiGsn::model()->find(array('select' => 'gsn_name, gsn_ip, city', 'condition' => 'gsn_id=' . $gsn_id));

	$sensor_row = DiSensors::model()->find(array(
		    'select' => 'sensor_user_name, location_x, location_y, sensor_type',
		    'condition' => 'sensor_id = ' . $sensor_id));

	Yii::import('application.extensions.EGMap.*');

	$gMap = new EGMap();
	$gMap->setWidth($width);
// it can also be called $gMap->height = 400;
	$gMap->setHeight($height);
	$gMap->zoom = 15;
	$mapTypeControlOptions = array(
	    'position' => EGMapControlPosition::LEFT_BOTTOM,
	    'style' => EGMap::MAPTYPECONTROL_STYLE_DROPDOWN_MENU
	);

	$gMap->mapTypeControlOptions = $mapTypeControlOptions;

	$gMap->setCenter($sensor_row['location_y'], $sensor_row['location_x']);

// Create GMapInfoWindows
	$info_window_a = new EGMapInfoWindow('<div>This is the location of the chosen sensor</div>');
	//$info_window_b = new EGMapInfoWindow('Hey! I am a marker with label!');

	$icon = new EGMapMarkerImage("http://161.53.67.224/lion/images/icon_advanced.png");

	$icon->setSize(40, 40);
	$icon->setAnchor(16, 16.5);
	$icon->setOrigin(0, 0);

// Create marker
	$marker = new EGMapMarker($sensor_row['location_y'], $sensor_row['location_x'], array('title' => $sensor_row['sensor_user_name'], 'icon' => $icon));
	$marker->addHtmlInfoWindow($info_window_a);
	$gMap->addMarker($marker);

	$gMap->enableMarkerClusterer(new EGMapMarkerClusterer());

	$gMap->renderMap();
    }

}

?>
