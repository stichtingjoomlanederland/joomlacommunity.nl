<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSForm_PDFs extends JTable
{
	public $form_id 			 	= null;
	public $useremail_send		 	= null;
	public $useremail_filename		= '';
	public $useremail_php		 	= '';
	public $useremail_layout	 	= '';
	public $useremail_userpass		= null;
	public $useremail_ownerpass		= null;
	public $useremail_options		= null;
	public $adminemail_send	 		= '';
	public $adminemail_filename 	= null;
	public $adminemail_php		 	= '';
	public $adminemail_layout   	= '';
	public $adminemail_userpass		= null;
	public $adminemail_ownerpass	= null;
	public $adminemail_options		= null;
	
	public function __construct(& $db) {
		parent::__construct('#__rsform_pdfs', 'form_id', $db);
	}
}