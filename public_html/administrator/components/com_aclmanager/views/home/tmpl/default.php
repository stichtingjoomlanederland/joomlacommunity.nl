<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;
?>

<div id="aclmanager" class="home">
	<!-- Start User Group Permissions -->
	<div class="width-35 fltlft">
		<fieldset class="adminform aclhome">
			<legend><?php echo JText::_('COM_ACLMANAGER_HOME_PERMISSION_USERGROUP'); ?></legend>
			<img class="fieldseticon" src="components/com_aclmanager/assets/images/group.png"/>
			<p class="intro"><?php echo JText::_('COM_ACLMANAGER_HOME_PERMISSION_USERGROUP_DESC'); ?></p>
			<a class="new-button" href="<?php echo JRoute::_('index.php?option=com_users&task=group.add')?>"><?php echo JText::_('COM_ACLMANAGER_HOME_NEW_USERGROUP'); ?></a>
			<table class="adminlist" id="groups">
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
		</fieldset>
		<div class="clr"></div>
	</div>
	<!-- End User Group Permissions -->

	<!-- Start User Permissions -->
	<div class="width-35 fltlft">
		<fieldset class="adminform aclhome">
			<legend><?php echo JText::_('COM_ACLMANAGER_HOME_PERMISSION_USER'); ?></legend>
			<img class="fieldseticon" src="components/com_aclmanager/assets/images/user.png"/>
			<p class="intro"><?php echo JText::_('COM_ACLMANAGER_HOME_PERMISSION_USER_DESC'); ?></p>
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
		</fieldset>
		<div class="clr"></div>
	</div>
	<!-- End User Permissions -->

	<!-- Start Sidebar -->
	<div class="width-30 fltrt">
		<fieldset class="adminform">
			<a href="http://www.aclmanager.net"><img class="aclmanagerlogo" src="components/com_aclmanager/assets/images/aclmanager.png"/></a>
			<ul class="adminformlist">
				<li>
					<label><?php echo JText::_('COM_ACLMANAGER_UPDATE_RELEASE'); ?></label>
					<span class="value"><?php echo $this->updateInfo->extInfo->version; ?></span>
				</li>
				<li>
					<label><?php echo JText::_('COM_ACLMANAGER_UPDATE_RELEASE_DATE'); ?></label>
					<span class="value"><?php echo $this->updateInfo->extInfo->date; ?></span>
				</li>
				<li>
					<label><?php echo JText::_('COM_ACLMANAGER_HOME_SUPPORT'); ?></label>
					<span class="value"><a target="_blank" href="http://www.aclmanager.net/support">www.aclmanager.net/support</a></span>
				</li>
				<li>
					<label><?php echo JText::sprintf('COM_ACLMANAGER_HOME_REVIEW', 'http://extensions.joomla.org/extensions/access-a-security/site-access/backend-a-full-access-control/17951'); ?></label>

				</li>
			</ul>
		</fieldset>

		<?php if(JFactory::getUser()->authorise('aclmanager.diagnostic', 'com_aclmanager')):?>
		<fieldset class="adminform <?php if(($this->assetissues) || ($this->orphanassets) || ($this->missingassets) || ($this->adminconflicts)): ?> issues<?php endif;?>">
			<legend><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CHECKS'); ?></legend>
			<img class="fieldseticon" src="components/com_aclmanager/assets/images/diagnostic.png"/>
			<ul>
				<li class="rule">
					<span class="icon <?php if ($this->orphanassets): ?>denied<?php else:?>allowed<?php endif;?>"></span>
					<span class="legend">
						<a href="<?php echo JRoute::_('index.php?option=com_aclmanager&view=diagnostic#orphanassets')?>"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ORPHAN_ASSETS'); ?></a>
					</span>
				</li>
				<li class="rule">
					<span class="icon <?php if ($this->missingassets): ?>denied<?php elseif ($this->orphanassets): ?>unset<?php else:?>allowed<?php endif;?>"></span>
					<span class="legend">
						<a href="<?php echo JRoute::_('index.php?option=com_aclmanager&view=diagnostic#missingassets')?>"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_MISSING_ASSETS'); ?></a>
					</span>
				</li>
				<li class="rule">
					<span class="icon <?php if ($this->assetissues): ?>denied<?php elseif ($this->orphanassets || $this->missingassets): ?>unset<?php else:?>allowed<?php endif;?>"></span>
					<span class="legend">
						<a href="<?php echo JRoute::_('index.php?option=com_aclmanager&view=diagnostic#assetissues')?>"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES'); ?></a>
					</span>
				</li>
				<li class="rule">
					<span class="icon <?php if ($this->adminconflicts): ?>denied<?php else:?>allowed<?php endif;?>"></span>
					<span class="legend">
						<a href="<?php echo JRoute::_('index.php?option=com_aclmanager&view=diagnostic#adminconflicts')?>"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ADMIN_ACCESS_CONFLICTS'); ?></a>
					</span>
				</li>
			</ul>
		</fieldset>
		<?php endif;?>
		<div class="clr"></div>
	</div>
	<!-- End Sibebar -->
</div>
<div class="copyright">
	<p><?php echo JText::_('COM_ACLMANAGER_COPYRIGHT'); ?> &copy; 2011 - <?php echo date('Y');?>. <?php echo JText::_('COM_ACLMANAGER_DEVELOPED_BY');?>. <a href="http://www.aclmanager.net" target="_blank">www.aclmanager.net</a></p>
</div>