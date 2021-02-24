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
<div class="o-card o-card--ed-active-tag">
	<div class="o-card__body">
		<div class="t-text--center l-stack">
			<h2 class="o-title t-text--truncate t-my--no">
				<?php echo JText::sprintf('COM_ED_POSTS_TAGGED', $this->html('string.escape', $tag->getTitle())); ?>
			</h2>
		</div>
	</div>

	<div class="o-card__footer">
		<div class="g-list-flex g-list-flex--separator t-justify-content--c o-descriptions">
			<div class="g-list-flex__item">
				<a href="<?php echo EDR::_('view=tags');?>" class="t-text--600">
					<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? '→' : '←';?> <?php echo JText::_('COM_ED_BACK'); ?>
				</a>
			</div>

			<?php if ($this->config->get('main_rss')) { ?>
			<div class="g-list-flex__item t-text--600">•</div>

			<div class="g-list-flex__item">
				<a href="<?php echo $tag->getFeedLink();?>" class="t-text--600">
					<i class="fas fa-rss"></i> <?php echo JText::_('COM_ED_SUBSCRIBE_VIA_RSS_READER');?>
				</a> 
			</div>
			<?php } ?>
		</div>
	</div>
</div>