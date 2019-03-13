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
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_SEO_ADVANCED'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_sef_unicode', 'COM_EASYDISCUSS_MAIN_SEO_ALLOW_UNICODE_ALIAS'); ?>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_MAIN_SEO_USER_PERMALINK_FORMAT'); ?>
						</div>
						<div class="col-md-7">
							<select name="main_sef_user">
								<option value="default"<?php echo $this->config->get('main_sef_user') == 'default' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_DEFAULT'); ?></option>
								<option value="username"<?php echo $this->config->get('main_sef_user') == 'username' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_USERNAME'); ?></option>
								<option value="realname"<?php echo $this->config->get('main_sef_user') == 'realname' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_REALNAME'); ?></option>
							</select>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_MAIN_SEO_ROUTING_BEHAVIOR'); ?>
						</div>
						
						<div class="col-md-7">
							<div class="ed-radio">
								<input type="radio" value="currentactive" id="main_routing1" name="main_routing"<?php echo $this->config->get('main_routing') == 'currentactive' ? ' checked="checked"' : '';?>>
								<label for="main_routing1">
									<?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_ROUTING_BEHAVIOR_USE_CURRENT_ACTIVEMENU');?>
								</label>
							</div>

							<div class="ed-radio">
								<input type="radio" value="auto" id="main_routing2" name="main_routing"<?php echo $this->config->get('main_routing') == 'auto' ? ' checked="checked"' : '';?>>
								<label for="main_routing2">
									<?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_ROUTING_BEHAVIOR_USE_AUTO');?>
								</label>
							</div>

							<div class="ed-radio">
								<input type="radio" value="menuitem" id="main_routing_itemid" name="main_routing"<?php echo $this->config->get('main_routing') == 'menuitem' ? ' checked="checked"' : '';?>>
								<label for="main_routing_itemid">
									<?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_ROUTING_BEHAVIOR_USE_MENUITEM');?>
									<input type="text" name="main_routing_itemid" class="inputbox" style="width: 50px; display: inline;" value="<?php echo $this->config->get('main_routing_itemid');?>" />
								</label>
							</div>

							<div class="mt-20">
								<?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_ROUTING_BEHAVIOR_NOTICE'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_SEO_POST_PERMALINK'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_WORKFLOW_SEF_FORMAT'); ?>
						</div>
		

						<div class="col-md-7">
							<div class="form-control-static"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_WORKFLOW_SEF_FORMAT_NOTICE');?></div>

							<div class="ed-radio">
								<input type="radio" value="default" id="defaultEntry" name="main_sef"<?php echo $this->config->get('main_sef') == 'default' ? ' checked="checked"' : '';?>>
								<label for="defaultEntry">
									<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_WORKFLOW_SEF_FORMAT_TITLE_TYPE');?>
								</label>
								<p class="list-group-item-text">
									http://yoursite.com/menu/view/title
								</p>
							</div>

							<div class="ed-radio">
								<input type="radio" value="category" id="categoryEntry" name="main_sef"<?php echo $this->config->get('main_sef') == 'category' ? ' checked="checked"' : '';?>>
								<label for="categoryEntry">
									<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_WORKFLOW_SEF_FORMAT_CATEGORY_TYPE');?>
								</label>
								<p class="list-group-item-text">
									http://yoursite.com/menu/category/title
								</p>
							</div>

							<div class="ed-radio">
								<input type="radio" value="simple" id="simpleEntry" name="main_sef"<?php echo $this->config->get('main_sef') == 'simple' ? ' checked="checked"' : '';?>>
								<label for="simpleEntry">
									<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_WORKFLOW_SEF_FORMAT_SIMPLE_TYPE');?>
								</label>
								<p class="list-group-item-text">
									http://yoursite.com/menu/title
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>