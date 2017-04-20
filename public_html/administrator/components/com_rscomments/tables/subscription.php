<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsTableSubscription extends JTable {
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__rscomments_subscriptions', 'IdSubscription', $db);
	}
}