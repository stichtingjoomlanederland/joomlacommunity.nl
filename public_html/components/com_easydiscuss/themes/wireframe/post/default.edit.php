<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>

<div class="ed-reply-form t-lg-mt--lg <?php echo $composer->classname; ?>" data-ed-composer-wrapper>

    <form autocomplete="off" action="<?php echo JRoute::_('index.php');?>" method="post" enctype="multipart/form-data" data-ed-reply-form>

        <div class="ed-post-reply-form t-lg-mt--xl">

            <div role="alert" class="o-alert o-alert--icon t-hidden" data-ed-composer-alert></div>

            <div class="ed-reply-form__bd">

                <div class="ed-reply-form__title">
                    <?php if ($this->config->get('layout_avatar')) { ?>
                        <div class="o-avatar o-avatar--sm t-lg-mr--sm">
                            <img alt="<?php echo $my->getName(); ?>" src="<?php echo $my->getAvatar(); ?>"> 
                         </div>
                     <?php } ?>
                    <?php echo JText::_('COM_EASYDISCUSS_EDIT_YOUR_RESPONSE'); ?>
                </div>

                <?php if ($this->config->get('main_anonymous_posting')) { ?>
                <div class="o-checkbox t-lg-mb--lg small">
                    <input id="anonymous" type="checkbox" name="anonymous" value="1"<?php echo $post->anonymous ? ' checked="checked"' : '';?> />
                    <label for="anonymous">
                        <?php echo JText::_('COM_EASYDISCUSS_POST_ANONYMOUSLY');?>
                    </label>
                </div>
                <?php } ?>

                <div class="ed-editor ed-editor--<?php echo $composer->getEditorClass();?> <?php echo $composer->hasTabs() ? '' : 'has-no-tab'; ?>" <?php echo $composer->uid;?>>

                    <div class="ed-editor-widget ed-editor-widget--no-pad">
                        <?php echo $composer->renderEditor(); ?>

                        <?php echo $composer->renderTabs(); ?>
                    </div>

                    <?php echo $this->output('site/composer/forms/location', array('post' => $post, 'editorId' => $composer->uid, 'operation' => $composer->operation)); ?>

                    <div class="ed-editor__ft">
                        <a class="btn btn-link t-pl--xs" href="<?php echo $cancel;?>">
                            <?php echo JText::_('COM_EASYDISCUSS_CANCEL_AND_DISCARD');?>
                        </a>
                        <button class="btn btn-primary pull-right" type="button" data-ed-submit-button>
                            <?php echo JText::_('COM_EASYDISCUSS_BUTTON_UPDATE_REPLY');?>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <?php echo $this->html('form.hidden', 'posts', 'posts', 'saveReply'); ?>

        <?php if (!empty($reference) && $referenceId) { ?>
        <input type="hidden" name="reference" value="<?php echo $reference; ?>" />
        <input type="hidden" name="reference_id" value="<?php echo $referenceId; ?>" />
        <?php } ?>

        <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
        <input type="hidden" name="id" id="id" value="<?php echo $post->id; ?>" />
        <input type="hidden" name="parent_id" id="parent_id" value="<?php echo $post->parent_id; ?>" />
    </form>
</div>
