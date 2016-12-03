<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

class acyuserHelper {

	function getIP(){
		$ip = '';
		if( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) AND strlen($_SERVER['HTTP_X_FORWARDED_FOR'])>6 ){
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}elseif( !empty($_SERVER['HTTP_CLIENT_IP']) AND strlen($_SERVER['HTTP_CLIENT_IP'])>6 ){
			 $ip = $_SERVER['HTTP_CLIENT_IP'];
		}elseif(!empty($_SERVER['REMOTE_ADDR']) AND strlen($_SERVER['REMOTE_ADDR'])>6){
			 $ip = $_SERVER['REMOTE_ADDR'];
		}//endif

		return strip_tags($ip);
	}

	function validEmail($email,$extended = false){
		if(empty($email) OR !is_string($email)) return false;

		if(!preg_match('/^([a-z0-9_\'&\.\-\+=])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,10})+$/i',$email)) return false;

		if(!$extended) return true;

		$config = acymailing_config();

		if($config->get('email_checkpopmailclient',false)){
			if(preg_match('#^.{1,5}@(gmail|yahoo|aol|hotmail|msn|ymail)#i',$email)) return false;
		}

		if($config->get('email_checkdomain',false) AND function_exists('getmxrr')){
			$domain = substr($email,strrpos($email,'@')+1);
			$mxhosts = array();
			$checkDomain = getmxrr($domain, $mxhosts);
			if(!empty($mxhosts) && strpos($mxhosts[0],'hostnamedoesnotexist')) array_shift($mxhosts);
			if(!$checkDomain || empty($mxhosts)){
				$dns = @dns_get_record($domain, DNS_A);
				$domainChanged = true;
				foreach($dns as $oneRes){
					if(strtolower($oneRes['host']) == strtolower($domain)) $domainChanged = false;
				}
				if(empty($dns) || $domainChanged) return false;
			}
		}
		$object = new stdClass();
		$object->IP = $this->getIP();
		$object->emailAddress = $email;

		if($config->get('email_botscout',false)){
			$botscoutClass = new acybotscout();
			$botscoutClass->apiKey = $config->get('email_botscout_key');
			if(!$botscoutClass->getInfo($object)) return false;
		}

		if($config->get('email_stopforumspam',false)){
			$email_stopforumspam = new acystopforumspam();
			if(!$email_stopforumspam->getInfo($object)) return false;
		}

		if($config->get('email_iptimecheck', 0)){
			$lapseTime = time() - 7200;
			$db = JFactory::getDBO();
			$db->setQuery('SELECT COUNT(*) FROM #__acymailing_subscriber WHERE created > '.intval($lapseTime).' AND ip = '.$db->Quote($object->IP));
			$nbUsers = $db->loadResult();
			if($nbUsers >= 3) return false;
		}

		return true;
	}

	function getUserGroups($userid){
		$db = JFactory::getDBO();
		if(ACYMAILING_J16){
			$db->setQuery('SELECT ug.id, ug.title FROM #__usergroups AS ug JOIN #__user_usergroup_map AS ugm ON ug.id = ugm.group_id WHERE ugm.user_id = '.intval($userid));
		}else{
			$db->setQuery('SELECT gid AS id, userType AS title FROM #__users WHERE id = '.intval($userid));
		}
		$groups = $db->loadObjectList();
		return $groups;
	}
}

class acybotscout {

	var $apiKey = '';
	var $conn;
	var $error = '';


	function connect() {
		if (is_resource($this->conn)) return true;

		$this->conn = fsockopen('www.botscout.com', 80, $errno, $errstr, 20);
		if (!$this->conn) {
			$this->error = "Could not open connection " . $errstr;
			return false;
		}
		return true;
	}

	function getInfo(&$object) {
		if (!$this->connect()) return true;
		$result = true;

		if (!empty($object->IP) && $object->IP != '127.0.0.1') {
			$data = 'ip=' . $object->IP;
			$resIP = $this->sendInfo($data);
			$result = $this->checkXML($resIP,$object) && $result;
		}
		if (!empty($object->emailAddress)) {
			$data = 'mail=' . $object->emailAddress;
			$resAddress = $this->sendInfo($data);
			$result = $this->checkXML($resAddress,$object) && $result;
		}

		if (is_resource($this->conn)) fclose($this->conn);

		return $result;
	}

	function sendInfo($data) {
		$res = '';
		if (!empty($this->apiKey)) $data .= '&key=' . $this->apiKey;
		$data .= '&format=xml';
		$header = "GET /test/?" . $data . " HTTP/1.1\r\n";
		$header .= "Host: www.botscout.com \r\n";
		$header .= "Connection: keep-alive\r\n\r\n";
		fwrite($this->conn, $header);
		while (!feof($this->conn)) {
			$res .= fread($this->conn, 1024);
			if (strpos($res,"</response>")) break;
		}
		return $res;
	}

	function checkXML($res,$object) {

		if(!preg_match('#<response.*</response>#Uis',$res,$results)){
			$this->error = 'There is an error while trying to get the xml could not find "<reponse>"';
			return true;
		}

		$xml = new SimpleXMLElement($results[0]);
		if ($xml->matched == "Y" && $xml->test == 'IP'){
			$this->error .= 'There is a problem with the IP : '.$object->IP.' you used to do the registration ( Spam test positive )</br>'; // Check failed. Result indicates dangerous.
			return false;
		}
		if ($xml->matched == "Y" && $xml->test == 'MAIL'){
			$this->error .= 'There is a problem with the email : '.$object->emailAddress.' you entered in the form ( Spam test positive )</br>';
			return false;
		}
		return true;
	}
}



class acystopforumspam {

	var $conn;
	var $error = '';

	function connect() {
		$this->conn = fsockopen('www.stopforumspam.com', 80, $errno, $errstr, 20);
		if (!$this->conn) {
			$this->error = "Could not open connection " . $errstr;
			return false;
		}
		return true;
	}

	function getInfo(&$object) {
		if (!$this->connect())	return true;

		$IP = '';
		$emailAddress = '';

		if(empty($object->IP) && empty($object->emailAddress)) return true;
		if(!empty($object->IP)) $IP ='ip='.$object->IP.'&';
		if(!empty($object->emailAddress)) $emailAddress ='email='.$object->emailAddress.'&';

		$data = $IP.$emailAddress;
		$data = trim($data,'&');
		$res='';

		$header = "GET /api?".$data." HTTP/1.1\r\n";
		$header .= "Host: www.stopforumspam.com \r\n";
		$header .= "Connection: Close\r\n\r\n";
		fwrite($this->conn, $header);
		while (!feof($this->conn)) {
			$res .= fread($this->conn, 1024);
		}

		if(!preg_match('#<response.*</response>#Uis',$res,$results)){
			$this->error = 'There is an error while trying to get the xml could not find "<reponse>"';
			return true;
		}

		$xml = new SimpleXMLElement($results[0]);

		$number=0;
		foreach($xml->appears as  $oneTest){
			if ($oneTest == "yes")
			{
				if(strtolower($xml->type[$number]) == 'ip') $problemSource = $object->IP ;
				if(strtolower($xml->type[$number]) == 'email') $problemSource = $object->emailAddress ;
				$this->error .= 'There is a problem with the '.$xml->type[$number].' : '.$problemSource.' you used ( Spam test positive ) </br>'; // Check failed. Result indicates dangerous.
				return false;
			}
			elseif ($oneTest == "no"){}
			else{
				$this->error = 'There is a problem with the result. Service down ? '; // Test returned neither positive or negative result. Service might be down?
				continue;
			}
			$number ++ ;
		}
		return true;
	}
}
