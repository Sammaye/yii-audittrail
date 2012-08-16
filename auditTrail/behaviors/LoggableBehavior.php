<?php
class LoggableBehavior extends CActiveRecordBehavior
{
	private $_oldattributes = array();

	public $allowed = array();
	public $ignored = array();

	public function afterSave($event){
		try {
			$userid = Yii::app()->user->id;
		} catch(Exception $e) { //If we have no user object, this must be a command line program
			$userid = null;
		}

		if(empty($userid)) {
			$userid = null;
		}

		$allowedFields = $this->allowed;
		$ignoredFields = $this->ignored;

		$newattributes = $this->Owner->getAttributes();
		$oldattributes = $this->getOldAttributes();

		if(sizeof($allowedFields) > 0){
			foreach($newattributes as $f => $v){
				if(array_search($f, $allowedFields) === false) unset($newattributes[$f]);
			}

			foreach($oldattributes as $f => $v){
				if(array_search($f, $allowedFields) === false) unset($oldattributes[$f]);
			}
		}

		if(sizeof($ignoredFields) > 0){
			foreach($newattributes as $f => $v){
				if(array_search($f, $ignoredFields) !== false) unset($newattributes[$f]);
			}

			foreach($oldattributes as $f => $v){
				if(array_search($f, $ignoredFields) !== false) unset($oldattributes[$f]);
			}
		}

		if (!$this->Owner->isNewRecord) {
			// compare old and new
			foreach ($newattributes as $name => $value) {
				if (!empty($oldattributes)) {
					$old = $oldattributes[$name];
				} else {
					$old = '';
				}

				if(empty($oldattributes[$name]) && empty($newattributes[$name])){
					continue;
				}

				if ($value != $old) {
					$log			= new AuditTrail();
					$log->old_value = $old;
					$log->new_value = $value;
					$log->action 	= 'CHANGE';
					$log->model 	= get_class($this->Owner);
					$log->model_id 	= $this->Owner->getPrimaryKey();
					$log->field 	= $this->owner->getAttributeLabel($name);
					$log->stamp 	= date('Y-m-d H:i:s');
					$log->user_id 	= $userid;

					$log->save();
				}
			}
		} else {
			$log			= new AuditTrail();
			$log->old_value = '';
			$log->new_value = '';
			$log->action	= 'CREATE';
			$log->model		= get_class($this->Owner);
			$log->model_id	= $this->Owner->getPrimaryKey();
			$log->field		= 'N/A';
			$log->stamp		= date('Y-m-d H:i:s');
			$log->user_id	= $userid;

			$log->save();

			foreach ($newattributes as $name => $value) {

				if(empty($value)){
					continue;
				}

				$log			= new AuditTrail();
				$log->old_value = '';
				$log->new_value = $value;
				$log->action	= 'SET';
				$log->model		= get_class($this->Owner);
				$log->model_id	= $this->Owner->getPrimaryKey();
				$log->field		= $this->owner->getAttributeLabel($name);
				$log->stamp		= date('Y-m-d H:i:s');
				$log->user_id	= $userid;
				$log->save();
			}
		}
		return parent::afterSave($event);
	}

	public function afterDelete($event){

		try {
			$userid = Yii::app()->user->id;
		} catch(Exception $e) {
			$userid = null;
		}

		if(empty($userid)) {
			$userid = null;
		}

		$log=new AuditTrail();
		$log->old_value = '';
		$log->new_value = '';
		$log->action 	= 'DELETE';
		$log->model		= get_class($this->Owner);
		$log->model_id	= $this->Owner->getPrimaryKey();
		$log->field		= 'N/A';
		$log->stamp		= date('Y-m-d H:i:s');
		$log->user_id	= $userid;
		$log->save();
		return parent::afterDelete($event);
	}

	public function afterFind($event){
		// Save old values
		$this->setOldAttributes($this->Owner->getAttributes());
		return parent::afterFind($event);
	}

	public function getOldAttributes(){
		return $this->_oldattributes;
	}

	public function setOldAttributes($value){
		$this->_oldattributes=$value;
	}
}