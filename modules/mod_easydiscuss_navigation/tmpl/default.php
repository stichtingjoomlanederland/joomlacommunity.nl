<?php
/**
 * @package		mod_easydiscuss_navigation
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
<script type="text/javascript">
EasyDiscuss
.require()
.script( 'toolbar' )
.done(function($){

	$( '#discuss-navigation' ).implement( EasyDiscuss.Controller.Toolbar );

});
</script>
<div id="discuss-navigation" class="discuss-mod">
	<div class="navbar">
		<div class="discuss-navigation-title"><?php echo JText::_( 'MOD_NAVIGATION_FORUMS' );?>:</div>

		<ul class="nav nav-pills">
			<li>
				<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=categories'); ?>"><?php echo JText::_( 'MOD_NAVIGATION_ALL' ); ?></a>
			</li>

			<?php foreach( $categories as $category ){ ?>
				<?php
					$totalNew 	= ( $my->id > 0 ) ? $category->getUnreadCount() : '0';
					$postCount	= $category->getPostCount();
				?>

				<?php if( $params->get( 'display_empty_category' ) ){ ?>
					<li class="category-item<?php echo $category->id == $active ? ' active' : '';?>">
						<a href="<?php echo DiscussRouter::getCategoryRoute( $category->id );?>" data-category="<?php echo $category->id;?>"><?php echo JText::_( $category->title ); ?></a>

						<?php if( $totalNew > 0 ){ ?>
						<div class="discuss-notice-bubble"><?php echo $totalNew; ?></div>
						<?php } ?>
					</li>
				<?php }else{ ?>
					<?php if( $postCount > 0 ){ ?>
						<li class="category-item<?php echo $category->id == $active ? ' active' : '';?>">
							<a href="<?php echo DiscussRouter::getCategoryRoute( $category->id );?>" data-category="<?php echo $category->id;?>"><?php echo JText::_( $category->title ); ?></a>

							<?php if( $totalNew > 0 ){ ?>
							<div class="discuss-notice-bubble"><?php echo $totalNew; ?></div>
							<?php } ?>
						</li>
					<?php } ?>
				<?php } ?>
			<?php } ?>
		</ul>

		<?php if( $my->id > 0 && $params->get( 'display_notification_button' ) ){ ?>
		<ul class="nav nav-right">
			<li class="dropdown_ dc-mod-dropdown">
				<a class="btn-notification notificationLink" href="javascript:void(0);">
					<em class="icon-dc-mod-list"></em>
					<span id="mod-notification-count" style="display: <?php echo $totalNotifications > 0 ? 'inline-block' : 'none';?>" class="discuss-notice-bubble"><?php echo $totalNotifications; ?></span>
				</a>
				<!-- @Javascript Parent wrapper with overflow hidden issue need to fix -->
				<ul class="dropdown-menu dropdown-menu-large notificationDropDown" style="display: none;">
					<li>
						<div class="discuss-notice-menu">
							<ul class="unstyled notification-result notificationResult">
								<li class="loading-indicator notificationLoader"><i><?php echo JText::_( 'COM_EASYDISCUSS_LOADING' );?></i></li>
							</ul>

							<div class="modal-footer pt-0 pb-5">
								<a class="btn btn-link small" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=notifications' );?>"><?php echo JText::_( 'COM_EASYDISCUSS_VIEW_ALL_NOTIFICATIONS' );?></a>
							</div>
						</div>
					</li>
				</ul>
			</li>
		</ul>
		<?php } ?>
	</div>
</div>
