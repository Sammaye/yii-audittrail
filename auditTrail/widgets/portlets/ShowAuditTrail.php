<?php
/**
 * ShowAuditTrail shows the audit trail for the current item
 */

Yii::import('zii.widgets.CPortlet');
require_once(realpath(dirname(__FILE__) . '/../../AuditTrailModule.php'));

class ShowAuditTrail extends CPortlet
{
	/**
	 * @var CActiveRecord the model you want to use with this field
	 */
	public $model;

	/**
	 * @var boolean whether or not to show the widget
	 */
	public $visible = true;

	/**
	 * @var this allows you to override individual columns' display properties in the datagrid.
	 * Column definitions should be indexed by column name, and the value should match the column
	 * format of CDataGrid. For example:
	 *
	 * 'dataGridColumnsOverride' => array(
	 * 		'old_value' => array(
	 * 			'name' => 'old_value',
	 * 			'filter' => '',
	 * 		),
	 * 		'new_value' => array(
	 * 			'name' => 'new_value',
	 * 			'filter' => '',
	 * 		),
	 * )
	 *
	 * Please do not specify a column if you do not wish to override the defaults of that column.
	 * Also, please be careful when specifying a format for user_id, as special handling exists
	 * to format the user name
	 */
	public $dataGridColumnsOverride = array( );

	/**
	 * @var AuditTrailModule static variable to hold the module so we don't have to instantiate it a million times to get config values
	 */
	private static $__auditTrailModule;

	/**
	 * Sets the title of the portlet
	 */
	public function init() {
		$this->title = "Audit Trail For " . get_class($this->model) . " " . $this->model->id;
		parent::init();
	}

	/**
	 * generates content of widget the widget.
	 * This renders the widget, if it is visible.
	 */
	public function renderContent()
	{
		if($this->visible) {
			$auditTrail = AuditTrail::model()->recently();
			$auditTrail->model = get_class($this->model);
			$auditTrail->model_id = $this->model->primaryKey;
			$columnFormat = $this->getColumnFormat();
			$this->widget('zii.widgets.grid.CGridView', array(
				'id'=>'audit-trail-grid',
				'dataProvider'=>$auditTrail->search(),
				'columns'=> $this->getColumnFormat(),
			));
		}
	}
	
	/**
	 * Builds the label code we need to display usernames correctly
	 * @return The code to be evaled to display the user info correctly
	 */
	protected function getEvalUserLabelCode() {
		$userClass = $this->getFromConfigOrObject('userClass');
		$userNameColumn = $this->getFromConfigOrObject('userNameColumn');
		$userEvalLabel = ' ( ($t = '
							. $userClass
							. '::model()->findByPk($data->user_id)) == null ? "": $t->'
							. $userNameColumn
							. ' ) ';
		return $userEvalLabel;
	}
	
	/**
	 * Returns the value you want to look up, either from the config file or a user's override
	 * @var value The name of the value you would like to look up
	 * @return the config value you need
	 */
	protected function getFromConfigOrObject($value) {
		$at = Yii::app()->modules['auditTrail'];

		//If we can get the value from the config, do that to save overhead
		if( isset( $at[$value]) && !empty($at[$value] ) ) {
			return $at[$value];
		}

		//If we cannot get the config value from the config file, get it from the
		//instantiated object. Only instantiate it once though, its probably 
		//expensive to do. PS I feel this is a dirty trick and I don't like it
		//but I don't know a better way
		if(!is_object(self::$__auditTrailModule)) {
			self::$__auditTrailModule = new AuditTrailModule(microtime(), null);
		}
		
		return self::$__auditTrailModule->$value;
	}

	/**
	 * Gets final column format. Starts with default column format (specified in this method
	 * and checks $this->dataGridColumnsOverride array to see if any columns need to use a
	 * user specified format.
	 * @return array The final format array, with any user specified formats taking precedent over defaults
	 */
	protected function getColumnFormat() {
		$evalUserLabel = $this->getEvalUserLabelCode();
		$columnFormat = array();
		$defaultColumnFormat = array(
			'old_value' => array(
				'name' => 'old_value',
				'filter' => '',
			),
			'new_value' => array(
				'name' => 'new_value',
				'filter' => '',
			),
			'action' => array(
				'name' => 'action',
				'filter'=> '',
			),
			'field' => array(
				'name' => 'field',
				'filter' => '',
			),
			'stamp' => array(
				'name' => 'stamp',
				'filter' => '',
			),
			'user_id' => array(
				'name' => 'user_id',
				'value'=>$evalUserLabel,
				'filter'=> '',
			),
		);
		
		foreach($defaultColumnFormat as $key => $format) {
			$columnFormat[] = isset($this->dataGridColumnsOverride[$key]) ? $this->dataGridColumnsOverride[$key] : $defaultColumnFormat[$key];
		}
		
		return $columnFormat;
	}
}