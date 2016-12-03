<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<!-- <div class="wrapper" style="width: 300px;">
	<textarea class="mention" placeholder="Please type something ..." style="height:200px;"></textarea>
</div> -->

<style type="text/css">
.markItUpHeader ul li {
	display: inline-block;
}
</style>
<div class="ask-notification"></div>

<form autocomplete="off" action="<?php echo JRoute::_('index.php');?>" method="post" enctype="multipart/form-data" class="form-horizontal" data-ask-form>
	<div class="discuss-form discuss-composer <?php echo $composer->classname; ?> discuss-composer-<?php echo $composer->operation; ?>"
		 data-id="<?php echo $composer->id; ?>"
		 data-editortype="<?php echo $composer->editorType ?>"
		 data-operation="<?php echo $composer->operation; ?>"
	>

		<?php if ($isEditMode) { ?>
			<legend><?php echo JText::_('COM_EASYDISCUSS_ENTRY_EDITING_TITLE');?></legend>		
		<?php } else { ?>
			<legend><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_NEW_DISCUSSION');?></legend>
		<?php } ?>

		<div id="dc_post_notification">
			<div class="msg_in"></div>
		</div>

		<div class="fd-cf control-group discuss-category-selection categorySelection">
			<div class="form-inline">
				<?php if ($this->config->get('layout_category_selection') == 'multitier') { ?>
					<?php echo $this->output('site/ask/category.select.multitier.php'); ?>
				<?php } else { ?>
					<?php echo $nestedCategories; ?>
				<?php } ?>
			</div>
		</div>

		<hr />

		<div class="row-fluid">
			<div class="span<?php echo $this->config->get('layout_post_types') ? '9' : '12';?>">
				<div class="control-group">
					<input type="text" name="title" placeholder="<?php echo JText::_('COM_EASYDISCUSS_POST_TITLE_EXAMPLE' , true ); ?>" class="form-control" autocomplete="off" 
						value="<?php echo $this->escape($post->title);?>" 
						data-ed-post-title
					/>
					
					<div class="hidden" data-ed-similar-questions></div>

					<div class="hidden" data-ed-similar-questions-loader></div>
				</div>

				<?php if ($this->config->get('main_private_post', false)) { ?>
				<div class="control-group">
					<label class="checkbox" for="private">
						<input id="private" type="checkbox" name="private" value="1"<?php echo $post->private ? ' checked="checked"' : '';?> /> <?php echo JText::_('COM_EASYDISCUSS_MAKE_THIS_POST_PRIVATE');?>
					</label>
				</div>
				<?php } ?>
			</div>

			<?php if( $this->config->get( 'layout_post_types' ) ){ ?>
			<div class="span3">
				<div class="control-group">
					<select id="post_type" class="inputbox full-width post-type" name="post_type">
						<option value="default"><?php echo JText::_('COM_EASYDISCUSS_SELECT_POST_TYPES');?></option>
						<?php foreach( $postTypes as $type ){ ?>
							<option <?php echo ($type->alias == $post->post_type) ? 'selected="selected"' : '' ?> value="<?php echo $type->alias ?>"><?php echo $type->title ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<?php } ?>
		</div>

		<div class="fd-cf">
			<?php echo $composer->getEditor(); ?>
		</div>

		<?php if ($this->config->get('main_location_discussion')) { ?>
		<div class="control-group">
			<?php echo $this->output('site/forms/location'); ?>
		</div>
		<?php } ?>

		<?php echo $composer->getFields(); ?>

		<?php if( !$this->my->id && $this->acl->allowed('add_question', 0)) { ?>
		<hr />

		<div class="control-group">
			<div class="row-fluid">
				<div class="span5">
					<label for="poster_name" class="fs-12 mr-10"><?php echo JText::_('COM_EASYDISCUSS_YOUR_NAME'); ?> :</label>
					<input class="input width-200" type="text" id="poster_name" name="poster_name" value="<?php echo empty($post->poster_name) ? '' : $post->poster_name; ?>"/>
				</div>
				<div class="span7">
					<label for="poster_email" class="fs-12 mr-10"><?php echo JText::_('COM_EASYDISCUSS_YOUR_EMAIL'); ?> :</label>
					<input class="input width-200" type="text" id="poster_email" name="poster_email" value="<?php echo empty($post->poster_email) ? '' : $post->poster_email; ?>"/>
				</div>
			</div>
			<div class="form-inline">

			</div>
		</div>
		<div class="control-group">
			<div class="form-inline">

			</div>
		</div>
		<?php } ?>


		<div class="modal-footer">
			<div class="row-fluid">
				<div class="pull-left">
					<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss' );?>" class="btn btn-medium btn-danger"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL'); ?></a>
				</div>

				<div class="pull-right">
					<input type="button" class="btn btn-medium btn-primary" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_SUBMIT' , true ); ?>" data-form-submit />
				</div>
			</div>
		</div>

		<div class="clearfix"></div>

		<?php echo JHTML::_('form.token'); ?>

		<?php if (!empty($reference) && $referenceId) { ?>
		<input type="hidden" name="reference" value="<?php echo $reference; ?>" />
		<input type="hidden" name="reference_id" value="<?php echo $referenceId; ?>" />
		<?php } ?>

		<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
		<input type="hidden" name="option" value="com_easydiscuss" />
		<input type="hidden" name="controller" value="posts" />
		<input type="hidden" name="task" value="submit" />
		<input type="hidden" name="id" id="id" value="<?php echo $post->id; ?>" />
	</div>
</form>
