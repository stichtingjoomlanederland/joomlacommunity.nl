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
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_SHARE_OPTIONS' ); ?></div>
			<div class="panel-body">
					<!-- Enable Facebook Share -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SHARE_FACEBOOK', 'share_facebook' ); ?>

					<!-- Enable Twitter Share -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SHARE_TWITTER', 'share_twitter' ); ?>

					<!-- Enable Google Plus Share -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SHARE_GPLUS', 'share_googleplus' ); ?>

					<!-- Enable Linkedin Share -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SHARE_LINKEDIN', 'share_linkedin' ); ?>

					<!-- Enable Tumblr Share -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SHARE_TUMBLR', 'share_tumblr' ); ?>

					<!-- Enable Digg Share -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SHARE_DIGG', 'share_digg' ); ?>

					<!-- Enable Delicious Share -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SHARE_DELICIOUS', 'share_delicious' ); ?>

					<!-- Enable Reddit Share -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SHARE_REDDIT', 'share_reddit' ); ?>

					<!-- Enable Stumbleupon Share -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SHARE_STUMBLEUPON', 'share_stumbleupon' ); ?>
			</div>
		</fieldset>
	</div>
</div>

