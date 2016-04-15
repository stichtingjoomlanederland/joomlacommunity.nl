<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSForm_PDFs extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $form_id 			 = null;
	var $useremail_send		 = null;
	var $useremail_filename	 = '';
	var $useremail_php		 = '';
	var $useremail_layout	 = '';
	var $adminemail_send	 = '';
	var $adminemail_filename = null;
	var $adminemail_php		 = '';
	var $adminemail_layout   = '';
		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(& $db)
	{
		parent::__construct('#__rsform_pdfs', 'form_id', $db);
	}
}