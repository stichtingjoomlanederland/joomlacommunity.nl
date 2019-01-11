<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

$thread 	= RSCommentsHelper::getThreadStatus($this->id,$this->theoption);
$sid 		= JFactory::getSession()->getId();
$ownComment = false;
$ownComment	= $this->user->guest ? ($this->comment->sid == $sid) : ($this->comment->uid == $this->user->id);
$canEmail	= isset($this->permissions['show_emails']) && $this->permissions['show_emails'];
$canReply	= !empty($this->permissions['enable_reply']) && !$thread;
$canQuote	= isset($this->permissions['new_comments']) && $this->permissions['new_comments'] && !$thread;
$canViewIP	= isset($this->permissions['view_ip']) && $this->permissions['view_ip'];
$canPublish	= isset($this->permissions['publish_comments']) && $this->permissions['publish_comments'];
$canDelete	= ((isset($this->permissions['delete_own_comment']) && $this->permissions['delete_own_comment']) && $ownComment ) || (isset($this->permissions['delete_comments']) && $this->permissions['delete_comments']);
$canEdit	= ((isset($this->permissions['edit_own_comment']) && $this->permissions['edit_own_comment']) && $ownComment && !$thread) || (isset($this->permissions['edit_comments']) && $this->permissions['edit_comments'] && !$thread); ?>

