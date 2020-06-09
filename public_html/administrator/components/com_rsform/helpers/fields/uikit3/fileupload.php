<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/fileupload.php';

class RSFormProFieldUikit3Fileupload extends RSFormProFieldFileUpload
{
    public function getFormInput()
    {
		$multipleplus = $this->getProperty('MULTIPLEPLUS', false);

	    $target = $this->getProperty('ACCEPTEDFILESIMAGES') && $this->getProperty('SHOWIMAGEPREVIEW') ? 'false' : 'true';
    	$html = '<div uk-form-custom="target: ' . $target . '">' .
			$this->getFileInput() .
			'<input class="uk-input uk-form-width-medium" type="text" placeholder="' . JText::_('COM_RSFORM_SELECT_FILE_PLACEHOLDER') . '" disabled>' .
			'<button class="uk-button uk-button-default" type="button" tabindex="-1">' . JText::_('JSELECT') . '</button>' .
			'</div>';

    	if ($multipleplus)
		{
			$minFiles = (int) $this->getProperty('MINFILES', 1);

			if ($minFiles > 1)
			{
				$html = str_repeat('<div class="rsfp-field-multiple-plus">' . $this->getFileInput() . '</div>', $minFiles);
			}
			else
			{
				$html = '<div class="rsfp-field-multiple-plus">' . $html . '</div>';
			}

			$html .= $this->getButtonInput();
		}

        return $html;
    }

	protected function getButtonAttributes()
	{
		return array('class' => 'uk-button uk-button-small rsfp-field-multiple-plus-button');
	}
}