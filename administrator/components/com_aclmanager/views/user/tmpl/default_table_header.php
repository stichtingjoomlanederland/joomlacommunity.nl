<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2014 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;
?>

<thead>
	<tr>
		<th rowspan="2" width="22%" class="brrgt"><?php echo JText::_('COM_ACLMANAGER_TABLE_ASSET_TITLE'); ?></th>
		<th colspan="3" width="21%"><?php echo JText::_('COM_ACLMANAGER_ACTION_LOGIN'); ?></th>
		<th colspan="2" width="14%" class="brlft brrgt"><?php echo JText::_('COM_ACLMANAGER_ACTION_EXTENSION'); ?></th>
		<th colspan="5" width="35%"><?php echo JText::_('COM_ACLMANAGER_ACTION_OBJECT'); ?></th>
		<th rowspan="2" width="3%" class="nowrap brlft"><?php echo JText::_('JGLOBAL_FIELD_ID_LABEL'); ?></th>
	</tr>
	<tr>
		<th width="7%">
			<span class="hasTip" title="<?php echo (JText::_('JACTION_LOGIN_SITE').'::'.JText::_('COM_CONFIG_ACTION_LOGIN_SITE_DESC')); ?>">
				<?php echo JText::_('JSITE'); ?>
			</span>
		</th>
		<th width="7%">
			<span class="hasTip" title="<?php echo (JText::_('JACTION_LOGIN_ADMIN').'::'.JText::_('COM_CONFIG_ACTION_LOGIN_ADMIN_DESC')); ?>">
				<?php echo JText::_('COM_ACLMANAGER_ACTION_ADMIN'); ?>
			</span>
		</th>
		<th width="7%">
			<span class="hasTip" title="<?php echo (JText::_('JACTION_LOGIN_OFFLINE').'::'.JText::_('COM_CONFIG_ACTION_LOGIN_OFFLINE_DESC')); ?>">
				<?php echo JText::_('COM_ACLMANAGER_ACTION_OFFLINE'); ?>
			</span>
		</th>
		<th width="7%" class="brlft">
			<span class="hasTip" title="<?php echo (JText::_('JACTION_ADMIN').'::'.JText::_('JACTION_ADMIN_COMPONENT_DESC')); ?>">
				<?php echo JText::_('JACTION_ADMIN'); ?>
			</span>
		</th>
		<th width="7%" class="brrgt">
			<span class="hasTip" title="<?php echo (JText::_('JACTION_MANAGE').'::'.JText::_('JACTION_MANAGE_COMPONENT_DESC')); ?>">
				<?php echo JText::_('JFIELD_ACCESS_LABEL'); ?>
			</span>
		</th>
		<th width="7%">
			<span class="hasTip" title="<?php echo (JText::_('JACTION_CREATE').'::'.JText::_('COM_CONFIG_ACTION_CREATE_DESC')); ?>">
				<?php echo JText::_('JACTION_CREATE'); ?>
			</span>
		</th>
		<th width="7%">
			<span class="hasTip" title="<?php echo (JText::_('JACTION_DELETE').'::'.JText::_('COM_CONFIG_ACTION_DELETE_DESC')); ?>">
				<?php echo JText::_('JACTION_DELETE'); ?>
			</span>
		</th>
		<th width="7%">
			<span class="hasTip" title="<?php echo (JText::_('JACTION_EDIT').'::'.JText::_('COM_CONFIG_ACTION_EDIT_DESC')); ?>">
				<?php echo JText::_('JACTION_EDIT'); ?>
			</span>
		</th>
		<th width="7%">
			<span class="hasTip" title="<?php echo (JText::_('JACTION_EDITSTATE').'::'.JText::_('COM_CONFIG_ACTION_EDITSTATE_DESC')); ?>">
				<?php echo JText::_('JACTION_EDITSTATE'); ?>
			</span>
		</th>
		<th width="7%">
			<span class="hasTip" title="<?php echo (JText::_('JACTION_EDITOWN').'::'.JText::_('COM_CONFIG_ACTION_EDITOWN_DESC')); ?>">
				<?php echo JText::_('JACTION_EDITOWN'); ?>
			</span>
		</th>
	</tr>
</thead>