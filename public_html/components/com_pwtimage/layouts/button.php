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
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

// Load the scripts that are required to make the modal work
HTMLHelper::_('jquery.framework');
HTMLHelper::_('script', 'com_pwtimage/pwtimage.min.js', array('relative' => true, 'version' => 'auto'));
HTMLHelper::_('stylesheet', 'com_pwtimage/pwtimage.min.css', array('relative' => true, 'version' => 'auto'));

// Load the language files
$language = Factory::getLanguage();
$language->load('com_pwtimage', JPATH_SITE . '/components/com_pwtimage', 'en-GB');
$language->load('com_pwtimage', JPATH_SITE . '/components/com_pwtimage');

// Default settings
$buttonText   = 'JSELECT';
$imagePreview = '';
$target       = null;
$value        = null;
$multiple     = false;
$class        = '';

// Get the override values
/**
 * @param   int    $modalId           The unique identifier for the image block
 * @param   string $ratio             The image ratio to use
 * @param   int    $width             The fixed with for an image
 * @param   bool   $useOriginal       Use the original image
 * @param   bool   $keepOriginal      Keep the image size of the original image
 * @param   string $sourcePath        The main image path
 * @param   string $subPath           The image sub-folder
 * @param   bool   $showUpload        Set if the upload page should be shown
 * @param   bool   $showFolder        Set if the image selection from server should be shown
 * @param   bool   $toCanvas          Set if the image is shown directly on the canvas for editing
 * @param   bool   $showRotationTools Set if the rotation tools needs to be shown
 * @param   bool   $showFlippingTools Set if the flipping tools needs to be shown
 * @param   bool   $showZoomTools     Set if the zoom tools needs to be shown
 * @param   string $activePage        Set which tab should be shown by default this is the upload tab
 * @param   string $imagePreview      A given image to show in preview
 * @param   bool   $multiple          Set if multiple images should be allowed
 * @param   bool   $modal             Set if the canvas should be shown in a modal popup
 * @param   bool   $showHelp          Set if the help tab should be shown
 * @param   string $target            The name of the original form field
 * @param   string $value             The value of the original form field
 * @param   string $origin            The breadcrumb path to load the profile for
 * @param   string $class             Extra class(es) to add to the container div
 * @param   string $repeatable        Set if the image is inside a repeatable subform
 */
/** @var array $displayData */
extract($displayData);

// The link to PWT Image
$root   = Uri::root();
$prefix = '';

// Check if we are admin side
$isAdmin = Factory::getApplication()->isClient('administrator');

if ($isAdmin)
{
	$root   .= 'administrator/';
	$prefix = '../';
}

$modal  = '';
$modals = 1;

if ((bool) $multiple && is_array($imagePreview))
{
	$modals = count($imagePreview);
}

for ($i = 0; $i < $modals; $i++)
{
	if (!isset($modalId) || $i > 0)
	{
		$modalId = uniqid();
		$displayData['modalId'] = $modalId;
	}

	$frameId = isset($repeatable) && $repeatable === '1' ? '#js-modal-content iframe' : 'iframe#pwtImageFrame-' . $modalId;

	$preview                = is_array($imagePreview) ? isset($imagePreview[$i]) ? $imagePreview[$i] : '' : $imagePreview;
	$originalValue          = is_array($value) ? isset($value[$i]) ? $value[$i] : '' : $value;
	$link                   = $root . 'index.php?option=com_pwtimage&amp;view=image&amp;tmpl=component&modalId=' . $modalId
		. '&settings=' . base64_encode(json_encode($displayData));

	$modal .= '<div class="js-image-controls ' . $class . '">
				<!-- Render the image preview -->
				<div id="' . $modalId . '_preview" class="pwt-image-preview">
					<img ' . ($preview ? 'src="' . $prefix . $preview . '"' : "") . '/>
				</div>
				
				<!-- Render the original input field -->
				' . ($target ? '<input type="hidden" id="' . $modalId . '_value" name="' . $target . '" value="' . $originalValue . '" />' : "") . '

				<!-- Select button to open the modal window -->
				<button id="label_modal_' . $modalId . '" class="btn btn-primary pwt-image-select js-modal"
				        data-modal-close-text="' . Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '"
				        data-modal-content-id="' . $modalId . '_modal"
				        data-modal-background-click="disabled"
				        data-modal-prefix-class="pwt"
				        title="' . Text::_($buttonText) . '"
                        onclick="jQuery(\'' . $frameId . '\').attr(\'src\', \'' . $link . '\'); pwtImage.setTargetId(\'' . $modalId . '\')"				        type="button">
					<span class="icon-list icon-white"></span>
					' . Text::_($buttonText) . '
				</button>

				<!-- Reset button -->
				<button type="button" class="btn ' . ($imagePreview ? '' : ' hidden') . '" id="' . $modalId . '_clear" onclick="pwtImage.clearImage(\'' . $modalId . '\');">
					<span class="icon-remove"></span>
					' . Text::_('JCLEAR') . '
				</button>
			';

	if ((bool) $multiple)
	{
		$modal .= HTMLHelper::_('link', 'index.php', Text::_('COM_PWTIMAGE_ADD_ROW'), array('class' => 'btn btn-success', 'id' => 'addmore' . $modalId, 'onclick' => 'pwtImage.addRepeatImage(this); return false;'));
	}

	$modal .= '<div id="' . $modalId . '_modal" class="pwt-component is-hidden">
					' . HTMLHelper::_('iframe', Uri::root() . 'index.php?option=com_pwtimage&amp;view=image&layout=iframe&amp;tmpl=component', 'pwtmodel', 'id="pwtImageFrame-' . $modalId . '"') . '
				</div>';

	$modal .= '</div>';
}

echo $modal;
