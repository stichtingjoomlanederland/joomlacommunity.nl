<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

ED::import('admin:/tables/table');

class DiscussOauth extends EasyDiscussTable
{
	public $id = null;
	public $type = null;
	public $request_token = null;
	public $access_token = null;
	public $message = null;
	public $params = null;

	public function __construct(& $db)
	{
		parent::__construct('#__discuss_oauth', 'id', $db);
	}

	public function loadByType($type)
	{
		$type = (string) $type;
		$db = ED::db();

		$query = 'SELECT * FROM ' . $db->nameQuote($this->_tbl);
		$query .= ' WHERE ' . $db->nameQuote('type') . '=' . $db->Quote($type);

		$db->setQuery($query);
		$result	= $db->loadObject();

		if (!$result) {
			return false;
		}

		return parent::bind($result);
	}
}
