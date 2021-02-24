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
<div class="ed-tag">
	<a href="<?php echo EDR::getTagRoute($tag->id); ?>" class="ed-tag__name"><?php echo $this->html('string.escape', JText::_($tag->title)); ?></a>
	<?php if ($this->config->get('main_rss')) { ?>
	<a href="<?php echo ED::feeds()->getFeedURL('index.php?option=com_easydiscuss&view=tags&id=' . $tag->id);?>" 
		class="ed-tag__subscribe-link" 
		data-ed-provide="tooltip" 
		data-placement="bottom" 
		title="<?php echo JText::_('COM_ED_SUBSCRIBE_VIA_RSS_READER');?>"
	>
		<span><?php echo ED::formatNumbers($tag->post_count); ?></span>
		<i class="fas fa-rss t-ml--sm"></i>
	</a>
	<?php } else { ?>
	<a href="<?php echo EDR::getTagRoute($tag->id); ?>" class="ed-tag__subscribe-link">
		<span><?php echo ED::formatNumbers($tag->post_count); ?></span>
	</a>
	<?php } ?>
</div>