<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class RSFormViewFormajax extends JViewLegacy
{
	function display($tpl = null)
	{
		switch ($this->getLayout())
		{
			case 'component':
				$this->fields = $this->get('componentFields');
				$this->data = $this->get('componentData');
				
				$this->type_id = $this->get('componentType');
				$this->componentId = $this->get('componentId');
				$this->show_save = count($this->fields) > 3;
			break;
			
			case 'component_published':
			case 'component_required':
				$this->i = $this->get('i');
				$this->field = $this->get('component');
			break;
		}
		
		parent::display($tpl);
	}
}