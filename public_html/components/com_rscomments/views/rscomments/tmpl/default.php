<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access'); 
JText::script('COM_RSCOMMENTS_NO_SUBSCRIBER_NAME');
JText::script('COM_RSCOMMENTS_NO_SUBSCRIBER_EMAIL');
JText::script('COM_RSCOMMENTS_INVALID_SUBSCRIBER_EMAIL');
JText::script('COM_RSCOMMENTS_REPORT_NO_REASON');
JText::script('COM_RSCOMMENTS_REPORT_INVALID_CAPTCHA'); ?>

<div id="rscomments-top-alert" class="alert alert-info" style="display: none;"></div>
<div class="rscomment-top-actions">
	<div class="pull-left">
		<div id="rscomments-top-loader" style="display:none;">
			<?php echo JHtml::image('com_rscomments/loader.gif', '', array(), true); ?>
		</div>
	</div>
	<div class="pull-right">
	<?php
		if ($this->config->enable_subscription) {
			if (RSCommentsHelper::isSubscribed($this->id, $this->theoption)) {
				if ($this->user->get('id') > 0) { ?>
					<span id="rsc_subscr">
						<a class="<?php echo RSTooltip::tooltipClass(); ?>" href="javascript:void(0);" onclick="rsc_unsubscribe('<?php echo $this->id; ?>','<?php echo $this->theoption; ?>')" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_UNSUBSCRIBE')); ?>">
							<i class="fa fa-envelope"></i> <?php echo JText::_('COM_RSCOMMENTS_UNSUBSCRIBE'); ?>
						</a>
					</span>
	<?php		}
			} else {
				if ($this->user->get('id') > 0) { ?>
					<span id="rsc_subscr">
						<a class="<?php echo RSTooltip::tooltipClass(); ?>" href="javascript:void(0);" onclick="rsc_subscribe('<?php echo $this->id; ?>','<?php echo $this->theoption; ?>')" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_SUBSCRIBE')); ?>">
							<i class="fa fa-envelope"></i> <?php echo JText::_('COM_RSCOMMENTS_SUBSCRIBE'); ?>
						</a>
					</span>
	<?php		} else { ?>
					<span id="rsc_subscr">
						<a href="javascript:void(0)" class="<?php echo RSTooltip::tooltipClass(); ?>" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_SUBSCRIBE')); ?>" data-toggle="modal" data-target="#rscomments-subscribe">
							<i class="fa fa-envelope"></i> <?php echo JText::_('COM_RSCOMMENTS_SUBSCRIBE'); ?>
						</a>
					</span>
	<?php		}
			}
		}
			
		if ($this->config->enable_rss) { ?>
			<span id="rsc_rss">
				<a class="<?php echo RSTooltip::tooltipClass(); ?>" href="<?php echo RSCommentsHelper::route('index.php?option=com_rscomments&view=rscomments&format=feed&type=rss&opt='.$this->theoption.'&id='.$this->id); ?>" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_RSS')); ?>">
					<i class="fa fa-feed"></i> <?php echo JText::_('COM_RSCOMMENTS_RSS'); ?>
				</a>
			</span>
	<?php }
		if (isset($this->permissions['close_thread']) && $this->permissions['close_thread'] == 1) {
			if (RSCommentsHelper::getThreadStatus($this->id,$this->theoption)) { ?>
				<span id="rsc_thread">
					<a class="<?php echo RSTooltip::tooltipClass(); ?>" href="javascript:void(0);" onclick="rsc_open('<?php echo $this->id; ?>','<?php echo $this->theoption; ?>','<?php echo (int) $this->override; ?>');" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_OPEN_THREAD')); ?>">
						<i class="fa fa-tag"></i> <?php echo JText::_('COM_RSCOMMENTS_OPEN_THREAD'); ?>
					</a> 
				</span>
	<?php	} else { ?>
				<span id="rsc_thread">
					<a class="<?php echo RSTooltip::tooltipClass(); ?>" href="javascript:void(0);" onclick="rsc_close('<?php echo $this->id; ?>','<?php echo $this->theoption; ?>','<?php echo (int) $this->override; ?>');" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_CLOSE_THREAD')); ?>">
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

<?php if ($this->pagination->get('pages.total') > 1) { ?>
<div id="rsc_global_pagination" class="rsc_pagination pagination">
	<?php if ($this->pagination->get('pages.current') != $this->pagination->get('pages.total')) { ?>
		<a id="rscommentsPagination" class="rsc_button btn btn-info" href="javascript:void(0);" onclick="rsc_pagination('<?php echo ($this->pagination->get('pages.current')*$this->config->nr_comments); ?>', '<?php echo $this->theoption; ?>', '<?php echo $this->id; ?>', '<?php echo $this->template; ?>', '<?php echo $this->override; ?>');">
			<?php echo JText::_('COM_RSCOMMENTS_LOAD_MORE_COMMENTS'); ?>
		</a>
	<?php } ?>
</div>
<div id="rsc_loading_pages" style="text-align:center;display:none;">
	<?php echo JHtml::image('com_rscomments/loader.gif', '', array(), true); ?>
</div>
<span style="display:none;" id="rsc_total"><?php echo $this->total; ?></span>
<?php } ?>