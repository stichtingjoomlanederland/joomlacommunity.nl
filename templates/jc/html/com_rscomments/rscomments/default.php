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

<div class="rscomments-comments-list">
	<?php echo $this->loadTemplate('items'); ?>
</div>

<?php if ($this->pagination->get('pages.total') > 1) { ?>
<div id="rsc_global_pagination" class="rsc_pagination pagination">
	<?php if ($this->pagination->get('pages.current') != $this->pagination->get('pages.total')) { ?>
		<a id="rscommentsPagination" class="rsc_button btn btn-info" href="javascript:void(0);" onclick="rsc_pagination('<?php echo ($this->pagination->get('pages.current')*$this->config->nr_comments); ?>', '<?php echo $this->option; ?>', '<?php echo $this->id; ?>', '<?php echo $this->template; ?>', '<?php echo $this->override; ?>');">
			<?php echo JText::_('COM_RSCOMMENTS_LOAD_MORE_COMMENTS'); ?>
		</a>
	<?php } ?>
</div>
<div id="rsc_loading_pages" style="text-align:center;display:none;"><img src="<?php echo RSCommentsHelper::ImagePath('loader.gif'); ?>" alt="" /></div>
<span style="display:none;" id="rsc_total"><?php echo $this->total; ?></span>
<?php } ?>