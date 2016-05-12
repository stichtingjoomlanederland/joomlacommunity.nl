<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<?php if( $posts ){ ?>
<div class="">
    <!-- <div class="ed-ask-similar-menu__arrow"></div> -->
    <div class="ed-ask-similar-menu__scroll">

        <?php foreach( $posts as $post ){ ?>
        <div class="ed-ask-similar-menu__item">
            <div class="o-row">
                <div class="o-col">
                    <a class="ed-ask-similar-menu__title" href="<?php echo $post->getPermalink(); ?>"><?php echo $post->getTitle(); ?> <i class="fa fa-external-link"></i></a>
                    <div class="ed-ask-similar-menu__date"><?php echo JText::sprintf('COM_EASYDISCUSS_SIMILAR_QUESTION_ASKED_ON', ED::date($post->created)->format('d F Y'), $post->getOwner()->getName()); ; ?></div>
                </div>
                <div class="o-col">
                    <div class="ed-statistic pull-right">
                        <div class="ed-statistic__item">
                            <a href="<?php echo $post->getPermalink();?>">
                                <span class="ed-statistic__item-count"><?php echo $post->getTotalReplies();?></span>
                                <span><?php echo JText::_('COM_EASYDISCUSS_REPLIES');?></span>
                            </a>
                        </div>
                        <div class="ed-statistic__item">
                            <a href="<?php echo $post->getPermalink();?>">
                                <span class="ed-statistic__item-count"><?php echo $post->getHits();?></span>
                                <span><?php echo JText::_('COM_EASYDISCUSS_VIEWS');?></span>
                            </a>
                        </div>

                        <?php if ($this->config->get('main_allowquestionvote')) { ?>
                        <div class="ed-statistic__item">
                            <a href="<?php echo $post->getPermalink();?>">
                                <span class="ed-statistic__item-count"><?php echo $post->getTotalVotes();?></span>
                                <span><?php echo JText::_('COM_EASYDISCUSS_VOTES');?></span>
                            </a>
                        </div>
                        <?php } ?>

                        <?php if ($this->config->get('main_likes_discussions')) { ?>
                        <div class="ed-statistic__item">
                            <a href="<?php echo $post->getPermalink();?>">
                                <span class="ed-statistic__item-count"><?php echo $post->getTotalLikes();?></span>
                                <span><?php echo JText::_('COM_EASYDISCUSS_LIKES');?></span>
                            </a>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

    </div>
</div>

<?php } else { ?>
<div class="small t-mt--sm is-empty">
  <div class="o-empty">
      <div class="o-empty__content">
          <i class="o-empty__icon fa fa-book"></i>
          <div class="o-empty__text"><?php echo JText::_('COM_EASYDISCUSS_SIMILAR_QUESTION_NOT_FOUND'); ?></div>
      </div>
  </div>
</div>
<?php } ?>
