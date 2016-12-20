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

require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php');

$profile = DiscussHelper::getTable('Profile');
$profile->load($this->item->created_by);

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

// Determine if the article information column must be shown or not
$showArticleInformation = ($params->get('info_block_show_title')) ? ($params->get('show_create_date') || $params->get('show_category') || $params->get('show_author')) : false;

// Check Joomla version label
$joomla3 = false;
$joomla4 = false;

foreach ($this->item->tags->itemTags as $tag)
{
	$joomla3 = ($tag->title == 'joomla3' && $joomla3 === false) ? true : false;
	$joomla4 = ($tag->title == 'joomla4' && $joomla4 === false) ? true : false;
}
?>
<div class="well <?php echo($image == 'large' ? 'photoheader' : ''); ?>">
	<?php if ($image == 'large'): ?>
        <div class="photobox">
            <img src="<?php echo($images->image_fulltext); ?>"/>
        </div>
	<?php endif; ?>
    <div class="row">
		<?php if ($showArticleInformation != false) : ?>
            <div class="col-md-2">
				<?php if ($image == 'small'): ?>
                    <div class="photoboxsmall<?php if ($images->float_intro == 'right'): ?> logo<?php endif; ?>">
                        <img src="<?php echo($images->image_intro); ?>"/>
                    </div>
				<?php endif; ?>
                <div class="item-meta">
					<?php if ($params->get('show_create_date')) : ?>
                        <div class="article-meta item-datum">
                            <p class="article-meta-label">datum</p>
                            <p>
                                <time class="post-date"><?php echo JHtml::_('date', $this->item->created, JText::_('j F Y')); ?></time>
                                <span class="article-meta-mobile">, </span>
                            </p>
                        </div>
					<?php endif; ?>

					<?php if ($params->get('show_author')) : ?>
                        <div class="article-meta auteur-info">
                            <p class="article-meta-label">door</p>
							<?php if (!empty($this->item->created_by_alias)) : ?>
                                <p>
									<?php echo $this->item->created_by_alias; ?>
                                </p>
							<?php else: ?>
                                <p>
									<?php echo JHtml::_('link', $profile->getLink(), $profile->user->get('name')); ?>
                                </p>
							<?php endif; ?>

                        </div>
					<?php endif; ?>

					<?php if ($params->get('show_category')) : ?>
                        <div class="article-meta item-categorie">
                            <p class="article-meta-label">
                                <span class="article-meta-mobile">in</span>
                                <span class="article-meta-desktop">categorie</span>
                            </p>
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

                    <div class="article-meta item-share full">
						<?php
						$data = array(
							'title'    => 'share',
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
		<?php endif; ?>

        <div class="<?php echo($showArticleInformation ? "col-md-9" : "col-md-12"); ?>">
            <div class="item">
                <div class="page-header">
					<?php if ($canEdit) : ?>
                        <div class="edit-buttons">
							<?php echo JLayoutHelper::render('joomla.content.icons', array('params' => $params, 'item' => $this->item, 'print' => false)); ?>
                        </div>
					<?php endif; ?>

                    <div class="pull-right">
						<?php if ($joomla3): ?>
                            <span class="label label-joomla3"><span class="icon-joomla"></span> Joomla 3.0</span>
						<?php endif; ?>
                    </div>

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

                <div class="item-share item-share-below full">
					<?php
					$data = array(
						'title'    => 'Share:',
						'facebook' => true,
						'twitter'  => true,
						'linkedin' => true,
						'item'     => $this->item,
						'inline'   => true
					);
					echo JLayoutHelper::render('template.snippet-share-page', $data);
					?>
                </div>

				<?php if ($params->get('show_modify_date')) : ?>
                    <div class="articleinfo">
                        <p class="text-muted">
                            <strong>Gepubliceerd:</strong> <?php echo JHtml::_('date', $this->item->created, JText::_('j F Y')); ?>,
                            <strong>aangepast:</strong> <?php echo JHtml::_('date', $this->item->modified, JText::_('j F Y')); ?>
                        </p>
                    </div>
				<?php endif; ?>
            </div>
        </div>
    </div>

	<?php if ($params->get('show_author') && empty($this->item->created_by_alias)) : ?>
		<?php echo JLayoutHelper::render('template.snippet-author', $this->item->created_by); ?>
	<?php endif; ?>
</div>


<?php echo $this->item->event->afterDisplayContent; ?>

<?php if (!empty($this->item->pagination) && $this->item->pagination): ?>
	<?php echo $this->item->pagination; ?>
<?php endif; ?>
