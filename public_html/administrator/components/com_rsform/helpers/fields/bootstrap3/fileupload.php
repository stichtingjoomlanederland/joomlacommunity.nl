<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/fileupload.php';

class RSFormProFieldBootstrap3Fileupload extends RSFormProFieldFileUpload
{
	protected function getButtonAttributes()
	{
		return array('class' => 'btn btn-default btn-sm rsfp-field-multiple-plus-button');
	}
}