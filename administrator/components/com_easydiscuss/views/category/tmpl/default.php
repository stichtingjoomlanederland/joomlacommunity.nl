<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
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
	.ready(function($) {

		$.Joomla( 'submitbutton' , function( action ){
			$.Joomla( 'submitform' , [action] );
		});

		$('#activerule').val("select");

		$('#accordion-toggle-select').click( function() {
			$('#activerule').val("select");
		});

		$('#accordion-toggle-view').click( function() {
			$('#activerule').val("view");
		});

		$('#accordion-toggle-reply').click( function() {
			$('#activerule').val("reply");
		});

		$('#accordion-toggle-viewreply').click( function() {
			$('#activerule').val("viewreply");
		});

		$('#accordion-toggle-moderate').click( function() {
			$('#activerule').val("moderate");
		});

		$('#category-acl-assign-group').click( function() {
			categoryAclAssign('group');
		});

		$('#category-acl-assign-user').click( function() {
			categoryAclAssign('user');
		});



		window.selectUser = function( id , name, prefix )
		{
			admin.category.acl.addpaneluser(id, name, prefix);

			// Close dialog
			$.Joomla( 'squeezebox' ).close();
		};


		function categoryAclAssign(type)
		{
			var action	= $('#activerule').val();
			var items = $(":input[name='acl_panel_"+ type + "[]']:checked");

			if( items != null )
			{
				for(i = 0; i < items.length; i++)
				{
					var ele			= items[i];
					var id			= $(ele).val();
					var text		= $("#acl_panel_" + type + "_" + id).val();

					var doinsert	= true;
					var curProcessItem = $(":input[name='acl_" + type + "_" + action + "[]']");

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
						input += '<span><a href="javascript: admin.category.acl.remove(\'acl_' + type + '_' + action + '_' + id + '\');">Delete</a></span>';
						input += ' - ' + text;
						input += '<input type="hidden" name="acl_'+ type + '_' + action + '[]" value="' + id + '" />';
						input += '</li>';

						$('#category_acl_' + type + '_' + action)
							.append(
								input
							);
					}
				}//end for i
			}//end if type is null
		}

	});

</script>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="adminform-body">
<ul class="nav nav-tabs">
	<li class="active"><a href="#si-option1" data-foundry-toggle="tab"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_GENERAL' ); ?></a></li>
	<li><a href="#si-option2" data-foundry-toggle="tab"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_PERMISSION' ); ?></a></li>
</ul>
<div class="tab-content">
	<div class="tab-pane active" id="si-option1">
		<?php echo $this->loadTemplate('main');?>
	</div>
	<div class="tab-pane" id="si-option2">
		<?php echo $this->loadTemplate('acl');?>
	</div>
</div>

<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="controller" value="category" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="catid" value="<?php echo $this->cat->id;?>" />
<input type="hidden" name="private" value="<?php echo ( empty( $this->cat->private ) ) ? DISCUSS_PRIVACY_ACL : $this->cat->private ;?>">
</form>
