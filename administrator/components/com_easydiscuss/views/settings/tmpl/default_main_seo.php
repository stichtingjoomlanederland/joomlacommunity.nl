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
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SEO_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SEO_DESC' );?>
		</p>
		<!-- <a href="javascript:void(0)" class="btn btn-success">Documentation</a> -->
	</div>
</div>

<div class="row-fluid ">
	<div class="span6">

		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SEO_ADVANCED' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span4 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_SEO_ROUTING_BEHAVIOR' ); ?>
							</label>
						</div>
						<div class="span8"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_SEO_ROUTING_BEHAVIOR' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_ROUTING_BEHAVIOR_DESC'); ?>"
	  					>
							<div>
								<input type="radio" class="inputbox" value="currentactive" id="main_routing1" name="main_routing"<?php echo $this->config->get('main_routing') == 'currentactive' ? ' checked="checked"' : '';?>>
								<label for="main_routing1">
									<?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_ROUTING_BEHAVIOR_USE_CURRENT_ACTIVEMENU');?>
								</label>
							</div>

							<div>
								<input type="radio" class="inputbox" value="auto" id="main_routing2" name="main_routing"<?php echo $this->config->get('main_routing') == 'auto' ? ' checked="checked"' : '';?>>
								<label for="main_routing2">
									<?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_ROUTING_BEHAVIOR_USE_AUTO');?>
								</label>
							</div>

							<div class="row-fluid">
								<input type="radio" class="inputbox" value="menuitem" id="main_routing_itemid" name="main_routing"<?php echo $this->config->get('main_routing') == 'menuitem' ? ' checked="checked"' : '';?>>
								<label for="main_routing_itemid" style="display: inline;">
									<?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_ROUTING_BEHAVIOR_USE_MENUITEM');?>
									
								</label>
								<input type="text" name="main_routing_itemid" class="inputbox" style="width: 50px;vertical-align:top" value="<?php echo $this->config->get('main_routing_itemid' );?>" />
							</div>



							<div class="notice">
								<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_SEO_ROUTING_BEHAVIOR_NOTICE' ); ?>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
	<div class="span6">

		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SEO_GENERAL' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_SEO_ALLOW_UNICODE_ALIAS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_SEO_ALLOW_UNICODE_ALIAS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_ALLOW_UNICODE_ALIAS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_sef_unicode' , $this->config->get( 'main_sef_unicode' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_USER_PERMALINK_FORMAT'); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_USER_PERMALINK_FORMAT'); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_USER_PERMALINK_FORMAT_DESC'); ?>"
						>
							<select name="main_sef_user">
								<option value="default"<?php echo $this->config->get('main_sef_user') == 'default' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_DEFAULT'); ?></option>
								<option value="username"<?php echo $this->config->get('main_sef_user') == 'username' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_USERNAME'); ?></option>
								<option value="realname"<?php echo $this->config->get('main_sef_user') == 'realname' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_REALNAME'); ?></option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>

