<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="row">
	<div class="col-md-6">
		<?php if( Komento::getHelper( 'components' )->isInstalled( 'com_easysocial' ) ) { ?>
		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_EASYSOCIAL_INFO' ); ?></div>
			<div class="panel-body">
				<p class="warning"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_EASYSOCIAL_WARNING' ) ;?></p>
			</div>
		</fieldset>
		<?php } ?>

		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_EASYSOCIAL' ); ?></div>
			<div class="panel-body">
				<?php if( Komento::getHelper( 'components' )->isInstalled( 'com_easysocial' ) ) { ?>
					<!-- Enable EasySocial Points -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_ENABLE_EASYSOCIAL_POINTS', 'enable_easysocial_points' ); ?>

					<!-- Enable EasySocial Badges -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_ENABLE_EASYSOCIAL_BADGES', 'enable_easysocial_badges' ); ?>

					<!-- Enable EasySocial Sream Comment -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_ENABLE_EASYSOCIAL_STREAM_COMMENT', 'enable_easysocial_stream_comment' ); ?>

					<!-- Enable EasySocial Sream Like -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_ENABLE_EASYSOCIAL_STREAM_LIKE', 'enable_easysocial_stream_like' ); ?>
				<?php } else { ?>
					<img src="<?php echo JURI::root(); ?>administrator/components/com_komento/assets/images/easysocial.png" />
					<?php echo JText::_( 'COM_KOMENTO_WHAT_IS_EASYSOCIAL' ); ?>
					<a target="_blank" href="http://www.stackideas.com/easysocial.html"><?php echo JText::_( 'COM_KOMENTO_GET_EASYSOCIAL' ); ?></a>
				<?php } ?>
			</div>
		</fieldset>
	</div>

	<div class="col-md-6">
		<?php if( Komento::getHelper( 'components' )->isInstalled( 'com_easysocial' ) ) { ?>

		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_EASYSOCIAL_INFO' ); ?></div>
			<div class="panel-body">
				<p class="warning"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_EASYSOCIAL_SYNC_WARNING' ) ;?></p>
			</div>
		</fieldset>

		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_EASYSOCIAL_SYNC' ); ?></div>
			<div class="panel-body">
				<!-- Enable EasySocial Sync Comment -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_ENABLE_EASYSOCIAL_SYNC_COMMENT', 'enable_easysocial_sync_comment' ); ?>

				<!-- Enable EasySocial Sync Like -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_ENABLE_EASYSOCIAL_SYNC_LIKE', 'enable_easysocial_sync_like' ); ?>
			</div>
		</fieldset>
		<?php } ?>
	</div>
</div>
