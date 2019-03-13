<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformViewFiles extends JViewLegacy
{
	public function display($tpl = null)
	{
        if (!JFactory::getUser()->authorise('forms.manage', 'com_rsform'))
        {
            throw new Exception(JText::_('COM_RSFORM_NOT_AUTHORISED_TO_USE_THIS_SECTION'));
        }

		$this->canUpload 	= $this->get('canUpload');
		$this->files 		= $this->get('files');
		$this->folders 		= $this->get('folders');
		$this->elements 	= $this->get('elements');
		$this->current 		= $this->get('current');
		$this->previous 	= $this->get('previous');
		
		parent::display($tpl);
	}
}