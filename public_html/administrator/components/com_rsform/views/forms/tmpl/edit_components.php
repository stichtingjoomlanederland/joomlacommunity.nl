<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
	<ul class="rsform_leftnav" id="rsform_firstleftnav">
		<?php $this->triggerEvent('rsfp_onBeforeShowComponents');?>
		<li class="rsform_navtitle"><?php echo JText::_('RSFP_FORM_FIELDS'); ?></li>
		<li><a href="javascript: void(0);" onclick="displayTemplate('<?php echo RSFORM_FIELD_TEXTBOX; ?>');" id="rsfpc1"><span class="rsficon rsficon-progress-full"></span><span class="inner-text"><?php echo JText::_('RSFP_COMP_TEXTBOX'); ?></span></a></li>
		<li><a href="javascript: void(0);" onclick="displayTemplate('<?php echo RSFORM_FIELD_TEXTAREA; ?>');" id="rsfpc2"><span class="rsficon rsficon-file-text"></span><span class="inner-text"><?php echo JText::_('RSFP_COMP_TEXTAREA'); ?></span></a></li>
		<li><a href="javascript: void(0);" onclick="displayTemplate('<?php echo RSFORM_FIELD_SELECTLIST; ?>');" id="rsfpc3"><span class="rsficon rsficon-caret-square-o-down"></span><span class="inner-text"><?php echo JText::_('RSFP_COMP_DROPDOWN'); ?></span></a></li>
		<li><a href="javascript: void(0);" onclick="displayTemplate('<?php echo RSFORM_FIELD_CHECKBOXGROUP; ?>');" id="rsfpc4"><span class="rsficon rsficon-check-square-o"></span><span class="inner-text"><?php echo JText::_('RSFP_COMP_CHECKBOX'); ?></span></a></li>
		<li><a href="javascript: void(0);" onclick="displayTemplate('<?php echo RSFORM_FIELD_RADIOGROUP; ?>');" id="rsfpc5"><span class="rsficon rsficon-dot-circle-o"></span><span class="inner-text"><?php echo JText::_('RSFP_COMP_RADIO'); ?></span></a></li>
		<li><a href="javascript: void(0);" onclick="displayTemplate('<?php echo RSFORM_FIELD_SUBMITBUTTON; ?>');" id="rsfpc13"><span class="rsficon rsficon-square"></span><span class="inner-text"><?php echo JText::_('RSFP_COMP_SUBMITBUTTON'); ?></span></a></li>
		<li><a href="javascript: void(0);" onclick="displayTemplate('<?php echo RSFORM_FIELD_PASSWORD; ?>');" id="rsfpc14"><span class="rsficon rsficon-lock"></span><span class="inner-text"><?php echo JText::_('RSFP_COMP_PASSWORD'); ?></span></a></li>
		<li><a href="javascript: void(0);" onclick="displayTemplate('<?php echo RSFORM_FIELD_FILEUPLOAD; ?>');" id="rsfpc9"><span class="rsficon rsficon-file-text-o"></span><span class="inner-text"><?php echo JText::_('RSFP_COMP_FILE'); ?></span></a></li>
		<li><a href="javascript: void(0);" onclick="displayTemplate('<?php echo RSFORM_FIELD_FREETEXT; ?>');" id="rsfpc10"><span class="rsficon rsficon-sort-alphabetically"></span><span class="inner-text"><?php echo JText::_('RSFP_COMP_FREETEXT'); ?></span></a></li>
		<li><a href="javascript: void(0);" onclick="displayTemplate('<?php echo RSFORM_FIELD_CALENDAR; ?>');" id="rsfpc6"><span class="rsficon rsficon-calendar-o"></span><span class="inner-text"><?php echo JText::_('RSFP_COMP_CALENDAR'); ?></span></a></li>
		<li><a href="javascript: void(0);" onclick="displayTemplate('<?php echo RSFORM_FIELD_JQUERY_CALENDAR; ?>');" id="rsfpc411"><span class="rsficon rsficon-calendar"></span><span class="inner-text"><?php echo JText::_('RSFP_COMP_JQUERY_CALENDAR'); ?></span></a></li>
		<li><a href="javascript: void(0);" onclick="displayTemplate('<?php echo RSFORM_FIELD_BUTTON; ?>');" id="rsfpc7"><span class="rsficon rsficon-square"></span><span class="inner-text"><?php echo JText::_('RSFP_COMP_BUTTON'); ?></span></a></li>
		<li><a href="javascript: void(0);" onclick="displayTemplate('<?php echo RSFORM_FIELD_CAPTCHA; ?>');" id="rsfpc8"><span class="rsficon rsficon-shield"></span><span class="inner-text"><?php echo JText::_('RSFP_COMP_CAPTCHA'); ?></span></a></li>
		<li><a href="javascript: void(0);" onclick="displayTemplate('<?php echo RSFORM_FIELD_HIDDEN; ?>');" id="rsfpc11"><span class="rsficon rsficon-texture"></span><span class="inner-text"><?php echo JText::_('RSFP_COMP_HIDDEN'); ?></span></a></li>
		<li><a href="javascript: void(0);" onclick="displayTemplate('<?php echo RSFORM_FIELD_TICKET; ?>');" id="rsfpc15"><span class="rsficon rsficon-ticket"></span><span class="inner-text"><?php echo JText::_('RSFP_COMP_TICKET'); ?></span></a></li>
		<?php $this->triggerEvent('rsfp_bk_onAfterShowStandardComponents'); ?>
		<li class="rsform_navtitle"><?php echo JText::_('RSFP_MULTIPAGE'); ?></li>
		<li><a href="javascript: void(0);" onclick="displayTemplate('<?php echo RSFORM_FIELD_PAGEBREAK; ?>');" id="rsfpc41"><span class="rsficon rsficon-vertical_align_center"></span><span class="inner-text"><?php echo JText::_('RSFP_PAGE_BREAK'); ?></span></a></li>
		<li class="rsform_navtitle"><?php echo JText::_('RSFP_ADVANCED_FORM_FIELDS'); ?></li>
		<li><a href="javascript: void(0);" onclick="displayTemplate('<?php echo RSFORM_FIELD_BIRTHDAY; ?>');" id="rsfpc211"><span class="rsficon rsficon-birthday-cake"></span><span class="inner-text"><?php echo JText::_('RSFP_COMP_BIRTHDAY'); ?></span></a></li>
		<li><a href="javascript: void(0);" onclick="displayTemplate('<?php echo RSFORM_FIELD_GMAPS; ?>');" id="rsfpc212"><span class="rsficon rsficon-map-marker"></span><span class="inner-text"><?php echo JText::_('RSFP_COMP_GMAP'); ?></span></a></li>
		<li><a href="javascript: void(0);" onclick="displayTemplate('<?php echo RSFORM_FIELD_RANGE_SLIDER; ?>');" id="rsfpc355"><span class="rsficon rsficon-th-list"></span><span class="inner-text"><?php echo JText::_('RSFP_COMP_RANGE_SLIDER'); ?></span></a></li>
		<?php $this->triggerEvent('rsfp_bk_onAfterShowComponents'); ?>
	</ul>
	
	<input type="hidden" name="componentIdToEdit" id="componentIdToEdit" value="-1" />
	<input type="hidden" name="componentEditForm" id="componentEditForm" value="-1" />

