<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var JdidealgatewayViewProfiles $this */

HTMLHelper::_('searchtools.form', '#adminForm', []);

$listOrdering  = $this->escape($this->state->get('list.ordering'));
$listDirection = $this->escape($this->state->get('list.direction'));
$saveOrder     = $listOrdering === 'profiles.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_jdidealgateway&task=profiles.saveOrderAjax&tmpl=component';
	HTMLHelper::_('sortablelist.sortable', 'profilesList', 'adminForm', strtolower($listDirection), $saveOrderingUrl);
}
?>
<form action="index.php?option=com_jdidealgateway&view=profiles" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
	<?php if (JVERSION < 4) : ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
	<?php endif; ?>
	<div id="j-main-container" class="span10">
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items alert-info">
				<?php if (JVERSION >= 4) : ?>
					<span class="fas fa-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
				<?php endif; ?>
				<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped" id="profilesList">
				<thead>
				<tr>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo HTMLHelper::_('searchtools.sort', '', 'profiles.ordering', $listDirection, $listOrdering, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
					</th>
					<th width="1%" class="nowrap center">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
					<th class="left">
						<?php echo Text::_('COM_ROPAYMENTS_PROFILES_NAME'); ?>
					</th>
					<th class="nowrap">
						<?php echo Text::_('COM_ROPAYMENTS_PROFILES_PSP'); ?>
					</th>
					<th class="nowrap">
						<?php echo Text::_('COM_ROPAYMENTS_PROFILES_ALIAS'); ?>
					</th>
                    <th class="nowrap center">
						<?php echo Text::_('COM_ROPAYMENTS_PROFILES_DEFAULT'); ?>
                    </th>
				</tr>
				</thead>
				<?php if (JVERSION < 4) : ?>
					<tfoot>
					<tr>
						<td colspan="15">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
					</tfoot>
				<?php endif; ?>
				<tbody>
				<?php
					$canEdit   = $this->canDo->get('core.edit');
					$canChange = $this->loggedInUser->authorise('core.edit.state',	'com_jdidealgateway');

					foreach ($this->items as $i => $item) :
						?>
					<tr>
						<td class="order nowrap center hidden-phone">
							<?php
							$iconClass = '';

							if (!$canChange)
							{
								$iconClass = ' inactive';
							}
                            elseif (!$saveOrder)
							{
								$iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::tooltipText('JORDERINGDISABLED');
							}
							?>
                            <span class="sortable-handler <?php echo $iconClass ?>">
									<span class="icon-menu"></span>
								</span>
							<?php if ($canChange && $saveOrder) : ?>
                                <input type="text" style="display:none" name="order[]" size="5"
                                       value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
							<?php endif; ?>
                        </td>
						<td class="center">
							<?php if ($canEdit || $canChange) : ?>
								<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
							<?php endif; ?>
						</td>
						<td>
							<div class="name break-word">
								<?php if ($canEdit) : ?>
									<a href="<?php echo Route::_('index.php?option=com_jdidealgateway&task=profile.edit&id=' . (int) $item->id); ?>" title="<?php echo Text::sprintf('COM_ROPAYMENTS_EDIT_PROFILE', $this->escape($item->name)); ?>">
										<?php echo $this->escape($item->name); ?></a>
								<?php else : ?>
									<?php echo $this->escape($item->name); ?>
								<?php endif; ?>
							</div>
						</td>
						<td class="break-word">
							<?php echo $this->escape(Text::_('COM_ROPAYMENTS_IDEAL_'. str_replace('-', '_', $item->psp))); ?>
						</td>
						<td class="break-word">
							<?php echo $this->escape($item->alias); ?>
						</td>
                        <td class="center">
							<?php echo HTMLHelper::_('jgrid.isdefault', $item->published, $i, 'profiles.', !$item->published && $canChange); ?>
                        </td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<?php if (JVERSION >= 4) : ?>
				<?php echo $this->pagination->getListFooter(); ?>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
