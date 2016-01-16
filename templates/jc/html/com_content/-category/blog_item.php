<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die; ?>
<?php
// Create a shortcut for params.
$params = $this->item->params;
$images = json_decode($this->item->images);
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$canEdit = $this->item->params->get('access-edit');

if (!$images)
{
	$image = 'none';
}
elseif ($images->image_fulltext)
{
	$image = 'large';
}
elseif ($images->image_intro)
{
	$image = 'small';
}
?>


<div class="well<?php if ($image == 'large'): ?> photoheader<?php endif; ?>">
	<?php if ($image == 'large'): ?>
		<div class="photobox">
			<img src="<?php echo($images->image_fulltext); ?>"/>
		</div>
	<?php endif; ?>
	<div class="row">
		<div class="col-3">
			<?php if ($image == 'small'): ?>
				<div class="photoboxsmall<?php if ($images->float_intro == 'right'): ?> logo<?php endif; ?>">
					<img src="<?php echo($images->image_intro); ?>"/>
				</div>
			<?php endif; ?>

			<div class="item-meta">
				<a href="#" class="auteur-image">
					<?php $authorid = $this->item->created_by;//echo($authorid);?>
					<?php if ($authorid == 64): ?>
						<img src="https://pbs.twimg.com/profile_images/425632290765418498/6SemXPR0_bigger.jpeg" class="img-circle">
					<?php elseif ($authorid == 124): ?>
						<img src="https://si0.twimg.com/profile_images/2243458847/ruud_bigger.jpg" class="img-circle">
					<?php elseif ($authorid == 258): ?>
						<img src="https://si0.twimg.com/profile_images/1072716082/martijn3_bigger.jpg" class="img-circle">
					<?php elseif ($authorid == 157): ?>
						<img src="https://si0.twimg.com/profile_images/3303597413/c25b10a09564e2bf373d6994510d5da4_bigger.png" class="img-circle">
					<?php elseif ($authorid == 3915): ?>
						<img src="https://si0.twimg.com/profile_images/1282882047/foto_bigger.jpg" class="img-circle">
					<?php else: ?>
						<img src="media/com_easydiscuss/images/default.png" class="img-circle">
					<?php endif; ?>
				</a>

				<div class="auteur-info">
					<p><a href="#" title="Ga naar het profiel van <?php echo $author; ?>" rel="author">
							<?php $author = $this->item->author; ?>
							<?php $author = ($this->item->created_by_alias ? $this->item->created_by_alias : $author); ?>
							<?php if (!empty($this->item->contactid) && $displayData['params']->get('link_author') == true) : ?>
								<?php
								echo JText::sprintf('COM_CONTENT_WRITTEN_BY',
									JHtml::_('link', JRoute::_('index.php?option=com_contact&view=contact&id=' . $this->item->contactid), $author)
								); ?>
							<?php else : ?>
								<?php echo $author; ?>
							<?php endif; ?>
						</a></p>
				</div>

				<div class="item-datum">
					<strong>Datum</strong>
					<p>
						<time class="post-date"><?php echo JHtml::_('date', $this->item->created, JText::_('j F Y')); ?></time>
					</p>
				</div>

				<div class="item-categorie">
					<strong>Categorie</strong>
					<p>
						<time class="post-date">
							<?php $title = $this->escape($this->item->category_title);
							$url         = '<a href="' . JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug)) . '">' . $title . '</a>'; ?>
							<?php if ($params->get('link_category') && $this->item->catslug) : ?>
								<?php echo $url; ?>
							<?php else : ?>
								<?php echo $title; ?>
							<?php endif; ?>
						</time>
					</p>
				</div>

				<div class="item-share">
					<ul class="list-inline share-buttons">
						<li class="share-twitter">
							<a href="#"><span class="icon jc-twitter"></span>12</a>
						</li>
						<li class="share-facebook">
							<a href="https://www.facebook.com/sharer/sharer.php?u=http://www.joomlacommunity.eu/nieuws/joomla-versies/886-joomla-2510-vrijgegeven.html" target="_blank"><span class="icon jc-facebook"></span>6</a>
						</li>
						<li class="share-googleplus">
							<a href="#"><span class="icon jc-googleplus"></span>4</a>
						</li>
					</ul>
				</div>

			</div>
		</div>

		<div class="col-9">
			<div class="item">
				<div class="page-header">
					<?php if ($params->get('show_title')) : ?>
						<h2>
							<?php if ($params->get('link_titles') && $params->get('access-view')) : ?>
								<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid)); ?>">
									<?php echo $this->escape($this->item->title); ?></a>
							<?php else : ?>
								<?php echo $this->escape($this->item->title); ?>
							<?php endif; ?>
						</h2>
					<?php endif; ?>

					<?php if ($this->item->state == 0) : ?>
						<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
					<?php endif; ?>
				</div>
				<div class="item-content">
					<?php echo $this->item->introtext; ?>
				</div>
				<?php if ($params->get('show_readmore') && $this->item->readmore) :
					$link = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid));
					?>

					<a class="btn btn-danger" href="<?php echo $link; ?>"> <span class="icon-chevron-right"></span>

						<?php if ($readmore = $this->item->alternative_readmore) :
							echo $readmore;
							if ($params->get('show_readmore_title', 0) != 0) :
								echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
							endif;
						elseif ($params->get('show_readmore_title', 0) == 0) :
							echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
						else :
							echo JText::_('COM_CONTENT_READ_MORE');
							echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
						endif; ?>

					</a>

				<?php endif; ?>
			</div>
		</div>
	</div>
</div>