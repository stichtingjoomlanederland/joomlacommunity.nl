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
<div class="ed-attachments l-cluster">
	<div>
		<?php foreach ($post->getAttachments() as $attachment) { ?>
			<div class="ed-attachment-item" data-ed-attachment-item="<?php echo $attachment->id;?>">
				<a href="javascript:void(0);" class="o-label t-text--600">
					<?php echo $attachment->html(); ?>
				</a>
			</div>
		<?php } ?>
	</div>
</div>