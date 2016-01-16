<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.keepalive'); ?>

<?php 
	$rseClass = $this->items['rsevents'] ? ' btn btn-success' : ' btn btn-danger '.rseventsproHelper::tooltipClass();
	$rseClick = $this->items['rsevents'] ? 'onclick="Joomla.submitbutton(\'imports.rsevents\');"' : '';
	$rseTitle = !$this->items['rsevents'] ? 'title="'.rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_IMPORT_RSEVENTS_MISSING')).'"' : '';
	$rseico	  = !$this->items['rsevents'] ? '<i class="fa fa-times"></i>' : '<i class="fa fa-check"></i>';
	$jevClass = $this->items['jevents'] ? ' btn btn-success' : ' btn btn-danger '.rseventsproHelper::tooltipClass();
	$jevClick = $this->items['jevents'] ? 'onclick="Joomla.submitbutton(\'imports.jevents\');"' : '';
	$jevTitle = !$this->items['jevents'] ? 'title="'.rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_IMPORT_JEVENTS_MISSING')).'"' : '';
	$jevico	  = !$this->items['jevents'] ? '<i class="fa fa-times"></i>' : '<i class="fa fa-check"></i>';
	$evlClass = $this->items['eventlist'] ? ' btn btn-success' : ' btn btn-danger '.rseventsproHelper::tooltipClass();
	$evlClick = $this->items['eventlist'] ? 'onclick="Joomla.submitbutton(\'imports.eventlist\');"' : '';
	$evlTitle = !$this->items['eventlist'] ? 'title="'.rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_IMPORT_EVENTLIST_MISSING')).'"' : '';
	$evbClass = $this->items['eventlistbeta'] ? ' btn btn-success' : ' btn btn-danger '.rseventsproHelper::tooltipClass();
	$evbClick = $this->items['eventlistbeta'] ? 'onclick="Joomla.submitbutton(\'imports.eventlistbeta\');"' : '';
	$evbTitle = !$this->items['eventlistbeta'] ? 'title="'.rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_IMPORT_EVENTLIST_MISSING')).'"' : '';
	$jclClass = $this->items['jcalpro'] ? ' btn btn-success' : ' btn btn-danger '.rseventsproHelper::tooltipClass();
	$jclClick = $this->items['jcalpro'] ? 'onclick="Joomla.submitbutton(\'imports.jcalpro\');"' : '';
	$jclTitle = !$this->items['jcalpro'] ? 'title="'.rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_IMPORT_JCALPRO_MISSING')).'"' : '';
	$jclico	  = !$this->items['jcalpro'] ? '<i class="fa fa-times"></i>' : '<i class="fa fa-check"></i>';
	$ohaClass = $this->items['ohanah'] ? ' btn btn-success' : ' btn btn-danger '.rseventsproHelper::tooltipClass();
	$ohaClick = $this->items['ohanah'] ? 'onclick="Joomla.submitbutton(\'imports.ohanah\');"' : '';
	$ohaTitle = !$this->items['ohanah'] ? 'title="'.rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_IMPORT_OHANAH_MISSING')).'"' : '';
	$ohaico	  = !$this->items['ohanah'] ? '<i class="fa fa-times"></i>' : '<i class="fa fa-check"></i>';
