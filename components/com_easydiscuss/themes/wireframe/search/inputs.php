<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>

<form name="discuss-search" method="post" action="<?php echo JRoute::_('index.php');?>" data-search-form>
    <div class="input-group">
        <input type="text" name="query" value="<?php echo ED::string()->escape($query); ?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_SEARCH_DEFAULT'); ?>" class="form-control" data-search-text>
        <span class="input-group-btn">
            <button type="button" class="btn btn-default" data-search-button><?php echo JText::_('COM_EASYDISCUSS_BUTTON_SEARCH'); ?></button>
        </span>
    </div>

    <div class="ed-search-results-choices t-lg-mt--lg t-lg-mb--lg">
        <div class="o-col t-lg-pr--md t-xs-pr--no">
            <div class="ed-search-results-choices__title">Filter by tags:</div>
            <?php
                $tagLabels = '';
                if ($tagFilters) {
                    foreach($tagFilters as $item) {
                        $tagLabels .= ($tagLabels) ? ',' . $item->title : $item->title;
                    }
                }
            ?>
            <input type="text" placeholder="<?php echo JText::_('COM_EASYDISCUSS_SEARCH_PLACEHOLDER_ENTER_TAG'); ?>" value="<?php echo $tagLabels; ?>" data-search-tags-label />

        </div>
        <div class="o-col">
            <div class="ed-search-results-choices__title">Filter by categories:</div>
            <?php
                $catLabels = '';
                if ($catFilters) {
                    foreach($catFilters as $item) {
                        $catLabels .= ($catLabels) ? ',' . $item->title : $item->title;
                    }
                }
            ?>
            <input type="text" placeholder="<?php echo JText::_('COM_EASYDISCUSS_SEARCH_PLACEHOLDER_ENTER_CATEGORY'); ?>" value="<?php echo $catLabels; ?>" data-search-categories-label />
        </div>
    </div>

    <div class="hide" data-tags-container>
        <?php
            if ($tagFilters) {
                foreach($tagFilters as $item) {
                    $val = $item->id . ':' . $item->title;
        ?>
                <input type="hidden" name="tags[]" value="<?php echo ED::string()->escape($val); ?>" data-id="<?php echo $item->id; ?>" data-title="<?php echo $item->title; ?>" data-search-tags />
        <?php
                }
            }
        ?>
    </div>

    <div class="hide" data-categories-container>
        <?php
            if ($catFilters) {
                foreach($catFilters as $item) {
                        $val = $item->id . ':' . $item->title;
        ?>
                <input type="hidden" name="categories[]" value="<?php echo ED::string()->escape($val); ?>"data-id="<?php echo $item->id; ?>" data-title="<?php echo $item->title; ?>" data-search-categories />
        <?php
                }
            }
        ?>
    </div>

    <input type="hidden" name="option" value="com_easydiscuss" />
    <input type="hidden" name="controller" value="search" />
    <input type="hidden" name="task" value="query" />
    <input type="hidden" name="Itemid" value="<?php echo DiscussRouter::getItemId('search'); ?>" />
     <?php echo JHTML::_( 'form.token' ); ?>
</form>
