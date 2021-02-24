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

$attachments = $post->getAttachments(false);

if (!$attachments) {
	return;
}
?>
<div class="o-card o-card--ed-post-widget" data-ed-post-attachments>
	<div class="o-card__body l-stack">
		<div class="o-title-01">
			<?php echo JText::sprintf('COM_EASYDISCUSS_POST_ATTACHMENTS', count($attachments)); ?>
		</div>
		<div class="ed-attachments l-cluster">
			<div>
			<?php foreach ($attachments as $attachment) { ?>
				<?php echo $attachment->html(); ?>
			<?php } ?>
			</div>
		</div>
	</div>
</div>