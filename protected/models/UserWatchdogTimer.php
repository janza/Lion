<?php

class UserWatchdogTimer extends CFormModel
{
	//email of the recipients
	public $email;
	//phone number of the recipient
	public $phone_number;
	//verification code user needs to fill in
	public $verify_code;
	//type of the watchdog -sms/email
	public $watchdog_type;
	//minimal interval between two notifications that will be sent to the user
	public $minimal_delay_between_emails;
	//critical value on which notifications are being sent (in combination to above/below action)
	public $critical_period;
	//sensor list for user to choose from
	public $sensor_list;
	//gsn list for user to choose from
	public $gsn_list;
	//some variables for using with controller actions
	public $gsn_id;
	//id of the chosen sensor
	public $sensor_id;
	
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			// all the fields are required
			array('watchdog_type,minimal_delay_between_emails,critical_period,sensor_list, gsn_list', 'required'),
			// verifyCode needs to be entered correctly
			array('verify_code', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements()),
		);
	}

	public function attributeLabels()
	{
		return array(
			'verify_code'=>'Verification Code',
		);
	}
	
}
