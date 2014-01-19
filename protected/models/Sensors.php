<?php

/**
 * This is the model class for table "di_sensors".
 *
 * The followings are the available columns in table 'di_sensors':
 * @property integer $sensor_id
 * @property string $sensor_name
 * @property string $sensor_user_name
 * @property integer $gsn_id
 * @property string $sensor_type
 * @property double $location_x
 * @property double $location_y
 * @property string $date_activated_id
 * @property string $date_deactivated_id
 * @property string $is_active
 * @property string $is_dummy
 * @property string $is_real_sensor
 *
 * The followings are the available model relations:
 * @property FSensorType[] $fSensorTypes
 * @property FReadings[] $fReadings
 * @property ProdSmsNotifications[] $prodSmsNotifications
 * @property AggDay[] $aggDays
 * @property ProdEmailNotifications[] $prodEmailNotifications
 * @property DiGsn[] $diGsns
 * @property DiDays $dateActivated
 * @property DiDays $dateDeactivated
 * @property DiGsn $gsn
 * @property AggDayPart[] $aggDayParts
 * @property AggMonthDayPart[] $aggMonthDayParts
 */
class Sensors extends CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Sensors the static model class
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
		return 'di_sensors';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('gsn_id', 'numerical', 'integerOnly'=>true),
			array('location_x, location_y', 'numerical'),
			array('sensor_name, sensor_user_name', 'length', 'max'=>30),
			array('sensor_type', 'length', 'max'=>20),
			array('is_active, is_dummy, is_real_sensor', 'length', 'max'=>1),
			array('date_activated_id, date_deactivated_id', 'safe'),
			array('fSensorTypes', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('fSensorTypes','sensor_id, sensor_name, sensor_user_name, gsn_id, sensor_type, location_x, location_y, date_activated_id, date_deactivated_id, is_active, is_dummy, is_real_sensor', 'safe', 'on'=>'search'),
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
			'sensorTypes' => array(self::HAS_MANY, 'SensorType', 'sensor_id'),
			'diGsn' => array(self::HAS_ONE, 'DiGsn', array('gsn_id'=>'gsn_id')),
			'units'=>array(self::HAS_MANY,'Units',array('unit_id'=>'unit_id'),'through'=>'sensorTypes'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'sensor_id' => 'Sensor',
			'sensor_name' => 'Sensor Name',
			'sensor_user_name' => 'Sensor User Name',
			'gsn_id' => 'Gsn',
			'sensor_type' => 'Sensor Type',
			'location_x' => 'Location X',
			'location_y' => 'Location Y',
			'date_activated_id' => 'Date Activated',
			'date_deactivated_id' => 'Date Deactivated',
			'is_active' => 'Is Active',
			'is_dummy' => 'Is Dummy',
			'is_real_sensor' => 'Is Real Sensor',

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

		$criteria->compare('sensor_id',$this->sensor_id);
		$criteria->compare('sensor_name',$this->sensor_name,true);
		$criteria->compare('sensor_user_name',$this->sensor_user_name,true);
		$criteria->compare('gsn_id',$this->gsn_id);
		$criteria->compare('sensor_type',$this->sensor_type,true);
		$criteria->compare('location_x',$this->location_x);
		$criteria->compare('location_y',$this->location_y);
		$criteria->compare('date_activated_id',$this->date_activated_id,true);
		$criteria->compare('date_deactivated_id',$this->date_deactivated_id,true);
		$criteria->compare('is_active',$this->is_active,true);
		$criteria->compare('is_dummy',$this->is_dummy,true);
		$criteria->compare('is_real_sensor',$this->is_real_sensor,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}