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
defined('_JEXEC') or die('Restricted access');
?>
<table class="noshow">
	<tr>
		<td width="50%" valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- System environment -->
					<?php $options = array();
						$options[] = array( 'static', JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_ENVIRONMENT_STATIC' ) );
						$options[] = array( 'optimized', JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_ENVIRONMENT_OPTIMIZED' ) );
						$options[] = array( 'development', JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_ENVIRONMENT_DEVELOPMENT' ) );
						echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SYSTEM_ENVIRONMENT', 'komento_environment', 'dropdown', $options ); ?>

					<tr>
						<td width="300" class="key"></td>
						<td valign="top">
							<p><strong><?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_ENVIRONMENT_STATIC' ); ?></strong> - <?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_ENVIRONMENT_STATIC_DESC' ); ?></p>
							<p><strong><?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_ENVIRONMENT_OPTIMIZED' ); ?></strong> - <?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_ENVIRONMENT_OPTIMIZED_DESC' ); ?></p>
							<p><strong><?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_ENVIRONMENT_DEVELOPMENT' ); ?></strong> - <?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_ENVIRONMENT_DEVELOPMENT_DESC' ); ?></p>
						</td>
					</tr>

					<!-- JavaScript compression -->
					<?php $options = array();
						$options[] = array( 'compressed', JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_MODE_COMPRESSED' ) );
						$options[] = array( 'uncompressed', JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_MODE_UNCOMPRESSED' ) );

						echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SYSTEM_MODE', 'komento_mode', 'dropdown', $options ); ?>

					<tr>
						<td width="300" class="key"></td>
						<td valign="top">
							<p><strong><?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_MODE_COMPRESSED' ); ?></strong> - <?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_MODE_COMPRESSED_DESC' ); ?></p>
							<p><strong><?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_MODE_UNCOMPRESSED' ); ?></strong> - <?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_MODE_UNCOMPRESSED_DESC' ); ?></p>
							<p><?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_MODE_DEVELOPMENT_DESC' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td valign="top">

		</td>
	</tr>
</table>
