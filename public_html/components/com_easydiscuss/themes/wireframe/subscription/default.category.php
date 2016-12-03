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
<div class="ed-post-item" data-ed-subscription-settings-category data-id=<?php echo $post->id;?>>
    <div class="ed-post-item__hd">
        <div class="o-row">
            <div class="o-col">
                <div class="o-flag">
                    <div class="o-flag__image o-flag--top">
                        <a class="o-avatar o-avatar--md" href="<?php echo $post->permalink; ?>">
                            <img src="<?php echo $post->avatar; ?>">
                        </a>
                    </div>
                    <div class="o-flag__body">
                        <a class="ed-cat-name t-lg-mb--sm" href="<?php echo $post->permalink; ?>"><?php echo $post->bname; ?></a>
                        <ol class="g-list-inline g-list-inline--delimited ed-cat-item-meta">
                            <li><?php echo JText::sprintf('COM_EASYDISCUSS_SUBSCRIBE_CATEGORY_DISCUSSIONS_COUNT', $post->totalPosts); ?></li>
                        </ol>
                    </div>
                </div>
                <a href="<?php echo $post->unsubscribeLink; ?>" class="ed-unsubscribe-link"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIPTION_UNSUBSCRIBE') ?></a>
            </div>
            <div class="o-col t-lg-pr--md t-xl-pr--no">
                <?php echo $this->output('site/subscription/subscribe.interval', array('subscribe' => $post))?>    
            </div>
            <div class="o-col t-lg-pr--md t-xl-pr--no">
                <?php echo $this->output('site/subscription/subscribe.sort', array('subscribe' => $post))?> 
            </div>
            <div class="o-col">
                <?php echo $this->output('site/subscription/subscribe.count', array('subscribe' => $post))?> 
            </div>
        </div>
    </div>
</div>