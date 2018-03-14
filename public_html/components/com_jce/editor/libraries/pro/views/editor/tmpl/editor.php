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
<form onsubmit="return false;">
    <div id="editor" class="offleft uk-position-cover uk-grid uk-grid-small">
        <div class="uk-width-7-10 uk-width-xlarge-8-10">
          <div id="editor-image" class="uk-placeholder uk-position-cover"><!-- Edited image goes here --></div>
        </div>
        <div class="uk-width-3-10 uk-width-xlarge-2-10">
          <div id="editor-tools" class="uk-position-cover">
              <div id="tabs">
                  <ul>
                      <li><a href="#transform_tab"><?php echo WFText::_('WF_MANAGER_EDITOR_TRANSFORM', 'Transform'); ?></a></li>
                      <li><a href="#effects_tab"><?php echo WFText::_('WF_MANAGER_EDITOR_EFFECTS', 'Effects'); ?></a></li>
                  </ul>
                  <div id="transform_tab">
                      <?php
                      echo $this->loadTemplate('resize');
                      echo $this->loadTemplate('crop');
                      echo $this->loadTemplate('rotate');
                      ?>
                  </div>
                  <div id="effects_tab">
                      <?php
                      echo $this->loadTemplate('effects');
                      ?>
                  </div>
              </div>
          </div>
        </div>

    </div>
    <div class="actionPanel uk-modal-footer">
        <button class="revert uk-button"><i class="uk-icon-refresh uk-margin-small-right"></i><?php echo WFText::_('WF_LABEL_REVERT'); ?></button>
        <button class="undo uk-button"><i class="uk-icon-undo uk-margin-small-right"></i><?php echo WFText::_('WF_LABEL_UNDO'); ?></button>
        <button class="save uk-button uk-button-primary"><i class="uk-icon-check uk-margin-small-right"></i><?php echo WFText::_('WF_LABEL_SAVE'); ?></button>
    </div>
    <input type="hidden" name="<?php echo WFToken::getToken(); ?>" value="1" />
</form>
<!-- SVG Filters -->
<?php echo $this->loadTemplate('svg'); ?>
