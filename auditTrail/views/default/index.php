<?php
$this->breadcrumbs=array(
	$this->module->id,
);
?>
<h1>Welcome to Audit Trail!</h1>
<h2>Introduction</h2>
<p>
	This is the audit trail module. This module provides basic access to any changes performed via active record through any class that has the LoggableBehavior assigned. It is based off of <?php echo CHtml::link('this cookbook article', 'http://www.yiiframework.com/wiki/9/how-to-log-changes-of-activerecords'); ?>. I've noticed I always do the same things with it, and I hoped to help others who probably do the same thing.
</p>
<h2>Changes</h2>
<p>
	<ul>
		<li><b><em>The widget now uses the zii Cportlet widget</em></b> and does not need the <?php echo CHtml::link('xportlet widget', 'http://www.yiiframework.com/extension/portlet/'); ?> any longer</li>
		<li>This extension now uses migrations, so the db schema files are no longer necessary.</li>
	</ul>
</p>
<h2>Requirements</h2>
<p>
	This module requires:
	<ul>
		<li>Yii 1.1.6 or higher</li>
		<li>command line access to use database migrations</li>
		<li>a database connection. So far this has only been tested on MySQL, but it should work on any DB as long as the initDb script is properly modified to create tables in the syntax of your RDBMS. Any RDBMS translations would be very much appreciated!</li>
		<li>a user object with an id and a username field. The name of the class, the id field, and the username field can be overridden in the config file.</li>
	</ul>
</p>
<h2>Installation</h2>
<p>
	If you are looking at this page, you at least enabled the module in your main.php config. Good job! Now we can continue with the installation:
	<ol>
		<li>Make sure your components->db is configured in protected/config/main.php</li>
		<li>Make sure the rest of this module is set up in your protected/config/main.php. See <a href="#config">configuration</a> for help with this.</li>
		<li>
			Run the database migrations to create the tables for audit trail. Keep in mind that you will have to use the --migrationPath flag to tell the yiic tool where the migrations are. It should look something like this: 
			<blockquote><code>prompt:> php ./yiic.php migrate up --migrationPath=application.modules.auditTrail.migrations</code></blockquote>
			Keep in mind that you may have to change the mirgrationPath to match where you installed the extension. My examples assumes you put it in MyWebApp/protected/modules
		</li>
		<li>Make sure any active record objects you want to log are using the <a href="#loggable">loggable behavior</a></li>
		<li>
			Add the <a href="#widget">audit trail widget</a> to any admin pages you want (optional)
		</li>
		<li>
			Build in RBAC rules if using RBAC (optional). This controllers in this module automatically extend the Controller class of the current web app, so any logic you built into your app for RBAC should work fine. You may need to adjust settings in your RBAC management interface, but specific instructions depend on which implementation you are using. If you need a recommendation, I really like <a href="http://www.yiiframework.com/extension/rights/">Rights</a> by Chris83.
		</li>
		<li>Use the <a href="#manager">Audit Trail Manager</a> to manage your audit trail!</li>
	</ol>
</p>
<h2>Parts</h2>
<a name="config"><h3>main.php Configuration</h3></a>
<p>
	Please add the AuditTrail model to the import section of your main.php config file so that all models that need it can find the AR model:
<blockquote><code><pre>
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.modules.auditTrail.models.AuditTrail',
		.....
	),
</pre></code></blockquote>
	Here are the following options for your main.php configurations (the defaults for all of them should work, so you may not need to use any of them, but if you need to override them you can)
<blockquote><code><pre>
	'modules'=>array(
		'auditTrail'=>array(
			'userClass' => 'User', // the class name for the user object
			'userIdColumn' => 'id', // the column name of the primary key for the user
			'userNameColumn' => 'username', // the column name of the primary key for the user
		),
	.......
</pre></code></blockquote>
</p>

<a name="loggable"><h3>Loggable Behavior</h3></a>
<p>
	You should make sure your ActiveRecord objects use the LoggableBehavior. If you installed AuditTrail to your modules directory, this would typically be referenced by adding this function to your AR model:
<code>public function behaviors()
{
	return array(
		'LoggableBehavior'=>
			'application.modules.auditTrail.behaviors.LoggableBehavior',
	);
}</code>
</p>
<a name="widget"><h3>Audit Trail Widget</h3></a>
<p>
	You can easily add the audit trail widget to any page that is specifcally about one row of one thing (ie: one instance of one model, like an update or view page, not like an admin or list page), and it will give you insight into changes for just that object.
<code>$this->widget(
	'application.modules.auditTrail.widgets.portlets.ShowAuditTrail',
	array(
		'model' => $model,
	)
);</code>
</p>
<a name="manager"><h3>Audit Trail Manager</h3></a>
<p>The manager is just a searchable table of all audits. You can find it here: <?php echo Chtml::link('Audit Trail Manager', array('/auditTrail/admin')); ?></p>
