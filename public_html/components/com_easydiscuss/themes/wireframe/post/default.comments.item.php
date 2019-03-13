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
<div class="ed-comment-item">
    <div class="o-flag">
        <div class="o-flag__image o-flag--top">
            <?php echo $this->html('user.avatar', $comment->creator, array('size' => 'sm')); ?>
        </div>

        <div class="o-flag__body">
            <div class="ed-comment-item__content">
				<?php echo $comment->comment; ?>
			</div>
            <div class="ed-comment-item__action">
                <ol class="g-list-inline g-list-inline--delimited">
                    <li data-breadcrumb="·"><a href="<?php echo $comment->creator->getLink();?>">S<?php echo $comment->creator->getName(); ?></a></li>
                    <li data-breadcrumb="·"><?php echo $comment->duration; ?></li>

					<?php if ($comment->canConvert()) { ?>
                    	<li data-breadcrumb="·"><a href="javascript:void(0);" data-comment-convert-link><?php echo JText::_('COM_EASYDISCUSS_CONVERT_THIS_COMMENT_TO_REPLY'); ?></a></li>
					<?php } ?>
					<?php if ($comment->canDelete()) { ?>
                    	<li data-breadcrumb="·"><a href="#"><?php echo JText::_('COM_EASYDISCUSS_DELETE_COMMENT')?></a></li>
					<?php } ?>
                </ol>
            </div>
        </div>
    </div>
</div>
