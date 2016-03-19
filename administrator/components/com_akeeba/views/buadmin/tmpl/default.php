<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 *
 * @since     1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var  AkeebaViewBuadmin  $this */

JHtml::_('behavior.calendar');
JHtml::_('behavior.modal');
JHtml::_('bootstrap.popover', '.akeebaCommentPopover', array(
	'animation'	=> true,
	'html'		=> true,
	'title'		=> JText::_('STATS_LABEL_COMMENT'),
	'placement'	=> 'bottom'
));

JFactory::getDocument()->addStyleSheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css');

$dateFormat = \Akeeba\Engine\Util\Comconfig::getValue('dateformat', '');
$dateFormat = trim($dateFormat);
$dateFormat = !empty($dateFormat) ? $dateFormat : JText::_('DATE_FORMAT_LC4');

// Filesize formatting function by eregon at msn dot com
// Published at: http://www.php.net/manual/en/function.number-format.php
function format_filesize($number, $decimals = 2, $force_unit = false, $dec_char = '.', $thousands_char = '')
{
	if ($number <= 0)
	{
		return '-';
	}

	$units = array('b', 'Kb', 'Mb', 'Gb', 'Tb');
	if ($force_unit === false)
	{
		$unit = floor(log($number, 2) / 10);
	}
	else
	{
		$unit = $force_unit;
	}
	if ($unit == 0)
	{
		$decimals = 0;
	}

	return number_format($number / pow(1024, $unit), $decimals, $dec_char, $thousands_char) . ' ' . $units[$unit];
}

// Load a mapping of backup types to textual representation
$scripting = \Akeeba\Engine\Factory::getEngineParamsProvider()->loadScripting();
$backup_types = array();
foreach ($scripting['scripts'] as $key => $data)
{
	$backup_types[$key] = JText::_($data['text']);
}

?>

<script type="text/javascript">
	Joomla.orderTable = function () {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $this->escape($this->lists->order); ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>

<?php
// Restoration information prompt
$proKey = (defined('AKEEBA_PRO') && AKEEBA_PRO) ? 'PRO' : 'CORE';
if (\Akeeba\Engine\Platform::getInstance()->get_platform_configuration_option('show_howtorestoremodal', 1) && version_compare(JVERSION, '3.0.0', 'ge')):
	echo $this->loadAnyTemplate('admin:com_akeeba/buadmin/howtorestore_modal');
else:
?>
<div class="alert alert-info">
	<button class="close" data-dismiss="alert">×</button>
	<h4 class="alert-heading"><?php echo JText::_('BUADMIN_LABEL_HOWDOIRESTORE_LEGEND') ?></h4>

	<?php echo JText::sprintf('COM_AKEEBA_BUADMIN_LABEL_HOWDOIRESTORE_TEXT_' . $proKey,
			'https://www.akeebabackup.com/videos/1212-akeeba-backup-core/1618-abtc04-restore-site-new-server.html',
			'index.php?option=com_akeeba&view=transfer'); ?>
</div>
<?php endif; ?>

<div id="j-main-container">
<form action="index.php" method="post" name="adminForm" id="adminForm">
<input type="hidden" name="option" id="option" value="com_akeeba"/>
<input type="hidden" name="view" id="view" value="buadmins"/>
<input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
<input type="hidden" name="task" id="task" value="default"/>
<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->lists->order ?>"/>
<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->lists->order_Dir ?>"/>
<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken() ?>" value="1"/>

<?php
	// Construct the array of sorting fields
	$sortFields = array(
	'id'          => JText::_('STATS_LABEL_ID'),
	'description' => JText::_('STATS_LABEL_DESCRIPTION'),
	'backupstart' => JText::_('STATS_LABEL_START'),
	'profile_id'  => JText::_('STATS_LABEL_PROFILEID'),
	);
	JHtml::_('formbehavior.chosen', 'select');

	?>
	<div id="filter-bar" class="btn-toolbar">
		<div class="filter-search btn-group pull-left">
			<input type="text" name="description" placeholder="<?php echo JText::_('STATS_LABEL_DESCRIPTION'); ?>"
			       id="filter_description"
			       value="<?php echo $this->escape($this->getModel()->getState('description', '')); ?>"
			       title="<?php echo JText::_('STATS_LABEL_DESCRIPTION'); ?>"/>
		</div>
		<div class="btn-group pull-left hidden-phone">
			<button class="btn" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i
					class="icon-search"></i></button>
			<button class="btn" type="button"
			        onclick="document.id('filter_description').value='';this.form.submit();"
			        title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
		</div>

		<div class="filter-search btn-group pull-left hidden-phone">
			<?php echo JHTML::_('calendar', $this->lists->fltFrom, 'from', 'from', '%Y-%m-%d', array('class' => 'input-small')); ?>
		</div>
		<div class="filter-search btn-group pull-left hidden-phone">
			<?php echo JHTML::_('calendar', $this->lists->fltTo, 'to', 'to', '%Y-%m-%d', array('class' => 'input-small')); ?>
		</div>
		<div class="btn-group pull-left hidden-phone">
			<button class="btn" type="button" onclick="this.form.submit(); return false;"
			        title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
		</div>
		<div class="btn-group pull-left hidden-phone">
			<?php echo JHTML::_('select.genericlist', $this->profilesList, 'profile', 'onchange="document.forms.adminForm.submit()" class="advancedSelect"', 'value', 'text', $this->lists->fltProfile); ?>
		</div>

		<?php if (version_compare(JVERSION, '3.0.0', 'ge')): ?>
		<div class="btn-group pull-right">
			<label for="limit"
			       class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
		<div class="btn-group pull-right hidden-phone">
			<label for="directionTable"
			       class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></label>
			<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
				<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
				<option
					value="asc" <?php if ($this->lists->order_Dir == 'asc')
				{
					echo 'selected="selected"';
				} ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
				<option
					value="desc" <?php if ($this->lists->order_Dir == 'desc')
				{
					echo 'selected="selected"';
				} ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING'); ?></option>
			</select>
		</div>
		<div class="btn-group pull-right">
			<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY'); ?></label>
			<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
				<option value=""><?php echo JText::_('JGLOBAL_SORT_BY'); ?></option>
				<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $this->lists->order); ?>
			</select>
		</div>
		<?php endif; ?>
	</div>

