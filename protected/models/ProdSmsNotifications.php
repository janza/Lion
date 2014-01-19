<?php

/**
 * This is the model class for table "prod_sms_notifications".
 *
 * The followings are the available columns in table 'prod_sms_notifications':
 * @property integer $notification_id
 * @property integer $time_notification_asked
 * @property integer $time_notification_approved
 * @property string $is_active
 * @property integer $unit_id
 * @property integer $sensor_id
 * @property integer $prod_user_id
 * @property string $unit_name
 * @property string $unit_name_upper
 * @property string $sensor_name
 * @property string $xml_name
 * @property string $phone
 * @property integer $critical_value
 * @property integer $resending_interval
 * @property integer $criteria_type
 * @property string $criteria_type_name
 *
 * The followings are the available model relations:
 * @property ProdUsers $prodUser
 * @property DiSensors $sensor
 * @property DiUnits $unit
 */
class ProdSmsNotifications extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ProdSmsNotifications the static model class
     */
    public static function model($className=__CLASS__) {
	return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
	return 'prod_sms_notifications';
    }

    public function beforeSave() {
	$headers = "From: " . Yii::app()->params['adminEmail'] . "\r\nReply-To: " . Yii::app()->params['adminEmail'];
	$subject = "SMS Notification before save";
	$body = "";
	$body .= "\n\nNotification transfering process started! Time: " . date('Y-m-d H:i:s');
	if ($this->isNewRecord) {
	    $this->time_notification_asked = new CDbExpression('NOW()');
	    $this->is_active = '0';
	    //$body = "New notification has been saved!";
	} else {
//            if ($this->is_active == '0')
//                $this->is_active = '1';
//            else
//                $this->is_active = '0';
	}

	//if this notification request is turned on
	if ($this->is_active == '1') {
	    $this->time_notification_approved = new CDbExpression('NOW()');

	    $returned_xml = null;
	    try {
		if ($this->criteria_type == 1)
		    $returned_xml = $this->sms_xml_file_below($this->notification_id);
		else if ($this->criteria_type == 0)
		    $returned_xml = $this->sms_xml_file_above($this->notification_id);
	    } catch (Exception $e) {
		$body = $body . "\nThere was an error when creating XML file!\n No changes have been made!\n";
		exit(-1);
	    }

	    if ($returned_xml != null) {
		$gsn_row = DiGsn::model()->find(array('select' => 'gsn_name, gsn_ip, notification_folder, notification_backup_folder, sftp_username, sftp_password', 'condition' => 'gsn_id=' . $this->gsn_id));

		if ($gsn_row != null) {
		    try {
			if (strcmp($gsn_row['gsn_ip'], "127.0.0.1") == 0) {
			    $file_w = fopen($gsn_row['notification_folder'] . "\\" . $this->xml_name . ".xml", 'w');
			    fwrite($file_w, $returned_xml);

			    $file_w_backup = fopen($gsn_row['notification_backup_folder'] . "\\" . $this->xml_name . ".xml", 'w');
			    fwrite($file_w_backup, $returned_xml);
			} else {
			    $sftp_obj = new SftpComponent($gsn_row['gsn_ip'], $gsn_row['sftp_username'], $gsn_row['sftp_password']);
			    $sftp_obj->connect();
			    $body.="\nNotification folder: " . $gsn_row['notification_folder'] . "\nNotification backup folder: " . $gsn_row['notification_backup_folder'];
			    try {
				if ($sftp_obj->isDir($gsn_row['notification_folder']))
				    $body.="\nNotification folder exists!\n";
				else
				    $body.="\nNotification folder does not exist!\n";


				$sftp_obj->chdir($gsn_row['notification_folder']);
				$sftp_obj->sendFile($returned_xml, $this->xml_name . ".xml");
				$body .= "\nNotification has been successfully deployed on the given location!\nNotification ID: " . $this->notification_id;
			    } catch (Exception $er) {
				$body .= "\nNotification has not been successfully deployed on the given location!\nError message: " . $er->getMessage();
			    }

			    try {
				if ($sftp_obj->isDir($gsn_row['notification_backup_folder']))
				    $body.="\nNotification backup folder exists!\n";
				else
				    $body.="\nNotification backup folder does not exist!\n";

				$sftp_obj->chdir($gsn_row['notification_backup_folder']);
				$sftp_obj->sendFile($returned_xml, $this->xml_name . ".xml");
				$body .= "\nNotification backup has been successfully deployed on the given location!\nNotification ID: " . $this->notification_id;
			    } catch (Exception $er) {
				$body .= "\nNotification backup has not been successfully deployed on the given location!\nError message: " . $er->getMessage();
			    }
			}
		    } catch (Exception $e) {
			$body.="Something went wrong with the connection.\nError message: " . $e->getMessage();
		    }
		}
		else
		    $body = $body . "Something went wrong when acquiring data for the GSN!\nNotification ID: " . $this->notification_id;
	    }
	    else
		$body = $body . "\nFor some reason XML file was not saved properly in the variable and program did not stop!Exit command does not work properly!\n";
	    $body .= "\n\nNotification transfering process finished! Time: " . date('Y-m-d H:i:s');

	    mail("hyracoidea@gmail.com", $subject, $body, $headers);
	}

	return parent::beforeSave();
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
	// NOTE: you should only define rules for those attributes that
	// will receive user inputs.
	return array(
	    array('unit_id, sensor_id, prod_user_id, critical_value, resending_interval, criteria_type', 'numerical', 'integerOnly' => true),
	    array('is_active', 'length', 'max' => 1),
	    array('unit_name, unit_name_upper, sensor_name, phone', 'length', 'max' => 40),
	    array('criteria_type_name', 'length', 'max' => 50),
	    array('xml_name', 'length', 'max' => 150),
	    // The following rule is used by search().
	    // Please remove those attributes that should not be searched.
	    array('notification_id, time_notification_asked, time_notification_approved, is_active,criteria_type, unit_id, sensor_id, prod_user_id, unit_name, unit_name_upper, sensor_name, xml_name, phone, critical_value, resending_interval', 'safe', 'on' => 'search'),
	);
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
	// NOTE: you may need to adjust the relation name and the related
	// class name for the relations automatically generated below.
	return array(
	    'prodUser' => array(self::BELONGS_TO, 'ProdUsers', 'prod_user_id'),
	    'sensor' => array(self::BELONGS_TO, 'DiSensors', 'sensor_id'),
	    'unit' => array(self::BELONGS_TO, 'DiUnits', 'unit_id'),
	);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
	return array(
	    'notification_id' => 'Notification',
	    'time_notification_asked' => 'Time Notification Asked',
	    'time_notification_approved' => 'Time Notification Approved',
	    'is_active' => 'Is Active',
	    'unit_id' => 'Unit',
	    'sensor_id' => 'Sensor',
	    'prod_user_id' => 'Prod User',
	    'unit_name' => 'Unit Name',
	    'unit_name_upper' => 'Unit Name Upper',
	    'sensor_name' => 'Sensor Name',
	    'xml_name' => 'Xml Name',
	    'phone' => 'Phone',
	    'critical_value' => 'Critical Value',
	    'resending_interval' => 'Resending Interval',
	    'criteria_type' => 'Criteria Type',
	    'criteria_type_name' => 'Criteria Type Name'
	);
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
	// Warning: Please modify the following code to remove attributes that
	// should not be searched.

	$criteria = new CDbCriteria;

	$criteria->compare('notification_id', $this->notification_id);
	$criteria->compare('time_notification_asked', $this->time_notification_asked);
	$criteria->compare('time_notification_approved', $this->time_notification_approved);
	$criteria->compare('is_active', $this->is_active, true);
	$criteria->compare('unit_id', $this->unit_id);
	$criteria->compare('sensor_id', $this->sensor_id);
	$criteria->compare('prod_user_id', $this->prod_user_id);
	$criteria->compare('unit_name', $this->unit_name, true);
	$criteria->compare('unit_name_upper', $this->unit_name_upper, true);
	$criteria->compare('sensor_name', $this->sensor_name, true);
	$criteria->compare('xml_name', $this->xml_name, true);
	$criteria->compare('phone', $this->phone, true);
	$criteria->compare('critical_value', $this->critical_value);
	$criteria->compare('resending_interval', $this->resending_interval);
	$criteria->compare('criteria_type', $this->criteria_type);
	$criteria->compare('criteria_type_name', $this->criteria_type_name);

	return new CActiveDataProvider($this, array(
	    'criteria' => $criteria,
	));
    }

    function sms_xml_file_below($id) {

	$sms_notification = new ProdSmsNotifications;
	$sms_notification = ProdSmsNotifications::model()->findByAttributes(array('notification_id' => $id));

	$sensor_data = new DiSensors();
	$sensor_data = DiSensors::model()->findByAttributes(array('sensor_id' => $sms_notification['sensor_id']));

	$gsn_data = new DiGsn();
	$gsn_data = DiGsn::model()->findByAttributes(array('gsn_id' => $sensor_data['gsn_id']));

	$user_data = new ProdUsers();
	$user_data = ProdUsers::model()->findByAttributes(array('user_id' => $sms_notification['prod_user_id']));

	$sensor_information = "Sensor @ " . $gsn_data['gsn_name'] . " - " . $gsn_data['city'] . " " .
		$gsn_data['state'] . ", latitude " . $sensor_data['location_y'] .
		", longitude " . $sensor_data['location_x'];

$xml= '<?xml version="1.0" encoding="UTF-8"?>' . "\n".
          '<virtual-sensor name="' . $sms_notification['xml_name'] . '" priority="10">'. "\n" .
          '  <processing-class>'. "\n" .
          '    <class-name>gsn.processor.ScriptletProcessor</class-name>'. "\n" .
          '    <init-params>'. "\n" .
          '      <param name="persistant">false</param>'. "\n" .
          '      <param name="notification-state">0</param>'. "\n" .
		  '      <param name="mail-state">0</param>'. "\n" .
          '      <param name="scriptlet"><![CDATA['. "\n" .
          '                    //this is a start of a scriptlet'. "\n" .
          '                    //data definition'. "\n" .
          ''. "\n" .
          '                    def delay = ' . $sms_notification['resending_interval'] . '; //time in miliseconds between resending SMS'. "\n" .
          '                    def filePath ="virtual-sensors/' . $sms_notification['xml_name'] . '.xml";'. "\n" .
          '                    def recipients = ["' . $sms_notification['phone'] . '"]; // Define one or more recipients'. "\n" .
          '                    def criticalValue = ' . $sms_notification['critical_value'] . ';'. "\n" .
          '                    def measuringUnit = "' . $sms_notification['unit_name'] . '";'. "\n" .
          ''. "\n" .
          ''. "\n" .
          '                    def state = notificationState; //notification state variable is defined in scriptlet from init-params'. "\n" .
		  '					   def mail = mailState;' . "\n" .
          '                    def nodePath ="streams/stream/query";'. "\n" .
          '                    def emailContent="";'. "\n" .
          '                    def newQuery=""; '. "\n" .
          ' '. "\n" .
          '                    //end of data definition'. "\n" .
		  '					   if(mail == 1){' . "\n" .
		  '							if(state == 0){' . "\n" .
		  '								mail = 0;' . "\n" .
		  '								updateMailState(filePath, mail);' . "\n" .
		  '							}' . "\n" .
		  '							else{' . "\n" .
		  '								state = 2;' . "\n" .
		  '							}' . "\n" .
		  '					   }' . "\n" .
          ' '. "\n" .
          '                    switch(state){'. "\n" .
          ' '. "\n" .
          '                          case 0: def delayTime = TIMED + delay;'. "\n" .
          '                                  newQuery = "select "+measuringUnit+", timed from source1 where (("+measuringUnit+"<"+criticalValue+") and (timed >" + delayTime + ")) or ("+measuringUnit+">="+criticalValue+")";'. "\n" .
          '                                  emailContent = "Warning! Measured "+measuringUnit+" dropped under "+criticalValue+".\nLast measured value: " + ' . $sms_notification['unit_name_upper'] . ' + "\nDisable notification on: http://161.53.67.224/lion/index.php/webService/NotificationDisableService?notification_id='.$id.'&type=SMS\n ' . $sensor_information . '\nColdWatch";'. "\n" .
          '                                  state++;'. "\n" .
          '                                  break;'. "\n" .
          ''. "\n" .
          '                          case 1: if (' . $sms_notification['unit_name_upper'] . '<criticalValue){'. "\n" .
          '                                          def delayTime = TIMED + delay;'. "\n" .
          '                                          newQuery = "select "+measuringUnit+", timed from source1 where (("+measuringUnit+"<"+criticalValue+") and (timed >" + delayTime + ")) or ("+measuringUnit+">="+criticalValue+")";'. "\n" .
          '                                          emailContent = "Warning! Measured "+measuringUnit+" still under "+criticalValue+".\nMeasured value: " + ' . $sms_notification['unit_name_upper'] . ' + ".\nDisable notification on: http://161.53.67.224/lion/index.php/webService/NotificationDisableService?notification_id='.$id.'&type=SMS\n ' . $sensor_information . '\nColdWatch";'. "\n" .
          '                                  }'. "\n" .
          '                                  else{'. "\n" .
          '                                       newQuery = "select "+measuringUnit+", timed from source1 where "+measuringUnit+"<"+criticalValue;'. "\n" .
          '                                       emailContent = "Everything OK! Measured "+measuringUnit+" rised over "+criticalValue+".\nMeasured value: " + ' . $sms_notification['unit_name_upper'] . ' + ". Sensor readings OK!\n ' . $sensor_information . '\nColdWatch";'. "\n" .
          '                                       state=0;'. "\n" .
          ''. "\n" .
          '                                  }'. "\n" .
          '                                  break;'. "\n" .
		  '							 case 2: if(' . $sms_notification['unit_name_upper'] . '>=criticalValue){'. "\n" .
		  '										  newQuery = "select "+measuringUnit+", timed from source1 where "+measuringUnit+"<"+criticalValue;'. "\n" .
		  '										  emailContent = "Everything OK! Measured "+measuringUnit+" rised over "+criticalValue+".\nMeasured value: " + ' . $sms_notification['unit_name_upper'] . ' + ". Sensor readings OK!\n ' . $sensor_information . '\nColdWatch";'. "\n" .
		  '										  state = 0;'. "\n" .
		  '										  mail = 0;'. "\n" .
		  '										  updateMailState(filePath, mail);'. "\n" .
		  '									 }' . "\n" .
		  '									 else{' . "\n" .
		  '										  newQuery = "select "+measuringUnit+", timed from source1 where "+measuringUnit+">="+criticalValue;' . "\n" .
		  '									 }' . "\n" .
		  '									 break;' . "\n" .
          '                    }'. "\n" .
          ''. "\n" .
		  '					   if(mail == 0 && state != 2){' . "\n" .
          '                    	   sendSMS(recipients, emailContent);'. "\n" .
		  '					   }' . "\n" .
          '                    updateNotificationVSXML(filePath,nodePath,newQuery,state);]]></param>' . "\n" .
          '    </init-params>' . "\n" .
          '    <output-structure />' . "\n" .
          '  </processing-class>' . "\n" .
          '  <description>Automatic notification VS (type 3) for sensor ' . $sms_notification['sensor_name'] . ' and user ' . $user_data['username'] . '. Triggered on less then critical value.</description>' . "\n" .
          '  <addressing />' . "\n" .
          '  <storage history-size="1" />' . "\n" .
          '  <streams>' . "\n" .
          '    <stream name="stream1">' . "\n" .
          '      <source alias="source1" storage-size="1" sampling-rate="1">' . "\n" .
          '        <address wrapper="local">' . "\n" .
          '          <predicate key="query">select * from ' . $sms_notification['sensor_name'] . '</predicate>' . "\n" .
          '        </address>' . "\n" .
          '        <query>select * from wrapper</query>' . "\n" .
          '      </source>' . "\n" .
          '      <query>select ' . $sms_notification['unit_name'] . ', timed from source1 where ' . $sms_notification['unit_name'] . '&lt;' . $sms_notification['critical_value'] . '</query>' . "\n" .
          '    </stream>' . "\n" .
          '  </streams>*/' . "\n" .
          '</virtual-sensor>';
	return $xml;
    }

    function sms_xml_file_above($id) {

	$sms_notification = new ProdSmsNotifications;
	$sms_notification = ProdSmsNotifications::model()->findByAttributes(array('notification_id' => $id));

	$sensor_data = new DiSensors();
	$sensor_data = DiSensors::model()->findByAttributes(array('sensor_id' => $sms_notification['sensor_id']));

	$gsn_data = new DiGsn();
	$gsn_data = DiGsn::model()->findByAttributes(array('gsn_id' => $sensor_data['gsn_id']));

	$user_data = new ProdUsers();
	$user_data = ProdUsers::model()->findByAttributes(array('user_id' => $sms_notification['prod_user_id']));

	$sensor_information = "Sensor @ " . $gsn_data['gsn_name'] . " - " . $gsn_data['city'] . " " .
		$gsn_data['state'] . ", latitude " . $sensor_data['location_y'] .
		", longitude " . $sensor_data['location_x'];

	$xml= '<?xml version="1.0" encoding="UTF-8"?>' . "\n".
          '<virtual-sensor name="' . $sms_notification['xml_name'] . '" priority="10">'. "\n" .
          '  <processing-class>'. "\n" .
          '    <class-name>gsn.processor.ScriptletProcessor</class-name>'. "\n" .
          '    <init-params>'. "\n" .
          '      <param name="persistant">false</param>'. "\n" .
          '      <param name="notification-state">0</param>'. "\n" .
		  '		 <param name="mail-state">0</param>'. "\n" .
          '      <param name="scriptlet"><![CDATA['. "\n" .
          '                    //this is a start of a scriptlet'. "\n" .
          '                    //data definition'. "\n" .
          ''. "\n" .
          '                    def delay = ' . $sms_notification['resending_interval'] . '; //time in miliseconds between resending SMS'. "\n" .
          '                    def filePath ="virtual-sensors/' . $sms_notification['xml_name'] . '.xml";'. "\n" .
          '                    def recipients = ["' . $sms_notification['phone'] . '"]; // Define one or more recipients'. "\n" .
          '                    def criticalValue = ' . $sms_notification['critical_value'] . ';'. "\n" .
          '                    def measuringUnit = "' . $sms_notification['unit_name'] . '";'. "\n" .
          ''. "\n" .
          ''. "\n" .
          '                    def state = notificationState; //notification state variable is defined in scriptlet from init-params'. "\n" .
		  '					   def mail = mailState;'. "\n" .
          '                    def nodePath ="streams/stream/query";'. "\n" .
          '                    def emailContent="";'. "\n" .
          '                    def newQuery=""; '. "\n" .
          ' '. "\n" .
          '                    //end of data definition'. "\n" .
          ' '. "\n" .
		  '					   if(mail == 1){' . "\n" .
		  '					   		if(state == 0){' . "\n" .
		  '								mail = 0;' . "\n" .
		  '								updateMailState(filePath, mail);' . "\n" .
		  '							}' . "\n" .
		  '							else{' . "\n" .
		  '								state = 2;' . "\n" .
		  '							}' . "\n" .
		  '					   }' . "\n" .
          '                    switch(state){'. "\n" .
          ' '. "\n" .
          '                          case 0: def delayTime = TIMED + delay;'. "\n" .
          '                                  newQuery = "select "+measuringUnit+", timed from source1 where (("+measuringUnit+">"+criticalValue+") and (timed >" + delayTime + ")) or ("+measuringUnit+"<="+criticalValue+")";'. "\n" .
          '                                  emailContent = "Warning! Measured "+measuringUnit+" rised over "+criticalValue+".\nLast measured value: " + ' . $sms_notification['unit_name_upper'] . ' + "\nDisable notification on: http://161.53.67.224/lion/index.php/webService/NotificationDisableService?notification_id='.$id.'&type=SMS\n ' . $sensor_information . '\nColdWatch";'. "\n" .
          '                                  state++;'. "\n" .
          '                                  break;'. "\n" .
          ''. "\n" .
          '                          case 1: if (' . $sms_notification['unit_name_upper'] . '>criticalValue){'. "\n" .
          '                                          def delayTime = TIMED + delay;'. "\n" .
          '                                          newQuery = "select "+measuringUnit+", timed from source1 where (("+measuringUnit+">"+criticalValue+") and (timed >" + delayTime + ")) or ("+measuringUnit+"<="+criticalValue+")";'. "\n" .
          '                                          emailContent = "Warning! Measured "+measuringUnit+" still over "+criticalValue+".\nMeasured value: " + ' . $sms_notification['unit_name_upper'] . ' + ".\nDisable notification on: http://161.53.67.224/lion/index.php/webService/NotificationDisableService?notification_id='.$id.'&type=SMS\n ' . $sensor_information . '\nColdWatch";'. "\n" .
          '                                  }'. "\n" .
          '                                  else{'. "\n" .
          '                                       newQuery = "select "+measuringUnit+", timed from source1 where "+measuringUnit+">"+criticalValue;'. "\n" .
          '                                       emailContent = "Everything OK! Measured "+measuringUnit+" dropped under "+criticalValue+".\nMeasured value: " + ' . $sms_notification['unit_name_upper'] . ' + ". Sensor readings OK!\n ' . $sensor_information . '\nColdWatch";'. "\n" .
          '                                       state=0;'. "\n" .
          ''. "\n" .
          '                                  }'. "\n" .
          '                                  break;'. "\n" .
		  '							 case 2: if(' .$sms_notification['unit_name_upper'] . '<=citicalValue){'. "\n" .
		  '										  newQuery = "select "+measuringUnit+", timed from source1 where "+measuringUnit+">"+criticalValue;'. "\n" .
		  '										  emailContent = "Everything OK! Measured "+measuringUnit+" dropped under "+criticalValue+".\nMeasured value: " + ' . $sms_notification['unit_name_upper'] . ' + ". Sensor readings OK!\n ' .$sensor_information . '\nColdWatch";'. "\n" .
		  '										  state = 0;'. "\n" .
		  '										  mail = 0;'. "\n" .
		  '										  updateMailState(filePath, mail);'. "\n" .
		  '									 }'. "\n" .
		  '									 else{'. "\n" .
		  '										  newQuery = select "+measuringUnit+", timed from source1 where "+measuringUnit+"<="criticalValue;' . "\n" .
		  '									 }'. "\n" .
		  '									 break;'. "\n" .
          '                    }'. "\n" .
          ''. "\n" .
		  '					   if(mail == 0 && state != 2){'. "\n" .
          '                        sendSMS(recipients, emailContent);'. "\n" .
		  '					   }'. "\n" .
          '                    updateNotificationVSXML(filePath,nodePath,newQuery,state);]]></param>' . "\n" .
          '    </init-params>' . "\n" .
          '    <output-structure />' . "\n" .
          '  </processing-class>' . "\n" .
          '  <description>Automatic notification VS (type 4) for sensor ' . $sms_notification['sensor_name'] . ' and user ' . $user_data['username'] . '. Triggered on greater than critical value.</description>' . "\n" .
          '  <addressing />' . "\n" .
          '  <storage history-size="1" />' . "\n" .
          '  <streams>' . "\n" .
          '    <stream name="stream1">' . "\n" .
          '      <source alias="source1" storage-size="1" sampling-rate="1">' . "\n" .
          '        <address wrapper="local">' . "\n" .
          '          <predicate key="query">select * from ' . $sms_notification['sensor_name'] . '</predicate>' . "\n" .
          '        </address>' . "\n" .
          '        <query>select * from wrapper</query>' . "\n" .
          '      </source>' . "\n" .
          '      <query>select ' . $sms_notification['unit_name'] . ', timed from source1 where ' . $sms_notification['unit_name'] . '&gt;' . $sms_notification['critical_value'] . '</query>' . "\n" .
          '    </stream>' . "\n" .
          '  </streams>*/' . "\n" .
          '</virtual-sensor>';
	return $xml;
    }

}