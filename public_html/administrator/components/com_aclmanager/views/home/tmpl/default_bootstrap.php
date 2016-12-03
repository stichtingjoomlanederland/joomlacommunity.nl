<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;
?>

<div id="aclmanager" class="home row-fluid bootstraphome">
	<!-- Start User Group Permissions -->
	<div class="span4">
		<div class="well groups">
			<legend><?php echo JText::_('COM_ACLMANAGER_HOME_PERMISSION_USERGROUP'); ?></legend>
			<div class="desc">
				<img class="pull-right" src="components/com_aclmanager/assets/images/group.png"/>
				<p><?php echo JText::_('COM_ACLMANAGER_HOME_PERMISSION_USERGROUP_DESC'); ?></p>
				<a class="new-button" href="<?php echo JRoute::_('index.php?option=com_users&task=group.add')?>"><?php echo JText::_('COM_ACLMANAGER_HOME_NEW_USERGROUP'); ?></a>
			</div>
			<table class="table table-striped" id="groups">
				<thead>
					<tr>
						<th class="left"><?php echo JText::_('COM_USERS_GROUP_FIELD_TITLE_LABEL'); ?></th>
						<th width="15%"><?php echo JText::_('JGLOBAL_FIELD_ID_LABEL'); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2" class="dataTables_empty"></td>
					</tr>
				</tbody>
			</table>
			<script type="text/javascript" charset="utf-8">
				jQuery(document).ready(function($) {
					$('#groups').dataTable( {
						"bServerSide": true,
						"iDisplayLength": 15,
						"bSort": false,
						"sDom": 'frtlip',
						"bInfo": false,
						"aoColumnDefs": [{ "sClass": "center", "aTargets":[1]}],
						"sAjaxSource": "index.php?option=com_aclmanager&view=home&format=json&type=group",
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
		</div>
	</div>
	<!-- End User Group Permissions -->

	<!-- Start User Permissions -->
	<div class="span4">
		<div class="well users">
			<legend><?php echo JText::_('COM_ACLMANAGER_HOME_PERMISSION_USER'); ?></legend>
			<div class="desc">
				<img class="pull-right" src="components/com_aclmanager/assets/images/user.png"/>
				<p><?php echo JText::_('COM_ACLMANAGER_HOME_PERMISSION_USER_DESC'); ?></p>
				<a class="new-button" href="<?php echo JRoute::_('index.php?option=com_users&task=user.add')?>"><?php echo JText::_('COM_ACLMANAGER_HOME_NEW_USER'); ?></a>
			</div>
			<table class="table table-striped" id="users">
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
						"sAjaxSource": "index.php?option=com_aclmanager&view=home&format=json&type=user",
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
		</div>
	</div>
	<!-- End User Permissions -->

	<!-- Start Sidebar -->
	<div class="span4">
		<div class="well aclmanager">
			<a href="http://www.aclmanager.net"><img class="aclmanagerlogo" src="components/com_aclmanager/assets/images/aclmanager.png"/></a>
			<dl>
				<dt><?php echo JText::_('COM_ACLMANAGER_UPDATE_RELEASE'); ?></dt>
				<dd><?php echo $this->updateInfo->extInfo->version; ?></dd>
				<dt><?php echo JText::_('COM_ACLMANAGER_UPDATE_RELEASE_DATE'); ?></dt>
				<dd><?php echo $this->updateInfo->extInfo->date; ?></dd>
				<dt><?php echo JText::_('COM_ACLMANAGER_HOME_SUPPORT'); ?></dt>
				<dd><a target="_blank" href="http://www.aclmanager.net/support">www.aclmanager.net/support</a></dd>
				<dt><?php echo JText::_('Review'); ?></dt>
				<dd><?php echo JText::sprintf('COM_ACLMANAGER_HOME_REVIEW', 'http://extensions.joomla.org/extensions/access-a-security/site-access/backend-a-full-access-control/17951'); ?></dd>
			</dl>
		</div>

		<?php if(JFactory::getUser()->authorise('aclmanager.diagnostic', 'com_aclmanager')):?>
		<div class="well diagnostic <?php if(($this->assetissues) || ($this->orphanassets) || ($this->missingassets) || ($this->adminconflicts)): ?> alert alert-error<?php endif;?>">
			<legend><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CHECKS'); ?></legend>
			<img class="pull-right" src="components/com_aclmanager/assets/images/diagnostic.png"/>
			<ul class="unstyled checks">
				<li class="<?php if ($this->orphanassets): ?>error<?php else:?>success<?php endif;?>">
					<i class="icon-<?php if ($this->orphanassets): ?>warning<?php else:?>checkmark<?php endif;?>"></i> <a href="<?php echo JRoute::_('index.php?option=com_aclmanager&view=diagnostic#orphanassets')?>"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ORPHAN_ASSETS'); ?></a>
				</li>
				<li class="<?php if ($this->missingassets): ?>error<?php elseif ($this->orphanassets): ?>error<?php else:?>success<?php endif;?>">
					<i class="icon-<?php if ($this->missingassets): ?>warning<?php elseif ($this->orphanassets): ?>minus-2<?php else:?>checkmark<?php endif;?>"></i> <a href="<?php echo JRoute::_('index.php?option=com_aclmanager&view=diagnostic#missingassets')?>"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_MISSING_ASSETS'); ?></a>
				</li>
				<li class="<?php if ($this->assetissues): ?>error<?php elseif ($this->orphanassets || $this->missingassets): ?>error<?php else:?>success<?php endif;?>">
					<i class="icon-<?php if ($this->assetissues): ?>warning<?php elseif ($this->orphanassets || $this->missingassets): ?>minus-2<?php else:?>checkmark<?php endif;?>"></i> <a href="<?php echo JRoute::_('index.php?option=com_aclmanager&view=diagnostic#assetissues')?>"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES'); ?></a>
				</li>
				<li class="<?php if ($this->adminconflicts): ?>error<?php else:?>success<?php endif;?>">
					<i class="icon-<?php if ($this->adminconflicts): ?>error<?php else:?>checkmark<?php endif;?>"></i> <a href="<?php echo JRoute::_('index.php?option=com_aclmanager&view=diagnostic#adminconflicts')?>"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ADMIN_ACCESS_CONFLICTS'); ?></a>
				</li>
			</ul>
		</div>
		<?php endif;?>
	</div>
	<!-- End Sibebar -->
</div>
<div class="copyright">
	<p><?php echo JText::_('COM_ACLMANAGER_COPYRIGHT'); ?> &copy; 2011 - <?php echo date('Y');?>. <?php echo JText::_('COM_ACLMANAGER_DEVELOPED_BY');?>. <a href="http://www.aclmanager.net" target="_blank">www.aclmanager.net</a></p>
</div>