<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// Initialise variable
$groupId = $this->state->get('filter.group_id');
?>

<?php if ($this->params->get('show_info',1)): ?>
<div class="well">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_ACLMANAGER_SIDEBAR_USERGROUP_INFORMATION'); ?></legend>
		<img class="fieldseticon" src="components/com_aclmanager/assets/images/group.png"/>
		<ul class="adminformlist">
			<li>
				<label><?php echo JText::_('COM_ACLMANAGER_SIDEBAR_NAME'); ?></label>
				<span class="value link">
					<?php if ((JFactory::getUser()->authorise('core.admin', 'com_users')) && (JFactory::getUser()->authorise('core.manage', 'com_users'))):?>
						<a href="<?php echo JRoute::_('index.php?option=com_users&task=group.edit&id='.$groupId.'&aclmanager=group'); ?>"><?php echo(AclmanagerHelper::groupName($groupId));?></a>
					<?php else:?>
						<?php echo(AclmanagerHelper::groupName($groupId));?>
					<?php endif;?>
				</span>
			</li>
			<li>
				<label><?php echo JText::_('COM_ACLMANAGER_SIDEBAR_USERGROUP_ID'); ?></label>
				<span class="value"><?php echo($groupId);?></span>
			</li>
		</ul>
	</fieldset>
</div>
<?php endif; ?>

<?php if ($this->params->get('show_assigned',1)): ?>
<div class="well">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_ACLMANAGER_SIDEBAR_ASSIGNED_USERS'); ?></legend>
		<a class="new-button" href="<?php echo JRoute::_('index.php?option=com_users&task=user.add')?>"><?php echo JText::_('COM_ACLMANAGER_HOME_NEW_USER'); ?></a>
		<table class="adminlist" id="users">
			<thead>
				<tr>
					<th class="left"><?php echo JText::_('COM_USERS_HEADING_NAME'); ?></th>
					<th class="left" width="45%"><?php echo JText::_('JGLOBAL_USERNAME'); ?></th>
					<th width="15%"><?php echo JText::_('JGLOBAL_FIELD_ID_LABEL'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="3" class="dataTables_empty"></td>
				</tr>
			</tbody>
		</table>
		<script type="text/javascript" charset="utf-8">
			jQuery(document).ready(function($) {
				$('#users').dataTable( {
					"bServerSide": true,
					"iDisplayLength": 15,
					"sDom": 'frtlip',
					"bInfo": false,
					"aaSorting": [[ 0, "asc" ]],
					"aoColumnDefs": [{ "sClass": "center", "aTargets":[2]}],
					"sAjaxSource": "index.php?option=com_aclmanager&view=home&format=json&type=user&group=<?php echo($this->state->get('filter.group_id'));?>",
					"oLanguage": {
					    "sSearch": "<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>",
					    "sLengthMenu": "<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?> _MENU_",
					    "oPaginate": {
						    "sNext": "<?php echo JText::_('JNEXT'); ?>",
						    "sPrevious": "<?php echo JText::_('JPREVIOUS'); ?>"
						  }
					  }
				} );
			} );
		</script>
	</fieldset>
</div>
<?php endif; ?>

<?php if ($this->params->get('show_legend',1)): ?>
<div class="well">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_ACLMANAGER_SIDEBAR_LEGEND'); ?></legend>
		<ul>
			<li class="rule">
				<span class="icon unset"></span>
				<span class="legend hasTip" title="<?php echo JText::_('COM_ACLMANAGER_SIDEBAR_NOT_ALLOWED_TITLE'); ?>::<?php echo JText::_('COM_ACLMANAGER_SIDEBAR_NOT_ALLOWED_DESC'); ?>">
					<?php echo JText::_('COM_ACLMANAGER_SIDEBAR_NOT_ALLOWED'); ?>
				</span>
			</li>
			<li class="rule allowed">
				<span class="icon allowed"></span>
				<span class="legend hasTip" title="<?php echo JText::_('COM_ACLMANAGER_SIDEBAR_ALLOWED_TITLE'); ?>::<?php echo JText::_('COM_ACLMANAGER_SIDEBAR_ALLOWED_DESC'); ?>">
					<?php echo JText::_('COM_ACLMANAGER_SIDEBAR_ALLOWED'); ?>
				</span>
			</li>
			<li class="rule">
				<span class="icon allowed-i"></span>
				<span class="legend hasTip" title="<?php echo JText::_('COM_ACLMANAGER_SIDEBAR_INHERITED_ALLOWED_TITLE'); ?>::<?php echo JText::_('COM_ACLMANAGER_SIDEBAR_INHERITED_ALLOWED_DESC'); ?>">
					<?php echo JText::_('COM_ACLMANAGER_SIDEBAR_INHERITED_ALLOWED'); ?>
				</span>
			</li>
			<li class="rule denied">
				<span class="icon denied"></span>
				<span class="legend hasTip" title="<?php echo JText::_('COM_ACLMANAGER_SIDEBAR_DENIED_TITLE'); ?>::<?php echo JText::_('COM_ACLMANAGER_SIDEBAR_DENIED_DESC'); ?>">
					<?php echo JText::_('COM_ACLMANAGER_SIDEBAR_DENIED'); ?>
				</span>
			</li>
			<li class="rule">
				<span class="icon denied-i"></span>
				<span class="legend hasTip" title="<?php echo JText::_('COM_ACLMANAGER_SIDEBAR_INHERITED_DENIED_TITLE'); ?>::<?php echo JText::_('COM_ACLMANAGER_SIDEBAR_INHERITED_DENIED_DESC'); ?>">
					<?php echo JText::_('COM_ACLMANAGER_SIDEBAR_INHERITED_DENIED'); ?>
				</span>
			</li>
			<li class="rule conflict">
				<span class="icon conflict"></span>
				<span class="legend hasTip" title="<?php echo JText::_('COM_ACLMANAGER_SIDEBAR_CONFLICT_TITLE'); ?>::<?php echo JText::_('COM_ACLMANAGER_SIDEBAR_CONFLICT_DESC'); ?>">
					<?php echo JText::_('COM_ACLMANAGER_SIDEBAR_CONFLICT'); ?>
				</span>
			</li>
		</ul>
	</fieldset>
</div>
<?php endif; ?>

<div class="clr"> </div>