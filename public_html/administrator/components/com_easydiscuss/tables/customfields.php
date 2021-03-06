<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ED::import('admin:/tables/table');

class DiscussCustomFields extends EasyDiscussTable
{
	public $id = null;
	public $type = null;
	public $title = null;
	public $tooltips = null;
	public $ordering = null;
	public $published = null;
	public $required = null;
	public $section = null;
	public $params = null;
	public $global = null;

	public function __construct(&$db)
	{
		parent::__construct('#__discuss_customfields', 'id', $db);
	}
}
