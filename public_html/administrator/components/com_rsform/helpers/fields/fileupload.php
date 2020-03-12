<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/field.php';

class RSFormProFieldFileUpload extends RSFormProField
{
	// backend preview
	public function getPreviewInput()
	{
		return '<input type="file" />';
	}

	// @desc Returns the full name of the name HTML tag (eg. form[textbox])
	public function getName()
	{
		$name = $this->namespace.'['.$this->name.']';

		if ($this->getProperty('MULTIPLE', false))
		{
			$name .= '[]';
		}

		return $name;
	}

	// functions used for rendering in front view
	public function getFormInput()
	{
		$multiple 		= $this->getProperty('MULTIPLE', false);
		$multipleplus 	= $this->getProperty('MULTIPLEPLUS', false);

		if ($multiple && $multipleplus)
		{
			$minFiles = (int) $this->getProperty('MINFILES', 1);

			// If we require a minimum number of files to be uploaded, let's show a separate input for each upload in order to help the user
			if ($minFiles > 1)
			{
				$html = str_repeat('<div class="rsfp-field-multiple-plus">' . $this->getFileInput() . '</div>', $minFiles);
			}
			else
			{
				$html = '<div class="rsfp-field-multiple-plus">' . $this->getFileInput() . '</div>';
			}

			return $html . $this->getButtonInput();
		}
		else
		{
			return $this->getFileInput();
		}
	}

	public function getButtonInput()
	{
		$button = '<button type="button"';

		if ($attr = $this->getButtonAttributes())
		{
			foreach ($attr as $key => $value)
			{
				$button .= $this->attributeToHtml($key, $value);
			}
		}

		$maxFiles = (int) $this->getProperty('MAXFILES', 0);
		if ($maxFiles > 0)
		{
			$button .= ' data-rsfp-maxfiles="' . $maxFiles . '"';
		}
		$minFiles = (int) $this->getProperty('MINFILES', 0);
		/*
		 * We have only one maximum file => disable
		 * We have a defined number of maximum files but we've set minimum files lower => disable
		 */
		if ($maxFiles === 1 || ($maxFiles > 0 && $minFiles >= $maxFiles))
		{
			$button .= ' disabled';
		}

		$button .= ' data-rsfp-formid="' . $this->formId . '"';

		$button .= ' onclick="RSFormPro.addMoreFiles(this);">' . JText::_('COM_RSFORM_FILE_ADD_PLUS') . '</button>';

		return $button;
	}

	public function getFileInput()
	{
		$name			= $this->getName();
		$id				= $this->getId();
		$attr			= $this->getAttributes();
		$multiple		= $this->getProperty('MULTIPLE', false);
		$multipleplus	= $this->getProperty('MULTIPLEPLUS', false);
		$type 			= 'file';
		$additional 	= '';
		
		// Start building the HTML input
		$html = '<input';
		// Parse Additional Attributes
		if ($attr) {
			foreach ($attr as $key => $values) {
				// @new feature - Some HTML attributes (type) can be overwritten
				// directly from the Additional Attributes area
				if ($key == 'type' && strlen($values)) {
					${$key} = $values;
					continue;
				}
				$additional .= $this->attributeToHtml($key, $values);
			}
		}
		// Set the type
		$html .= ' type="'.$this->escape($type).'"';
		// Name & id
		$html .= ' name="'.$this->escape($name).'"'.
				 ' id="'.$this->escape($id).'"';
		if ($multiple)
		{
			if ($multipleplus)
			{
				$html .= ' data-rsfp-skip-ajax="true"';
			}
			else
			{
				$html .= ' multiple';
			}
		}

		if ($this->getProperty('ACCEPTEDFILESIMAGES') && $this->getProperty('SHOWIMAGEPREVIEW'))
		{
			$html .= ' onchange="RSFormPro.loadImage(this);"';
		}

		// Additional HTML
		$html .= $additional;

		$html .= $this->addDataAttributes();

		// Close the tag
		$html .= ' />';
		
		return $html;
	}

