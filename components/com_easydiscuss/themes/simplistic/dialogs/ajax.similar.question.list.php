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
<?php if( $posts ){ ?>
<div class="popover bottom">
	<div class="arrow"></div>
	<div class="popover-title">
		<?php echo JText::_('COM_EASYDISCUSS_SIMIMAR_QUESTION_IS_YOUR_QUESTION_SIMILAR_BELOW'); ?>
		<a href="javascript:void(0);" id="similar-question-close" class="btn-close pull-right"><i class="icon-remove"></i> </a>
	</div>
	<div class="popover-content">
		<ol class="discuss-similar-list">
			<?php foreach( $posts as $post ){ ?>
			<li>
				<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>" target="_blank"><?php echo $post->title; ?></a>
			</li>
			<?php } ?>
		</ol>
	</div>
</div>
<?php } ?>
