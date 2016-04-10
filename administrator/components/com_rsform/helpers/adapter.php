<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

if (version_compare(JVERSION, '3.0', '>=')) {
	require_once dirname(__FILE__).'/adapters/input.php';
	
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
	
	// JModelLegacy
	if (!class_exists('JModelLegacy')) {
		class JModelLegacy extends JModel
		{
			public static function addIncludePath($path = '', $prefix = '') {
				return parent::addIncludePath($path, $prefix);
			}
		}
	}
	
	// JViewLegacy
	if (!class_exists('JViewLegacy')) {
		class JViewLegacy extends JView {}
	}
	
	// JControllerLegacy
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
				$iterator = new DirectoryIterator(JPATH_SITE . '/libraries/joomla/http/transport');
				foreach ($iterator as $file)
				{
					$fileName = $file->getFilename();

					// Only load for php files.
					// Note: DirectoryIterator::getExtension only available PHP >= 5.3.6
					if ($file->isFile() && substr($fileName, strrpos($fileName, '.') + 1) == 'php')
					{
						$names[] = substr($fileName, 0, strrpos($fileName, '.'));
					}
				}

				return $names;
			}
		}
	}
	
} elseif (version_compare(JVERSION, '1.5.0', '>=')) {
	// Joomla! 1.5
	
}