<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<h2 class="ed-page-title"><?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_MANAGE_POSTS'); ?></h2>

<div class="ed-dashboard t-lg-mb--lg">
	<div class="ed-dashboard__hd">
		<div class="o-col-sm">
			<i class="fa fa-clock-o"></i> <?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_MANAGE_PENDING_POSTS'); ?>    
		</div> 
	</div>

	<div class="ed-dashboard__bd">

		<?php if ($pendingPosts) { ?>
			<?php foreach ($pendingPosts as $post) { ?>
			<div class="ed-dashboard-item" data-ed-moderation-post data-id="<?php echo $post->id; ?>">
				<div class="o-col ed-dashboard-item__col-name">
					<?php if ($post->isQuestion()) { ?>
						<a href="<?php echo EDR::_('view=ask&id=' . $post->id); ?>"><?php echo $post->title; ?></a>
					<?php } else { ?>
						<?php echo $post->title; ?>
					<?php } ?>
					<?php if ($post->isReply()) { ?>
					<p><?php echo $post->content; ?></p>
					<?php } ?>				
					<div class="t-mt--sm">
						<ol class="g-list-inline xed-post-item__post-meta">
							<li>
								<?php if ($post->isQuestion()) { ?>							
								<span class="o-label o-label--warning-o"><?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_MANAGE_POST_TYPE_QUESTION');?></span>
								<?php } else { ?>
								<span class="o-label o-label--info-o"><?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_MANAGE_POST_TYPE_REPLY');?></span>
								<?php } ?>
							</li>
						</ol>
					</div>
				</div>
				<div class="o-col ed-dashboard-item__col-timestamp">
					<div><?php echo $post->getDuration(); ?></div>
				</div>
				<div class="o-col ed-dashboard-item__col-avatar">
					<?php echo $this->html('user.avatar', $post->getOwner(), array('rank' => false, 'status' => true)); ?>
				</div>
				<div class="o-col ed-dashboard-item__col-btn-group">
					<button data-ed-moderation-post-approve class="btn btn-primary btn-sm is-loading"><?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_MANAGE_POST_APPROVE');?></button>
					<button data-ed-moderation-post-reject class="btn btn-danger btn-sm"><?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_MANAGE_POST_REJECT');?></button>
				</div>
				
			</div>
			<?php } ?>
		<?php } else { ?>
		<div class="is-empty">
			<div class="o-empty">
				<div class="o-empty__content">
					<i class="o-empty__icon fa fa-clock-o t-lg-mb--md"></i>
					<div class="o-empty__text"><?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_NO_POST_TO_MANAGE');?></div>
				</div>
			</div>    
		</div>  
		<?php } ?>
	</div>
</div>