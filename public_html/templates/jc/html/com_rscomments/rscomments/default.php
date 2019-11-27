<?php
/**
 * @package RSComments!
 * @copyright (C) 2015 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access'); ?>

<?php if (count($this->comments)): ?>
    <h3><?php echo count($this->comments); ?><?php echo (count($this->comments) == 1) ? ' reactie' : ' reacties'; ?></h3>
    <div class="rscomments-comments-list">
		<?php echo $this->loadTemplate('items'); ?>
    </div>
<?php endif; ?>

<?php if ($this->pagination->get('pagesTotal') > 1) { ?>
	<div class="rsc_pagination pagination">
		<?php if ($this->pagination->get('pagesCurrent') != $this->pagination->get('pagesTotal')) { ?>
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