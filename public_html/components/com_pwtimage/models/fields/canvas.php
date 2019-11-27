<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Layout\FileLayout;

defined('_JEXEC') or die;

/**
 * The PWT Image form field Image.
 *
 * @since  1.0
 */
class PwtimageFormFieldCanvas extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 * @since  1.0
	 */
	protected $type = 'Pwtcanvas';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.0
	 */
	protected function getInput()
	{
		// Setup variables for display.
		$html = array();

		if (!is_array($this->value))
		{
			$this->value = (array) $this->value;
		}

		$cropperLayout = new FileLayout('cropper', JPATH_ROOT . '/components/com_pwtimage/layouts');

		foreach ($this->value as $value)
		{
			// Get a unique ID for each image
			$modalId = uniqid();

			// Check if the image exists
			if (JFile::exists('../' . $value))
			{
				$value = '../' . $value;
			}

			// Set the actions
			$canDo = ContentHelper::getActions('com_pwtimage');

			// Set the PWT Image basic data
			$data = array(
				'id'           => (string) $modalId,
				'imagePreview' => $value,
				'canDo'        => $canDo
			);

			// Add the options
			if (isset($this->element['ratio']))
			{
				$data['ratio'] = (string) $this->element['ratio'];
			}

			if (isset($this->element['freeRatio']))
			{
				$data['freeRatio'] = (string) $this->element['freeRatio'] === 'false' ? false : true;
			}

			if (isset($this->element['useOriginal']))
			{
				$data['useOriginal'] = (string) $this->element['useOriginal'] === 'false' ? false : true;
			}

			if (isset($this->element['keepOriginal']))
			{
				$data['keepOriginal'] = (string) $this->element['keepOriginal'] === 'false' ? false : true;
			}

			if (isset($this->element['width']))
			{
				$data['width'] = (string) $this->element['width'];
			}

			if (isset($this->element['sourcePath']))
			{
				$data['sourcePath'] = (string) $this->element['sourcePath'];
			}

			if (isset($this->element['subPath']))
			{
				$data['subPath'] = (string) $this->element['subPath'];
			}

			if (isset($this->element['showUpload']))
			{
				$data['showUpload'] = (string) $this->element['showUpload'] === 'false' ? false : true;
			}

			if (isset($this->element['showFolder']))
			{
				$data['showFolder'] = (string) $this->element['showFolder'] === 'false' ? false : true;
			}

			if (isset($this->element['showSavePath']))
			{
				$data['showSavePath'] = (string) $this->element['showSavePath'] === 'false' ? false : true;
			}

			if (isset($this->element['showSavePathSelect']))
			{
				$data['showSavePathSelect'] = (string) $this->element['showSavePathSelect'] === 'false' ? false : true;
			}

			if (isset($this->element['toCanvas']))
			{
				$data['toCanvas'] = (string) $this->element['toCanvas'] === 'true' ? true : false;
			}

			if (isset($this->element['showRotationTools']))
			{
				$data['showRotationTools'] = (string) $this->element['showRotationTools'] === 'false' ? false : true;
			}

			if (isset($this->element['showFlippingTools']))
			{
				$data['showFlippingTools'] = (string) $this->element['showFlippingTools'] === 'false' ? false : true;
			}

			if (isset($this->element['showZoomTools']))
			{
				$data['showZoomTools'] = (string) $this->element['showZoomTools'] === 'false' ? false : true;
			}

			if (isset($this->element['activePage']))
			{
				$data['activePage'] = (string) $this->element['activePage'];
			}

			if (isset($this->element['multiple']))
			{
				$data['multiple'] = (string) $this->element['multiple'] === 'false' ? false : true;
			}

			if (isset($this->element['showHelp']))
			{
				$data['showHelp'] = (string) $this->element['showHelp'] === 'false' ? false : true;
			}

			if (isset($this->element['viewMode']))
			{
				$data['viewMode'] = (int) $this->element['viewMode'];
			}

			if (isset($this->element['backgroundColor']))
			{
				$data['backgroundColor'] = (string) $this->element['backgroundColor'];
			}

			// Render PWT Image
			$html[] = $cropperLayout->render($data);

			// The class='required' for client side validation
			$class = array();

			if ($this->required)
			{
				$class[] = 'required';
				$class[] = 'modal-value';
			}

			$html[] = '<input type="hidden" id="' . $modalId . '_value" class="' . implode(' ', $class) . '" name="' . $this->name . '" value="' . $value . '" />';
		}

		return implode("\n", $html);
	}
}
