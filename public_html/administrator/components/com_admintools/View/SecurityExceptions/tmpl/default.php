<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\AdminTools\Admin\Helper\Html;
use Akeeba\AdminTools\Admin\Helper\Select;
use Akeeba\AdminTools\Admin\Helper\Storage;
use FOF30\Utils\FEFHelper\Html as FEFHtml;

/** @var $this \Akeeba\AdminTools\Admin\View\SecurityExceptions\Html */

defined('_JEXEC') or die;

$js = FEFHtml::jsOrderingBackend($this->order);
$this->getContainer()->template->addJSInline($js);

echo $this->loadAnyTemplate('admin:com_admintools/BlacklistedAddresses/toomanyips_warning');
echo $this->loadAnyTemplate('admin:com_admintools/ControlPanel/needsipworkarounds', array(
        'returnurl' => base64_encode('index.php?option=com_admintools&view=SecurityExceptions')
));

?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-form">
	<section class="akeeba-panel--33-66 akeeba-filter-bar-container">
		<div class="akeeba-filter-bar akeeba-filter-bar--left akeeba-form-section akeeba-form--inline">
			<div class="akeeba-filter-element akeeba-form-group akeeba-filter-joomlacalendarfix">
				<?php echo \JHtml::_('calendar', $this->filters['from'], 'datefrom', 'datefrom', '%Y-%m-%d', array('class' => 'input-small')); ?>
			</div>

			<div class="akeeba-filter-element akeeba-form-group akeeba-filter-joomlacalendarfix">
				<?php echo \JHtml::_('calendar', $this->filters['to'], 'dateto', 'dateto', '%Y-%m-%d', array('class' => 'input-small')); ?>
			</div>

			<div class="akeeba-filter-element akeeba-form-group">
				<input type="text" name="ip" placeholder="<?php echo \JText::_('COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_IP'); ?>"
					   id="filter_ip" onchange="document.adminForm.submit();"
					   value="<?php echo $this->escape($this->filters['ip']); ?>"
					   title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_IP'); ?>"/>
			</div>

			<div class="akeeba-filter-element akeeba-form-group">
				<?php echo Select::reasons('reason', $this->filters['reason'], ['onchange' => 'document.adminForm.submit()'])?>
			</div>

			<div class="akeeba-filter-element akeeba-form-group">
				<button class="akeeba-btn--grey akeeba-btn--icon-only akeeba-btn--small akeeba-hidden-phone" onclick="this.form.submit();" title="<?php echo \JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
					<span class="akion-search"></span>
				</button>
			</div>
		</div>

		<?php echo FEFHtml::selectOrderingBackend($this->getPagination(), $this->sortFields, $this->order, $this->order_Dir)?>

	</section>

	<table class="akeeba-table akeeba-table--striped" id="itemsList">
		<thead>
		<tr>
			<th width="20px">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/>
			</th>
			<th style="width:17%">
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_LOGDATE', 'logdate', $this->order_Dir, $this->order, 'browse'); ?>
			</th>
			<th style="width:15%">
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_IP', 'ip', $this->order_Dir, $this->order, 'browse'); ?>
			</th>
			<th style="width: 15%">
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_REASON', 'reason', $this->order_Dir, $this->order, 'browse'); ?>
			</th>
			<th>
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_URL', 'url', $this->order_Dir, $this->order, 'browse'); ?>
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
					<?php echo JText::_('COM_ADMINTOOLS_ERR_BLACKLISTEDADDRESS_NOITEMS')?>
				</td>
			</tr>
		<?php endif;?>
		<?php
		if ($this->items):
			$i = 0;

			$cparams = Storage::getInstance();
			$iplink  = $cparams->getValue('iplookupscheme', 'http') . '://' . $cparams->getValue('iplookup', 'ip-lookup.net/index.php?ip={ip}');

			foreach($this->items as $row):
				$logdate = Html::localisedDate($row->logdate, 'Y-m-d H:i:s T', false);

				$reason = \JText::_('COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_REASON_' . strtoupper($row->reason));

				if ($row->extradata)
				{
					if (stristr($row->extradata, '|') === false)
					{
						$row->extradata .= '|';
					}

					list($moreinfo, $techurl) = explode('|', $row->extradata);

					$reason .= '&nbsp;'.\JHtml::_('tooltip', strip_tags(htmlspecialchars($moreinfo, ENT_COMPAT, 'UTF-8')), '', 'tooltip.png', '', $techurl);
				}

				$link = str_replace('{ip}', $row->ip, $iplink);

				$ip = '<a href="'.$link.'" target="_blank" class="akeeba-btn--small"><span class="akion-search"></span></a>&nbsp;';

				$token = $this->getContainer()->platform->getToken(true);

				if($row->block)
				{
					$ip .= '<a class="akeeba-btn--green--small" ';
					$ip .= 'href="index.php?option=com_admintools&view=SecurityExceptions&task=unban&id='.$row->id.'&'.$token.'=1" ';
					$ip .= 'title="'.\JText::_('COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_UNBAN').'">';
					$ip .= '<span class="akion-minus"></span>';
					$ip .= '</a>&nbsp;';
				}
				else
				{
					$ip .= '<a class="akeeba-btn--red--small" ';
					$ip .= 'href="index.php?option=com_admintools&view=SecurityExceptions&task=ban&id='.$row->id.'&'.$token.'=1" ';
					$ip .= 'title="'.\JText::_('COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_BAN').'">';
					$ip .= '<span class="akion-flag"></span>';
					$ip .= '</a>&nbsp;';
				}

				$ip .= htmlspecialchars($row->ip, ENT_COMPAT);
			?>
				<tr>
					<td><?php echo \JHtml::_('grid.id', ++$i, $row->id); ?></td>
					<td>
						<?php echo $logdate?>
					</td>
					<td>
						<?php echo $ip ?>
					</td>
					<td>
						<?php echo $reason ?>
					</td>
					<td>
						<?php echo $row->url ?>
					</td>
				</tr>
			<?php
			endforeach;
		endif; ?>
		</tbody>

	</table>

	<div class="akeeba-hidden-fields-container">
		<input type="hidden" name="option" id="option" value="com_admintools"/>
		<input type="hidden" name="view" id="view" value="SecurityExceptions"/>
		<input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
		<input type="hidden" name="task" id="task" value="browse"/>
		<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->escape($this->order); ?>"/>
		<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->escape($this->order_Dir); ?>"/>
		<input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
	</div>
</form>