<table class="table table-striped" id="itemsList">
<thead>
<tr>
	<th width="20"><input type="checkbox" name="toggle" value=""
						  onclick="Joomla.checkAll(this);"/></th>
	<th width="20" class="hidden-phone">
		<?php echo JHTML::_('grid.sort', 'STATS_LABEL_ID', 'id', $this->lists->order_Dir, $this->lists->order, 'default'); ?>
	</th>
	<th>
		<?php echo JHTML::_('grid.sort', 'STATS_LABEL_DESCRIPTION', 'description', $this->lists->order_Dir, $this->lists->order, 'default'); ?>
	</th>
	<th  class="hidden-phone">
		<?php echo JHTML::_('grid.sort', 'STATS_LABEL_PROFILEID', 'profile_id', $this->lists->order_Dir, $this->lists->order, 'default'); ?>
	</th>
	<th width="5%">
		<?php echo JText::_('STATS_LABEL_DURATION') ?>
	</th>
	<th width="40">
		<?php echo JText::_('STATS_LABEL_STATUS'); ?>
	</th>
	<th width="80" class="hidden-phone">
		<?php echo JText::_('STATS_LABEL_SIZE'); ?>
	</th>
	<th class="hidden-phone">
		<?php echo JText::_('STATS_LABEL_MANAGEANDDL'); ?>
	</th>
</tr>

</thead>
<tfoot>
<tr>
	<td colspan="11" class="center"><?php echo $this->pagination->getListFooter(); ?></td>
