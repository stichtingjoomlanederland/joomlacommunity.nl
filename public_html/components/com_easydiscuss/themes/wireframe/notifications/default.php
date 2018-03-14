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
<div class="ed-noti">
    <div class="ed-noti-hd">
        <div class="o-row">
            <div class="o-col">
                <div class="ed-noti-hd__title">
                    	<?php echo JText::_('COM_EASYDISCUSS_ALL_NOTIFICATIONS'); ?>
                    <span data-ed-notifications-counter class="ed-noti-hd__badge">
                		<?php echo $totalNotifications; ?>
                    </span>
                </div>
            </div>

            <?php if ($notifications) { ?>
            <div class="o-col">
                <div class="pull-right">
                    <a href="javascript:void(0)" data-ed-notifications-read-all class="btn btn-default"><?php echo JText::_('COM_EASYDISCUSS_MARK_AS_READ'); ?></a>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <?php if ($notifications) { ?>
    	<?php foreach ($notifications as $day => $data) { ?>
    	    <div class="ed-noti_list">
    	        <div class="ed-noti__date t-lg-mt--lg t-lg-mb--lg"><?php echo $day; ?></div>
                <?php foreach ($data as $item) { ?>

    	        	<div class="ed-noti__item is-<?php echo $item->state == DISCUSS_NOTIFICATION_READ ? 'read' : 'unread';?>
                        <?php echo $item->type == DISCUSS_NOTIFICATIONS_REPLY ? 'type-reply' : '' ; ?>
                        <?php echo $item->type == DISCUSS_NOTIFICATIONS_FAVOURITE ? 'type-fav' : '' ; ?>
                        <?php echo $item->type == DISCUSS_NOTIFICATIONS_LIKES_DISCUSSION ? 'type-like' : '' ; ?>
                        <?php echo $item->type == DISCUSS_NOTIFICATIONS_LIKES_REPLIES ? 'type-like' : '' ; ?>
                        <?php echo $item->type == DISCUSS_NOTIFICATIONS_RESOLVED ? 'type-resolved' : '' ; ?>
                        <?php echo $item->type == DISCUSS_NOTIFICATIONS_FEATURED ? 'type-featured' : '' ; ?>
                        <?php echo $item->type == DISCUSS_NOTIFICATIONS_COMMENT ? 'type-comment' : '' ; ?>
                        <?php echo $item->type == DISCUSS_NOTIFICATIONS_MENTIONED ? 'type-mention' : '' ; ?>
                        <?php echo $item->type == DISCUSS_NOTIFICATIONS_LOCKED ? 'type-locked' : '' ; ?>
                        <?php echo $item->type == DISCUSS_NOTIFICATIONS_UNLOCKED ? 'type-unlocked' : '' ; ?>
                        <?php echo $item->type == DISCUSS_NOTIFICATIONS_VOTE_UP_REPLY ? 'type-vote' : '' ; ?>
                        <?php echo $item->type == DISCUSS_NOTIFICATIONS_VOTE_DOWN_REPLY ? 'type-vote' : '' ; ?>
                        <?php echo $item->type == DISCUSS_NOTIFICATIONS_VOTE_UP_DISCUSSION ? 'type-vote' : '' ; ?>
                        <?php echo $item->type == DISCUSS_NOTIFICATIONS_VOTE_DOWN_DISCUSSION ? 'type-vote' : '' ; ?>
                    ">
    		            <div class="ed-noti__item-indicator"></div>
			            <div class="o-flag ed-noti__item-content">
                            <div class="o-flag__image o-flag--top t-pr--md">
                                <?php echo $this->html('user.avatar', $item->authorProfile, array('size' => 'sm')); ?>
                            </div>

			                <div class="o-flag__body o-flag--top">
			                    <div class="ed-noti__title">
			                        <?php echo $item->title;?>
			                    </div>
			                    <div class="ed-noti__desp t-lg-mb--md">
			                    </div>
			                    <div class="ed-noti__meta">
			                        <ol class="g-list-inline g-list-inline--delimited">
			                            <li>
			                            	<a href="<?php echo $item->permalink;?>">
			                            		<?php echo $item->touched; ?>
			                            	</a>
			                            </li>
			                            <?php if ($item->state != DISCUSS_NOTIFICATION_READ) { ?>
				                            <li data-breadcrumb="·">
				                            	<a href="<?php echo EDR::_('controller=notification&task=markread&id=' . $item->id); ?>">
				                            		<?php echo JText::_('COM_EASYDISCUSS_MARK_AS_READ'); ?>
				                            	</a>
				                            </li>
				                        <?php } ?>
			                        </ol>
			                    </div>
			                </div>
			            </div>
                        <div class="ed-noti__item-type">
                            <i class="ed-noti__item-type-icon"></i>
                        </div>
    		        </div>
                <?php } ?>
		  	</div>
        <?php } ?>
    <?php } else { ?>
        <div class="test-object t-lg-mt--xl is-empty">
            <div class="o-empty">
                <div class="o-empty__content">
                    <i class="o-empty__icon fa fa-bell t-lg-mb--xl"></i>
                    <div class="o-empty__text"><?php echo JText::_('COM_EASYDISCUSS_NOTIFICATIONS_ALL_CAUGHT_UP');?></div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<?php if (isset($pagination)) { ?>
    <div class="ed-pagination">
        <?php echo $pagination;?>
    </div>
<?php } ?>
