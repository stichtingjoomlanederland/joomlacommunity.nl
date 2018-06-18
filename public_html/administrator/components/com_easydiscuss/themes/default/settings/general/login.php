<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_MAIN'); ?>

			<div class="panel-body">
				<div class="form-horizontal">

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SELECT_LOGIN_PROVIDER'); ?>
						</div>
						<div class="col-md-7">
							<select name="main_login_provider" class="form-control" >
								<option value="easysocial"<?php echo $this->config->get('main_login_provider') == 'easysocial' ? ' selected="selected"' : '';?>><?php echo JText::_('EasySocial');?></option>
								<option value="joomla"<?php echo $this->config->get('main_login_provider') == 'joomla' ? ' selected="selected"' : '';?>><?php echo JText::_('Joomla');?></option>
								<option value="jomsocial"<?php echo $this->config->get('main_login_provider') == 'jomsocial' ? ' selected="selected"' : '';?>><?php echo JText::_('JomSocial');?></option>
								<option value="cb"<?php echo $this->config->get('main_login_provider') == 'cb' ? ' selected="selected"' : '';?>><?php echo JText::_('Community Builder');?></option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SELECT_LOGIN_REDIRECT'); ?>
						</div>
						<div class="col-md-7">
							<select name="main_login_redirect" class="form-control" >
								<option value="frontpage"<?php echo $this->config->get('main_login_redirect') == 'frontpage' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_REDIRECT_FRONTPAGE');?></option>
								<option value="same.page"<?php echo $this->config->get('main_login_redirect') == 'same.page' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_REDIRECT_SAME_PAGE');?></option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SELECT_LOGOUT_REDIRECT'); ?>
						</div>
						<div class="col-md-7">
							<select name="main_logout_redirect" class="form-control" >
								<option value="frontpage"<?php echo $this->config->get('main_logout_redirect') == 'frontpage' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_REDIRECT_FRONTPAGE');?></option>
								<option value="same.page"<?php echo $this->config->get('main_logout_redirect') == 'same.page' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_REDIRECT_SAME_PAGE');?></option>
								<option value="forums"<?php echo $this->config->get('main_logout_redirect') == 'forums' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_REDIRECT_FORUMS');?></option>
							</select>
						</div>
					</div>                  
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
	</div>
</div>