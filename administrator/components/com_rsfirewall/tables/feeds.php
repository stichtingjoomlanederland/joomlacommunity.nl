<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallTableFeeds extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	public $id 		= null;
	public $url 		= null;
	public $limit 		= null;
	public $ordering 	= null;
	public $published 	= 1;
		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(& $db) {
		parent::__construct('#__rsfirewall_feeds', 'id', $db);
	}
	
	public function check() {
		// check for connectivity
		$feed = JSimplepieFactory::getFeedParser($this->url);
		if ($feed) {
			return true;
		} else {
			$this->setError(JText::sprintf('COM_RSFIREWALL_FEED_COULD_NOT_CONNECT', $this->url));
			return false;
		}
		
		return true;
	}
}