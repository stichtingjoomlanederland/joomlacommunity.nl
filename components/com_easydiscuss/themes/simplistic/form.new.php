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
<!-- do not remove this div -->
<div class="ask-notification"></div>

<form id="dc_submit" autocomplete="off" name="dc_submit" action="<?php echo JRoute::_( 'index.php' );?>" method="post" enctype="multipart/form-data" class="form-horizontal">
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="controller" value="posts" />
<input type="hidden" name="task" value="submit" />
<div class="discuss-form discuss-composer <?php echo $composer->classname; ?> discuss-composer-<?php echo $composer->operation; ?>"
	 data-id="<?php echo $composer->id; ?>"
	 data-editortype="<?php echo $composer->editorType ?>"
	 data-operation="<?php echo $composer->operation; ?>"
	 >

	<?php if( $isEditMode ){ ?>
	<legend><?php echo JText::_( 'COM_EASYDISCUSS_ENTRY_EDITING_TITLE');?></legend>
	<input type="hidden" name="id" id="id" value="<?php echo $post->id; ?>" />
	<?php } else { ?>
	<legend><?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_NEW_DISCUSSION');?></legend>
	<?php } ?>

	<div id="dc_post_notification"><div class="msg_in"></div></div>

	<div class="fd-cf control-group discuss-category-selection categorySelection">
		<div class="form-inline">
			<?php if( $config->get( 'layout_category_selection' ) == 'multitier' ) { ?>
				<?php echo $this->loadTemplate( 'category.select.multitier.php' ); ?>
			<?php } else { ?>
				<?php echo $nestedCategories; ?>
			<?php } ?>
		</div>
	</div>

	<hr />

	<div class="row-fluid">
		<div class="span<?php echo $system->config->get('layout_post_types') ? '9' : '12';?>">
			<div class="control-group">
				<input type="text" id="ez-title" name="title" placeholder="<?php echo JText::_('COM_EASYDISCUSS_POST_TITLE_EXAMPLE' , true ); ?>" class="full-width input input-title" autocomplete="off" value="<?php echo $this->escape( $post->title );?>" />
				<div id="dc-search-loader" style="display:none;">
					<div class="discuss-loader"></div>
				</div>
				<div id="dc_similar-questions" style="display:none"></div>
			</div>

			<?php if ($system->config->get('main_private_post', false)) { ?>
			<div class="control-group">
				<label class="checkbox" for="private">
					<input id="private" type="checkbox" name="private" value="1"<?php echo $post->private ? ' checked="checked"' : '';?> /> <?php echo JText::_('COM_EASYDISCUSS_MAKE_THIS_POST_PRIVATE');?>
				</label>
			</div>
			<?php } ?>
		</div>

		<?php if( $system->config->get( 'layout_post_types' ) ){ ?>
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

	<div class="control-group">
		<?php echo $this->loadTemplate( 'form.location.php' ); ?>
	</div>



	<?php echo $composer->getFields(); ?>

	<?php if( !$system->my->id && $acl->allowed('add_question', 0)) { ?>
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

	<?php if( $recaptcha = $this->getRecaptcha() ){ ?>
	<hr />
	<div class="control-group">
		<div id="post_new_antispam"><?php echo $recaptcha; ?></div>
	</div>
	<?php }else if( DiscussHelper::getHelper( 'Captcha' )->showCaptcha() ){ ?>
		<?php echo DiscussHelper::getHelper( 'Captcha' )->getHTML();?>
	<?php } ?>

	<div class="modal-footer">
		<div class="row-fluid">
			<div class="pull-left">
				<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss' );?>" class="btn btn-medium btn-danger"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL'); ?></a>
			</div>

			<div class="pull-right">
				<input type="button" class="btn btn-medium btn-primary submitDiscussion" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_SUBMIT' , true ); ?>" />
			</div>
		</div>
	</div>

	<div class="clearfix"></div>
</div>

<?php if( !empty( $reference ) && !empty( $referenceId ) ){ ?>
<input type="hidden" name="reference" value="<?php echo $reference; ?>" />
<input type="hidden" name="reference_id" value="<?php echo $referenceId; ?>" />
<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
<?php } ?>

<?php echo JHTML::_( 'form.token' ); ?>
</form>
