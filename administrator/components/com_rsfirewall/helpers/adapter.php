<?php
/**
* @version 1.0.0
* @package RSJoomla! Adapter
* @copyright (C) 2012 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

if (version_compare(JVERSION, '3.0', '>=')) {
	require_once dirname(__FILE__).'/adapters/input.php';
	
	// Joomla! 3.0
	if (!class_exists('JPane')) {
		class JPane {
			function getInstance() {
				return new JPane();
			}
			
			function startPane($id) {
				return JHtml::_('tabs.start', $id, array('useCookie' => 1));
			}
			
			function startPanel($text, $id) {
				return JHtml::_('tabs.panel', $text, $id);
			}
			
			function endPanel() {
				return '';
			}
			
			function endPane() {
				return JHtml::_('tabs.end');
			}
		}
	}
} elseif (version_compare(JVERSION, '2.5.0', '>=')) {
	require_once dirname(__FILE__).'/adapters/input.php';
	
	// Joomla! 2.5
	jimport('joomla.application.component.model');
	jimport('joomla.application.component.modelform');
	jimport('joomla.application.component.modellist');
	jimport('joomla.application.component.modeladmin');
	jimport('joomla.application.component.modelitem');
	jimport('joomla.application.component.view');
	jimport('joomla.application.component.controller');
	jimport('joomla.application.component.controlleradmin');
	jimport('joomla.application.component.controllerform');
	jimport('joomla.html.editor');
	jimport('joomla.http.http');
	
	if (!class_exists('JModelLegacy')) {
		class JModelLegacy extends JModel
		{
			public static function addIncludePath($path = '', $prefix = '') {
				return parent::addIncludePath($path, $prefix);
			}
		}
	}
	
	if (!class_exists('JViewLegacy')) {
		class JViewLegacy extends JView {}
	}
	
	if (!class_exists('JControllerLegacy')) {
		class JControllerLegacy extends JController {}
	}
	
	if (!class_exists('JSimplepieFactory')) {
		class JSimplepieFactory
		{
			public static function getFeedParser($url) {
				return JFactory::getFeedParser($url);
			}
		}
	}
	
	if (!class_exists('JHttpFactory')) {
		class JHttpFactory
		{
			public static function getHttp() {
				$options = new JRegistry;
				return new JHttp($options, self::getAvailableDriver($options));
			}
			
			public static function getAvailableDriver($options) {
				$availableAdapters = self::getHttpTransports();
				// Check if there is available http transport adapters
				if (!count($availableAdapters))
				{
					return false;
				}
				foreach ($availableAdapters as $adapter)
				{
					$class = 'JHttpTransport' . ucfirst($adapter);
					try {
						if ($object = new $class($options)) {
							return $object;
						}
					}
					catch (RuntimeException $e) {
						
					}
				}
				return false;
			}
			
			public static function getHttpTransports() {
				$names = array();
				jimport('joomla.filesystem.folder');
				$transports = JFolder::files(JPATH_SITE . '/libraries/joomla/http/transport', '\.php', false, false);
				asort($transports);
				foreach ($transports as $fileName) {
					$names[] = substr($fileName, 0, strrpos($fileName, '.'));
				}

				return $names;
			}
		}
	}
	
} elseif (version_compare(JVERSION, '1.5.0', '>=')) {
	// Joomla! 1.5
	
}