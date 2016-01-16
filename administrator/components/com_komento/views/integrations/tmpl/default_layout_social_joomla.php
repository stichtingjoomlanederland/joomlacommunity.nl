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
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_SHARE_OPTIONS' ); ?></legend>
			<table class="admintable social-sharing" cellspacing="1">
				<tbody>

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

					<!-- Enable Identica Share -->
					<?php // echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SHARE_IDENTICA', 'share_identica' ); ?>

					<!-- Enable Stumpedia Share -->
					<?php // echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SHARE_STUMPEDIA', 'share_stumpedia' ); ?>

					<!-- Enable Technorati Share -->
					<?php // echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SHARE_TECHNORATI', 'share_technorati' ); ?>

					<!-- Enable Blogmarks Share -->
					<?php // echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SHARE_BLOGMARKS', 'share_blogmarks' ); ?>

					<!-- Enable Pinterest Share -->
					<?php // echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SHARE_PINTEREST', 'share_pinterest' ); ?>

				</tbody>
			</table>
			</fieldset>
		</td>
		<td width="50%" valign="top">
		</td>
	</tr>
</table>
