<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
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
					<div class="o-form-horizontal">
						<?php echo $this->html('forms.textbox', 'title', 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_NAME', $this->html('string.escape', $category->title)); ?>
						<?php echo $this->html('forms.textbox', 'alias', 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_ALIAS', $this->html('string.escape', $category->alias)); ?>
						<?php echo $this->html('forms.toggle', 'container', 'COM_EASYDISCUSS_CATEGORIES_USE_AS_CONTAINER', $category->container, '', 'COM_EASYDISCUSS_CATEGORIES_USE_AS_CONTAINER_INFO'); ?>
						<?php echo $this->html('forms.toggle', 'published', 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_PUBLISHED', $category->published, '', 'COM_EASYDISCUSS_CATEGORIES_USE_AS_CONTAINER_INFO'); ?>

						<div class="o-form-group">
							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_LANGUAGE'); ?>
							</div>
							<div class="col-md-7">
								<select id="language" class="o-form-select" name="language">
									<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $category->language);?>
								</select>
							</div>
						</div>

						<?php if ($categories) { ?>
						<div class="o-form-group">
							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_PARENT_CATEGORY'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $categories; ?>
							</div>
						</div>
						<?php } ?>

						<div class="o-form-group">
							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORIES_EDIT_AVATAR'); ?>
							</div>
							<div class="col-md-7">
								<div>
									<img style="border-style:solid; float:none;" src="<?php echo $category->getAvatar(); ?>" width="60" height="60"/>
								</div>
								<?php if ($category->avatar) { ?>
									<div>
										[ <a href="index.php?option=com_easydiscuss&controller=category&task=removeAvatar&id=<?php echo $category->id;?>&<?php echo ED::getToken();?>=1"><?php echo JText::_( 'COM_EASYDISCUSS_REMOVE_AVATAR' ); ?></a> ]
									</div>
								<?php } ?>
								<div style="margin-top:5px;">
									<input id="file-upload" type="file" name="Filedata" class="o-form-control" size="33"/>
								</div>
							</div>
						</div>

						<?php echo $this->html('forms.iconpicker', 'cat_icon', 'COM_ED_POST_TYPES_ICON', $category->getParam('cat_icon', ''), ''); ?>

						<?php echo $this->html('forms.colorpicker', 'cat_colour', 'COM_ED_CATEGORY_COLOUR', $category->getParam('cat_colour', '#000000'), '#000'); ?>

						<?php echo $this->html('forms.toggle', 'show_description', 'COM_EASYDISCUSS_CATEGORIES_EDIT_SHOW_DESCRIPTION', $category->getParam('show_description', true)); ?>

						<div class="o-form-group">
							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_DESCRIPTION'); ?>
							</div>
						</div>
						<div class="o-form-group">
							<div class="col-md-12">
								<?php echo $editor->display('description' , $category->description , '100%' , '300' , 10 , 10 , false); ?>
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
					<div class="o-form-horizontal">
						<?php echo $this->html('forms.toggle', 'cat_default_private', 'COM_ED_DEFAULT_PRIVATE_POST', $category->getParam('cat_default_private', false)); ?>
						<?php echo $this->html('forms.toggle', 'cat_enforce_private', 'COM_ED_ENFORCE_PRIVATE_POST', $category->getParam('cat_enforce_private', false)); ?>
					</div>
				</div>
			</div>
			<?php } ?>

			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CATEGORY_EMAIL_PARSER'); ?>

				<div class="panel-body">
					<div class="o-form-horizontal">
						<?php echo $this->html('forms.toggle', 'cat_email_parser_switch', 'COM_EASYDISCUSS_CATEGORY_EMAIL_PARSER_SWITCH', $category->getParam('cat_email_parser_switch', false)); ?>
						<?php echo $this->html('forms.textbox', 'cat_email_parser', 'COM_EASYDISCUSS_CATEGORY_EMAIL_PARSER_ADDRESS', $category->getParam('cat_email_parser')); ?>
						<?php echo $this->html('forms.password', 'cat_email_parser_password', 'COM_EASYDISCUSS_CATEGORY_EMAIL_PARSER_PASSWORD', $category->getParam('cat_email_parser_password'),
							array('attributes' => 'autocomplete="new-password"')
						); ?>
					</div>

				</div>
			</div>
			
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CATEGORY_POST_PARAMETERS'); ?>

				<div class="panel-body">
					<div class="o-form-horizontal">
						<?php echo $this->html('forms.toggle', 'maxlength', 'COM_EASYDISCUSS_CATEGORIES_EDIT_POST_MAX_LENGTH', $category->getParam('maxlength', false)); ?>

						<?php echo $this->html('forms.textbox', 'maxlength_size', 'COM_EASYDISCUSS_CATEGORIES_EDIT_POST_MAX_LENGTH_SIZE', $category->getParam('maxlength_size', 1000),
							array('postfix' => 'COM_EASYDISCUSS_CHARACTERS' , 'size' => 7, 'class' => 'text-center')
						); ?>
					</div>
				</div>
			</div>

			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CATEGORY_POST_NOTIFICATIONS'); ?>

				<div class="panel-body">
					<div class="o-form-horizontal">
						<?php echo $this->html('forms.textbox', 'cat_notify_custom', 'COM_EASYDISCUSS_CATEGORY_NOTIFY_CUSTOM_EMAIL_ADDRESS', $category->getParam('cat_notify_custom')); ?>
					</div>
				</div>
			</div>

			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_ED_CATEGORY_CUSTOM_FIELDS'); ?>

				<div class="panel-body">
					<div class="o-form-horizontal">
						<div class="o-form-group">
							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_ED_CATEGORY_SELECT_FIELDS', '', 'field_group'); ?>
							</div>

							<div class="col-md-7">
								<?php echo $this->html('form.dropdown', 'custom_fields[]', $customFields, $selectedCF, 'multiple="true"'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
