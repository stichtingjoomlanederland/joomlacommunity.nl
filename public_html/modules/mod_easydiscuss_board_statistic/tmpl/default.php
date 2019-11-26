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
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
//do not remove this line
ed.require(['edq'], function($) {});
</script>
<div id="ed" class="discuss-mod discuss-mod-statistics">
    <div class="ed-board-stats t-lg-mt--xl">
        <div class="ed-board-stats__hd">
            <ol class="g-list-inline">
                <li>
                    <span class="ed-board-stats__meta"><?php echo JText::_('MOD_EASYDISCUSS_TOTAL_POSTS');?>:</span>
                    <b class="ed-board-stats__result"><?php echo $totalPosts; ?></b>
                </li>

                <?php if ($config->get('main_qna')) { ?>
                <li>
                    <span class="ed-board-stats__meta"><?php echo JText::_('MOD_EASYDISCUSS_TOTAL_RESOLVED_POSTS');?>:</span>
                    <b class="ed-board-stats__result"><?php echo $resolvedPosts;?></b>
                </li>
                <li>
                    <span class="ed-board-stats__meta"><?php echo JText::_('MOD_EASYDISCUSS_TOTAL_UNRESOLVED_POSTS');?>:</span>
                    <b class="ed-board-stats__result"><?php echo $unresolvedPosts;?></b>
                </li>
                <?php } ?>
                
                <li>
                    <span class="ed-board-stats__meta"><?php echo JText::_('MOD_EASYDISCUSS_LATEST_MEMBER');?>:</span>
                    <a href="<?php echo $latestMember->getLink();?>" class="ed-board-stats__result"><?php echo $latestMember->getName(); ?></a>
                </li>
            </ol>
        </div>
        <div class="ed-board-stats__bd">
            <div class="ed-board-stats__bd-title"><?php echo JText::_('MOD_EASYDISCUSS_ONLINE_USERS');?></div>
            <div class="o-avatar-list">
                <?php if ($onlineUsers) { ?>
                    <?php foreach ($onlineUsers as $user) { ?>
                     <?php echo ED::themes()->html('user.avatar', $user); ?>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
