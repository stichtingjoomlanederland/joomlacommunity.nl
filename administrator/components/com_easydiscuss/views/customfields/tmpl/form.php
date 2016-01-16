<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
EasyDiscuss
.require()
.library('ui/datepicker')
.done(function($){


	EasyDiscuss(function($){
		$.Joomla( 'submitbutton' , function(action)
		{
			$.Joomla( 'submitform' , [action] );
		});
	});

	$('#activerule').val("view");

	$('#accordion-toggle-view').click( function() {
		$('#activerule').val("view");
	});

	$('#accordion-toggle-input').click( function() {
		$('#activerule').val("input");
	});

	$('#customFields-acl-assign-group').click( function() {
		customFieldAclAssign('group');
	});

	$('#customFields-acl-assign-user').click( function() {
		customFieldAclAssign('user');
	});

	selectUser = function( id , name, prefix )
	{
		admin.customFields.acl.addpaneluser(id, name, prefix);

		// Close dialog
		$.Joomla( 'squeezebox' ).close();
	};

});


function customFieldAclAssign(type)
{
	var action	= EasyDiscuss.$('#activerule').val();
	var items = EasyDiscuss.$(":input[name='acl_panel_"+ type + "[]']:checked");

	if( items != null )
	{
		for(i = 0; i < items.length; i++)
		{
			var ele			= items[i];
			var id			= EasyDiscuss.$(ele).val();
			var text		= EasyDiscuss.$("#acl_panel_" + type + "_" + id).val();

			var doinsert	= true;
			var curProcessItem = EasyDiscuss.$(":input[name='acl_" + type + "_" + action + "[]']");

			if( curProcessItem.length > 0 )
			{
				for(c = 0; c < curProcessItem.length; c++)
				{
					var cele = curProcessItem[c];
					if( cele.value == id )
					{
						doinsert = false;
						break;
					}
				}
			}

			if( doinsert )
			{
				var input = '<li id="acl_' + type + '_' + action + '_' + id + '">';
				input += '<span><a href="javascript: admin.customFields.acl.remove(\'acl_' + type + '_' + action + '_' + id + '\');">Delete</a></span>';
				input += ' - ' + text;
				input += '<input type="hidden" name="acl_'+ type + '_' + action + '[]" value="' + id + '" />';
				input += '</li>';

				EasyDiscuss.$('#customFields_acl_' + type + '_' + action)
					.append(
						input
					);
			}
		}//end for i
	}//end if type is null
}

</script>
<div class="adminform-body">
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#si-option1" data-foundry-toggle="tab"><?php echo JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_TAB_MAIN' ); ?></a></li>
		<li><a href="#si-option2" data-foundry-toggle="tab"><?php echo JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_TAB_PERMISSION' ); ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="si-option1">
			<?php echo $this->loadTemplate('main');?>
		</div>
		<div class="tab-pane" id="si-option2">

			<?php echo $this->loadTemplate('acl');?>
		</div>
	</div>
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="controller" value="customfields" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="custom_id" value="<?php echo $this->field->id;?>" />
</form>
</div>
