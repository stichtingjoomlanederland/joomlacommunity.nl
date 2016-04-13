<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-header-mini" data-id="<?php echo $group->id;?>" data-name="<?php echo $this->html('string.escape', $group->getName());?>" data-avatar="<?php echo $group->getAvatar();?>" data-es-group-item>
    <div class="es-header-mini-cover" style="background-image: url('<?php echo $group->getCover();?>');background-position: <?php echo $group->getCoverPosition();?>;">
        <b></b>
        <b></b>
    </div>
    <div class="es-header-mini-avatar">
        <a class="es-avatar es-avatar-md" href="<?php echo $group->getPermalink();?>">
            <img alt="<?php echo $this->html('string.escape', $group->getName());?>" src="<?php echo $group->getAvatar(SOCIAL_AVATAR_SQUARE);?>" />
        </a>
    </div>
    <div class="es-header-mini-body" data-appscroll>
        <div class="es-header-mini-meta">
            <ul class="fd-reset-list">
                <li>
                    <h2 class="h4 es-cover-title" style="width: 100%;">
                        <a href="<?php echo $group->getPermalink();?>" title="<?php echo $this->html('string.escape', $group->getName());?>"><?php echo $group->getName();?></a>
                    </h2>
                    <?php if ($group->isOpen()) { ?>
                        <span class="label label-success" data-original-title="<?php echo ES::_('COM_EASYSOCIAL_GROUPS_OPEN_GROUP_TOOLTIP', true);?>" data-es-provide="tooltip" data-placement="top">
                            <i class="fa fa-globe"></i> <?php echo JText::_('COM_EASYSOCIAL_GROUPS_OPEN_GROUP'); ?>
                        </span>
                    <?php } ?>

                    <?php if ($group->isClosed()) { ?>
                        <span class="label label-danger" data-original-title="<?php echo ES::_('COM_EASYSOCIAL_GROUPS_CLOSED_GROUP_TOOLTIP', true );?>" data-es-provide="tooltip" data-placement="top">
                            <i class="fa fa-lock"></i> <?php echo JText::_('COM_EASYSOCIAL_GROUPS_CLOSED_GROUP'); ?>
                        </span>
                    <?php } ?>
                </li>
            </ul>
            <div class="fd-small info-actions">
                <a href="<?php echo ESR::groups(array('layout' => 'item', 'type' => 'info', 'id' => $group->getAlias()));?>"><?php echo JText::_('COM_EASYSOCIAL_GROUPS_MORE_ABOUT_THIS_GROUP'); ?></a>
            </div>  
        </div>

        <?php if ($group->isMember() && $view == 'groups') { ?>
        <div class="es-header-mini-apps-action" data-appscroll-viewport>
            <ul class="fd-nav es-nav-apps" data-appscroll-content>
                <li>
                    <a class="btn btn-primary pull-right" style="width: 100%; height: auto;"href="<?php echo EDR::_('view=ask&group_id=' . $group->id);?>">
                        <i class="fa fa-pencil"></i>&nbsp; <?php echo JText::_('COM_EASYDISCUSS_NEW_POST');?>
                    </a>
                </li>
            </ul>
        </div>
        <?php } ?>                  
    </div>

    <div class="es-header-mini-footer">
        <div class="pull-left">
            <div class="es-list-vertical-divider mb-0 ml-0">
                <?php echo $this->render('widgets', 'group', 'groups', 'groupStatsStart', array($group)); ?>
                <span>
                    <a href="<?php echo ESR::groups(array('layout' => 'category', 'id' => $group->getCategory()->getAlias()));?>">
                        <i class="fa fa-database"></i> <?php echo $group->getCategory()->get('title'); ?>
                    </a>
                </span>

                <?php if ($this->config->get('video.enabled', true) && $group->getParams()->get('videos', true)) { ?>
                <span>
                    <a href="<?php echo ESR::videos(array('uid' => $group->getAlias(), 'type' => SOCIAL_TYPE_GROUP));?>">

                        <i class="fa fa-film"></i>
                        &#8207;
                        <?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_EVENTS_VIDEOS', $group->getTotalVideos()), $group->getTotalVideos()); ?>
                    </a>
                </span>
                <?php } ?>

                <?php if ($this->config->get('photos.enabled', true) && $group->getCategory()->getAcl()->get('photos.enabled', true) && $group->getParams()->get('photo.albums', true)) { ?>
                <span>
                    <a href="<?php echo ESR::albums(array('uid' => $group->getAlias(), 'type' => SOCIAL_TYPE_GROUP));?>">
                        <i class="fa fa-photo"></i> <?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_GROUPS_ALBUMS', $group->getTotalAlbums()), $group->getTotalAlbums()); ?>
                    </a>
                </span>
                <?php } ?>
                
                <span>
                    <i class="fa fa-users"></i> <?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_GROUPS_MEMBERS', $group->getTotalMembers()), $group->getTotalMembers()); ?>
                </span>

                <?php if ($this->config->get('groups.hits.display')) { ?>
                <span>
                    <i class="fa fa-eye"></i> <?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_GROUPS_VIEWS', $group->hits), $group->hits); ?></a>
                </span>
                <?php } ?>
                
                <?php echo $this->render('widgets', 'group', 'groups', 'groupStatsEnd', array($group)); ?>
                <span>
                    <?php echo ES::sharing(array('url' => $group->getPermalink(false, true), 'display' => 'dialog', 'text' => JText::_('COM_EASYSOCIAL_STREAM_SOCIAL'), 'css' => 'fd-small'))->getHTML(true); ?>
                </span>
            </div>
        </div>

        <?php if (!$group->isMember() && !$group->isPendingMember()) { ?>
        <div class="pull-right">
            <span class="action">
                <a class="btn btn-es-primary" href="javascript:void(0);" data-es-group-join><?php echo JText::_('COM_EASYSOCIAL_GROUPS_JOIN_THIS_GROUP');?> &rarr;</a>
            </span>
        </div>
        <?php } ?>

    </div>
</div>