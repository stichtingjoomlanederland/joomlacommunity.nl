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
JHtml::_('behavior.modal');
?>
<tbody>
    <?php foreach ($this->items as $i => $item): ?>
        <tr class="row<?php echo $i % 2; ?>">
            <td>
                <?php echo $i; ?>
            </td>
            <td>
                <?php if ($item->extension_name != 'com_media') : ?>
                    <?php echo JHtml::_('grid.id', $i, $item->id); ?>            
                <?php else: ?>
                    <?php if ($this->ftpConnection) : ?>
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>            
                    <?php endif; ?>
                <?php endif; ?>            
            </td>	
            <td class="left" id="names<?php echo $i;?>">
                <?php echo JText::_($item->extension_name); ?>
                <?php echo ' -> '; ?>
                <?php echo JText::_($item->extension_name . '_' . $item->name); ?>
            </td>
            <td class="left">
                <?php echo JText::_($item->extension_name . '_' . $item->name . '_desc'); ?>
            </td>
            <td class="center">             
                <?php if ($item->extension_name == 'com_media') : ?>
                    <?php if (($item->extension_name . '_' . $item->name) == 'com_media_images') : ?>
                        <?php if (!$this->ftpConnection) : ?>
                            <?php echo JText::_('COM_SPUPGRADE_MSG_ERROR_FTP_CONNECTION'); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (($item->extension_name . '_' . $item->name) == 'com_media_template') : ?>
                        <?php if ($this->ftpConnection) : ?>
                            <label class="hasTip" title="<?php echo JText::_('COM_SPUPGRADE_TEMPLATE_NAME_DESC'); ?>"><?php echo JText::_('COM_SPUPGRADE_TEMPLATE_NAME_LABEL'); ?></label><br/>
                            <input type="text" name="input_template" id="input_template" value="" class="inputbox" size="45" aria-invalid="false">
                        <?php endif; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <input type="text" name="input_ids[]" id="input_ids<?php echo $i;?>" value="" class="inputbox" size="45" aria-invalid="false">
                <?php endif; ?>            
                <input type="hidden" name="task_ids[]" value="<?php echo $item->id; ?>" >
                <input type="hidden" name="status[]" id="status<?php echo $i;?>" value="" >
            </td>
            <td class="right">
                <div class="btn-group">
                    <?php if (($item->extension_name . '_' . $item->name) == 'com_users_users') : ?>                
                        <?php $pk = 'id'; ?><a class="btn btn-mini btn-primary modal" title="<?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?>" href="<?php echo JRoute::_('index.php?option=com_spupgrade&amp;view=component&amp;layout=default&amp;tmpl=component&amp;pk=' . $pk . '&amp;extension_name=' . $item->extension_name . '&amp;name=' . $item->name . '&amp;cid=' . $i); ?>" onclick="return false;" rel="{handler: 'iframe', size: {x: 900, y: 400}}"><?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?></a>
                    <?php endif; ?>
                    <?php if (($item->extension_name . '_' . $item->name) == 'com_content_sections') : ?>
                        <?php $pk = 'id'; ?><a class="btn btn-mini btn-primary modal" title="<?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?>" href="<?php echo JRoute::_('index.php?option=com_spupgrade&amp;view=component&amp;layout=default&amp;tmpl=component&amp;pk=' . $pk . '&amp;extension_name=' . $item->extension_name . '&amp;name=' . $item->name . '&amp;cid=' . $i); ?>" onclick="return false;" rel="{handler: 'iframe', size: {x: 900, y: 400}}"><?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?></a>
                    <?php endif; ?>
                    <?php if (($item->extension_name . '_' . $item->name) == 'com_content_categories') : ?>
                        <?php $pk = 'id'; ?><a class="btn btn-mini btn-primary modal" title="<?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?>" href="<?php echo JRoute::_('index.php?option=com_spupgrade&amp;view=component&amp;layout=default&amp;tmpl=component&amp;pk=' . $pk . '&amp;extension_name=' . $item->extension_name . '&amp;name=' . $item->name . '&amp;cid=' . $i); ?>" onclick="return false;" rel="{handler: 'iframe', size: {x: 900, y: 400}}"><?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?></a>
                    <?php endif; ?>
                    <?php if (($item->extension_name . '_' . $item->name) == 'com_content_content') : ?>
                        <?php $pk = 'id'; ?><a class="btn btn-mini btn-primary modal" title="<?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?>" href="<?php echo JRoute::_('index.php?option=com_spupgrade&amp;view=component&amp;layout=default&amp;tmpl=component&amp;pk=' . $pk . '&amp;extension_name=' . $item->extension_name . '&amp;name=' . $item->name . '&amp;cid=' . $i); ?>" onclick="return false;" rel="{handler: 'iframe', size: {x: 900, y: 400}}"><?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?></a>
                    <?php endif; ?>
                    <?php if (($item->extension_name . '_' . $item->name) == 'com_contact_categories') : ?>
                        <?php $pk = 'id'; ?><a class="btn btn-mini btn-primary modal" title="<?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?>" href="<?php echo JRoute::_('index.php?option=com_spupgrade&amp;view=component&amp;layout=default&amp;tmpl=component&amp;pk=' . $pk . '&amp;extension_name=' . $item->extension_name . '&amp;name=' . $item->name . '&amp;cid=' . $i); ?>" onclick="return false;" rel="{handler: 'iframe', size: {x: 900, y: 400}}"><?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?></a>
                    <?php endif; ?>                
                    <?php if (($item->extension_name . '_' . $item->name) == 'com_contact_contact_details') : ?>
                        <?php $pk = 'id'; ?><a class="btn btn-mini btn-primary modal" title="<?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?>" href="<?php echo JRoute::_('index.php?option=com_spupgrade&amp;view=component&amp;layout=default&amp;tmpl=component&amp;pk=' . $pk . '&amp;extension_name=' . $item->extension_name . '&amp;name=' . $item->name . '&amp;cid=' . $i); ?>" onclick="return false;" rel="{handler: 'iframe', size: {x: 900, y: 400}}"><?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?></a>
                    <?php endif; ?>
                    <?php if (($item->extension_name . '_' . $item->name) == 'com_weblinks_categories') : ?>
                        <?php $pk = 'id'; ?><a class="btn btn-mini btn-primary modal" title="<?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?>" href="<?php echo JRoute::_('index.php?option=com_spupgrade&amp;view=component&amp;layout=default&amp;tmpl=component&amp;pk=' . $pk . '&amp;extension_name=' . $item->extension_name . '&amp;name=' . $item->name . '&amp;cid=' . $i); ?>" onclick="return false;" rel="{handler: 'iframe', size: {x: 900, y: 400}}"><?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?></a>
                    <?php endif; ?>
                    <?php if (($item->extension_name . '_' . $item->name) == 'com_weblinks_weblinks') : ?>
                        <?php $pk = 'id'; ?><a class="btn btn-mini btn-primary modal" title="<?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?>" href="<?php echo JRoute::_('index.php?option=com_spupgrade&amp;view=component&amp;layout=default&amp;tmpl=component&amp;pk=' . $pk . '&amp;extension_name=' . $item->extension_name . '&amp;name=' . $item->name . '&amp;cid=' . $i); ?>" onclick="return false;" rel="{handler: 'iframe', size: {x: 900, y: 400}}"><?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?></a>
                    <?php endif; ?>
                    <?php if (($item->extension_name . '_' . $item->name) == 'com_newsfeeds_categories') : ?>
                        <?php $pk = 'id'; ?><a class="btn btn-mini btn-primary modal" title="<?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?>" href="<?php echo JRoute::_('index.php?option=com_spupgrade&amp;view=component&amp;layout=default&amp;tmpl=component&amp;pk=' . $pk . '&amp;extension_name=' . $item->extension_name . '&amp;name=' . $item->name . '&amp;cid=' . $i); ?>" onclick="return false;" rel="{handler: 'iframe', size: {x: 900, y: 400}}"><?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?></a>
                    <?php endif; ?>
                    <?php if (($item->extension_name . '_' . $item->name) == 'com_newsfeeds_newsfeeds') : ?>
                        <?php $pk = 'id'; ?><a class="btn btn-mini btn-primary modal" title="<?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?>" href="<?php echo JRoute::_('index.php?option=com_spupgrade&amp;view=component&amp;layout=default&amp;tmpl=component&amp;pk=' . $pk . '&amp;extension_name=' . $item->extension_name . '&amp;name=' . $item->name . '&amp;cid=' . $i); ?>" onclick="return false;" rel="{handler: 'iframe', size: {x: 900, y: 400}}"><?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?></a>
                    <?php endif; ?>
                    <?php if (($item->extension_name . '_' . $item->name) == 'com_banners_categories') : ?>
                        <?php $pk = 'id'; ?><a class="btn btn-mini btn-primary modal" title="<?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?>" href="<?php echo JRoute::_('index.php?option=com_spupgrade&amp;view=component&amp;layout=default&amp;tmpl=component&amp;pk=' . $pk . '&amp;extension_name=' . $item->extension_name . '&amp;name=' . $item->name . '&amp;cid=' . $i); ?>" onclick="return false;" rel="{handler: 'iframe', size: {x: 900, y: 400}}"><?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?></a>
                    <?php endif; ?>
                    <?php if (($item->extension_name . '_' . $item->name) == 'com_banners_banner_clients') : ?>
                        <?php $pk = 'cid'; ?><a class="btn btn-mini btn-primary modal" title="<?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?>" href="<?php echo JRoute::_('index.php?option=com_spupgrade&amp;view=component&amp;layout=default&amp;tmpl=component&amp;pk=' . $pk . '&amp;extension_name=' . $item->extension_name . '&amp;name=bannerclient&amp;cid=' . $i); ?>" onclick="return false;" rel="{handler: 'iframe', size: {x: 900, y: 400}}"><?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?></a>
                    <?php endif; ?>
                    <?php if (($item->extension_name . '_' . $item->name) == 'com_banners_banners') : ?>
                        <?php $pk = 'bid'; ?><a class="btn btn-mini btn-primary modal" title="<?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?>" href="<?php echo JRoute::_('index.php?option=com_spupgrade&amp;view=component&amp;layout=default&amp;tmpl=component&amp;pk=' . $pk . '&amp;extension_name=' . $item->extension_name . '&amp;name=banner&amp;cid=' . $i); ?>" onclick="return false;" rel="{handler: 'iframe', size: {x: 900, y: 400}}"><?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?></a>
                    <?php endif; ?>
                    <?php if (($item->extension_name . '_' . $item->name) == 'com_menus_menu_types') : ?>
                        <?php $pk = 'id'; ?><a class="btn btn-mini btn-primary modal" title="<?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?>" href="<?php echo JRoute::_('index.php?option=com_spupgrade&amp;view=component&amp;layout=default&amp;tmpl=component&amp;pk=' . $pk . '&amp;extension_name=' . $item->extension_name . '&amp;name=' . $item->name . '&amp;cid=' . $i); ?>" onclick="return false;" rel="{handler: 'iframe', size: {x: 900, y: 400}}"><?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?></a>
                    <?php endif; ?>
                    
                    <?php if (($item->extension_name . '_' . $item->name) == 'com_menus_menu') : ?>
                        <select id="all_menus" name="all_menus" class="inputbox input-small hasTip" aria-invalid="false" title="<?php echo JText::_('COM_SPUPGRADE_MENUS_ALL_TIP'); ?>">
                            <optgroup id="all_menus" label="<?php echo JText::_('COM_SPUPGRADE_MENUS_ALL'); ?>">
                                <option value="0" selected="selected"><?php echo JText::_('JYES'); ?></option>
                                <option value="1"><?php echo JText::_('JNO'); ?></option>
                            </optgroup>
                        </select>
                        <?php $pk = 'id'; ?><a class="btn btn-mini btn-primary" title="<?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?>" href="<?php echo JRoute::_('index.php?option=com_spupgrade&amp;view=component&amp;layout=default&amp;tmpl=component&amp;pk=' . $pk . '&amp;extension_name=' . $item->extension_name . '&amp;name=' . $item->name . '&amp;cid=' . $i); ?>" onclick="return false;" rel="{handler: 'iframe', size: {x: 900, y: 400}}"><?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?></a>
                    <?php endif; ?>
                    <?php if (($item->extension_name . '_' . $item->name) == 'com_modules_modules') : ?>
                        <select id="all_modules" name="all_modules" class="inputbox input-small hasTip" aria-invalid="false" title="<?php echo JText::_('COM_SPUPGRADE_MODULES_ALL_TIP'); ?>">                    
                            <optgroup id="all_modules" label="<?php echo JText::_('COM_SPUPGRADE_MODULES_ALL'); ?>">
                                <option value="0" selected="selected"><?php echo JText::_('JYES'); ?></option>
                                <option value="1"><?php echo JText::_('JNO'); ?></option>
                            </optgroup>
                        </select>
                        <?php $pk = 'id'; ?><a class="btn btn-mini btn-primary" title="<?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?>" href="<?php echo JRoute::_('index.php?option=com_spupgrade&amp;view=component&amp;layout=default&amp;tmpl=component&amp;pk=' . $pk . '&amp;extension_name=' . $item->extension_name . '&amp;name=' . $item->name . '&amp;cid=' . $i); ?>" onclick="return false;" rel="{handler: 'iframe', size: {x: 900, y: 400}}"><?php echo JText::_('COM_SPUPGRADE_CHOOSE'); ?></a>
                    <?php endif; ?>
                    
                    <?php if ($item->extension_name != 'com_media') : ?>                
                        <a class="btn btn-mini" title="<?php echo JText::_('JCLEAR'); ?>" href="#" onclick="jClearItem('<?php echo $i; ?>');"><?php echo JText::_('JCLEAR'); ?></a>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>