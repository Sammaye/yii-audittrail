audittrail
==========

This is basically a modification of a previous extension made by (MadSkillsTisdale)[http://www.yiiframework.com/user/597/] at http://www.yiiframework.com/extension/audittrail.

I have basically cleaned up some of the code and made a few additions to the behaviour bundled within this extension.

## Ignoring and allowing specific fields

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