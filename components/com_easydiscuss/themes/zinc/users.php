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
<header>
	<h2><?php echo JText::_('COM_EASYDISCUSS_MEMBERS'); ?></h2>
</header>

<article id="dc_users">
	<div class="discuss-users-search-wrap mt-10">
		<div class="form-inline">
			<form name="discuss-users-search" method="GET" action="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=users'); ?>">
				<button class="butt butt-default float-r"><?php echo JText::_( 'COM_EASYDISCUSS_SEARCH_BUTTON' );?></button>
				<div style="overflow:hidden; padding-right: 10px;">
					<input type="text" class="form-control input-users-searchbar" placeholder="<?php echo JText::_( 'COM_EASYDISCUSS_USERS_SEARCH_PLACEHOLDER' );?>" name="userQuery" value="<?php echo DiscussHelper::getHelper( 'String' )->escape($userQuery) ? DiscussHelper::getHelper( 'String' )->escape($userQuery) : '';?>" />
				</div>
				<input type="hidden" name="option" value="com_easydiscuss" />
				<input type="hidden" name="view" value="users" />
			</form>
		</div>
	</div>

	<hr />

	<ul class="discuss-grid grid-users reset-ul float-li clearfix<?php echo $system->config->get('main_members_small')? ' discuss-users-list-small' : ' discuss-users-list' ?>">
		<?php foreach( $users as $user ){ ?>
			<?php echo $this->loadTemplate( 'users.item.php' , array( 'user' => $user ) ); ?>
		<?php } ?>
	</ul>

	<?php echo $pagination->getPagesLinks();?>
</article>