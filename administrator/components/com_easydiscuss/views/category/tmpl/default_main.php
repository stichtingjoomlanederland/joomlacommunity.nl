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
<div class="row-fluid ">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_SETTINGS' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">


					<div class="span12">
						<div class="alert mt-10" >
							<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_EDIT_PRIVACY_NOTICE'); ?>
						</div>
					</div>


					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_NAME' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_NAME' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_NAME_DESC'); ?>"
							>
							<input type="text" class="input-full" id="catname" name="title" size="55" maxlength="255" value="<?php echo $this->cat->title;?>" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_ALIAS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_ALIAS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_ALIAS_DESC'); ?>"
							>
							<input type="text" class="input-full" id="alias" name="alias" maxlength="255" value="<?php echo $this->cat->alias;?>" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_PUBLISHED' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_PUBLISHED' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_PUBLISHED_DESC'); ?>"
							>
							<?php echo $this->renderCheckbox( 'published' , $this->cat->published );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_PARENT_CATEGORY' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_PARENT_CATEGORY' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_PARENT_CATEGORY_DESC'); ?>"
							>
							<?php echo $this->parentList; ?>
						</div>
					</div>

					<?php if($this->config->get('layout_categoryavatar', true)) : ?>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_AVATAR' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_AVATAR' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_EDIT_AVATAR_DESC'); ?>"
							>
							<?php if(! empty($this->cat->avatar)) { ?>
								<div>
									<img style="border-style:solid; float:none;" src="<?php echo $this->cat->getAvatar(); ?>" width="60" height="60"/>
								</div>
								<div>
									[ <a href="index.php?option=com_easydiscuss&controller=category&task=removeAvatar&id=<?php echo $this->cat->id;?>&<?php echo DiscussHelper::getToken();?>=1"><?php echo JText::_( 'COM_EASYDISCUSS_REMOVE_AVATAR' ); ?></a> ]
								</div>
							<?php } ?>
								<div style="margin-top:5px;">
									<input id="file-upload" type="file" name="Filedata" class="input-full" size="33"/>
								</div>
						</div>
					</div>
					<?php endif; ?>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_CREATED' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_CREATED' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_CREATED_DESC'); ?>"
							>
							<?php echo DiscussDateHelper::toFormat( DiscussDateHelper::dateWithOffset( $this->cat->created ) , '%A, %B %d %Y, %I:%M %p' ); ?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_SHOW_DESCRIPTION' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_SHOW_DESCRIPTION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_EDIT_SHOW_DESCRIPTION_DESC'); ?>"
							>
							<?php echo $this->renderCheckbox( 'show_description' , $this->cat->getParam( 'show_description' , true ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span12 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_DESCRIPTION' ); ?>
							</label>
						</div>
						<div class="span12"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_DESCRIPTION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_DESCRIPTION_DESC'); ?>"
							>
							<?php echo $this->editor->display( 'description' , $this->cat->description , '100%' , '300' , 10 , 10 , array( 'zemanta' , 'readmore' , 'pagebreak' , 'article' , 'image' ) ); ?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_USE_AS_CONTAINER' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_USE_AS_CONTAINER' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_USE_AS_CONTAINER_DESC'); ?>"
							>
							<?php echo $this->renderCheckbox( 'container' , $this->cat->get( 'container' , false ) );?>
							<div class="alert mt-20"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_USE_AS_CONTAINER_INFO' ); ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option02">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_POST_PARAMETERS' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option02" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_POST_MAX_LENGTH' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_POST_MAX_LENGTH' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_EDIT_POST_MAX_LENGTH_DESC'); ?>"
							>
							<?php echo $this->renderCheckbox( 'maxlength' , $this->cat->getParam( 'maxlength' , false ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_POST_MAX_LENGTH_SIZE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_POST_MAX_LENGTH_SIZE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_EDIT_POST_MAX_LENGTH_SIZE_DESC'); ?>"
							>
							<input type="text" class="input-mini center" name="maxlength_size" id="maxlength_size" value="<?php echo $this->cat->getParam( 'maxlength_size' , 1000 );?>" />
							<span><?php echo JText::_( 'COM_EASYDISCUSS_CHARACTERS' ); ?></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option03">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_POST_NOTIFICATIONS' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option03" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_NOTIFY_CUSTOM_EMAIL_ADDRESS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_NOTIFY_CUSTOM_EMAIL_ADDRESS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_CATEGORY_NOTIFY_CUSTOM_EMAIL_ADDRESS_DESC'); ?>"
						>
							<input type="text" value="<?php echo $this->cat->getParam( 'cat_notify_custom' );?>" name="cat_notify_custom" class="input-full"/>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option04">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_EMAIL_PARSER' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option04" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_EMAIL_PARSER_SWITCH' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_EMAIL_PARSER_SWITCH' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_CATEGORY_EMAIL_PARSER_SWITCH_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'cat_email_parser_switch' , $this->cat->getParam( 'cat_email_parser_switch' , false ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_EMAIL_PARSER_ADDRESS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_EMAIL_PARSER_ADDRESS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_CATEGORY_EMAIL_PARSER_ADDRESS_DESC'); ?>"
						>
							<input type="text" value="<?php echo $this->cat->getParam( 'cat_email_parser' );?>" name="cat_email_parser" class="input-full"/>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_EMAIL_PARSER_PASSWORD' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_EMAIL_PARSER_PASSWORD' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_CATEGORY_EMAIL_PARSER_PASSWORD_DESC'); ?>"
						>
							<input name="cat_email_parser_password" value="<?php echo $this->cat->getParam( 'cat_email_parser_password' );?>" type="password" autocomplete="off" class="input-full"/>
						</div>
					</div>
				</div>

			</div>

		</div>

	</div>
</div>
