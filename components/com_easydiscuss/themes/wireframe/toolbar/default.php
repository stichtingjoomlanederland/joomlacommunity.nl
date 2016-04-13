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

<?php if ($renderToolbarModule) { ?>
<?php echo ED::renderModule('easydiscuss-before-header'); ?>
<?php } ?>

<?php if ($showHeader) { ?>
<div class="ed-head t-lg-mb--lg">
    <div class="ed-head__info">
        <h2 class="ed-head__title"><?php echo $headers->title;?></h2>
        <div class="ed-head__desp"><?php echo $headers->desc;?></div>
    </div>

    <div class="ed-subscribe">

        <?php if ($this->config->get('main_rss')) { ?>
        <a href="<?php echo ED::feeds()->getFeedUrl('view=index');?>" class="t-lg-mr--md" target="_blank">
            <i class="fa fa-rss-square ed-subscribe__icon t-lg-mr--sm"></i> <?php echo JText::_("COM_EASYDISCUSS_TOOLBAR_SUBSCRIBE_RSS");?>
        </a>
        <?php } ?>

        <?php if ($this->config->get('main_sitesubscription')) { ?>
        <?php echo ED::subscription()->html($this->my->id, '0', 'site'); ?>
        <?php } ?>
    </div>
</div>
<?php } ?>

<?php if ($renderToolbarModule) { ?>
<?php echo ED::renderModule('easydiscuss-after-header'); ?>

<?php echo ED::renderModule('easydiscuss-before-toolbar'); ?>
<?php } ?>

