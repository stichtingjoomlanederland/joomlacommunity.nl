<?php
/**
 * @version		$Id: default.php 21837 2011-07-12 18:12:35Z dextercowley $
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

//JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.modal', 'a.modal');

$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_spupgrade&view=log'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="btn-toolbar">
        <div class="btn-group pull-left" style="display: none;">
            <label class="filter-hide-lbl" for="filter_begin"><?php echo JText::_('COM_SPUPGRADE_BEGIN_LABEL'); ?></label>
            <?php echo JHtml::_('calendar', $this->state->get('filter.begin'), 'filter_begin', 'filter_begin', '%Y-%m-%d', array('size' => 10, 'onchange' => "this.form.fireEvent('submit');this.form.submit()")); ?>

            <label class="filter-hide-lbl" for="filter_end"><?php echo JText::_('COM_SPUPGRADE_END_LABEL'); ?></label>
            <?php echo JHtml::_('calendar', $this->state->get('filter.end'), 'filter_end', 'filter_end', '%Y-%m-%d', array('size' => 10, 'onchange' => "this.form.fireEvent('submit');this.form.submit()")); ?>
        </div>
        <div class="btn-group pull-right">
            <select name="filter_tables_id" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('COM_SPUPGRADE_SELECT_TABLES_ID'); ?></option>
                <?php echo JHtml::_('select.options', JFormFieldTables::getOptions(), 'value', 'text', $this->state->get('filter.tables_id')); ?>
            </select>
            <select name="filter_state" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('COM_SPUPGRADE_SELECT_STATES_ID'); ?></option>
                <?php echo JHtml::_('select.options', JFormFieldTables::getStates(), 'value', 'text', $this->state->get('filter.state')); ?>
            </select>
        </div>
    </div>
    <div class="clearfix"> </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th width="20">
                    <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
                </th>
                <th class="title">
                    <?php echo JText::_('COM_SPUPGRADE_FIELD_EXTENSION_EXTENSION_LABEL'); ?>
                </th>
                <th class="title" width="20%">
                    <?php echo JText::_('COM_SPUPGRADE_FIELD_EXTENSION_STATE_LABEL'); ?>
                </th>
                <th width="40%" class="nowrap">
                    <?php echo JText::_('COM_SPUPGRADE_FIELD_EXTENSION_NOTE_LABEL'); ?>
                </th>
                <th width="10%" class="nowrap">
                    <?php echo JText::_('COM_SPUPGRADE_FIELD_EXTENSION_SOURCE_ID_LABEL'); ?>
                </th>
                <th width="10%" class="nowrap">
                    <?php echo JText::_('COM_SPUPGRADE_FIELD_EXTENSION_DESTINATION_ID_LABEL'); ?>
                </th>
                <th width="10%" class="nowrap hide">
                    <?php echo JHtml::_('grid.sort', 'JDATE', 'created', $listDirn, $listOrder); ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="6">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
            <?php foreach ($this->items as $i => $item) : ?>            
                <tr class="row<?php echo $i % 2; ?>">
                    <td>
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>            
                    </td>
                    <td class="left">
                        <?php echo $item->extension_name . '_' . $item->name; ?>
                    </td>
                    <td class="left">
                        <?php echo JText::_('COM_SPUPGRADE_STATE_' . $item->state); ?>
                    </td>
                    <td class="left">
                        <?php echo $item->note; ?>
                    </td>
                    <td class="center">
                        <?php echo $item->source_id; ?>
                    </td>
                    <td class="center">
                        <?php echo $item->destination_id; ?>
                    </td>
                    <td class="center hide">
                        <?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4') . ' H:i'); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </div>

</form>
