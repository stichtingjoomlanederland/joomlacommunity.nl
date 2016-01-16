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
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_JOMSOCIAL' ); ?></legend>
			<?php if( Komento::getHelper( 'components' )->isInstalled( 'com_community' ) ) { ?>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Post comments to Jomsocial -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_JOMSOCIAL_ENABLE_COMMENT', 'jomsocial_enable_comment' ); ?>

					<!-- Post replies to Jomsocial -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_JOMSOCIAL_ENABLE_REPLY', 'jomsocial_enable_reply' ); ?>

					<!-- Post likes to Jomsocial -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_JOMSOCIAL_ENABLE_LIKE', 'jomsocial_enable_like' ); ?>

					<!-- Comment length -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_JOMSOCIAL_COMMENT_LENGTH', 'jomsocial_comment_length', 'input' ); ?>

					<!-- JomSocial User Points -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_JOMSOCIAL_ENABLE_USERPOINTS', 'jomsocial_enable_userpoints' ); ?>
				</tbody>
			</table>
			<?php } else {
				echo JText::_( 'COM_KOMENTO_JOMSOCIAL_NOT_INSTALLED' );
			} ?>
			</fieldset>
		</td>
		<td width="50%" valign="top">

		</td>
	</tr>
</table>
