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
<div class="source-editor">
  <div class="uk-navbar uk-navbar-attached">
    <div class="uk-navbar-content uk-padding-remove">
        <button class="uk-button" data-action="undo" title="<?php echo WFText::_('WF_SOURCE_UNDO', 'Undo'); ?>"><i class="uk-icon uk-icon-undo"></i></button>
        <button class="uk-button" data-action="redo" title="<?php echo WFText::_('WF_SOURCE_REDO', 'Redo'); ?>"><i class="uk-icon uk-icon-redo"></i></button>

        <button class="uk-button uk-button-checkbox" data-action="highlight" title="<?php echo WFText::_('WF_SOURCE_HIGHLIGHT', 'Highlight'); ?>"><i class="uk-icon uk-icon-highlight"></i></button>
        <button class="uk-button uk-button-checkbox" data-action="linenumbers" title="<?php echo WFText::_('WF_SOURCE_NUMBERS', 'Line Numbers'); ?>"><i class="uk-icon uk-icon-linenumbers"></i></button>
        <button class="uk-button uk-button-checkbox" data-action="wrap" title="<?php echo WFText::_('WF_SOURCE_WRAP', 'Wrap Lines'); ?>"><i class="uk-icon uk-icon-wrap"></i></button>
        <button class="uk-button" data-action="format" title="<?php echo WFText::_('WF_SOURCE_FORMAT', 'Format Code'); ?>"><i class="uk-icon uk-icon-format"></i></button>

        <button class="uk-button uk-button-checkbox" data-action="fullscreen" title="<?php echo WFText::_('WF_SOURCE_FULLSCREEN', 'Fullscreen'); ?>"><i class="uk-icon uk-icon-fullscreen"></i></button>
    </div>
    <div class="uk-navbar-content uk-navbar-flip uk-padding-left-remove">
      <div class="uk-form uk-margin-remove uk-display-inline-block">
        <input id="source_search_value" placeholder="<?php echo WFText::_('WF_SOURCE_SEARCH', 'Search'); ?>" type="text" />
        <button class="uk-button" data-action="search" title="<?php echo WFText::_('WF_SOURCE_SEARCH', 'Search'); ?>"><i class="uk-icon uk-icon-search"></i></button>
        <button class="uk-button" data-action="search-previous" title="<?php echo WFText::_('WF_SOURCE_SEARCH_PREV', 'Search Previous'); ?>"><i class="uk-icon uk-icon-search-previous"></i></button>
      </div>

        <div class="uk-form uk-margin-remove uk-display-inline-block">
          <input id="source_replace_value" placeholder="<?php echo WFText::_('WF_SOURCE_REPLACE', 'Replace'); ?>" type="text" />
          <button class="uk-button" data-action="replace" title="<?php echo WFText::_('WF_SOURCE_REPLACE', 'Replace'); ?>"><i class="uk-icon uk-icon-replace"></i></button>
          <button class="uk-button" data-action="replace-all" title="<?php echo WFText::_('WF_SOURCE_REPLACE_ALL', 'Replace All'); ?>"><i class="uk-icon uk-icon-replace-all"></i></button>
          <label><input type="checkbox" id="source_search_regex" /><?php echo WFText::_('WF_SOURCE_SOURCE_REGEX', 'Regular Expression'); ?></label>
        </div>
    </div>
  </div>
  <div class="source-editor-container"></div>
</div>
