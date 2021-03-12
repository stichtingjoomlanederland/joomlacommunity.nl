<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

$user      = Factory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'datalayer.ordering';

HTMLHelper::_('stylesheet', 'com_pwtseo/pwtseo.css', array('version' => 'auto', 'relative' => true));

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_pwtseo&task=datalayers.saveOrderAjax&tmpl=component';
	HTMLHelper::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<form action="<?php echo Route::_('index.php?option=com_pwtseo&view=datalayers'); ?>" method="post" name="adminForm"
      id="adminForm">
    <div id="j-main-container" class="span10">
		<?php
		echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));?>
		<?php if (empty($this->items)) : ?>
            <div class="alert alert-no-items">
				<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
		<?php else : ?>
            <table class="table table-striped" id="articleList">
                <thead>
                <tr>
                    <th width="1%" class="nowrap center hidden-phone">
		                <?php echo JHtml::_('searchtools.sort', '', 'datalayer.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                    </th>
                    <th width="1%" class="center">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
                    </th>
                    <th width="1%" class="nowrap center">
		                <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                    </th>
                    <th width="52%">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_PWTSEO_HEADING_TITLE', 'datalayer.title', $listDirn, $listOrder); ?>
                    </th>
                    <th width="15%">
						<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'datalayer.language', $listDirn, $listOrder); ?>
                    </th>
                    <th width="15%" class="nowrap hidden-phone">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_PWTSEO_HEADING_TEMPLATE', 'datalayer.template', $listDirn, $listOrder); ?>
                    </th>
                    <th width="2%" class="nowrap hidden-phone">
						<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'datalayer.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td colspan="7">
						<?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
                </tfoot>
                <tbody>
				<?php foreach ($this->items as $i => $item) :
					$ordering = ($listOrder == 'ordering');
					$canEdit = $user->authorise('core.edit', 'com_pwtseo');
					$canChange  = $user->authorise('core.edit.state', 'com_pwtseo.' . $item->id);
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
			                    $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::_('tooltipText', 'JORDERINGDISABLED');
		                    }
		                    ?>
                            <span class="sortable-handler<?php echo $iconClass ?>">
								<span class="icon-menu" aria-hidden="true"></span>
							</span>
		                    <?php if ($canChange && $saveOrder) : ?>
                                <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order" />
		                    <?php endif; ?>
                        </td>
                        <td class="center">
							<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td class="center">
                            <div class="btn-group">
			                    <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'datalayers.', $canChange, 'cb'); ?>
                            </div>
                        </td>
                        <td class="has-context">
                            <div class="pull-left">
								<?php if ($canEdit) : ?>
                                    <a href="<?php echo Route::_('index.php?option=com_pwtseo&task=datalayer.edit&id=' . (int) $item->id); ?>">
										<?php echo $this->escape($item->title); ?></a>
								<?php else : ?>
									<?php echo $this->escape($item->title); ?>
								<?php endif; ?>
                            </div>
                        </td>
                        <td>
	                        <?php echo LayoutHelper::render('joomla.content.language', $item); ?>
                        </td>
                        <td class="">
	                        <?php echo $item->template_title ? $this->escape(str_replace(',', ', ', $item->template_title)) : Text::_('JALL'); ?>
                        </td>
                        <td class="hidden-phone">
							<?php echo $item->id; ?>
                        </td>
                    </tr>
				<?php endforeach; ?>
                </tbody>
            </table>

		<?php endif; ?>
    </div>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
