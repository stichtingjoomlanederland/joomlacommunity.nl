<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

extract($displayData);
?>

<div class="pwt-article-image">
	<img src="<?php echo $image; ?>" alt="<?php echo $alt; ?>" title="<?php echo $alt; ?>"/>
	<?php
	if ($caption)
	:
		?>
		<div class="pwt-article-image__caption"><?php echo $caption; ?></div>
	<?php endif; ?>
</div>
