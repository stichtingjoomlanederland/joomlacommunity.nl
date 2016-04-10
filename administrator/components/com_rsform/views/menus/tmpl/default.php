<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<p><?php JText::sprintf('RSFP_ADD_TO_MENU', $this->formTitle); ?></p>
	<table class="adminlist table table-striped" id="articleList">
	<thead>
		<tr>
			<th width="20"><?php echo JText::_('#'); ?></th>
			<th class="title" nowrap="nowrap"><?php echo JText::_('RSFP_TITLE'); ?></th>
			<th class="title" nowrap="nowrap"><?php echo JText::_('RSFP_TYPE'); ?></th>
			<th width="3%"><?php echo JText::_('ID'); ?></th>
		</tr>
	</thead>
	<?php
	$i = 0;
	$k = 0;
	foreach ($this->menus as $menu) { ?>
		<tr class="row<?php echo $k; ?>">
			<td align="center" width="30"><?php echo $this->pagination->getRowOffset($i); ?></td>
			<td><a href="index.php?option=com_rsform&amp;task=setmenu&amp;menutype=<?php echo urlencode($menu->menutype); ?>&amp;formId=<?php echo $this->formId; ?>"><?php echo $this->escape($menu->title); ?></a></td>
			<td><?php echo $this->escape($menu->menutype); ?></td>
			<td align="center"><?php echo $menu->id; ?></td>
		</tr>
	<?php
		$i++;
		$k=1-$k;
	}
	?>
	<tfoot>
		<tr>
			<td colspan="4">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	</table>

	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="view" value="menus" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="formId" value="<?php echo $this->formId; ?>" />
</form>