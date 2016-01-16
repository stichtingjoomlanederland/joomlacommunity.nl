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
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_AKISMET' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
					<!-- Akismet -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_AKISMET_ENABLE', 'antispam_akismet' ); ?>

					<!-- Akismet key -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_AKISMET_API_KEY', 'antispam_akismet_key', 'input', array( 'size' => 60, 'class' => 'full-width' ) ); ?>

					<!-- Akismet filter trackback -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_AKISMET_FILTER_TRACKBACKS', 'antispam_akismet_trackback' ); ?>
				</tbody>
			</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_FLOOD_CONTROL' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Flood Control -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_FLOOD_CONTROL_ENABLE', 'antispam_flood_control' ); ?>

					<!-- Flood Interval -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_FLOOD_INTERVAL', 'antispam_flood_interval', 'input' ); ?>
				</tbody>
			</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_COMMENT_LENGTH_CHECK' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Enable Minimum Comment Length -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_COMMENT_MINIMUM_LENGTH_CHECK_ENABLE', 'antispam_min_length_enable' ); ?>

					<!-- Minimum Comment Length -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_MINIMUM_COMMENT_LENGTH', 'antispam_min_length', 'input' ); ?>

					<!-- Enable Maximum Comment Length -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_COMMENT_MAXIMUM_LENGTH_CHECK_ENABLE', 'antispam_max_length_enable' ); ?>

					<!-- Maximum Comment Length -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_MAXIMUM_COMMENT_LENGTH', 'antispam_max_length', 'input' ); ?>
				</tbody>
			</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_WORD_CENSORING' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Enable Word Censoring -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_WORD_CENSORING_ENABLE', 'filter_word' ); ?>

					<!-- Word censoring warning -->
					<?php echo $this->renderText( JText::_( 'COM_KOMENTO_SETTINGS_WORDS_TO_CENSOR_ADVANCE' ) ); ?>

					<!-- Words to censor -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_WORDS_TO_CENSOR', 'filter_word_text', 'textarea' ); ?>

				</tbody>
			</table>
			</fieldset>
		</td>
		<td valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_CAPTCHA' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Enable Captcha -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_CAPTCHA_ENABLE', 'antispam_captcha_enable' ); ?>

					<!-- Choose captcha type -->
					<?php $options	= array();
						$options[] = array( '0', 'COM_KOMENTO_SETTINGS_CAPTCHA_BUILT_IN' );
						$options[] = array( '1', 'COM_KOMENTO_SETTINGS_CAPTCHA_RECAPTCHA' );
						echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_CAPTCHA_TYPE', 'antispam_captcha_type', 'dropdown', $options );
					?>

					<!-- Captcha for user group -->
					<?php
						$usergroups = $this->getUsergroupsMultilist();
						echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_CAPTCHA_USERGROUPS', 'show_captcha', 'multilist', $usergroups );
					?>

				</tbody>
			</table>
			</fieldset>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_RECAPTCHA' ); ?></legend>
			<p class="small"><?php echo JText::sprintf( 'COM_KOMENTO_SETTINGS_RECAPTCHA_REGISTER', '<a href="http://www.google.com/recaptcha" target="_blank">http://www.google.com/recaptcha</a>' ); ?></p>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Recaptcha use SSL -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_RECAPTCHA_USE_SSL', 'antispam_recaptcha_ssl' ); ?>

					<!-- Recaptcha Public Key -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_RECAPTCHA_PUBLIC_KEY', 'antispam_recaptcha_public_key', 'input', array( 'size' => 60, 'class' => 'full-width' ) ); ?>

					<!-- Recaptcha Private Key -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_RECAPTCHA_PRIVATE_KEY', 'antispam_recaptcha_private_key', 'input', array( 'size' => 60, 'class' => 'full-width' ) ); ?>

					<!-- Recaptcha Theme -->
					<?php $options = array();
						$options[] = array( 'clean', 'COM_KOMENTO_SETTINGS_RECAPTCHA_THEME_CLEAN' );
						$options[] = array( 'white', 'COM_KOMENTO_SETTINGS_RECAPTCHA_THEME_WHITE' );
						$options[] = array( 'red', 'COM_KOMENTO_SETTINGS_RECAPTCHA_THEME_RED' );
						$options[] = array( 'blackglass', 'COM_KOMENTO_SETTINGS_RECAPTCHA_THEME_BLACKGLASS' );
						echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_RECAPTCHA_THEME', 'antispam_recaptcha_theme', 'dropdown', $options );
					?>

					<!-- Recaptcha Language -->
					<?php $options = array();
						$options[] = array( 'en', 'COM_KOMENTO_SETTINGS_RECAPTCHA_LANGUAGE_ENGLISH' );
						$options[] = array( 'ru', 'COM_KOMENTO_SETTINGS_RECAPTCHA_LANGUAGE_RUSSIAN' );
						$options[] = array( 'fr', 'COM_KOMENTO_SETTINGS_RECAPTCHA_LANGUAGE_FRENCH' );
						$options[] = array( 'de', 'COM_KOMENTO_SETTINGS_RECAPTCHA_LANGUAGE_GERMAN' );
						$options[] = array( 'nl', 'COM_KOMENTO_SETTINGS_RECAPTCHA_LANGUAGE_DUTCH' );
						$options[] = array( 'pt', 'COM_KOMENTO_SETTINGS_RECAPTCHA_LANGUAGE_PORTUGUESE' );
						$options[] = array( 'tr', 'COM_KOMENTO_SETTINGS_RECAPTCHA_LANGUAGE_TURKISH' );
						$options[] = array( 'es', 'COM_KOMENTO_SETTINGS_RECAPTCHA_LANGUAGE_SPANISH' );
						echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_RECAPTCHA_LANGUAGE', 'antispam_recaptcha_lang', 'dropdown', $options );
					?>

				</tbody>
			</table>
			</fieldset>
		</td>
	</tr>
</table>
