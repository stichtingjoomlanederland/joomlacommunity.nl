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
$currentTagId = JRequest::getInt('id');
?>
<header>
	<h2><?php echo JText::_('COM_EASYDISCUSS_EDIT_PROFILE'); ?></h2>
</header>

<article id="dc_profile">
	<form id="dashboard" name="dashboard" enctype="multipart/form-data" method="post" action="">
		<ul class="nav nav-tabs editProfileTabs">
			<?php if( $system->config->get( 'layout_avatar' ) && $system->config->get( 'layout_avatarIntegration') == 'default' || $system->config->get( 'layout_avatarIntegration') == 'gravatar'  ){ ?>
			<li id="photo" class="active">
				<a data-foundry-toggle="tab" href="#edit-photo">
					<b><?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_PICTURE' );?></b>
				</a>
			</li>
			<?php } ?>
			<?php if( $system->config->get( 'layout_profile_showbiography') ) { ?>
			<li id="bio">
				<a data-foundry-toggle="tab" href="#edit-bio">
					<b><?php echo JText::_( 'COM_EASYDISCUSS_BIOGRAPHY' );?></b>
				</a>
			</li>
			<?php } ?>

			<?php if( $system->config->get( 'layout_profile_showsocial') ) { ?>
			<li id="social">
				<a data-foundry-toggle="tab" href="#edit-social">
					<b><?php echo JText::_( 'COM_EASYDISCUSS_SOCIAL_PROFILES' );?></b>
				</a>
			</li>
			<?php } ?>

			<?php if( $system->config->get( 'layout_profile_showaccount') ) { ?>
			<li id="post">
				<a data-foundry-toggle="tab" href="#edit-post">
					<b><?php echo JText::_( 'COM_EASYDISCUSS_ACCOUNT' ); ?></b>
				</a>
			</li>
			<?php } ?>
			<?php if( $system->config->get( 'layout_profile_showlocation') ) { ?>
			<li id="location">
				<a data-foundry-toggle="tab" href="#edit-location">
					<b><?php echo JText::_( 'COM_EASYDISCUSS_LOCATION' ); ?></b>
				</a>
			</li>
			<?php } ?>
			<?php if( $system->config->get( 'layout_profile_showurl') ) { ?>
			<li id="alias">
				<a data-foundry-toggle="tab" href="#edit-alias">
					<b><?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_URL' );?></b>
				</a>
			</li>
			<?php } ?>
			<?php if( $system->config->get( 'layout_profile_showsite') ) { ?>
			<li id="site">
				<a data-foundry-toggle="tab" href="#edit-site">
					<b><?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_SITE_DETAILS' );?></b>
				</a>
			</li>
			<?php } ?>
		</ul>

		<div class="tab-content editProfileTabsContent">

			<?php if( $system->config->get( 'layout_avatar' ) && $system->config->get( 'layout_avatarIntegration') == 'default' || $system->config->get( 'layout_avatarIntegration') == 'gravatar'  ){ ?>
			<div class="tab-pane active" id="edit-photo">
				<?php echo $this->loadTemplate( 'user.edit.photo.php' ); ?>
			</div>
			<?php } ?>

			<div class="tab-pane" id="edit-bio">
				<?php echo $this->loadTemplate( 'user.edit.bio.php' ); ?>
			</div>
			
			<div class="tab-pane" id="edit-social">
				<?php echo $this->loadTemplate( 'user.edit.social.php' ); ?>
			</div>

			<div class="tab-pane" id="edit-post">
				<?php echo $this->loadTemplate( 'user.edit.account.php' ); ?>
			</div>
			<div class="tab-pane" id="edit-location">
				<?php echo $this->loadTemplate( 'user.edit.location.php' ); ?>
			</div>
			<div class="tab-pane" id="edit-alias">
				<?php echo $this->loadTemplate( 'user.edit.alias.php' ); ?>
			</div>
			<div class="tab-pane" id="edit-site">
				<?php echo $this->loadTemplate( 'user.edit.site.php' ); ?>
			</div>
		</div>

		<div class="form-actions">
			<input type="submit" class="butt butt-primary float-r" name="save" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_SAVE'); ?>" />
		</div>

		<input type="hidden" name="controller" value="profile" />
		<input type="hidden" name="task" value="saveProfile" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</article><!--end:#dc_profile-->
