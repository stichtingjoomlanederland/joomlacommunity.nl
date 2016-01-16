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
<div class="row-fluid">
	<div class="span12 panel-title">
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_TOOLBAR_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_TOOLBAR_DESC' );?>
		</p>
	</div>
</div>

<div class="row-fluid ">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#discuss-toolbar">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_TOOLBAR' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="discuss-toolbar" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_HEADERS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_HEADERS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_HEADERS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_headers' , $this->config->get( 'layout_headers' ) );?>
						</div>
					</div>
					
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TOOLBAR' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TOOLBAR' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_TOOLBAR_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_enabletoolbar' , $this->config->get( 'layout_enabletoolbar' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_CATEGORY_TREE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_CATEGORY_TREE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_CATEGORY_TREE_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_category_tree' , $this->config->get( 'layout_category_tree' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TAGS_BUTTON' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TAGS_BUTTON' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_TAGS_BUTTON_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_toolbartags' , $this->config->get( 'layout_toolbartags' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_CATEGORIES_BUTTON' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_CATEGORIES_BUTTON' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_CATEGORIES_BUTTON_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_toolbarcategories' , $this->config->get( 'layout_toolbarcategories' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_DISCUSSION_BUTTON' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_DISCUSSION_BUTTON' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_DISCUSSION_BUTTON_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_toolbardiscussion' , $this->config->get( 'layout_toolbardiscussion' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_PROFILE_BUTTON' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_PROFILE_BUTTON' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_PROFILE_BUTTON_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_toolbarprofile' , $this->config->get( 'layout_toolbarprofile' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_USERS_BUTTON' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_USERS_BUTTON' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_USERS_BUTTON_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_toolbarusers' , $this->config->get( 'layout_toolbarusers' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_TOOLBAR_LOGIN' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_TOOLBAR_LOGIN' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_LAYOUT_TOOLBAR_LOGIN_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_toolbarlogin' , $this->config->get( 'layout_toolbarlogin' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_TOOLBAR_BADGES' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_TOOLBAR_BADGES' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_LAYOUT_TOOLBAR_BADGES_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_toolbarbadges' , $this->config->get( 'layout_toolbarbadges' ) );?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#discuss-searchbar">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_TOOLBAR_SEARCHBAR' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="discuss-searchbar" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TOOLBAR_SEARCHBAR' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TOOLBAR_SEARCHBAR' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_TOOLBAR_SEARCHBAR_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_toolbar_searchbar' , $this->config->get( 'layout_toolbar_searchbar' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_ASK_BUTTON' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_ASK_BUTTON' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_ASK_BUTTON_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_toolbarcreate' , $this->config->get( 'layout_toolbarcreate' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_FILTER_CATEGORY' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_FILTER_CATEGORY' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_FILTER_CATEGORY_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_toolbar_cat_filter' , $this->config->get( 'layout_toolbar_cat_filter' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ASK_COLOR' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ASK_COLOR' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ASK_COLOR_DESC'); ?>"
						>
							<?php
								$colorType = array();
								$colorType[] = JHTML::_('select.option', ' ', JText::_( 'COM_EASYDISCUSS_ASK_COLOR_DEFAULT' ) );
								$colorType[] = JHTML::_('select.option', 'primary', JText::_( 'COM_EASYDISCUSS_ASK_COLOR_BLUE' ) );
								$colorType[] = JHTML::_('select.option', 'info', JText::_( 'COM_EASYDISCUSS_ASK_COLOR_LIGHT_BLUE' ) );
								$colorType[] = JHTML::_('select.option', 'success', JText::_( 'COM_EASYDISCUSS_ASK_COLOR_GREEN' ) );
								$colorType[] = JHTML::_('select.option', 'warning', JText::_( 'COM_EASYDISCUSS_ASK_COLOR_ORANGE' ) );
								$colorType[] = JHTML::_('select.option', 'danger', JText::_( 'COM_EASYDISCUSS_ASK_COLOR_RED' ) );
								$colorType[] = JHTML::_('select.option', 'inverse', JText::_( 'COM_EASYDISCUSS_ASK_COLOR_INVERSE' ) );
								$colorType[] = JHTML::_('select.option', 'link', JText::_( 'COM_EASYDISCUSS_ASK_COLOR_NONE' ) );

								$colorTypeHTML = JHTML::_('select.genericlist', $colorType, 'layout_ask_color', 'class="full-width" size="1" ', 'value', 'text', $this->config->get('layout_ask_color' , 'primary' ) );
								echo $colorTypeHTML;
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
