<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

ED::import('admin:/tables/table');

class DiscussHashKeys extends EasyDiscussTable
{
	public $id = null;
	public $uid = null;
	public $type = null;
	public $key = null;

	public function __construct(& $db)
	{
		parent::__construct('#__discuss_hashkeys', 'id', $db);
	}

	public function store($updateNulls = false)
	{
		if (empty($this->key)) {
			$this->key	= $this->generate();
		}

		return parent::store($updateNulls);
	}

	/**
	 * Generates a hashkey
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function generate()
	{
		return JString::substr(md5($this->uid . $this->type . ED::date()->toSql()), 0, 12);
	}
}
