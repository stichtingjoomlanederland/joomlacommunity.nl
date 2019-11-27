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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

extract($displayData);

// Set if the image should be shown on canvas
$direct = !isset($showTools) || (isset($showTools) && $showTools === false) ? 0 : 1;

// Overide if profile setting forces direct to canvas
if ($toCanvas)
{
	$direct = 1;
}


Factory::getDocument()->addScriptDeclaration(
	<<<JS
	jQuery(document).ready(function () {
		pwtImage.initialiseDragnDrop();
		pwtImage.showImageDirect({$direct});
	});
JS
);
?>

<!-- Content -->
<div class="pwt-content">

	<!-- Message -->
	<?php // @TODO: rename `has_folder` class below; ?>
	<div class="pwt-message">
		<?php echo Text::_('COM_PWTIMAGE_IMAGE_SAVED_IN'); ?><span class="has_folder"></span>
	</div><!-- .pwt-message -->

	<!-- Dropper area -->
	<div class="pwt-dropper" id="js-dragarea">
		<div class="pwt-dropper__content" id="dragarea-content">
			<p class="pwt-dropper__lead"><?php echo Text::_('COM_PWTIMAGE_DRAG_AND_UPLOAD'); ?></p>
			<p class="pwt-dropper__support">
				<label class="pwt-button pwt-button--primary" for="<?php echo $modalId; ?>_upload" title="<?php echo Text::_('COM_PWTIMAGE_UPLOAD_IMAGE'); ?>">
					<input class="visually-hidden" type="file" id="<?php echo $modalId; ?>_upload" name="image" accept="image/*" onclick="pwtImage.prepareUpload('<?php echo $modalId; ?>'); pwtImage.uploadImagePreview(this);"><?php echo Text::_('COM_PWTIMAGE_DRAG_AND_UPLOAD_SUPPORT'); ?>
				</label>
			</p>
		</div>
	</div><!-- .pwt-dropper -->

</div><!-- .pwt-content -->
