<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php');

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

// Determ if the article information column must be shown or not
$showArticleInformation = ($params->get('show_create_date') || $params->get('show_category') || $params->get('show_author'));

?>
<div class="well <?php echo($image == 'large' ? 'photoheader' : ''); ?>">
	<?php if ($image == 'large'): ?>
        <div class="photobox">
            <img src="<?php echo($images->image_fulltext); ?>"/>
        </div>
	<?php endif; ?>
    <div class="row">
        <div class="col-2">
			<?php if ($image == 'small'): ?>
                <div class="photoboxsmall<?php if ($images->float_intro == 'right'): ?> logo<?php endif; ?>">
                    <img src="<?php echo($images->image_intro); ?>"/>
                </div>
			<?php endif; ?>

            <div class="item-meta">
				<?php if ($showArticleInformation != false) : ?>
					<?php if ($params->get('show_author')) : ?>
                        <div class="auteur-info">
							<?php
							if (!empty($this->item->created_by_alias)) : ?>
                                <strong>Door</strong>
                                <p>
									<?php echo $this->item->created_by_alias; ?>
                                </p>
							<?php else: ?>
                                <strong>Door</strong>
                                <p>
									<?php echo JHtml::_('link', $profile->getLink(), $profile->user->get('name')); ?>
                                </p>
							<?php endif; ?>
                        </div>
					<?php endif; ?>

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
								<?php if ($params->get('link_category') && $this->item->catslug) : ?>
                                    <a href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug)); ?>">
										<?php echo $this->escape($this->item->category_title); ?>
                                    </a>
								<?php else : ?>
									<?php echo $this->escape($this->item->category_title); ?>
								<?php endif; ?>
                            </p>
                        </div>
					<?php endif; ?>
				<?php endif; ?>

                <div class="item-share full">
					<?php
					$data = array(
						'title'    => 'Share',
						'facebook' => true,
						'twitter'  => true,
						'linkedin' => true,
						'item'     => $this->item
					);
					echo JLayoutHelper::render('template.snippet-share-page', $data);
					?>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="item">
                <div class="page-header">
					<?php if ($canEdit) : ?>
                        <div class="edit-buttons">
							<?php echo JLayoutHelper::render('joomla.content.icons', array('params' => $params, 'item' => $this->item, 'print' => false)); ?>
                        </div>
					<?php endif; ?>

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

                    <a class="btn btn-nieuws" href="<?php echo $link; ?>">

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
