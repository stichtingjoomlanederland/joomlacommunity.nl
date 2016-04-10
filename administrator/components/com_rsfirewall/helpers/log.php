<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallLogger {
	protected $table;
	protected $root;
	protected $config;
	protected $emails;
	protected $mailfrom;
	protected $fromname;
	protected $bound = false;
	
	public function __construct() {
		$config = JFactory::getConfig();
		
		$this->table = JTable::getInstance('Logs', 'RSFirewallTable');
		$this->table->bind(array(
			'date' 		=> JFactory::getDate()->toSql(),
			'ip' 		=> $this->getIP(),
			'user_id' 	=> JFactory::getUser()->get('id'),
			'username' 	=> JFactory::getUser()->get('username'),
			'page'		=> JURI::getInstance()->toString(),
			'referer' 	=> isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''
		));
		
		$this->root 	= JURI::root();
		$this->config	= RSFirewallConfig::getInstance();
		$this->emails	= $this->config->get('log_emails', array(), true);
		$this->mailfrom = $config->get('mailfrom');
		$this->fromname = $config->get('fromname');
	}
	
	public static function getInstance() {
		static $initialized = false;
		if (!$initialized) {
			// load language
			$lang = JFactory::getLanguage();
			$lang->load('com_rsfirewall', JPATH_ADMINISTRATOR);
			// set table path
			JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_rsfirewall/tables');
			// load config class if not already loaded
			if (!class_exists('RSFirewallConfig')) {
				require_once JPATH_ADMINISTRATOR.'/components/com_rsfirewall/helpers/config.php';
			}
			
			// don't call this again
			$initialized = true;
		}
		// always create a new instance to allow subsequent calls to grab the correct details
		$inst = new RSFirewallLogger();
		return $inst;
	}
	
	protected function getIP() {
		require_once dirname(__FILE__).'/ip/ip.php';
		
		return RSFirewallIP::get();
	}
	
	public function add($level='low', $code=1, $debug_variables=null) {
		$this->table->bind(array(
			'level' => $level,
			'code' => $code,
			'debug_variables' => $debug_variables
		));
		
		$this->bound = true;
		
		return $this;
	}
	
	protected function escape($string) {
		return htmlentities($string, ENT_COMPAT, 'utf-8');
	}
	
	protected function showDate($date) {
		return JHTML::_('date', $date, 'Y-m-d H:i:s');
	}
	
	public function save() {
		// save to db
		if ($this->bound) {
			$this->bound = false;
			
			$this->table->store();
			// if this level is higher or equal to the configured minimum level
			if (in_array($this->table->level, $this->config->get('log_alert_level'))) {
				// send the email alert
				$this->sendAlert();
			}
		}
	}
	
	protected function sendAlert() {
		$subject = JText::sprintf('COM_RSFIREWALL_LOG_EMAIL_SUBJECT', JText::_('COM_RSFIREWALL_LEVEL_'.$this->table->level), $this->escape($this->root));
		
		$body =  '<html>'."\n"
				.'<body>'."\n"
				.'<p><strong>'.JText::_('COM_RSFIREWALL_WEBSITE').':</strong> <a href="'.$this->escape($this->root).'">'.$this->escape($this->root).'</a></p>'."\n"
				.'<p><strong>'.JText::_('COM_RSFIREWALL_LOG_PAGE').':</strong> '.$this->escape($this->table->page).'</p>'."\n"
				.'<p><strong>'.JText::_('COM_RSFIREWALL_LOG_REFERER').':</strong> '.($this->table->referer ? $this->escape($this->table->referer) : '<em>'.JText::_('COM_RSFIREWALL_NO_REFERER').'</em>').'</p>'."\n"
				.'<p><strong>'.JText::_('COM_RSFIREWALL_LOG_DESCRIPTION').':</strong> '.JText::_('COM_RSFIREWALL_EVENT_'.$this->table->code).'</p>'."\n"
				.'<p><strong>'.JText::_('COM_RSFIREWALL_LOG_DEBUG_VARIABLES').':</strong> '.nl2br($this->escape($this->table->debug_variables)).'</p>'."\n"
				.'<p><strong>'.JText::_('COM_RSFIREWALL_ALERT_LEVEL').':</strong> '.JText::_('COM_RSFIREWALL_LEVEL_'.$this->table->level).'</p>'."\n"
				.'<p><strong>'.JText::_('COM_RSFIREWALL_LOG_DATE_EVENT').':</strong> '.$this->showDate($this->table->date).'</p>'."\n"
				.'<p><strong>'.JText::_('COM_RSFIREWALL_LOG_IP_ADDRESS').':</strong> '.$this->escape($this->table->ip).'</p>'."\n"
				.'<p><strong>'.JText::_('COM_RSFIREWALL_LOG_USER_ID').':</strong> '.$this->escape($this->table->user_id).'</p>'."\n"
				.'<p><strong>'.JText::_('COM_RSFIREWALL_LOG_USERNAME').':</strong> '.$this->escape($this->table->username).'</p>'."\n"
				.'<small>'.JText::_('COM_RSFIREWALL_EMAIL_NOTICE').'</small>'."\n"
				.'</body>'."\n"
				.'</html>';
				
		// sent so far
		$sent = (int) $this->config->get('log_emails_count');
		// limit per hour
		$limit = $this->config->get('log_hour_limit');
		// after the hour we're allowed to send
		$after = $this->config->get('log_emails_send_after');
		// the start of the current hour
		$start = gmmktime(gmdate('H'), 0, 0, gmdate('n'), gmdate('j'), gmdate('Y'));
		// now
		$now = @gmmktime();
		
		// are we allowed to send?
		if ($now > $after) {
			// do we have emails set?
			if ($this->emails) {
				// loop through emails and attempt sending
				foreach ($this->emails as $email) {
					$email = trim($email);
					if (JMailHelper::isEmailAddress($email) && $sent < $limit) {
						$mailer = JFactory::getMailer();
						$mailer->sendMail($this->mailfrom, $this->fromname, $email, $subject, $body, true);
						// increment number of sent emails
						$sent++;
					}
				}
				
				// reached the limit?
				if ($sent >= $limit) {
					// allow to send in the next hour
					$next_after = gmmktime(gmdate('H')+1, 0, 0, gmdate('n'), gmdate('j'), gmdate('Y'));
					$this->config->set('log_emails_send_after', $next_after);
					$this->config->set('log_emails_count', 0);
				} else {
					$this->config->set('log_emails_count', $sent);
				}
			}
		}
	}
}