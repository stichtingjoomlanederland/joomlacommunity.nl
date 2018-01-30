<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2018 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access'); ?>

<table width="100%" class="com-rsform-table-props table table-bordered adminlist">
    <tr>
        <td width="1%" nowrap="nowrap" valign="top"><strong><?php echo JText::_('RSFP_SUBM_DIR_CAN_EDIT'); ?></strong></td>
        <td>
            <div class="control-group">
                <div class="controls">
                    <label for="own" class="checkbox">
                        <input type="checkbox" id="own" value="own" name="jform[groups][]" <?php if (in_array('own',$this->directory->groups)) echo 'checked="checked"'; ?> />
                        <?php echo JText::_('RSFP_SUBM_DIR_EDIT_OWN_SUBMISSIONS'); ?>
                    </label>
                </div>
            </div>
            <?php echo JHtml::_('access.usergroups', 'jform[groups]', $this->directory->groups, true); ?>
        </td>
    </tr>
    <tr>
        <td width="1%" nowrap="nowrap" valign="top"><strong><?php echo JText::_('RSFP_SUBM_DIR_CAN_DELETE'); ?></strong></td>
        <td>
            <div class="control-group">
                <div class="controls">
                    <label for="deletionown" class="checkbox">
                        <input type="checkbox" id="deletionown" value="own" name="jform[DeletionGroups][]" <?php if (in_array('own',$this->directory->DeletionGroups)) echo 'checked="checked"'; ?> />
                        <?php echo JText::_('RSFP_SUBM_DIR_DELETE_OWN_SUBMISSIONS'); ?>
                    </label>
                </div>
            </div>
            <?php echo JHtml::_('access.usergroups', 'jform[DeletionGroups]', $this->directory->DeletionGroups, true); ?>
        </td>
    </tr>
</table>