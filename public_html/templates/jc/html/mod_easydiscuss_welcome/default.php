<?php
/**
 * @package      EasyDiscuss
 * @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
 * @license      GNU/GPL, see LICENSE.php
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>


<ul class="nav navbar-nav navbar-right navbar-user">
    <li class="dropdown">
        <a data-toggle="dropdown" class="dropdown-toggle profile" href="#">
			<?php if ($isLoggedIn) : ?>
                Welkom!
			<?php else: ?>
                Login
			<?php endif; ?>
            <img src="<?php echo $my->getAvatar(); ?>" class="avatar">
			<?php if ($isLoggedIn) : ?>
                <span class="dropdown-toggle-text"><?php echo $my->getName(); ?></span>
			<?php else: ?>
                <span class="dropdown-toggle-text">Welkom!</span>
			<?php endif; ?>
        </a>
        <ul class="dropdown-menu">
			<?php if ($isLoggedIn) : ?>
                <li>
                    <a class="user-discussions" href="<?php echo $my->getLink(); ?>">
                        <span><?php echo $my->getName(); ?></span>
                    </a>
                </li>

                <li>
                    <a class="edit-profile" href="<?php echo ED::getEditProfileLink(); ?>">
                        <i class="fa fa-cog t-lg-mr--sm"></i>&nbsp;
                        <span><?php echo JText::_('MOD_EASYDISCUSS_WELCOME_EDIT_PROFILE'); ?></span>
                    </a>
                </li>

                <li>
                    <a class="my-favourites" href="<?php echo EDR::_('index.php?option=com_easydiscuss&view=favourites'); ?>">
                        <i class="fa fa-heart-o t-lg-mr--sm"></i>&nbsp;
                        <span><?php echo JText::_('Bekijk favorieten'); ?></span>
                    </a>
                </li>

                <li>
                    <a class="my-subscriptions" href="<?php echo EDR::_('index.php?option=com_easydiscuss&view=subscription'); ?>">
                        <i class="fa fa-inbox t-lg-mr--sm"></i>&nbsp;
                        <span><?php echo JText::_('Bekijk abonnementen'); ?></span>
                    </a>
                </li>

                <li>
                    <a class="user-discussions" href="<?php echo $my->getLink(); ?>">
                        <i class="fa fa-file-text-o t-lg-mr--sm"></i>&nbsp;
                        <span><?php echo JText::_('MOD_EASYDISCUSS_WELCOME_MY_DISCUSSIONS'); ?></span>
                    </a>
                </li>

                <li>
                    <a class="discuss-logout" href="<?php echo JRoute::_('index.php?option=com_users&task=user.logout&' . ED::getToken() . '=1&return=' . $return); ?>">
                        <i class="fa fa-sign-out t-lg-mr--sm"></i>&nbsp;
                        <span><?php echo JText::_('MOD_EASYDISCUSS_WELCOME_SIGN_OUT'); ?></span>
                    </a>
                </li>
			<?php else: ?>
                <li style="padding: 15px; padding-bottom: 0;">
                    <form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" name="login" id="form-login">
                        <div class="form-group">
                            <input type="text" id="username" name="username" class="form-control" placeholder="Gebruikersnaam" style="margin-bottom:15px;" autocomplete="username">
                        </div>
                        <div class="form-group">
                            <input type="password" id="password" name="password" class="form-control" placeholder="Wachtwoord" style="margin-bottom:15px;" autocomplete="current-password">
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <input type="checkbox" id="modlgn_remember" name="remember" value="yes" title="<?php echo JText::_('MOD_EASYDISCUSS_WELCOME_REMEMBER_ME'); ?>" alt="<?php echo JText::_('MOD_EASYDISCUSS_WELCOME_REMEMBER_ME'); ?>">
                                <label for="modlgn_remember"><?php echo JText::_('Blijf aangemeld'); ?></label>
                                <input type="submit" value="<?php echo JText::_('Login'); ?>" name="Submit" class="btn btn-primary btn-small pull-right">
                            </div>
                        </div>

                        <input type="hidden" name="option" value="com_users"/>
                        <input type="hidden" name="task" value="user.login"/>
                        <input type="hidden" name="return" value="<?php echo $return; ?>"/>
						<?php echo JHTML::_('form.token'); ?>
                    </form>
                </li>

                <li class="divider"></li>

                <li>
                    <a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
						<?php echo JText::_('Wachtwoord vergeten?'); ?>
                    </a>
                </li>

                <li>
                    <a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
						<?php echo JText::_('Gebruikersnaam vergeten?'); ?>
                    </a>
                </li>

                <li class="divider"></li>

                <li>
					<?php if ($allowRegister): ?>
                        <a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
							<?php echo JText::_('Maak een account aan'); ?>
                        </a>
					<?php endif; ?>
                </li>
			<?php endif; ?>
        </ul>
    </li>
</ul>
