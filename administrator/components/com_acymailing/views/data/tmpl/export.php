<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="acy_content">
	<div id="iframedoc"></div>
	<?php $app = JFactory::getApplication(); ?>
	<form action="index.php?option=<?php echo ACYMAILING_COMPONENT ?>" method="post" name="adminForm" id="adminForm">
		<style>
			#acy_content .oneBlock{
			<?php if($app->isAdmin()){ ?> float: left;
				width: 49%;
				padding: 5px;
				min-width: 500px;
			<?php }else{ ?> width: 100%;
			<?php } ?>
			}
		</style>
		<div style="width:100%;">
			<div class="<?php echo $this->isAdmin ? 'acyblockoptions' : 'onelineblockoptions'; ?>">
				<span class="acyblocktitle"><?php echo JText::_('FIELD_EXPORT'); ?></span>
				<table class="acymailing_smalltable">
					<?php
					$k = 0;
					if(!empty($this->fields)){
						foreach($this->fields as $fieldName => $fieldType){
							?>
							<tr class="<?php echo "row$k"; ?>" id="userField_<?php echo $fieldName; ?>">
								<td>
									<?php echo $fieldName ?>
								</td>
								<td align="center" style="text-align:center">
									<?php echo JHTML::_('acyselect.booleanlist', "exportdata[".$fieldName."]", '', in_array($fieldName, $this->selectedfields) ? 1 : 0); ?>
								</td>
							</tr>
							<?php
							$k = 1 - $k;
						}
					}
					if(!empty($this->otherfields)){

						foreach($this->otherfields as $fieldName){
							?>
							<tr class="<?php echo "row$k"; ?>" id="userField_<?php echo $fieldName; ?>">
								<td>
									<?php echo $fieldName ?>
								</td>
								<td align="center" style="text-align:center">
									<?php echo JHTML::_('acyselect.booleanlist', "exportdataother[".$fieldName."]", '', in_array($fieldName, $this->selectedfields) ? 1 : 0, JText::_('JOOMEXT_YES'), JText::_('JOOMEXT_NO'), str_replace('.', '_', $fieldName)); ?>
								</td>
							</tr>
							<?php
							$k = 1 - $k;
						}
					}
					if(!empty($this->fieldsList)){
						foreach($this->fieldsList as $fieldName => $fieldType){
							?>
							<tr class="<?php echo "row$k"; ?>" id="userField_<?php echo $fieldName; ?>">
								<td>
									<?php echo $fieldName ?>
								</td>
								<td align="center" style="text-align:center">
									<?php echo JHTML::_('acyselect.booleanlist', "exportdatalist[".$fieldName."]", '', in_array($fieldName, $this->selectedfields) ? 1 : 0); ?>
								</td>
							</tr>
							<?php
							$k = 1 - $k;
						}
					}
					if(!empty($this->geolocfields)){
						?>
						<tr class="<?php echo "row$k"; ?>" id="userField_<?php echo $fieldName; ?>">
							<td>
								<?php echo JText::_('ACYEXPORT_GEOLOC_VALUE'); ?>
							</td>
							<td align="center" style="text-align:center">
								<?php
								$values = array(JHTML::_('select.option', 'asc', JText::_('SEPARATOR_FIRST_GEOL_SAVED')), JHTML::_('select.option', 'desc', JText::_('ACYEXPORT_LAST_GEOL_SAVED')));
								echo JHTML::_('acyselect.genericlist', $values, 'exportgeolocorder', '', 'value', 'text', $this->config->get('exportgeolocorder', 'asc')); ?>
							</td>
						</tr>
						<?php
						$k = 1 - $k;

						foreach($this->geolocfields as $fieldName => $fieldType){
							if(in_array($fieldName, array('geolocation_id', 'geolocation_subid'))) continue;
							?>
							<tr class="<?php echo "row$k"; ?>" id="userField_<?php echo $fieldName; ?>">
								<td>
									<?php echo $fieldName ?>
								</td>
								<td align="center" style="text-align:center">
									<?php echo JHTML::_('acyselect.booleanlist', "exportdatageoloc[".$fieldName."]", '', in_array($fieldName, $this->selectedfields) ? 1 : 0); ?>
								</td>
							</tr>
							<?php
							$k = 1 - $k;
						}
					}
					?>
					<tr class="<?php echo "row$k";
					$k = 1 - $k; ?>" id="userField_exportFormat">
						<td>
							<?php echo JText::_('EXPORT_FORMAT'); ?>
						</td>
						<td align="center" style="text-align:center">
							<?php echo $this->charset->display('exportformat', $this->config->get('export_format', 'UTF-8')); ?>
						</td>
					</tr>
					<tr class="<?php echo "row$k"; ?>" id="userField_separator">
						<td>
							<?php echo JText::_('ACY_SEPARATOR'); ?>
						</td>
						<td align="center" nowrap="nowrap">
							<?php
							$values = array(JHTML::_('select.option', 'semicolon', JText::_('SEPARATOR_SEMICOLON')), JHTML::_('select.option', 'comma', JText::_('SEPARATOR_COMMA')));
							$data = str_replace(array(';', ','), array('semicolon', 'comma'), $this->config->get('export_separator', ';'));
							if($data == 'colon') $data = 'comma';
							echo JHTML::_('acyselect.radiolist', $values, 'exportseparator', '', 'value', 'text', $data);
							?>
						</td>
					</tr>
				</table>
			</div>
			<?php if (empty($this->users)){ ?>
			<div class="<?php echo $this->isAdmin ? 'acyblockoptions' : 'onelineblockoptions'; ?>">
				<span class="acyblocktitle"><?php echo JText::_('ACY_FILTERS'); ?></span>
				<table class="acymailing_smalltable">
					<tr class="row0">
						<td>
							<?php echo JText::_('EXPORT_SUB_LIST'); ?>
						</td>
						<td align="center" nowrap="nowrap">
							<?php echo JHTML::_('acyselect.booleanlist', "exportfilter[subscribed]", 'onchange="if(this.value == 1){document.getElementById(\'exportlists\').style.display = \'block\'; }else{document.getElementById(\'exportlists\').style.display = \'none\'; }"', (in_array('subscribed', $this->selectedFilters) || !empty($this->exportlist)) ? 1 : 0, JText::_('JOOMEXT_YES'), JText::_('JOOMEXT_NO').' : '.JText::_('ALL_USERS')); ?>
						</td>
					</tr>
					<tr class="row1">
						<td>
							<?php echo JText::_('EXPORT_REGISTERED'); ?>
						</td>
						<td align="center" style="text-align:center">
							<?php echo JHTML::_('acyselect.booleanlist', "exportfilter[registered]", '', in_array('registered', $this->selectedFilters) ? 1 : 0, JText::_('JOOMEXT_YES'), JText::_('JOOMEXT_NO').' : '.JText::_('ALL_USERS')); ?>
						</td>
					</tr>
					<tr class="row0">
						<td>
							<?php echo JText::_('EXPORT_CONFIRMED'); ?>
						</td>
						<td align="center" style="text-align:center">
							<?php echo JHTML::_('acyselect.booleanlist', "exportfilter[confirmed]", '', in_array('confirmed', $this->selectedFilters) ? 1 : 0, JText::_('JOOMEXT_YES'), JText::_('JOOMEXT_NO').' : '.JText::_('ALL_USERS')); ?>
						</td>
					</tr>
					<tr class="row1">
						<td>
							<?php echo JText::_('EXPORT_ENABLED'); ?>
						</td>
						<td align="center" style="text-align:center">
							<?php echo JHTML::_('acyselect.booleanlist', "exportfilter[enabled]", '', in_array('enabled', $this->selectedFilters) ? 1 : 0, JText::_('JOOMEXT_YES'), JText::_('JOOMEXT_NO').' : '.JText::_('ALL_USERS')); ?>
						</td>
					</tr>
				</table>
				</id>
				<?php } ?>
			</div>
			<div class="<?php echo $this->isAdmin ? 'acyblockoptions' : 'onelineblockoptions'; ?>" id="exportlists" <?php echo (in_array('subscribed', $this->selectedFilters) || !empty($this->exportlist) || !empty($this->users)) ? '' : 'style="display:none"' ?> >
				<?php
				if(empty($this->users)){ ?>
					<span class="acyblocktitle"><?php echo JText::_('LISTS'); ?></span>
					<?php
					$currentPage = 'export';
					include_once(ACYMAILING_BACK.'views'.DS.'list'.DS.'tmpl'.DS.'filter.lists.php');
				}else{ ?>
					<span class="acyblocktitle"><?php echo JText::_('USERS'); ?></span>
					<table class="acymailing_table" cellpadding="1">
						<?php
						$k = 0;
						foreach($this->users as $row){
							?>
							<tr class="<?php echo "row$k"; ?>">
								<td><?php echo htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'); ?></td>
								<td><?php echo htmlspecialchars($row->email, ENT_QUOTES, 'UTF-8'); ?></td>
							</tr>
							<?php $k = 1 - $k;
						}

						if(count($this->users) >= 10){
							?>
							<tr class="<?php echo "row$k"; ?>">
								<td>...</td>
								<td>...</td>
							</tr>
						<?php } ?>
					</table>
				<?php } ?>
			</div>
			<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>"/>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>"/>
			<input type="hidden" name="sessionvalues" value="<?php echo empty($this->users) ? 0 : JRequest::getInt('sessionvalues'); ?>"/>
			<input type="hidden" name="sessionquery" value="<?php echo empty($this->users) ? 0 : JRequest::getInt('sessionquery'); ?>"/>
			<?php echo JHTML::_('form.token'); ?>
	</form>
	<div class="clr"></div>
</div>
