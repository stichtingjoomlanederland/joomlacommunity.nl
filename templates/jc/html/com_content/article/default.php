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

// Includee easydiscuss helper to load profile information
require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php');

// Create shortcuts to some parameters.
$params  = $this->item->params;
$images  = json_decode($this->item->images);
$urls    = json_decode($this->item->urls);
$canEdit = $params->get('access-edit');
$user    = JFactory::getUser();

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

// Load the profile data from the database.
$profile = DiscussHelper::getTable('Profile');
$profile->load($this->item->created_by);
$userparams        = DiscussHelper::getRegistry($profile->params);
$profile->twitter  = $userparams->get('twitter', '');
$profile->website  = $userparams->get('website', '');
$profile->facebook = $userparams->get('facebook', '');
$profile->linkedin = $userparams->get('linkedin', '');

// Determ if the article information column must be shown or not
$showArticleInformation = ($params->get('show_create_date') && $params->get('show_category') && $params->get('show_author'));

?>
<div class="well <?php echo ($image == 'large' ? 'photoheader' : ''); ?>">
	<?php if ($image == 'large'): ?>
		<div class="photobox">
			<img src="<?php echo($images->image_fulltext); ?>"/>
		</div>
	<?php endif; ?>
	<div class="row">
		<?php if ($showArticleInformation != false) : ?>
			<div class="col-2">
				<?php if ($image == 'small'): ?>
					<div class="photoboxsmall<?php if ($images->float_intro == 'right'): ?> logo<?php endif; ?>">
						<img src="<?php echo($images->image_intro); ?>"/>
					</div>
				<?php endif; ?>
				<div class="item-meta">
					<?php if ($params->get('show_create_date')) : ?>
					<div class="item-datum">
						<strong>Datum</strong>
						<p>
							<time class="post-date"><?php echo JHtml::_('date', $this->item->created, JText::_('j F Y')); ?></time>
						</p>
					</div>
					<?php endif; ?>

					<?php if ($params->get('show_category')) : ?>
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
					<?php endif; ?>

					<?php if ($params->get('show_author')) : ?>
					<div class="item-auteur">
						<strong>Door</strong>
						<p>
							<?php echo JHtml::_('link', $profile->getLink(), $profile->user->get('name')); ?>
						</p>
					</div>
					<?php endif; ?>

					<div class="item-share full">
						<?php
						$data = array(
							'title'      => 'Share',
							'facebook'   => true,
							'twitter'    => true,
							'googleplus' => true,
							'item'       => $this->item
						);
						echo JLayoutHelper::render('template.snippet-share-page', $data);
						?>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<div class="<?php echo ($showArticleInformation ? 'col-8' : 'col-12'); ?>">
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
					<?php if ($canEdit) : ?>
						<div class="edit-buttons">
							<?php echo JLayoutHelper::render('joomla.content.icons', array('params' => $params, 'item' => $this->item, 'print' => false)); ?>
						</div>
					<?php endif; ?>
					<?php echo $this->item->text; ?>
				</div>
			</div>
		</div>
	</div>

	<?php if ($showArticleInformation != false) : ?>
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
						<a href="<?php echo($profile->twitter); ?>" target="_blank"><span class="icon icon-twitter"></span></a>
					</li>
				<?php endif; ?>
				<?php if ($profile->facebook): ?>
					<li class="share-facebook">
						<a href="<?php echo($profile->facebook); ?>" target="_blank"><span class="icon icon-facebook"></span></a>
					</li>
				<?php endif; ?>
				<?php if ($profile->linkedin): ?>
					<li class="share-linkedin">
						<a href="<?php echo($profile->linkedin); ?>" target="_blank"><span class="icon icon-linkedin"></span></a>
					</li>
				<?php endif; ?>
				<?php if ($profile->website): ?>
					<li class="share-website">
						<a href="<?php echo($profile->website); ?>" target="_blank"><span class="icon icon-website"></span></a>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
	<?php endif; ?>
</div>


<?php echo $this->item->event->afterDisplayContent; ?>

<?php
if (!empty($this->item->pagination) && $this->item->pagination)
{
	echo $this->item->pagination;
}
?>
