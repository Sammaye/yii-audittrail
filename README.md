audittrail
==========

This is basically a modification of a previous extension made by [MadSkillsTisdale](http://www.yiiframework.com/user/597/) at http://www.yiiframework.com/extension/audittrail.

I have basically cleaned up some of the code and made a few additions to the behaviour bundled within this extension.

## Installing the extension

The method of installation has changed. I have removed the need to install a module class since:

- It only provided global configration variables for the audit log widget
- It was extra bloat that didn't justify the needs
- I found that in a real system you wouldn't want a page showing all audit log entries since the audit logs would grow to unmanagable sizes greater than SQL could truely handle in one page
- The audit log is quite easy to add to a page using `CGridView`

As such for these reasons the module itself has been deleted.

### Step 1

To install you must first choose a folder in which to place this repository. I have chosen:

    /root/backend/extensions/modules

Since this seems most right to me. Clone this repository to that location.

### step 2

Time to install the table. You can use the migration file provided by the original author of this extension or you can use the SQL file bundled within the migrations folder. Simply
run it on your DB server (using PHPMyAdmin or something) and watch the magic unfold.

### Step 3

Reference the `AuditTrail` model within your configuration:

	'import'=>array(
		'site.backend.extensions.modules.auditTrail.models.AuditTrail',
	),

**Note** You can move `AuditTrail` to your `models` folder preventing you from having to link it like this.

### Step 4

Simply use the behaviour within a model like:

	'LoggableBehavior'=> array(
		'class' => 'site.backend.extensions.modules.auditTrail.behaviors.LoggableBehavior',
	)

### Epilogue

If your user class is not `User` then you may (depending on your setup) need to change the relation within the `AuditLog` model to suite your needs.

## API

### Custom User Attributes

Some people don't actually have defined users but do have an attribute of the auditable model that would define a unique identification of who edited it. For this end you can use:

	'LoggableBehavior'=> array(
		'class' => 'site.backend.extensions.modules.auditTrail.behaviors.LoggableBehavior',
  		'userAttribute' => 'name'
	)

### Storing Timestamps

The date of the audit can be changed to used timestamps instead using:

	'LoggableBehavior'=> array(
		'class' => 'site.backend.extensions.modules.auditTrail.behaviors.LoggableBehavior',
  		'storeTimestamp' => true
	)

### Changing the date format

You can adjust the date format using the `dateFormat` property of the behaviour:

	'LoggableBehavior'=> array(
		'class' => 'site.backend.extensions.modules.auditTrail.behaviors.LoggableBehavior',
  		'dateFormat' => 'Y-m-d H:i:s'
	)

### Ignoring and allowing specific fields

There is one interesting addition to this version. You can now specify an `allowed` set of fields and a `ignored` set of fields...or both.

To do this include the behaviour in your models like you normally would:

    'LoggableBehavior'=> 'site.backend.extensions.modules.auditTrail.behaviors.LoggableBehavior'

But then add either a `ignored` or `allowed` (or both) to the behaviour like so:

	'LoggableBehavior'=> array(
		'class' => 'site.backend.extensions.modules.auditTrail.behaviors.LoggableBehavior',
  		'allowed' => array(
  			'version',
  			'ns_purchase_description'
  		),
  		'ignored' => array(
  			'ns_purchase_description',
  			'ns_display_name',
  			'update_time'
  		)
	)

The names put into the `allowed` and `ignored` parameters of the behaviour represent field names.

As you will notice I allow the `ns_purchase_description` field but also ignore it. When you use the fields in this way `ignored` will replace the `allowed` and this field will be omitted.

## Printing out the audit log

Since this no longer uses a module to do its work there is not global configuration for the previously inbuilt audit log to work from. Instead you can insert an audit log onto a models
page like (as an example only, showing an audit of changes to a book title and it's products on a book title page):

    <?php

	$model_ids = array($model->id);
	foreach($model->products as $id => $product){
		$model_ids[] = $product->id;
	}

	$criteria=new CDbCriteria(array(
		'order'=>'stamp DESC',
		'with'=>array('user'),
	));
	$criteria->addInCondition('model_id', $model_ids);

	$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'title-grid',
		'dataProvider'=>new CActiveDataProvider('AuditTrail', array(
		    'criteria'=>$criteria,
			'pagination'=>array(
		        'pageSize'=>100,
		    )
		)),
		'columns'=>array(
			array(
				'name' => 'Author',
				'value' => '$data->user ? $data->user->email : ""'
			),
			'model',
			'model_id',
			'action',
			array(
				'name' => 'field',
				'value' => '$data->getParent()->getAttributeLabel($data->field)'
			),
			'old_value',
			'new_value',
			array(
				'name' => 'Date Changed',
				'value' => 'date("d-m-Y h:i:s", strtotime($data->stamp))'
			)
		),
	)); ?>