	protected function getButtonAttributes()
	{
		return array('class' => 'rsfp-field-multiple-plus-button');
	}

	protected function addDataAttributes()
	{
		$html = '';

		if ($this->isRequired())
		{
			$html .= ' data-rsfp-required="true"';
		}

		if ($this->getProperty('MULTIPLE'))
		{
			$minFiles = (int) $this->getProperty('MINFILES', 1);
			$maxFiles = (int) $this->getProperty('MAXFILES', 0);

			if ($minFiles > 0)
			{
				$html .= ' data-rsfp-minfiles="' . $minFiles . '"';
			}
			if ($maxFiles > 0)
			{
				$html .= ' data-rsfp-maxfiles="' . $maxFiles . '"';
			}
		}

		if ($this->getProperty('ACCEPTEDFILESIMAGES'))
		{
			$acceptedExts = array('jpg', 'jpeg', 'png', 'gif');
		}
		elseif ($exts = $this->getProperty('ACCEPTEDFILES'))
		{
			$acceptedExts = RSFormProHelper::explode($exts);
		}
		else
		{
			$acceptedExts = array();
		}

		if ($acceptedExts)
		{
			array_walk($acceptedExts, array($this, 'cleanExtension'));
			$html .= ' data-rsfp-exts="' . $this->escape(json_encode($acceptedExts)) . '"';
		}

		$size = (int) $this->getProperty('FILESIZE');

		if ($size)
		{
			$html .= ' data-rsfp-size="' . ($size * 1024) . '"';
		}

		$messages = array(
			'VALIDATIONMESSAGE' => $this->getProperty('VALIDATIONMESSAGE'),
			'COM_RSFORM_FILE_EXCEEDS_LIMIT' => JText::_('COM_RSFORM_FILE_EXCEEDS_LIMIT'),
			'COM_RSFORM_FILE_EXTENSION_NOT_ALLOWED' => JText::_('COM_RSFORM_FILE_EXTENSION_NOT_ALLOWED'),
			'COM_RSFORM_MINFILES_REQUIRED' => JText::_('COM_RSFORM_MINFILES_REQUIRED'),
			'COM_RSFORM_MAXFILES_REQUIRED' => JText::_('COM_RSFORM_MAXFILES_REQUIRED'),
		);

		foreach ($messages as $key => $message)
		{
			$this->addScriptDeclaration('RSFormPro.Translations.add(' . $this->formId . ', ' . json_encode($this->name) . ', ' . json_encode($key) . ', ' . json_encode($message) . ');');
		}

		return $html;
	}

	public function cleanExtension(&$value, $key = null)
	{
		$value = strtolower(trim($value));
	}
	
	// @desc All upload fields should have a 'rsform-upload-box' class for easy styling
	public function getAttributes() {
		$attr = parent::getAttributes();
		if (strlen($attr['class'])) {
			$attr['class'] .= ' ';
		}
		$attr['class'] .= 'rsform-upload-box';
		
		return $attr;
	}

