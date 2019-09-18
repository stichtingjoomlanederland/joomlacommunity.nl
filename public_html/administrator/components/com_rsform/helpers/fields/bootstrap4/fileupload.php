<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/fileupload.php';

class RSFormProFieldBootstrap4Fileupload extends RSFormProFieldFileUpload
{
    public function getAttributes() {
        $attr = parent::getAttributes();
        if (strlen($attr['class'])) {
            $attr['class'] .= ' ';
        }
        $attr['class'] .= 'form-control-file';

        return $attr;
    }

	protected function getButtonAttributes()
	{
		return array('class' => 'btn btn-secondary btn-sm rsfp-field-multiple-plus-button');
	}
}