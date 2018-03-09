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

<?php

/**
 * @copyright    Copyright (c) 2009-2017 Ryan Demmer. All rights reserved
 * @license    GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses
 */
defined('_JEXEC') or die('RESTRICTED');
?>
<div class="uk-form-row">
    <label for="style" class="hastip uk-form-label uk-width-1-5"
           title="<?php echo WFText::_('WF_LABEL_STYLE_DESC'); ?>"><?php echo WFText::_('WF_LABEL_STYLE'); ?></label>
    <div class="uk-form-controls uk-width-4-5"><input id="style" type="text" value=""/></div>
</div>
<div class="uk-form-row">
    <label for="classlist" class="hastip uk-form-label uk-width-1-5"
           title="<?php echo WFText::_('WF_LABEL_CLASSES_DESC'); ?>"><?php echo WFText::_('WF_LABEL_CLASSES'); ?></label>
    <div class="uk-form-controls uk-width-4-5 uk-datalist">
        <input id="classes" type="text" value=""/>
        <select id="classlist">
          <option value=""><?php echo WFText::_('WF_OPTION_NOT_SET'); ?></option>
        </select>
    </div>
</div>
<div class="uk-form-row">
    <label for="title" class="hastip uk-form-label uk-width-1-5"
           title="<?php echo WFText::_('WF_LABEL_TITLE_DESC'); ?>"><?php echo WFText::_('WF_LABEL_TITLE'); ?></label>
    <div class="uk-form-controls uk-width-4-5"><input id="title" type="text" value=""/></div>
</div>
<div class="uk-form-row">
    <label for="id" class="hastip uk-form-label uk-width-1-5"
           title="<?php echo WFText::_('WF_LABEL_ID_DESC'); ?>"><?php echo WFText::_('WF_LABEL_ID'); ?></label>
    <div class="uk-form-controls uk-width-4-5"><input id="id" type="text" value=""/></div>
</div>

<div class="uk-form-row">
    <label for="border" class="hastip uk-form-label uk-width-1-5" title="<?php echo WFText::_('WF_LABEL_BORDER_DESC'); ?>">
        <?php echo WFText::_('WF_LABEL_BORDER'); ?>
    </label>

    <div class="uk-form-controls uk-width-4-5">
        <div class="uk-form-controls uk-width-0-3 uk-margin-small-right uk-margin-small-top">
            <input type="checkbox" id="border" />
        </div>

        <div class="uk-width-1-5">
            <label for="border_width" class="hastip uk-form-label uk-width-4-10"
                   title="<?php echo WFText::_('WF_LABEL_BORDER_WIDTH_DESC'); ?>"><?php echo WFText::_('WF_LABEL_WIDTH'); ?></label>
            <div class="uk-form-controls uk-width-6-10 uk-datalist">
                <input type="text" id="border_width" />
                <select>
                    <option value="inherit"><?php echo WFText::_('WF_OPTION_NOT_SET'); ?></option>
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="thin"><?php echo WFText::_('WF_OPTION_BORDER_THIN'); ?></option>
                    <option value="medium"><?php echo WFText::_('WF_OPTION_BORDER_MEDIUM'); ?></option>
                    <option value="thick"><?php echo WFText::_('WF_OPTION_BORDER_THICK'); ?></option>
                </select>
            </div>
        </div>

        <div class="uk-width-1-5 uk-margin-left">
            <label for="border_style" class="hastip uk-form-label uk-width-4-10"
                   title="<?php echo WFText::_('WF_LABEL_BORDER_STYLE_DESC'); ?>"><?php echo WFText::_('WF_LABEL_STYLE'); ?></label>
            <div class="uk-form-controls uk-width-6-10">
                <select id="border_style">
                    <option value="inherit"><?php echo WFText::_('WF_OPTION_NOT_SET'); ?></option>
                    <option value="none"><?php echo WFText::_('WF_OPTION_BORDER_NONE'); ?></option>
                    <option value="solid"><?php echo WFText::_('WF_OPTION_BORDER_SOLID'); ?></option>
                    <option value="dashed"><?php echo WFText::_('WF_OPTION_BORDER_DASHED'); ?></option>
                    <option value="dotted"><?php echo WFText::_('WF_OPTION_BORDER_DOTTED'); ?></option>
                    <option value="double"><?php echo WFText::_('WF_OPTION_BORDER_DOUBLE'); ?></option>
                    <option value="groove"><?php echo WFText::_('WF_OPTION_BORDER_GROOVE'); ?></option>
                    <option value="inset"><?php echo WFText::_('WF_OPTION_BORDER_INSET'); ?></option>
                    <option value="outset"><?php echo WFText::_('WF_OPTION_BORDER_OUTSET'); ?></option>
                    <option value="ridge"><?php echo WFText::_('WF_OPTION_BORDER_RIDGE'); ?></option>
                </select>
            </div>
        </div>

        <div class="uk-width-1-3 uk-margin-left">
            <label for="border_color" class="hastip uk-form-label uk-width-4-10"
                   title="<?php echo WFText::_('WF_LABEL_BORDER_COLOR_DESC'); ?>"><?php echo WFText::_('WF_LABEL_COLOR'); ?></label>
            <div class="uk-form-controls uk-width-5-10">
                <input id="border_color" class="color" type="text" value="#000000" />
            </div>
        </div>
    </div>
</div>

<div class="uk-form-row">
    <label for="controller_height" class="hastip uk-form-label uk-width-1-5"
               title="<?php echo WFText::_('WF_LABEL_CONTROLLER_HEIGHT_DESC'); ?>"><?php echo WFText::_('WF_LABEL_CONTROLLER_HEIGHT'); ?></label>
    <div class="uk-form-controls uk-width-4-5">
        <input type="text" id="controller_height" value="" pattern="[0-9]+" />
    </div>
</div>
    <div class="uk-form-row">
    <label for="html" class="hastip uk-form-label uk-width-1-5"
               title="<?php echo WFText::_('WF_LABEL_HTML_DESC'); ?>"><?php echo WFText::_('WF_LABEL_HTML'); ?></label>
        <div class="uk-form-controls uk-width-4-5">
            <textarea id="html"></textarea>
        </div>
</div>
