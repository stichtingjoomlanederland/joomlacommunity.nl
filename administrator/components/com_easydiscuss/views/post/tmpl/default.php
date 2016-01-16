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

JHTML::_('behavior.modal' , 'a.modal' );

// Force isDiscussion = true
$this->composer->setIsDiscussion(true);
$editor = JFactory::getEditor();
?>

<?php if( $this->config->get( 'layout_editor' ) == 'bbcode' ) { ?>
<script type="text/javascript">
EasyDiscuss.require()
	.library('markitup')
	.done(function($){
		$('.dc_reply_content').markItUp(
			$.getEasyDiscussBBCodeSettings
		);
	});
</script>
<?php } ?>

<script type="text/javascript">
EasyDiscuss.require()
.script('composer', 'legacy', 'bbcode')
.done(function($){

	$.Joomla( 'submitbutton' , function( action ){

		if(action == 'cancel')
		{
			window.location.href 	= 'index.php?option=com_easydiscuss&view=posts';
		}
		else if(action == 'submit')
		{
			if(admin.post.validate(false, 'newpost'))
			{
				admin.post.submit();
			}
		}
		else
		{
			$.Joomla( 'submitform' , [action] );
		}
	});

	discuss.composer.init("<?php echo $this->composer->classname; ?>");

	// User selection.
	window.selectUser = function( id , name )
	{
		$( '#user_id' ).val( id );
		$( '#user_name' ).val( name );

		// Close dialog
		$.Joomla( 'squeezebox' ).close();
	};

	// Initialize custom Tabs.
	// Try to test if there is a 'default' class in all of the tabs
	if( $( 'ul.formTabs' ).children().find( '.default' ).html() != null )
	{
		var id 	= $( 'ul.formTabs' ).children().find( '.default' ).attr( 'id' );
		var tab = id.substr( id.indexOf( '-' ) + 1 , id.length );

		$( 'ul.formTabs' ).children().find( '.default' ).parent().addClass( 'active' );

		$( 'div.formTabsContent' ).children().hide();
		$( '.tab-' + tab ).show();
	}
	else
	{
		$( 'ul.formTabs a:first' ).tab( 'show' );
	}
});
</script>
<div class="discuss-form">
<form id="adminForm" name="adminForm" action="index.php" method="post" enctype="multipart/form-data" class="adminform-body">
<div class="discuss-form <?php echo $this->composer->id; ?>"
	 data-id="<?php echo $this->composer->id; ?>"
	 data-editor="<?php echo $this->config->get('layout_editor') ?>">

<div id="dc_post_notification"><div class="msg_in"></div></div>

<div class="row-fluid">
	<div class="span8">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_POST_DETAILS' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span2">
							<label for="title"><?php echo JText::_( 'COM_EASYDISCUSS_POST_TITLE' );?></label>
						</div>
						<div class="span10">
							<input type="text" maxlength="255" size="100" id="title" name="title" class="input-xxlarge" value="<?php echo $this->escape( $this->post->title );?>" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span2">
							<label for="alias"><?php echo JText::_( 'COM_EASYDISCUSS_POST_ALIAS' );?></label>
						</div>
						<div class="span10">
							<input type="text" maxlength="255" size="100" id="alias" name="alias" class="input-xxlarge" value="<?php echo $this->escape( $this->post->alias );?>" />
						</div>
					</div>

					<?php if ($this->config->get('main_private_post', false)) { ?>
					<div class="si-form-row">
						<label class="checkbox" for="private">
							<input id="private" type="checkbox" name="private" value="1" <?php echo $this->post->private ? ' checked="checked"' : '';?>/> <?php echo JText::_('COM_EASYDISCUSS_MAKE_THIS_POST_PRIVATE');?>
						</label>
					</div>
					<?php } ?>

					<div class="si-form-row">
<!-- 						<?php if( $this->config->get( 'layout_editor') == 'bbcode' ) { ?>
							<textarea class="dc_reply_content full-width" name="dc_reply_content" class="full-width"><?php echo $this->escape( $this->post->content ); ?></textarea>
						<?php } else { ?>
							<?php echo $editor->display( 'dc_reply_content', $this->escape( $this->post->content ), '100%', '350', '10', '10' , array( 'pagebreak' , 'readmore' ) ); ?>
						<?php } ?> -->

						<?php echo $this->composer->getEditor(); ?>
					</div>

					<div class="control-group">
						<?php //echo $this->loadTemplate( 'form.location.php' ); ?>
					</div>

					<?php echo $this->composer->getFields(); ?>

				</div>
			</div>
		</div>
	</div>

	<div class="span4">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#publishoptions">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_PUBLISHING_OPTIONS' );?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="publishoptions" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span4">
							<label for="title"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY' );?></label>
						</div>
						<div class="span8">
							<?php echo $this->nestedCategories; ?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span4">
							<label><?php echo JText::_( 'COM_EASYDISCUSS_POST_AUTHOR' ); ?></label>
						</div>
						<div class="span8">
							<input type="text" disabled="disabled" id="user_name" value="<?php echo $this->creatorName;?>" class="input-xlarge" />
							<input type="hidden" name="user_id" id="user_id" value="<?php echo $this->post->user_id;?>" />
							<a href="index.php?option=com_easydiscuss&view=users&tmpl=component&browse=1&browsefunction=selectUser" class="btn btn-mini btn-primary modal" rel="{handler: 'iframe', size: {x: 700, y: 500}}"><i class="icon-plus-sign"></i> <?php echo JText::_( 'COM_EASYDISCUSS_BROWSE_USERS' ); ?></a>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span4">
							<label><?php echo JText::_( 'COM_EASYDISCUSS_PUBLISHED' ); ?></label>
						</div>
						<div class="span8">
							<?php echo $this->renderCheckbox( 'published' , $this->post->published ); ?>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

</div>

<input type="hidden" name="id" id="id" value="<?php echo $this->post->id; ?>" />
<input type="hidden" name="parent_id" id="parent_id" value="<?php echo $this->post->parent_id; ?>" />
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="controller" value="posts" />
<input type="hidden" id="task" name="task" value="submit" />
<input type="hidden" name="source" value="<?php echo $this->source ;?>" />
<?php echo JHTML::_( 'form.token' ); ?>

</div>
</form>
</div>
