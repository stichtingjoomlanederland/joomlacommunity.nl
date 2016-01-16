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
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SUBSCRIPTION_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SUBSCRIPTION_DESC' );?>
		</p>
	</div>
</div>

<div class="row-fluid ">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_SUBSCRIPTION' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_SITE_SUBSCRIPTION' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_SITE_SUBSCRIPTION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_SITE_SUBSCRIPTION_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_sitesubscription' , $this->config->get( 'main_sitesubscription' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_CATEGORIES_SUBSCRIPTION' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_CATEGORIES_SUBSCRIPTION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_CATEGORIES_SUBSCRIPTION_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_ed_categorysubscription' , $this->config->get( 'main_ed_categorysubscription' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POST_SUBSCRIPTION' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POST_SUBSCRIPTION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_POST_SUBSCRIPTION_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_postsubscription' , $this->config->get( 'main_postsubscription' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_AUTO_POST_SUBSCRIPTION' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_AUTO_POST_SUBSCRIPTION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_AUTO_POST_SUBSCRIPTION_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_autopostsubscription' , $this->config->get( 'main_autopostsubscription' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_GUEST_SUBSCRIPTION' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_GUEST_SUBSCRIPTION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_GUEST_SUBSCRIPTION_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_allowguestsubscribe' , $this->config->get( 'main_allowguestsubscribe' ) );?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>




