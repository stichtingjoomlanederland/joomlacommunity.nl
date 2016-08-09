<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallModelRSFirewall extends JModelLegacy
{
	const DS = DIRECTORY_SEPARATOR;
	protected $config;
	protected $isJ30 = null;
	
	public function __construct() {
		parent::__construct();
		
		$jversion 	  = new JVersion();
		
		$this->config = RSFirewallConfig::getInstance();
		$this->isJ30  = $jversion->isCompatible('3.0');
	}
	public function getButtons() {
		JFactory::getLanguage()->load('com_rsfirewall.sys', JPATH_ADMINISTRATOR);
		
		$buttons = array(
			array(
				'link' => JRoute::_('index.php?option=com_rsfirewall&view=check'),
				'image' => $this->isJ30 ? 'checkmark' : 'com_rsfirewall/icon-48-check.png',
				'text' => JText::_('COM_RSFIREWALL_SYSTEM_CHECK'),
				'access' => array('check.run', 'com_rsfirewall')
			),
			array(
				'link' => JRoute::_('index.php?option=com_rsfirewall&view=dbcheck'),
				'image' => $this->isJ30 ? 'database' : 'com_rsfirewall/icon-48-dbcheck.png',
				'text' => JText::_('COM_RSFIREWALL_DATABASE_CHECK'),
				'access' => array('dbcheck.run', 'com_rsfirewall')
			),
			array(
				'link' => JRoute::_('index.php?option=com_rsfirewall&view=logs'),
				'image' => $this->isJ30 ? 'bars' : 'com_rsfirewall/icon-48-logs.png',
				'text' => JText::_('COM_RSFIREWALL_SYSTEM_LOGS'),
				'access' => array('logs.view', 'com_rsfirewall')
			),
			array(
				'link' => JRoute::_('index.php?option=com_rsfirewall&view=configuration'),
				'image' => $this->isJ30 ? 'cog' : 'com_rsfirewall/icon-48-configuration.png',
				'text' => JText::_('COM_RSFIREWALL_FIREWALL_CONFIGURATION'),
				'access' => array('core.admin', 'com_rsfirewall')
			),
			array(
				'link' => JRoute::_('index.php?option=com_rsfirewall&view=lists'),
				'image' => $this->isJ30 ? 'drawer' : 'com_rsfirewall/icon-48-lists.png',
				'text' => JText::_('COM_RSFIREWALL_LISTS'),
				'access' => array('lists.manage', 'com_rsfirewall')
			),
			array(
				'link' => JRoute::_('index.php?option=com_rsfirewall&view=exceptions'),
				'image' => $this->isJ30 ? 'checkbox' : 'com_rsfirewall/icon-48-exceptions.png',
				'text' => JText::_('COM_RSFIREWALL_EXCEPTIONS'),
				'access' => array('exceptions.manage', 'com_rsfirewall')
			),
			array(
				'link' => JRoute::_('index.php?option=com_rsfirewall&view=feeds'),
				'image' => $this->isJ30 ? 'feed' : 'com_rsfirewall/icon-48-feeds.png',
				'text' => JText::_('COM_RSFIREWALL_RSS_FEEDS_CONFIGURATION'),
				'access' => array('feeds.manage', 'com_rsfirewall')
			),
			array(
				'link' => JRoute::_('index.php?option=com_rsfirewall&view=updates'),
				'image' => $this->isJ30 ? 'download' : 'com_rsfirewall/icon-48-updates.png',
				'text' => JText::_('COM_RSFIREWALL_UPDATES'),
				'access' => array('updates.view', 'com_rsfirewall')
			)
		);
		
		return $buttons;
	}
	
	public function getLastMonthLogs() {       
        $db     = $this->getDbo();
		$query  = $db->getQuery(true);
		
        // get the date format
        $format = $db->getDateFormat();
        // get the date class
        $date   = JFactory::getDate();
        $now    = JFactory::getDate();
       
        $date->modify('-30 days');
       
        $query->select("COUNT('id') AS num, YEAR(date) AS y, LPAD(MONTH(date), 2, '0') AS m, LPAD(DAY(date), 2, '0') AS d, level")
			  ->from('#__rsfirewall_logs')
			  ->where($db->qn('date').' > '.$db->q($date->format($format)))
			  ->group('level, YEAR(date), MONTH(date), DAY(date)');
        $db->setQuery($query);
        $results = $db->loadObjectList();
       
        $nowformat = $now->format('d.m.Y');
        $dates = array();
        while ($nowformat != $date->format('d.m.Y')) {
            $format = $date->format('Y, m-1, d');
            $dates[$format] = array(
                'low'       => 0,
                'medium'	=> 0,
                'high'      => 0,
                'critical'  => 0
            );
            $date->modify('+1 day');
        }
        // add the current day as well
        $format = $date->format('Y, m-1, d');
        $dates[$format] = array(
            'low'        => 0,
            'medium'     => 0,
            'high'       => 0,
            'critical'   => 0
        );
	   
        foreach ($results as $result) {
            $y = $result->y;
            $m = $result->m.'-1';
            $d = $result->d;
           
            $format = "$y, $m, $d";
           
            if (!isset($dates[$format])) {
                $dates[$format] = array(
                    'low'       => 0,
                    'medium'    => 0,
                    'high'      => 0,
                    'critical'  => 0
                );
            }
           
            $dates[$format][$result->level] = $result->num;
        }
       
        return $dates;
    }
	
	public function getLogOverviewNum() {
		return $this->config->get('log_overview');
	}
	
	public function getLastLogs() {
		$db 	= $this->getDbo();
		$query 	= $db->getQuery(true);		
		$query->select('*')
			  ->from('#__rsfirewall_logs')
			  ->order($db->qn('date').' DESC');
		
		$db->setQuery($query, 0, $this->getLogOverviewNum());
		return $db->loadObjectList();
	}
	
	public function getCode() {
		return $this->config->get('code');
	}
	
	public function getFeeds() {
		$feeds  = array();
		$db 	= $this->getDbo();
		$query 	= $db->getQuery(true);		
		$query->select('*')
			  ->from('#__rsfirewall_feeds')
			  ->where($db->qn('published').'='.$db->q(1))
			  ->order($db->qn('ordering').' ASC');
		$db->setQuery($query);
		$items = $db->loadObjectList();
		foreach ($items as $item) {
			if ($feed = $this->getParsedFeed($item)) {
				$feeds[] = $feed;
			}
		}
		
		return $feeds;
	}
	
	protected function getParsedFeed($item) {
		$parsedFeed = new stdClass;
		$url	 	= $item->url;
		
		if ($this->isJ30) {
			// 3.x
			$parser = new JFeedFactory;
			try {
				$feed = $parser->getFeed($url);
			} catch (Exception $e) {
				JError::raiseError($e->getCode(), $e->getMessage());
				return false;
			}
			
			$parsedFeed->title = $feed->title;
			$parsedFeed->items = array();
			
			if (!empty($feed[0])) {
				for ($i = 0; $i < $item->limit; $i++) {
					if (!empty($feed[$i])) {
						$uri = !empty($feed[$i]->guid) || !is_null($feed[$i]->guid) ? $feed[$i]->guid : $feed[$i]->uri;
						$uri = substr($uri, 0, 4) != 'http' ? '' : $uri;
						
						$parsedFeed->items[] = (object) array(
							'title' => $feed[$i]->title,
							'date' 	=> $feed[$i]->updatedDate,
							'link'	=> $uri
						);
					}
				}				
				
			}
			
			return $parsedFeed;
		} else {
			// 2.5
			
			// SimplePie throws Strict Standards, so a workaround is in place.
			$errorReporting = error_reporting(0);
			
			$parser = JFactory::getFeedParser($url);
			
			$parsedFeed->title = $parser->get_title();
			$parsedFeed->items = array();
			
			foreach ($parser->get_items() as $count => $feed) {
				if ($count >= $item->limit) {
					break;
				}
				$parsedFeed->items[] = (object) array(
					'title' => $feed->get_title(),
					'date' 	=> $feed->get_date(),
					'link' 	=> $feed->get_link()
				);
			}
			
			// Revert error reporting
			error_reporting($errorReporting);
			
			return $parsedFeed;
		}
		
		return false;
	}
	
	public function getModifiedFiles() {
		$db 		= $this->getDbo();
		$query 		= $db->getQuery(true);		
		$jversion 	= new JVersion();
		
		$query->select('*')
			  ->from('#__rsfirewall_hashes')
			  ->where('('.$db->qn('type').'='.$db->q('protect').' OR '.$db->qn('type').'='.$db->q($jversion->getShortVersion()).')')
			  ->where($db->qn('flag').'!='.$db->q(''));
		$db->setQuery($query);
		$files = $db->loadObjectList();
		foreach ($files as $i => $file) {
			$file->error = false;
			$file->path  = $file->type == 'protect' ? $file->file : JPATH_SITE.self::DS.$file->file;
			
			if (!is_file($file->path)) {
				$file->modified_hash = JText::_('COM_RSFIREWALL_FILE_IS_MISSING');
				$file->error = true;
			} elseif (!is_readable($file->path)) {
				$file->modified_hash = JText::sprintf('COM_RSFIREWALL_COULD_NOT_READ_FILE', $file->file);
				$file->error = true;
			} else {
				$file->modified_hash = md5_file($file->path);
			}
			
			if ($file->modified_hash === $file->hash) {
				unset($files[$i]);
				
				$query->clear()
					  ->update($db->qn('#__rsfirewall_hashes'))
					  ->set($db->qn('flag').'='.$db->q(''))
					  ->where($db->qn('id').'='.$db->q($file->id));
				
				$db->setQuery($query)->execute();
			}
		}
		
		return $files;
	}
	
	public function acceptModifiedFiles($cids) {
		$files = $this->getModifiedFiles();
		
		foreach ($files as $file) {
			if (!$file->error) {
				$table = JTable::getInstance('Hashes', 'RSFirewallTable');
				$table->bind(array(
					'id' => $file->id,
					'hash' => $file->modified_hash,
					'flag' => ''
				));
				$table->store();
			}
		}
		
		return true;
	}

	public function getCountryBlocking()
	{
		$countries = $this->config->get('blocked_countries');
		if (!empty($countries))
		{
			return true;
		}
		return false;
	}

	public function getGeoIpStatus(){
		require_once JPATH_ADMINISTRATOR . '/components/com_rsfirewall/models/configuration.php';
		$model = new RSFirewallModelConfiguration();

		if($model->getIsGeoIPUploaded() || $model->isGeoIPNative()){
			return true;
		}

		return false;
	}

	public function isPluginEnabled() {
		return JPluginHelper::isEnabled('system', 'rsfirewall');
	}
	
	public function getIsJ30() {
		$jversion = new JVersion();
		return $jversion->isCompatible('3.0');
	}
	
	public function getSideBar() {
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		
		return RSFirewallToolbarHelper::render();
	}
}