<div id="componentscontent">
    <div class="alert alert-info text-center" id="rsform_ordering_msg">
        <?php echo JText::_('RSFP_ORDERING_FROM_GRID_LAYOUT'); ?><a href="javascript: void();" onclick="jQuery('#properties').click();jQuery('#gridlayout').click();" class="btn btn-primary"><?php echo JText::_('RSFP_GO_TO_ORDERING_GRID_LAYOUT'); ?></a>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <div class="alert alert-error" id="rsform_layout_msg" <?php if ($this->form->FormLayoutAutogenerate) { ?>style="display: none"<?php } ?>>
        <?php echo JText::_('RSFP_AUTOGENERATE_LAYOUT_DISABLED'); ?>
    </div>
    <div class="alert alert-error center" id="rsform_submit_button_msg" <?php if ($this->hasSubmitButton) { ?>style="display: none"<?php } ?>>
        <p><?php echo JHtml::image('com_rsform/admin/submit-help.jpg', JText::_('RSFP_NO_SUBMIT_BUTTON'), 'align="middle"', true); ?></p>
        <p><?php echo JText::_('RSFP_NO_SUBMIT_BUTTON'); ?></p>
    </div>
	<table border="0" width="100%" class="adminrsform">
		<tr>
			<td valign="top" class="componentPreview">
					<table border="0" id="componentPreview" class="adminlist table table-striped">
						<thead>
						<tr>
							<th class="title" width="1"><input type="hidden" value="-2" name="previewComponentId"/><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/></th>
							<th class="title"><?php echo JText::_('RSFP_NAME');?></th>
							<th class="title"><?php echo JText::_('RSFP_CAPTION');?></th>
							<th class="title"><?php echo JText::_('RSFP_PREVIEW');?></th>
							<th class="title" width="5">&nbsp;</th>
							<th class="title" width="5">&nbsp;</th>
							<th width="150" class="order nowrap center"><span class="pull-left"><?php echo JText::_('Ordering'); ?></span> <?php echo JHtml::_('grid.order',$this->fields); ?></th>
							<th class="title" width="5"><?php echo JText::_('RSFP_PUBLISHED');?></th>
							<th class="title" width="5" nowrap="nowrap"><?php echo JText::_('RSFP_COMP_FIELD_REQUIRED');?></th>
							<th class="title" width="5" nowrap="nowrap"><?php echo JText::_('RSFP_COMP_FIELD_VALIDATIONRULE');?></th>
						</tr>
						</thead>
						<tbody>
						<?php
						$i = 0;
						$k = 0;
						$n = count($this->fields);
						// hack to show order down icon
						$n++;
						foreach ($this->fields as $field) { ?>
						<tr class="row<?php echo $k; ?><?php if ($field->type_id == 41) { ?> rsform_page<?php } ?>">
							<td><input type="hidden" id="preview-id-<?php echo $field->id; ?>" name="previewComponentId" value="<?php echo $field->id; ?>" /><?php echo JHtml::_('grid.id', $i, $field->id); ?></td>
							<td><?php echo $field->name; ?></td>
							<?php echo $field->preview; ?>
							<td align="center"><button type="button" class="btn" onclick="displayTemplate('<?php echo $field->type_id; ?>','<?php echo $field->id; ?>');"><?php echo JText::_('RSFP_EDIT'); ?></button></td>
							<td align="center"><button type="button" class="btn btn-danger" onclick="if (confirm(Joomla.JText._('RSFP_REMOVE_COMPONENT_CONFIRM').replace('%s', '<?php echo $this->escape($field->name); ?>'))) removeComponent('<?php echo $this->form->FormId; ?>','<?php echo $field->id; ?>');"><?php echo JText::_('RSFP_DELETE'); ?></button></td>
							<td class="order center">
								<span><?php echo $this->pagination->orderUpIcon( $i, true, 'orderup', 'Move Up', 'ordering'); ?></span>
								<span><?php echo $this->pagination->orderDownIcon( $i, $n, true, 'orderdown', 'Move Down', 'ordering' ); ?></span>
								<input type="text" name="order[]" size="5" value="<?php echo $field->ordering; ?>" disabled="disabled" class="width-20 text-area-order" style="text-align:center" />
							</td>
							<td align="center" id="publishcb<?php echo $i; ?>"><?php echo JHtml::_('jgrid.published', $field->published, $i, 'components.'); ?></td>
							<td align="center" id="requiredcb<?php echo $i; ?>"><?php echo is_bool($field->required) ?
							JHtml::_('jgrid.state', array(
								0 => array('setrequired', 'JYES', '', '', false, 'unpublish', 'unpublish'),
								1 => array('unsetrequired', 'JNO', '', '', false, 'publish', 'publish')
							), $field->required, $i, 'components.')
							: '-'; ?></td>
							<td align="center"><?php echo $field->validation; ?></td>
						</tr>
						<?php
						$i++;
						$k=1-$k;
						}
						?>
						</tbody>
					</table>
			</td>
		</tr>
	</table>
</div>