	// process the upload file after form validation
	public function processBeforeStore($submissionId, &$post, &$files, $addToDb = true)
	{
		if (!isset($files[$this->name]))
		{
			return false;
		}

		$allFiles = array();

		$actualFiles = $this->getProperty('MULTIPLE', false) ? $files[$this->name] : array($files[$this->name]);
		foreach ($actualFiles as $actualFile)
		{
			if ($actualFile['error'] != UPLOAD_ERR_OK)
			{
				continue;
			}

			$prefixProperty = $this->getProperty('PREFIX', '');
			$destination    = RSFormProHelper::getRelativeUploadPath($this->getProperty('DESTINATION', ''));
			$sanitize       = $this->getProperty('SANITIZEFILENAME', false);

			// Prefix
			$prefix = uniqid('') . '-';
			if (strlen(trim($prefixProperty)) > 0)
			{
				$prefix = $this->isCode($prefixProperty);
			}

			// Path
			$realpath = realpath($destination . DIRECTORY_SEPARATOR);
			if (substr($realpath, -1) != DIRECTORY_SEPARATOR)
			{
				$realpath .= DIRECTORY_SEPARATOR;
			}

			// Filename
			if ($sanitize)
			{
				$file = $realpath . $prefix . $this->sanitize($actualFile['name']);
			}
			else
			{
				$file = $realpath . $prefix . $actualFile['name'];
			}

			jimport('joomla.filesystem.file');

			// Upload File
			if (JFile::upload($actualFile['tmp_name'], $file, false, (bool) RSFormProHelper::getConfig('allow_unsafe')))
			{
				if ($this->getProperty('ACCEPTEDFILESIMAGES', false) && function_exists('imagecreatefromstring'))
				{
					$newWidth  = (int) $this->getProperty('THUMBSIZE', 220);
					$quality   = (int) $this->getProperty('THUMBQUALITY', 75);
					$image     = @imagecreatefromstring(file_get_contents($file));

					if (is_resource($image) && $newWidth > 0)
					{
						// Try to rotate it
						$this->tryToRotate($image, $file);

						// If we're downsizing, IMG_BICUBIC produces better results
						if ($newWidth < imagesx($image))
						{
							$image = imagescale($image, $newWidth, -1, IMG_BICUBIC);
						}
						else
						{
							$image = imagescale($image, $newWidth);
						}

						if (is_resource($image))
						{
							$thumbFile = JFile::stripExt($file) . '.jpg';

							// Delete old file, we no longer need it
							JFile::delete($file);

							imagejpeg($image, $thumbFile, $quality);

							$file = $thumbFile;

							unset($image);
						}
					}
				}

				// Trigger Event - onBeforeStoreSubmissions
				JFactory::getApplication()->triggerEvent('rsfp_f_onAfterFileUpload', array(array('formId' => $this->formId, 'fieldname' => $this->name, 'file' => $file, 'name' => $prefix . $actualFile['name'])));

				$allFiles[] = $file;
			}
		}

		if (!$allFiles)
		{
			return false;
		}

		$object = (object) array(
			'SubmissionId' 	=> $submissionId,
			'FormId'		=> $this->formId,
			'FieldName'		=> $this->name,
			'FieldValue'	=> implode("\n", $allFiles)
		);

		if ($addToDb)
		{
			JFactory::getDbo()->insertObject('#__rsform_submission_values', $object, 'SubmissionValueId');
		}

		return $object;
	}

	protected function tryToRotate(&$image, $file)
	{
		if (!function_exists('exif_read_data') || !function_exists('exif_imagetype') || !function_exists('imagerotate'))
		{
			return false;
		}

		if (exif_imagetype($file) !== IMAGETYPE_JPEG)
		{
			return false;
		}

		$data = exif_read_data($file);

		if ($data === false || !isset($data['Orientation']) || $data['Orientation'] == 1)
		{
			return false;
		}

		switch ($data['Orientation'])
		{
			case 2:
				$image = $this->imageFlip($image, 2);
				break;

			case 3:
				$image = $this->imageFlip($image, 3);
				break;

			case 4:
				$image = $this->imageFlip($image, 3);
				$image = $this->imageFlip($image, 2);
				break;

			case 5:
				$image = imagerotate($image, 270, 0);
				$image = $this->imageFlip($image, 2);
				break;

			case 6:
				$image = imagerotate($image, 270, 0);
				break;

			case 7:
				$image = $this->imageFlip($image, 2);
				$image = imagerotate($image, 270, 0);
				break;

			case 8:
				$image = imagerotate($image, 90, 0);
				break;
		}

		return true;
	}

