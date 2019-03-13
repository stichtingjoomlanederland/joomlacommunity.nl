<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2018 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class RSFormProValidations
{
	public static function none($value,$extra=null,$data=null)
	{
		return true;
	}

	public static function alpha($param,$extra=null,$data=null)
	{
		if(strpos($param,"\n") !== false) 
			$param = str_replace(array("\r","\n"),'',$param);
			
		for($i=0;$i<strlen($param);$i++)
			if(strpos($extra,$param[$i]) === false && preg_match('#([^a-zA-Z ])#', $param[$i]))
				return false;
				
		return true;
	}
	
	public static function numeric($param,$extra=null,$data=null)
	{
		if(strpos($param,"\n") !== false) 
			$param = str_replace(array("\r","\n"),'',$param);
		
		for($i=0;$i<strlen($param);$i++)
			if (strpos($extra,$param[$i]) === false && !is_numeric($param[$i]))
				return false;
				
		return true;
	}
	
	public static function alphanumeric($param,$extra = null,$data=null)
	{
		if(strpos($param,"\n") !== false) 
			$param = str_replace(array("\r","\n"),'',$param);
		
		for($i=0;$i<strlen($param);$i++)
			if(strpos($extra,$param[$i]) === false && preg_match('#([^a-zA-Z0-9 ])#', $param[$i]))
				return false;
				
		return true;
	}
	
	public static function alphaaccented($value, $extra=null, $data=null) {
		if (preg_match('#[^[:alpha:] ]#u', $value)) {
			return false;
		}
		return true;
	}
	
	public static function alphanumericaccented($value, $extra=null, $data=null) {
		if (preg_match('#[^[:alpha:]0-9 ]#u', $value)) {
			return false;
		}
		return true;
	}
	
	public static function email($email, $extra = null, $data = null)
	{
		jimport('joomla.mail.helper');

		return JMailHelper::isEmailAddress($email);
	}
	
	public static function emaildns($email, $extra = null, $data = null)
	{
		// Check if it's an email address format
		if (!self::email($email,$extra,$data))
		{
			return false;
		}
		
		// Fallback if we don't have these
		if (!function_exists('checkdnsrr') || !is_callable('checkdnsrr'))
		{
			return true;
		}
		
		// IDN convert
		$email = JStringPunycode::emailToPunycode($email);
		list($user, $domain) = explode('@', $email, 2);

		// Does this domain have a mail exchange record?
		return checkdnsrr($domain . '.', 'MX');
	}

	public static function uniquefield($value, $extra = null, $data = null)
	{
		$db 	= JFactory::getDbo();
		$app	= JFactory::getApplication();
		$form   = $app->input->get('form', array(), 'array');
		$formId = isset($form['formId']) ? $form['formId'] : 0;
		$option = $app->input->getCmd('option');
		$ctrl 	= $app->input->getCmd('controller');
		$task 	= $app->input->getCmd('task');
		$id		= $app->input->getInt('id');

		$query = $db->getQuery(true)
			->select($db->qn('SubmissionValueId'))
			->from($db->qn('#__rsform_submission_values'))
			->where($db->qn('FormId').'='.$db->q($formId))
			->where($db->qn('FieldName').'='.$db->q($data['NAME']))
			->where($db->qn('FieldValue').'='.$db->q($value));

		// Is this a directory edit?
		if ($id && $option == 'com_rsform' && $ctrl == 'directory' && ($task == 'save' || $task == 'apply')) {
			$query->where($db->qn('SubmissionId').' != '.$db->q($id));
		}

		return $db->setQuery($query)->loadResult() ? false : true;
	}

	public static function uniquefielduser($value, $extra=null,$data=null)
	{
		$db 		= JFactory::getDbo();
		$app		= JFactory::getApplication();
		$form   	= $app->input->get('form', array(), 'array');
		$formId 	= isset($form['formId']) ? $form['formId'] : 0;
		$user		= JFactory::getUser();
		$userField 	= $user->guest ? 's.UserIp' : 's.UserId';
		$userValue 	= $user->guest ? $app->input->server->getString('REMOTE_ADDR') : $user->id;
		$option 	= $app->input->getCmd('option');
		$ctrl 		= $app->input->getCmd('controller');
		$task 		= $app->input->getCmd('task');
		$id			= $app->input->getInt('id');

		$query = $db->getQuery(true)
			->select($db->qn('sv.SubmissionValueId'))
			->from($db->qn('#__rsform_submission_values', 'sv'))
			->join('left', $db->qn('#__rsform_submissions', 's').' ON ('.$db->qn('sv.SubmissionId').' = '.$db->qn('s.SubmissionId').')')
			->where($db->qn('sv.FormId').'='.$db->q($formId))
			->where($db->qn('sv.FieldName').'='.$db->q($data['NAME']))
			->where($db->qn('sv.FieldValue').'='.$db->q($value));

		// Is this a directory edit?
		if ($id && $option == 'com_rsform' && $ctrl == 'directory' && ($task == 'save' || $task == 'apply')) {
			$query->where($db->qn('s.SubmissionId').' != '.$db->q($id));
			
			// Override the $userValue based on the submission original values
			$newquery = $db->getQuery(true)
				->select($db->qn('UserId'))
				->select($db->qn('UserIp'))
				->from($db->qn('#__rsform_submissions'))
				->where($db->qn('SubmissionId').'='.$db->q($id));
			
			$submission = $db->setQuery($newquery)->loadObject();
			if ($submission->UserId) {
				$userField = 's.UserId';
				$userValue = $submission->UserId;
			} else {
				$userField = 's.UserIp';
				$userValue = $submission->UserIp;
			}
		}

		$query->where($db->qn($userField).'='.$db->q($userValue));

		return $db->setQuery($query)->loadResult() ? false : true;
	}
	
	public static function uszipcode($value)
	{
		return preg_match("/^([0-9]{5})(-[0-9]{4})?$/i",$value);
	}
	
	public static function phonenumber($value)
	{
		return preg_match("/\(?\b[0-9]{3}\)?[-. ]?[0-9]{3}[-. ]?[0-9]{4}\b/i", $value);
	}
	
	public static function creditcard($value,$extra=null,$data=null)
	{
		$value = preg_replace('/[^0-9]+/', '', $value);
		if (!$value)
			return false;
		
		if (preg_match("/^([34|37]{2})([0-9]{13})$/", $value) && self::luhn($value)) // Amex
			return true;
		
		if (preg_match("/^([30|36|38]{2})([0-9]{12})$/", $value) && self::luhn($value)) // Diners
			return true;
		
		if (preg_match("/^(?:6(?:011|5[0-9][0-9])[0-9]{12})$/", $value) && self::luhn($value)) // Discover
			return true;
			
		if (preg_match("/^([51|52|53|54|55]{2})([0-9]{14})$/", $value) && self::luhn($value)) // Master
			return true;
			
		if (preg_match("/^([4]{1})([0-9]{12,15})$/", $value) && self::luhn($value)) // Visa
			return true;
		
		return false;
	}

	public static function custom($param,$extra=null,$data=null)
	{
		if(strpos($param,"\n") !== FALSE) 
			$param = str_replace(array("\r","\n"),'',$param);
		
		for($i=0;$i<strlen($param);$i++)
			if(strpos($extra,$param[$i]) === false)
				return false;
				
		return true;
	}

	public static function password($param,$extra=null,$data=null)
	{
		if (RSFormProHelper::isCode($data['DEFAULTVALUE']) == $param)
			return true;
		
		return false;
	}
	
	public static function ipaddress($param,$extra=null,$data=null)
	{
		return preg_match('#\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b#', $param, $match);
	}
	
	public static function validurl($param,$extra=null,$data=null)
	{
		try
		{
			// URLs need a scheme to be valid
			if (!preg_match('/^(https?):\/\//i', $param, $match))
			{
				$param = 'http://'.$param;
			}
			
			// But too many schemes don't do
			if (substr_count($param, '://') > 1)
			{
				return false;
			}
			
			// Let's encode utf-8 characters
			$param = JStringPunycode::urlToPunycode($param);
			
			// Let's use the Joomla! Uri to grab the host
			$uri = JUri::getInstance($param);
			$host = $uri->getScheme().'://'.$uri->getHost();
			
			// Now FILTER_VALIDATE_URL should suffice
			if (filter_var($host, FILTER_VALIDATE_URL))
			{
				return true;
			}
			
			return false;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	public static function regex($value,$pattern=null,$data=null) {
		return preg_match($pattern, $value);
	}
	
	public static function sameas($value, $secondField, $data) {
		$valid 	= false;
		$form 	= JFactory::getApplication()->input->get('form', array(), 'array');
		if (isset($form[$secondField])) {
			$secondValue = is_array($form[$secondField]) ? implode('', $form[$secondField]) : $form[$secondField];
			if ($value == $secondValue) {
				$valid = true;
			}
		}
		
		return $valid;
	}
	
	public static function multiplerules($value, $extra = null, $data = null) {
		$validations 	= explode(',', $data['VALIDATIONMULTIPLE']);
		$extra 			= json_decode($extra);
	
		if (!empty($validations)) {
			foreach ($validations as $function) {
				$newData = $data;
				unset($newData['VALIDATIONMULTIPLE']);
				
				$newData['VALIDATIONRULE']  = $function;
				$newData['VALIDATIONEXTRA'] = !empty($extra->{$function}) ? $extra->{$function} : null;

				if (!call_user_func_array('static::'.$function, array($value, $newData['VALIDATIONEXTRA'], $newData))) {
					return false;
				}
			}
		}
		
		return true;
	}

	public static function iban($value, $extra = null, $data = null)
    {
        require_once __DIR__ . '/iban.php';
        $iban = new RSFormIBAN($value);
        return $iban->validate();
    }
	
	protected static function luhn($value) {
		$sum = 0;
		$odd = strlen($value) % 2;

		// Calculate sum of digits.
		for($i = 0; $i < strlen($value); $i++) {
			$sum += $odd ? $value[$i] : (($value[$i] * 2 > 9) ? $value[$i] * 2 - 9 : $value[$i] * 2);
			$odd = !$odd;
		}

		// Check validity.
		return ($sum % 10 == 0) ? true : false;
	}
}