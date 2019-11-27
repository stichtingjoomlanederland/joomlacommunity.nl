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

// Loop through the list of settings
$settings = array('showSavePath', 'showSavePathSelect', 'showRotationTools', 'showFlippingTools');

foreach ($settings as $setting)
{
	if ($$setting === false || $$setting === '0')
	{
		$$setting = 0;
	}
	else
	{
		$$setting = 1;
	}
}

$showTools = $showRotationTools || $showFlippingTools || $showZoomTools;

if (isset($showSavePath) && $showSavePath)
{
	// Get the possible destinations to write the image to
	$destinationOptions = array();

	foreach (array('default', 'select', 'custom') as $item)
	{
		if ($item === 'select' && !$showSavePathSelect)
		{
			continue;
		}

		$destinationOptions[] = HTMLHelper::_('select.option', $item, $item);
	}

	// Construct the source path
	if (substr($sourcePath, 0, 1) !== '/')
	{
		$sourcePath = '/' . $sourcePath;
	}
}

// Get the possible widths
$widthOptions = array();

if (!is_array($width))
{
	$width = array(array('width' => $width));
}

foreach ($width as $index => $resizeWidth)
{
	if (isset($resizeWidth['width']) && $resizeWidth['width'])
	{
		$widthOptions[] = HTMLHelper::_('select.option', $resizeWidth['width'], $resizeWidth['width']);
	}
}

// Set the values
$countWidthOptions = count($widthOptions);

if (is_array($canDo))
{
	$canEdit  = array_key_exists('core.edit', $canDo) ? (int) $canDo['core.edit'] : 0;
}
else
{
	$canEdit = (int) $canDo->get('core.edit');
}

// Check the ratio
if (!is_array($ratio))
{
	$ratio = array($ratio);
}

// Make it accessible to all the code
Factory::getDocument()->addScriptOptions('PWTImageConfig', array('sessionToken' => $tokenValue));

Factory::getDocument()->addScriptDeclaration(<<<JS
	var choicesDestination,
		choicesFolder,
		choicesWidth;

	jQuery(document).ready(function (){
	    if ({$showSavePath}) {
			// Init choices
			choicesDestination = new Choices('[destination-choices]', {
			    silent: true,
				searchEnabled: false,
				shouldSort: false,
				removeItemButton: false
			});
		
			choicesFolder = new Choices('[folder-choices]', {
			    silent: true,
				searchEnabled: false,
				shouldSort: false,
				removeItemButton: false
			});
			
			pwtImage.loadSelectFolders('{$sourcePath}', '{$tokenValue}');
		}
		
		if ({$canEdit}) {
		    if ({$countWidthOptions}) {
                choicesWidth = new Choices('[width-choices]', {
                    searchEnabled: false,
                    shouldSort: true,
                    removeItemButton: true
                });
			}
		}
	});
JS
);
?>

