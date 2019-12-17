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
?>
<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<?php for ($i = 0; $i <= $this->sitemap->sitemapArrays; $i++) : ?>
		<url>
			<loc><?php echo PwtSitemapUrlHelper::getURL('index.php?option=com_pwtsitemap&view=' . $this->getName() . '&format=xml&layout=sitemapxml&part=' . $i); ?></loc>
		</url>
	<?php endfor; ?>
</sitemapindex>