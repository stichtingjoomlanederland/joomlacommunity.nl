<?php
/**
 * @package     Slider
 * @subpackage  com_slider
 *
 * @copyright   Copyright (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<form action="index.php?option=com_slider&view=slides" method="post" id="adminForm" name="adminForm">
	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th width="2%">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
					<th width="5%">
						<?php echo JText::_('JSTATUS'); ?>
					</th>
					<th width="80%">
						<?php echo JText::_('COM_SLIDES_TITLE') ;?>
					</th>
					<th width="2%">
						<?php echo JText::_('JGRID_HEADING_ID'); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="5">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach ($this->items as $i => $row) :
					$link = JRoute::_('index.php?option=com_slider&task=slide.edit&id=' . $row->id);
				?>
					<tr>
						<td>
							<?php echo JHtml::_('grid.id', $i, $row->id); ?>
						</td>
						<td align="center">
							<?php echo JHtml::_('jgrid.published', $row->published, $i, 'slides.', true, 'cb'); ?>
						</td>
						<td>
							<a href="<?php echo $link; ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
								<?php echo $row->title; ?>
							</a>
						</td>
						<td align="center">
							<?php echo $row->id; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
