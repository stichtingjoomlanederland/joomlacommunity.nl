<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="row-fluid">
	<div class="span12 panel-title">
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_ACL_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_ACL_DESC' );?>
		</p>
	</div>
</div>
<form action="index.php?option=com_easydiscuss" method="post" name="adminForm" id="adminForm">

	<div class="row-fluid filter-bar">
		<div class="pa-10">
			<div class="span12">
				<div class="pull-left form-inline">
					<input type="text" name="search" id="search" value="<?php echo $this->filter->search; ?>" class="input-medium" onchange="document.adminForm.submit();" placeholder="<?php echo JText::_( 'COM_EASYDISCUSS_SEARCH' , true );?>"/>
					<button class="btn btn-success" type="submit" onclick="this.form.submit();"><?php echo JText::_( 'COM_EASYDISCUSS_SEARCH' ); ?></button>
					<button class="btn" type="submit" onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_EASYDISCUSS_RESET' ); ?></button>
				</div>

				<div class="pull-right">
					<?php echo JText::_( 'COM_EASYDISCUSS_ACL_FILTER_BY' ); ?>: <?php echo $this->filter->type; ?>
				</div>
			</div>
		</div>
	</div>



		<table class="table table-striped table-discuss">
		<thead>
			<tr>
				<th class="title" style="width:1%;">
					<input type="checkbox" name="toggle" class="discussCheckAll" />
				</th>
				<th class="title" style="text-align:left;"><?php echo JHTML::_('grid.sort', 'COM_EASYDISCUSS_GROUP_NAME', 'a.`name`', $this->sort->orderDirection, $this->sort->order ); ?></th>
				<th class="title" style="width:6%;"><?php echo JHTML::_('grid.sort', 'COM_EASYDISCUSS_ID', 'a.`id`', $this->sort->orderDirection, $this->sort->order ); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3">
					<div class="footer-pagination">
						<?php echo $this->pagination->getListFooter(); ?>
					</div>
				</td>
			</tr>
		</tfoot>
		<tbody>
	<?php

		$k = 0;
		$x = 0;
		$count = 0;
		foreach($this->rulesets as $ruleset)
		{
			$tips 		= '';
			$iconPath	= 'components/com_easydiscuss/assets/images';

			foreach($ruleset as $key=>$value)
			{
				if($key!='name' && $key!='id' && $key != 'level')
				{
					$tipImg = empty($value)? $iconPath . '/publish_x.png' : $iconPath .'/tick.png';
					$tips .= '<div style="float:left; width:145px;">'.JText::_('COM_EASYDISCUSS_ACL_OPTION_' . $key).'</div><div style="float:left; width:10px;">:</div><div style="float:left;"><img src="'.$tipImg.'" /></div><div style="clear:both"></div>';
				}
			}

			$editlink = 'index.php?option=com_easydiscuss&controller=acl&task=edit&cid='.$ruleset->id.'&type='.$this->type;
	?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo JHTML::_('grid.id', $x++, $ruleset->id); ?></td>
				<td>
					<?php echo str_repeat('<span class="gi">|&mdash;</span>', $ruleset->level) ?>
					<a href="<?php echo JRoute::_($editlink);?>"><?php echo $ruleset->name; ?></a>
				</td>
				<td>
					<?php echo $ruleset->id;?>
				</td>
			</tr>
	<?php
			$k = 1 - $k;
			$count++;
		}
	?>
		</tbody>
		</table>




	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="view" value="acls" />
	<input type="hidden" name="controller" value="acl" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->sort->order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<input type="hidden" name="boxchecked" value="0" />
</form>
