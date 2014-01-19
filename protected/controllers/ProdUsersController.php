<?php

class ProdUsersController extends Controller {

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/admin_main';

    /**
     * @return array action filters
     */
    public function filters() {
	return array(
	    'accessControl', // perform access control for CRUD operations
	);
    }

    public function actions() {

	return array(
	    // captcha action renders the CAPTCHA image displayed on the contact page
	    'captcha' => array(
		'class' => 'CCaptchaAction',
		'backColor' => 0xFFFFFF,
	    ),
	);
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     *//*
      public function accessRules() {
      return array(
      array('allow', // allow all users to perform 'index' and 'view' actions
      'actions' => array('index', 'view'),
      'users' => array('*'),
      ),
      array('allow', // allow authenticated user to perform 'create' and 'update' actions
      'actions' => array('create', 'update'),
      'users' => array('@'),
      ),
      array('allow', // allow admin user to perform 'admin' and 'delete' actions
      'actions' => array('admin', 'delete'),
      'users' => array('@'),
      ),
      array('deny', // deny all users
      'users' => array('*'),
      ),
      );
      } */

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id) {
	$this->render('view', array(
	    'model' => $this->loadModel($id),
	));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {
	$model = new ProdUsers;

	// Uncomment the following line if AJAX validation is needed
	$this->performAjaxValidation($model);

	if (isset($_POST['ProdUsers'])) {
	    $model->attributes = $_POST['ProdUsers'];
	    if ($model->save())
		$this->redirect(array('view', 'id' => $model->user_id));
	}

	$this->render('create', array(
	    'model' => $model,
	));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {
	$model = $this->loadModel($id);

	// Uncomment the following line if AJAX validation is needed
	// $this->performAjaxValidation($model);

	if (isset($_POST['ProdUsers'])) {
	    $model->attributes = $_POST['ProdUsers'];
	    if ($model->save())
		$this->redirect(array('view', 'id' => $model->user_id));
	}

	$this->render('update', array(
	    'model' => $model,
	));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
	if (Yii::app()->request->isPostRequest) {
	    // we only allow deletion via POST request
	    $this->loadModel($id)->delete();

	    // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
	    if (!isset($_GET['ajax']))
		$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}
	else
	    throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Lists all models.
     */
    public function actionIndex() {
	$dataProvider = new CActiveDataProvider('ProdUsers', array('criteria' => array('order' => 'user_id')));
	$this->render('index', array(
	    'dataProvider' => $dataProvider,
	));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
	$model = new ProdUsers('search');
	$model->unsetAttributes();  // clear any default values
	if (isset($_GET['ProdUsers']))
	    $model->attributes = $_GET['ProdUsers'];

	$this->render('admin', array(
	    'model' => $model,
	));
    }

    /*
     * Takes care of user activation and revoking rights on login
     */

    public function actionUserActivation() {
	$model = new ProdUsers;
	//$model->unsetAttributes();  // clear any default values

	if (isset($_GET['user_id'])) {
	    $model = ProdUsers::model()->findByPk($_GET['user_id']);

	    if ($model->active == '1') {
		$model->active = '0';
	    } else {
		$model->active = '1';
	    }

	    if ($model->save(false))
		$this->render('userActivation', array(
		    'model' => $model,
		));
	}

	$this->render('userActivation', array(
	    'model' => $model,
	));
    }

    public function actionUserGroup() {
	$model = new ProdUsers;
	//$model->unsetAttributes();  // clear any default values

	if (isset($_GET['user_id'])) {
	    $model = ProdUsers::model()->findByPk($_GET['user_id']);

	    if ($model->group_id == '1') {
		$model->group_id = '0';
	    } else {
		$model->group_id = '1';
	    }

	    if ($model->save(false))
		$this->render('userActivation', array(
		    'model' => $model,
		));
	}

	$this->render('userActivation', array(
	    'model' => $model,
	));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
	$model = ProdUsers::model()->findByPk($id);
	if ($model === null)
	    throw new CHttpException(404, 'The requested page does not exist.');
	return $model;
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
