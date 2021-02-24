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
<div class="o-card o-card--ed-edit-profile-item">
	<div class="o-card__body l-stack l-spaces--sm">
		<div class="o-title-01"><?php echo JText::_('COM_ED_GDPR_DOWNLOAD_INFORMATION'); ?></div>
		<div class="o-body"><?php echo JText::_('COM_ED_GDPR_DOWNLOAD_INFORMATION_SUBTITLE'); ?></div>
	</div>
	<div class="o-card__body t-border-top--1 l-stack">

		<?php if (!$download->id) { ?>
		<div class="o-body">
			<?php echo JText::_('COM_ED_GDPR_DOWNLOAD_INFORMATION_DESC');?>
		</div>

		<div class="">
			<a href="javascript:void(0);" class="o-btn o-btn--default-o" data-ed-gdpr-request><?php echo JText::_('COM_ED_GDPR_REQUEST_DATA_BUTTON');?></a>
		</div>
		<?php } ?>

		<?php if ($download->id && ($download->isProcessing() || $download->isNew())) { ?>
		<p><?php echo JText::_('COM_ED_GDPR_DOWNLOAD_INFORMATION_PROCESSING');?></p>
		<?php } ?>

		<?php if ($download->id && $download->isReady()) { ?>
		<p><?php echo JText::sprintf('COM_ED_GDPR_REQUEST_IS_READY_DESC', $download->getExpireDays());?></p>

		<div class="o-form-group">
			<div class="gdpr-download-link t-text--center t-lg-mt--xl t-lg-mb--xl">
				<a href="<?php echo $download->getDownloadLink();?>" target="_blank" class="o-btn o-btn--primary">
					<?php echo JText::_('COM_ED_GDPR_DOWNLOAD_MY_DATA');?>
				</a>
			</div>
		</div>
		<?php } ?>
	</div>
</div>