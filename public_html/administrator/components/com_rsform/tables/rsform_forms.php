<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSForm_Forms extends JTable
{
	public $FormId = null;
	
	public $FormName = '';
	public $FormLayout = '';
	public $GridLayout;
	public $FormLayoutName = 'responsive';
	public $FormLayoutAutogenerate = 1;
	public $FormLayoutFlow = 0;
	public $LoadFormLayoutFramework	= 1;
	public $CSS = '';
	public $JS = '';
	public $FormTitle = '';
	public $ShowFormTitle = 1;
	public $Lang = '';
	public $Keepdata = 1;
	public $KeepIP = 1;
	public $DeleteSubmissionsAfter = 0;
	public $ReturnUrl = '';
	public $ShowSystemMessage = 1;
	public $ShowThankyou = 1;
	public $Thankyou = '';
	public $ShowContinue = 1;
	public $UserEmailText = '';
	public $UserEmailTo = '';
	public $UserEmailCC = '';
	public $UserEmailBCC = '';
	public $UserEmailFrom = '{global:mailfrom}';
	public $UserEmailReplyTo = '';
	public $UserEmailFromName = '{global:fromname}';
	public $UserEmailSubject = '';
	public $UserEmailMode = 1;
	public $UserEmailAttach = 0;
	public $UserEmailAttachFile = '';
	public $AdminEmailText = '';
	public $AdminEmailTo = '';
	public $AdminEmailCC = '';
	public $AdminEmailBCC = '';
	public $AdminEmailFrom = '';
	public $AdminEmailReplyTo = '';
	public $AdminEmailFromName = '';
	public $AdminEmailSubject = '';
	public $AdminEmailMode = 1;
    public $DeletionEmailText = '';
    public $DeletionEmailTo = '';
    public $DeletionEmailCC = '';
    public $DeletionEmailBCC = '';
    public $DeletionEmailFrom = '{global:mailfrom}';
    public $DeletionEmailReplyTo = '';
    public $DeletionEmailFromName = '{global:fromname}';
    public $DeletionEmailSubject = '';
    public $DeletionEmailMode = 1;
	public $ScriptProcess = '';
	public $ScriptProcess2 = '';
	public $ScriptBeforeDisplay = '';
	public $ScriptBeforeValidation = '';
	public $ScriptDisplay = '';
	public $UserEmailScript = '';
	public $AdminEmailScript = '';
	public $AdditionalEmailsScript = '';
	public $MetaTitle = '';
	public $MetaDesc = '';
	public $MetaKeywords = '';
	public $Required = '(*)';
	public $ErrorMessage = '<p class="formRed">Please complete all required fields!</p>';
	public $MultipleSeparator = '\n';
	public $TextareaNewLines = 1;
	public $CSSClass = '';
	public $CSSId = 'userForm';
	public $CSSName = '';
	public $CSSAction = '';
	public $CSSAdditionalAttributes = '';
	public $AjaxValidation = 0;
	public $Backendmenu = '';
	public $ConfirmSubmission = 0;
	public $Access = '';
	
	public $Published = 1;
		
	public function __construct(& $db) {
		parent::__construct('#__rsform_forms', 'FormId', $db);
	}
}