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

$attachments	= $post->getAttachments();
?>
<?php if( $attachments ) { ?>
<div class="discuss-reply-attachments">
	<h5><?php echo JText::_( 'COM_EASYDISCUSS_ATTACHMENTS' ); ?>:</h5>
	<ul class="discuss-attachments reset-ul">
	<?php foreach( $attachments as $attachment ) { ?>
		<li class="attachment-item attachment attachment-type-<?php echo $attachment->attachmentType; ?>" id="attachment-<?php echo $attachment->id;?>" data-attachment-item>
			<?php echo $attachment->toHTML();?>
		</li>
	<?php } ?>
	</ul>
</div>
<?php } ?>
