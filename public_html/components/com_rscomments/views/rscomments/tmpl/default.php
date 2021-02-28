<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access'); ?>

<div class="rscomments-top-alert alert alert-info" style="display: none;"></div>
<div class="rscomment-top-actions">
	<div class="pull-left">
		<div class="rscomments-top-loader" style="display:none;">
			<?php echo JHtml::image('com_rscomments/loader.gif', '', array(), true); ?>
		</div>
	</div>
	<div class="pull-right">
		<?php if ($this->config->enable_usercomments) { ?>
		<span class="rsc_my_comments">
			<?php if ($this->config->modal == 2) { ?>
			<a class="mycomments-modal <?php echo RSTooltip::tooltipClass(); ?>" href="<?php echo JRoute::_('index.php?option=com_rscomments&task=mycomments&tmpl=component', false); ?>" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_MY_COMMENTS_DESC')); ?>">
			<?php } else { ?>
			<a class="<?php echo RSTooltip::tooltipClass(); ?>" href="javascript:void(0);" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_MY_COMMENTS_DESC')); ?>" data-toggle="modal" data-target="#rscomments-mycomments" data-bs-toggle="modal" data-bs-target="#rscomments-mycomments">
			<?php } ?>
				<i class="fa fa-comments"></i> <?php echo JText::_('COM_RSCOMMENTS_MY_COMMENTS'); ?>
			</a>
		</span>
		<?php } ?>
	<?php
		if ($this->config->enable_subscription) {
			if (RSCommentsHelper::isSubscribed($this->id, $this->theoption)) {
				if ($this->user->get('id') > 0) { ?>
					<span class="rsc_subscr">
						<a class="<?php echo RSTooltip::tooltipClass(); ?>" href="javascript:void(0);" data-rsc-task="unsubscribe" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_UNSUBSCRIBE')); ?>">
							<i class="fa fa-envelope"></i> <?php echo JText::_('COM_RSCOMMENTS_UNSUBSCRIBE'); ?>
						</a>
					</span>
	<?php		}
			} else {
				if ($this->user->get('id') > 0) { ?>
					<span class="rsc_subscr">
						<a class="<?php echo RSTooltip::tooltipClass(); ?>" href="javascript:void(0);" data-rsc-task="subscribe" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_SUBSCRIBE')); ?>">
							<i class="fa fa-envelope"></i> <?php echo JText::_('COM_RSCOMMENTS_SUBSCRIBE'); ?>
						</a>
					</span>
	<?php		} else { ?>
					<span class="rsc_subscr">
						<a href="javascript:void(0)" class="<?php echo RSTooltip::tooltipClass(); ?>" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_SUBSCRIBE')); ?>" data-rsc-task="subscribeform">
							<i class="fa fa-envelope"></i> <?php echo JText::_('COM_RSCOMMENTS_SUBSCRIBE'); ?>
						</a>
					</span>
	<?php		}
			}
		}
			
		if ($this->config->enable_rss) { ?>
			<span class="rsc_rss">
				<a class="<?php echo RSTooltip::tooltipClass(); ?>" href="<?php echo RSCommentsHelper::route('index.php?option=com_rscomments&view=rscomments&format=feed&type=rss&opt='.$this->theoption.'&id='.$this->id); ?>" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_RSS')); ?>">
					<i class="fa fa-feed"></i> <?php echo JText::_('COM_RSCOMMENTS_RSS'); ?>
				</a>
			</span>
	<?php }
		if (isset($this->permissions['close_thread']) && $this->permissions['close_thread'] == 1) {
			if (RSCommentsHelper::getThreadStatus($this->id,$this->theoption)) { ?>
				<span class="rsc_thread">
					<a class="<?php echo RSTooltip::tooltipClass(); ?>" href="javascript:void(0);" data-rsc-task="open" data-rsc-override="<?php echo (int) $this->override; ?>" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_OPEN_THREAD')); ?>">
						<i class="fa fa-tag"></i> <?php echo JText::_('COM_RSCOMMENTS_OPEN_THREAD'); ?>
					</a> 
				</span>
	<?php	} else { ?>
				<span class="rsc_thread">
					<a class="<?php echo RSTooltip::tooltipClass(); ?>" href="javascript:void(0);" data-rsc-task="close" data-rsc-override="<?php echo (int) $this->override; ?>" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_CLOSE_THREAD')); ?>">
						<i class="fa fa-tag"></i> <?php echo JText::_('COM_RSCOMMENTS_CLOSE_THREAD'); ?>
					</a> 
				</span>
	<?php 	}
		} ?>
	</div>
	<div class="clearfix"></div>
</div>

<div class="rscomments-comments-list">
	<?php echo $this->loadTemplate('items'); ?>
</div>

<?php if ($this->pagination->pagesTotal > 1) { ?>
<div class="rsc_pagination">
	<?php if ($this->pagination->pagesCurrent != $this->pagination->pagesTotal) { ?>
		<a class="rsc_button btn btn-info" href="javascript:void(0);" data-rsc-task="pagination" data-task-override="<?php echo $this->override; ?>">
			<?php echo JText::_('COM_RSCOMMENTS_LOAD_MORE_COMMENTS'); ?>
		</a>
	<?php } ?>
</div>
<div class="rsc_loading_pages" style="text-align:center;display:none;">
	<?php echo JHtml::image('com_rscomments/loader.gif', '', array(), true); ?>
</div>
<span style="display:none;" class="rsc_total"><?php echo $this->total; ?></span>
<?php } ?>