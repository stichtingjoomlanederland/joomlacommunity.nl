<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-attachment-item" data-ed-attachment-item data-id="<?php echo $attachment->id;?>">
	<a class="ed-attachment-item__link" 
		title="<?php echo $this->html('string.escape', $attachment->title);?>"
		href="<?php echo $attachment->getDownloadLink(); ?>" 
		rel="post-<?php echo $attachment->uid;?>" 
		<?php if ($type == 'image') { ?>
		data-ed-attachment-preview
		<?php } else { ?>
		target="_blank"
		<?php } ?>
	>
		<?php if ($type == 'image') { ?>
			<img class="ed-attachment-item__img" src="<?php echo $attachment->getThumbnail($external);?>" alt="<?php echo $this->html('string.escape', $attachment->title);?>" />

		<?php } else { ?>
			<i class="fa fa-download ed-attachment-item__icon"></i>
		<?php } ?>

		<span class="ed-attachment-item__caption">
			<?php echo $attachment->title;?>
		</span>
	</a>

	

	<?php if ($attachment->canDelete()) { ?>
	<a data-ed-attachment-delete="" href="javascript:void(0);" class="ed-attachment-item__btn-del"><i></i></a>
	<?php } ?>
	
</div>