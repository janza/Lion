<?php

/**
 * This is the model class for table "prod_sms_watchdog_timer".
 *
 * The followings are the available columns in table 'prod_sms_watchdog_timer':
 * @property integer $watchdog_id
 * @property integer $time_watchdog_asked
 * @property integer $time_watchdog_approved
 * @property string $is_active
 * @property integer $sensor_id
 * @property integer $user_id
 * @property string $sensor_name
 * @property string $phone
 * @property string $xml_name
 * @property string $critical_period
 * @property string $period_script
 * @property string $minimal_delay_between_emails
 */
class ProdSmsWatchdogTimer extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProdSmsWatchdogTimer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'prod_sms_watchdog_timer';
	}
	
	public function beforeSave()
	{
		$headers = "From: " . Yii::app()->params['adminEmail'] . "\r\nReply-To: " . Yii::app()->params['adminEmail'];
        $subject = "SMS watchdog timer before save";
        $body = "";
        
        $body .= "\n\nSMS Watchdog timer transfering process started! Time: " . date('Y-m-d H:i:s');
        
        if ($this->isNewRecord) {
            $this->time_watchdog_asked = new CDbExpression('NOW()');
            $this->is_active = '0';
            //$body = "New notification has been saved!";
        } else {
//            if ($this->is_active == '0')
//                $this->is_active = '1';
//            else
//                $this->is_active = '0';
        }

        //if this watchdog request is turned on
        if ($this->is_active == '1') {
            $this->time_watchdog_approved = new CDbExpression('NOW()');

            $returned_xml = null;
            try {
				$returned_xml = utf8_encode($this->sms_xml_file($this->watchdog_id));
            } catch (Exception $e) {
                $body = $body . "\nThere was an error when creating XML file!\n No changes have been made!\n";
                exit(-1);
            }

            if ($returned_xml != null) {
                $gsn_row = DiGsn::model()->find(array('select' => 'gsn_name, gsn_ip, notification_folder, notification_backup_folder, sftp_username, sftp_password', 'condition' => 'gsn_id=' . $this->gsn_id));

                if ($gsn_row != null) {
                    try {
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
                            $sftp_obj->sendFile($returned_xml, $this->xml_name . "Monitor.xml");
                            $body .= "\nNotification backup has been successfully deployed on the given location!\nNotification ID: " . $this->notification_id;
                        } catch (Exception $er) {
                            $body .= "\nNotification backup has not been successfully deployed on the given location!\nError message: " . $er->getMessage();
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
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('time_watchdog_asked, time_watchdog_approved, sensor_id, user_id', 'numerical', 'integerOnly'=>true),
			array('is_active', 'length', 'max'=>1),
			array('sensor_name', 'length', 'max'=>40),
			array('phone', 'length', 'max'=>100),
			array('xml_name', 'length', 'max'=>150),
			array('critical_period, period_script, minimal_delay_between_emails', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('watchdog_id, time_watchdog_asked, time_watchdog_approved, is_active, sensor_id, user_id, sensor_name, phone, xml_name, critical_period, period_script, minimal_delay_between_emails', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'watchdog_id' => 'Watchdog',
			'time_watchdog_asked' => 'Time Watchdog Asked',
			'time_watchdog_approved' => 'Time Watchdog Approved',
			'is_active' => 'Is Active',
			'sensor_id' => 'Sensor',
			'user_id' => 'User',
			'sensor_name' => 'Sensor Name',
			'phone' => 'Phone',
			'xml_name' => 'Xml Name',
			'critical_period' => 'Critical Period',
			'period_script' => 'Period Script',
			'minimal_delay_between_emails' => 'Minimal Delay Between Emails',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('watchdog_id',$this->watchdog_id);
		$criteria->compare('time_watchdog_asked',$this->time_watchdog_asked);
		$criteria->compare('time_watchdog_approved',$this->time_watchdog_approved);
		$criteria->compare('is_active',$this->is_active,true);
		$criteria->compare('sensor_id',$this->sensor_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('sensor_name',$this->sensor_name,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('xml_name',$this->xml_name,true);
		$criteria->compare('critical_period',$this->critical_period,true);
		$criteria->compare('period_script',$this->period_script,true);
		$criteria->compare('minimal_delay_between_emails',$this->minimal_delay_between_emails,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function sms_xml_file($id) 
	{
        $sms_notification = new ProdSmsWatchdogTimer;
        $sms_notification = ProdSmsWatchdogTimer::model()->findByAttributes(array('watchdog_id' => $id));

        $sensor_data = new DiSensors();
        $sensor_data = DiSensors::model()->findByAttributes(array('sensor_id' => $email_notification['sensor_id']));

        $gsn_data = new DiGsn();
        $gsn_data = DiGsn::model()->findByAttributes(array('gsn_id' => $sensor_data['gsn_id']));

        $user_data = new ProdUsers();
        $user_data = ProdUsers::model()->findByAttributes(array('user_id' => $email_notification['user_id']));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
                '<virtual-sensor name="' . $sms_notification['xml_name'].'" priority="10">' . "\n" .
                '  <processing-class>' . "\n" .
                '    <class-name>gsn.processor.ScriptletProcessor</class-name>' . "\n" .
                '    <init-params>' . "\n" .
                '      <param name="persistant">false</param>' . "\n" .
                '      <param name="notification-state">1</param>' . "\n" .
                '      <param name="scriptlet"><![CDATA[' . "\n" .
                '                    //this is a start of a scriptlet' . "\n" .
                '                    //data definition' . "\n" .
                '' . "\n" .
                '                    lastProcessedTime = System.currentTimeMillis();  '. "\n" .
                '                    def filePath ="virtual-sensors/' . $sms_notification['xml_name'] . '.xml";' . "\n" .
                '                    def recipients = ["' . $sms_notification['phone'] . '"]; // Define one or more recipients' . "\n" .
                '                    def criticalValue = ' . $sms_notification['critical_period'] . ';' . "\n" .
                '' . "\n" .
                '' . "\n" .
                '                    def smsContent="";' . "\n" .
                ' ' . "\n" .
                '                    //end of data definition' . "\n" .
                ' ' . "\n" .
                ' ' . "\n" .
                '					 if (notificationState == 0){ '. "\n" .
                '    	             	updateNotificationVSXMLState(filePath, 1); '. "\n" .
                '                       smsContent=\'Korisnice '.$user_data['first_name'].' '.$user_data['last_name'].',\n nadzor senzora \' + sensorName + \' je ponovno ukljucen!!!\'; '."\n".
                '                       sendSMS(recipients, smsContent); '. "\n" .
                '                    }else if (notificationState == 2){  '."\n".
                '    	                updateNotificationVSXMLState(filePath, 1); '."\n".
                '                       smsContent=\'Korisnice '.$user_data['first_name'].' '.$user_data['last_name'].',\n senzor \' + sensorName + \' je proradio!!!\'; '."\n".
                '                       sendSMS(recipients, smsContent); '."\n".
                '                    }]]></param>' . "\n" .
                '      <param name="sensor-name">'.$sensor_data['sensor_name'].'</param>'. "\n" .
                '      <param name="last-error-message-time">0</param>'. "\n" .
				'	   <param name="delay">'.$sms_notification['minimal_delay_between_emails'].'</param>'. "\n" .
                '      <param name="period">'.$sms_notification['critical_period'].'</param>'. "\n" .
				'	   <param name="scriplet-periodic"><![CDATA['. "\n".
				'                    def filePath ="virtual-sensors/' . $sms_notification['xml_name'] . '.xml";' . "\n" .
				'                    def recipients = ["' . $sms_notification['phone'] . '"]; // Define one or more recipients' . "\n" .
                '                    def smsContent=\'\';'."\n".
                '                    def currentTime = System.currentTimeMillis();'."\n\n".
                '                    if ( ! isdef(\'lastProcessedTime\')) { '. "\n".
                '                          lastProcessedTime = currentTime;'."\n".
                '                    }'."\n".
                '                    else {'."\n".
                '                          def timeDifference = currentTime - lastProcessedTime;'."\n".
                '                          def timeDifferenceMinutes = timeDifference/60000;'."\n".         
                '                          def timeDifferenceSecunds = timeDifference%60000;'."\n".
                '                          def criticalPeriodMinutes = criticalPeriod/60000;'."\n".
                '                          def criticalPeriodSecunds = criticalPeriod%60000;'."\n".
                '                          if (timeDifference >= criticalPeriod) {'."\n".
                '        	                      switch(notificationState){'"\n".
                '        			                  case 0: break;'."\n".      			
                '        			                  case 1: '."\n".
                '            				                    smsContent = \'Korisnice '.$user_data['first_name'].' '.$user_data['last_name'].',\n senzor \' + sensorName + \' nije primio poruku \' + timeDifferenceMinutes + \' min\' + timeDifferenceSecunds \'s !!!\nKriticni period je \' +criticalPeriodMinutes + \'min\' + criticalPeriodSecunds \'s !\';'."\n".                                                     
				'												sendSMS(recipients, smsContent);'."\n".
				'												updateNotificationVSXMLState(filePath, 2);'."\n".
				'												updateNotificationVSXMLErrorMessageTime(filePath, currentTime);'."\n".
				'												break;'"\n".	
                '            		                  case 2: '."\n".
				'            				                  smsContent = \'Korisnice '.$user_data['first_name'].' '.$user_data['last_name'].',\n senzor \' + sensorName + \' nije primio poruku \' + timeDifferenceMinutes + \' min\' + timeDifferenceSecunds \'s !!!\nKriticni period je \' +criticalPeriodMinutes + \'min\' + criticalPeriodSecunds \'s !\nObavijesti mozete iskljuciti na linku http://www.gsn.com?watchdog_id='.$id.'\';'."\n". 
                '            				                  if((currentTime-lastErrorMessageTime) >= delay ){'."\n".
                '                                               sendSMS(recipients, smsContent);'."\n".
                '                                               updateNotificationVSXMLErrorMessageTime(filePath, currentTime);'."\n".
                '            				                  }'."\n".
                '            				                  break;'."\n".                    	
                '        	                       }'."\n".
                '                          }'."\n".   
                '                      }]]></param>'."\n".
                '    </init-params>' . "\n" .
                '    <output-structure />' . "\n" .
                '  </processing-class>' . "\n" .
                '  <description>                   </description>' . "\n" .
                '  <addressing />' . "\n" .
                '  <storage history-size="1" />' . "\n" .
                '  <streams>' . "\n" .
                '    <stream name="stream1">' . "\n" .
                '      <source alias="source1" storage-size="1" sampling-rate="1">' . "\n" .
                '        <address wrapper="local">' . "\n" .
                '          <predicate key="name">'.$sensor_data['sensor_name'].'</predicate>' . "\n" .
                '        </address>' . "\n" .
                '        <query>select * from wrapper</query>' . "\n" .
                '      </source>' . "\n" .
                '      <query>select * from source1</query>' . "\n" .
                '    </stream>' . "\n" .
                '  </streams>' . "\n" .
                '</virtual-sensor>';
        return $xml;
    }
	
	
	
}