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
	<h3 class="pull-left">
		<?php echo JText::_('COM_EASYDISCUSS_PROFILE_BLOGS_TITLE'); ?>
		(<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=blogger&layout=listings&id='.$user->id); ?>">View Blog <i class="icon-external-link"></i> </a>)
	</h3>
	<div class="pull-right">
		<a href="<?php echo EasyBlogHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $user->id );?>">
			<i class="icon-ed-rss"></i>
			<?php echo JText::_( 'COM_EASYBLOG_SUBSCRIBE_FEEDS' ); ?>
		</a>
	</div>
</div>
<hr />
<?php if( $blogs ){ ?>
<ul class="unstyled discuss-blog-listing">
	<?php foreach( $blogs as $post ) { ?>
	<li>
		<div class="discuss-blog-item">
			<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$post->id); ?>">
				<h2 class="discuss-post-title">
					<?php echo $post->title; ?>
				</h2>
			</a>
			<div class="discuss-blog-meta">
				<span class="discuss-blog-created">
					<?php echo JText::_( 'COM_EASYBLOG_ON' ); ?>
					<time datetime="<?php echo $this->formatDate( '%Y-%m-%d' , $post->created ); ?>">
						<span><?php echo $this->formatDate(JText::_('DATE_FORMAT_LC1'), $post->created); ?></span>
					</time>
				</span>

				<span class="blog-category">
					<?php $categoryName   = $post->getPrimaryCategory()->title; ?>
					<?php echo JText::sprintf( 'COM_EASYBLOG_IN' , $post->getPrimaryCategory()->getPermalink(), $categoryName ); ?>
				</span>
			</div>
			<div class="media">

				<?php if ($post->image) {?>
				<div class="media-object pull-left">
					<a href="<?php echo $post->getPermalink(); ?>" title="<?php echo $this->escape( $post->title );?>" class="blog-image float-l mrm mbm">
					<img src="<?php echo $post->getImage('thumbnail'); ?>" class="discuss-blog-image" /></a>
				</div>
				<?php } ?>

				<div class="media-body">
					<div class="discuss-blog-introtext">
						<?php echo $post->getIntro(); ?>
					</div>

					<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$post->id); ?>" class="btn btn-small"><?php echo JText::_('COM_EASYBLOG_CONTINUE_READING'); ?> &raquo;</a>
				</div>
			</div>

		</div>
	</li>
	<?php } ?>
</ul>
<?php } else { ?>
<div class="empty">
	<?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_BLOG_NO_ENTRIES_YET' );?>
</div>
<?php } ?>
