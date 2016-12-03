<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="tab-item user-site">
	<div class="ed-form-panel__hd">
	    <div class="ed-form-panel__title"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SITE_DETAILS'); ?></div>
	    <div class="ed-form-panel__"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SITE_DETAILS_DESC'); ?></div>
	</div>

	<div class="ed-form-panel__bd">

		<div class="form-group">
		    <label for="siteUrl"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SITE_URL'); ?></label>
		    <input type="text" class="form-control" id="siteUrl" name="siteUrl" value="<?php echo $siteDetails->get('siteUrl'); ?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_SITE_URL_DESC'); ?>">
		</div>

		<div class="o-row">
			<div class="o-col t-lg-pr--md t-xs-pr--no">
				<div class="form-group">
				    <label for="siteUsername"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SITE_USERNAME'); ?></label>
				    <input type="text" class="form-control" id="siteUsername" name="siteUsername" value="<?php echo $siteDetails->get('siteUsername'); ?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_SITE_USERNAME_DESC'); ?>">
				</div>
			</div>

			<div class="o-col t-lg-pr--md t-xs-pr--no">
				<div class="form-group">
				    <label for="sitePassword"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SITE_PASSWORD'); ?></label>
				    <input type="text" class="form-control" id="sitePassword" name="sitePassword" value="<?php echo $siteDetails->get('sitePassword'); ?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_SITE_PASSWORD_DESC'); ?>">
				</div>
			</div>
		</div>

		<div class="form-group">
		    <label for="ftpUrl"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_FTP_URL'); ?></label>
		    <input type="text" class="form-control" id="ftpUrl" name="ftpUrl" value="<?php echo $siteDetails->get('ftpUrl'); ?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_FTP_URL_DESC'); ?>">
		</div>	

		<div class="o-row">
			<div class="o-col t-lg-pr--md t-xs-pr--no">
				<div class="form-group">
				    <label for="ftpUsername"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_FTP_USERNAME'); ?></label>
				    <input type="text" class="form-control" id="ftpUsername" name="ftpUsername" value="<?php echo $siteDetails->get('ftpUsername'); ?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_FTP_USERNAME_DESC'); ?>">
				</div>
			</div>

			<div class="o-col t-lg-pr--md t-xs-pr--no">
				<div class="form-group">
				    <label for="ftpPassword"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_FTP_PASSWORD'); ?></label>
				    <input type="text" class="form-control" id="ftpPassword" name="ftpPassword" value="<?php echo $siteDetails->get('ftpPassword'); ?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_FTP_PASSWORD_DESC'); ?>">
				</div>
			</div>
		</div>

		<div class="form-group">
		    <label for="optional"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_OPTIONAL'); ?></label>
		    <textarea name="optional" id="optional" class="form-control" cols="30" rows="5" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_OPTIONAL_DESC'); ?>"><?php echo $siteDetails->get('optional'); ?></textarea>
		</div>		
	</div>
</div>
