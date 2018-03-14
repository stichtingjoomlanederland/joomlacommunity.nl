<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

/** @var $this \Akeeba\AdminTools\Admin\View\ExceptionsFromWAF\Html */
use FOF30\Utils\FEFHelper\Html as FEFHtml;

$js = FEFHtml::jsOrderingBackend($this->order);
$this->getContainer()->template->addJSInline($js);

?>

<div id="admintools-whatsthis" class="akeeba-block--info">
    <p><?php echo JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_WHATSTHIS_LBLA') ?></p>
    <ul>
        <li><?php echo JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_WHATSTHIS_LBLB') ?></li>
        <li><?php echo JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_WHATSTHIS_LBLC') ?></li>
    </ul>
</div>

<?php
// Let's check if the system plugin is correctly installed AND published
echo $this->loadAnyTemplate('admin:com_admintools/ControlPanel/plugin_warning');

?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-form">

    <section class="akeeba-panel--33-66 akeeba-filter-bar-container">
        <div class="akeeba-filter-bar akeeba-filter-bar--left akeeba-form-section akeeba-form--inline">
            <div class="akeeba-filter-element akeeba-form-group">
                <input type="text" name="foption" placeholder="<?php echo \JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_OPTION'); ?>"
                       id="filter_foption" onchange="document.adminForm.submit();"
                       value="<?php echo $this->escape($this->filters['foption']); ?>"
                       title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_OPTION'); ?>"/>
            </div>

            <div class="akeeba-filter-element akeeba-form-group">
                <input type="text" name="fview" placeholder="<?php echo \JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_VIEW'); ?>"
                       id="filter_fview" onchange="document.adminForm.submit();"
                       value="<?php echo $this->escape($this->filters['fview']); ?>"
                       title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_VIEW'); ?>"/>
            </div>

            <div class="akeeba-filter-element akeeba-form-group">
                <input type="text" name="fquery" placeholder="<?php echo \JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_QUERY'); ?>"
                       id="filter_fquery" onchange="document.adminForm.submit();"
                       value="<?php echo $this->escape($this->filters['fquery']); ?>"
                       title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_QUERY'); ?>"/>
            </div>
        </div>

		<?php echo FEFHtml::selectOrderingBackend($this->getPagination(), $this->sortFields, $this->order, $this->order_Dir)?>

    </section>

    <table class="akeeba-table akeeba-table--striped" id="itemsList">
        <thead>
        <tr>
            <th width="32">
                <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/>
            </th>
            <th>
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_OPTION', 'foption', $this->order_Dir, $this->order, 'browse'); ?>
            </th>
            <th>
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_VIEW', 'fview', $this->order_Dir, $this->order, 'browse'); ?>
            </th>
            <th>
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_QUERY', 'fquery', $this->order_Dir, $this->order, 'browse'); ?>
            </th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan="11" class="center">
				<?php echo $this->pagination->getListFooter(); ?>
            </td>
        </tr>
        </tfoot>
        <tbody>
		<?php if (!count($this->items)):?>
            <tr>
                <td colspan="6">
					<?php echo JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_NOITEMS')?>
                </td>
            </tr>
		<?php endif;?>
		<?php
		if ($this->items):
			$i = 0;
			foreach($this->items as $row):
				$edit   = 'index.php?option=com_admintools&view=ExceptionsFromWAF&task=edit&id='.$row->id;
				$option = $row->option ? $row->option : JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_OPTION_ALL');
				$view   = $row->view ? $row->view : JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_VIEW_ALL');
				$query  = $row->query ? $row->query : JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_QUERY_ALL');

				$keepParams = $row->keepurlparams == 0 ? 'OFF' : ($row->keepurlparams == 1 ? 'ALL' : 'ADD');
				$enabled = $this->container->platform->getUser()->authorise('core.edit.state', 'com_admintools')
				?>
                <tr>
                    <td><?php echo \JHtml::_('grid.id', ++$i, $row->id); ?></td>
                    <td>
                        <a href="<?php echo $edit ?>">
							<?php echo $option ?>
                        </a>
                    </td>
                    <td>
                        <a href="<?php echo $edit ?>">
							<?php echo $view ?>
                        </a>
                    </td>
                    <td>
                        <a href="<?php echo $edit ?>">
							<?php echo $query ?>
                        </a>
                    </td>

                </tr>
			<?php
			endforeach;
		endif; ?>
        </tbody>

    </table>

    <div class="akeeba-hidden-fields-container">
        <input type="hidden" name="option" id="option" value="com_admintools"/>
        <input type="hidden" name="view" id="view" value="ExceptionsFromWAF"/>
        <input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
        <input type="hidden" name="task" id="task" value="browse"/>
        <input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->escape($this->order); ?>"/>
        <input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->escape($this->order_Dir); ?>"/>
        <input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
    </div>
</form>
