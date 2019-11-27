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
<div class="ed-badges-entry">
    <div class="ed-badge ed-badge--entry">
        <div class="o-flag ed-badge--flag">
            <div class="o-flag__image o-flag--top">
                <a href="<?php echo EDR::_('view=badges&layout=listings&id=' . $badge->id);?>" class="o-avatar o-avatar--lg">
                    <img src="<?php echo $badge->getAvatar();?>"/>
                </a>
            </div>
            <div class="o-flag__body">
                <a href="<?php echo EDR::_('view=badges&layout=listings&id=' . $badge->id);?>" class="ed-badge__name"><?php echo $badge->get('title');?></a>
                <div class="ed-badge__meta t-lg-mb--md"><?php echo $badge->get('description');?></div>
                <a href="<?php echo EDR::_('view=badges&layout=listings&id=' . $badge->id);?>" class="ed-badge__rank t-lg-mb--sm"><?php echo JText::sprintf('COM_EASYDISCUSS_BADGE_TOTAL_ACHIEVERS', $badge->getTotalAchievers());?></a>
            </div>
        </div>

        <div class="ed-badge__indicator">
            <i class="fa fa-trophy"></i>
        </div>        
    </div>

    <?php if ($users) { ?>
	    <div class="ed-achievers">
	    	<?php foreach ($users as $user) { ?>
		        <div class="ed-achievers__item">
		            <div class="ed-achiever">
		                <div class="o-flag">
		                    <div class="o-flag__image">
		                        <div class="o-avatar-status is-online">
		                            <div class="o-avatar-status__indicator"></div>
		                            <a href="<?php echo $user->getLink(); ?>">
                                        <?php echo $this->html('user.avatar', $user, array('size' => 'md'), false, true); ?>
		                            </a>
		                        </div>
		                    </div>
		                    <div class="o-flag__body">
                                <?php echo $this->html('user.username', $user, array('lgMarginTop' => true, 'lgMarginBottom' => true)); ?>
		                        <div class="ed-user-rank t-lg-mb--sm"><?php echo ED::ranks()->getRank($user->id); ?></div>
		                    </div>
		                </div>
		                <div class="ed-achiever__date"><?php echo JText::sprintf('COM_EASYDISCUSS_ACHIEVED_ON', $badge->getAchievedDate($user->id)); ?></div>    
		            </div>
		        </div>
	        <?php } ?>
	    </div>
	<?php } else { ?>
        <div class="test-object t-lg-mt--xl is-empty">
            <div class="o-empty o-empty--bordered">
                <div class="o-empty__content">
                    <i class="o-empty__icon fa fa-book"></i>
                    <div class="o-empty__text">
                        <?php echo JText::_('COM_EASYDISCUSS_BADGES_NO_USERS'); ?>
                    </div>
                </div>
            </div>
        </div>
	<?php } ?>
</div>
