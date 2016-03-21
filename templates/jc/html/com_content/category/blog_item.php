<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Inlcude easydiscuss helper for avatar
require_once JPATH_ROOT . '/components/com_easydiscuss/helpers/helper.php';

$profile = DiscussHelper::getTable('Profile');
$profile->load($this->item->created_by);

// Create a shortcut for params.
$params = $this->item->params;
$images = json_decode($this->item->images);

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$canEdit = $this->item->params->get('access-edit');

if (!empty($images->image_fulltext))
{
	$image = 'large';
}
elseif (!empty($images->image_intro))
{
	$image = 'small';
}
else
{
	$image = 'none';
}
?>
<div class="well <?php echo ($image == 'large' ? 'photoheader' : ''); ?>">
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
					<?php if (!empty($profile->getAvatar())) : ?>
						<img src="<?php echo $profile->getAvatar(); ?>" class="img-circle">
					<?php else: ?>
						<img src="media/com_easydiscuss/images/default.png" class="img-circle">
					<?php endif; ?>
				</a>

				<div class="auteur-info">
					<?php echo JHtml::_('link', $profile->getLink(), $profile->user->get('name')); ?>
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
							<?php if ($params->get('link_category') && $this->item->catslug) : ?>
								<a href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug)); ?>">
									<?php echo $this->escape($this->item->category_title); ?>
								</a>
							<?php else : ?>
								<?php echo $this->escape($this->item->category_title); ?>
							<?php endif; ?>
						</time>
					</p>
				</div>

				<!-- @TODO Integrate social stats
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
			-->
			</div>
		</div>

		<div class="col-9">
			<div class="item">
				<div class="page-header">
					<?php if ($params->get('show_title')) : ?>
						<h2>
							<?php if ($params->get('link_titles') && $params->get('access-view')) : ?>
								<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid)); ?>">
									<?php echo $this->escape($this->item->title); ?>
								</a>
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