<div id="rscomment<?php echo $this->comment->IdComment; ?>" class="media rscomment rsc_comment_big_box<?php echo $this->comment->level; ?>" <?php echo !$this->comment->published ? 'style="opacity:0.6;"' : ''; ?> itemprop="comment" itemscope itemtype="http://schema.org/UserComments" data-rsc-cid="<?php echo $this->comment->IdComment; ?>">
	<?php $name			= RSCommentsHelper::name($this->comment, $this->permissions); ?>
	<?php $social		= RSCommentsHelper::getUserSocialLink($this->comment->uid); ?>
	<?php $badComment	= isset($this->config->negative_count) && $this->config->negative_count && $this->comment->neg >= $this->config->negative_count; ?>
	
	<div class="rscomment-body">
	
		<?php if ($this->config->avatar) { ?>
		<div class="media-container pull-left">
			<?php if ($social) { ?><a href="<?php echo $social; ?>"><?php } ?>
				<?php echo RSCommentsHelper::getAvatar($this->comment->uid, $this->comment->email, null, 'media-object'); ?>
			<?php if ($social) { ?></a><?php } ?>
		</div>
		<?php } ?>
	
		<div class="media-body">
			<div class="rscomm-header">
				
				<span itemprop="creator" itemscope itemtype="http://schema.org/Person">
				<span itemprop="name" class="rscomm-big">
				<?php if ($this->config->enable_website_field == 1 && !empty($this->comment->website)) { ?>
				<a class="rscomm-user" itemprop="url" href="<?php echo $this->comment->website; ?>" <?php echo $this->config->nofollow_rel == 1 ? 'rel="nofollow"' : ''; ?> target="_blank">
				<?php } ?>
				<?php echo $name['cleanname']; ?>
				<?php if ($this->config->enable_website_field == 1 && !empty($this->comment->website)) { ?>
				</a>
				<?php } ?>
				</span>
				</span>
				
				<small class="rscomm-time muted <?php echo RSTooltip::tooltipClass(); ?>" title="<?php echo RSTooltip::tooltipText(RSCommentsHelper::showDate($this->comment->date)); ?>">
					<i class="rscomm-meta-icon fa fa-clock-o"></i> <time itemprop="commentTime" datetime="<?php echo RSCommentsHelper::showDate($this->comment->date,'Y-m-d H:i:s'); ?>"><?php echo RSCommentsHelper::humanReadableDate($this->comment->date); ?></time>
				</small>
				
				<?php if (isset($this->config->enable_modified) && $this->config->enable_modified) { ?>
				<?php if (!empty($this->comment->modified) && $this->comment->modified != JFactory::getDbo()->getNullDate()) { ?>
				<div class="clearfix"></div>
				<small class="rscomm-meta-item rscomm-last-edited rscomm-time muted">
					<?php echo JText::sprintf('COM_RSCOMMENTS_LAST_MODIFIED_ON',RSCommentsHelper::showDate($this->comment->modified)); ?>
					<?php $modified_by = $this->comment->modified_by ? JFactory::getUser($this->comment->modified_by)->get('name') : JText::_('COM_RSCOMMENTS_GUEST'); ?>
					<?php echo JText::sprintf('COM_RSCOMMENTS_LAST_MODIFIED_BY',$modified_by); ?>
				</small>
				<?php } ?>
				<?php } ?>
				
			</div>
			
			<?php if ($this->config->enable_title_field == 1 && !empty($this->comment->subject)) { ?>
			<h5 class="rscomm-heading media-heading"><?php echo $this->comment->subject; ?></h5>
			<?php } ?>
			
			<?php if ($badComment) { ?>
			<span id="comment-hidden-<?php echo $this->comment->IdComment; ?>"><?php echo JText::_('COM_RSCOMMENTS_COMMENT_HIDDEN'); ?> <a href="javascript:void(0);" data-rsc-task="showcomment"><?php echo JText::_('COM_RSCOMMENTS_COMMENT_HIDDEN_LINK'); ?></a></span>
			<?php } ?>
			
			<span id="c<?php echo $this->comment->IdComment; ?>" class="rscomm-content<?php if ($badComment) { ?> muted hidden<?php } ?>" itemprop="commentText">
				<?php echo RSCommentsHelper::parseComment($this->comment->comment, $this->permissions, true); ?>
			</span>
			
			<?php if (!empty($this->comment->file)) { ?>
			<a href="<?php echo RSCommentsHelper::route('index.php?option=com_rscomments&task=download&id='.$this->comment->IdComment,false);?>" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_ATTACHMENT')); ?>" class="attached <?php echo RSTooltip::tooltipClass(); ?>">
				<i class="fa fa-file"></i> <?php echo $this->comment->file; ?>
			</a>
			<?php } ?>
			
			<hr />
			
			<div class="rscomm-meta">
				
				<?php if ($this->config->enable_location || $this->config->enable_reports || $canEmail || $canViewIP || $canEdit || $canDelete || $canPublish) { ?>
				<span class="rscomm-meta-item rscomm-flag muted">
					
					<?php if ($this->config->enable_location && !empty($this->comment->location)) { ?>
					<?php $locationlink = $this->comment->coordinates ? 'https://www.google.com/maps/place/'.$this->comment->coordinates : 'javascript: void(0)'; ?>
					<a href="<?php echo $locationlink; ?>" target="_blank" class="<?php echo RSTooltip::tooltipClass(); ?>" title="<?php echo RSTooltip::tooltipText($this->comment->location); ?>">
						<i class="rscomm-meta-icon fa fa-map-marker"></i>
					</a>
					<?php } ?>
					
					<?php if ($canEmail) { ?>
					<a href="mailto:<?php echo $this->comment->email; ?>" title="<?php echo $this->comment->email; ?>" class="<?php echo RSTooltip::tooltipClass(); ?>">
						<i class="rscomm-meta-icon fa fa-envelope"></i>
					</a>
					<?php } ?>
					
					<?php if ($this->config->enable_reports) { ?>
					<a href="javascript:void(0)" class="<?php echo RSTooltip::tooltipClass(); ?>" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_REPORT_COMMENT')); ?>" data-rsc-task="report">
						<i class="rscomm-meta-icon fa fa-flag"></i>
					</a>
					<?php } ?>
					
					<?php if ($canViewIP) { ?>
					<a href="http://www.db.ripe.net/whois?searchtext=<?php echo $this->comment->ip; ?>" target="_blank" class="<?php echo RSTooltip::tooltipClass(); ?>" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_IP_ADDRESS'),$this->comment->ip); ?>">
						<i class="rscomm-meta-icon fa fa-home"></i>
					</a>
					<?php } ?>
					
					<?php if ($canEdit) { ?>
					<a class="<?php echo RSTooltip::tooltipClass(); ?>" href="javascript:void(0);" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_EDIT_COMMENT')); ?>" data-rsc-task="edit">
						<i class="rscomm-meta-icon fa fa-edit"></i>
					</a>
					<?php } ?>
					
					<?php if ($canDelete) { ?>
					<a class="<?php echo RSTooltip::tooltipClass(); ?>" href="javascript:void(0);" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_DELETE_COMMENT')); ?>" data-rsc-task="delete" data-rsc-text="<?php echo JText::_('COM_RSCOMMENTS_DELETE_COMMENT_CONFIRM'); ?>">
						<i class="rscomm-meta-icon fa fa-trash"></i>
					</a>
					<?php } ?>
					
					<?php if ($canPublish) { ?>
					<?php $publish = ($this->comment->published == 1) ? 'minus-circle' : 'check'; ?>
					<?php $function = ($this->comment->published == 1) ? ' data-rsc-task="unpublish"' : ' data-rsc-task="publish"'; ?>
					<?php $message = ($this->comment->published == 1) ? JText::_('COM_RSCOMMENTS_UNPUBLISH') : JText::_('COM_RSCOMMENTS_PUBLISH'); ?>
					<span id="rsc_publish<?php echo $this->comment->IdComment; ?>">
						<a class="<?php echo RSTooltip::tooltipClass(); ?>" href="javascript:void(0);" title="<?php echo RSTooltip::tooltipText($message); ?>"<?php echo $function; ?>>
							<i class="rscomm-meta-icon fa fa-<?php echo $publish; ?>"></i>
						</a>
					</span>
					<?php } ?>
					
				</span>
				<?php } ?>
				
				<?php if ($this->config->enable_votes) { ?>
				<span class="rscomm-meta-item rscomm-rate">
					
					<?php $positive = '<a class="'.RSTooltip::tooltipClass().'" href="javascript:void(0);" title="'.RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_GOOD_COMMENT')).'" data-rsc-task="voteup"><i class="rscomm-meta-icon fa fa-thumbs-up"></i></a>'; ?>
					<?php $negative = '<a class="'.RSTooltip::tooltipClass().'" href="javascript:void(0);" title="'.RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_BAD_COMMENT')).'" data-rsc-task="votedown"><i class="rscomm-meta-icon fa fa-thumbs-down"></i></a>'; ?>
					
					
					<?php if (isset($this->permissions['vote_comments']) && $this->permissions['vote_comments']) { ?>
					<?php $voted = RSCommentsHelper::voted($this->comment->IdComment); ?>
					<?php if (empty($voted)) { ?>
					<span id="rsc_voting<?php echo $this->comment->IdComment; ?>"><?php echo $positive.' '.$negative; ?></span>
					<?php } else { ?>
					<?php if ($this->comment->pos - $this->comment->neg > 0) { ?>
					<i class="rscomm-meta-icon fa fa-thumbs-up"></i>
					<span class="rsc_green"><?php echo $this->comment->pos - $this->comment->neg; ?></span>
					<?php } else { ?>
					<i class="rscomm-meta-icon fa fa-thumbs-down"></i>
					<span class="rsc_red"><?php echo $this->comment->pos - $this->comment->neg; ?></span>
					<?php } ?>
					<?php } ?>
					
					<?php } else { ?>
					<?php if ($this->comment->pos - $this->comment->neg > 0) { ?>
					<i class="rscomm-meta-icon fa fa-thumbs-up"></i>
					<span class="rsc_green"><?php echo $this->comment->pos - $this->comment->neg; ?></span>
					<?php } else { ?>
					<i class="rscomm-meta-icon fa fa-thumbs-down"></i>
					<span class="rsc_red"><?php echo $this->comment->pos - $this->comment->neg; ?></span>
					<?php } ?>
					<?php } ?>

				</span>
				<?php } ?>
				
				<?php if ($this->comment->published && ($canReply || $canQuote)) { ?>
				<span class="rscomm-meta-item rscomm-actions">
					<?php if ($canReply) { ?>
					<button class="btn btn-mini btn-primary" type="button" data-rsc-commentid="<?php echo $this->comment->IdComment; ?>" data-rsc-task="reply"><?php echo JText::_('COM_RSCOMMENTS_REPLY'); ?></button>
					<?php } ?>
					
					<?php if ($canQuote) { ?>
					<button class="btn btn-mini" type="button" data-rsc-commentid="<?php echo $this->comment->IdComment; ?>" data-rsc-task="quote" data-rsc-name="<?php echo $this->escape($name['cleanname']); ?>"><?php echo JText::_('COM_RSCOMMENTS_COMMENT_QUOTE'); ?></button>
					<?php } ?>
				</span>
				<?php } ?>
				
			</div>
			
			<div id="rscomments-reply-<?php echo $this->comment->IdComment; ?>"></div>
			
		</div>
	</div>
	<div id="rscomment-comment-loader-<?php echo $this->comment->IdComment; ?>" class="rscomment-loader" style="display: none;">
		<?php echo JHtml::image('com_rscomments/loader.gif', '', array(), true); ?>
	</div>
</div>