</tr>
</tfoot>
<tbody>
<?php if (!empty($this->list)): ?>
	<?php $id = 1;
	$i = 0; ?>
	<?php foreach ($this->list as $record): ?>
		<?php
		$id = 1 - $id;
		$check = JHTML::_('grid.id', ++$i, $record['id']);

		$backupId = isset($record['backupid']) ? $record['backupid'] : '';
		$originLanguageKey = 'STATS_LABEL_ORIGIN_' . strtoupper($record['origin']);
		$originDescription = JText::_($originLanguageKey);

		switch (strtolower($record['origin']))
		{
			case 'backend':
				$originIcon = 'fa-desktop';
				break;

			case 'frontend':
				$originIcon = 'fa-globe';
				break;

			case 'json':
				$originIcon = 'fa-cloud';
				break;

			case 'cli':
				$originIcon = 'fa-keyboard-o';
				break;

			case 'xmlrpc':
				$originIcon = 'fa-code';
				break;

			case 'restorepoint':
				$originIcon = 'fa-refresh';
				break;

			case 'lazy':
				$originIcon = 'fa-joomla';
				break;

			default:
				$originIcon = 'fa-question';
				break;
		}

		if (empty($originLanguageKey) || ($originDescription == $originLanguageKey))
		{
			$originDescription = '&ndash;';
			$originIcon = 'fa-question';
		}

		if (array_key_exists($record['type'], $backup_types))
		{
			$type = $backup_types[$record['type']];
		}
		else
		{
			$type = '&ndash;';
		}

		JLoader::import('joomla.utilities.date');
		$startTime = new JDate($record['backupstart']);
		$endTime = new JDate($record['backupend']);

		$duration = $endTime->toUnix() - $startTime->toUnix();
		if ($duration > 0)
		{
			$seconds = $duration % 60;
			$duration = $duration - $seconds;

			$minutes = ($duration % 3600) / 60;
			$duration = $duration - $minutes * 60;

			$hours = $duration / 3600;
			$duration = sprintf('%02d', $hours) . ':' . sprintf('%02d', $minutes) . ':' . sprintf('%02d', $seconds);
		}
		else
		{
			$duration = '';
		}

		$user = JFactory::getUser();
		$userTZ = $user->getParam('timezone', 'UTC');
		$tz = new DateTimeZone($userTZ);
		$startTime->setTimezone($tz);

		// Link for Show Comments lightbox
		$info_link = "";

		if (!empty($record['comment']))
		{
			$info_link = "<span class=\"icon icon-question-sign akeebaCommentPopover\" rel=\"popover\" data-content=\"" .
			             $this->escape($record['comment']) ."\"></span>";
		}

		// Label class based on status
		$status = JText::_('STATS_LABEL_STATUS_' . $record['meta']);
		$statusClass = '';
		switch ($record['meta'])
		{
			case 'ok':
				$statusIcon = 'fa-check';
				$statusClass = 'label-success';
				break;
			case 'pending':
				$statusIcon = 'fa-play-circle-o';
				$statusClass = 'label-warning';
				break;
			case 'fail':
				$statusIcon = 'fa-times';
				$statusClass = 'label-important';
				break;
			case 'remote':
				$statusIcon = 'fa-cloud';
				$statusClass = 'label-info';
				break;
			default:
				$statusIcon = 'fa-trash-o';
				break;
		}

		$edit_link = JUri::base() . 'index.php?option=com_akeeba&view=buadmin&task=showcomment&id=' . $record['id'];

		if (empty($record['description']))
		{
			$record['description'] = JText::_('STATS_LABEL_NODESCRIPTION');
		}
		?>
		<tr class="row<?php echo $id; ?>">
			<td><?php echo $check; ?></td>
			<td class="hidden-phone">
				<?php echo $record['id']; ?>
			</td>
			<td>
				<span class="fa fa-fw <?php echo $originIcon ?> akeebaCommentPopover" rel="popover"
					  title="<?php echo JText::_('STATS_LABEL_ORIGIN'); ?>"
					data-content="<?php echo htmlentities($originDescription) ?>"
					></span>
				<?php echo $info_link ?>
				<a href="<?php echo $edit_link; ?>"><?php echo $this->escape($record['description']) ?></a>
				<br/>
				<div class="akeeba-buadmin-startdate" title="<?php echo JText::_('STATS_LABEL_START') ?>">
					<small>
						<span class="fa fa-fw fa-calendar"></span>
						<?php echo $startTime->format($dateFormat); ?>
					</small>
				</div>
			</td>
			<td class="hidden-phone">
				<?php
				$profileName = '&mdash;';

				if (isset($this->profiles[$record['profile_id']]))
				{
					$profileName = $this->escape($this->profiles[$record['profile_id']]->description);
				}
				?>
				#<?php echo $record['profile_id'] ?>. <?php echo $profileName ?>
				<br/>
				<small>
					<em><?php echo $type ?></em>
				</small>
			</td>
			<td>
				<?php echo $duration; ?>
			</td>
			<td>
				<span class="label <?php echo $statusClass; ?> akeebaCommentPopover" rel="popover"
					  title="<?php echo JText::_('STATS_LABEL_STATUS')?>"
					  data-content="<?php echo $status ?>"
					>
					<span class="fa fa-fw <?php echo $statusIcon; ?>"></span>
				</span>
			</td>
			<td class="hidden-phone"><?php echo ($record['meta'] == 'ok') ? format_filesize($record['size']) : ($record['total_size'] > 0 ? "(<i>" . format_filesize($record['total_size']) . "</i>)" : '&mdash;') ?></td>
			<td class="hidden-phone">
				<?php echo $this->loadAnyTemplate('admin:com_akeeba/buadmin/manage_column', array(
					'record' => &$record,
				)); ?>
			</td>
		</tr>
	<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
</form>
</div>