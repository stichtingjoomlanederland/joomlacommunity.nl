<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformViewSubmissions extends JViewLegacy
{
	/* @var $params Joomla\Registry\Registry */
	public $params;

	public function display($tpl = null)
	{
		$this->params = JFactory::getApplication()->getParams('com_rsform');
		
		if ($this->getLayout() == 'default')
		{
			$this->template		= $this->get('listingTemplate');
			$this->filter 		= $this->get('filter');
			$this->pagination 	= $this->get('pagination');
		}
		else
		{
			// Add pathway
			JFactory::getApplication()->getPathway()->addItem(JText::_('RSFP_VIEW_SUBMISSION'), '');

			$this->template = $this->get('detailTemplate');
		}
		
		$title = $this->params->get('page_title', '');
		$this->setDocumentTitle($title);
		
		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
		
		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
		
		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
		
		parent::display($tpl);
	}
}