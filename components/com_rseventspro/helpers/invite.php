<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

// YAHOO Class
class RSYahoo
{
	public function results($username, $password) {
		if (empty($username) || empty($password)) 
			return false;
		
		$result = RSYahoo::getContacts($username, $password);		
		
		if ((!is_array($result) && ($result == 1 || $result == 2)) || empty($result)) 
			return false;
		
		$return = '';
		foreach ($result as $name => $email)
			$return .= $email."\n";
		
		return rtrim($return,"\n");
	}
	
	public function getContacts($login, $password) {
		global $location;
		global $cookiearr;
		global $ch;
		
		if ((isset($login) && trim($login)=="") || (isset($password) && trim($password)==""))
			return 2;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"https://login.yahoo.com/config/login_verify2");
		curl_setopt($ch, CURLOPT_REFERER, "");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		
		$agents = array(
			"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1",
			"Opera/9.80 (Windows NT 5.1; U; en) Presto/2.2.15 Version/10.10",
			"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/532.0 (KHTML, like Gecko) Chrome/3.0.195.33 Safari/532.0",
			"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/528.16 (KHTML, like Gecko) Version/4.0 Safari/528.16"
		);
		curl_setopt($ch, CURLOPT_USERAGENT, $agents[mt_rand(0,3)]);
		$html = curl_exec($ch);
		
		$matches = array();
		
		preg_match_all('/<input type\="hidden" name\="([^"]+)" value\="([^"]*)">/', $html, $matches);
		$values = $matches[2];
		$params = "";
		
		$i=0;
		foreach ($matches[1] as $name) {
		  $params .= "$name=$values[$i]&";
		  ++$i;
		}
		
		$login = urlencode($login);
		$password = urlencode($password);
		
		curl_setopt($ch, CURLOPT_URL,"https://login.yahoo.com/config/login?");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params . "login=$login&passwd=$password");
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'RSYahoo::read_header');
		
		//curl_setopt($ch, CURLOPT_HEADER, 1);
		
		$html = curl_exec($ch);
		
		if (!is_array($cookiearr) || !isset($cookiearr['F']))
			return 1;
		
		preg_match('/\.rand=([^"]*)"/si', $html, $matches);
		$value = @$matches[1];
		
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_URL,"http://address.mail.yahoo.com/?1&VPC=import_export&A=B&.rand=$value");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		
		$html = curl_exec($ch);
		
		$matches = array();
		preg_match('/name="\.crumb".*?id="crumb2".*?value="([^"]*)"/si', $html, $matches);
		$crumb = $matches[1];
		
		preg_match('/\.rand=([^"]*)"/si', $html, $matches);
		$rand = $matches[1];
		
		
		$action = "http://address.yahoo.com/?1&VPC=print&A=B&.rand=$rand";
		curl_setopt($ch, CURLOPT_URL, $action);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, ".crumb=$crumb&VPC=print&field%5Ballc%5D=1&field%5Bstyle%5D=quick&A=B&submit%5Baction_display%5D=Display+for+Printing");
		$html = curl_exec($ch);
		
		curl_close ($ch);
		
		$blockpattern = '#<table class=\"qprintable2\" ([^>]*)>(.*?)<\/table>#is';
		$trpattern = '#<tr ([^>]*)>(.*?)<\/tr>#is';
		$trpattern = '/<tr.*?>(.*?)<\/[\s]*tr>/s';
		$namepatternb = '#<b>(.*)<\/b>#is';
		$namepatternsmall = '#<small>(.*)<\/small>#is';
		$emailpattern = '#<div>(.*?)<\/div>#is';
		$addresses = array();

		preg_match_all($blockpattern,$html,$blocks);

		if (!empty($blocks) && !empty($blocks[2])) {
			foreach ($blocks[2] as $block) {
				preg_match_all($trpattern,$block,$rows);
				
				if (!empty($rows) && !empty($rows[1])) {
					$address_name = '';
					$address_email = '';
					
					preg_match($namepatternb,$rows[1][0],$datab);
					preg_match($namepatternsmall,$rows[1][0],$datasmall);
					if (!empty($datab) && !empty($datab[1]))
						$address_name = trim($datab[1]);
					
					if (!empty($datab) && empty($datab[1]))
					{
						if (!empty($datasmall) && !empty($datasmall[1]))
							$address_name = trim($datasmall[1]);
					}
					
					preg_match($emailpattern,$rows[1][1],$email);
					if (!empty($email) && !empty($email[1]))
						$address_email = trim($email[1]);
					
					if (empty($address_email) && !empty($datasmall) && !empty($datasmall[1]))
					{
						$address_email = trim($datasmall[1]);
						$address_email = stristr($address_email,'@') ? $address_email : $address_email.'@yahoo.com';
					}
					
					if (empty($address_email)) continue;
					
					$addresses[$address_name] = $address_email;
				}
			}
		}
		
		return $addresses;
	}
	
	protected function read_header($ch, $string) {
		global $location;
		global $cookiearr;
		global $ch;

		$length = strlen($string);
		
		if(!strncmp($string, "Location:", 9))
			$location = trim(substr($string, 9, -1));

		if(!strncmp($string, "Set-Cookie:", 11)) {
			$cookiestr = trim(substr($string, 11, -1));
			$cookie = explode(';', $cookiestr);
			$cookie = explode('=', $cookie[0]);
			$cookiename = trim(array_shift($cookie)); 
			$cookiearr[$cookiename] = trim(implode('=', $cookie));
		}
		
		$cookie = "";
		if(trim($string) == "") 
			if(!empty($cookiearr))
			{
				foreach ($cookiearr as $key=>$value)
					$cookie .= "$key=$value; ";
				curl_setopt($ch, CURLOPT_COOKIE, $cookie);
			}

		return $length;
	}
}

