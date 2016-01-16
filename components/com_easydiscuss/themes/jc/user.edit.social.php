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
<div class="tab-item user-social">
	<div class="form-group">
		<label><?php echo JText::_('COM_EASYDISCUSS_FACEBOOK'); ?></label>
		<input
				type="text"
				value="<?php echo $this->escape( $userparams->get( 'facebook' ) );?>"
				name="facebook"
				class="form-control"
				rel="ed-tooltip"
				data-placement="top"
				data-original-title="<?php echo JText::_('COM_EASYDISCUSS_FACEBOOK_DESC'); ?>"
			>
		<div class="checkbox">
			<label>
				<input type="checkbox" value="1" id="show_facebook" name="show_facebook" <?php echo $userparams->get( 'show_facebook' ) ? ' checked="1"' : ''; ?>>
				<?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
			</label>
		</div>
	</div>
	<div class="form-group">
		<label><?php echo JText::_('COM_EASYDISCUSS_TWITTER'); ?></label>
		<input
			type="text"
			value="<?php echo $this->escape( $userparams->get( 'twitter' ) ); ?>" name="twitter"
			class="form-control"
			rel="ed-tooltip"
			data-placement="top"
			data-original-title="<?php echo JText::_('COM_EASYDISCUSS_TWITTER_DESC'); ?>"
			>
		<div class="checkbox">
			<label>
				<input type="checkbox" value="1" id="show_twitter" name="show_twitter" <?php echo $userparams->get( 'show_twitter' ) ? ' checked="1"' : ''; ?>>
				<?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
			</label>
		</div>
	</div>
	<div class="form-group">
		<label><?php echo JText::_('COM_EASYDISCUSS_LINKEDIN'); ?></label>
		<input
			type="text"
			value="<?php echo $this->escape( $userparams->get( 'linkedin' ) ); ?>"
			name="linkedin"
			class="form-control"
			rel="ed-tooltip"
			data-placement="top"
			data-original-title="<?php echo JText::_('COM_EASYDISCUSS_LINKEDIN_DESC'); ?>"
			>
		<div class="checkbox">
			<label >
				<input type="checkbox" value="1" id="show_linkedin" name="show_linkedin" <?php echo $userparams->get( 'show_linkedin' ) ? ' checked="1"' : ''; ?>>
				<?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
			</label>
		</div>
	</div>
	<div class="form-group">
		<label><?php echo JText::_('COM_EASYDISCUSS_SKYPE_USERNAME'); ?></label>
		<input
			type="text"
			value="<?php echo $this->escape( $userparams->get( 'skype' ) ); ?>"
			name="skype"
			class="form-control"
			rel="ed-tooltip"
			data-placement="top"
			data-original-title="<?php echo JText::_('COM_EASYDISCUSS_SKYPE_DESC'); ?>"
			>
		<div class="checkbox">
			<label >
				<input type="checkbox" value="1" id="show_skype" name="show_skype" <?php echo $userparams->get( 'show_skype' ) ? ' checked="1"' : ''; ?>>
				<?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
			</label>
		</div>
	</div>
	<div class="form-group">
		<label><?php echo JText::_('COM_EASYDISCUSS_WEBSITE'); ?></label>
		<input
			type="text"
			value="<?php echo $this->escape( $userparams->get( 'website' ) ); ?>"
			name="website" class="form-control"
			rel="ed-tooltip"
			data-placement="top"
			data-original-title="<?php echo JText::_('COM_EASYDISCUSS_WEBSITE_DESC'); ?>"
			>
		<div class="checkbox">
			<label >
				<input type="checkbox" value="1" id="show_website" name="show_website" <?php echo $userparams->get( 'show_website' ) ? ' checked="1"' : ''; ?>>
				<?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
			</label>
		</div>
	</div>
</div>
