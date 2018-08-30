<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<h2 class="ed-page-title"><?php echo JText::_('COM_EASYDISCUSS_EDIT_PROFILE'); ?></h2>
<div class="ed-profile">
<form id="dashboard" name="dashboard" enctype="multipart/form-data" method="post" action="<?php echo JRoute::_('index.php');?>">
	<div class="ed-profile-container">
		<div class="ed-profile-container__side">
			<div class="ed-profile-container__side-bd">
				<ul class="o-nav  o-nav--stacked ed-profile-container__side-nav" ed-data-profile-sidebar-nav>

					<?php if ($this->config->get('layout_profile_showaccount')) { ?>
					<li id="bio">
						<a data-ed-toggle="tab" href="#edit-bio">
							<b><?php echo JText::_('COM_EASYDISCUSS_ACCOUNT'); ?></b>
						</a>
					</li>
					<?php } ?>

					<?php if ($this->config->get('layout_avatar') && $this->config->get('layout_avatarIntegration') == 'default' || $this->config->get('layout_avatarIntegration') == 'gravatar' || $allowJFBCAvatarEdit) { ?>
					<li id="photo">
						<a data-ed-toggle="tab" href="#edit-photo">
							<b><?php echo JText::_('COM_EASYDISCUSS_PROFILE_PICTURE'); ?></b>
						</a>
					</li>
					<?php } ?>

					<?php if ($this->config->get('layout_profile_showsocial')) { ?>
					<li id="social">
						<a data-ed-toggle="tab" href="#edit-social">
							<b><?php echo JText::_('COM_EASYDISCUSS_SOCIAL_PROFILES'); ?></b>
						</a>
					</li>
					<?php } ?>

					<?php if ($this->config->get('layout_profile_showlocation')) { ?>
					<li id="location">
						<a data-ed-toggle="tab" href="#edit-location" data-ed-location-tab>
							<b><?php echo JText::_('COM_EASYDISCUSS_LOCATION'); ?></b>
						</a>
					</li>
					<?php } ?>
					<?php if ($this->config->get('layout_profile_showurl')) { ?>
					<li id="alias">
						<a data-ed-toggle="tab" href="#edit-alias">
							<b><?php echo JText::_('COM_EASYDISCUSS_PROFILE_URL'); ?></b>
						</a>
					</li>
					<?php } ?>
					<?php if ($this->config->get('layout_profile_showsite')) { ?>
					<li id="site">
						<a data-ed-toggle="tab" href="#edit-site">
							<b><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SITE_DETAILS'); ?></b>
						</a>
					</li>
					<?php } ?>
					<li id="others">
						<a data-ed-toggle="tab" href="#edit-others">
							<b><?php echo JText::_('COM_EASYDISCUSS_PROFILE_OTHERS'); ?></b>
						</a>
					</li>

					<?php if ($this->config->get('main_userdownload')) { ?>
					<li>
						<a href="<?php echo EDR::_('view=profile&layout=download', false); ?>">
							<b><?php echo JText::_('COM_ED_PROFILE_DOWNLOAD'); ?></b>
						</a>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<div class="ed-profile-container__content">
			<div class="ed-profile-container__content-bd t-pl--xl">
				<div class="ed-form-panel">
					<div class="tab-content editProfileTabsContent">
						<?php if ($this->config->get('layout_profile_showaccount')) { ?>
							<div class="tab-pane active" id="edit-bio">
								<?php echo $this->output('site/user/account'); ?>
							</div>
						<?php } ?>
						<?php if ($this->config->get('layout_avatar') && $this->config->get('layout_avatarIntegration') == 'default' || $this->config->get('layout_avatarIntegration') == 'gravatar' || $allowJFBCAvatarEdit) { ?>
							<div class="tab-pane" id="edit-photo">
								<?php echo $this->output('site/user/photo'); ?>
							</div>
						<?php } ?>
						<?php if ($this->config->get('layout_profile_showsocial')) { ?>
							<div class="tab-pane" id="edit-social">
								<?php echo $this->output('site/user/social'); ?>
							</div>
						<?php } ?>
						<?php if ($this->config->get('layout_profile_showlocation')) { ?>
							<div class="tab-pane" id="edit-location">
								<?php echo $this->output('site/user/location'); ?>
							</div>
						<?php } ?>
						<?php if ($this->config->get('layout_profile_showurl')) { ?>
							<div class="tab-pane" id="edit-alias">
								<?php echo $this->output('site/user/alias'); ?>
							</div>
						<?php } ?>
						<?php if ($this->config->get('layout_profile_showsite')) { ?>
							<div class="tab-pane" id="edit-site">
								<?php echo $this->output('site/user/site'); ?>
							</div>
						<?php } ?>
						<div class="tab-pane" id="edit-others">
							<?php echo $this->output('site/user/others'); ?>
						</div>
					</div>
					<div class="ed-form-panel__ft">
						<input type="submit" class="btn btn-primary pull-right" name="save" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_SAVE'); ?>" />
					</div>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" name="controller" value="profile" />
	<input type="hidden" name="task" value="saveProfile" />
	<?php echo JHTML::_('form.token'); ?>
</form>
</div><!--end:#dc_profile-->