	protected function imageFlip($imgsrc, $mode)
	{
		$width  = imagesx($imgsrc);
		$height = imagesy($imgsrc);

		$src_x = 0;
		$src_y = 0;
		$src_width  = $width;
		$src_height = $height;

		switch ($mode)
		{
			case 1:
				$src_y = $height - 1;
				$src_height = -$height;
				break;

			case 2:
				$src_x = $width - 1;
				$src_width = -$width;
				break;

			case 3:
				$src_x = $width - 1;
				$src_y = $height - 1;
				$src_width 	= -$width;
				$src_height = -$height;
				break;

			default:
				return $imgsrc;
				break;
		}

		$imgdest = imagecreatetruecolor($width, $height);
		if (imagecopyresampled($imgdest, $imgsrc, 0, 0, $src_x, $src_y , $width, $height, $src_width, $src_height))
		{
			return $imgdest;
		}

		return $imgsrc;
	}

	protected function sanitize($string)
    {
        // Remove any '-' from the string since they will be used as concatenaters
        $str = str_replace('-', ' ', $string);

        // Transliterate on the current language
        $str = JFactory::getLanguage()->transliterate($str);

        // Trim white spaces at beginning and end
        $str = trim($str);

        // Remove any duplicate whitespace, and ensure all characters are alphanumeric
        $str = preg_replace('/(\s|[^A-Za-z0-9\-\.])+/', '-', $str);

        // Trim dashes at beginning and end of alias
        $str = trim($str, '-');

        return $str;
    }

    public function md5(&$item, $key)
	{
		$item = md5($item);
	}

    public function removeHashedValues(&$form, $delete)
	{
		if (empty($form) || !is_array($form) || empty($delete) || !is_array($delete))
		{
			return false;
		}

		$hashes = $form;
		array_walk($hashes, array($this, 'md5'));

		foreach ($delete as $hashToDelete)
		{
			$position = array_search($hashToDelete, $hashes);

			if ($position !== false)
			{
				unset($form[$position]);
			}
		}

		return true;
	}

