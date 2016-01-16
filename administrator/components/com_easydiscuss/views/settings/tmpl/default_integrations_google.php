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
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_GOOGLE_INTEGRATIONS_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_GOOGLE_INTEGRATIONS_DESC' );?>
		</p>
		<!-- <a href="javascript:void(0)" class="btn btn-success">Documentation</a> -->
	</div>
</div>

<div class="row-fluid ">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span4 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_ENABLE' ); ?>
							</label>
						</div>
						<div class="span8"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_ENABLE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_ENABLE_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_google_adsense_enable' , $this->config->get( 'integration_google_adsense_enable' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span4 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_CODE' ); ?>
							</label>
						</div>
						<div class="span8"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_CODE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_CODE_DESC'); ?>"
						>
							<textarea name="integration_google_adsense_code" class="input-full" style="margin-bottom: 10px;height: 75px;"><?php echo $this->config->get('integration_google_adsense_code');?></textarea>

						</div>
						<div class="notice offset4" style="text-align: left !important;"><?php echo JText::_('COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_CODE_EXAMPLE');?></div>
					</div>
					<div class="si-form-row">
						<div class="span4 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_DISPLAY' ); ?>
							</label>
						</div>
						<div class="span8"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_DISPLAY' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_DISPLAY_DESC'); ?>"
						>
							<?php
							$display = array();
							$display[] = JHTML::_('select.option', 'both', JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_BOTH' ) );
							$display[] = JHTML::_('select.option', 'header', JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_HEADER' ) );
							$display[] = JHTML::_('select.option', 'footer', JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_FOOTER' ) );
							$display[] = JHTML::_('select.option', 'beforereplies', JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_BEFORE_REPLIES' ) );
							$showOption = JHTML::_('select.genericlist', $display, 'integration_google_adsense_display', 'class="full-width" size="1" ', 'value', 'text', $this->config->get('integration_google_adsense_display' , 'both' ) );
							echo $showOption;
							?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span4 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_ADSENSE_DISPLAY_ACCESS' ); ?>
							</label>
						</div>
						<div class="span8"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_ADSENSE_DISPLAY_ACCESS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_INTEGRATIONS_ADSENSE_DISPLAY_ACCESS_DESC'); ?>"
						>
							<?php
							$display = array();
							$display[] = JHTML::_('select.option', 'both', JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_ADSENSE_DISPLAY_ALL' ) );
							$display[] = JHTML::_('select.option', 'members', JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_ADSENSE_DISPLAY_MEMBERS' ) );
							$display[] = JHTML::_('select.option', 'guests', JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_ADSENSE_DISPLAY_GUESTS' ) );
							$showOption = JHTML::_('select.genericlist', $display, 'integration_google_adsense_display_access', 'class="full-width" size="1" ', 'value', 'text', $this->config->get('integration_google_adsense_display_access' , 'both' ) );
							echo $showOption;
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="span6">

	</div>
</div>
