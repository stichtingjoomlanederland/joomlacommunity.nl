<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormViewRsform extends JViewLegacy
{
	public function display( $tpl = null )
	{
		$this->params	= $this->get('Params');
		$this->formId 	= $this->get('FormId');
		$this->document	= JFactory::getDocument();
		
		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = JFactory::getConfig()->get('sitename');
		}
		elseif (JFactory::getConfig()->get('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', JFactory::getConfig()->get('sitename'), $title);
		}
		elseif (JFactory::getConfig()->get('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, JFactory::getConfig()->get('sitename'));
		}
		
		$this->document->setTitle($title);
		
		if ($this->params->get('robots')) {
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
		
		if ($this->params->get('menu-meta_description')) {
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
		
		if ($this->params->get('menu-meta_keywords')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
		
		parent::display($tpl);
	}
}