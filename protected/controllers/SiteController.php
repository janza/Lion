<?php

class SiteController extends Controller {

    public $layout = 'main';

    /**
     * Declares class-based actions.
     */
    public function actions() {
	return array(
	    // captcha action renders the CAPTCHA image displayed on the contact page
	    'captcha' => array(
		'class' => 'CCaptchaAction',
		'backColor' => 0xFFFFFF,
	    ),
	    // page action renders "static" pages stored under 'protected/views/site/pages'
	    // They can be accessed via: index.php?r=site/page&view=FileName
	    'page' => array(
		'class' => 'CViewAction',
	    ),
	);
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {
	//$this->layout = 'temp_two_columns';
	// renders the view file 'protected/views/site/index.php'
	// using the default layout 'protected/views/layouts/main.php'
	if (Yii::app()->user->isGuest){
	    $this->render('index');
	}
	else if (Yii::app()->user->group ==1){
	    $this->redirect(array('/admin/index')); //if we have administrator in the system
	}
	else if ($model->validate() && $model->login() && Yii::app()->user->group != 1){
		$this->redirect(array('/user/userPersonal')); //if we have a normal person doing a log in
	} else {
    	$this->render('index');
        }

    }


    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
	if ($error = Yii::app()->errorHandler->error) {
	    if (Yii::app()->request->isAjaxRequest)
		echo $error['message'];
	    else
		$this->render('error', $error);
	}
    }

    /**
     * Displays the contact page
     */
    public function actionContact() {
	$model = new ContactForm;
	$model->message = "<b>If you have business inquiries or other questions, please fill out the following form to contact us. Thank you in advance.</b>";

	if (isset($_POST['ContactForm'])) {
	    $model->attributes = $_POST['ContactForm'];
	    if ($model->validate()) {
		$headers = "From: {$model->email}\r\nReply-To: {$model->email}";
		mail(Yii::app()->params['adminEmail'], $model->subject, $model->body, $headers);
		//Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
		$model->message = "<b>Your message has successfully been sent! Thank you for your interest, we will try to repond as soon as possible.<b>";
		$model->body = "Do you have some more questions?";
		$model->subject = "";
		$this->render('contact', array('model' => $model));
	    }
	}
	$this->render('contact', array('model' => $model));
    }

    /**
     * Displays the login page
     */
    public function actionLogin() {
	$model = new LoginForm;

	// if it is ajax validation request
	if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
	    echo CActiveForm::validate($model);
	    Yii::app()->end();
	}

	// collect user input data
	if (isset($_POST['LoginForm'])) {
	    $model->attributes = $_POST['LoginForm'];
	    // validate user input and redirect to appropriate site according to the group
	    if ($model->validate() && $model->login() && Yii::app()->user->group == 1)
	    //$this->redirect(Yii::app()->user->returnUrl);
		$this->redirect(array('/admin/index')); //if we have administrator in the system
 else if ($model->validate() && $model->login() && Yii::app()->user->group != 1)
		$this->redirect(array('/user/userPersonal')); //if we have a normal person doing a log in
	}
	// display the login form
	$this->render('login', array('model' => $model));
    }

    /*
      public function accessRules ()
      {
      return array('allow', // allow authenticated user to perform 'create' and 'update' actions
      'actions'=>array('create','update','dynamiccities'),
      'users'=>array('@'),
      );
      }
     */

    public function actionRegistrationForm() {
	$model = new ProdUsers;

	$this->performAjaxValidation($model);

	if (isset($_POST['ProdUsers'])) {
	    $model->attributes = $_POST['ProdUsers'];
	    $isNew = ProdUsers::model()->findByAttributes(array('username' => $model->username));

	    if (strcmp($model->password, $_POST['ProdUsers']['PasswordConfirm']) != 0) {
		$model->passwdMessage = "You have provided different passwords, please try again!";
		$this->render('registrationForm', array('model' => $model));
	    }

	    if (!empty($isNew)) {
		$model->message = "This username is already taken, please choose another!";
		$this->render('registrationForm', array('model' => $model));
	    }

	    if ($model->save())
		$this->redirect(array('site/login'));
	}

	$this->render('registrationForm', array('model' => $model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout() {
	Yii::app()->user->logout();
	$this->redirect(Yii::app()->homeUrl);
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model) {
	if (isset($_POST['ajax']) && $_POST['ajax'] === 'prod-users-form') {
	    echo CActiveForm::validate($model);
	    Yii::app()->end();
	}
    }

}
