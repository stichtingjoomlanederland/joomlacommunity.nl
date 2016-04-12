<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormViewFiles extends JViewLegacy
{
	public function display($tpl = null)
	{
		$this->canUpload 	= $this->get('canUpload');
		$this->files 		= $this->get('files');
		$this->folders 		= $this->get('folders');
		$this->elements 	= $this->get('elements');
		$this->current 		= $this->get('current');
		$this->previous 	= $this->get('previous');
		
		parent::display($tpl);
	}
}