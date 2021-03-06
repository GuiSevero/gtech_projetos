<?php

class ClassPlanController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column1';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','typeahead','tokens','tag', 'plan'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
				array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('delete'),
				'expression'=>function($user, $rule){

						if(isset($_GET['id'])){
						$model = $this->loadModel($_GET['id']);
						return ($user->getId() == $model->id_owner);
					}else return false;

				}
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Displays a particular model to students.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionPlan($id)
	{
		$this->layout = false;

		$model = $this->loadModel($id);

		if (!$model->released) throw new CHttpException(404);

		$this->render('student',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new ClassPlan;

		$model->id_owner = Yii::app()->user->getId();

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ClassPlan']))
		{
			$model->attributes=$_POST['ClassPlan'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id_class));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ClassPlan']))
		{
			$model->attributes=$_POST['ClassPlan'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id_class));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(array('/user/view','id'=>Yii::app()->user->getId()));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$criteria = new CDbCriteria();
		$criteria->compare('released', true);
		$dataProvider=new CActiveDataProvider('ClassPlan', array('criteria'=>$criteria,));
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new ClassPlan('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ClassPlan']))
			$model->attributes=$_GET['ClassPlan'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=ClassPlan::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='class-plan-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function actionTypeahead($q=''){

		
		$command =  Yii::app()->db->createCommand();
		$select = array(
					'id_class'
				,	'tags'
				,	'title'
		);

		$command->from('class');
		//$command->params = array('query'=>$q);
		$command->where = "title ILIKE '%{$q}%' OR tags ILIKE '%{$q}%' AND released = true";
		$command->select = implode(', ', $select);
		$result =  array_map(function($item){

			return array(
				'name'=>$item['title'],
				'value'=>$item['title'],
				'url'=> Yii::app()->createUrl('/classPlan/view',array('id'=>$item['id_class'])),
				'tokens'=>explode(' ',$item['tags'])

			);

		}, $command->queryAll());
	
		header('Content-type: application/json');
		echo json_encode($result);
		Yii::app()->end();
		
	}

	public function actionTokens($q=''){

		$term = CHtml::encode($q);
		$command =  Yii::app()->db->createCommand();
		$command->from('token');
		$command->where = "name ILIKE '%{$term}%'";
		$command->select = 'id, name';

		$result =  array_map(function($item){
			return array(
				'name'=>$item['name'],
				'value'=>$item['name'],
				'url'=> Yii::app()->createUrl('/classPlan/tag',array('tag'=>$item['name'])),
				'tokens'=>array($item['name'])

			);
		}, $command->queryAll());

		header('Content-type: application/json');
		echo json_encode($result);
		Yii::app()->end();
		
	}

	public function actionTag($tag){
		$model = new ClassPlan();

		$term = CHtml::encode($tag);
		$criteria=new CDbCriteria;
		$criteria->condition = "t.tags ILIKE '%{$term}%' AND released = true";
		$criteria->order = 't.title ASC';

		$dataProvider=new CActiveDataProvider('ClassPlan', array(
							'criteria'=>$criteria, 
							'pagination'=>array('pageSize'=>10,),
    						));
    						
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
			'model'=>$model,
		));
	}
}
