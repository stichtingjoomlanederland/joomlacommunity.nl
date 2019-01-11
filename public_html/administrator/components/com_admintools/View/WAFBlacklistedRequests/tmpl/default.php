<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\AdminTools\Admin\Helper\Select;
use FOF30\Utils\FEFHelper\Html as FEFHtml;

/** @var $this \Akeeba\AdminTools\Admin\View\WAFBlacklistedRequests\Html */

defined('_JEXEC') or die;

$js = FEFHtml::jsOrderingBackend($this->order);
$this->getContainer()->template->addJSInline($js);

// Let's check if the system plugin is correctly installed AND published
echo $this->loadAnyTemplate('admin:com_admintools/ControlPanel/plugin_warning');

//echo $this->getRenderedForm();
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-form">

	<section class="akeeba-panel--33-66 akeeba-filter-bar-container">
		<div class="akeeba-filter-bar akeeba-filter-bar--left akeeba-form-section akeeba-form--inline">
            <div class="akeeba-filter-element akeeba-form-group">
				<?php echo Select::wafApplication('application', ['onchange' => 'document.adminForm.submit()'], $this->filters['application'])?>
            </div>
			<div class="akeeba-filter-element akeeba-form-group">
				<?php echo Select::httpVerbs('fverb', ['onchange' => 'document.adminForm.submit()'], $this->filters['fverb'])?>
			</div>
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
                <input type="text" name="ftask" placeholder="<?php echo \JText::_('COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_TASK'); ?>"
                       id="filter_ftask" onchange="document.adminForm.submit();"
                       value="<?php echo $this->escape($this->filters['ftask']); ?>"
                       title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_TASK'); ?>"/>
            </div>

			<div class="akeeba-filter-element akeeba-form-group">
				<input type="text" name="fquery" placeholder="<?php echo \JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_QUERY'); ?>"
					   id="filter_fquery" onchange="document.adminForm.submit();"
					   value="<?php echo $this->escape($this->filters['fquery']); ?>"
					   title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_QUERY'); ?>"/>
			</div>

            <div class="akeeba-filter-element akeeba-form-group">
                <input type="text" name="fquery_content" placeholder="<?php echo \JText::_('COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_QUERY_CONTENT'); ?>"
                       id="filter_fquery_content" onchange="document.adminForm.submit();"
                       value="<?php echo $this->escape($this->filters['fquery_content']); ?>"
                       title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_QUERY_CONTENT'); ?>"/>
            </div>

            <div class="akeeba-filter-element akeeba-form-group">
				<?php echo Select::published($this->filters['published'], 'enabled', ['onchange' => 'document.adminForm.submit()'])?>
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
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_VERB', 'fverb', $this->order_Dir, $this->order, 'browse'); ?>
            </th>
			<th>
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_OPTION', 'foption', $this->order_Dir, $this->order, 'browse'); ?>
			</th>
			<th>
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_VIEW', 'fview', $this->order_Dir, $this->order, 'browse'); ?>
			</th>
			<th>
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_TASK', 'ftask', $this->order_Dir, $this->order, 'browse'); ?>
			</th>

            <th>
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_QUERY', 'fquery', $this->order_Dir, $this->order, 'browse'); ?>
			</th>
            <th>
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_QUERY_CONTENT', 'fquery_content', $this->order_Dir, $this->order, 'browse'); ?>
			</th>
            <th>
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_APPLICATION', 'application', $this->order_Dir, $this->order, 'browse'); ?>
			</th>
            <th>
				<?php echo \JHtml::_('grid.sort', 'JPUBLISHED', 'published', $this->order_Dir, $this->order, 'browse'); ?>
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
				<td colspan="9">
					<?php echo JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_NOITEMS')?>
				</td>
			</tr>
		<?php endif;?>
		<?php
		if ($this->items):
			$i = 0;
			foreach($this->items as $row):
				$edit    = 'index.php?option=com_admintools&view=WAFBlacklistedRequests&task=edit&id='.$row->id;
				$verb    = $row->verb ? $row->verb : JText::_('COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_ALL');
				$option  = $row->option ? $row->option : JText::_('COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_ALL');
				$view    = $row->view ? $row->view : JText::_('COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_ALL');
				$task    = $row->task ? $row->task : JText::_('COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_ALL');
				$query   = $row->query ? $row->query : JText::_('COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_ALL');
				$query_c = $row->query_content ? $row->query_content : JText::_('COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_ALL');

				switch ($row->application)
                {
                    case "site":
                        $application = JText::_('COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_APPLICATION_SITE');
                        break;
                    case "admin":
                        $application = JText::_('COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_APPLICATION_ADMIN');
                        break;
                    default:
                        $application = JText::_('COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_APPLICATION_BOTH');
                }

				$enabled = $this->container->platform->getUser()->authorise('core.edit.state', 'com_admintools')
				?>
				<tr>
					<td><?php echo \JHtml::_('grid.id', ++$i, $row->id); ?></td>
					<td>
						<a href="<?php echo $edit ?>">
							<?php echo $verb ?>
						</a>
					</td>
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
							<?php echo $task ?>
						</a>
					</td>
					<td>
						<a href="<?php echo $edit ?>">
							<?php echo $query ?>
						</a>
					</td>
                    <td>
                        <a href="<?php echo $edit ?>">
							<?php echo $query_c ?>
                        </a>
                    </td>
                    <td>
                        <a href="<?php echo $edit ?>">
							<?php echo $application ?>
                        </a>
                    </td>
                    <td>
						<?php echo JHTML::_('jgrid.published', $row->enabled, $i, '', $enabled, 'cb')?>
                    </td>
				</tr>
			<?php
			endforeach;
		endif; ?>
		</tbody>

	</table>

	<div class="akeeba-hidden-fields-container">
		<input type="hidden" name="option" id="option" value="com_admintools"/>
		<input type="hidden" name="view" id="view" value="WAFBlacklistedRequests"/>
		<input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
		<input type="hidden" name="task" id="task" value="browse"/>
		<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->escape($this->order); ?>"/>
		<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->escape($this->order_Dir); ?>"/>
		<input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
	</div>
</form>
