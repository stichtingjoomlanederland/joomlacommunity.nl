<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.5.0.585
 * @date        2016-08-25
 */

// no direct access
defined('_JEXEC') or die;
?>

<nav
	class="wbamp-container wbamp-header <?php echo empty($displayData['site_image']) ? '' : 'wbamp-header-image wbamp-header-no-background'; ?> <?php echo empty($displayData['site_name']) ? '' : 'wbamp-header-site-name'; ?>">
	<a class="wbamp-site-logo"
	   href="<?php echo empty($displayData['site_link']) ? '/' : $this->escape(ShlSystem_Route::absolutify($displayData['site_link'])); ?>">
		<?php if (!empty($displayData['site_image'])) : ?>
			<amp-img
				src="<?php echo $this->escape(ShlSystem_Route::absolutify($displayData['site_image'])); ?>"
				width="<?php echo $this->escape($displayData['site_image_size']['width']); ?>"
				height="<?php echo $this->escape($displayData['site_image_size']['height']); ?>"
				class="amp-wp-enforced-sizes wbamp-site-logo"></amp-img>
		<?php endif; ?>
		<?php if (!empty($displayData['site_name'])) : ?>
			<div class="wbamp-header-text">
				<?php echo $this->escape($displayData['site_name']); ?>
			</div>
		<?php endif; ?>
	</a>
</nav>
