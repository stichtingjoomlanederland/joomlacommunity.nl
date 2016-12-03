<?php
/**
 * @package		SP Upgrade
 * @subpackage	Components
 * @copyright	KAINOTOMO PH LTD - All rights reserved.
 * @author		KAINOTOMO PH LTD
 * @link		http://www.kainotomo.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<thead>
    <tr>
        <th width="1%">
            <?php echo JText::_('JGLOBAL_FIELD_ID_LABEL'); ?>
        </th>
        <th width="20">
            <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
        </th>
        <th class="left">
            <?php echo JText::_('COM_SPUPGRADE_FIELD_EXTENSION_EXTENSION_LABEL'); ?>
        </th>
        <th class="left">
            <?php echo JText::_('COM_SPUPGRADE_FIELD_EXTENSION_DESCRIPTION_LABEL'); ?>
        </th>
        <th class="center">
            <?php echo JText::_('COM_SPUPGRADE_FIELD_IDS_BATCH_DESCRIPTION_LABEL'); ?>
        </th>
    </th>
    <th width="20%">

    </th>
</tr>
</thead>
