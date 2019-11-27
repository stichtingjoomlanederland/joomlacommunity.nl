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
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Layout\FileLayout;

defined('_JEXEC') or die;

/**
 * The PWT Image form field select button.
 *
 * This creates a button to open PWT Image in a modal window
 *
 * @since  1.0
 */
class PwtimageFormFieldImage extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 * @since  1.0
	 */
	protected $type = 'Pwtimage';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @throws  Exception
	 *
	 * @since   1.0
	 */
	protected function getInput()
	{
		// Set the actions
		$canDo = ContentHelper::getActions('com_pwtimage');

		// Set the PWT Image data
		$data = array(
			'imagePreview' => $this->value,
			'multiple'     => false,
			'target'       => $this->getName($this->fieldname),
			'value'        => $this->value,
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

		if (isset($this->element['repeatable']))
		{
			$data['repeatable'] = (string) $this->element['repeatable'];
		}

		$app = Factory::getApplication();

		if ($app->isClient('administrator'))
		{
			$data['tokenName']  = 'sessionId';
			$data['tokenValue'] = Factory::getSession()->getId();
		}

		// Set the origin
		$attributes     = $this->element->attributes();
		$origin         = (string) $attributes->origin;

		// If there is no origin set, we take a best guess
		if (empty($origin))
		{
			$origin = $app->input->getCmd('option') . '.' . (string) $this->element['name'];
		}

		$data['origin'] = $origin;

		$buttonLayout = new FileLayout('button', JPATH_ROOT . '/components/com_pwtimage/layouts');

		return $buttonLayout->render($data);
	}
}
