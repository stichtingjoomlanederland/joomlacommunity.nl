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
?>
<?php if ($post->getSiteDetails()) { ?>
<div class="ed-post-widget">
    <div class="ed-post-widget__hd">
		<?php echo JText::_('COM_EASYDISCUSS_TAB_SITE_DETAILS'); ?>
    </div>
    <div class="ed-post-widget__bd">
        <div class="ed-post-site-info">
            <a href="<?php echo $post->getSiteDetails()->siteUrl; ?>" target="_blank" class="ed-post-site-info__link"><?php echo $post->getSiteDetails()->siteUrl; ?></a>

            <div class="ed-post-site-info__title"><?php echo JText::_('COM_EASYDISCUSS_TAB_SITE_FORM_USERNAME'); ?></div>
            	<input class="ed-post-site-info__field" type="text" value="<?php echo $post->getSiteDetails()->siteUsername; ?>" readonly="">

            <div class="ed-post-site-info__title"><?php echo JText::_('COM_EASYDISCUSS_TAB_SITE_FORM_PASSWORD');?></div>
            	<input class="ed-post-site-info__field" type="text" value="<?php echo $post->getSiteDetails()->sitePassword; ?>" readonly="">

            <div class="ed-post-site-info__title"><?php echo JText::_('COM_EASYDISCUSS_TAB_SITE_FORM_FTP_URL'); ?></div>
            	<input class="ed-post-site-info__field" type="text" value="<?php echo $post->getSiteDetails()->ftpUrl; ?>" readonly="">

            <div class="ed-post-site-info__title"><?php echo JText::_('COM_EASYDISCUSS_TAB_SITE_FORM_FTP_USERNAME');?></div>
            	<input class="ed-post-site-info__field" type="text" value="<?php echo $post->getSiteDetails()->ftpUsername; ?>" readonly="">

            <div class="ed-post-site-info__title"><?php echo JText::_('COM_EASYDISCUSS_TAB_SITE_FORM_FTP_PASSWORD');?></div>
            	<input class="ed-post-site-info__field" type="text" value="<?php echo $post->getSiteDetails()->ftpPassword; ?>" readonly="">

            <div class="ed-post-site-info__title"><?php echo JText::_('COM_EASYDISCUSS_TAB_SITE_FORM_OPTIONAL');?></div>
            	<div class="ed-post-site-info__note"><?php echo $post->getSiteDetails()->siteInfo; ?></div>
        </div>
    </div>
</div>
<?php } ?>