<!-- Edit screen -->
<div class="pwt-content">

	<!-- Message when no file is being selected -->
	<div class="pwt-fulltab-message">
		<p><?php echo Text::_('COM_PWTIMAGE_NO_IMAGE_FOUND'); ?></p>
		<p>
			<a class="js-upload-new" href="#"><?php echo Text::_('COM_PWTIMAGE_UPLOAD_IMAGE_LINK'); ?></a>
			<?php
			if (isset($showFolder) && $showFolder)
			:
				?>
				<?php echo Text::_('COM_PWTIMAGE_SELECT_OR_UPLOAD_IMAGE');?>
				<a class="js-select-existing" href="#"><?php echo Text::_('COM_PWTIMAGE_SELECT_IMAGE_LINK'); ?></a>
			<?php endif; ?>
		</p>
	</div><!-- .pwt-fulltab-message -->

	<!-- Wrapper -->
	<div class="pwt-wrapper pwt-edit-block is-hidden">
		<?php if (isset($showSavePath) && $showSavePath)
		:
			?>
			<!-- Input group -->
			<label class="pwt-image-modify">
				<strong>
					<?php echo Text::_('COM_PWTIMAGE_SAVE_TO_FOLDER'); ?>
				</strong>
			</label>

			<div class="pwt-input-group pwt-input-group--responsive pwt-image-modify">
				<div class="pwt-input-group__row">
					<div class="pwt-input-group__choices pwt-input-group__choices--select">
						<?php echo HTMLHelper::_('select.genericlist', $destinationOptions, 'destinationFolder', 'destination-choices onchange="return pwtImage.setDestination(this);"', 'value', 'text', null, $modalId . '_destinationFolder'); ?>
					</div>
					<div class="pwt-input-group__addon pwt-input-group__flex-item js-sourcePath"><?php echo $sourcePath; ?>/</div>
				</div>
				<div class="pwt-input-group__row pwt-input-group__row--flex">
					<?php if ($showSavePathSelect)
					:
						?>
						<div id="<?php echo $modalId; ?>_selectFolder" class="pwt-input-group__choices pwt-filename-input is-hidden">
							<?php echo HTMLHelper::_('select.genericlist', array(), 'selectFolder', 'folder-choices', 'value', 'text', null, $modalId . '_selectedFolderOptions'); ?>
						</div>
					<?php endif; ?>
					<div id="<?php echo $modalId; ?>_enterFolder" class="pwt-input-group__input pwt-input-group__input--first is-visible">
						<input class="pwt-form-control js-pwt-image-subPath" type="input" name="subPath" id="<?php echo $modalId; ?>_subPath" value="<?php echo $subPath; ?>" disabled />
					</div>
					<div class="pwt-input-group__input">
						<input class="pwt-form-control js-pwt-image-targetfile" id="pwt-image-targetFile" type="text" name="pwt-image-targetFile" value="">
					</div>
				</div>
			</div><!-- .pwt-input-group -->
		<?php else

		:
			?>
			<input type="hidden" id="<?php echo $modalId; ?>_destinationFolder" name="destinationFolder" value="" />
			<input type="hidden" class="js-pwt-image-subPath" name="pwt-image-subPath" value="<?php echo $subPath; ?>">
			<input type="hidden" class="js-pwt-image-targetfile" id="pwt-image-targetFile" name="pwt-image-targetFile" value="" />
		<?php endif; ?>

		<!-- Grid -->
		<div class="pwt-grid pwt-grid--2-1">

			<!-- Grid main -->
			<div class="pwt-grid__main js-image-cropper is-hidden">

				<!-- Cropper canvas -->
				<div class="pwt-canvas-wrapper" id="<?php echo $modalId; ?>_canvas">

					<!-- Cancel button -->
					<?php if ($showTools && $canEdit)
					:
						?>
					<div class="pwt-form-group">
						<?php if ($wysiwyg)
						:
							?>
							<button class="pwt-button pwt-button--primary js-button-save-new" onclick="return pwtImage.saveImage('<?php echo $modalId; ?>','<?php echo $tokenValue; ?>', true);">
								<?php echo Text::_('COM_PWTIMAGE_SAVE_AND_NEW'); ?>
							</button>
						<?php endif; ?>
						<?php
						if (!$toCanvas)
						:
							?>
							<button type="button" class="pwt-button" onclick="pwtImage.cancelImage('<?php echo $modalId; ?>');">
								<?php echo Text::_('COM_PWTIMAGE_CANCEL_EDIT_IMAGE'); ?>
							</button>
						<?php endif; ?>
					</div><!-- .pwt-form-group -->

					<!-- Toolbar -->
					<div class="pwt-toolbar">

						<!-- Rotate and flip tools -->
						<?php if ($showRotationTools)
						:
							?>
							<!-- Rotate left -->
							<div class="pwt-toolbar__item">
								<div class="pwt-button-group">
									<div class="pwt-button-group__addon">
										<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
											<title><?php echo Text::_('COM_PWTIMAGE_ROTATE_LEFT'); ?></title>
											<g transform="matrix(1.00008,0,0,1.00008,-0.00200017,-2.00088)"><path d="M24,14C24,15.625 23.682,17.177 23.047,18.656C22.412,20.135 21.558,21.411 20.485,22.484C19.412,23.557 18.136,24.411 16.657,25.046C15.178,25.681 13.626,25.998 12.001,25.999C10.209,25.999 8.506,25.621 6.892,24.866C5.278,24.111 3.903,23.046 2.767,21.671C2.694,21.567 2.66,21.45 2.665,21.319C2.67,21.188 2.714,21.082 2.798,20.999L4.939,18.843C5.043,18.749 5.173,18.702 5.33,18.702C5.497,18.723 5.616,18.785 5.689,18.889C6.45,19.878 7.382,20.644 8.486,21.186C9.59,21.728 10.762,21.999 12.002,21.998C13.085,21.998 14.119,21.787 15.104,21.365C16.089,20.943 16.94,20.373 17.659,19.654C18.378,18.935 18.948,18.084 19.37,17.099C19.792,16.114 20.003,15.08 20.003,13.997C20.003,12.914 19.792,11.88 19.37,10.895C18.948,9.91 18.378,9.059 17.659,8.34C16.94,7.621 16.089,7.051 15.104,6.629C14.119,6.207 13.085,5.996 12.002,5.996C10.981,5.996 10.002,6.181 9.064,6.551C8.126,6.921 7.293,7.45 6.564,8.137L8.705,10.293C9.028,10.606 9.101,10.965 8.924,11.371C8.747,11.788 8.439,11.996 8.002,11.996L1.002,11.996C0.731,11.996 0.497,11.897 0.299,11.699C0.101,11.501 0.002,11.267 0.002,10.996L0.002,3.996C0.002,3.559 0.21,3.251 0.627,3.074C1.033,2.897 1.392,2.97 1.705,3.293L3.736,5.309C4.851,4.257 6.124,3.442 7.556,2.864C8.988,2.286 10.47,1.997 12.001,1.997C13.626,1.997 15.178,2.315 16.657,2.95C18.136,3.585 19.412,4.439 20.485,5.512C21.558,6.585 22.412,7.861 23.047,9.34C23.682,10.819 23.999,12.371 24,13.996L24,14Z"/></g>
										</svg>
										<span class="visually-hidden"><?php echo Text::_('COM_PWTIMAGE_ROTATE_LEFT'); ?></span>
									</div>
									<div class="pwt-button-group__button">
										<button
												type="button"
												onclick="pwtImage.imageToolbar(this);"
												class="pwt-button"
												data-method="rotate"
												data-option="-90"
										>
											<?php echo Text::_('COM_PWTIMAGE_ROTATE_LEFT_90'); ?>
										</button>
									</div>
									<div class="pwt-button-group__button">
										<button
												type="button"
												onclick="pwtImage.imageToolbar(this);"
												class="pwt-button"
												data-method="rotate"
												data-option="-45"
										>
											<?php echo Text::_('COM_PWTIMAGE_ROTATE_LEFT_45'); ?>
										</button>
									</div>
									<div class="pwt-button-group__button">
										<button
												type="button"
												onclick="pwtImage.imageToolbar(this);"
												class="pwt-button"
												data-method="rotate"
												data-option="-30"
										>
											<?php echo Text::_('COM_PWTIMAGE_ROTATE_LEFT_30'); ?>
										</button>
									</div>
								</div>
							</div><!-- .pwt-toolbar__item -->

							<!-- Rotate right -->
							<div class="pwt-toolbar__item">
								<div class="pwt-button-group">
									<div class="pwt-button-group__addon">
										<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
											<title><?php echo Text::_('COM_PWTIMAGE_ROTATE_RIGHT'); ?></title>
											<g transform="matrix(-1.00008,0,-0,1.00008,24.002,-2.00088)"><path d="M24,14C24,15.625 23.682,17.177 23.047,18.656C22.412,20.135 21.558,21.411 20.485,22.484C19.412,23.557 18.136,24.411 16.657,25.046C15.178,25.681 13.626,25.998 12.001,25.999C10.209,25.999 8.506,25.621 6.892,24.866C5.278,24.111 3.903,23.046 2.767,21.671C2.694,21.567 2.66,21.45 2.665,21.319C2.67,21.188 2.714,21.082 2.798,20.999L4.939,18.843C5.043,18.749 5.173,18.702 5.33,18.702C5.497,18.723 5.616,18.785 5.689,18.889C6.45,19.878 7.382,20.644 8.486,21.186C9.59,21.728 10.762,21.999 12.002,21.998C13.085,21.998 14.119,21.787 15.104,21.365C16.089,20.943 16.94,20.373 17.659,19.654C18.378,18.935 18.948,18.084 19.37,17.099C19.792,16.114 20.003,15.08 20.003,13.997C20.003,12.914 19.792,11.88 19.37,10.895C18.948,9.91 18.378,9.059 17.659,8.34C16.94,7.621 16.089,7.051 15.104,6.629C14.119,6.207 13.085,5.996 12.002,5.996C10.981,5.996 10.002,6.181 9.064,6.551C8.126,6.921 7.293,7.45 6.564,8.137L8.705,10.293C9.028,10.606 9.101,10.965 8.924,11.371C8.747,11.788 8.439,11.996 8.002,11.996L1.002,11.996C0.731,11.996 0.497,11.897 0.299,11.699C0.101,11.501 0.002,11.267 0.002,10.996L0.002,3.996C0.002,3.559 0.21,3.251 0.627,3.074C1.033,2.897 1.392,2.97 1.705,3.293L3.736,5.309C4.851,4.257 6.124,3.442 7.556,2.864C8.988,2.286 10.47,1.997 12.001,1.997C13.626,1.997 15.178,2.315 16.657,2.95C18.136,3.585 19.412,4.439 20.485,5.512C21.558,6.585 22.412,7.861 23.047,9.34C23.682,10.819 23.999,12.371 24,13.996L24,14Z" /></g>
										</svg>
										<span class="visually-hidden"><?php echo Text::_('COM_PWTIMAGE_ROTATE_RIGHT'); ?></span>
									</div>
									<div class="pwt-button-group__button">
										<button
												type="button"
												onclick="pwtImage.imageToolbar(this);"
												class="pwt-button"
												data-method="rotate"
												data-option="90"
										>
											<?php echo Text::_('COM_PWTIMAGE_ROTATE_RIGHT_90'); ?>
										</button>
									</div>
									<div class="pwt-button-group__button">
										<button
												type="button"
												onclick="pwtImage.imageToolbar(this);"
												class="pwt-button"
												data-method="rotate"
												data-option="45"
										>
											<?php echo Text::_('COM_PWTIMAGE_ROTATE_RIGHT_45'); ?>
										</button>
									</div>
									<div class="pwt-button-group__button">
										<button
												type="button"
												onclick="pwtImage.imageToolbar(this);"
												class="pwt-button"
												data-method="rotate"
												data-option="30"
										>
											<?php echo Text::_('COM_PWTIMAGE_ROTATE_RIGHT_30'); ?>
										</button>
									</div>
								</div>
							</div><!-- .pwt-toolbar__item -->
						<?php endif; ?>

						<!-- Ratio tools -->
						<?php if ($ratio || $freeRatio)
						:
							?>
						<div class="pwt-toolbar__item">
							<div class="pwt-button-group">
								<div class="pwt-button-group__addon">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 985.00279 983.75235">
										<title><?php echo Text::_('COM_PWTIMAGE_RATIO'); ?></title>
										<g transform="translate(-1206.5509,-3537.7299)"><path d="m 1292.4888,4436.7901 -84.6852,-84.6921 85.0012,-84.9943 85.0011,-84.9942 84.6852,84.6852 84.6852,84.6852 -85.0011,85.0011 -85.0011,85.0012 -84.6853,-84.6921 z m 321.2368,-322.5133 -407.1747,-407.1761 84.6909,-84.6841 84.691,-84.6841 407.2007,407.2022 407.2009,407.2023 -9.3904,9.3276 c -5.1647,5.1303 -9.3903,9.9063 -9.3903,10.6135 0,1.6368 -128.9657,130.6518 -130.6018,130.6518 -0.6699,0 -5.4555,4.2127 -10.6347,9.3616 l -9.4168,9.3615 -407.1748,-407.1762 z m 322.828,-322.172 -84.368,-84.375 84.9931,-85 84.9931,-85 84.6909,84.6841 84.691,84.6841 -84.6841,84.6909 c -46.5762,46.58 -84.9684,84.6909 -85.316,84.6909 -0.3475,0 -38.5975,-37.9687 -85,-84.375 z"/></g>
									</svg>
									<span class="visually-hidden"><?php echo Text::_('COM_PWTIMAGE_RATIO'); ?></span>
								</div>
								<?php if ($ratio)
								:
									foreach ($ratio as $index => $item)
									{
										?>
									<div class="pwt-button-group__button">
										<?php
										if (isset($item['width']) && isset($item['height']))
										{
											$option = abs((int) $item['width']) . '/' . abs((int) $item['height']);
											$text   = abs((int) $item['width']) . ':' . abs((int) $item['height']);
											?>
										<button
												type="button"
												onclick="pwtImage.imageToolbar(this);"
												class="pwt-button"
												data-method="ratio"
												data-option="<?php echo $option; ?>"
										>
											<?php echo $text; ?>
										</button>
											<?php
										}
										?></div><?php
									}
								endif; ?>

								<?php if ($freeRatio)
								:
									?>
									<div class="pwt-button-group__button">
										<button
												type="button"
												onclick="pwtImage.imageToolbar(this);"
												class="pwt-button"
												data-method="ratio"
												data-option="NaN"
										>
											<?php echo Text::_('COM_PWTIMAGE_RATIO_FREE'); ?>
										</button>
									</div>
								<?php endif; ?>
							</div>
						</div><!-- .pwt-toolbar__item -->
						<?php endif; ?>

						<?php if ($showFlippingTools)
						:
							?>
							<!-- Flip horizontal / vertical tools -->
							<div class="pwt-toolbar__item">
								<div class="pwt-button-group">
									<div class="pwt-button-group__button">
										<button
												type="button"
												onclick="pwtImage.imageToolbar(this);"
												class="pwt-button"
												data-method="scaleX"
												data-option="-1"
												title="<?php echo Text::_('COM_PWTIMAGE_FLIP_HORIZONTAL'); ?>"
										>
											<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
												<title><?php echo Text::_('COM_PWTIMAGE_FLIP_HORIZONTAL'); ?></title>
												<g transform="matrix(0.857143,0,0,0.857143,0,0)"><path d="M28,14C28,14.271 27.901,14.505 27.703,14.703L23.703,18.703C23.505,18.901 23.271,19 23,19C22.729,19 22.495,18.901 22.297,18.703C22.099,18.505 22,18.271 22,18L22,16L6,16L6,18C6,18.271 5.901,18.505 5.703,18.703C5.505,18.901 5.271,19 5,19C4.729,19 4.495,18.901 4.297,18.703L0.297,14.703C0.099,14.505 0,14.271 0,14C0,13.729 0.099,13.495 0.297,13.297L4.297,9.297C4.495,9.099 4.729,9 5,9C5.271,9 5.505,9.099 5.703,9.297C5.901,9.495 6,9.729 6,10L6,12L22,12L22,10C22,9.729 22.099,9.495 22.297,9.297C22.495,9.099 22.729,9 23,9C23.271,9 23.505,9.099 23.703,9.297L27.703,13.297C27.901,13.495 28,13.729 28,14Z"/></g>
											</svg>
											<span class="visually-hidden"><?php echo Text::_('COM_PWTIMAGE_FLIP_HORIZONTAL'); ?></span>
										</button>
									</div>
									<div class="pwt-button-group__button">
										<button
												type="button"
												onclick="pwtImage.imageToolbar(this);"
												class="pwt-button"
												data-method="scaleY"
												data-option="-1"
												title="<?php echo Text::_('COM_PWTIMAGE_FLIP_VERTICAL'); ?>"
										>
											<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
												<title><?php echo Text::_('COM_PWTIMAGE_FLIP_VERTICAL'); ?></title>
												<g transform="matrix(0.857143,0,0,0.857143,6.85714,0)"><path d="M11,5C11,5.271 10.901,5.505 10.703,5.703C10.505,5.901 10.271,6 10,6L8,6L8,22L10,22C10.271,22 10.505,22.099 10.703,22.297C10.901,22.495 11,22.729 11,23C11,23.271 10.901,23.505 10.703,23.703L6.703,27.703C6.505,27.901 6.271,28 6,28C5.729,28 5.495,27.901 5.297,27.703L1.297,23.703C1.099,23.505 1,23.271 1,23C1,22.729 1.099,22.495 1.297,22.297C1.495,22.099 1.729,22 2,22L4,22L4,6L2,6C1.729,6 1.495,5.901 1.297,5.703C1.099,5.505 1,5.271 1,5C1,4.729 1.099,4.495 1.297,4.297L5.297,0.297C5.495,0.099 5.729,0 6,0C6.271,0 6.505,0.099 6.703,0.297L10.703,4.297C10.901,4.495 11,4.729 11,5Z"/></g>
											</svg>
											<span class="visually-hidden"><?php echo Text::_('COM_PWTIMAGE_FLIP_VERTICAL'); ?></span>
										</button>
									</div>
								</div>
							</div><!-- .pwt-toolbar__item -->
						<?php endif; ?>

						<?php if ($showZoomTools)
						:
							?>
						<div class="pwt-toolbar__item">
							<div class="pwt-button-group">
								<div class="pwt-button-group__button">
									<button
											type="button"
											onclick="pwtImage.imageToolbar(this);"
											class="pwt-button"
											data-method="zoom"
											data-option="0.1"
											title="<?php echo Text::_('COM_PWTIMAGE_ZOOM_IN'); ?>"
									>
										<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
											<title><?php echo Text::_('COM_PWTIMAGE_ZOOM_IN'); ?></title>
											<path d="M14.768,9.693l0,0.923c0,0.125 -0.045,0.233 -0.136,0.325c-0.091,0.091 -0.2,0.137 -0.325,0.136l-3.231,0l0,3.231c0,0.125 -0.046,0.233 -0.137,0.325c-0.091,0.092 -0.199,0.137 -0.325,0.137l-0.923,0c-0.125,0 -0.233,-0.046 -0.325,-0.137c-0.091,-0.091 -0.137,-0.199 -0.136,-0.325l0,-3.231l-3.231,0c-0.125,0 -0.233,-0.045 -0.325,-0.136c-0.092,-0.091 -0.137,-0.2 -0.136,-0.325l0,-0.923c0,-0.125 0.045,-0.233 0.136,-0.325c0.091,-0.092 0.2,-0.137 0.325,-0.137l3.231,0l0,-3.231c0,-0.124 0.045,-0.233 0.136,-0.324c0.092,-0.092 0.2,-0.138 0.325,-0.137l0.923,0c0.125,0 0.234,0.045 0.325,0.137c0.092,0.091 0.138,0.199 0.137,0.324l0,3.231l3.231,0c0.125,0 0.233,0.046 0.325,0.137c0.091,0.091 0.137,0.199 0.136,0.325Zm1.846,0.461c0,-1.779 -0.632,-3.3 -1.896,-4.564c-1.265,-1.264 -2.787,-1.897 -4.565,-1.897c-1.779,-0.001 -3.3,0.631 -4.565,1.897c-1.264,1.265 -1.897,2.786 -1.897,4.564c0,1.778 0.633,3.3 1.897,4.565c1.265,1.265 2.786,1.897 4.565,1.897c1.778,-0.001 3.3,-0.633 4.565,-1.897c1.264,-1.264 1.896,-2.786 1.896,-4.565Zm7.385,12c0,0.51 -0.18,0.945 -0.541,1.306c-0.36,0.36 -0.796,0.54 -1.305,0.54c-0.519,0 -0.952,-0.182 -1.298,-0.548l-4.947,-4.933c-1.721,1.192 -3.639,1.788 -5.754,1.788c-1.375,0 -2.69,-0.267 -3.944,-0.8c-1.255,-0.534 -2.337,-1.255 -3.246,-2.164c-0.909,-0.909 -1.63,-1.991 -2.164,-3.245c-0.533,-1.255 -0.8,-2.57 -0.8,-3.945c0,-1.374 0.267,-2.689 0.8,-3.944c0.534,-1.255 1.255,-2.337 2.164,-3.245c0.909,-0.909 1.991,-1.631 3.246,-2.164c1.254,-0.534 2.569,-0.8 3.944,-0.8c1.375,0 2.689,0.266 3.944,0.8c1.255,0.533 2.337,1.255 3.246,2.164c0.909,0.908 1.63,1.99 2.163,3.245c0.534,1.255 0.801,2.57 0.801,3.944c0,2.115 -0.596,4.034 -1.788,5.755l4.946,4.947c0.356,0.355 0.534,0.788 0.534,1.297l-0.001,0.002Z"/>
										</svg>
										<span class="visually-hidden"><?php echo Text::_('COM_PWTIMAGE_ZOOM_IN'); ?></span>
									</button>
								</div>
								<div class="pwt-button-group__button">
									<button
											type="button"
											onclick="pwtImage.imageToolbar(this);"
											class="pwt-button"
											data-method="zoom"
											data-option="-0.1"
											title="<?php echo Text::_('COM_PWTIMAGE_ZOOM_OUT'); ?>"
									>
										<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
											<title><?php echo Text::_('COM_PWTIMAGE_ZOOM_OUT'); ?></title>
											<path d="M14.768,9.693l0,0.923c0,0.125 -0.045,0.233 -0.136,0.325c-0.091,0.091 -0.2,0.137 -0.325,0.136l-8.308,0c-0.125,0 -0.233,-0.045 -0.325,-0.136c-0.092,-0.091 -0.137,-0.2 -0.136,-0.325l0,-0.923c0,-0.125 0.045,-0.233 0.136,-0.325c0.091,-0.092 0.2,-0.137 0.325,-0.137l8.308,0c0.125,0 0.233,0.046 0.325,0.137c0.091,0.091 0.137,0.199 0.136,0.325Zm1.846,0.461c0,-1.779 -0.632,-3.3 -1.896,-4.564c-1.265,-1.264 -2.787,-1.897 -4.565,-1.897c-1.779,-0.001 -3.3,0.631 -4.565,1.897c-1.264,1.265 -1.897,2.786 -1.897,4.564c0,1.778 0.633,3.3 1.897,4.565c1.265,1.265 2.786,1.897 4.565,1.897c1.778,-0.001 3.3,-0.633 4.565,-1.897c1.264,-1.264 1.896,-2.786 1.896,-4.565Zm7.385,12c0,0.51 -0.18,0.945 -0.541,1.306c-0.36,0.36 -0.796,0.54 -1.305,0.54c-0.519,0 -0.952,-0.182 -1.298,-0.548l-4.947,-4.933c-1.721,1.192 -3.639,1.788 -5.754,1.788c-1.375,0 -2.69,-0.267 -3.944,-0.8c-1.255,-0.534 -2.337,-1.255 -3.246,-2.164c-0.909,-0.909 -1.63,-1.991 -2.164,-3.245c-0.533,-1.255 -0.8,-2.57 -0.8,-3.945c0,-1.374 0.267,-2.689 0.8,-3.944c0.534,-1.255 1.255,-2.337 2.164,-3.245c0.909,-0.909 1.991,-1.631 3.246,-2.164c1.254,-0.534 2.569,-0.8 3.944,-0.8c1.375,0 2.689,0.266 3.944,0.8c1.255,0.533 2.337,1.255 3.246,2.164c0.909,0.908 1.63,1.99 2.163,3.245c0.534,1.255 0.801,2.57 0.801,3.944c0,2.115 -0.596,4.034 -1.788,5.755l4.946,4.947c0.356,0.355 0.534,0.788 0.534,1.297l-0.001,0.002Z"/>
										</svg>
										<span class="visually-hidden"><?php echo Text::_('COM_PWTIMAGE_ZOOM_OUT'); ?></span>
									</button>
								</div>
							</div>
						</div>
						<?php endif; ?>

					</div><!-- .pwt-toolbar -->
					<?php endif; ?>

					<!-- Actual cropper -->
					<div class="pwt-cropper-wrapper">

						<?php // Class names are not namespaced with .pwt- because the cropper is third party; ?>
						<div class="cropper-container cropper-bg">
							<img id="<?php echo $modalId; ?>_js-pwtimage-image">
						</div>

					</div><!-- .pwt-cropper-wrapper -->

				</div><!-- cropper canvas -->

			</div><!-- .pwt-grid__main -->

			<!-- Grid main -->
			<div class="pwt-grid__main js-image-preview">

				<!-- Grid content, group content so it won't be effected by flexbox parent -->
				<div class="pwt-grid__content">

					<!-- Save and new button -->
					<?php if ($showTools && $canEdit)
					:
						?>
						<div class="pwt-form-group pwt-image-modify">
							<?php if ($wysiwyg)
							:
								?>
							<button class="pwt-button pwt-button--primary js-button-save-new" onclick="return pwtImage.saveImage('<?php echo $modalId; ?>','<?php echo $tokenValue; ?>', true);">
								<?php echo Text::_('COM_PWTIMAGE_SAVE_AND_NEW'); ?>
							</button>
							<?php endif; ?>
							<button type="button" onclick="pwtImage.directToCanvas(this); return false;" class="pwt-button js-edit-image-button">
								<?php echo Text::_('COM_PWTIMAGE_EDIT_PREVIEW_IMAGE'); ?>
							</button>
						</div>
					<?php endif; ?>

					<!-- Image preview holder -->
					<div class="pwt-image-preview-holder">
						<div id="<?php echo $modalId; ?>_preview" class="pwt-image-preview">
							<img src="" alt="" />
						</div>
					</div><!-- .pwt-image-preview-holder -->

				</div><!-- .pwt-grid__content -->

			</div><!-- .pwt-grid__main -->

			<!-- Grid sub -->
			<div class="pwt-grid__sub">

				<!-- Heading -->
				<?php if ($wysiwyg || $useOriginal || $keepOriginal || ($canEdit && $showTools && $countWidthOptions > 1))
				:
					?>
					<h2 class="pwt-heading"><?php echo Text::_('COM_PWTIMAGE_EDIT_IMAGE_INFO'); ?></h2>
				<?php endif; ?>

				<div class="pwt-form-group">
					<table class="pwt-table pwt-table-striped">
						<tbody>
						<tr>
							<td><?php echo Text::_('COM_PWTIMAGE_LABELS_NAME') ?></td>
							<td><span class="pwt-word-wrap js-pwt-filename"></span></td>
						</tr>
						<tr>
							<td><?php echo Text::_('COM_PWTIMAGE_LABELS_TYPE') ?></td>
							<td><span class="js-pwt-fileext"></span></td>
						</tr>
						<tr>
							<td><?php echo Text::_('COM_PWTIMAGE_LABELS_SIZE') ?></td>
							<td><span class="js-pwt-filesize"></span></td>
						</tr>
						<tr>
							<td><?php echo Text::_('COM_PWTIMAGE_LABELS_DIMENSIONS') ?></td>
							<td><span class="js-pwt-filedimensions"></span></td>
						</tr>
						</tbody>
					</table>
				</div>

				<?php if ($wysiwyg)
				:
					?>
					<div class="pwt-form-group pwt-image-modify">
						<label for="alt"><?php echo Text::_('COM_PWTIMAGE_ALT_TEXT'); ?></label>
						<input class="pwt-form-control js-pwt-image-alt" id="alt" type="text" name="alt" value="" />
					</div>
					<div class="pwt-form-group pwt-image-modify">
						<label for="caption"><?php echo Text::_('COM_PWTIMAGE_CAPTION_TEXT'); ?></label>
						<input class="pwt-form-control js-pwt-image-caption" id="caption" type="text" name="caption" value="" />
					</div>
				<?php endif; ?>

				<?php if ($useOriginal)
				:
					?>
					<div class="pwt-form-group">
						<input type="checkbox" value="1" name="pwt-image-useOriginal" id="useOriginal" class="pull-left js-pwt-image-useOriginal" onclick="pwtImage.useOriginal('<?php echo $modalId; ?>', this);"/>
						<label for="useOriginal">&nbsp;<?php echo Text::_('COM_PWTIMAGE_USE_ORIGINAL_SIZE'); ?></label>
					</div>
				<?php endif; ?>

				<?php if ($keepOriginal)
				:
					?>
					<div class="pwt-form-group pwt-image-modify">
						<input type="checkbox" value="1" name="pwt-image-keepOriginal" id="keepOriginal" class="pull-left js-pwt-image-keepOriginal" onclick="pwtImage.keepOriginal('<?php echo $modalId; ?>', this);"/>
						<label for="keepOriginal">&nbsp;<?php echo Text::_('COM_PWTIMAGE_KEEP_ORIGINAL_SIZE'); ?></label>
					</div>
				<?php endif; ?>

				<?php if ($canEdit && $showTools)
				:
					?>
					<?php
					$class   = $countWidthOptions > 1 ? '' : 'is-hidden';
					?>
					<div class="pwt-form-group pwt-image-modify">
						<div class="<?php echo $class; ?>">
							<label for="<?php echo $modalId; ?>_widthOptions"><?php echo Text::_('COM_PWTIMAGE_SELECT_WIDTH'); ?></label>
							<?php
								echo HTMLHelper::_(
									'select.genericlist',
									$widthOptions,
									'pwt-image-width',
									'class="advancedSelect js-pwt-image-width" multiple="true" ' . ($countWidthOptions >= 1 ? 'width-choices' : ''),
									'value',
									'text',
									reset($width),
									$modalId . '_widthOptions'
								);
							?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>

	</div>

</div><!-- .pwt-content -->

<script>
	jQuery('#<?php echo $modalId; ?>_selectFolder').off('change').on('change', function() {
		var sourcePath = jQuery('#<?php echo $modalId; ?> .js-sourcePath'),
			path = sourcePath.text();
		if (jQuery('#<?php echo $modalId; ?>_destinationFolder').val() === 'select') {
			if (jQuery('#<?php echo $modalId; ?>_selectedFolder').val() === '/') {
				sourcePath.text(path.substring(0, (path.length - 1)));
			}
		}
		else {
			if (path.substring(path.length - 1) !== '/') {
				sourcePath.text(path + '/');
			}
		}
	});
</script>
