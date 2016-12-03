<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RSJAkismet {    

	protected $api_version = '1.1';
	protected $connection;
	protected $urls = array();
	public $api_key;
	public $site_url;
		
	public function __construct($api_key = null, $site_url = null) {
		if ($api_key) {
			$this->api_key = $api_key;
		} else {
			throw new Exception('Akismet API Key not set.');
		}

		if (!$site_url) {
			$this->site_url = 'http://'. $_SERVER['SERVER_NAME'];
		} else {
			$this->site_url = $site_url;
		}

		$this->urls = array(
			'verify'		=> 'rest.akismet.com/'.$this->api_version.'/verify-key',
			'check_spam'	=> $this->api_key.'.rest.akismet.com/'.$this->api_version.'/comment-check',
			'submit_spam'	=> $this->api_key.'.rest.akismet.com/'.$this->api_version.'/submit-spam',
			'submit_ham' 	=> $this->api_key.'.rest.akismet.com/'.$this->api_version.'/submit-ham'
		);

		$this->connect();
	}

	// Make the connection
    protected function connect() {
		if (!is_resource($this->connection)) {
			if (!$this->connection = curl_init()) {
				throw new Exception('Could not start new CURL instance');
			}
		}
        
		curl_setopt($this->connection, CURLOPT_HEADER, 0);
		curl_setopt($this->connection, CURLOPT_POST, 1);
		curl_setopt($this->connection, CURLOPT_TIMEOUT, 6); 
		curl_setopt($this->connection, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->connection, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0.1');
		curl_setopt($this->connection, CURLOPT_FRESH_CONNECT, 1);

		return true;
    }

	// Close the connection
    public function close() {
		if(is_resource($this->connection)) {
			if(!curl_close($this->connection)) {
				throw new Exception('Could not close the CURL instance');
			}
		}
		
		return true;
    }

	// Send the request
	protected function send($request=null, $url=null) {
		curl_setopt($this->connection, CURLOPT_URL, $url);
		curl_setopt($this->connection, CURLOPT_POSTFIELDS, $request);

		if (!$response = curl_exec($this->connection)) {
			throw new Exception('Could not send cURL request');
		}
		
		return $response;
	}

	// Check if the Akismet API key is valid
    public function valid_key() {
        $data	= array('key' => $this->api_key, 'blog' => $this->site_url);
		$string = $this->create_query_string($data);
		
        return ($this->send($string, $this->urls['verify']) == 'valid');
    }

	// Check if the comment is marked as spam
	public function is_spam($comment) {
		$comment['blog'] = $this->site_url;

		$comment  = $this->create_query_string($comment);
		$comment .= $this->create_server_string();

		$response = $this->send($comment, $this->urls['check_spam']);
		return ($response == 'true');
	}

	// Submit a comment that Akismet missed as SPAM!
	public function submit_spam($comment, $server_data=null) {
		$comment['blog'] = $this->site_url;
		$comment = $this->create_query_string($comment);

		if ($server_data) {
			$comment .= $this->create_query_string($server_data);
		}

		$this->send($comment, $this->urls['submit_spam']);
	}

	// Let Akismet know that the comment was valid and NOT spam.
	public function submit_ham($comment, $server_data=null) {
		$comment['blog'] = $this->site_url;
		$comment = $this->create_query_string($comment);
		
		if ($server_data) {
			$comment .= $this->create_query_string($server_data);
		}
		
		$this->send($comment, $this->urls['submit_ham']);
	}
	
	protected function create_query_string($array=null) {
		$query_string = '';

		if (is_array($array)) {
			foreach($array as $key => $value) {
				$query_string .= $key. '='. urlencode($value). '&';
			}
		}
		
		return $query_string;
	}

	
    protected function create_server_string() {
        $array = array(
            'SERVER_PROTOCOL'	=> isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : '',
            'REQUEST_METHOD'	=> isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '',
            'QUERY_STRING'		=> isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '',
            'HTTP_REFERER'		=> isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
            'REMOTE_PORT'		=> isset($_SERVER['REMOTE_PORT']) ? $_SERVER['REMOTE_PORT'] : '',
            'HTTP_ACCEPT'		=> isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '',
            'user_agent'		=> isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
            'referrer'			=> isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
            'user_ip'			=> ($_SERVER['REMOTE_ADDR'] != getenv('SERVER_ADDR')) ? $_SERVER['REMOTE_ADDR'] : getenv('HTTP_X_FORWARDED_FOR'),
            'blog'				=> $this->site_url
		);
        
        return $this->create_query_string($array);
    }
}