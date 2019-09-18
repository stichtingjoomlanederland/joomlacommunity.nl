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
defined('_JEXEC') or die('Restricted access');
?>
<div class="ed-post-item<?php echo $post->getHeaderClass(); ?>"
>
    <div class="ed-post-item__hd">
        <div class="o-row">
            <div class="o-col">
                <h2 class="ed-post-item__title t-lg-mt--md t-lg-mb--md">
                	<a href="<?php echo $post->getPermalink();?>"><?php echo $post->getTitle();?></a>

                    <?php if ($post->isFeatured() || $post->isLocked() || $post->isProtected() || $post->isPrivate()) { ?>
                    <div class="ed-post-item__status t-ml--sm">
                        <i class="fa fa-star ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_FEATURED_DESC');?>"></i>

                        <i class="fa fa-lock ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_LOCKED_DESC');?>"></i>

                        <i class="fa fa-key ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_PROTECTED_DESC');?>"></i>

                        <i class="fa fa-eye ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_PRIVATE_DESC');?>"></i>

                    </div>
                    <?php } ?>
                </h2>

                <?php if ($post->getTags()) { ?>
	                <ol class="g-list-inline ed-post-item__post-meta">
	                	<?php foreach ($post->getTags() as $tag) { ?>
	                    	<li><a href="<?php echo EDR::getTagRoute($tag->id); ?>">#<?php echo $tag->title; ?></a></li>
	                    <?php } ?>
	                </ol>
				<?php } ?>

                <?php if ($this->config->get('main_qna')) { ?>
                <ol class="g-list-inline ed-post-item__post-meta">
                    <li>
                    	<span class="o-label o-label--<?php echo ($post->isresolve) ? 'success' : 'danger';?>-o">
                    		<?php echo ($post->isresolve) ? JText::_('COM_EASYDISCUSS_RESOLVED') : JText::_('COM_EASYDISCUSS_UNRESOLVED');?></span>
                    </li>
                </ol>
                <?php } ?>
            </div>
        </div>

    </div>

    <div class="ed-post-item__ft t-bdt-no">
        <ol class="g-list-inline g-list-inline--dashed">
            <li><span class=""><?php echo $post->getDuration(); ?></span></li>
            <li><a class="" href="<?php echo EDR::getCategoryRoute($post->getCategory()->id); ?>"><?php echo JText::_($post->getCategory()->title); ?></a></li>

            <?php if ($post->getLastReplier()) { ?>
                <li class="current">
                    <div class="">
                        <span><?php echo JText::_('COM_EASYDISCUSS_LAST_REPLIER'); ?>: </span>
                        <?php if (!$post->isLastReplyAnonymous()) { ?>
                            <?php echo $this->html('user.avatar', $post->getLastReplier(), array('rank' => false, 'size' => 'sm')); ?>
                        <?php } else { ?>
                            <?php echo $this->html('user.anonymous', $post->getOwner(), $post->isAnonymous()); ?>
                        <?php } ?>
                    </div>
                </li>
			<?php } ?>
        </ol>
    </div>
</div>