<?php if ($showToolbar) { ?>
<div class="ed-navbar ed-responsive t-lg-mb--lg" data-ed-toolbar>
    <div class="ed-navbar__body">

        <?php if ($showSearch) { ?>
    	<div class="ed-navbar__search">
            <form class="ed-navbar__search-form" name="discuss-toolbar-search" data-search-toolbar-form method="post" action="<?php echo JRoute::_('index.php'); ?>">
    	        <input type="text" placeholder="<?php echo JText::_('COM_EASYDISCUSS_SEARCH_DEFAULT');?>" autocomplete="off" class="ed-navbar__search-input" data-search-input name="query" value="<?php echo ED::string()->escape($query); ?>">
                <input type="hidden" name="option" value="com_easydiscuss" />
                <input type="hidden" name="controller" value="search" />
                <input type="hidden" name="task" value="query" />
                <input type="hidden" name="Itemid" value="<?php echo DiscussRouter::getItemId('search'); ?>" />
                <?php echo JHTML::_( 'form.token' ); ?>
    	    </form>
    	</div>
        <?php } ?>

        <ul class="o-nav ed-navbar__o-nav">
            <?php if ($this->my->id) { ?>
                <?php if ($this->config->get('main_conversations') && $showConversation && $this->acl->allowed('allow_privatemessage')) { ?>
                <li>
                    <a href="javascript:void(0);" class="ed-navbar__icon-link <?php echo $conversationsCount ? 'has-new' : '';?>"
                        data-ed-conversations-wrapper
                        data-ed-popbox="ajax://site/views/conversation/popbox"
                        data-ed-popbox-position="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'bottom-left' : 'bottom-right';?>"
                        data-ed-popbox-toggle="click"
                        data-ed-popbox-offset="4"
                        data-ed-popbox-type="navbar-conversations"
                        data-ed-popbox-component="popbox--navbar"
                        data-ed-popbox-cache="0"

                        data-ed-provide="tooltip"
                        data-original-title="<?php echo JText::_('COM_EASYDISCUSS_CONVERSATIONS');?>"
                    >
                    	<i class="fa fa-envelope"></i>
                        <span class="ed-navbar__link-text"><?php echo JText::_('COM_EASYDISCUSS_CONVERSATIONS');?></span>
                    	<span class="ed-navbar__link-bubble" data-ed-conversations-counter><?php echo $conversationsCount;?></span>
                    </a>
                </li>
                <?php } ?>

                <?php if ($showNotification) { ?>
                <li>
                    <a href="javascript:void(0);" class="ed-navbar__icon-link <?php echo $notificationsCount ? 'has-new' : '';?>"
                        data-ed-notifications-wrapper
                    	data-ed-popbox="ajax://site/views/notifications/popbox"
                    	data-ed-popbox-position="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'bottom-left' : 'bottom-right';?>"
                    	data-ed-popbox-toggle="click"
                        data-ed-popbox-offset="4"
                    	data-ed-popbox-type="navbar-notifications"
                    	data-ed-popbox-component="popbox--navbar"
                    	data-ed-popbox-cache="0"

                        data-ed-provide="tooltip"
                        data-original-title="<?php echo JText::_('COM_EASYDISCUSS_NOTIFICATIONS');?>"
                    >
    					<i class="fa fa-bell"></i> <span class="ed-navbar__link-text"><?php echo JText::_('COM_EASYDISCUSS_NOTIFICATIONS');?></span>
    					<span class="ed-navbar__link-bubble" data-ed-notifications-counter><?php echo $notificationsCount;?></span>
                    </a>

                </li>
                <?php } ?>

                <!-- Show more settings -->
                <?php if ($showSettings) { ?>
                <li>
                    <a href="javascript:void(0);" class="ed-navbar__icon-link"
                        data-ed-popbox
                        data-ed-popbox-position="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'bottom-left' : 'bottom-right';?>"
                        data-ed-popbox-offset="4"
                        data-ed-popbox-type="navbar-profile"
                        data-ed-popbox-component="popbox--navbar"
                        data-ed-popbox-target="[data-ed-toolbar-profile-dropdown]"

                        data-ed-provide="tooltip"
                        data-original-title="<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MORE_SETTINGS');?>"
                    >
                    	<i class="fa fa-cog"></i> <span class="ed-navbar__link-text"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MORE_SETTINGS');?></span>
                    </a>

                    <div class="t-hidden" data-ed-toolbar-profile-dropdown>
                        <div class="popbox-dropdown">

                            <div class="popbox-dropdown__hd">
                                <div class="o-flag o-flag--rev">
                                    <div class="o-flag__body">
                                        <a href="<?php echo $this->profile->getPermalink();?>" class="ed-user-name"><?php echo $this->profile->getName();?></a>
                                        <div class="ed-user-rank "><?php echo ED::ranks()->getRank($this->profile->getId()); ?></div>
                                    </div>

                                    <div class="o-flag__image">
                                        <?php echo $this->html('user.avatar', $this->profile, array('rank' => true, 'popbox' => false)); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="popbox-dropdown__bd">
                                <div class="popbox-dropdown-nav">

                                    <div class="popbox-dropdown-nav__item <?php echo $active == 'profile' ? ' is-active' : '';?>">
                                        <a href="<?php echo ED::getEditProfileLink();?>" class="popbox-dropdown-nav__link">
                                            <div class="o-flag">
                                                <div class="o-flag__image o-flag--top">
                                                    <i class="popbox-dropdown-nav__icon fa fa-cog"></i>
                                                </div>
                                                <div class="o-flag__body">
                                                    <div class="popbox-dropdown-nav__name"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_EDIT_PROFILE'); ?></div>
                                                    <ol class="g-list-inline g-list-inline--delimited popbox-dropdown-nav__meta-lists">
                                                        <li><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_PROFILE_ACCOUNT_SETTINGS'); ?></li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                                    <div class="popbox-dropdown-nav__item <?php echo $active == 'mypost' ? ' is-active' : '';?>">
                                        <a href="<?php echo EDR::_('view=mypost');?>" class="popbox-dropdown-nav__link">
                                            <div class="o-flag">
                                                <div class="o-flag__image o-flag--top">
                                                    <i class="popbox-dropdown-nav__icon fa fa-file-text-o"></i>
                                                </div>
                                                <div class="o-flag__body">
                                                    <div class="popbox-dropdown-nav__name"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_POSTS');?></div>
                                                    <ol class="g-list-inline g-list-inline--delimited popbox-dropdown-nav__meta-lists">
                                                        <li><?php echo JText::sprintf('COM_EASYDISCUSS_TOTAL_QUESTION_CREATED', $this->profile->getTotalQuestions()); ?></li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                                    <?php if (ED::isModerator()) { ?>
                                    <div class="popbox-dropdown-nav__item <?php echo $active == 'assigned' ? ' is-active' : '';?>">
                                        <a href="<?php echo EDR::_('view=assigned');?>" class="popbox-dropdown-nav__link">
                                            <div class="o-flag">
                                                <div class="o-flag__image o-flag--top">
                                                    <i class="popbox-dropdown-nav__icon fa fa-file-text-o"></i>
                                                </div>
                                                <div class="o-flag__body">
                                                    <div class="popbox-dropdown-nav__name"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_ASSIGNED_POSTS');?></div>
                                                    <ol class="g-list-inline g-list-inline--delimited popbox-dropdown-nav__meta-lists">
                                                        <li><?php echo JText::sprintf('COM_EASYDISCUSS_TOOLBAR_ASSIGNED', $this->profile->getTotalAssigned()); ?></li>
                                                        <li><?php echo JText::sprintf('COM_EASYDISCUSS_TOOLBAR_RESOLVED', $this->profile->getTotalResolved()); ?></li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <?php } ?>

                                    <?php if ($this->config->get('main_favorite')) { ?>
                                    <div class="popbox-dropdown-nav__item <?php echo $active == 'favourites' ? ' is-active' : '';?>">
                                        <a href="<?php echo EDR::_('view=favourites');?>" class="popbox-dropdown-nav__link">
                                            <div class="o-flag">
                                                <div class="o-flag__image o-flag--top">
                                                    <i class="popbox-dropdown-nav__icon fa fa-heart-o"></i>
                                                </div>
                                                <div class="o-flag__body">
                                                    <div class="popbox-dropdown-nav__name"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_FAVOURITES');?></div>
                                                    <ol class="g-list-inline g-list-inline--delimited popbox-dropdown-nav__meta-lists">
                                                        <li><?php echo JText::sprintf('COM_EASYDISCUSS_TOOLBAR_MY_FAVOURITES_POST', $this->profile->getTotalFavourites()); ?></li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <?php } ?>

                                    <div class="popbox-dropdown-nav__item <?php echo $active == 'subscription' ? ' is-active' : '';?>">
                                        <a href="<?php echo EDR::_('view=subscription');?>" class="popbox-dropdown-nav__link">
                                            <div class="o-flag">
                                                <div class="o-flag__image o-flag--top">
                                                    <i class="popbox-dropdown-nav__icon fa fa-inbox"></i>
                                                </div>
                                                <div class="o-flag__body">
                                                    <div class="popbox-dropdown-nav__name"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_SUBSCRIPTION'); ?></div>
                                                    <ol class="g-list-inline g-list-inline--delimited popbox-dropdown-nav__meta-lists">
                                                        <li><?php echo JText::sprintf('COM_EASYDISCUSS_TOOLBAR_MY_SUBSCRIPTION_POST', $this->profile->getTotalSubscriptions()); ?></li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <?php if ($this->acl->allowed('manage_holiday')) { ?>
                                    <div class="popbox-dropdown-nav__item <?php echo $active == 'dashboard' ? ' is-active' : '';?>">
                                        <a href="<?php echo EDR::_('view=dashboard');?>" class="popbox-dropdown-nav__link">
                                            <div class="o-flag">
                                                <div class="o-flag__image o-flag--top">
                                                    <i class="popbox-dropdown-nav__icon fa fa-dashboard"></i>
                                                </div>
                                                <div class="o-flag__body">
                                                    <div class="popbox-dropdown-nav__name"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_DASHBOARD');?></div>
                                                    <ol class="g-list-inline g-list-inline--delimited popbox-dropdown-nav__meta-lists">
                                                        <li><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_DASHBOARD_DESC');?></li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <?php } ?>


                                    <div class="popbox-dropdown-nav__item">
                                        <a href="javascript:void(0);" class="popbox-dropdown-nav__link" data-ed-toolbar-logout>
                                            <div class="o-flag">
                                                <div class="o-flag__image o-flag--top">
                                                    <i class="popbox-dropdown-nav__icon fa fa-power-off"></i>
                                                </div>
                                                <div class="o-flag__body">
                                                    <div class="popbox-dropdown-nav__name"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_LOGOUT');?></div>
                                                    <ol class="g-list-inline g-list-inline--delimited popbox-dropdown-nav__meta-lists">
                                                        <li><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_LOGOUT_DESC');?></li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <form method="post" action="<?php echo JRoute::_('index.php');?>" data-ed-toolbar-logout-form>
                                        <input type="hidden" value="com_users"  name="option">
                                        <input type="hidden" value="user.logout" name="task">
                                        <input type="hidden" name="<?php echo ED::getToken();?>" value="1" />
                                        <input type="hidden" value="<?php echo EDR::getLogoutRedirect(); ?>" name="return" />
                                    </form>
                                </div>
                            </div>

                            <div class="popbox-dropdown__ft">
                                <div class="popbox-dropdown__note">
                                    <?php echo JText::sprintf('COM_EASYDISCUSS_TOOLBAR_LAST_LOGIN_NOTE', $this->profile->getLastOnline()); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <?php } ?>

            <?php } ?>


            <?php if (!$this->my->id && $showLogin) { ?>
            <li>
                <a href="javascript:void(0);" class="ed-navbar__icon-link"
                    data-ed-popbox
                    data-ed-popbox-position="bottom-right"
                    data-ed-popbox-offset="4"
                    data-ed-popbox-type="navbar-signin"
                    data-ed-popbox-component="popbox--navbar"
                    data-ed-popbox-target="[data-ed-toolbar-signin-dropdown]"

                    data-ed-provide="tooltip"
                    data-original-title="<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_SIGN_IN');?>"
                >
                    <i class="fa fa-lock"></i> <span class="ed-navbar__link-text"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_SIGN_IN');?></span>
                </a>
                <div class="t-hidden" data-ed-toolbar-signin-dropdown>
                    <div class="popbox-dropdown">

                        <div class="popbox-dropdown__hd">
                            <div class="o-flag o-flag--rev">
                                <div class="o-flag__body">
                                    <div class="popbox-dropdown__title"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_SIGN_IN_HEADING');?></div>
                                    <div class="popbox-dropdown__meta"><?php echo JText::sprintf('COM_EASYDISCUSS_TOOLBAR_NEW_USERS', '<a href="'. ED::getRegistrationLink() .'">' . JText::_('COM_EASYDISCUSS_TOOLBAR_REGISTER') . '</a>');?></div>
                                </div>
                            </div>
                        </div>

                        <div class="popbox-dropdown__bd">

                            <form action="<?php echo JRoute::_('index.php');?>" class="popbox-dropdown-signin" method="post" data-ed-toolbar-login-form>
                                <div class="form-group">
                                    <label for="ed-username"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_USERNAME');?>:</label>
                                    <input name="username" type="text" class="form-control" id="ed-username" placeholder="<?php echo JText::_('Username');?>" />
                                </div>
                                <div class="form-group">
                                    <label for="ed-password"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_PASSWORD');?>:</label>
                                    <input name="password" type="password" class="form-control" id="ed-password" placeholder="<?php echo JText::_('Password');?>" />
                                </div>
                                <div class="o-row">
                                    <div class="o-col o-col--8">
                                        <div class="o-checkbox o-checkbox--sm">
                                            <input type="checkbox" id="ed-remember" name="remember" />
                                            <label for="ed-remember"><?php echo JText::_('COM_EASYDISCUSS_REMEMBER_ME');?></label>
                                        </div>
                                    </div>
                                    <div class="o-col">
                                        <button class="btn btn-primary btn-sm pull-right"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_SIGN_IN');?></button>
                                    </div>
                                </div>
                                <?php if ($this->config->get('integrations_jfbconnect') && ED::jfbconnect()->exists()) { ?>
                                    <div class="o-row">
                                        {JFBCLogin}
                                    </div>
                                <?php } ?>
                                <input type="hidden" value="com_users"  name="option" />
                                <input type="hidden" value="user.login" name="task" />
                                <input type="hidden" name="return" value="<?php echo $return; ?>" />
                                <input type="hidden" name="<?php echo ED::getToken();?>" value="1" />
                            </form>
                        </div>

                        <div class="popbox-dropdown__ft">
                            <a href="<?php echo ED::getResetPasswordLink();?>" class="popbox-dropdown__note pull-left"><?php echo JText::_('COM_EASYDISCUSS_FORGOT_PASSWORD');?></a>
                        </div>
                    </div>
                </div>
            </li>
            <?php } ?>
        </ul>

        <?php if ($this->acl->allowed('add_question') && !$post->isUserBanned()) { ?>
            <a
            data-ed-provide="tooltip"
            data-original-title="<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_NEW_DISCUSSION');?>"
            href="<?php echo EDR::_('view=ask');?>" class="btn btn-primary ed-navbar__btn-ask t-lg-mr--md pull-right">
                <i class="fa fa-pencil"></i>
            </a>
        <?php } ?>
    </div>
    <div class="ed-navbar__footer">
        <div class="o-row">
            <div class="o-col">
                <a href="javascript:void(0);" class="btn btn-sm btn-default ed-navbar-submenu-toggle" data-ed-navbar-submenu-toggle>Toggle Sub Menu</a>
            	<ol class="g-list-inline g-list-inline--dashed ed-navbar__footer-submenu" data-ed-navbar-submenu>
            	    <li class="<?php echo $active == 'forums' ? ' is-active' : '';?>">
            	    	<a href="<?php echo EDR::_('view=forums');?>" class="ed-navbar__footer-link"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_FORUMS');?></a>
            	    </li>

                    <?php if ($showRecent) { ?>
            	    <li class="<?php echo $active == 'index' ? ' is-active' : '';?>">
            	    	<a href="<?php echo EDR::_('view=index');?>" class="ed-navbar__footer-link"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_RECENT');?></a>
            	    </li>
                    <?php } ?>

                    <?php if ($showCategories) { ?>
            	    <li class="<?php echo $active == 'categories' ? ' is-active' : '';?>">
            	    	<a href="<?php echo EDR::_('view=categories');?>" class="ed-navbar__footer-link"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_CATEGORIES');?></a>
            	    </li>
                    <?php } ?>

                    <?php if ($showTags) { ?>
            	    <li class="<?php echo $active == 'tags' ? ' is-active' : '';?>">
            	    	<a href="<?php echo EDR::_('view=tags');?>" class="ed-navbar__footer-link"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_TAGS');?></a>
            	    </li>
                    <?php } ?>

                    <?php if ($this->config->get('integration_easysocial_members') && ED::easysocial()->exists()) { ?>
                    <li class="<?php echo $active == 'users' ? ' is-active' : '';?>">
                        <a href="<?php echo ESR::users();?>" class="ed-navbar__footer-link"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_USERS');?></a>
                    </li>
                    <?php } else if ($showUsers && $this->config->get('main_user_listings')) { ?>
            	    <li class="<?php echo $active == 'users' ? ' is-active' : '';?>">
            	    	<a href="<?php echo EDR::_('view=users');?>" class="ed-navbar__footer-link"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_USERS');?></a>
            	    </li>
                    <?php } ?>

                    <?php if ($showBadges && $this->config->get('main_badges')) { ?>
            	    <li class="<?php echo $active == 'badges' ? ' is-active' : '';?>">
            	    	<a href="<?php echo EDR::_('view=badges');?>" class="ed-navbar__footer-link"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_BADGES');?></a>
            	    </li>
                    <?php } ?>

                    <?php if ($group) { ?>
                    <li class="<?php echo $active == 'groups' ? ' is-active' : '';?>">
                        <a href="<?php echo EDR::_('view=groups');?>" class="ed-navbar__footer-link"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_GROUPS');?></a>
                    </li>                    
                    <?php } ?>

            	</ol>
            </div>
            <?php if (ED::work()->enabled()) { ?>
            <div class="o-col o-col--4">
                <?php echo ED::work(ED::date())->html(); ?>
            </div>
            <?php } ?>
        </div>

    </div>
</div>
<?php } ?>

<?php echo $header; ?>

<?php if ($renderToolbarModule) { ?>
<?php echo ED::renderModule('easydiscuss-after-toolbar'); ?>
<?php } ?>

<?php if($messageObject) { ?>
	<div class="o-alert o-alert--<?php echo $messageObject->type;?>">
		<?php echo $messageObject->message; ?>
	</div>
<?php } ?>
