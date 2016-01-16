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
	<div class="form-group">
		<label><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SITE_URL'); ?></label>
		<input type="text" value="<?php echo $this->escape( $siteDetails->get( 'siteUrl' ) ); ?>" name="siteUrl" class="form-control">
	</div>

	<div class="form-group">
		<label><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SITE_USERNAME'); ?></label>
		<input type="text" value="<?php echo $this->escape( $siteDetails->get( 'siteUsername' ) ); ?>" name="siteUsername" class="form-control">
	</div>

	<div class="form-group">
		<label><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SITE_PASSWORD'); ?></label>
		<input type="text" value="<?php echo $this->escape( $siteDetails->get( 'sitePassword' ) ); ?>" name="sitePassword" class="form-control">
	</div>

	<hr>

	<div class="form-group">
		<label><?php echo JText::_('COM_EASYDISCUSS_PROFILE_FTP_URL'); ?></label>
		<input type="text" value="<?php echo $this->escape( $siteDetails->get( 'ftpUrl' ) ); ?>" name="ftpUrl" class="form-control">
	</div>

	<div class="form-group">
		<label><?php echo JText::_('COM_EASYDISCUSS_PROFILE_FTP_USERNAME'); ?></label>
		<input type="text" value="<?php echo $this->escape( $siteDetails->get( 'ftpUsername' ) ); ?>" name="ftpUsername" class="form-control">
	</div>

	<div class="form-group">
		<label><?php echo JText::_('COM_EASYDISCUSS_PROFILE_FTP_PASSWORD'); ?></label>
		<input type="text" value="<?php echo $this->escape( $siteDetails->get( 'ftpPassword' ) ); ?>" name="ftpPassword" class="form-control">
	</div>

	<div class="form-group">
		<label><?php echo JText::_('COM_EASYDISCUSS_PROFILE_FTP_PASSWORD'); ?></label>
		<input type="text" value="<?php echo $this->escape( $siteDetails->get( 'ftpPassword' ) ); ?>" name="ftpPassword" class="form-control">
	</div>

	<hr>
	
	<div class="form-group">
		<label><?php echo JText::_('COM_EASYDISCUSS_PROFILE_OPTIONAL'); ?></label>
		<textarea name="optional" id="optional" class="form-control" rows="5" placeholder="<?php echo $this->escape( $siteDetails->get( 'optional' ) ); ?>"></textarea>
	</div>
</div>
