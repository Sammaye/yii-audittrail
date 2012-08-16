<?php

class AdminController extends Controller
{
	public $defaultAction = "admin";
	public $layout='//layouts/column1';

	public function actionAdmin()
	{
		$model=new AuditTrail('search');
		$model->unsetAttributes();	// clear any default values
		if(isset($_GET['AuditTrail'])) {
			$model->attributes=$_GET['AuditTrail'];
		}
		$this->render('admin',array(
			'model'=>$model,
		));
	}
}