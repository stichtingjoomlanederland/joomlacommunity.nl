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
require_once DISCUSS_HELPERS . '/jc.php';
?>
<a name="<?php echo JText::_('COM_EASYDISCUSS_TOP_ANCHOR');?>"></a>
<div class="well forum-question discuss-read<?php echo $post->islock ? ' is-locked' : '';?><?php echo !empty($post->password) ? ' is-protected' : '';?><?php echo $post->isresolve ? ' is-resolved' : '';?><?php echo $post->isFeatured() ? ' is-featured' : '';?><?php echo $post->isPollLocked() ? ' is-poll-lock' : '';?>" data-id="<?php echo $post->id;?>">

		<header>
			<?php if( $system->config->get( 'main_allowquestionvote' ) ){ ?>
				<?php echo $this->loadTemplate( 'post.vote.php' , array( 'access' => $access , 'post' => $post ) ); ?>
			<?php } ?>

		<!-- 	<span class="mark-locked"><i data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LOCKED_DESC' );?>" data-placement="top" rel="ed-tooltip" class="i i-lock"></i> &nbsp;</span> -->

			<?php if( !empty($post->password) ) { ?>
			<i data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_PROTECTED_DESC' );?>" data-placement="top" rel="ed-tooltip" class="i i-key"></i>
			<?php } ?>
<?php
$layout	= new JLayoutFile('avatar');
echo $layout->render($post->getOwner()->id);
?>			
			
	
			<div class="topic-info">
					<div class="page-header">
						
						<h2>
							<?php echo $post->title; ?>
						</h2>
						<time datetime="<?php echo $this->formatDate( '%Y-%m-%d' , $post->created ); ?>" class="muted">
							<?php echo DiscussJcHelper::time_delta('now', $post->created, 0); ?>
						</time>
						
						<?php echo JText::_( 'door' ); ?>
						<a href="<?php echo $post->getOwner()->link;?>">
						<?php echo $this->loadTemplate( 'author.name.php' , array( 'post' => $post ) ); ?>
						</a>
						<?php echo JText::_( ' in' ); ?>
				
						<a href="<?php echo DiscussRouter::getCategoryRoute( $category->id );?>"><?php echo $category->getTitle();?></a>
			
				
						
						
						<?php if ( $post->getPostType() ) { ?>
						<div class="forum-labels">
							<span class="label label-<?php echo $post->getPostTypeSuffix(); ?>"><span class="icon jc-joomla"></span> <?php echo $post->getPostType(); ?></span>
						</div>
						<?php } ?>
					</div>
			</div>
		</header>

		<div class="discuss-status">
			
			<?php if ( $post->getStatusMessage() ) { ?>
			<span class="butt butt-label butt-s postStatus label-info label-post_status<?php echo $post->getStatusClass();?>"><?php echo $post->getStatusMessage();?></span>
			<?php } ?>
		</div>
	

		<?php if( !$post->isProtected() || DiscussHelper::isModerator( $post->category_id ) ){ ?>
		<article class="discuss-post-article">
			<?php echo $post->content;?>

			<?php echo DiscussHelper::showSocialButtons( $post, 'horizontal' ); ?>
		</article>

		<?php echo $this->getFieldHTML( true , $post ); ?>

		<?php echo $this->loadTemplate( 'post.customfields.php' ); ?>

		<?php echo $this->loadTemplate( 'post.tags.php' , array( 'tags' => $tags ) ); ?>

		<?php echo $this->loadTemplate( 'post.likes.php' , array( 'post' => $post ) ); ?>

		<?php echo $this->loadTemplate( 'post.comments.php' , array( 'reply' => $post, 'question' => $post  ) ); ?>

		<?php echo $this->loadTemplate( 'post.location.php' , array( 'post' => $post ) ); ?>

		<?php echo $this->loadTemplate( 'post.signature.php' , array( 'signature' => $post->getOwner()->signature ) ); ?>

		<?php } else { ?>
		<article class="discuss-post-password"><?php echo $this->loadTemplate( 'entry.password.php' , array( 'post' => $post ) ); ?></article>
		<?php } ?>

		<footer>
			<?php echo $this->loadTemplate( 'post.actions.php' , array( 'access' => $access , 'post' => $post ) ); ?>
		</footer>

		<?php // echo $this->loadTemplate( 'post.reply.comments.php' , array( 'post' => $post ) ); ?>

</div>
