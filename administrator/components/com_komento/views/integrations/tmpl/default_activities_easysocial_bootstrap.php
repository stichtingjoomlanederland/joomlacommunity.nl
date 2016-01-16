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
<div class="row-fluid">
	<div class="span12">
		<div class="span6">
			<?php if( Komento::getHelper( 'components' )->isInstalled( 'com_easysocial' ) ) { ?>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_EASYSOCIAL_INFO' ); ?></legend>
			<p class="warning"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_EASYSOCIAL_WARNING' ) ;?></p>
			</fieldset>
			<?php } ?>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_EASYSOCIAL' ); ?></legend>
			<?php if( Komento::getHelper( 'components' )->isInstalled( 'com_easysocial' ) ) { ?>
			<table class="admintable" cellspacing="1">
				<tbody>
					<!-- Enable EasySocial Points -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_ENABLE_EASYSOCIAL_POINTS', 'enable_easysocial_points' ); ?>

					<!-- Enable EasySocial Badges -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_ENABLE_EASYSOCIAL_BADGES', 'enable_easysocial_badges' ); ?>

					<!-- Enable EasySocial Sream Comment -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_ENABLE_EASYSOCIAL_STREAM_COMMENT', 'enable_easysocial_stream_comment' ); ?>

					<!-- Enable EasySocial Sream Like -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_ENABLE_EASYSOCIAL_STREAM_LIKE', 'enable_easysocial_stream_like' ); ?>
				</tbody>
			</table>
			<?php } else { ?>
				<img src="<?php echo JURI::root(); ?>administrator/components/com_komento/assets/images/easysocial.png" />
				<?php echo JText::_( 'COM_KOMENTO_WHAT_IS_EASYSOCIAL' ); ?>
				<a target="_blank" href="http://www.stackideas.com/easysocial.html"><?php echo JText::_( 'COM_KOMENTO_GET_EASYSOCIAL' ); ?></a>
			<?php } ?>
			</fieldset>
		</div>

		<div class="span6">
			<?php if( Komento::getHelper( 'components' )->isInstalled( 'com_easysocial' ) ) { ?>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_EASYSOCIAL_INFO' ); ?></legend>
			<p class="warning"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_EASYSOCIAL_SYNC_WARNING' ) ;?></p>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_EASYSOCIAL_SYNC' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Enable EasySocial Sync Comment -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_ENABLE_EASYSOCIAL_SYNC_COMMENT', 'enable_easysocial_sync_comment' ); ?>

					<!-- Enable EasySocial Sync Like -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_ENABLE_EASYSOCIAL_SYNC_LIKE', 'enable_easysocial_sync_like' ); ?>

				</tbody>
			</table>
			</fieldset>
			<?php } ?>
		</div>
	</div>
</div>
