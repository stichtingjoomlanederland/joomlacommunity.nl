<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
$url = false;
$siteusername = false;
$password = false;
$siteinfo = false;
$ftpurl = false;
$ftpusername = false;
$ftppassword = false;

if (isset($post) && is_object($post)) {
	$url = $composer->getFieldData('siteurl', $post->params);
	$url = $url && isset($url[0]) ? $url[0] : '';

	$siteusername = $composer->getFieldData('siteusername', $post->params);
	$siteusername = $siteusername && isset($siteusername[0]) ? $siteusername[0] : '';

	$password = $composer->getFieldData('sitepassword', $post->params);
	$password = $password && isset($password[0]) ? $password[0] : '';

	$siteinfo = $composer->getFieldData('siteinfo', $post->params);
	$siteinfo = $siteinfo && isset($siteinfo[0]) ? $siteinfo[0] : '';

	$ftpurl = $composer->getFieldData('ftpurl', $post->params);
	$ftpurl = $ftpurl && isset($ftpurl[0]) ? $ftpurl[0] : '';

	$ftpusername = $composer->getFieldData('ftpusername', $post->params);
	$ftpusername = $ftpusername && isset($ftpusername[0]) ? $ftpusername[0] : '';

	$ftppassword = $composer->getFieldData('ftppassword', $post->params);
	$ftppassword = $ftppassword && isset($ftppassword[0]) ? $ftppassword[0] : '';
}
?>
<div id="access-<?php echo $editorId; ?>" class="ed-editor-tab__content fields-tab tab-pane">

	<div class="ed-editor-tab__content-note t-lg-mb--xl">
		<?php echo JText::_('COM_EASYDISCUSS_SITE_INFO'); ?>
	</div>

	<div class="ed-form-panel__bd"> 
		<div class="form-group">
			<label for="params_siteurl"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SITE_URL'); ?></label>
			<input type="text" class="form-control" id="siteurl" name="params_siteurl" value="<?php echo $url; ?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_SITE_URL_DESC'); ?>">
		</div>

		<div class="o-row">
			<div class="o-col t-lg-pr--md t-xs-pr--no">
				<div class="form-group">
					<label for="params_siteusername"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SITE_USERNAME'); ?></label>
					<input type="text" class="form-control" id="siteusername" name="params_siteusername" value="<?php echo $siteusername; ?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_SITE_USERNAME_DESC'); ?>">
				</div>
			</div>
			<div class="o-col t-lg-pr--md t-xs-pr--no">
				<div class="form-group">
					<label for="params_sitepassword"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SITE_PASSWORD'); ?></label>
					<input type="text" class="form-control" id="sitepassword" name="params_sitepassword" value="<?php echo $password; ?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_SITE_PASSWORD_DESC'); ?>">
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="params_ftpurl"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_FTP_URL'); ?></label>
			<input type="text" class="form-control" id="ftpurl" name="params_ftpurl" value="<?php echo $ftpurl; ?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_FTP_URL_DESC'); ?>">
		</div>

		<div class="o-row">
			<div class="o-col t-lg-pr--md t-xs-pr--no">
				<div class="form-group">
					<label for="params_ftpusername"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_FTP_USERNAME'); ?></label>
					<input type="text" class="form-control" id="ftpusername" name="params_ftpusername" value="<?php echo $ftpusername; ?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_FTP_USERNAME_DESC'); ?>">
				</div>
			</div>
			<div class="o-col t-lg-pr--md t-xs-pr--no">
				<div class="form-group">
					<label for="params_ftppassword"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_FTP_PASSWORD'); ?></label>
					<input type="text" class="form-control" id="ftppassword" name="params_ftppassword" value="<?php echo $ftppassword; ?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_FTP_PASSWORD_DESC'); ?>">
				</div>
			</div>
		</div>
		
		<div class="form-group">
			<label for="params_siteinfo"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_OPTIONAL'); ?></label>
			<textarea name="params_siteinfo" id="optional" class="form-control" cols="30" rows="10" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_OPTIONAL_DESC'); ?>"><?php echo $siteinfo ?></textarea>
		</div>      
	</div>
</div>