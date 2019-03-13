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
<div class="ed-board-stats t-lg-mt--xl">
	<div class="ed-board-stats__hd">
		<ol class="g-list-inline">
			<li>
				<span class="ed-board-stats__meta"><?php echo JText::_('COM_EASYDISCUSS_STATS_POSTS');?>:</span>
				<b class="ed-board-stats__result"><?php echo $totalPosts; ?></b>
			</li>

			<?php if ($this->config->get('main_qna')) { ?>
			<li>
				<span class="ed-board-stats__meta"><?php echo JText::_('COM_EASYDISCUSS_STATS_RESOLVED_POSTS');?>:</span>
				<b class="ed-board-stats__result"><?php echo $resolvedPosts;?></b>
			</li>
			<li>
				<span class="ed-board-stats__meta"><?php echo JText::_('COM_EASYDISCUSS_STATS_UNRESOLVED_POSTS');?>:</span>
				<b class="ed-board-stats__result"><?php echo $unresolvedPosts;?></b>
			</li>
			<?php } ?>

			<li>
				<span class="ed-board-stats__meta"><?php echo JText::_('COM_EASYDISCUSS_LATEST_MEMBER');?>:</span>
				<a href="<?php echo $latestMember->getLink();?>" class="ed-board-stats__result"><?php echo $latestMember->getName(); ?></a>
			</li>
		</ol>
	</div>
	<div class="ed-board-stats__bd">
		<div class="ed-board-stats__bd-title"><?php echo JText::_('COM_EASYDISCUSS_STATS_ONLINE_USERS');?></div>
		<div class="o-avatar-list">
			<?php if ($onlineUsers) { ?>
				<?php foreach ($onlineUsers as $user) { ?>
				 <?php echo $this->html('user.avatar', $user); ?>
				<?php } ?>
			<?php } ?>
		</div>
	</div>
</div>
