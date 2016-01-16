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
<div class="fd-cf">
	<h2 class="discuss-component-title pull-left"><?php echo JText::_('COM_EASYDISCUSS_MEMBERS'); ?></h2>
	<div class="discuss-users-search-wrap pull-right">
		<div class="input-append">
			<form name="discuss-users-search" method="GET" action="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=users'); ?>" style="margin:0;">
				<input type="text" class="input-large" placeholder="<?php echo JText::_( 'COM_EASYDISCUSS_USERS_SEARCH_PLACEHOLDER' );?>" name="userQuery" value="<?php echo DiscussHelper::getHelper( 'String' )->escape($userQuery) ? DiscussHelper::getHelper( 'String' )->escape($userQuery) : '';?>" />
				<input type="hidden" name="option" value="com_easydiscuss" />
				<input type="hidden" name="view" value="users" />
				<button class="btn btn-searchbar"><?php echo JText::_( 'COM_EASYDISCUSS_SEARCH_BUTTON' );?></button>
			</form>
		</div>
	</div>
</div>

<hr />
<div class="fd-cf">
	<!-- default: <ul class="unstyled discuss-list discuss-list-grid discuss-users-list"> -->
	<!-- small avatar: <ul class="unstyled discuss-list discuss-list-grid discuss-users-list discuss-users-list-small"> -->

	<ul class="unstyled discuss-list discuss-list-grid discuss-users-list <?php echo $system->config->get('main_members_small')? 'discuss-users-list-small' : 'discuss-users-list' ?>">
		<?php foreach( $users as $user ){ ?>
			<?php echo $this->loadTemplate( 'users.item.php' , array( 'user' => $user ) ); ?>
		<?php } ?>
	</ul>
</div>

<?php echo $pagination->getPagesLinks();?>

