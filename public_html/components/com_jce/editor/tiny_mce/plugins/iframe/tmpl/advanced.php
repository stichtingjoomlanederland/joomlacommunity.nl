<?php

/**
 * @copyright    Copyright (c) 2009-2019 Ryan Demmer. All rights reserved
 * @license    GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses
 */
defined('JPATH_PLATFORM') or die;
?>
<div class="uk-form-row">
    <label for="style" class="hastip uk-form-label uk-width-1-5" title="<?php echo JText::_('WF_LABEL_STYLE_DESC'); ?>">
            <?php echo JText::_('WF_LABEL_STYLE'); ?>
        </label>

    <div class="uk-form-controls uk-width-4-5">
        <input id="style" type="text" value="" />
    </div>
</div>
<div class="uk-form-row">
    <label class="uk-form-label uk-width-1-5" for="classes" class="hastip" title="<?php echo JText::_('WF_LABEL_CLASSES_DESC'); ?>"><?php echo JText::_('WF_LABEL_CLASSES'); ?></label>
    <div class="uk-form-controls uk-width-4-5 uk-datalist">
        <input type="text" id="classes" value="" />
        <select id="classlist">
          <option value=""><?php echo JText::_('WF_OPTION_NOT_SET'); ?></option>
        </select>
    </div>
</div>
<div class="uk-form-row">
    <label for="title" class="hastip uk-form-label uk-width-1-5" title="<?php echo JText::_('WF_LABEL_TITLE_DESC'); ?>">
            <?php echo JText::_('WF_LABEL_TITLE'); ?>
        </label>

    <div class="uk-form-controls uk-width-4-5">
        <input id="title" type="text" value=""/>
    </div>
</div>
<div class="uk-form-row">
    <label for="name" class="hastip uk-form-label uk-width-1-5" title="<?php echo JText::_('WF_LABEL_NAME_DESC'); ?>">
            <?php echo JText::_('WF_LABEL_NAME'); ?>
        </label>

    <div class="uk-form-controls uk-width-4-5">
        <input id="name" type="text" value=""/>
    </div>
</div>
<div class="uk-form-row">
    <label for="id" class="hastip uk-form-label uk-width-1-5" title="<?php echo JText::_('WF_LABEL_ID_DESC'); ?>">
            <?php echo JText::_('WF_LABEL_ID'); ?>
        </label>

    <div class="uk-form-controls uk-width-4-5">
        <input id="id" type="text" value=""/>
    </div>
</div>
<div class="uk-form-row">
    <label for="allowtransparency" class="hastip uk-form-label uk-width-1-5"
               title="<?php echo JText::_('WF_IFRAME_ALLOWTRANSPARENCY_DESC'); ?>">
            <?php echo JText::_('WF_IFRAME_ALLOWTRANSPARENCY'); ?>
        </label>

    <div class="uk-form-controls uk-width-1-5">
        <select id="allowtransparency">
            <option value="no">
                <?php echo JText::_('JNO'); ?>
            </option>
            <option value="yes">
                <?php echo JText::_('JYES'); ?>
            </option>
        </select>
    </div>
</div>
<div class="uk-form-row">
    <label for="html" class="hastip uk-form-label uk-width-1-5" title="<?php echo JText::_('WF_LABEL_HTML_DESC'); ?>">
            <?php echo JText::_('WF_LABEL_HTML'); ?>
        </label>

    <div class="uk-form-controls uk-width-4-5">
        <textarea id="html" value=""></textarea>
    </div>
</div>
