<?php
/**
* @package RSJoomla! Adapter
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/
defined('_JEXEC') or die('Restricted access');

class RSCommentsAdapterTabs {
	
	protected $id		= null;
	protected $titles   = array();
	protected $contents = array();
	
	public function __construct($id) {
		$this->id = preg_replace('#[^A-Z0-9_\. -]#i', '', $id);
	}
	
	public function addTitle($label, $id) {
		$this->titles[] = (object) array('label' => $label, 'id' => $id);
	}
	
	public function addContent($content) {
		$this->contents[] = $content;
	}
	
	public function render() {
		$active = reset($this->titles);

		echo JHtml::_('uitab.startTabSet', $this->id, array('active' => $active->id));

		foreach ($this->titles as $i => $title) {
			echo JHtml::_('uitab.addTab', $this->id, $title->id, JText::_($title->label));
			echo $this->contents[$i];
			echo JHtml::_('uitab.endTab');
		}

		echo JHtml::_('uitab.endTabSet');
	}
}