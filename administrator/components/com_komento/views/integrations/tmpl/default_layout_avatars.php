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
		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_LAYOUT_AVATAR' ); ?></div>
			<div class="panel-body">
					<!-- Avatar Integration -->
					<?php
						$options = array();
						$options[] = array( 'communitybuilder', 'Community Builder' );
						$options[] = array( 'easyblog', 'Easyblog' );
						$options[] = array( 'easydiscuss', 'EasyDiscuss' );
						$options[] = array( 'k2', 'K2' );
						$options[] = array( 'gravatar', 'Gravatar' );
						$options[] = array( 'jomsocial', 'Jomsocial' );
						$options[] = array( 'kunena', 'Kunena' );
						$options[] = array( 'kunena3', 'Kunena 3' );
						$options[] = array( 'phpbb', 'PHPBB' );
						$options[] = array( 'hwdmediashare', 'HWDMediaShare' );
						$options[] = array( 'easysocial', 'EasySocial' );
						$options[] = array( 'joomprofile', 'JoomProfile' );

						// $options[] = array( 'anahita', 'Anahita');
						// $options[] = array( 'mightyregistration', 'Mighty Registration');

						echo $this->renderSetting( 'COM_KOMENTO_LAYOUT_AVATAR_INTEGRATION', 'layout_avatar_integration', 'dropdown', $options );
					?>

					<!-- Use Komento Profile -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_LAYOUT_AVATAR_USE_KOMENTO_PROFILE', 'use_komento_profile' ); ?>

					<!-- Komento Profile Warning -->
					<?php echo $this->renderText( JText::_( 'COM_KOMENTO_LAYOUT_AVATAR_KOMENTO_PROFILE_WARNING' ), 'warning' ); ?>

					<!-- Use EasySocial Profile Popbox -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_LAYOUT_AVATAR_USE_EASYSOCIAL_PROFILE_POPBOX', 'easysocial_profile_popbox' ); ?>

					<!-- Gravatar Default -->
					<?php $options = array();
						$options[] = array( 'mm', 'Mysteryman');
						$options[] = array( 'identicon', 'Identicon' );
						$options[] = array( 'monsterid', 'Monsterid' );
						$options[] = array( 'wavatar', 'Wavatar' );
						$options[] = array( 'retro', 'Retro' );
						echo $this->renderSetting( 'COM_KOMENTO_LAYOUT_AVATAR_GRAVATAR_DEFAULT_PICTURE', 'gravatar_default_avatar', 'dropdown', $options ); ?>

					<!-- PHPBB Path -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_LAYOUT_LAYOUT_PHPBB_PATH', 'layout_phpbb_path', 'input', '60' ); ?>

					<!-- PHPBB Url -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_LAYOUT_LAYOUT_PHPBB_URL', 'layout_phpbb_url', 'input', '60' ); ?>
			</div>
		</fieldset>
	</div>
</div>
