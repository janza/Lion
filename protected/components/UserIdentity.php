<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {

    /**
     * Authenticates a user.
     * The example implementation makes sure if the username and password
     * are both 'demo'.
     * In practical applications, this should be changed to authenticate
     * against some persistent user identity storage (e.g. database).
     * @return boolean whether authentication succeeds.
     */
    private $_id;
    private $_name;

    /*
      public function authenticate()
      {
      $users=array(
      // username => password
      'demo'=>'demo',
      'admin'=>'admin',
      );
      if(!isset($users[$this->username]))
      $this->errorCode=self::ERROR_USERNAME_INVALID;
      else if($users[$this->username]!==$this->password)
      $this->errorCode=self::ERROR_PASSWORD_INVALID;
      else
      {
      $this->_id=1;
      $this->setState('group', 1);
      $this->errorCode=self::ERROR_NONE;
      }
      return !$this->errorCode;
      }
     */

    public function authenticate() {

	$record = ProdUsers::model()->findByAttributes(array('username' => $this->username, 'active' => '1'));
	if ($record === null)
	    $this->errorCode = self::ERROR_USERNAME_INVALID;
	else if ($record->password !== hash('sha256', $record->salt . $this->password))
	    $this->errorCode = self::ERROR_PASSWORD_INVALID;
	else {

	    $record->last_login = new CDbExpression('NOW()');
	    //$record->password = hash('sha256', $record->salt.$this->password);
	    if ($record->save()) {
		//we have saved this change successfuly
	    }

	    $this->_id = $record->user_id;
	    $this->_name = $record->first_name . " " . $record->last_name;
	    $this->setState('group', $record->group_id);
	    $this->setState('username', $record->username);
	    $this->errorCode = self::ERROR_NONE;
	}
	return!$this->errorCode;
    }

    public function getId() {
	return $this->_id;
    }

    public function getName() {
	return $this->_name;
    }

}