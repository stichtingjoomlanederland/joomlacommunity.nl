<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_menus
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('formbehavior.chosen');

$uri       = JUri::getInstance();
$return    = base64_encode($uri);
$user      = JFactory::getUser();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = ($listOrder == 'pwtsitemap_menu_types.ordering' && strtolower($listDirn) == 'asc');

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_pwtsitemap&task=menus.saveOrderAjax&tmpl=component';
	HTMLHelper::_('sortablelist.sortable', 'menuList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
}
?>
<form action="<?php echo Route::_('index.php?option=com_pwtsitemap&view=menus'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
    <div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
		<?php else : ?>
        <div id="j-main-container">
			<?php endif; ?>
			<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this, 'options' => array('filterButton' => false))); ?>
            <div class="clearfix"> </div>
			<?php if (empty($this->items)) : ?>
                <div class="alert alert-no-items">
					<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                </div>
			<?php else : ?>
                <table class="table table-striped" id="menuList">
                    <thead>
                    <tr>
                        <th width="1%" class="nowrap center hidden-phone">
		                    <?php echo HTMLHelper::_('searchtools.sort', '', 'pwtsitemap_menu_types.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                        </th>
                        <th>
							<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                        </th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <td colspan="15">
							<?php echo $this->pagination->getListFooter(); ?>
                        </td>
                    </tr>
                    </tfoot>
                    <tbody>
					<?php foreach ($this->items as $i => $item) :
                        $orderkey       = 1;
						$canEdit        = $user->authorise('core.edit',   'com_menus.menu.' . (int) $item->id);
						$canCheckin     = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $user->get('id')|| $item->checked_out == 0;
						$canChange      = $user->authorise('core.edit.state', 'com_menus.menu.' . $item->id) && $canCheckin;
						?>
                        <tr class="row<?php echo $i % 2; ?>">
                            <td class="order nowrap center hidden-phone">
		                        <?php
		                        $iconClass = '';

		                        if (!$canChange)
		                        {
			                        $iconClass = ' inactive';
		                        }
                                elseif (!$saveOrder)
		                        {
			                        $iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::_('tooltipText', 'JORDERINGDISABLED');
		                        }
		                        ?>
                                <span class="sortable-handler<?php echo $iconClass ?>">
                                        <span class="icon-menu"></span>
                                    </span>
		                        <?php if ($canChange && $saveOrder) : ?>
                                    <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $orderkey + 1; ?>" />
                                    <input type="checkbox" style="display:none" id="cb<?php echo $i; ?>" name="cid[]" size="5" value="<?php echo $item->id; ?>" />
		                        <?php endif; ?>

                            </td>
                            <td>
                                <a href="<?php echo Route::_('index.php?option=com_pwtsitemap&view=items&filter[menutype]=' . $item->menutype); ?>">
                                    <?php echo $this->escape($item->title); ?></a>
                                <div class="small">
									<?php echo Text::_('COM_PWTSITEMAP_MENU_MENUTYPE_LABEL'); ?>:
									<?php if ($canEdit) : ?>
                                        <a href="<?php echo Route::_('index.php?option=com_menus&task=menu.edit&id=' . $item->id); ?>" title="<?php echo $this->escape($item->description); ?>">
											<?php echo $this->escape($item->menutype); ?></a>
									<?php else : ?>
										<?php echo $this->escape($item->menutype); ?>
									<?php endif; ?>
                                </div>
                            </td>
                        </tr>
					<?php endforeach; ?>
                    </tbody>
                </table>
			<?php endif; ?>

            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
			<?php echo HTMLHelper::_('form.token'); ?>
        </div>
</form>
