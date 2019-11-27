<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

extract($displayData);

Factory::getDocument()->addScriptDeclaration(<<<JS
	jQuery(document).ready(function (){
			pwtImage.loadFolder(jQuery('.pwt-gallery__items--folders'), '{$sourcePath}', 'select', '{$tokenValue}');
			
			jQuery('.pwt-filter').on('change keyup keypress blur', '#selectFilter', function(event) {
			  	
			    if (event.keyCode === 13) {
			  	    event.preventDefault();
			  	}
			  	
			  	pwtImage.selectFilter(this.value);
			})
		});
JS
);
?>
<div class="pwt-content">
<!-- Message -->
<div class="pwt-message">
	<?php echo Text::_('COM_PWTIMAGE_IMAGE_SAVED_IN'); ?><span class="has_folder"></span>
</div>

<!-- Filter -->
<div class="pwt-filter pwt-form-group">
	<input type="text" id="selectFilter" value="" class="pwt-form-control" placeholder="<?php echo Text::_('COM_PWTIMAGE_FILTER_IMAGES'); ?>"/>
</div>
<!-- File picker -->
<div class="pwt-content pwt-filepicker">

	<!-- Path / breadcrumbs -->
	<div class="pwt-breadcrumb js-breadcrumb">
		<?php // @TODO: Replace below class icon with SVG; ?>
		<?php echo HTMLHelper::_('link', $baseFolder, '<span class="icon-folder-2"></span>' . basename($baseFolder), 'onclick="pwtImage.loadFolder(this, \'' . $sourcePath . '\', \'select\', \'' . $tokenName . '\', \'' . $tokenValue . '\'); return false;"'); ?>
	</div><!-- .pwt-filepicker__path -->

	<!-- File picker content area -->
	<div class="pwt-filepicker__content">

		<!-- Folders -->
		<div class="pwt-gallery" data-gallery>
			<div class="pwt-gallery__items pwt-gallery__items--folders"></div>
		</div><!-- .pwt-gallery -->

		<!-- Files -->
		<div class="pwt-gallery" data-gallery>
			<div class="pwt-gallery__items pwt-gallery__items--images"></div>
		</div><!-- .pwt-gallery -->

	</div><!-- .pwt-filepicker__content -->

	<!-- File picker pagination -->
	<div class="pwt-filepicker__pages">
		<div class="pwt-pagination"></div>
	</div><!-- .pwt-filepicker__pages -->

</div><!-- .pwt-filepicker -->
</div>
