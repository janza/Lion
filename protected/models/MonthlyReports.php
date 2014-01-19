<?php

/**
 * This is the model class for table "monthly_reports".
 *
 * The followings are the available columns in table 'monthly_reports':
 * @property integer $report_id
 * @property string $is_active
 * @property integer $sensor_id
 * @property integer $gsn_id
 * @property integer $user_id
 * @property string $email
 *
 * The followings are the available model relations:
 * @property ProdUsers $user
 * @property DiSensors $sensor
 * @property DiGsn $gsn
 */
class MonthlyReports extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return MonthlyReports the static model class
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
		return 'monthly_reports';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sensor_id, gsn_id, user_id', 'numerical', 'integerOnly'=>true),
			array('is_active', 'length', 'max'=>1),
			array('email', 'length', 'max'=>100),
                        array('sensor_id, gsn_id, user_id, email','required'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('report_id, is_active, sensor_id, gsn_id, user_id, email', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'ProdUsers', 'user_id'),
			'sensor' => array(self::BELONGS_TO, 'DiSensors', 'sensor_id'),
			'gsn' => array(self::BELONGS_TO, 'DiGsn', 'gsn_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'report_id' => 'Report',
			'is_active' => 'Is Active',
			'sensor_id' => 'Sensor',
			'gsn_id' => 'Gsn',
			'user_id' => 'User',
			'email' => 'Email',
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

		$criteria->compare('report_id',$this->report_id);
		$criteria->compare('is_active',$this->is_active,true);
		$criteria->compare('sensor_id',$this->sensor_id);
		$criteria->compare('gsn_id',$this->gsn_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('email',$this->email,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

        public function beforeSave(){
	    	$headers = "From: " . Yii::app()->params['adminEmail'] . "\r\nReply-To: " . Yii::app()->params['adminEmail'];
	$subject = "Monthly report";
	$start = "Time: " . date('Y-m-d H:i:s');
            if ($this->isNewRecord) {
                $this->is_active = '0';
            } else {
		mail("hyracoidea@gmail.com", $subject, $start . "\n" ."Saving finished for".$this->report_id."! Activity ".$this->is_active."! Time: " . date('Y-m-d H:i:s'), $headers);

            }

            return parent::beforeSave();
        }
}