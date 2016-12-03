<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');


class RSPagination extends JObject
{
	/**
	 * The record number to start dislpaying from
	 *
	 * @access public
	 * @var int
	 */
	public $limitstart = null;

	/**
	 * Number of rows to display per page
	 *
	 * @access public
	 * @var int
	 */
	public $limit = null;

	/**
	 * Total number of rows
	 *
	 * @access public
	 * @var int
	 */
	public $total = null;

	/**
	 * View all flag
	 *
	 * @access protected
	 * @var boolean
	 */
	protected $_viewall = false;

	/**
	 * The option
	 *
	 * @access public
	 * @var string
	 */
	public $option = null;

	/**
	 * The option id
	 *
	 * @access public
	 * @var int
	 */
	public $id = null;

	/**
	 * The template
	 *
	 * @access public
	 * @var string
	 */
	public $template = null;

	/**
	 * Overwrite
	 *
	 * @access public
	 * @var int
	 */
	public $overwrite = null;

	/**
	 * Constructor
	 *
	 * @param	int		The total number of items
	 * @param	int		The offset of the item to start at
	 * @param	int		The number of items to display per page
	 */
	public function __construct($total, $limitstart, $limit, $option, $id, $template, $overwrite) {
		JFactory::getLanguage()->load('com_rscomments');
		
		// Value/Type checking
		$this->total		= (int) $total;
		$this->limitstart	= (int) max($limitstart, 0);
		$this->limit		= (int) max($limit, 0);
		$this->option		= $option;
		$this->id			= (int) $id;
		$this->template		= $template;
		$this->overwrite	= (int) $overwrite;
		
		if ($this->limit > $this->total) {
			$this->limitstart = 0;
		}
		
		if (!$this->limit) {
			$this->limit = $total;
			$this->limitstart = 0;
		}
		
		if ($this->limitstart > $this->total) {
			$this->limitstart -= $this->limitstart % $this->limit;
		}

		// Set the total pages and current page values
		if($this->limit > 0) {
			$this->set('pages.total', ceil($this->total / $this->limit));
			$this->set('pages.current', ceil(($this->limitstart + 1) / $this->limit));
		}

		// Set the pagination iteration loop values
		$displayedPages	= 10;
		$this->set( 'pages.start', (floor(($this->get('pages.current') -1) / $displayedPages)) * $displayedPages +1);
		if ($this->get('pages.start') + $displayedPages -1 < $this->get('pages.total')) {
			$this->set( 'pages.stop', $this->get('pages.start') + $displayedPages -1);
		} else {
			$this->set( 'pages.stop', $this->get('pages.total'));
		}

		// If we are viewing all records set the view all flag to true
		if ($this->limit == $total) {
			$this->_viewall = true;
		}
	}

	/**
	 * Return the pagination data object, only creating it if it doesn't already exist
	 *
	 * @access	public
	 * @return	object	Pagination data object
	 * @since	1.5
	 */
	public function getData() {
		static $data;
		if (!is_object($data)) {
			$data = $this->_buildDataObject();
		}
		return $data;
	}

	/**
	 * Create and return the pagination pages counter string, ie. Page 2 of 4
	 *
	 * @access	public
	 * @return	string	Pagination pages counter string
	 * @since	1.5
	 */
	public function getPagesCounter() {
		// Initialize variables
		$html = null;
		if ($this->get('pages.total') > 1) {
			$html .= JText::sprintf('COM_RSCOMMENTS_CURRENT_OF_TOTAL', $this->get('pages.current'), $this->get('pages.total'));
		}
		return $html;
	}

	/**
	 * Create and return the pagination page list string, ie. Previous, Next, 1 2 3 ... x
	 *
	 * @access	public
	 * @return	string	Pagination page list string
	 * @since	1.0
	 */
	public function getPagesLinks() {
		// Build the page navigation list
		$data = $this->_buildDataObject();
		$list = array();

		// Build the select list
		if ($data->all->base !== null) {
			$list['all']['active'] = true;
			$list['all']['data'] = $this->_item_active($data->all);
		} else {
			$list['all']['active'] = false;
			$list['all']['data'] = $this->_item_inactive($data->all);
		}

		if ($data->start->base !== null) {
			$list['start']['active'] = true;
			$list['start']['data'] = $this->_item_active($data->start);
		} else {
			$list['start']['active'] = false;
			$list['start']['data'] = $this->_item_inactive($data->start);
		}
		if ($data->previous->base !== null) {
			$list['previous']['active'] = true;
			$list['previous']['data'] = $this->_item_active($data->previous);
		} else {
			$list['previous']['active'] = false;
			$list['previous']['data'] = $this->_item_inactive($data->previous);
		}

		$list['pages'] = array(); //make sure it exists
		foreach ($data->pages as $i => $page) {
			if ($page->base !== null) {
				$list['pages'][$i]['active'] = true;
				$list['pages'][$i]['data'] = $this->_item_active($page);
			} else {
				$list['pages'][$i]['active'] = false;
				$list['pages'][$i]['data'] = $this->_item_inactive($page);
			}
		}

		if ($data->next->base !== null) {
			$list['next']['active'] = true;
			$list['next']['data'] = $this->_item_active($data->next);
		} else {
			$list['next']['active'] = false;
			$list['next']['data'] = $this->_item_inactive($data->next);
		}
		
		if ($data->end->base !== null) {
			$list['end']['active'] = true;
			$list['end']['data'] = $this->_item_active($data->end);
		} else {
			$list['end']['active'] = false;
			$list['end']['data'] = $this->_item_inactive($data->end);
		}

		if($this->total > $this->limit){
			return $this->_list_render($list);
		} else {
			return '';
		}
	}

