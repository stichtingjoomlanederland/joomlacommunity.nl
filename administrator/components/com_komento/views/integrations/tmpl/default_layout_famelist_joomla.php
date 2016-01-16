<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access'); ?>

<table class="noshow">
	<tr>
		<td width="50%" valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_STICKIES' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Enable Stickies -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_STICKIES_ENABLE', 'enable_stickies' ); ?>

					<!-- Max Stickies -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_MAX_STICKIES', 'max_stickies', 'input', '1' ); ?>

				</tbody>
			</table>
			</fieldset>
		</td>

		<td width="50%" valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_LOVIES' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Enable Lovies -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_LOVIES_ENABLE', 'enable_lovies' ); ?>

					<!-- Lovies threshold -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_LOVIES_THRESHOLD', 'minimum_likes_lovies', 'input' ); ?>

					<!-- Max Stickies -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_MAX_LOVIES', 'max_lovies', 'input' ); ?>

				</tbody>
			</table>
			</fieldset>
		</td>
	</tr>
</table>
