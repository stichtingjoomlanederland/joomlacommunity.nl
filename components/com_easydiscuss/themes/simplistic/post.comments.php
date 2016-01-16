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
?>
<?php if( $reply->access->canComment() ) { ?>
<span id="comments-button-<?php echo $reply->id;?>"  class="discuss-post-comment" style="display:<?php echo $question->islock ? 'none' : '';?>">
	<a href="javascript:void(0);" class="btn btn-small butt butt-default butt-s addComment"><?php echo JText::_('COM_EASYDISCUSS_COMMENT');?></a>
</span>
<?php } ?>