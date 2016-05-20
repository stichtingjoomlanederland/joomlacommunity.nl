<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 

class rseventsproEmails
{	
	/*
	*	Get available placeholders
	*/
	
	public static function placeholders($text, $ide, $name, $optionals = null, $ids = null) {
		static $cache = array();
		
		if (!isset($cache[$ide])) {
			// Get the site root
			$u		= JURI::getInstance();	
			$root	= $u->toString(array('scheme','host'));
			
			// Load language
			JFactory::getLanguage()->load('com_rseventspro');
			
			$details	= rseventsproHelper::details($ide);
			$event		= $details['event'];
			$categories	= $details['categories'];
			$tags		= $details['tags'];
			
			// The event link
			$eventlink = $root.rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false,rseventsproHelper::itemid($event->id));
			
			// The location link
			$locationlink = $root.rseventsproHelper::route('index.php?option=com_rseventspro&layout=location&id='.rseventsproHelper::sef($event->locationid,$event->location));
			
			if (JFactory::getApplication()->isAdmin()) {
				$eventlink = str_replace('/administrator','',$eventlink);
				$locationlink = str_replace('/administrator','',$locationlink);
			}

			// Event times
			$startdate	= $event->allday ? rseventsproHelper::showdate($event->start,rseventsproHelper::getConfig('global_date'),true) : rseventsproHelper::showdate($event->start,null,true);
			$sdate		= rseventsproHelper::showdate($event->start,rseventsproHelper::getConfig('global_date'));
			$sdatetime	= $event->allday ? '' : rseventsproHelper::showdate($event->start,rseventsproHelper::getConfig('global_time'));
			$enddate	= $event->allday ? '' : rseventsproHelper::showdate($event->end,null,true);
			$edate		= $event->allday ? '' : rseventsproHelper::showdate($event->end,rseventsproHelper::getConfig('global_date'));
			$edatetime	= $event->allday ? '' : rseventsproHelper::showdate($event->end,rseventsproHelper::getConfig('global_time'));
			
			$owner = JFactory::getUser($event->owner);
			
			$search = array('{EventName}','{EventLink}','{EventDescription}','{EventStartDate}','{EventStartDateOnly}','{EventStartTime}','{EventEndDate}','{EventEndDateOnly}','{EventEndTime}','{Owner}','{OwnerUsername}','{OwnerName}','{OwnerEmail}','{EventURL}','{EventPhone}','{EventEmail}','{LocationName}','{LocationLink}','{LocationDescription}','{LocationURL}','{LocationAddress}','{EventCategories}','{EventTags}','{EventIconSmall}','{EventIconBig}');
			$replace = array($event->name, $eventlink, $event->description, $startdate, $sdate, $sdatetime, $enddate, $edate, $edatetime, $event->ownername, $owner->get('username'), $owner->get('name'), $owner->get('email'), $event->URL, $event->phone, $event->email, $event->location, $locationlink, $event->ldescription, $event->locationlink, $event->address, $categories, $tags, $details['image_s'], $details['image_b']);
			
			$cache[$ide] = array('search' => $search, 'replace' => $replace);
		} else  {
			$search = $cache[$ide]['search'];
			$replace = $cache[$ide]['replace'];
		}
		
		$search[]	= '{Message}';
		$search[]	= '{message}';
		$search[]	= '{User}';
		$search[]	= '{user}';
		$search[]	= '{timezone}';
		$replace[]	= JFactory::getApplication()->input->getHtml('message');
		$replace[]	= JFactory::getApplication()->input->getHtml('message');
		$replace[]	= $name;
		$replace[]	= $name;
		$replace[]	= rseventsproHelper::getTimezone();
		
		if (!is_null($ids)) {
			$search[] 	= '{barcodetext}';
			$replace[]	= rseventsproHelper::getConfig('barcode_prefix', 'string', 'RST-').$ids;
		}
		
		$optionalsSearch = array('{TicketInfo}','{TicketsTotal}','{Discount}','{Tax}','{LateFee}','{EarlyDiscount}','{Gateway}','{IP}','{Coupon}');
		
