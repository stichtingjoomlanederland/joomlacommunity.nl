<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.tooltip');
JHtml::_('behavior.keepalive');
?>

<?php 
	$rseClass = $this->items['rsevents'] ? ' rs_import_active btn btn-success' : ' rs_import_disabled btn btn-danger hasTip';
	$rseClick = $this->items['rsevents'] ? 'onclick="Joomla.submitbutton(\'imports.rsevents\');"' : '';
	$rseTitle = !$this->items['rsevents'] ? 'title="'.JText::_('COM_RSEVENTSPRO_IMPORT_RSEVENTS_MISSING').'"' : '';
	$jevClass = $this->items['jevents'] ? ' rs_import_active btn btn-success' : ' rs_import_disabled btn btn-danger hasTip';
	$jevClick = $this->items['jevents'] ? 'onclick="Joomla.submitbutton(\'imports.jevents\');"' : '';
	$jevTitle = !$this->items['jevents'] ? 'title="'.JText::_('COM_RSEVENTSPRO_IMPORT_JEVENTS_MISSING').'"' : '';
	$evlClass = $this->items['eventlist'] ? ' rs_import_active btn btn-success' : ' rs_import_disabled btn btn-danger hasTip';
	$evlClick = $this->items['eventlist'] ? 'onclick="Joomla.submitbutton(\'imports.eventlist\');"' : '';
	$evlTitle = !$this->items['eventlist'] ? 'title="'.JText::_('COM_RSEVENTSPRO_IMPORT_EVENTLIST_MISSING').'"' : '';
	$evbClass = $this->items['eventlistbeta'] ? ' rs_import_active btn btn-success' : ' rs_import_disabled btn btn-danger hasTip';
	$evbClick = $this->items['eventlistbeta'] ? 'onclick="Joomla.submitbutton(\'imports.eventlistbeta\');"' : '';
	$evbTitle = !$this->items['eventlistbeta'] ? 'title="'.JText::_('COM_RSEVENTSPRO_IMPORT_EVENTLIST_MISSING').'"' : '';
	$jclClass = $this->items['jcalpro'] ? ' rs_import_active btn btn-success' : ' rs_import_disabled btn btn-danger hasTip';
	$jclClick = $this->items['jcalpro'] ? 'onclick="Joomla.submitbutton(\'imports.jcalpro\');"' : '';
	$jclTitle = !$this->items['jcalpro'] ? 'title="'.JText::_('COM_RSEVENTSPRO_IMPORT_JCALPRO_MISSING').'"' : '';
	$ohaClass = $this->items['ohanah'] ? ' rs_import_active btn btn-success' : ' rs_import_disabled btn btn-danger hasTip';
	$ohaClick = $this->items['ohanah'] ? 'onclick="Joomla.submitbutton(\'imports.ohanah\');"' : '';
	$ohaTitle = !$this->items['ohanah'] ? 'title="'.JText::_('COM_RSEVENTSPRO_IMPORT_OHANAH_MISSING').'"' : '';
?>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=imports'); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal" enctype="multipart/form-data">
	<div class="row-fluid">
		<div class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div class="span10">
			<fieldset>
				<legend><?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_COMPONENTS'); ?></legend>
				<table class="table table-striped adminlist">
					<tr>
						<td width="2%"><img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/import.png" alt="" /></td>
						<td>
							<a href="javascript:void(0)" <?php echo $rseTitle; ?> <?php echo $rseClick; ?> class="rs_import_link<?php echo $rseClass; ?>"><?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_RSEVENTS'); ?></a>
						</td>
					</tr>
					<tr>
						<td width="2%"><img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/import.png" alt="" /></td>
						<td>
							<a href="javascript:void(0)" <?php echo $jevTitle; ?> <?php echo $jevClick; ?> class="rs_import_link<?php echo $jevClass; ?>"><?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_JEVENTS'); ?></a>
						</td>
					</tr>
					<?php 
					/*
					<tr>
						<td width="2%"><img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/import.png" alt="" /></td>
						<td>
							<a href="javascript:void(0)" <?php echo $evlTitle; ?> <?php echo $evlClick; ?> class="rs_import_link<?php echo $evlClass; ?>"><?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_EVENTLIST'); ?></a>
							&nbsp; | &nbsp;
							<a href="javascript:void(0)" <?php echo $evbTitle; ?> <?php echo $evbClick; ?> class="rs_import_link<?php echo $evbClass; ?>"><?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_EVENTLISTBETA'); ?></a> 
						</td>
					</tr>
					*/ ?>
					<tr>
						<td width="2%"><img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/import.png" alt="" /></td>
						<td>
							<a href="javascript:void(0)" <?php echo $jclTitle; ?> <?php echo $jclClick; ?> class="rs_import_link<?php echo $jclClass; ?>"><?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_JCALPRO'); ?></a>
						</td>
					</tr>
					<tr>
						<td width="2%"><img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/import.png" alt="" /></td>
						<td>
							<a href="javascript:void(0)" <?php echo $ohaTitle; ?> <?php echo $ohaClick; ?> class="rs_import_link<?php echo $ohaClass; ?>"><?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_OHANAH'); ?></a>
						</td>
					</tr>
					<tr>
						<td width="2%">&nbsp;</td>
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
		</div>
		<div class="span2"></div>
		<div class="span10">
			<fieldset>
				<legend><?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_FILE'); ?></legend>
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="adminlist">
					<tr>
						<td width="10%"><?php echo JText::_('COM_RSEVENTSPRO_CSV_EXAMPLE'); ?></td>
						<td>
							<div style="width: 60%;">
								<b>Event Name, Start Date, End Date, Event Description, Event URL, Event Email, Event Phone, Location Name, Location Address</b>
								<hr />
								<b>"First event name","2012-05-20 18:40:00","2012-05-25 18:40:00","Event Description","Event URL","Event Email","Event Phone","Location Name","Location address"</b>
								<br />
								<b>"Second event name","2012-08-20 20:40:00","2012-08-27 10:45:00","Event Description","Event URL","Event Email","Event Phone","Location Name","Location address"</b>
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
							<label for="category" class="hasTip" title="<?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_CSV_CATEGORIES_DESC'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_CSV_CATEGORIES'); ?></label>
						</td>
						<td>
							<select id="category" name="category">
								<?php echo JHtml::_('select.options', JHtml::_('category.options','com_rseventspro',array(1))); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td width="10%">
							<label for="location" class="hasTip" title="<?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_CSV_LOCATION_DESC'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_IMPORT_FROM_CSV_LOCATION'); ?></label>
						</td>
						<td>
							<select id="location" size="1" name="location">
								<?php echo JHtml::_('select.options', $this->locations); ?>
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