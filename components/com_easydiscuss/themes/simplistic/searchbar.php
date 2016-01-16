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
<?php echo DiscussHelper::renderModule( 'easydiscuss-before-searchbar' ); ?>

<?php if( $system->config->get( 'layout_toolbar_searchbar' ) ){ ?>
<div class="discuss-searchbar">
<form name="discuss-search" method="GET" action="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=search'); ?>">
	<div class="discuss-table">

		<div class="discuss-tablecell discuss-searchbar--left discuss-searchbar--avatar">
			<?php if( $system->config->get( 'layout_avatar' ) ){ ?>
			<div class="discuss-avatar avatar-medium avatar-circle pull-left">
				<a href="<?php echo $system->profile->getLink();?>"><img src="<?php echo $system->profile->getAvatar();?>" alt="<?php echo $this->escape( $system->profile->getName() );?>" /></a>
			</div>
			<?php } ?>
		</div>


		<?php if ($system->config->get('layout_toolbar_cat_filter')) { ?>
			<?php if (!empty($nestedCategories)) { ?>
				<div class="discuss-tablecell discuss-searchbar--left discuss-searchbar--filter">
					<div class="discuss-searchbar--select">
						<?php echo $nestedCategories; ?>
					</div>
				</div>
			<?php } ?>
		<?php } ?>


		<div class="discuss-tablecell discuss-searchbar--center">
			<div class=" discuss-searchbar--input">

				<input type="text" class="input-searchbar" style="position: inherit;" placeholder="<?php echo JText::_( 'COM_EASYDISCUSS_SEARCH_PLACEHOLDER' );?>" name="query" value="<?php echo DiscussHelper::getHelper( 'String' )->escape($query) ? DiscussHelper::getHelper( 'String' )->escape($query) : '';?>" />

				<input type="hidden" name="option" value="com_easydiscuss" />
				<input type="hidden" name="view" value="search" />
				<input type="hidden" name="Itemid" value="<?php echo DiscussRouter::getItemId('search'); ?>" />

				<div class="discuss-tablecell">
					<button class="btn btn-searchbar"><?php echo JText::_( 'COM_EASYDISCUSS_SEARCH_BUTTON' );?></button>
				</div>


			</div>
		</div>
		<div class="discuss-tablecell discuss-searchbar--right discuss-searchbar--ask">
			<?php if( $acl->allowed( 'add_question' ) && $system->config->get( 'layout_toolbarcreate' ) ){ ?>
				<a class="btn btn-<?php echo $system->config->get('layout_ask_color'); ?> btn-ask pull-left" href="<?php echo DiscussRouter::getAskRoute( $categoryId );?>"><?php echo JText::_( 'COM_EASYDISCUSS_OR_ASK_A_QUESTION' );?></a>
			<?php } else if( $system->my->id == 0 ) { ?>
				<a class="btn btn-<?php echo $system->config->get('layout_ask_color'); ?> btn-ask pull-left" href="<?php echo DiscussHelper::getRegistrationLink();?>"><?php echo JText::_( 'COM_EASYDISCUSS_OR_ASK_A_QUESTION' );?></a>
			<?php } ?>
		</div>

	</div>
</form>
</div>

<?php } ?>
<?php echo DiscussHelper::renderModule( 'easydiscuss-after-searchbar' ); ?>