		if (is_array($text)) {
			foreach($text as $name => $value) {
				$text[$name] = str_replace($search,$replace,$value);
			}
			
			if (!is_null($optionals) && is_array($optionals)) {
				$text['body'] = str_replace($optionalsSearch,$optionals,$text['body']);
			}
		} else {
			$text = str_replace($search,$replace,$text);
			
			if (!is_null($optionals) && is_array($optionals)) {
				$text = str_replace($optionalsSearch,$optionals,$text);
			}
		}
		
		return $text;
	}
	
	
	/*
	*	Invite e-mail
	*/
	
	public static function invite($from, $fromName, $to, $ide, $lang = 'en-GB') {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('invite', null, null, $lang);
		
		if (empty($email) || !$email->enable)
			return false;
		
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		$text			 	= rseventsproEmails::placeholders($replacer, $ide, $to);
		$text['cc']		 	= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']  = isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		$text['body']	 	= str_replace(array('{from}','{fromname}'),array($from, $fromName), $text['body']);
		
		$mailer	= JFactory::getMailer();
		$mailer->sendMail($from , $fromName , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		
		return true;
	}
	
	
	/*
	*	Registration e-mail
	*/
	
	public static function registration($to, $ide, $name, $optionals, $ids = null) {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('registration', $to, $ide);
		
		if (empty($email) || !$email->enable)
			return false;
			
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		$paymentURL = '';
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		if ($ids) {
			$db = JFactory::getDbo();
			
			$query = $db->getQuery(true)->select($db->qn('URL'))
					->from($db->qn('#__rseventspro_users'))
					->where($db->qn('id').' = '.$db->q($ids));
			$db->setQuery($query);
			if ($url = $db->loadResult()) {
				$paymentURL = base64_decode($url);
				$root = JUri::getInstance()->toString(array('scheme','host'));
				if (strpos($paymentURL,$root) === false) {
					$paymentURL = $root.$paymentURL;
				}
			}
		}
		
		JFactory::getApplication()->triggerEvent('rseproRegistrationEmail', array(array('ids' => $ids, 'data' => &$replacer)));
		
		$text			 	= rseventsproEmails::placeholders($replacer, $ide, $name, $optionals, $ids);
		$text['cc']		 	= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']	 	= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto'] 	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']  = isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		$text['body']	 	= str_replace('{PaymentURL}',$paymentURL,$text['body']);
		
		$mailer	= JFactory::getMailer();
		$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		
		return true;
	}
	
	
	/*
	*	Activation e-mail
	*/
	
	public static function activation($to, $ide, $name, $optionals, $ids = null) {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('activation', $to, $ide);
		
		if (empty($email) || !$email->enable)
			return false;
		
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		JFactory::getApplication()->triggerEvent('rseproActivationEmail', array(array('ids' => $ids, 'data' => &$replacer)));
		
		$text				= rseventsproEmails::placeholders($replacer, $ide, $name, $optionals, $ids);
		$text['cc']			= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']  = isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		$attachment			= rseventsproEmails::pdfAttachement($to,$ide,$name,$optionals,$ids);
		
		$mailer	= JFactory::getMailer();
		if ($mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , $attachment , $text['replyto'], $text['replyname'])) {
			JFactory::getApplication()->triggerEvent('rsepro_activationEmailCleanup',array(array('id'=>&$ide)));
			
			$db	= JFactory::getDBO();
			$query = $db->getQuery(true);
			
			$query->clear()
				->select($db->qn('ticket_pdf_layout'))
				->from($db->qn('#__rseventspro_events'))
				->where($db->qn('id').' = '.(int) $ide);
			
			$db->setQuery($query);
			$layout = $db->loadResult();
			
			if (strpos($layout,'{barcode}') !== FALSE) {
				jimport('joomla.filesystem.file');
				if (JFile::exists(JPATH_SITE.'/components/com_rseventspro/assets/barcode/rset-'.md5($name).'.png')) {
					JFile::delete(JPATH_SITE.'/components/com_rseventspro/assets/barcode/rset-'.md5($name).'.png');
				}
			}
		}
		
		return true;
	}
	
	
	/*
	*	Unsubscribe e-mail
	*/
	
	public static function unsubscribe($to, $ide, $name, $lang = 'en-GB', $ids = null) {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('unsubscribe', null, null, $lang);
		
		if (empty($email) || !$email->enable)
			return false;
		
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		JFactory::getApplication()->triggerEvent('rseproUnsubscribeEmail', array(array('ids' => $ids, 'data' => &$replacer)));
		
		$text			 	= rseventsproEmails::placeholders($replacer,$ide,$name, null, $ids);
		$text['cc']		 	= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']	 	= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto'] 	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']  = isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		$mailer	= JFactory::getMailer();
		$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		
		return true;
	}
	
	/*
	*	Denied e-mail
	*/
	
	public static function denied($to, $ide, $name, $ids = null) {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('denied', $to, $ide);
		
		if (empty($email) || !$email->enable)
			return false;
		
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		JFactory::getApplication()->triggerEvent('rseproDeniedEmail', array(array('ids' => $ids, 'data' => &$replacer)));
		
		$text			 	= rseventsproEmails::placeholders($replacer,$ide,$name,null,$ids);
		$text['cc']		 	= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']	 	= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto'] 	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']  = isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		$mailer	= JFactory::getMailer();
		$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		
		return true;
	}
	
	/*
	*	Reminder e-mail
	*/
	
	public static function reminder($to, $ide, $name, $lang = 'en-GB') {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('reminder', null, null, $lang);
		
		if (empty($email) || !$email->enable)
			return false;
			
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		$text			 	= rseventsproEmails::placeholders($replacer,$ide,$name);
		$text['cc']		 	= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']	 	= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto'] 	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']  = isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		$mailer	= JFactory::getMailer();
		$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		
		return true;
	}
	
	
	/*
	*	Post-reminder e-mail
	*/
	
	public static function postreminder($to, $ide, $name, $lang = 'en-GB') {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('preminder', null, null, $lang);
		
		if (empty($email) || !$email->enable)
			return false;
		
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		$text			 = rseventsproEmails::placeholders($replacer, $ide, $name);
		$text['cc']		 		= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']	 	= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto'] 	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']  = isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		$mailer	= JFactory::getMailer();
		$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		
		return true;
	}
	
	/*
	*	Guests e-mail
	*/
	
	public static function guests($to, $ide, $name, $subject, $body) {
		$config		= rseventsproHelper::getConfig();
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= 1;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		$text			 	= rseventsproEmails::placeholders($replacer,$ide,$name);
		$text['cc']		 	= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']	 	= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto'] 	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']  = isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		$mailer	= JFactory::getMailer();
		$mailer->sendMail($text['from'], $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		
		return true;
	}
	
	
	/*
	*	Moderation email
	*/
	
	public function moderation($to, $ide, $lang = 'en-GB') {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('moderation', null, null, $lang);
		
		if (empty($email))
			return false;
		
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		
		$text				= rseventsproEmails::placeholders($replacer,$ide,'');
		$text['cc']			= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']  = isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		$approve			= rseventsproHelper::route(JURI::root().'index.php?option=com_rseventspro&task=activate&key='.md5('event'.$ide));
		$text['body']		= str_replace('{EventApprove}',$approve,$text['body']);
		
		$mailer	= JFactory::getMailer();
		$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		
		return true;
	}
	
	public static function tag_moderation($to, $ide, $items, $lang = 'en-GB') {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('tag_moderation', null, null, $lang);
		
		if (empty($email))
			return false;
		
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		$text				= rseventsproEmails::placeholders($replacer,$ide,'');
		$text['cc']		 	= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']	 	= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto'] 	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']  = isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		// html
		if ($mode) {
			$approve = '<ul>';
			foreach ($items as $item) {
				$link = rseventsproHelper::route(JURI::root().'index.php?option=com_rseventspro&task=tagactivate&key='.md5('tag'.$item->id));
				$approve .= "\n".'<li><a href="'.$link.'">'.JText::sprintf('RSEPRO_APPROVE_TAG', $item->name).'</a></li>';
			}
			$approve .= '</ul>';
		} else // no html
		{
			$approve = '';
			foreach ($items as $item) {
				$link = rseventsproHelper::route(JURI::root().'index.php?option=com_rseventspro&task=tagactivate&key='.md5('tag'.$item->id));
				$approve .= "\n".JText::sprintf('RSEPRO_APPROVE_TAG', $item->name).': '.$link;
			}
		}
		$text['body'] = str_replace('{TagsApprove}',$approve,$text['body']);
		
		$mailer	= JFactory::getMailer();
		$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		
		return true;
	}
	
	/*
	*	Approval e-mail
	*/
	
	public static function approval($to, $ide, $name, $lang = 'en-GB') {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('approval', null, null, $lang);
		
		if (empty($email) || empty($to))
			return false;
		
		if (!$email->enable)
			return false;
		
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		$text			 	= rseventsproEmails::placeholders($replacer, $ide, $name);
		$text['cc']		 	= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto'] 	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']  = isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		$mailer	= JFactory::getMailer();
		$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		
		return true;
	}
	
	/*
	*	New event subscription notification email
	*/
	
	public static function notify_me($to, $ide, $additional_data = array(), $lang = 'en-GB', $optionals = null, $ids = null) {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('notify_me', null, null, $lang);
		
		if (empty($email))
			return false;
		
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		JFactory::getApplication()->triggerEvent('rseproNotifyEmail', array(array('ids' => $ids, 'data' => &$replacer)));
		
		$text			 	= rseventsproEmails::placeholders($replacer,$ide,'',$optionals, $ids);
		$text['cc']		 	= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']	 	= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto'] 	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']  = isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		if ($additional_data) {
			$text['body']		= str_replace(array_keys($additional_data), array_values($additional_data), $text['body']);
			$text['subject']	= str_replace(array_keys($additional_data), array_values($additional_data), $text['subject']);
		}
		
		$mailer	= JFactory::getMailer();
		$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		
		return true;
	}
	
	/*
	*	Report email
	*/
	public static function report($to, $ide, $additional_data = array(), $lang = 'en-GB') {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('report', null, null, $lang);
		
		if (empty($email) || empty($to) || !$email->enable)
			return false;
		
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		$text			 	= rseventsproEmails::placeholders($replacer,$ide,'');
		$text['cc']		 	= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']	 	= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto'] 	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']  = isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		if ($additional_data) {
			$text['body']		= str_replace(array_keys($additional_data), array_values($additional_data), $text['body']);
			$text['subject']	= str_replace(array_keys($additional_data), array_values($additional_data), $text['subject']);
		}
		
		$mailer	= JFactory::getMailer();
		$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		
		return true;
	}
	
	
	/*
	*	Rule email
	*/
	
	public static function rule($ids, $message) {
		$config		= rseventsproHelper::getConfig();
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$paymentURL = '';
		
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		
		// Get subscription details
		$query  ->clear()
				->select('*')
				->from($db->qn('#__rseventspro_users'))
				->where($db->qn('id').' = '.(int) $ids);
		
		$db->setQuery($query);
		$subscription = $db->loadObject();
		$subscriber =& $subscription;
		
		if ($subscriber->URL) {
			$paymentURL = base64_decode($subscriber->URL);
			$root = JUri::getInstance()->toString(array('scheme','host'));
			if (strpos($paymentURL,$root) === false) {
				$paymentURL = $root.$paymentURL;
			}
		}
		
		// Get tickets
		$tickets = rseventsproHelper::getUserTickets($ids);
		$info	 = '';
		
		if (!empty($tickets)) {
			foreach ($tickets as $ticket) {
				// Calculate the total
				if ($ticket->price > 0) {
					$price = $ticket->price * $ticket->quantity;
					$total += $price;
					$info .= $ticket->quantity . ' x ' .$ticket->name.' ('.rseventsproHelper::currency($ticket->price).') '.rseventsproHelper::getSeats($ids,$ticket->id).' <br />';
				} else {
					$info .= $ticket->quantity . ' x ' .$ticket->name.' ('.JText::_('COM_RSEVENTSPRO_GLOBAL_FREE').') <br />';
				}
			}
		}
		
		if (!empty($subscription->discount) && !empty($total)) {
			$total = $total - $subscription->discount;
		}
		
		if (!empty($subscription->early_fee) && !empty($total)) {
			$total = $total - $subscription->early_fee;
		}
		
		if (!empty($subscription->late_fee) && !empty($total)) {
			$total = $total + $subscription->late_fee;
		}
		
		if (!empty($subscription->tax) && !empty($total)) {
			$total = $total + $subscription->tax;
		}
		
		$ticketstotal		= rseventsproHelper::currency($total);
		$ticketsdiscount	= !empty($subscription->discount) ? rseventsproHelper::currency($subscription->discount) : '';
		$subscriptionTax	= !empty($subscription->tax) ? rseventsproHelper::currency($subscription->tax) : '';
		$lateFee			= !empty($subscription->late_fee) ? rseventsproHelper::currency($subscription->late_fee) : '';
		$earlyDiscount		= !empty($subscription->early_fee) ? rseventsproHelper::currency($subscription->early_fee) : '';
		$gateway			= rseventsproHelper::getPayment($subscription->gateway);
		$IP					= $subscription->ip;
		$coupon				= !empty($subscription->coupon) ? $subscription->coupon : '';
		$optionals			= array($info, $ticketstotal, $ticketsdiscount, $subscriptionTax, $lateFee, $earlyDiscount, $gateway, $IP, $coupon);
		
		$email = rseventsproEmails::emailrule($message, $subscriber->lang);
		if (!$email) return false;
		
		$mode			= @$email->mode;
		$subject		= @$email->subject;
		$body			= @$email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		$text				= rseventsproEmails::placeholders($replacer, $subscriber->ide, $subscriber->name, $optionals);
		$text['cc']			= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']  = isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		$text['body']		= str_replace('{Status}',rseventsproHelper::getStatuses($subscriber->state),$text['body']);
		$text['body']		= str_replace('{PaymentURL}',$paymentURL,$text['body']);
		
		$mailer	= JFactory::getMailer();
		$mailer->sendMail($text['from'] , $text['fromName'] , $subscriber->email , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		
		return true;
	}
	
	/*
	*	Attach the pdf to the activation email
	*/
	public static function pdfAttachement($to, $ide, $name, $optionals, $ids) {
		$pdf = rseventsproHelper::pdf();
		
		if ($pdf) {
			$db  = JFactory::getDBO();
			$query = $db->getQuery(true);
			
			$query->clear()
				->select($db->qn('id'))
				->select($db->qn('ticket_pdf_layout'))
				->from($db->qn('#__rseventspro_events'))
				->where($db->qn('id').' = '.(int) $ide);
				
			$db->setQuery($query);
			$event = $db->loadObject();
			$layout = $event->ticket_pdf_layout;
			
			JFactory::getApplication()->triggerEvent('rseproTicketPDFLayout',array(array('ids'=>$ids,'layout'=>&$layout)));
			
			$layout = rseventsproEmails::placeholders($layout, $ide, $name, $optionals);
			$layout = str_replace('{sitepath}',JPATH_SITE,$layout);
			
			if (strpos($layout,'{barcode}') !== FALSE) {
				$query->clear()
					->select($db->qn('id'))
					->from($db->qn('#__rseventspro_users'))
					->where($db->qn('name').' = '.$db->q($name))
					->where($db->qn('email').' = '.$db->q($to))
					->where($db->qn('ide').' = '.$db->q($ide))
					->order($db->qn('date').' DESC');
				
				$db->setQuery($query);
				$ids = $db->loadResult();
				
				jimport('joomla.filesystem.file');
				require_once JPATH_SITE.'/components/com_rseventspro/helpers/pdf/barcodes.php';
				$barcode = new TCPDFBarcode(rseventsproHelper::getConfig('barcode_prefix', 'string', 'RST-').$ids, rseventsproHelper::getConfig('barcode'));
				
				ob_start();
				$barcode->getBarcodePNG();
				$thecode = ob_get_contents();
				ob_end_clean();
				
				$file = JPATH_SITE.'/components/com_rseventspro/assets/barcode/rset-'.md5($name).'.png';
				$upload = JFile::write($file, $thecode);
				$barcodeHTML = $upload ? '<img src="'.$file.'" alt="" />' : '';
				
				$layout = str_replace('{barcode}',$barcodeHTML,$layout);
			}
			
			$query->clear()
				->select($db->qn('email'))
				->from($db->qn('#__rseventspro_users'))
				->where($db->qn('name').' = '.$db->q($name))
				->where($db->qn('email').' = '.$db->q($to))
				->where($db->qn('ide').' = '.$db->q($ide));
			
			$db->setQuery($query);
			$semail = $db->loadResult();
			
			$layout = str_replace('{useremail}',$semail,$layout);
			$layout = str_replace('{barcodetext}',rseventsproHelper::getConfig('barcode_prefix', 'string', 'RST-').$ids,$layout);
			
			$attachment = null;
			JFactory::getApplication()->triggerEvent('rsepro_activationEmail',array(array('id'=>&$ide,'attachment'=>&$attachment,'layout'=>&$layout)));
			return $attachment;
		}
		
		return null;
	}
	
	/*
	*	Get the subject and message text
	*/
	
	public static function email($type, $to, $ide, $ulang = 'en-GB') {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		
		if (is_null($to) && is_null($ide)) {
			$userlanguage = $ulang;
		} else {
			// Get user language
			$query->clear()
				->select($db->qn('lang'))
				->from($db->qn('#__rseventspro_users'))
				->where($db->qn('email').' = '.$db->q($to))
				->where($db->qn('ide').' = '.$db->q($ide));
			
			$db->setQuery($query);
			$userlanguage = $db->loadResult();
			
			// If we don't find the users language, we set the language to english (en-GB)
			if (empty($userlanguage)) {
				$userlanguage = 'en-GB';
			}
		}
		
		$query->clear()
			->select($db->qn('id'))
			->from($db->qn('#__rseventspro_emails'))
			->where($db->qn('lang').' = '.$db->q($userlanguage))
			->where($db->qn('type').' = '.$db->q($type));
		$db->setQuery($query);
		$emailid = (int) $db->loadResult();
		
		if (!$emailid)
			$userlanguage = 'en-GB';
		
		// Get email details
		$query->clear()
			->select($db->qn('subject'))->select($db->qn('message'))
			->select($db->qn('enable'))->select($db->qn('mode'))
			->from($db->qn('#__rseventspro_emails'))
			->where($db->qn('lang').' = '.$db->q($userlanguage))
			->where($db->qn('type').' = '.$db->q($type));
			
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	/*
	*	Get rule emails
	*/
	
	public static function emailrule($mid, $lang = 'en-GB') {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		
		// Get all emails
		$query->clear()
			->select($db->qn('mode'))->select($db->qn('subject'))
			->select($db->qn('message'))->select($db->qn('lang'))
			->from($db->qn('#__rseventspro_emails'))
			->where($db->qn('type').' = '.$db->q('rule'))
			->where('('.$db->qn('id').' = '.(int) $mid.' OR '.$db->qn('parent').' = '.(int) $mid.')');
			
		$db->setQuery($query);
		$emails = $db->loadObjectList();
		
		if (empty($emails)) 
			return false;
		
		// Search for the email that have the selected language
		foreach ($emails as $email) {
			if ($email->lang == $lang) {
				return $email;
			}
		}
		
		// If there is no email with the selected language get the first email
		return $emails[0];
	}
}