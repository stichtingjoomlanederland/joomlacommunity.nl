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
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_PASSWORD_PROTECT_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_PASSWORD_PROTECT_DESC' );?>
		</p>
	</div>
</div>


<div class="row-fluid ">
	<div class="span6">

		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option11">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_SIDEBAR_SETTINGS_TABS_PASSWORD_PROTECT' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div id="option11" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_PASSWORD_PROTECTION' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_PASSWORD_PROTECTION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_PASSWORD_PROTECTION_DESC'); ?>">
							<?php echo $this->renderCheckbox( 'main_password_protection' , $this->config->get( 'main_password_protection' ) );?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="span6"></div>
</div>
