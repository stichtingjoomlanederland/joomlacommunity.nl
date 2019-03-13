<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

// No direct access.
defined('_JEXEC') or die;

$group  = $displayData['group'];
$asset  = $displayData['asset'];
$action = $displayData['action'];
?>
<td
        class="<?php echo $action->class . ' ' . str_replace('.', '-', $action->name); ?>"
        data-groupid="<?php echo $group; ?>"
        data-assetid="<?php echo $asset->id; ?>"
        data-action="<?php echo $action->name; ?>"
        data-parentid="<?php echo $asset->parent_id; ?>"
        data-setting="<?php echo $action->setting; ?>"
        data-setting-calculated="<?php echo $action->setting_calculated; ?>"
        data-setting-parent="<?php echo $action->setting_parent; ?>"
>
    <span class="<?php echo $action->icon; ?>"></span>
</td>
