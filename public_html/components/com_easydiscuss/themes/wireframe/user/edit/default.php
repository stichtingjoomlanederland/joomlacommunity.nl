<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-profile-edit">
	<div class="l-stack">
		<h2 class="o-title"><?php echo JText::_('COM_EASYDISCUSS_EDIT_PROFILE'); ?></h2>

		<form id="dashboard" name="dashboard" enctype="multipart/form-data" method="post" action="<?php echo JRoute::_('index.php');?>">
			<div class="ed-profile-container l-stack">
				<div class="o-tabs o-tabs--ed">
					<div class="o-tabs__item active" data-ed-profile-tab>
						<a class="o-tabs__link" data-ed-toggle="tab" href="#account">
							<b><?php echo JText::_('COM_EASYDISCUSS_ACCOUNT'); ?></b>
						</a>
					</div>

					<?php if ($this->config->get('main_userdownload')) { ?>
					<div class="o-tabs__item" data-ed-profile-tab>
						<a class="o-tabs__link" data-ed-toggle="tab" href="#download">
							<b><?php echo JText::_('COM_ED_PROFILE_DOWNLOAD'); ?></b>
						</a>
					</div>
					<?php } ?>

					<?php echo $tabs->heading; ?>
				</div>

				<div class="ed-profile-container__content">
					<div class="ed-profile-container__content-bd">
						<div class="ed-form-panel">
							<div class="tab-content">
								<div class="tab-pane active" id="account">
									<?php echo $this->output('site/user/edit/account'); ?>
								</div>

								<?php if ($this->config->get('main_userdownload')) { ?>
								<div class="tab-pane" id="download">
									<?php echo $this->output('site/user/edit/download'); ?>
								</div>
								<?php } ?>

								<?php echo $tabs->contents; ?>
							</div>
						</div>
					</div>
				</div>

				<div class="t-d--flex t-mt--md">
					<div class="t-ml--auto">
						<button type="submit" class="o-btn o-btn--primary"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_SAVE'); ?></button>
					</div>
				</div>
				<input type="hidden" name="controller" value="profile" />
				<input type="hidden" name="task" value="saveProfile" />
				<?php echo JHTML::_('form.token'); ?>
			</div>
		</form>
	</div>
</div>