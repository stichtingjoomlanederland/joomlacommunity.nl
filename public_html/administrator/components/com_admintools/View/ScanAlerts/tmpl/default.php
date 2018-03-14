<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\AdminTools\Admin\Helper\Select;
use FOF30\Utils\FEFHelper\Html as FEFHtml;

/** @var $this \Akeeba\AdminTools\Admin\View\ScanAlerts\Html */

defined('_JEXEC') or die;

$scan_id   = $this->getModel()->getState('scan_id', '');

/** @var \Akeeba\AdminTools\Admin\Model\Scans $scanModel */
$scanModel = $this->getContainer()->factory->model('Scans')->tmpInstance();
$scanModel->find($scan_id);

$js = FEFHtml::jsOrderingBackend($this->order);
$js .= <<< JS

;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
// due to missing trailing semicolon and/or newline in their code.
akeeba.jQuery(document).ready(function($){
	$('#showComment').click(function(){
		$('#comment').toggle(400);
	});
});

JS;

$this->getContainer()->template->addJSInline($js);

?>
<div class="akeeba-container--50-50">
    <div></div>
    <div>
        <a href="index.php?option=com_admintools&view=Scans&task=edit&id=<?php echo $scan_id?>" class="akeeba-btn--green--small" style="float:right">
            <span class="icon-pencil"></span>
            <?php echo JText::_('COM_ADMINTOOLS_SCANALERTS_EDIT_COMMENT')?>
        </a>
        <span id="showComment" class="akeeba-btn--primary--small" style="margin-right: 10px;float:right">
            <span class="icon-comments icon-white"></span>
            <?php echo JText::_('COM_ADMINTOOLS_SCANALERTS_SHOWCOMMENT')?>
        </span>
        <div style="clear: both;"></div>
        <div id="comment" style="display:none">
            <p class="akeeba-panel--information" style="margin:5px 0 0">
				<?php echo nl2br($scanModel->comment);?>
            </p>
        </div>
    </div>
</div>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-form">

    <section class="akeeba-panel--33-66 akeeba-filter-bar-container">
        <div class="akeeba-filter-bar akeeba-filter-bar--left akeeba-form-section akeeba-form--inline">
            <div class="akeeba-filter-element akeeba-form-group">
                <?php echo Select::scanresultstatus('status', $this->filters['status'], ['onchange' => 'document.adminForm.submit()'])?>
            </div>

            <div class="akeeba-filter-element akeeba-form-group">
				<?php echo Select::markedsafe('acknowledged', $this->filters['acknowledged'], ['onchange' => 'document.adminForm.submit()'])?>
            </div>

            <div class="akeeba-filter-element akeeba-form-group">
                <input type="text" name="path" placeholder="<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_PATH'); ?>"
                       id="filter_path" onchange="document.adminForm.submit();"
                       value="<?php echo $this->escape($this->filters['path']); ?>"
                       title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_PATH'); ?>"/>
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
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_SCANALERTS_PATH', 'path', $this->order_Dir, $this->order, 'browse'); ?>
            </th>
            <th style="width: 100px">
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_SCANALERTS_STATUS', 'filestatus', $this->order_Dir, $this->order, 'browse'); ?>
            </th>
            <th style="width: 100px;">
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_SCANALERTS_THREAT_SCORE', 'threat_score', $this->order_Dir, $this->order, 'browse'); ?>
            </th>
            <th style="width: 100px;">
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_SCANALERTS_ACKNOWLEDGED', 'acknowledged', $this->order_Dir, $this->order, 'browse'); ?>
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
				if (strlen($row->path) > 100)
				{
					$truncatedPath = true;
					$path          = htmlspecialchars(substr($row->path, -100));
					$alt           = 'title="' . htmlspecialchars($row->path) . '"';
				}
				else
				{
					$truncatedPath = false;
					$path          = htmlspecialchars($row->path);
					$alt           = '';
				}

				$html_path  = $truncatedPath ? "&hellip;" : '';
				$html_path .= '<a href="index.php?option=com_admintools&view=ScanAlerts&task=edit&id='.$row->admintools_scanalert_id.'" '.$alt.'>';
				$html_path .= $path;
				$html_path .= '</a>';

				$extra_class= '';

				if(!$row->threat_score)
				{
					$extra_class = ' admintools-scanfile-nothreat';
				}

				if ($row->newfile)
				{
					$fstatus = 'new';
				}
                elseif ($row->suspicious)
				{
					$fstatus = 'suspicious';
				}
				else
				{
					$fstatus = 'modified';
				}

				$html_status = '<span class="admintools-scanfile-'.$fstatus.$extra_class.'">'.\JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_STATUS_' . $fstatus).'</span>';

				if ($row->threat_score == 0)
				{
					$threatindex = 'none';
				}
                elseif ($row->threat_score < 10)
				{
					$threatindex = 'low';
				}
                elseif ($row->threat_score < 100)
				{
					$threatindex = 'medium';
				}
				else
				{
					$threatindex = 'high';
				}

				$html_score  = '<span class="admintools-scanfile-threat-'.$threatindex.'">';
				$html_score .=    '<span class="admintools-scanfile-pic">&nbsp;</span>';
				$html_score .=    $row->threat_score;
				$html_score .= '</span>';

				$enabled = $this->container->platform->getUser()->authorise('core.edit.state', 'com_admintools')
            ?>
                <tr>
                    <td><?php echo \JHtml::_('grid.id', ++$i, $row->admintools_scanalert_id); ?></td>
                    <td>
                        <?php echo $html_path; ?>
                    </td>
                    <td>
                        <?php echo $html_status; ?>
                    </td>
                    <td>
                        <?php echo $html_score; ?>
                    </td>
                    <td>
						<?php echo JHTML::_('jgrid.published', $row->acknowledged, $i, '', $enabled, 'cb')?>
                    </td>
                </tr>
			<?php
			endforeach;
		endif; ?>
        </tbody>

    </table>

    <div class="akeeba-hidden-fields-container">
        <input type="hidden" name="option" id="option" value="com_admintools"/>
        <input type="hidden" name="view" id="view" value="ScanAlerts"/>
        <input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
        <input type="hidden" name="task" id="task" value="browse"/>
        <input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->escape($this->order); ?>"/>
        <input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->escape($this->order_Dir); ?>"/>
        <input type="hidden" name="scan_id" value="<?php echo $scan_id; ?>" />
        <input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
    </div>
</form>
