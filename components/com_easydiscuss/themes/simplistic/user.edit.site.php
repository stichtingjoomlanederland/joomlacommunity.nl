<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<div class="tab-item user-site">
	<div class="control-group">
		<div class="input-label pb-10"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SITE_URL'); ?></div>
		<div class="input-wrap"><input type="text" value="<?php echo $this->escape( $siteDetails->get( 'siteUrl' ) ); ?>" name="siteUrl" class="input width-350"></div>
	</div>
	<div class="control-group">
		<div class="input-label pb-10"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SITE_USERNAME'); ?></div>
		<div class="input-wrap mrm"><input type="text" value="<?php echo $this->escape( $siteDetails->get( 'siteUsername' ) ); ?>" name="siteUsername" class="input width-250"></div>
	</div>
	<div class="control-group">
		<div class="input-label pb-10"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SITE_PASSWORD'); ?></div>
		<div class="input-wrap mrm"><input type="text" value="<?php echo $this->escape( $siteDetails->get( 'sitePassword' ) ); ?>" name="sitePassword" class="input width-250"></div>
	</div>
	<div class="control-group">
		<div class="input-label pb-10"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_FTP_URL'); ?></div>
		<div class="input-wrap mrm"><input type="text" value="<?php echo $this->escape( $siteDetails->get( 'ftpUrl' ) ); ?>" name="ftpUrl" class="input width-250"></div>
	</div>
	<div class="control-group">
		<div class="input-label pb-10"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_FTP_USERNAME'); ?></div>
		<div class="input-wrap mrm"><input type="text" value="<?php echo $this->escape( $siteDetails->get( 'ftpUsername' ) ); ?>" name="ftpUsername" class="input width-250"></div>
	</div>
	<div class="control-group">
		<div class="input-label pb-10"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_FTP_PASSWORD'); ?></div>
		<div class="input-wrap mrm"><input type="text" value="<?php echo $this->escape( $siteDetails->get( 'ftpPassword' ) ); ?>" name="ftpPassword" class="input width-250"></div>
	</div>
	<div class="control-group">
		<div class="input-label pb-10"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_FTP_PASSWORD'); ?></div>
		<div class="input-wrap mrm"><input type="text" value="<?php echo $this->escape( $siteDetails->get( 'ftpPassword' ) ); ?>" name="ftpPassword" class="input width-250"></div>
	</div>
	<div class="control-group">
		<div class="input-label pb-10"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_OPTIONAL'); ?></div>
		<div class="input-wrap">
			<textarea name="optional" id="optional" class="full-width"><?php echo $this->escape( $siteDetails->get( 'optional' ) ); ?></textarea>
		</div>
	</div>
</div>
