<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

$app = Factory::getApplication();
$app->setHeader('X-Robots-Tag', 'noindex,follow');
?>
<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<?php foreach ($this->items as $menu) : ?>
		<?php foreach ($menu as $sitemapitems) : ?>
			<?php foreach ($sitemapitems as $item) : ?>
				<?php if ($item->type == 'link' && !$item->external): ?>
					<?php echo $item->renderXml(); ?>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endforeach; ?>
	<?php endforeach; ?>
</urlset>