	protected function _list_render($list) {
		// Initialize variables
		$html = null;

		// Reverse output rendering for right-to-left display
		//$html .= '&lt;&lt; ';
		$html .= ' '.$list['start']['data'];
		//$html .= ' &lt; ';
		$html .= ' '.$list['previous']['data'];
		foreach( $list['pages'] as $page ) {
			$html .= ' '.$page['data'];
		}
		$html .= ' '. $list['next']['data'];
		//$html .= ' &gt;';
		$html .= ' '. $list['end']['data'];
		//$html .= ' &gt;&gt;';

		return $html;
	}

	protected function _item_active(&$item) {
		$u		= JURI::getInstance();
		$root	= $u->toString(array('scheme','host'));
		
		return '<a title="'.$item->text.'" href="javascript:void(0);" onclick="rsc_pagination(\''.$root.'\', \''.JRoute::_('index.php?option=com_rscomments').'\', \''.$item->page.'\', \''.$this->option.'\', \''.$this->id.'\', \''.$this->template.'\', \''.$this->overwrite.'\');">'.$item->text.'</a>';
	}

	protected function _item_inactive(&$item) {
		return "<a title=\"".$item->text."\" href=\"javascript:void(0);\" class=\"rsc_inactive\">".$item->text."</a>";
	}

	/**
	 * Create and return the pagination data object
	 *
	 * @access	public
	 * @return	object	Pagination data object
	 * @since	1.5
	 */
	protected function _buildDataObject() {
		// Initialize variables
		$data = new stdClass();

		$data->all	= new RSPaginationObject(JText::_('COM_RSCOMMENTS_PAGE_VIEW_ALL'));
		if (!$this->_viewall) {
			$data->all->base	= '0';
			$data->all->page	= 0;
		}

		// Set the start and previous data objects
		$data->start	= new RSPaginationObject(JText::_('COM_RSCOMMENTS_PAGE_START'));
		$data->previous	= new RSPaginationObject(JText::_('COM_RSCOMMENTS_PAGE_PREV'));

		if ($this->get('pages.current') > 1)
		{
			$page = ($this->get('pages.current') -2) * $this->limit;

			$page = $page == 0 ? '' : $page; //set the empty for removal from route

			$data->start->base	= '0';
			$data->start->page	= 0;
			$data->previous->base	= $page;
			$data->previous->page	= $page;
		}

		// Set the next and end data objects
		$data->next	= new RSPaginationObject(JText::_('COM_RSCOMMENTS_PAGE_NEXT'));
		$data->end	= new RSPaginationObject(JText::_('COM_RSCOMMENTS_PAGE_END'));

		if ($this->get('pages.current') < $this->get('pages.total')) {
			$next = $this->get('pages.current') * $this->limit;
			$end  = ($this->get('pages.total') -1) * $this->limit;

			$data->next->base	= $next;
			$data->next->page	= $next;
			$data->end->base	= $end;
			$data->end->page	= $end;
		}

		$data->pages = array();
		$stop = $this->get('pages.stop');
		for ($i = $this->get('pages.start'); $i <= $stop; $i++) {
			$offset = ($i-1) * $this->limit;
			$offset = $offset == 0 ? '' : $offset;  //set the empty for removal from route
			$data->pages[$i] = new RSPaginationObject($i);
			if ($i != $this->get('pages.current') || $this->_viewall) {
				$data->pages[$i]->base	= $offset;
				$data->pages[$i]->page	= $offset;
			}
		}
		return $data;
	}
}

/**
 * Pagination object representing a particular item in the pagination lists
 *
 * @package 	Joomla.Framework
 * @subpackage	HTML
 * @since		1.5
 */
class RSPaginationObject extends JObject {
	public $text;
	public $base;
	public $link;

	public function __construct($text, $base=null, $link=null) {
		$this->text = $text;
		$this->base = $base;
		$this->link = $link;
	}
}