<?php
/**
 * @package		SP Upgrade
 * @subpackage	Components
 * @copyright	KAINOTOMO PH LTD - All rights reserved.
 * @author		KAINOTOMO PH LTD
 * @link		http://www.kainotomo.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
?>

<div class="btn-group" id="toolbar">
    <a class="btn btn-primary" href="#" onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{if (window.parent) window.parent.jSelectItem('<?php echo $this->prefix; ?>', '<?php echo $this->name; ?>', findChecked());}" class="toolbar">
        <?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?>
    </a>
</div>
<div class="clr"> </div>

<form action="<?php echo JRoute::_('index.php?option=com_spupgrade&amp;view=table&amp;layout=default&amp;tmpl=component&amp;prefix=' . $this->prefix . '&amp;name=' . $this->name); ?>" method="post" name="adminForm" id="adminForm">
    <table class="table table-striped">
        <thead>
            <tr>
                <th width="1%">                    
                </th>
                <th width="1%">
                    <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                </th>
                <?php foreach ($this->items[0] as $i => $item) : ?>
                    <?php if ($i != 'sp_id') echo '<th>' . $i . '</th>'; ?>                    
                <?php endforeach; ?>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this->items as $i => $item) : ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="width-1">
                        <?php echo $item->sp_id; ?>
                    </td>
                    <td class="center">
                        <?php echo JHtml::_('grid.id', $item->sp_id, $item->sp_id); ?>
                    </td>
                    <?php foreach ($item as $j => $value) : ?>
                        <?php if ($j != 'sp_id') echo '<td>' . $value . '</td>'; ?>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
        
        <tfoot>
            <tr>
                <td colspan="<?php echo count(JArrayHelper::fromObject($this->items[0]));?>">
                    <div class="btn-group pull-left hidden-phone">
                        <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
                        <?php echo $this->pagination->getLimitBox(); ?>
                    </div>
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
    </table>

    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
