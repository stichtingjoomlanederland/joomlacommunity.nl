<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelHolidays extends EasyDiscussAdminModel
{

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();

		$mainframe 	= JFactory::getApplication();

		// //get the number of events from database
		// $limit		= $mainframe->getUserStateFromRequest('com_easydiscuss.rules.limit', 'limit', $mainframe->getCfg('list_limit') , 'int');
		// $limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		// $this->setState('limit', $limit);
		// $this->setState('limitstart', $limitstart);
	}

	/**
     * Get all the holidays created on the site
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
	public function getHolidays()
	{
		$db	= ED::db();

		$query	= 'SELECT * FROM `#__discuss_holidays`';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

    /**
     * Get holidays that fall on today
     *
     * @since   4.0
     * @access  public
     * @param   string today date with offset
     * @return
     */
    public function getTodayHolidays($today)
    {
        $db = ED::db();

        $query = "select * from `#__discuss_holidays`";
        $query .= " where `published` = " . $db->Quote('1');
        $query .= " and `start` <= " . $db->Quote($today) . " and `end` >= " . $db->Quote($today);
        $query .= " order by `start` desc";
        $query .= " limit 1";

        $db->setQuery($query);

        $results = $db->loadObject();
        return $results;
    }
}
