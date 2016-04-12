<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSForm_Posts extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	public $form_id 	= null;
	public $enabled 	= 0;
	public $method	 	= 1;
	public $fields	= null;
	public $silent	 	= 1;
	public $url	 		= 'http://';
		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__rsform_posts', 'form_id', $db);
	}
}