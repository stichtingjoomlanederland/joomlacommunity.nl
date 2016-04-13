<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

//load porfile info
$siteDetails = new JRegistry($this->profile->get('site'));

$siteUrl = $siteDetails->get('siteUrl');
$siteusername = $siteDetails->get('siteUsername');
$password = $siteDetails->get('sitePassword');
$ftpurl = $siteDetails->get('ftpUrl');
$ftpusername = $siteDetails->get('ftpUsername');
$ftppassword = $siteDetails->get('ftpPassword');
$siteinfo = $siteDetails->get('optional');

$showProfileDetails = true;

if (empty($siteUrl) && empty($siteusername) && empty($password) && empty($ftpurl) && empty($ftpusername) && empty($ftppassword)) {
	$showProfileDetails = false;
}

$access = trim($this->config->get('tab_site_access'));

// Nobody can view this if access is not set yet.
if (!$access) {
	return;
}

$access = explode(',', $access);
$gids = ED::getUserGids();

$url = $composer->getFieldData('siteurl', $post->params);

if (stristr($url[0], 'http://') === false && stristr($url[0], 'https://') === false) {
	$url[0]	= 'http://' . $url[0];
}

if (!$showProfileDetails) {
	$siteusernameTemp = $composer->getFieldData('siteusername', $post->params);
	$passwordTemp = $composer->getFieldData('sitepassword', $post->params);
	$ftpurlTemp	= $composer->getFieldData('ftpurl', $post->params);
	$ftpusernameTemp = $composer->getFieldData('ftpusername', $post->params);
	$ftppasswordTemp = $composer->getFieldData('ftppassword', $post->params);
	$siteinfoTemp = $composer->getFieldData('siteinfo', $post->params);

	$siteUrl = $this->escape($url[0]);
	$siteusername = $siteusernameTemp[0];
	$password = $passwordTemp[0];
	$ftpurl = $ftpurlTemp[0];
	$ftpusername = $ftpusernameTemp[0];
	$ftppassword = $ftppasswordTemp[0];
	$siteinfo = $siteinfoTemp[0];
}

if ($siteUrl == 'http://' && empty($siteusername) && empty($password) && empty($ftpurl) && empty($ftpusername) && empty($ftppassword)) {
	return false;
}
?>
<?php foreach ($gids as $gid) { ?>
	<?php if (in_array($gid, $access)) { ?>
		<div class="ed-post-widget">
		    <div class="ed-post-widget__hd">
				<?php echo JText::_('COM_EASYDISCUSS_TAB_SITE_DETAILS'); ?>
		    </div>
		    <div class="ed-post-widget__bd">
		        <div class="ed-post-site-info">
		            <a href="<?php echo $siteUrl; ?>" target="_blank" class="ed-post-site-info__link"><?php echo $siteUrl; ?></a>
		            
		            <div class="ed-post-site-info__title"><?php echo JText::_('COM_EASYDISCUSS_TAB_SITE_FORM_USERNAME'); ?></div>
		            	<input class="ed-post-site-info__field" type="text" value="<?php echo $this->escape($siteusername); ?>" readonly="">
		            
		            <div class="ed-post-site-info__title"><?php echo JText::_('COM_EASYDISCUSS_TAB_SITE_FORM_PASSWORD');?></div>
		            	<input class="ed-post-site-info__field" type="text" value="<?php echo $this->escape($password); ?>" readonly="">
		            
		            <div class="ed-post-site-info__title"><?php echo JText::_('COM_EASYDISCUSS_TAB_SITE_FORM_FTP_URL'); ?></div>
		            	<input class="ed-post-site-info__field" type="text" value="<?php echo $this->escape($ftpurl); ?>" readonly="">
		            
		            <div class="ed-post-site-info__title"><?php echo JText::_('COM_EASYDISCUSS_TAB_SITE_FORM_FTP_USERNAME');?></div>
		            	<input class="ed-post-site-info__field" type="text" value="<?php echo $this->escape($ftpusername); ?>" readonly="">
		            
		            <div class="ed-post-site-info__title"><?php echo JText::_('COM_EASYDISCUSS_TAB_SITE_FORM_FTP_PASSWORD');?></div>
		            	<input class="ed-post-site-info__field" type="text" value="<?php echo $this->escape($ftppassword); ?>" readonly="">
		            
		            <div class="ed-post-site-info__title"><?php echo JText::_('COM_EASYDISCUSS_TAB_SITE_FORM_OPTIONAL');?></div>
		            	<div class="ed-post-site-info__note"><?php echo str_ireplace('\n' , "<br />" , nl2br($siteinfo)); ?></div>
		        </div>
		    </div>
		</div>
		<?php
		// If there is match, just return here.
		return;
		?>
	<?php } ?>
<?php } ?>