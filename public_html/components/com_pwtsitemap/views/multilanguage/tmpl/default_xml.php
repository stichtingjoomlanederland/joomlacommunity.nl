<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;
?>
<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
		xmlns:xhtml="http://www.w3.org/1999/xhtml">
	<?php foreach ($this->items as $menu) : ?>
		<?php foreach ($menu as $sitemapitems) : ?>
			<?php foreach ($sitemapitems as $item) : ?>
				<?php echo $item->renderXml(); ?>
			<?php endforeach; ?>
		<?php endforeach; ?>
	<?php endforeach; ?>
</urlset>
