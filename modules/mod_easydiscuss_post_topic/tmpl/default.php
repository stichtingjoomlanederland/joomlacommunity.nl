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
// no direct access
defined('_JEXEC') or die('Restricted access');
$user				= JFactory::getUser();

if( !empty($user->id) )
{
	$postId				= JRequest::getInt('id', 0);
	$postCat            = '';

	if( $postId )
	{
		$post	= DiscussHelper::getTable('Posts');
		$post->load($postId);

		$postCat    = $post->category_id;
	}
	$nestedCategories	= DiscussHelper::populateCategories('', '', 'select', 'mod_post_topic_category_id', $postCat, true, true);
}

?>

<?php if( !empty($user->id )){ ?>
<div id="discuss-post-topic" class="discuss-mod discuss-post-topic<?php echo $params->get( 'moduleclass_sfx' ) ?>">
	<form id="mod_post_topic" name="mod_post_topic" action="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&controller=posts&task=submit'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
		<div>
			<div>
				<?php echo $nestedCategories; ?>
			</div>
			<input type="text" id="post-topic-title" name="title" placeholder="<?php echo JText::_('MOD_POST_TOPIC_QUESTION' , true ); ?>" class="full-width input input-title" value="" onchange="this.form.dc_reply_content.value=this.value;" />
			<textarea style="display: none;" name="dc_reply_content"></textarea>

			<input type="button" class="btn-ask submitDiscussion" value="<?php echo JText::_('MOD_POST_TOPIC_QUESTION_SUBMIT' , true ); ?>" onclick="discuss.post.postTopicSubmit();" />

		</div>
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>
<?php }else{ ?>

<a href="<?php echo DiscussRouter::_('index.php?option=com_users&views=login'); ?>"><?php echo JText::_('MOD_LOGIN' , true ); ?></a>

<?php } ?>
