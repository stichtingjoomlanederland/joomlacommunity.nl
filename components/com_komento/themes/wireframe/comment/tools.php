<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
if ($system->my->allow('read_comment')) { ?>
<script type="text/javascript">
Komento.require().script('komento.commenttools').done(function($) {
	if($('.commentTools').exists()) {
		Komento.options.element.tools = $('.commentTools').addController('Komento.Controller.CommentTools');
		Komento.options.element.tools.kmt = Komento.options.element;
	}
});

</script>
<div class="commentTools kmt-comment-tools-wrap">
<!-- Comment Title -->
<h3 class="kmt-title">
	<?php echo JText::_( 'COM_KOMENTO_COMMENTS' ); ?>
	<?php if ($commentCount) { ?>
	(<span class="commentCounter"><?php echo $commentCount; ?></span>)
	<?php } ?>
</h3>

<?php if ($system->my->allow('read_comment')) { ?>

<div class="kmt-toolbar row-table">
	
	<?php if ($system->config->get('show_sort_buttons')) { ?>
	<div class="kmt-sort col-cell">
		<div class="sortOldest kmt-sort-oldest kmt-sorting">
			<a href="javascript:void(0);"<?php echo JRequest::getCmd('kmt-sort', $system->config->get('default_sort')) == 'oldest' ? ' class="selected"' : ''; ?>><?php echo JText::_('COM_KOMENTO_SORT_OLDEST');?></a>
		</div>
		<div class="sortLatest kmt-sort-latest kmt-sorting">
			<a href="javascript:void(0);"<?php echo JRequest::getCmd('kmt-sort', $system->config->get('default_sort')) == 'latest' ? ' class="selected"' : ''; ?>><?php echo JText::_('COM_KOMENTO_SORT_LATEST');?></a>
		</div>
	</div>
	<?php } ?>
	

	<div class="col-cell cell-tight">
		<?php if ($system->konfig->get('enable_admin_mode') && (($system->my->id == $componentHelper->getAuthorId() && $system->my->allow('author_publish_comment')) || $system->my->allow('publish_all_comment'))) { ?>
		<div class="kmt-admin-mode adminMode">
			<a href="javascript:void(0)"><?php echo JText::_('COM_KOMENTO_ADMIN_MODE'); ?></a>
		</div>
		<?php } ?>

		<?php if ($system->config->get('enable_rss') || $system->config->get('enable_subscription')) { ?>
		<div class="col-cell pr-5">
			<?php echo JText::_( 'COM_KOMENTO_FORM_SUBSCRIBE' ); ?>:
		</div>
		<?php } ?>

		<?php if ($system->config->get('enable_subscription')) { ?>
			<?php $subscribed = null;

			if ($system->my->id) {
				$subscribed = Komento::getModel('subscription')->checkSubscriptionExist($component, $cid, $system->my->id);
			}
			?>

			<?php if (is_null($subscribed)) { ?>
			<div class="kmt-subs-email col-cell subscribeEmail">
				<a href="javascript:void(0);">
					<?php echo JText::_( 'COM_KOMENTO_FORM_EMAIL' ); ?>
				</a>
			</div>
			<?php } else { ?>
			<div class="kmt-subs-email col-cell subscribeEmail">
				<a href="javascript:void(0);">
					<?php echo JText::_( 'COM_KOMENTO_FORM_UNSUBSCRIBE' ); ?>
				</a>
			</div>
			<?php } ?>
		<?php } ?>

		<!-- START: Pro Version Only -->
		<?php if ($system->config->get('enable_rss')) { ?>
		<div class="kmt-subs-rss col-cell">
			<a href="<?php echo Komento::getHelper('router')->getFeedUrl($component, $cid); ?>"><i class="kmt-ico"></i>
				<?php echo JText::_( 'COM_KOMENTO_FORM_RSS' ); ?>
			</a>
		</div>
		<?php } ?>
		<!-- END: Pro Version Only -->
	</div>
</div>
<?php } ?>
</div>
<?php } ?>
