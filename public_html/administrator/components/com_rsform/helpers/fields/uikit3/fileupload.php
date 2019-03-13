<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/fileupload.php';

class RSFormProFieldUikit3Fileupload extends RSFormProFieldFileUpload
{
    public function getFormInput()
    {
        $html = array(
            '<div uk-form-custom="target: true">',
            parent::getFormInput(),
            '<input class="uk-input uk-form-width-medium" type="text" placeholder="' . JText::_('COM_RSFORM_SELECT_FILE_PLACEHOLDER') . '" disabled>',
            '<button class="uk-button uk-button-default" type="button" tabindex="-1">' . JText::_('JSELECT') . '</button>',
            '</div>'
        );

        return implode("\n", $html);
    }
}