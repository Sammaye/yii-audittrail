<?php

/**
 * This is the model class for table "tbl_audit_trail".
 */
class AuditTrail extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'tbl_audit_trail':
	 * @var integer $id
	 * @var string $new_value
	 * @var string $old_value
	 * @var string $action
	 * @var string $model
	 * @var string $field
	 * @var string $stamp
	 * @var integer $user_id
	 * @var string $model_id
	 */

	/**
	 * Returns the static model of the specified AR class.
	 * @return AuditTrail the static model class
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
		if ( isset(Yii::app()->params['AuditTrail']) && isset(Yii::app()->params['AuditTrail']['table']) )
		    return Yii::app()->params['AuditTrail']['table'];
		else
		    return '{{audit_trail}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('action, model, stamp, model_id', 'required'),
			array('action', 'length', 'max'=>255),
			array('model', 'length', 'max'=>255),
			array('field', 'length', 'max'=>255),
			array('model_id', 'length', 'max'=>255),
			array('user_id', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, new_value, old_value, action, model, field, stamp, user_id, model_id', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('app', 'ID'),
			'old_value' => Yii::t('app', 'Old Value'),
			'new_value' => Yii::t('app', 'New Value'),
			'action' => Yii::t('app', 'Action'),
			'model' => Yii::t('app', 'Type'),
			'field' => Yii::t('app', 'Field'),
			'stamp' => Yii::t('app', 'Stamp'),
			'user_id' => Yii::t('app', 'User'),
			'model_id' => Yii::t('app', 'Model ID'),
		);
	}

	function getParent(){
		$model_name = $this->model;
		return $model_name::model();
	}
    
    function findModel(){
        return $this->getParent()->findByPK($this->model_id);
    }

    function getOldValue(){
        $model = $this->findModel();
        $relations = $model->relations();        
        foreach($relations as $name=>$relation){
            if ($relation[2] == $this->field){
                return $relation[1]::model()->findByPK($this->old_value);
            }
        }
        return $this->old_value;
    }

    function getNewValue(){
        $model = $this->findModel();
        $relations = $model->relations();        
        foreach($relations as $name=>$relation){
            if ($relation[2] == $this->field){
                return $relation[1]::model()->findByPK($this->new_value);
            }
        }
        return $this->new_value;
    }
    
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($options = array())
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);
		$criteria->compare('old_value',$this->old_value,true);
		$criteria->compare('new_value',$this->new_value,true);
		$criteria->compare('action',$this->action,true);
		$criteria->compare('model',$this->model);
		$criteria->compare('field',$this->field,true);
		$criteria->compare('stamp',$this->stamp,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('model_id',$this->model_id);
		$criteria->mergeWith($this->getDbCriteria());
		return new CActiveDataProvider(
			get_class($this),
			array_merge(
				array(
					'criteria'=>$criteria,
				),
				$options
			)
		);
	}

	public function scopes() {
		return array(
			'recently' => array(
				'order' => ' t.stamp DESC ',
			),

		);
	}
}
