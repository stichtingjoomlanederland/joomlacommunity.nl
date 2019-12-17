<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

?>

<div class="pwtsitemap<?php echo $this->pageclassSfx; ?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1><?php echo $this->escape($this->params->get('page_title')); ?></h1>
		</div>
	<?php endif; ?>

	<?php if ($this->params->get('introtext')): ?>
		<p class="sitemap-intro"><?php echo $this->params->get('introtext'); ?></p>
	<?php endif; ?>

	<?php foreach ($this->sitemap as $title => $sitemaps) : ?>

		<?php if ($this->params->get('showTitle')): ?>
			<h3><?php echo $title; ?></h3>
		<?php endif; ?>

		<?php
		$sitemapHtml = '<ul class="sitemap-list">';

		foreach ($sitemaps as $sitemap)
		{
			$previousLevel = 1;

			foreach ($sitemap as $i => $item)
			{
				if ($item->level > $previousLevel)
				{
					$sitemapHtml .= str_repeat('<ul>', ($item->level - $previousLevel));
				}
				elseif ($item->level < $previousLevel)
				{
					$sitemapHtml .= '</li>';
					$sitemapHtml .= str_repeat('</ul></li>', ($previousLevel - $item->level));
				}
				elseif ($i != 0 && $item->level = $previousLevel)
				{
					$sitemapHtml .= '</li>';
				}

				$sitemapHtml .= '<li class="sitemap-item level-' . $item->level . '">';
				$sitemapHtml .= ($item->type === 'link') ? HTMLHelper::_('link', $item->link, $item->title) : $item->title;

				if (end($sitemap) === $item)
				{
					$sitemapHtml .= str_repeat('</li></ul>', $item->level - 1);
					$sitemapHtml .= '</li>';

				}

				$previousLevel = $item->level;
			}
		}

		$sitemapHtml .= '</ul>';
		?>
		<?php echo $sitemapHtml; ?>
	<?php endforeach; ?>
</div>
