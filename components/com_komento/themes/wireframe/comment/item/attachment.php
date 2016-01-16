<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

$attachments = $row->getAttachments();

if( !empty( $attachments ) ) { ?>
<div class="kmt-attachments mt-5 mb-5 attachmentWrap kmt-view-list">
	<p><strong class="kmt-attachments-title"><?php echo JText::_( 'COM_KOMENTO_COMMENT_ATTACHMENTS' ); ?> :</strong></p>

	<?php if( $system->my->allow( 'download_attachment' ) ) { ?>

		<ul class="kmt-attachments-list reset-list attachmentList">

			<?php foreach( $attachments as $attachment ) { ?>
				<li class="kmt-attachment-item attachmentFile file-<?php echo $attachment->id; ?>" attachmentid="<?php echo $attachment->id; ?>" attachmentname="<?php echo $attachment->filename; ?>">

					<?php if( $system->acl->allow( 'delete_attachment', $row ) ) { ?>
						<a href="javascript:void(0);" class="kmt-attachment-del col-cell attachmentDelete">
							<i class="fa fa-times"></i>
						</a>
					<?php } ?>

					<a href="<?php echo $attachment->getLink(); ?>" class="kmt-attachment-detail col-cell attachmentDetail <?php echo $attachment->getIconType() == 'image' && $system->config->get( 'upload_image_fancybox' ) ? 'attachmentImageLink' : ''; ?>">
						<i class="icon-mime type-<?php echo $attachment->getIconType(); ?>"></i>
						<?php echo $attachment->filename; ?>
					</a>
				</li>
			<?php } ?>

		</ul>

	<?php } else { ?>

		<div><?php echo JText::_( 'COM_KOMENTO_COMMENT_ATTACHMENTS_NO_PERMISSION_TO_VIEW' ); ?></div>

	<?php } ?>

	</div>
<?php } ?>
