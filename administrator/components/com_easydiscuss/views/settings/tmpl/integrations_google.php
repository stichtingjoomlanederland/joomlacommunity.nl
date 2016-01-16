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
<table class="noshow">
	<tr>
		<td width="50%" valign="top">
			<!-- right -->
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE' ); ?></legend>

			<table class="admintable" cellspacing="1">
				<tbody>

				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_ENABLE' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_ENABLE_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_ENABLE' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'integration_google_adsense_enable' , $this->config->get( 'integration_google_adsense_enable' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key" valign="top">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_CODE' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_CODE_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_CODE' ); ?>
					</span>
					</td>
					<td valign="top">
						<textarea name="integration_google_adsense_code" class="input-full" style="margin-bottom: 10px;height: 75px;"><?php echo $this->config->get('integration_google_adsense_code');?></textarea>
						<div class="notice full-width" style="text-align: left !important;"><?php echo JText::_('COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_CODE_EXAMPLE');?></div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_DISPLAY' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_DISPLAY_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_DISPLAY' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php
						$display = array();
						$display[] = JHTML::_('select.option', 'both', JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_BOTH' ) );
						$display[] = JHTML::_('select.option', 'header', JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_HEADER' ) );
						$display[] = JHTML::_('select.option', 'footer', JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_FOOTER' ) );
						$display[] = JHTML::_('select.option', 'beforereplies', JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_BEFORE_REPLIES' ) );
						$showOption = JHTML::_('select.genericlist', $display, 'integration_google_adsense_display', 'size="1" ', 'value', 'text', $this->config->get('integration_google_adsense_display' , 'both' ) );
						echo $showOption;
						?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_ADSENSE_DISPLAY_ACCESS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_INTEGRATIONS_ADSENSE_DISPLAY_ACCESS_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_ADSENSE_DISPLAY_ACCESS' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php
						$display = array();
						$display[] = JHTML::_('select.option', 'both', JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_ADSENSE_DISPLAY_ALL' ) );
						$display[] = JHTML::_('select.option', 'members', JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_ADSENSE_DISPLAY_MEMBERS' ) );
						$display[] = JHTML::_('select.option', 'guests', JText::_( 'COM_EASYDISCUSS_INTEGRATIONS_ADSENSE_DISPLAY_GUESTS' ) );
						$showOption = JHTML::_('select.genericlist', $display, 'integration_google_adsense_display_access', 'size="1" ', 'value', 'text', $this->config->get('integration_google_adsense_display_access' , 'both' ) );
						echo $showOption;
						?>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>

		</td>
		<td width="50%" valign="top">&nbsp;</td>
	</tr>
</table>
