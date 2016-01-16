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
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_DISCUSS' ); ?></legend>
			<?php if( Komento::getHelper( 'components' )->isInstalled( 'com_easydiscuss' ) ) { ?>
			<p><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_DISCUSS_INSTRUCTIONS' ); ?></p>
			<table class="admintable" cellspacing="1">
				<tbody>
					<!-- Enable Discuss Points -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_ENABLE_DISCUSS_POINTS', 'enable_discuss_points' ); ?>

					<!-- Enable Discuss Log -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_ENABLE_DISCUSS_LOG', 'enable_discuss_log' ); ?>
				</tbody>
			</table>
			<?php } else { ?>
				<img src="<?php echo JURI::root(); ?>administrator/components/com_komento/assets/images/easydiscuss.png" />
				<?php echo JText::_( 'COM_KOMENTO_WHAT_IS_EASYDISCUSS' ); ?>
				<a target="_blank" href="http://www.stackideas.com/easydiscuss.html"><?php echo JText::_( 'COM_KOMENTO_GET_EASYDISCUSS' ); ?></a>
			<?php } ?>
			</fieldset>
		</td>
		<td width="50%" valign="top">

		</td>
	</tr>
</table>
