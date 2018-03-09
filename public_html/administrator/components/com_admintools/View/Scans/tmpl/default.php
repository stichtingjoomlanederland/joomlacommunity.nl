<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var \Akeeba\AdminTools\Admin\View\Scans\Html $this */
use FOF30\Utils\FEFHelper\Html as FEFHtml;

defined('_JEXEC') or die;

$js = FEFHtml::jsOrderingBackend($this->order);
$this->getContainer()->template->addJSInline($js);
?>

<div>
	<a class="akeeba-btn--primary--small" href="index.php?option=com_admintools&view=Scanner">
		<span class="akion-gear-b"></span>
		<?php echo JText::_('COM_ADMINTOOLS_LBL_SCAN_CONFIGURE'); ?>
	</a>
    <span>
        <?php echo JText::_('COM_ADMINTOOLS_MSG_SCAN_CONFIGUREHELP'); ?>
    </span>
</div>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-form">

    <section class="akeeba-panel--33-66 akeeba-filter-bar-container">

        <?php echo FEFHtml::selectOrderingBackend($this->getPagination(), $this->sortFields, $this->order, $this->order_Dir)?>

    </section>

    <table class="akeeba-table akeeba-table--striped" id="itemsList">
        <thead>
        <tr>
            <th width="32">
                <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/>
            </th>
            <th>
				<?php echo \JHtml::_('grid.sort', '#', 'id', $this->order_Dir, $this->order, 'browse'); ?>
            </th>
            <th>
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_SCAN_START', 'backupstart', $this->order_Dir, $this->order, 'browse'); ?>
            </th>
            <th>
				<?php echo JText::_('COM_ADMINTOOLS_LBL_SCAN_TOTAL'); ?>
            </th>
            <th>
				<?php echo JText::_('COM_ADMINTOOLS_LBL_SCAN_MODIFIED'); ?>
            </th>
            <th>
				<?php echo JText::_('COM_ADMINTOOLS_LBL_SCAN_THREATNONZERO'); ?>
            </th>
            <th>
				<?php echo JText::_('COM_ADMINTOOLS_LBL_SCAN_ADDED'); ?>
            </th>
            <th>
				<?php echo JText::_('COM_ADMINTOOLS_LBL_SCAN_ACTIONS'); ?>
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
                <td colspan="10">
					<?php echo JText::_('COM_ADMINTOOLS_MSG_COMMON_NOITEMS')?>
                </td>
            </tr>
		<?php endif;?>
		<?php
		if ($this->items):
			$i = 0;
			foreach($this->items as $row):
                $actions = '';

				if($row->files_modified + $row->files_new + $row->files_suspicious)
				{
					$actions  = '<a class="akeeba-btn--primary--small" href="index.php?option=com_admintools&view=ScanAlerts&scan_id='.$row->id.'">';
					$actions .= JText::_('COM_ADMINTOOLS_LBL_SCAN_ACTIONS_VIEW').'</a>';
				}
            ?>
                <tr>
                    <td><?php echo \JHtml::_('grid.id', ++$i, $row->id); ?></td>
                    <td>
                        <?php echo $row->id; ?>
                    </td>
                    <td>
                        <?php echo $row->backupstart; ?>
                    </td>
                    <td>
                        <?php echo $row->multipart; ?>
                    </td>
                    <td>
                        <span class="admintools-files-<?php echo $row->files_modified ? 'alert' : 'noalert'?>">
                            <?php echo $row->files_modified?>
                        </span>
                    </td>
                    <td>
                        <span class="admintools-files-<?php echo $row->files_suspicious ? 'alert' : 'noalert'?>">
                            <?php echo $row->files_suspicious?>
                        </span>
                    </td>
                    <td>
                        <span class="admintools-files-<?php echo $row->files_new ? 'alert' : 'noalert'?>">
                            <?php echo $row->files_new?>
                        </span>
                    </td>
                    <td>
                        <?php echo $actions;?>
                    </td>
                </tr>
			<?php
			endforeach;
		endif; ?>
        </tbody>

    </table>

    <div class="akeeba-hidden-fields-container">
        <input type="hidden" name="option" id="option" value="com_admintools"/>
        <input type="hidden" name="view" id="view" value="Scans"/>
        <input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
        <input type="hidden" name="task" id="task" value="browse"/>
        <input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->escape($this->order); ?>"/>
        <input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->escape($this->order_Dir); ?>"/>
        <input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
    </div>
</form>

<div id="admintools-scan-dim" style="display: none">
	<div id="admintools-scan-container">
		<p>
			<?php echo JText::_('COM_ADMINTOOLS_MSG_SCAN_PLEASEWAIT') ?><br/>
			<?php echo JText::_('COM_ADMINTOOLS_MSG_SCAN_SCANINPROGRESS') ?>
		</p>

		<p>
			<progress></progress>
		</p>
		<p>
			<span id="admintools-lastupdate-text" class="lastupdate"></span>
		</p>
	</div>
</div>
