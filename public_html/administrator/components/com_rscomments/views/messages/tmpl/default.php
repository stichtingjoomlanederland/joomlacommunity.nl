<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.keepalive'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rscomments&task=message.edit'); ?>" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<div class="clearfix"></div>
		<table class="table table-striped table-hover adminlist" id="rsc_comments_tbl" width="100%">
			<thead>
				<tr><th width="25%"><?php echo JText::_('COM_RSCOMMENTS_MESSAGES_LANGUAGE'); ?></th></tr>
			</thead>
			<tbody>
		<?php foreach ($this->items as $row) { ?>
				<tr><td><a href="<?php echo JRoute::_('index.php?option=com_rscomments&task=message.edit&tag='.$row->tag); ?>"><?php echo RSCommentsHelper::language($row->tag); ?></a></td></tr>
		<?php } ?>
			</tbody>
		</table>
		</div> <!-- .span10 -->
<?php echo JHtml::_('form.token'); ?>
<input type="hidden" name="task" value="" />

</div> <!-- .row-fluid -->
</form>