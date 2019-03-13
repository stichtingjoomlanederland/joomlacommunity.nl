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
<h2 class="ed-page-title"><?php echo JText::_('COM_ED_GDPR_DOWNLOAD_INFORMATION'); ?></h2>
<div class="ed-profile">
	<div class="ed-profile-container">
		<div class="ed-profile-container__content">
			<?php if (!$download->id) { ?>
			<p><?php echo JText::_('COM_ED_GDPR_DOWNLOAD_INFORMATION_DESC');?></p>

			<div class="o-form-group">
				<div class="gdpr-download-link t-text--center t-lg-mt--xl">
					<a href="javascript:void(0);" class="btn btn-primary" data-ed-gdpr-request>
						<?php echo JText::_('COM_ED_GDPR_REQUEST_DATA_BUTTON');?>
					</a>
				</div>
			</div>
			<?php } ?>

			<?php if ($download->id && ($download->isProcessing() || $download->isNew())) { ?>
			<p><?php echo JText::_('COM_ED_GDPR_DOWNLOAD_INFORMATION_PROCESSING');?></p>
			<?php } ?>

			<?php if ($download->id && $download->isReady()) { ?>
			<p><?php echo JText::sprintf('COM_ED_GDPR_REQUEST_IS_READY_DESC', $download->getExpireDays());?></p>

			<div class="o-form-group">
				<div class="gdpr-download-link t-text--center t-lg-mt--xl t-lg-mb--xl">
					<a href="<?php echo $download->getDownloadLink();?>" target="_blank" class="btn btn-primary">
						<?php echo JText::_('COM_ED_GDPR_DOWNLOAD_MY_DATA');?>
					</a>
				</div>
			</div>
			<?php } ?>

		</div>
	</div>
</div>
