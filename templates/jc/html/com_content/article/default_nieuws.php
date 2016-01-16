<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// Create shortcuts to some parameters.
$params  = $this->item->params;
$images  = json_decode($this->item->images);
$urls    = json_decode($this->item->urls);
$canEdit = $params->get('access-edit');
$user    = JFactory::getUser();

if (!$images)
{
	//$image = 'none';
	$image = 'large';
}
elseif ($images->image_fulltext)
{
	$image = 'large';
}
elseif ($images->image_intro)
{
	$image = 'small';
}

// Load the profile data from the database.
require_once JPATH_ROOT . '/components/com_easydiscuss/helpers/helper.php';
$profile = DiscussHelper::getTable('Profile');
$profile->load($this->item->created_by);
$userparams        = DiscussHelper::getRegistry($profile->params);
$profile->twitter  = $userparams->get('twitter', '');
$profile->website  = $userparams->get('website', '');
$profile->facebook = $userparams->get('facebook', '');
$profile->linkedin = $userparams->get('linkedin', '');
?>

<div class="well<?php if ($image == 'large'): ?> photoheader<?php endif; ?>">
	<?php if ($image == 'large'): ?>
		<div class="photobox">
			<!--<img src="--><?php //echo($images->image_fulltext); ?><!--"/>-->
			<?php $items = array('images/j3-voorbeeld.jpg', 'images/j3-voorbeeld-2.jpg', 'images/j3-voorbeeld-3.jpg', 'images/j3-voorbeeld-4.jpg'); ?>
			<img src="<?php echo $items[array_rand($items)]; ?>">
		</div>
	<?php endif; ?>
	<div class="row">
		<div class="col-sm-3 col-lg-2">
			<?php if ($image == 'small'): ?>
				<div class="photoboxsmall<?php if ($images->float_intro == 'right'): ?> logo<?php endif; ?>">
					<img src="<?php echo($images->image_intro); ?>"/>
					<!--<img src="http://placehold.it/1600x600"/>-->
				</div>
			<?php endif; ?>
			<div class="item-meta">
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
				<div class="item-auteur">
					<strong>Door</strong>
					<p>
						<?php echo $profile->nickname; ?>
					</p>
				</div>
				<div class="item-share full">
					<strong>Share</strong>
					<ul class="list-inline share-buttons">
						<li class="share-twitter">
							<a href="#"><span class="icon jc-twitter"></span></a>
						</li>
						<li class="share-facebook">
							<a href="https://www.facebook.com/sharer/sharer.php?u=http://www.joomlacommunity.eu/nieuws/joomla-versies/886-joomla-2510-vrijgegeven.html" target="_blank"><span class="icon jc-facebook"></span></a>
						</li>
						<li class="share-googleplus">
							<a href="#"><span class="icon jc-googleplus"></span></a>
						</li>
					</ul>
				</div>

			</div>
		</div>
		<div class="col-sm-9 col-md-8">
			<div class="item">
				<div class="page-header">
					<?php if ($params->get('show_title')) : ?>
						<h1>
							<?php echo $this->escape($this->item->title); ?>
						</h1>
					<?php endif; ?>

					<?php if ($this->item->state == 0) : ?>
						<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
					<?php endif; ?>
				</div>
				<div class="item-content">
					<?php echo $this->item->text; ?>
				</div>
			</div>
		</div>
	</div>

	<div class="row articleinfo">
		<div class="col-sm-2 author-img">
			<a href="<?php echo $profile->getLink(); ?>">
				<img class="img-circle" src="<?php echo $profile->getAvatar(); ?>"/>
			</a>
		</div>
		<div class="col-sm-10">
			<h4><a href="<?php echo $profile->getLink(); ?>"><?php echo $profile->nickname; ?></a></h4>
			<p class="text-muted"><?php echo($profile->description); ?></p>
			<ul class="list-inline share-buttons">
				<?php if ($profile->twitter): ?>
					<li class="share-twitter">
						<a href="<?php echo($profile->twitter); ?>"><span class="icon jc-twitter"></span></a>
					</li>
				<?php endif; ?>
				<?php if ($profile->facebook): ?>
					<li class="share-facebook">
						<a href="<?php echo($profile->facebook); ?>"><span class="icon jc-facebook"></span></a>
					</li>
				<?php endif; ?>
				<?php if ($profile->linkedin): ?>
					<li class="share-linkedin">
						<a href="<?php echo($profile->linkedin); ?>"><span class="icon jc-linkedin"></span></a>
					</li>
				<?php endif; ?>
				<?php if ($profile->website): ?>
					<li class="share-website">
						<a href="<?php echo($profile->website); ?>"><span class="icon jc-website"></span></a>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div>


<?php echo $this->item->event->afterDisplayContent; ?>

<?php
if (!empty($this->item->pagination) && $this->item->pagination)
{
	echo $this->item->pagination;
}
?>
