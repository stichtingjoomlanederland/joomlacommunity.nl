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

HTMLHelper::_('stylesheet', 'com_pwtseo/pwtseo.css', array('version' => 'auto', 'relative' => true));

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

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
            <table class="table table-striped" id="bannerList">
                <thead>
                <tr>
                    <th width="1%" class="center">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
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
                    <td colspan="5">
						<?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
                </tfoot>
                <tbody>
				<?php foreach ($this->items as $i => $item) :
					$ordering = ($listOrder == 'ordering');
					$canEdit = $user->authorise('core.edit', 'com_pwtseo');
					?>
                    <tr>
                        <td class="center">
							<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
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
	                        <?php echo $item->template_title ? $this->escape($item->template_title) : Text::_('JALL'); ?>
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
