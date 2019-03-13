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

<hr>
<div class="sidebar-content hidden-phone">
    <h4 class="page-header"><?php echo Text::_('COM_PWTACL_SIDEBAR_LEGEND'); ?></h4>
    <table id="legend">
        <tr class="hasTooltip" title="<?php echo Text::_('COM_PWTACL_SIDEBAR_NOT_ALLOWED_DESC'); ?>">
            <td class="legend-icon action">
                <span class="icon-not-ok"></span>
            </td>
            <td class="legend-title">
				<?php echo Text::_('COM_PWTACL_SIDEBAR_NOT_ALLOWED'); ?>
            </td>
        </tr>
        <tr class="hasTooltip" title="<?php echo Text::_('COM_PWTACL_SIDEBAR_DENIED_DESC'); ?>">
            <td class="legend-icon action denied">
                <span class="icon-not-ok"></span>
            </td>
            <td class="legend-title">
				<?php echo Text::_('COM_PWTACL_SIDEBAR_DENIED'); ?>
            </td>
        </tr>
        <tr class="hasTooltip" title="<?php echo Text::_('COM_PWTACL_SIDEBAR_INHERITED_DENIED_DESC'); ?>">
            <td class="legend-icon action">
                <span class="icon-lock"></span>
            </td>
            <td class="legend-title">
				<?php echo Text::_('COM_PWTACL_SIDEBAR_INHERITED_DENIED'); ?>
            </td>
        </tr>
        <tr class="hasTooltip" title="<?php echo Text::_('COM_PWTACL_SIDEBAR_ALLOWED_DESC'); ?>">
            <td class="legend-icon action allowed">
                <span class="icon-ok"></span>
            </td>
            <td class="legend-title">
				<?php echo Text::_('COM_PWTACL_SIDEBAR_ALLOWED'); ?>
            </td>
        </tr>
        <tr class="hasTooltip" title="<?php echo Text::_('COM_PWTACL_SIDEBAR_INHERITED_ALLOWED_DESC'); ?>">
            <td class="legend-icon action">
                <span class="icon-ok"></span>
            </td>
            <td class="legend-title">
				<?php echo Text::_('COM_PWTACL_SIDEBAR_INHERITED_ALLOWED'); ?>
            </td>
        </tr>
        <tr class="hasTooltip" title="<?php echo Text::_('COM_PWTACL_SIDEBAR_CONFLICT_DESC'); ?>">
            <td class="legend-icon action conflict">
                <span class="icon-warning"></span>
            </td>
            <td class="legend-title">
				<?php echo Text::_('COM_PWTACL_SIDEBAR_CONFLICT'); ?>
            </td>
        </tr>
    </table>
</div>
