<?php
/**
 * @package LiveUpdate
 * @copyright Copyright ©2011-2013 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

/**
 * Configuration class for your extension's updates. Override to your liking.
 */
class LiveUpdateConfig extends LiveUpdateAbstractConfig
{
	var $_extensionName			= 'com_aclmanager';
	var $_extensionTitle		= 'ACL Manager';
	var $_updateURL				= 'http://www.aclmanager.net/index.php?option=com_ars&view=update&format=ini&id=1';
	var $_requiresAuthorization	= true;
	var $_versionStrategy		= 'different';
	var $_storageAdapter		= 'component';
	var $_storageConfig			= array(
		'extensionName'	=> 'com_aclmanager',
		'key'			=> 'liveupdate'
	);

	public function __construct() 
	{
		JLoader::import('joomla.filesystem.file');
		JLoader::import('joomla.html.parameter');
		JLoader::import('joomla.application.component.helper');
		$db = JFactory::getDbo();
		$sql = $db->getQuery(true)
			->select($db->quoteName('params'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('type').' = '.$db->quote('component'))
			->where($db->quoteName('element').' = '.$db->quote('com_aclmanager'));
		$db->setQuery($sql);
		$rawparams = $db->loadResult();
		$params = new JRegistry();
		if(version_compare(JVERSION, '3.0', 'ge')) {
			$params->loadString($rawparams, 'JSON');
		} else {
			$params->loadJSON($rawparams);
		}

		// Should I use our private CA store?
		if(@file_exists(dirname(__FILE__).'/../assets/cacert.pem')) {
			$this->_cacerts = dirname(__FILE__).'/../assets/cacert.pem';
		}

		// Load js & css
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::root(true).'/administrator/components/com_aclmanager/assets/css/aclmanager.css');

		parent::__construct();
	}

	public function addMedia()
	{
		return true;
	}
}