// GMAIL Class

class RSGoogle
{
	/*
	*	Get the contact list of the current email address
	*
	*	Returns :
	*			0 - if the email or the password is empty
	*			1 - if there was an error connecting to the Google server
	*			2 - if there is no Authorization code
	*			3 - no contacts
	*			list of available contacts
	*
	*/
	
	public function getContacts($username,$password) {
		if (empty($username) || empty($password)) 
			return 0;
		
		$returns = array();
		$contacts = array();
		
		$username = stristr($username,'@') ? $username : $username.'@gmail.com';
		$login_url = "https://www.google.com/accounts/ClientLogin";
		$useragent = RSGoogle::useragents();
		
		$fields = array(
			'Email'       => $username,
			'Passwd'      => $password,
			'service'     => 'cp', // cp = Contact List
			'source'      => 'rsevents-google-contact-grabber',
			'accountType' => 'HOSTED_OR_GOOGLE',
		);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL,$login_url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS,$fields);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($curl, CURLOPT_USERAGENT, $useragent);
		$result = curl_exec($curl);

		if (empty($result)) return 1;
		
		$lines = explode("\n",$result);
		
		if (!empty($lines))
			foreach ($lines as $line) {
				$line = trim($line);
				if(!$line) continue;
				list($k,$v) = explode('=',$line,2);
				$returns[$k] = $v;
			}
		curl_close($curl);

		$feed_url = "http://www.google.com/m8/feeds/contacts/$username/full?alt=json&max-results=250";
		if (empty($returns['Auth'])) return 2;
		$header = array( 'Authorization: GoogleLogin auth=' . $returns['Auth'] );

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $feed_url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($curl, CURLOPT_USERAGENT, $useragent);

		$result = curl_exec($curl);
		curl_close($curl);
		$data = json_decode($result);
		if (empty($data)) return 3;
		
		foreach ($data->feed->entry as $entry) {			
			$name = $entry->title->{'$t'};
			$email = isset($entry->{'gd$email'}) ? $entry->{'gd$email'}[0]->address : '';
			$contacts[$name] = $email;
		}
		
		return $contacts;
	}
	
	/*
	*	Return the list of contacts
	*
	*/
	
	public function results($username,$password) {
		$result = '';
		$contacts = RSGoogle::getContacts($username,$password);
		
		if (empty($contacts) || $contacts == 0 || $contacts == 1 || $contacts == 2 || $contacts == 3) 
			return;
		
		foreach ($contacts as $name => $email) {
			$email = trim($email);
			if (!empty($email))
				$result .= $email."\n";
		}
		
		return rtrim($result,"\n");
	}
	
	public function useragents() {
		$useragents = array('Mozilla/5.0 (Windows NT 6.1; rv:13.0) Gecko/20100101 Firefox/13.0.1',
							'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0.1',
							'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/534.57.2 (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2',
							'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.57.2 (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2',
							'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)',
							'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)'
							);
		
		$count = count($useragents) - 1;
		return $useragents[rand(0,$count)];
	}
}