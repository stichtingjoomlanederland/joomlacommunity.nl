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
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LAYOUT_PROFILE_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LAYOUT_PROFILE_DESC' );?>
		</p>
		<!-- <a href="javascript:void(0)" class="btn btn-success">Documentation</a> -->
	</div>
</div>

<div class="row-fluid ">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_EDIT_PROFILE' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_BIOGRAPHY' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_BIOGRAPHY' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_BIOGRAPHY_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_profile_showbiography' , $this->config->get( 'layout_profile_showbiography' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_SOCIAL' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_SOCIAL' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_SOCIAL_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_profile_showsocial' , $this->config->get( 'layout_profile_showsocial' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_ACCOUNT' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_ACCOUNT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_ACCOUNT_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_profile_showaccount' , $this->config->get( 'layout_profile_showaccount' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_LOCATION' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_LOCATION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_LOCATION_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_profile_showlocation' , $this->config->get( 'layout_profile_showlocation' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_URL' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_URL' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_URL_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_profile_showurl' , $this->config->get( 'layout_profile_showurl' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_SITE_DETAILS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_SITE_DETAILS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_SITE_DETAILS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_profile_showsite' , $this->config->get( 'layout_profile_showsite' ) );?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="span6">

	</div>
</div>
