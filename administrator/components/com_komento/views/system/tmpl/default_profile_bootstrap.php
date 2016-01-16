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
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_PROFILE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Enable Profile System -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_PROFILE_ENABLE', 'profile_enable' ); ?>
				</tbody>
			</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_PROFILE_TAB' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Show Comments Tab -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_PROFILE_TAB_COMMENTS', 'profile_tab_comments' ); ?>

					<!-- Show Activities Tab -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_PROFILE_TAB_ACTIVITIES', 'profile_tab_activities' ); ?>

					<!-- Show Popular Comments Tab -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_PROFILE_TAB_POPULAR', 'profile_tab_popular' ); ?>

					<!-- Show Sticked Comments Tab -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_PROFILE_TAB_STICKED', 'profile_tab_sticked' ); ?>

				</tbody>
			</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_PROFILE_SETTINGS' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Username/Real Name/Stored Name -->
					<?php $options = array();
						$options[] = $this->renderOption( 'default', 'COM_KOMENTO_SETTINGS_NAME_TYPE_DEFAULT' );
						$options[] = $this->renderOption( 'username', 'COM_KOMENTO_SETTINGS_NAME_TYPE_USERNAME' );
						$options[] = $this->renderOption( 'name', 'COM_KOMENTO_SETTINGS_NAME_TYPE_NAME' );
						echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_COMMENTS_NAME_TYPE', 'name_type', 'dropdown', $options );
					?>

					<!-- Avatar Integration -->
					<?php
						$options = array();
						$options[] = $this->renderOption( 'communitybuilder', 'Community Builder' );
						$options[] = $this->renderOption( 'easyblog', 'Easyblog' );
						$options[] = $this->renderOption( 'easydiscuss', 'EasyDiscuss' );
						$options[] = $this->renderOption( 'k2', 'K2' );
						$options[] = $this->renderOption( 'gravatar', 'Gravatar' );
						$options[] = $this->renderOption( 'jomsocial', 'Jomsocial' );
						$options[] = $this->renderOption( 'kunena', 'Kunena' );
						$options[] = $this->renderOption( 'kunena3', 'Kunena 3' );
						$options[] = $this->renderOption( 'phpbb', 'PHPBB' );
						$options[] = $this->renderOption( 'hwdmediashare', 'HWDMediaShare' );
						$options[] = $this->renderOption( 'easysocial', 'EasySocial' );

						// $options[] = $this->renderOption( 'anahita', 'Anahita');
						// $options[] = $this->renderOption( 'mightyregistration', 'Mighty Registration');

						echo $this->renderSetting( 'COM_KOMENTO_LAYOUT_AVATAR_INTEGRATION', 'layout_avatar_integration', 'dropdown', $options );
					?>

					<!-- PHPBB Path -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_LAYOUT_LAYOUT_PHPBB_PATH', 'layout_phpbb_path', 'input', '60' ); ?>

					<!-- PHPBB Url -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_LAYOUT_LAYOUT_PHPBB_URL', 'layout_phpbb_url', 'input', '60' ); ?>

				</tbody>
			</table>
			</fieldset>
		</div>

		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_PROFILE_ACTIVITIES' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Show Comments -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_PROFILE_ACTIVITIES_COMMENTS', 'profile_activities_comments' ); ?>

					<!-- Show Replies -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_PROFILE_ACTIVITIES_REPLIES', 'profile_activities_replies' ); ?>

					<!-- Show Likes -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_PROFILE_ACTIVITIES_LIKES', 'profile_activities_likes' ); ?>

				</tbody>
			</table>
			</fieldset>
		</div>
	</div>
</div>

