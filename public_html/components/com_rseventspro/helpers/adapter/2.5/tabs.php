<?php
/**
* @package RSJoomla! Adapter
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

/**
 * Utility class for Tabs.
 *
 * @package     RSJoomla!
 */
class RSTabs {
	
	protected $id		= null;
	protected $titles 	= array();
	protected $contents = array();
	
	/**
	 * Method to set the tab id
	 *
	 * @return  void
	 */
	public function __construct($id) {
		$this->id	   = preg_replace('#[^A-Z0-9_\. -]#i', '', $id);
	}

	/**
	 * Method to add tab title
	 *
	 * @return  void
	 */
	public function title($label, $id) {
		$this->titles[] = (object) array('label' => $label, 'id' => $id);
	}
	
	/**
	 * Method to add content to the tab
	 *
	 * @return  void
	 */
	public function content($content) {
		$this->contents[] = $content;
	}
	
	/**
	 * Render tabs
	 *
	 * @return  string  HTML for tabs
	 */
	public function render() {
		$html   = array();
		
		$html[] = JHtml::_('tabs.start', $this->id, array('useCookie' => 1));
		foreach ($this->titles as $i => $title) {
			$html[] = JHtml::_('tabs.panel', JText::_($title->label), $title->id);
			$html[] = $this->contents[$i];
		}
		$html[] = JHtml::_('tabs.end');
		
		return implode("\n",$html);
	}
}