?>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=imports'); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal" enctype="multipart/form-data">
	<div class="row-fluid">
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
			<fieldset>
				<legend><?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_COMPONENTS'); ?></legend>
				<table class="table table-striped adminlist">
					<tr>
						<td>
							<a href="javascript:void(0)" <?php echo $rseTitle; ?> <?php echo $rseClick; ?> class="rs_import_link<?php echo $rseClass; ?>"><?php echo $rseico; ?> <?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_RSEVENTS'); ?></a>
						</td>
					</tr>
					<tr>
						<td>
							<a href="javascript:void(0)" <?php echo $jevTitle; ?> <?php echo $jevClick; ?> class="rs_import_link<?php echo $jevClass; ?>"><?php echo $jevico; ?> <?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_JEVENTS'); ?></a>
						</td>
					</tr>
					<?php 
					/*
					<tr>
						<td>
							<a href="javascript:void(0)" <?php echo $evlTitle; ?> <?php echo $evlClick; ?> class="rs_import_link<?php echo $evlClass; ?>"><?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_EVENTLIST'); ?></a>
							&nbsp; | &nbsp;
							<a href="javascript:void(0)" <?php echo $evbTitle; ?> <?php echo $evbClick; ?> class="rs_import_link<?php echo $evbClass; ?>"><?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_EVENTLISTBETA'); ?></a> 
						</td>
					</tr>
					*/ ?>
					<tr>
						<td>
							<a href="javascript:void(0)" <?php echo $jclTitle; ?> <?php echo $jclClick; ?> class="rs_import_link<?php echo $jclClass; ?>"><?php echo $jclico; ?> <?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_JCALPRO'); ?></a>
						</td>
					</tr>
					<tr>
						<td>
							<a href="javascript:void(0)" <?php echo $ohaTitle; ?> <?php echo $ohaClick; ?> class="rs_import_link<?php echo $ohaClass; ?>"><?php echo $ohaico; ?> <?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_OHANAH'); ?></a>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_('COM_RSEVENTSPRO_IMPORT_ADJUST_TIMES'); ?> 
							<select id="offset" style="float:none;" size="1" name="offset">
								<?php echo JHtml::_('select.options', $this->offsets, 'value', 'text', 0); ?>
							</select>
							<?php echo JText::_('COM_RSEVENTSPRO_IMPORT_ADJUST_TIMES_HOURS'); ?>
						</td>
					</tr>
					</table>
			</fieldset>
			
			<br />
			
			<fieldset>
				<legend><?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_FILE'); ?></legend>
				<table width="100%" cellpadding="5" cellspacing="0" border="0" class="adminlist">
					<tr>
						<td colspan="2">
							<div>
								<?php $now = rseventsproHelper::showdate('now','Y-m-d H:i:s'); ?>
								<?php $date = JFactory::getDate($now); ?>
								<?php $date->modify('+2 days'); ?>
								<?php $endone = $date->format('Y-m-d H:i:s'); ?>
								<?php $date->modify('+1 days'); ?>
								<?php $endtwo = $date->format('Y-m-d H:i:s'); ?>
								<?php $endthree = '0000-00-00 00:00:00'; ?>
								<b>Event Name, Start Date, End Date, Event Description, Event URL, Event Email, Event Phone, Location Name, Location Address, Category Name, Category Description, Tags</b>
								<hr />
								<b>"First event name","<?php echo $now; ?>","<?php echo $endone; ?>","Event Description","Event URL","Event Email","Event Phone","Location Name","Location address","Category name","Category description","tag1|tag2|tag3"</b>
								<br />
								<b>"Second event name","<?php echo $now; ?>","<?php echo $endtwo; ?>","Event Description","Event URL","Event Email","Event Phone","Location Name","Location address","Category name","Category description","tag1|tag2|tag3"</b>
								<br />
								<b>"Third event name","<?php echo $now; ?>","<?php echo $endthree; ?>","Event Description","Event URL","Event Email","Event Phone","Location Name","Location address","Category name","Category description","tag1|tag2|tag3"</b>
								<br />
								<b>"Event with multiple categories","<?php echo $now; ?>","<?php echo $endthree; ?>","Event Description","Event URL","Event Email","Event Phone","Location Name","Location address","First category|Second category|Third category","First category description|Second description|Third description","tag1|tag2|tag3"</b>
							</div>
						</td>
					</tr>
					<tr>
						<td width="10%"><?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_CSV'); ?></td>
						<td>
							<input type="file" name="events" size="50" />
						</td>
					</tr>
					<tr>
						<td width="10%">
							<label for="category" class="<?php echo rseventsproHelper::tooltipClass(); ?>" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_IMPORT_FROM_CSV_CATEGORIES_DESC')); ?>"><?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_CSV_CATEGORIES'); ?></label>
						</td>
						<td>
							<select id="category" name="category">
								<option value="0"><?php echo JText::_('COM_RSEVENTSPRO_IMPORT_CATEGORY_DEFAULT'); ?></option>
								<?php echo JHtml::_('select.options', JHtml::_('category.options','com_rseventspro',array(1))); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td width="10%">
							<label for="location" class="<?php echo rseventsproHelper::tooltipClass(); ?>" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_IMPORT_FROM_CSV_LOCATION_DESC')); ?>"><?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_CSV_LOCATION'); ?></label>
						</td>
						<td>
							<select id="location" size="1" name="location">
								<?php echo JHtml::_('select.options', $this->locations); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td width="10%">
							<label for="dateformat" class="<?php echo rseventsproHelper::tooltipClass(); ?>" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_IMPORT_FROM_CSV_DATE_DESC')); ?>"><?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_CSV_DATE'); ?></label>
						</td>
						<td>
							<select id="dateformat" size="1" name="dateformat">
								<option value="Y-m-d">Y-m-d</option>
								<option value="Y/m/d">Y/m/d</option>
								<option value="Y.m.d">Y.m.d</option>
								<option value="Y m d">Y m d</option>
								<option value="d-m-Y">d-m-Y</option>
								<option value="d/m/Y">d/m/Y</option>
								<option value="d.m.Y">d.m.Y</option>
								<option value="d m Y">d m Y</option>
								<option value="m-d-Y">m-d-Y</option>
								<option value="m/d/Y">m/d/Y</option>
								<option value="m.d.Y">m.d.Y</option>
								<option value="m d Y">m d Y</option>
							</select>
						</td>
					</tr>
					<tr>
						<td width="10%">&nbsp;</td>
						<td>
							<button type="button" class="btn" onclick="Joomla.submitbutton('imports.csv');"><?php echo JText::_('COM_RSEVENTSPRO_IMPORT'); ?></button>
						</td>
					</tr>
				</table>
			</fieldset>
		</div>
	</div>
	
	<?php echo JHTML::_( 'form.token' ); ?>	
	<input type="hidden" name="task" value="" />
</form>