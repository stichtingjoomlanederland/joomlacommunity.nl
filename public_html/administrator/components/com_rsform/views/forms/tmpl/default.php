<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('bootstrap.tooltip');

JText::script('COM_RSFORM_ARE_YOU_SURE_YOU_WANT_TO_CLEAR');
?>

<form action="index.php?option=com_rsform" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php echo $this->filterbar->show(); ?>
	<table class="adminlist table table-striped table-align-middle" id="articleList">
		<thead>
		<tr>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('#'); ?></th>
			<th width="1%" nowrap="nowrap"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
			<th class="title"><?php echo JHtml::_('grid.sort', JText::_('RSFP_FORM_TITLE'), 'FormTitle', $this->sortOrder, $this->sortColumn, 'forms.manage'); ?></th>
			<th class="title"><?php echo JHtml::_('grid.sort', JText::_('RSFP_FORM_NAME'), 'FormName', $this->sortOrder, $this->sortColumn, 'forms.manage'); ?></th>
			<th width="1%" nowrap="nowrap" class="title"><?php echo JHtml::_('grid.sort', JText::_('RSFP_PUBLISHED'), 'Published', $this->sortOrder, $this->sortColumn, 'forms.manage'); ?></th>
            <?php if ($this->user->authorise('submissions.manage', 'com_rsform')) { ?>
			<th width="1%" nowrap="nowrap" class="title"><?php echo JText::_('RSFP_SUBMISSIONS'); ?></th>
            <?php } ?>
			<th class="title"><?php echo JText::_('RSFP_TOOLS'); ?></th>
			<th class="title" width="1%" nowrap="nowrap"><?php echo JText::_('RSFP_LAST_LANGUAGE'); ?></th>
			<th width="1%" nowrap="nowrap" class="title"><?php echo JHtml::_('grid.sort', JText::_('RSFP_FORM_ID'), 'FormId', $this->sortOrder, $this->sortColumn, 'forms.manage'); ?></th>
		</tr>
		</thead>
	<?php
	$i = 0;
	$k = 0;
	foreach($this->forms as $row)
	{
		$row->published = $row->Published;
		$row->FormTitle = strip_tags($row->FormTitle);
		?>
		<tr class="row<?php echo $k; ?>">
			<td width="1%" nowrap="nowrap"><?php echo $this->pagination->getRowOffset($i); ?></td>
			<td width="1%" nowrap="nowrap"><?php echo JHtml::_('grid.id', $i, $row->FormId); ?></td>
			<td><a href="index.php?option=com_rsform&amp;view=forms&amp;layout=edit&amp;formId=<?php echo $row->FormId; ?>"><?php echo !empty($row->FormTitle) ? $row->FormTitle : '<em>'.JText::_('RSFP_FORM_DEFAULT_TITLE').'</em>'; ?></a></td>
			<td><?php echo $row->FormName; ?></td>
			<td width="1%" nowrap="nowrap" align="center"><?php echo JHtml::_('jgrid.published', $row->published, $i, 'forms.'); ?></td>
            <?php if ($this->user->authorise('submissions.manage', 'com_rsform')) { ?>
			<td width="1%" nowrap="nowrap">
				<span class="<?php echo RSFormProHelper::getTooltipClass(); ?>" title="<?php echo JText::sprintf('RSFP_TODAY_SUBMISSIONS', $row->_todaySubmissions); ?>"><a href="index.php?option=com_rsform&amp;view=submissions&amp;formId=<?php echo $row->FormId; ?>"><i class="rsficon rsficon-calendar"></i> <?php echo $row->_todaySubmissions; ?></a></span>
				<span class="<?php echo RSFormProHelper::getTooltipClass(); ?>" title="<?php echo JText::sprintf('RSFP_MONTH_SUBMISSIONS', $row->_monthSubmissions); ?>"><a href="index.php?option=com_rsform&amp;view=submissions&amp;formId=<?php echo $row->FormId; ?>"><i class="rsficon rsficon-calendar"></i> <?php echo $row->_monthSubmissions; ?></a></span>
				<span class="<?php echo RSFormProHelper::getTooltipClass(); ?>" title="<?php echo JText::sprintf('RSFP_ALL_SUBMISSIONS', $row->_allSubmissions); ?>"><a href="index.php?option=com_rsform&amp;view=submissions&amp;formId=<?php echo $row->FormId; ?>"><i class="rsficon rsficon-calendar"></i> <?php echo $row->_allSubmissions; ?></a></span>
			</td>
            <?php } ?>
			<td align="center" nowrap="nowrap">
				<a class="btn" href="<?php echo JUri::root(); ?>index.php?option=com_rsform&amp;view=rsform&amp;formId=<?php echo $row->FormId; ?>" target="_blank"><span class="rsficon rsficon-eye rsficon-green"></span> <?php echo JText::_('RSFP_PREVIEW'); ?></a>
				<div class="btn-group">
					<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><?php echo JText::_('RSFP_TOOLS'); ?> <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="<?php echo JText::_('RSFP_TOOLS'); ?>">
						<li><a href="index.php?option=com_rsform&amp;task=forms.menuadd.screen&amp;formId=<?php echo $row->FormId; ?>"><span class="rsficon rsficon-share rsficon-blue"></span> <?php echo JText::_('RSFP_LINK_TO_MENU'); ?></a></li>
						<?php if ($row->Backendmenu) { ?>
						<li><a href="index.php?option=com_rsform&amp;task=forms.menuremove.backend&amp;formId=<?php echo $row->FormId; ?>"><span class="rsficon rsficon-minus-circle rsficon-red"></span>  <?php echo JText::_('LINK_TO_BACKEND_REMOVE_MENU'); ?></a></li>
						<?php } else { ?>
						<li><a href="index.php?option=com_rsform&amp;task=forms.menuadd.backend&amp;formId=<?php echo $row->FormId; ?>"><span class="rsficon rsficon-plus-circle rsficon-green"></span>  <?php echo JText::_('LINK_TO_BACKEND_MENU'); ?></a></li>
						<?php } ?>
                        <?php if ($this->user->authorise('submissions.manage', 'com_rsform')) { ?>
						<li><a href="index.php?option=com_rsform&amp;task=submissions.clear&amp;formId=<?php echo $row->FormId; ?>&amp;<?php echo JSession::getFormToken(); ?>=1" onclick="return (confirm(Joomla.JText._('COM_RSFORM_ARE_YOU_SURE_YOU_WANT_TO_CLEAR')));"><span class="rsficon rsficon-times-circle-o rsficon-red"></span>  <?php echo JText::_('RSFP_CLEAR_SUBMISSIONS'); ?></a></li>
                        <?php } ?>
					</ul>
				</div>
			</td>
			<td width="1%" nowrap="nowrap"><?php echo $this->escape(RSFormProHelper::getCurrentLanguage($row->FormId)); ?></td>
			<td width="1%" nowrap="nowrap"><?php echo $row->FormId; ?></td>
		</tr>
	<?php
		$i++;
		$k=1-$k;
	}
	?>
	<tfoot>
	<tr>
		<td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
	</tr>
	</tfoot>
	</table>
	</div>
	
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="task" value="forms.manage" />
	
	<input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortOrder; ?>" />
</form>