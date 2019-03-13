<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\Language\Text;

// No direct access.
defined('_JEXEC') or die;
?>

<thead>
<tr>
    <th rowspan="2" width="12%"><?php echo Text::_('COM_PWTACL_TABLE_ASSET_TITLE'); ?></th>
    <th colspan="3" width="20,25%" class="border-left"><?php echo Text::_('COM_PWTACL_TABLE_ACTION_LOGIN'); ?></th>
    <th colspan="3" width="20,25%" class="brlft border-left"><?php echo Text::_('COM_PWTACL_TABLE_ACTION_EXTENSION'); ?></th>
    <th colspan="6" width="40.5%" class="border-left"><?php echo Text::_('COM_PWTACL_TABLE_ACTION_OBJECT'); ?></th>
    <th rowspan="2" width="4%" class="nowrap brlft border-left"></th>
    <th rowspan="2" width="3%" class="nowrap brlft border-left"><?php echo Text::_('JGLOBAL_FIELD_ID_LABEL'); ?></th>
</tr>
<tr>
    <th width="6.75%" class="border-left">
		<span class="hasTooltip" title="<?php echo Text::_('COM_CONFIG_ACTION_LOGIN_SITE_DESC'); ?>">
			<?php echo Text::_('JSITE'); ?>
		</span>
    </th>
    <th width="6.75%">
		<span class="hasTooltip" title="<?php echo Text::_('COM_CONFIG_ACTION_LOGIN_ADMIN_DESC'); ?>">
			<?php echo Text::_('COM_PWTACL_TABLE_ACTION_ADMIN'); ?>
		</span>
    </th>
    <th width="6.75%">
		<span class="hasTooltip" title="<?php echo Text::_('COM_CONFIG_ACTION_LOGIN_OFFLINE_DESC'); ?>">
			<?php echo Text::_('COM_PWTACL_TABLE_ACTION_OFFLINE'); ?>
		</span>
    </th>
    <th width="6.75%" class="brlft border-left">
		<span class="hasTooltip" title="<?php echo Text::_('JACTION_ADMIN_COMPONENT_DESC'); ?>">
			<?php echo Text::_('JACTION_ADMIN'); ?>
		</span>
    </th>
    <th width="6.75%">
        <span class="hasTooltip" title="<?php echo Text::_('JACTION_OPTIONS_COMPONENT_DESC'); ?>">
            <?php echo Text::_('JACTION_OPTIONS'); ?>
        </span>
    </th>
    <th width="6.75%">
		<span class="hasTooltip" title="<?php echo Text::_('JACTION_MANAGE_COMPONENT_DESC'); ?>">
			<?php echo Text::_('JFIELD_ACCESS_LABEL'); ?>
		</span>
    </th>
    <th width="6.75%" class="border-left">
		<span class="hasTooltip" title="<?php echo Text::_('COM_CONFIG_ACTION_CREATE_DESC'); ?>">
			<?php echo Text::_('JACTION_CREATE'); ?>
		</span>
    </th>
    <th width="6.75%">
		<span class="hasTooltip" title="<?php echo Text::_('COM_CONFIG_ACTION_DELETE_DESC'); ?>">
			<?php echo Text::_('JACTION_DELETE'); ?>
		</span>
    </th>
    <th width="6.75%">
		<span class="hasTooltip" title="<?php echo Text::_('COM_CONFIG_ACTION_EDIT_DESC'); ?>">
			<?php echo Text::_('JACTION_EDIT'); ?>
		</span>
    </th>
    <th width="6.75%">
		<span class="hasTooltip" title="<?php echo Text::_('COM_CONFIG_ACTION_EDITSTATE_DESC'); ?>">
			<?php echo Text::_('JACTION_EDITSTATE'); ?>
		</span>
    </th>
    <th width="6.75%">
		<span class="hasTooltip" title="<?php echo Text::_('COM_CONFIG_ACTION_EDITOWN_DESC'); ?>">
			<?php echo Text::_('JACTION_EDITOWN'); ?>
		</span>
    </th>
    <th width="6.75%">
		<span class="hasTooltip" title="<?php echo Text::_('COM_CONFIG_ACTION_EDITVALUE_DESC'); ?>">
			<?php echo Text::_('JACTION_EDITVALUE'); ?>
		</span>
    </th>
</tr>
</thead>