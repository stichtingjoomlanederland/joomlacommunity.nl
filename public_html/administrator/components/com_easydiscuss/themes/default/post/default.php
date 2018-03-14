<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.modal', 'a.modal');
?>
<script type="text/javascript">
ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	$.Joomla('submitbutton', function(action) {

		if (action == 'cancel') {
			window.location.href = '<?php echo $returnUrl; ?>';
		} else if (action == 'submit') {
			if(admin.post.validate(false, 'newpost')) {
				admin.post.submit();
			}
		} else {
			$.Joomla('submitform', [action]);
		}
	});

	// User selection.
	window.selectUser = function(id, name)
	{
		$('#user_id').val(id);
		$('#user_name').val(name);

		// Close dialog
		$.Joomla('squeezebox').close();
	};

});
</script>

<div class="discuss-form">
<form id="adminForm" name="adminForm" action="index.php" method="post" enctype="multipart/form-data" class="adminform-body">
<div class="discuss-form <?php echo $composer->id; ?>"
	 data-id="<?php echo $composer->id; ?>"
	 data-editor="<?php echo $this->config->get('layout_editor') ?>">

<div id="dc_post_notification"><div class="msg_in"></div></div>

<div class="row-fluid">
	<div class="col-md-8">
		<div class="panel">
			<div class="panel-head">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo $post->isReply() ? JText::_('Reply Details') : JText::_('COM_EASYDISCUSS_POST_DETAILS'); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="panel-body">
				<div class="">
					<div class="form-horizontal t-lg-mb--lg <?php echo $post->isReply() ? 't-hidden' : ''; ?>">
						<div class="form-group">
							<div class="col-md-3 control-label">
								<label for="title"><?php echo JText::_( 'COM_EASYDISCUSS_POST_TITLE' );?></label>
							</div>
							<div class="col-md-9">
								<input type="text" maxlength="255" size="100" id="title" name="title" class="form-control" value="<?php echo $this->escape( $post->title );?>" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-3 control-label">
								<label for="alias"><?php echo JText::_( 'COM_EASYDISCUSS_POST_ALIAS' );?></label>
							</div>
							<div class="col-md-9">
								<input type="text" maxlength="255" size="100" id="alias" name="alias" class="form-control" value="<?php echo $this->escape( $post->alias );?>" />
							</div>
						</div>
						<?php if ($operation == 'editing') { ?>
						<div class="form-group">
							<div class="col-md-3">
							</div>
							<div class="col-md-9">
								<?php echo JText::_('COM_EASYDISCUSS_POST_ALIAS_CHANGE_NOTE'); ?>
							</div>
						</div>
						<?php } ?>
					</div>


					<?php if ($this->config->get('main_private_post', false) && !$post->isReply()) { ?>
					<div class="form-group">
						<label class="checkbox" for="private">
							<input id="private" type="checkbox" name="private" value="1" <?php echo $post->private ? ' checked="checked"' : '';?>/> <?php echo JText::_('COM_EASYDISCUSS_MAKE_THIS_POST_PRIVATE');?>
						</label>
					</div>
					<?php } ?>

					<div class="ed-editor ed-editor--<?php echo $composer->getEditorClass();?> <?php echo $composer->hasTabs() ? '' : 'has-no-tab'; ?>" <?php echo $composer->uid;?>>
						<div class="ed-editor-widget ed-editor-widget--no-pad">
			        		<?php echo $composer->renderEditor(); ?>

			        		<?php echo $composer->renderTabs(); ?>
			        	</div>

	        			<div class="control-group">
	        				<?php if ($this->config->get('main_master_tags') && $this->acl->allowed('add_tag') && !$post->isReply()) { ?>
	        					<?php echo $this->output('site/composer/forms/tags', array('post' => $post)); ?>
	        	            <?php } ?>

	        	            <?php if ($this->config->get('main_location_discussion')) { ?>
	        	            	<?php echo $this->output('site/composer/forms/location', array('post' => $post, 'editorId' => $composer->uid, 'operation' => $operation)); ?>
	        	            <?php } ?>
	        			</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-4 <?php echo $post->isReply() ? 't-hidden' : ''; ?>">
		<div class="panel">
			<div class="panel-head">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#publishoptions">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_PUBLISHING_OPTIONS' );?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="publishoptions" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-3 control-label">
							<label for="title"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY' );?></label>
						</div>
						<div class="col-md-9">
							<?php echo $nestedCategories; ?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-3 control-label">
							<label><?php echo JText::_( 'COM_EASYDISCUSS_POST_AUTHOR' ); ?></label>
						</div>
						<div class="col-md-9">
							<div class="input-group t-lg-mb--lg">
						    	<input type="text" disabled="disabled" id="user_name" value="<?php echo $creatorName;?>" class="form-control" />
						    	<span class="input-group-btn">
						        	<a href="index.php?option=com_easydiscuss&view=users&tmpl=component&browse=1&browsefunction=selectUser" class="btn btn-primary modal" rel="{handler: 'iframe', size: {x: 700, y: 500}}">
						        		<i class="fa fa-plus"></i> <?php echo JText::_( 'COM_EASYDISCUSS_BROWSE_USERS' ); ?>
						        	</a>
						    	</span>
						  	</div>

							<input type="hidden" name="user_id" id="user_id" value="<?php echo $post->user_id;?>" />

						</div>
					</div>
					<div class="form-group">
						<div class="col-md-3 control-label">
							<label><?php echo JText::_( 'COM_EASYDISCUSS_PUBLISHED' ); ?></label>
						</div>
						<div class="col-md-9">
							<?php echo $this->html('form.boolean', 'published', $post->published); ?>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

</div>

<input type="hidden" name="id" id="id" value="<?php echo $post->id; ?>" />
<input type="hidden" name="parent_id" id="parent_id" value="<?php echo $post->parent_id; ?>" />
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="controller" value="posts" />
<input type="hidden" id="task" name="task" value="save" />
<input type="hidden" name="source" value="posts" />
<?php echo JHTML::_( 'form.token' ); ?>

</div>
</form>
</div>
