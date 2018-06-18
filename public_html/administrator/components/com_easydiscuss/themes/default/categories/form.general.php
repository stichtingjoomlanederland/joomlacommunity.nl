<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="general" class="tab-pane <?php echo $active == 'general' ? 'active in' : '';?>">
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CATEGORY_SETTINGS'); ?>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_NAME'); ?>
							</div>
							<div class="col-md-7">
								<input type="text" class="form-control" id="catname" name="title" size="55" maxlength="255" value="<?php echo $category->title;?>" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_ALIAS'); ?>
							</div>
							<div class="col-md-7">
								<input type="text" class="form-control" id="alias" name="alias" maxlength="255" value="<?php echo $category->alias;?>" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORIES_USE_AS_CONTAINER'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.boolean', 'container', $category->container);?>
								<p class="small"><?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_USE_AS_CONTAINER_INFO'); ?></p>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_PUBLISHED'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.boolean', 'published', $category->published); ?>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_LANGUAGE'); ?>
							</div>
							<div class="col-md-7">
								<select id="language" class="form-control" name="language">
									<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $category->language);?>
								</select>
							</div>
						</div>

						<?php if ($categories) { ?>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_PARENT_CATEGORY'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $categories; ?>
							</div>
						</div>
						<?php } ?>

						<?php if($this->config->get('layout_categoryavatar', true)) : ?>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORIES_EDIT_AVATAR'); ?>
							</div>
							<div class="col-md-7">
								<div>
									<img style="border-style:solid; float:none;" src="<?php echo $category->getAvatar(); ?>" width="60" height="60"/>
								</div>
								<?php if ($category->avatar) { ?>
									<div>
										[ <a href="index.php?option=com_easydiscuss&controller=category&task=removeAvatar&id=<?php echo $category->id;?>&<?php echo DiscussHelper::getToken();?>=1"><?php echo JText::_( 'COM_EASYDISCUSS_REMOVE_AVATAR' ); ?></a> ]
									</div>
								<?php } ?>
								<div style="margin-top:5px;">
									<input id="file-upload" type="file" name="Filedata" class="form-control" size="33"/>
								</div>
							</div>
						</div>
						<?php endif; ?>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORIES_EDIT_SHOW_DESCRIPTION'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.boolean', 'show_description', $category->getParam('show_description', true)); ?>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_DESCRIPTION'); ?>
							</div>

						</div>
						<div class="form-group">
							<div class="col-md-12">
								<?php echo $editor->display( 'description' , $category->description , '100%' , '300' , 10 , 10 , array( 'zemanta' , 'readmore' , 'pagebreak' , 'article' , 'image' ) ); ?>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<?php if ($this->config->get('main_private_post')) { ?>
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_ED_CATEGORY_PRIVATE_POSTINGS'); ?>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_ED_DEFAULT_PRIVATE_POST'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.boolean', 'cat_default_private', $category->getParam('cat_default_private', false));?>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_ED_ENFORCE_PRIVATE_POST'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.boolean', 'cat_enforce_private', $category->getParam('cat_enforce_private', false));?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>

			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CATEGORY_EMAIL_PARSER'); ?>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORY_EMAIL_PARSER_SWITCH'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.boolean', 'cat_email_parser_switch', $category->getParam('cat_email_parser_switch', false));?>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORY_EMAIL_PARSER_ADDRESS'); ?>
							</div>
							<div class="col-md-7">
								<input type="text" value="<?php echo $category->getParam( 'cat_email_parser' );?>" name="cat_email_parser" class="form-control"/>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORY_EMAIL_PARSER_PASSWORD'); ?>
							</div>
							<div class="col-md-7">
								<input name="cat_email_parser_password" value="<?php echo $category->getParam( 'cat_email_parser_password' );?>" type="password" autocomplete="off" class="form-control"/>
							</div>
						</div>
					</div>

				</div>
			</div>
			
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CATEGORY_POST_PARAMETERS'); ?>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORIES_EDIT_POST_MAX_LENGTH'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.boolean', 'maxlength', $category->getParam('maxlength', false)); ?>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORIES_EDIT_POST_MAX_LENGTH_SIZE'); ?>
							</div>
							<div class="col-md-7">
								<input type="text" class="form-control form-control-sm text-center t-lg-mr--md" name="maxlength_size" id="maxlength_size" value="<?php echo $category->getParam( 'maxlength_size' , 1000 );?>" />
								<span><?php echo JText::_( 'COM_EASYDISCUSS_CHARACTERS' ); ?></span>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CATEGORY_POST_NOTIFICATIONS'); ?>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORY_NOTIFY_CUSTOM_EMAIL_ADDRESS'); ?>
							</div>
							<div class="col-md-7">
								<input type="text" value="<?php echo $category->getParam( 'cat_notify_custom' );?>" name="cat_notify_custom" class="form-control"/>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
