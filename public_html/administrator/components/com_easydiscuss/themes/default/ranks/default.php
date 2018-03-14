<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data" id="adminForm">
	<div class="row-fluid ">
		<div class="span12">
		<div class="panel">
			<div class="panel-head">
					<h6><?php echo JText::_( 'COM_EASYDISCUSS_RANKING' );?></h6>
				</div>

			<div id="rank01" class="panel-body">
				<div class="">
					<div class="pb-10">
						<?php if (!$this->config->get('main_ranking')) { ?>
							<?php echo JText::_('COM_EASYDISCUSS_RANKING_DISABLED_BY_ADMIN'); ?>
						<?php } else { ?>
							<?php echo JText::sprintf('COM_EASYDISCUSS_RANKING_NOTE', $rankingType); ?>
						<?php } ?>
					</div>
					<div class="row-table">
						<div class="col-cell pr-10">
							<input type="text" class="form-control" id="newtitle" name="newtitle" value="" placeholder="<?php echo JText::_( 'COM_EASYDISCUSS_RANKING_TITLE' );?>" />
						</div>
						<div class="col-cell cell-tight">
							<input class="btn btn-primary" type="button" data-rank-add value="<?php echo JText::_('COM_EASYDISCUSS_RANKING_ADD'); ?>" />
							</div>
						</div>
						<div id="sys-msg" style="color:red;"></div>
					</div>

				<hr>

				<div class="ed-bleed--middle">
					<table class="app-table table">
						<thead>
							<tr>
								<th class="title" colspan="2"><?php echo JText::_('COM_EASYDISCUSS_RANKING_TITLE'); ?></th>
								<th width="200" style="text-align: center;"><?php echo JText::_('COM_EASYDISCUSS_RANKING_START_POINT'); ?></th>
								<th width="200" style="text-align: center;"><?php echo JText::_('COM_EASYDISCUSS_RANKING_END_POINT'); ?></th>
								<th width="20" style="text-align: center;">&nbsp;</th>
							</tr>
						</thead>
						<tbody id="rank-list">
							<?php if ($ranks) { ?>
								<?php $i = 1; ?>
								<?php foreach ($ranks as $rank) { ?>
								<tr id="rank-<?php echo $rank->id; ?>">
									<td width="1">
										<?php echo $i++; ?>
										<input type="hidden" name="id[]" value="<?php echo $rank->id; ?>" />
									</td>
									<td style="text-align: center;"><input data-title-text type="text" name="title[]" value="<?php echo $rank->title; ?>" class="form-control"/></td>
									<td style="text-align: center;"><input data-start-text style="text-align: center;" type="text" name="start[]" value="<?php echo $rank->start; ?>" class="form-control"/></td>
									<td style="text-align: center;"><input data-end-text style="text-align: center;" type="text" name="end[]" value="<?php echo $rank->end; ?>" class="form-control"/></td>
									<td style="text-align: center;"><a href="javascript:void(0);" data-remove-button data-id="<?php echo $rank->id;?>" class="btn btn-danger"><?php echo JText::_('COM_EASYDISCUSS_RANKING_DELETE'); ?></a></td>
								</tr>
								<?php
										$itemCnt = $rank->id;
									}//end foreach
								?>
							<?php } else { ?>
							<tr>
								<td colspan="5" class="text-center"><?php echo JText::_('COM_EASYDISCUSS_RANKING_EMPTY_LIST'); ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
				</div>
			</div>
		</div>

	</div>


	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="controller" value="ranks" />
	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" value="<?php echo ++$itemCnt; ?>" id="itemCnt" name="itemCnt" />
	<input type="hidden" value="" id="itemRemove" name="itemRemove" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
