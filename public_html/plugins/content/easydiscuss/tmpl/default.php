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
<?php include dirname( __FILE__ ) . '/default.scripts.php'; ?>
<div id="ed" data-ed-wrapper>

	<div class="o-row t-lg-mb--lg">
		<?php include dirname(__FILE__) . '/default.likes.php'; ?>
	</div>

	<?php if ($params->get('show_online_users',true)) { ?>
		<?php echo DiscussHelper::getWhosOnline(); ?>
	<?php } ?>

	<a name="replies"></a>

	<div class="o-row t-lg-mb--lg">
		<div class="pull-left">
			<?php echo JText::sprintf('COM_EASYDISCUSS_PLUGIN_TOTAL_RESPONSE', $totalReplies); ?>
		</div>

		<?php if ($params->get('show_discussion_link',true)) { ?>
		<div class="pull-right">
			<a href="<?php echo $post->getPermalink();?>" class="float-r btn btn-small btn-info small"><?php echo JText::_('COM_EASYDISCUSS_PLUGIN_VIEW_ALL_RESPONSES');?></a>
		</div>
		<?php } ?>
	</div>

	<?php include(dirname(__FILE__) . '/default.replies.php'); ?>

	<?php if (!$replies) { ?>
		<div class="empty">
			<?php echo JText::_('COM_EASYDISCUSS_PLUGIN_NO_REPLIES'); ?>
		</div>
	<?php } ?>

	<?php if ($replies && $pagination) { ?>
		<div class="ed-pagination">
			<?php echo $pagination->getPagesLinks(); ?>
		</div>
	<?php } ?>

	<?php if ($params->get('allow_reply', true)) { ?>
		<?php if ($acl->allowed('add_reply')) { ?>
			<?php include dirname(__FILE__) . '/default.form.php'; ?>
		<?php } else if (!$my->id && !$acl->allowed('add_reply')) { ?>
			<?php echo  ED::getLoginForm('COM_EASYDISCUSS_PLEASE_LOGIN_TO_REPLY', base64_encode(EDR::getRoutedURL('index.php?option=com_content&view=article&id=' . $article->id, false, true))); ?>
		<?php } ?>
	<?php } ?>

	<input type="hidden" class="easydiscuss-token" value="<?php echo DiscussHelper::getToken();?>" data-ed-token />
	<input type="hidden" name="pagelimit" id="pagelimit" value="<?php echo $params->get( 'items_count' ); ?>" />
	<input type="hidden" name="total-responses" id="total-responses" value="<?php echo $totalReplies;?>" />

</div>
