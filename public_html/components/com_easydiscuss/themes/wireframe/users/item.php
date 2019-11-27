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
<div class="ed-user-item">
    <div class="o-row">
        <div class="o-col">
            <div class="o-flag">
                <div class="o-flag__image o-flag--top">
                    <?php echo $this->html('user.avatar', $user, array('status' => true)); ?>
                </div>

                <div class="o-flag__body">
                    <?php echo $this->html('user.username', $user, array('lgMarginBottom' => true)); ?>

                    <?php echo $this->html('user.role', $user); ?>

                    <?php echo $this->output('site/widgets/rank.progress', array('user' => $user)); ?>
                </div>
            </div>
        </div>

        <div class="o-col--7">
            <div class="ed-statistic pull-right">
                <div class="ed-statistic__item">
                    <a href="<?php echo EDR::_('view=profile&viewtype=questions&id='. $user->id); ?>">
                        <span class="ed-statistic__item-count"><?php echo $user->getTotalQuestions();?></span>
                        <span><?php echo JText::_('COM_EASYDISCUSS_USER_POSTS');?></span>
                    </a>
                </div>

                <div class="ed-statistic__item">
                    <a href="<?php echo EDR::_('view=profile&viewtype=replies&id='.$user->id); ?>">
	                    <span class="ed-statistic__item-count"><?php echo $user->getTotalReplies();?></span>
	                    <span><?php echo JText::_('COM_EASYDISCUSS_USER_REPLIES');?></span>
                    </a>
                </div>
            <?php if ($this->config->get('main_badges')) { ?>                
                <div class="ed-statistic__item">
                    <a href="<?php echo EDR::_('view=badges&userid='.$user->id); ?>">
	                    <span class="ed-statistic__item-count"><?php echo $user->getTotalBadges();?></span>
	                    <span><?php echo JText::_('COM_EASYDISCUSS_USER_BADGES');?></span>
                    </a>
                </div>
            <?php } ?>
            
                <?php echo $this->html('user.pm', $user->id, 'list'); ?>

                <?php if ($this->config->get('main_rss')) { ?>
                <div class="ed-statistic__item">
                    <a href="<?php echo $user->getRSS();?>" target="_blank"><i class="fa fa-rss ed-statistic__item-icon"></i></a>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
