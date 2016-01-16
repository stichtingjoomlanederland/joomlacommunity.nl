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
	<div class="control-group mb-20 form-inline">
		<div class="input-label facebook pb-10"><i class="icon-ed-fb"></i> <?php echo JText::_('COM_EASYDISCUSS_FACEBOOK'); ?></div>
		<div class="input-wrap">
			<input
				type="text"
				value="<?php echo $this->escape( $userparams->get( 'facebook' ) );?>"
				name="facebook"
				class="input width-350"
				rel="ed-tooltip"
				data-placement="top"
				data-original-title="<?php echo JText::_('COM_EASYDISCUSS_FACEBOOK_DESC'); ?>"
			>
			<label for="show_facebook" class="checkbox">
				<input type="checkbox" value="1" id="show_facebook" name="show_facebook" <?php echo $userparams->get( 'show_facebook' ) ? ' checked="1"' : ''; ?>>
				<?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
			</label>
		</div>
	</div>
	<div class="control-group mb-20 form-inline">
		<div class="input-label twitter pb-10"><i class="icon-ed-twitter"></i> <?php echo JText::_('COM_EASYDISCUSS_TWITTER'); ?></div>
		<div class="input-wrap">
			<input
				type="text"
				value="<?php echo $this->escape( $userparams->get( 'twitter' ) ); ?>" name="twitter"
				class="input width-350"
				rel="ed-tooltip"
				data-placement="top"
				data-original-title="<?php echo JText::_('COM_EASYDISCUSS_TWITTER_DESC'); ?>"
				>
			<label for="show_twitter" class="checkbox">
				<input type="checkbox" value="1" id="show_twitter" name="show_twitter" <?php echo $userparams->get( 'show_twitter' ) ? ' checked="1"' : ''; ?>>
				<?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
			</label>
		</div>
	</div>
	<div class="control-group mb-20 form-inline">
		<div class="input-label linkedin pb-10"><i class="icon-ed-linkedin"></i> <?php echo JText::_('COM_EASYDISCUSS_LINKEDIN'); ?></div>
		<div class="input-wrap">
			<input
				type="text"
				value="<?php echo $this->escape( $userparams->get( 'linkedin' ) ); ?>"
				name="linkedin"
				class="input width-350"
				rel="ed-tooltip"
				data-placement="top"
				data-original-title="<?php echo JText::_('COM_EASYDISCUSS_LINKEDIN_DESC'); ?>"
				>
			<label for="show_linkedin" class="checkbox">
				<input type="checkbox" value="1" id="show_linkedin" name="show_linkedin" <?php echo $userparams->get( 'show_linkedin' ) ? ' checked="1"' : ''; ?>>
				<?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
			</label>
		</div>

	</div>
	<div class="control-group mb-20 form-inline">
		<div class="input-label skype pb-10"><i class="icon-ed-skype"></i> <?php echo JText::_('COM_EASYDISCUSS_SKYPE_USERNAME'); ?></div>
		<div class="input-wrap">
			<input
				type="text"
				value="<?php echo $this->escape( $userparams->get( 'skype' ) ); ?>"
				name="skype"
				class="input width-350"
				rel="ed-tooltip"
				data-placement="top"
				data-original-title="<?php echo JText::_('COM_EASYDISCUSS_SKYPE_DESC'); ?>"
				>
			<label for="show_skype" class="checkbox">
				<input type="checkbox" value="1" id="show_skype" name="show_skype" <?php echo $userparams->get( 'show_skype' ) ? ' checked="1"' : ''; ?>>
				<?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
			</label>
		</div>

	</div>
	<div class="control-group mb-20 form-inline">
		<div class="input-label website pb-10"><i class="icon-ed-website"></i> <?php echo JText::_('COM_EASYDISCUSS_WEBSITE'); ?></div>
		<div class="input-wrap">
			<input
				type="text"
				value="<?php echo $this->escape( $userparams->get( 'website' ) ); ?>"
				name="website" class="input width-350"
				rel="ed-tooltip"
				data-placement="top"
				data-original-title="<?php echo JText::_('COM_EASYDISCUSS_WEBSITE_DESC'); ?>"
				>
			<label for="show_website" class="checkbox">
				<input type="checkbox" value="1" id="show_website" name="show_website" <?php echo $userparams->get( 'show_website' ) ? ' checked="1"' : ''; ?>>
				<?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
			</label>
		</div>

	</div>
</div>
