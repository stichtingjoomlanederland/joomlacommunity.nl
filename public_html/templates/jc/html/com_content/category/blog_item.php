<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php');

$profile = DiscussHelper::getTable('Profile');
$profile->load($this->item->created_by);

// Create a shortcut for params.
$params = $this->item->params;
$images = json_decode($this->item->images);

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$canEdit = $this->item->params->get('access-edit');

switch (true):
	case (!empty($images->image_fulltext)):
		$image = 'large';
		break;
	case (!empty($images->image_intro)):
		$image = 'small';
		break;

	case ($this->item->parent_alias === 'gebruikersgroepen'):
		$image = 'large';
		break;

	default:
		$image = 'none';
endswitch;

// Determ if the article information column must be shown or not
$showArticleInformation = ($params->get('show_create_date') || $params->get('show_category') || $params->get('show_author'));

?>
<div class="well <?php echo($image == 'large' ? 'photoheader' : ''); ?>">
	<?php if ($image == 'large'):
		$src = 'templates/' . Factory::getApplication()->getTemplate() . '/images/jc-pattern.png';
		$class = ' photobox--empty';
		if ($images->image_fulltext != false) :
			$src   = $images->image_fulltext;
			$class = null;
		endif;
		?>
        <div class="photobox<?php echo $class; ?>">
			<?php echo HTMLHelper::_('image', $src, ''); ?>
        </div>
	<?php endif; ?>
    <div class="row">
        <div class="col-md-2">
			<?php if ($image == 'small'): ?>
                <div class="photoboxsmall<?php if ($images->float_intro == 'right'): ?> logo<?php endif; ?>"><?php
                    $src = $images->image_intro;
                    $alt = '';
                    echo HTMLHelper::_('image', $src, $alt);
                    ?></div>
			<?php endif; ?>

            <div class="item-meta">
				<?php if ($showArticleInformation != false) : ?>
					<?php if ($params->get('show_create_date')) : ?>
                        <div class="article-meta item-datum">
                            <p class="article-meta-label">datum</p>
                            <p>
                                <time class="post-date"><?php echo HTMLHelper::_('date', $this->item->created, Text::_('j F Y')); ?></time>
                                <span class="article-meta-mobile">, </span>
                            </p>
                        </div>
					<?php endif; ?>

					<?php if ($params->get('show_author')) : ?>
                        <div class="article-meta auteur-info">
                            <p class="article-meta-label">door</p>
							<?php if (!empty($this->item->created_by_alias)) : ?>
                                <p><?php echo $this->item->created_by_alias; ?></p>
							<?php else: ?>
                                <p><?php
									$href = $profile->getLink();
									$text = $profile->user->get('name');
									echo HTMLHelper::_('link', $href, $text);
									?></p>
							<?php endif; ?>

                        </div>
					<?php endif; ?>

					<?php if ($params->get('show_category')) : ?>
                        <div class="article-meta item-categorie">
                            <p class="article-meta-label">
                                <span class="article-meta-mobile">in</span>
                                <span class="article-meta-desktop">categorie</span>
                            </p>
                            <p><?php
								if ($params->get('link_category') && $this->item->catslug) :
									$href = Route::_(ContentHelperRoute::getCategoryRoute($this->item->catslug));
									$text = $this->escape($this->item->category_title);
									echo HTMLHelper::_('link', $href, $text);
								else :
									$this->escape($this->item->category_title);
								endif; ?></p>
                        </div>
					<?php endif; ?>
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
					echo LayoutHelper::render('template.snippet-share-page', $data);
					?>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="item">
                <div class="page-header">
					<?php if ($canEdit) : ?>
                        <div class="edit-buttons">
							<?php echo LayoutHelper::render('joomla.content.icons', array('params' => $params, 'item' => $this->item, 'print' => false)); ?>
                        </div>
					<?php endif; ?>

					<?php if ($params->get('show_title')) : ?>
                        <h2><?php
						if ($params->get('link_titles') && $params->get('access-view')) :
							$href = Route::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid));
							$text = $this->escape($this->item->title);
							echo HTMLHelper::_('link', $href, $text);
						else :
							echo $this->escape($this->item->title);
						endif;
						?>
                        </h2><?php endif; ?>

					<?php if ($this->item->state == 0) : ?>
                        <span class="label label-warning"><?php echo Text::_('JUNPUBLISHED'); ?></span>
					<?php endif; ?>
                </div>

                <div class="item-content">
					<?php echo $this->item->introtext; ?>
                </div>

				<?php if ($params->get('show_readmore') && $this->item->readmore) :
					$link = Route::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid));
					?>

                    <a class="btn btn-nieuws" href="<?php echo $link; ?>">

						<?php if ($readmore = $this->item->alternative_readmore) :
							echo $readmore;
							if ($params->get('show_readmore_title', 0) != 0) :
								echo HTMLHelper::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
							endif;
                        elseif ($params->get('show_readmore_title', 0) == 0) :
							echo Text::sprintf('COM_CONTENT_READ_MORE_TITLE');
						else :
							echo Text::_('COM_CONTENT_READ_MORE');
							echo HTMLHelper::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
						endif; ?>

                    </a>

				<?php endif; ?>

				<?php // Content is generated by content plugin event "onContentAfterDisplay" ?>
				<?php echo $this->item->event->afterDisplayContent; ?>
            </div>
        </div>
    </div>
</div>
