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
	<th width="23%"><?php echo Text::_('COM_PWTACL_TABLE_ASSET_TITLE'); ?></th>

	<th width="12%" class="border-left">
        <span class="hasTooltip" title="<?php echo Text::_('COM_CONFIG_ACTION_CREATE_DESC'); ?>">
            <?php echo Text::_('JACTION_CREATE'); ?>
        </span>
	</th>
	<th width="12%">
        <span class="hasTooltip" title="<?php echo Text::_('COM_CONFIG_ACTION_DELETE_DESC'); ?>">
            <?php echo Text::_('JACTION_DELETE'); ?>
        </span>
	</th>
	<th width="12%">
        <span class="hasTooltip" title="<?php echo Text::_('COM_CONFIG_ACTION_EDIT_DESC'); ?>">
            <?php echo Text::_('JACTION_EDIT'); ?>
        </span>
	</th>
	<th width="12%">
        <span class="hasTooltip" title="<?php echo Text::_('COM_CONFIG_ACTION_EDITSTATE_DESC'); ?>">
            <?php echo Text::_('JACTION_EDITSTATE'); ?>
        </span>
	</th>
	<th width="12%">
        <span class="hasTooltip" title="<?php echo Text::_('COM_CONFIG_ACTION_EDITOWN_DESC'); ?>">
            <?php echo Text::_('JACTION_EDITOWN'); ?>
        </span>
	</th>
	<th width="12%">
        <span class="hasTooltip" title="<?php echo Text::_('COM_CONFIG_ACTION_EDITVALUE_DESC'); ?>">
            <?php echo Text::_('JACTION_EDITVALUE'); ?>
        </span>
	</th>
	<th width="5%" class="nowrap brlft border-left"></th>
</tr>
</thead>