	public function processValidation($validationType = 'form', $submissionId = 0)
	{
		$db 		= JFactory::getDbo();
		$required 	= $this->isRequired();
		$multiple 	= $this->getProperty('MULTIPLE', false);
		$files 		= JFactory::getApplication()->input->files->get('form', null, 'raw');

		if ($validationType == 'directory')
		{
			$query = $db->getQuery(true)
				->select($db->qn('FieldValue'))
				->from($db->qn('#__rsform_submission_values'))
				->where($db->qn('FieldName') . ' = ' . $db->q($this->name))
				->where($db->qn('SubmissionId') . ' = ' . $db->q($submissionId));

			if ($alreadyUploaded = $db->setQuery($query)->loadResult())
			{
				$alreadyUploaded = RSFormProHelper::explode($alreadyUploaded);
			}
			else
			{
				$alreadyUploaded = array();
			}

			$delete = JFactory::getApplication()->input->post->get('delete', array(), 'array');
			if (!empty($delete[$this->name]))
			{
				$this->removeHashedValues($alreadyUploaded, $delete[$this->name]);
			}
		}

		try
		{
			// No $_FILES, but required
			if (!$files && $required)
			{
				return false;
			}

			// $_FILES exists but not for our own field
			if (!isset($files[$this->name]))
			{
				$actualFiles = array();
			}
			else
			{
				if ($multiple)
				{
					$actualFiles = $files[$this->name];
				}
				else
				{
					$actualFiles = array($files[$this->name]);
				}
			}

			// Since we can't rely on counting $_FILES we need to count each correct file
			$countFiles = 0;

			$allowImages = $this->getProperty('ACCEPTEDFILESIMAGES', false);

			foreach ($actualFiles as $actualFile)
			{
				$name 	= $actualFile['name'];
				$error 	= $actualFile['error'];
				$size	= $actualFile['size'];

				// File has been uploaded correctly to the server
				if ($error == UPLOAD_ERR_OK)
				{
					// Let's check if the extension is allowed
					$extParts 		= explode('.', $name);
					$ext 			= strtolower(end($extParts));
					$acceptedExts   = false;
					if ($allowImages)
					{
						$acceptedExts = array('jpg', 'jpeg', 'png', 'gif');
					}
					elseif ($exts = $this->getProperty('ACCEPTEDFILES'))
					{
						$acceptedExts = RSFormProHelper::explode($exts);
					}

					// Let's check only if accepted extensions are set
					if ($acceptedExts)
					{
						$accepted = false;

						foreach ($acceptedExts as $acceptedExt)
						{
							$acceptedExt = trim(strtolower($acceptedExt));
							if (strlen($acceptedExt) && $acceptedExt == $ext)
							{
								$accepted = true;
								break;
							}
						}

						if ($allowImages && function_exists('exif_imagetype') && exif_imagetype($actualFile['tmp_name']) === false)
						{
							throw new Exception(JText::sprintf('COM_RSFORM_FILE_DOES_NOT_SEEM_TO_BE_AN_IMAGE', basename($name)));
						}

						if (!$accepted)
						{
							throw new Exception(JText::sprintf('COM_RSFORM_FILE_EXTENSION_NOT_ALLOWED', basename($name)));
						}
					}

					$filesize = (int) $this->getProperty('FILESIZE');

					// Let's check if it's the correct size
					if ($size > 0 && $filesize > 0 && $size > $filesize * 1024)
					{
						throw new Exception(JText::sprintf('COM_RSFORM_FILE_EXCEEDS_LIMIT', basename($name), $filesize));
					}

					$countFiles++;
				}
				elseif ($error != UPLOAD_ERR_NO_FILE)
				{
					// Parse the error message
					switch ($error)
					{
						default:
							// File has not been uploaded correctly
							throw new Exception(JText::_('RSFP_FILE_HAS_NOT_BEEN_UPLOADED_DUE_TO_AN_UNKNOWN_ERROR'));
							break;

						case UPLOAD_ERR_INI_SIZE:
							throw new Exception(JText::_('RSFP_UPLOAD_ERR_INI_SIZE'));
							break;

						case UPLOAD_ERR_FORM_SIZE:
							throw new Exception(JText::_('RSFP_UPLOAD_ERR_FORM_SIZE'));
							break;

						case UPLOAD_ERR_PARTIAL:
							throw new Exception(JText::_('RSFP_UPLOAD_ERR_PARTIAL'));
							break;

						case UPLOAD_ERR_NO_TMP_DIR:
							throw new Exception(JText::_('RSFP_UPLOAD_ERR_NO_TMP_DIR'));
							break;

						case UPLOAD_ERR_CANT_WRITE:
							throw new Exception(JText::_('RSFP_UPLOAD_ERR_CANT_WRITE'));
							break;

						case UPLOAD_ERR_EXTENSION:
							throw new Exception(JText::_('RSFP_UPLOAD_ERR_EXTENSION'));
							break;
					}
				}
			}

			if ($multiple)
			{
				$minFiles = $this->getProperty('MINFILES', 1);
				$maxFiles = $this->getProperty('MAXFILES', 0);

				if ($validationType == 'directory')
				{
					$countFiles += count($alreadyUploaded);
				}

				if ($required || $countFiles)
				{
					if ($minFiles > 0 && $countFiles < $minFiles)
					{
						throw new Exception(JText::sprintf('COM_RSFORM_MINFILES_REQUIRED', $minFiles));
					}

					if ($maxFiles > 0 && $countFiles > $maxFiles)
					{
						throw new Exception(JText::sprintf('COM_RSFORM_MAXFILES_REQUIRED', $maxFiles));
					}
				}
			}

			if ($required && $countFiles === 0 && empty($alreadyUploaded))
			{
				throw new Exception($this->getProperty('VALIDATIONMESSAGE'));
			}
		}
		catch (Exception $e)
		{
			$properties =& RSFormProHelper::getComponentProperties($this->componentId);
			$properties['VALIDATIONMESSAGE'] = $e->getMessage();

			return false;
		}

		return true;
	}
}