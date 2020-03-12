<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSForm_Emails extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	
	public $id = null;
	public $formId = null;
	public $from = null;
	public $fromname = null;
	public $replyto = null;
	public $replytoname = null;
	public $to = null;
	public $cc = null;
	public $bcc = null;
	public $subject = null;
	public $mode = 1;
	public $message = null;
		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__rsform_emails', 'id', $db);
	}
}