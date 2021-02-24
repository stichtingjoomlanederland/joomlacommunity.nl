<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_RANKING'); ?>

				<div class="panel-body">

					<div class="panel-info">

						<p>
						<?php if (!$this->config->get('main_ranking')) { ?>
							<?php echo JText::_('COM_EASYDISCUSS_RANKING_DISABLED_BY_ADMIN'); ?>
						<?php } else { ?>
							<?php echo JText::sprintf('COM_EASYDISCUSS_RANKING_NOTE', $rankingType, 'index.php?option=com_easydiscuss&view=settings&layout=users&goto=3a5e4708f631543b'); ?>
						<?php } ?>
						</p>

						<div class="row t-mt--md">
							<div class="col-md-4">
								<div class="o-input-group">
									<input type="text" class="o-form-control" id="newtitle" name="newtitle" value="" placeholder="<?php echo JText::_( 'COM_EASYDISCUSS_RANKING_TITLE' );?>" />

									<a href="javascript:void(0);" class="o-btn o-btn--default-o" data-rank-add>
										<?php echo JText::_('COM_EASYDISCUSS_RANKING_ADD'); ?>
									</a>
								</div>
							</div>
						</div>
						<div id="sys-msg" style="color:red;"></div>
					</div>

					<hr />

					<div class="ed-bleed--middle">
						<table class="app-table table">
							<thead>
								<tr>
									<th width="5%">&nbsp;</th>
									<th class="title">
										<?php echo JText::_('COM_EASYDISCUSS_RANKING_TITLE'); ?>
									</th>
									<th width="20%" class="center"><?php echo JText::_('COM_EASYDISCUSS_RANKING_START_POINT'); ?></th>
									<th width="20%" class="center">
										<?php echo JText::_('COM_EASYDISCUSS_RANKING_END_POINT'); ?>
									</th>
									<th width="5%" class="center">&nbsp;</th>
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
										<td>
											<input data-title-text type="text" name="title[]" value="<?php echo $rank->title; ?>" class="o-form-control"/>
										</td>
										<td class="center">
											<input data-start-text type="text" name="start[]" value="<?php echo $rank->start; ?>" class="o-form-control text-center"/>
										</td>
										<td class="center">
											<input data-end-text type="text" name="end[]" value="<?php echo $rank->end; ?>" class="o-form-control text-center" />
										</td>
										<td class="center">
											<a href="javascript:void(0);" data-remove-button data-id="<?php echo $rank->id;?>" class="o-btn o-btn--default-o t-text--danger">
												<i class="fa fa-times"></i>
											</a>
										</td>
									</tr>
									<?php
											$itemCnt = $rank->id;
										}
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
	</div>

	<?php echo $this->html('form.action', 'ranks', 'save'); ?>

	<input type="hidden" value="<?php echo ++$itemCnt; ?>" id="itemCnt" name="itemCnt" />
	<input type="hidden" value="" id="itemRemove" name="itemRemove" />
</form>
