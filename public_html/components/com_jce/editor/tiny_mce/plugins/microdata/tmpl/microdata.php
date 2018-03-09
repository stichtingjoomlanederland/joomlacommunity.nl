<?php
/**
 * @copyright 	Copyright (c) 2009-2017 Ryan Demmer. All rights reserved
 * @license   	GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses
 */
defined('_JEXEC') or die('RESTRICTED');
?>
    <div class="uk-form-row">
        <label for="itemtype" class="uk-form-label uk-width-3-10"><?php echo WFText::_('WF_LABEL_TYPE'); ?></label>
        <div class="uk-form-controls uk-width-7-10 uk-position-relative">
            <select id="itemtype">
                <option value="">-- <?php echo WFText::_('WF_OPTION_SELECT_TYPE'); ?> --</option>
            </select>
            <input type="text" disbabled value="<?php echo WFText::_('WF_LABEL_LOADING', 'Loading...'); ?>" />
        </div>
    </div>

    <div class="uk-form-row itemtype-options">
        <label><input type="radio" id="itemtype-replace" checked="checked" name="itemtype-option" class="uk-margin-small-right" /><?php echo WFText::_('WF_LABEL_REPLACE'); ?></label>
        <label><input type="radio" id="itemtype-new" name="itemtype-option" class="uk-margin-small-right" /><?php echo WFText::_('WF_LABEL_NEW'); ?></label>
    </div>

    <div class="uk-form-row">
        <label for="itemprop" class="uk-form-label uk-width-3-10"><?php echo WFText::_('WF_LABEL_PROPERTY'); ?></label>
        <div class="uk-form-controls uk-width-7-10">
            <select id="itemprop"></select>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="itemid" class="uk-form-label uk-width-3-10"><?php echo WFText::_('WF_LABEL_ID'); ?></label>
        <div class="uk-form-controls uk-width-7-10">
            <input type="text" value="" id="itemid" />
        </div>
    </div>
