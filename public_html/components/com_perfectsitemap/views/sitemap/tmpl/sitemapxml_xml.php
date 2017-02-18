<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<?php foreach ($this->items as $item) : ?>
        <url>
            <loc><?php echo $item->link; ?></loc>

			<?php if (!empty($item->modified)) : ?>
                <lastmod><?php echo $item->modified; ?></lastmod>
			<?php endif; ?>
        </url>
	<?php endforeach; ?>
</urlset>
