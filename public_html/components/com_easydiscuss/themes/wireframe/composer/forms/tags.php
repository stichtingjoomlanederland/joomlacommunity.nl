<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<style type="text/css">
.chzn-container {
    width: 100% !important;
}
</style>

<div class="ed-editor-widget t-lg-mt--xl">
    <div class="ed-editor-widget__title">
        <?php echo JText::_('COM_EASYDISCUSS_POST_CREATE_TAGS'); ?>
    </div>

    <div class="ed-editor-widget__note">
        <?php echo JText::_('COM_EASYDISCUSS_POST_CREATE_TAGS_INFO'); ?>
    </div>

    <div class="form-group" data-ed-tags-list>
        <select name="tags[]" data-placeholder="<?php echo JText::_('COM_EASYDISCUSS_ADD_TAG_PLACEHOLDER'); ?>" class="form-control <?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'xchosen-rtl' : '';?>" multiple data-ed-tags-select>
        <?php if ($tags) { ?>
            <?php foreach ($tags as $tag) { ?>
            <?php
                $selected = "";

                if (isset($post->tags) && $post->tags) {
                    foreach ($post->tags as $ptag) {
                        if ($tag->id == $ptag->id) {
                            $selected = ' selected="selected"';
                            break;
                        }
                    }
                }
            ?>
            <option value="<?php echo $tag->title;?>"<?php echo $selected; ?>><?php echo $tag->title;?></option>
            <?php } ?>
        <?php } ?>
        </select>
    </div>

    <?php if ($this->config->get('max_tags_allowed') > 0) { ?>
    <div class="ed-editor-widget__note ed-tags-limit">
        <span data-ed-used-tags>0</span>/<span data-ed-max-tags><?php echo $this->config->get('max_tags_allowed'); ?> <?php echo JText::_('COM_EASYDISCUSS_NUMBER_TAGS_ALLOWED'); ?></span>
    </div>
    <?php } ?>

</div>
