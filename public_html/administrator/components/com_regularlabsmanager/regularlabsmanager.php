<?php
/**
 * @package         Regular Labs Extension Manager
 * @version         7.4.3
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2019 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Access\Exception\NotAllowed as JAccessExceptionNotallowed;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Installer\Installer as JInstaller;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\MVC\Controller\BaseController as JController;
use Joomla\CMS\Plugin\PluginHelper as JPluginHelper;
use RegularLabs\Library\Language as RL_Language;

// Access check.
if ( ! JFactory::getUser()->authorise('core.manage', 'com_regularlabsmanager'))
{
	throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

$helper = new RegularLabsManagerHelper;

if ( ! $helper->isFrameworkEnabled())
{
	return false;
}

if (version_compare(PHP_VERSION, '5.3', '<'))
{
	$helper->throwError(JText::sprintf('RLEM_NOT_COMPATIBLE_PHP', PHP_VERSION, '5.3'));

	return false;
}

require_once JPATH_LIBRARIES . '/regularlabs/autoload.php';

RL_Language::load('plg_system_regularlabs', JPATH_ADMINISTRATOR);
RL_Language::load('com_modules', JPATH_ADMINISTRATOR);

$helper->uninstallNoNumberExtensionManager();

JController::getInstance('RegularLabsManager')
	->execute(JFactory::getApplication()->input->get('task'))
	->redirect();

class RegularLabsManagerHelper
{
	private $_title       = 'COM_REGULARLABSMANAGER';
	private $_lang_prefix = 'RLEM';

	/**
	 * Check if the Regular Labs Library is enabled
	 *
	 * @return bool
	 */
	public function isFrameworkEnabled()
	{
		// Return false if Regular Labs Library is not installed
		if ( ! $this->isFrameworkInstalled())
		{
			return false;
		}

		if ( ! JPluginHelper::isEnabled('system', 'regularlabs'))
		{
			$this->throwError(
				JText::_($this->_lang_prefix . '_REGULAR_LABS_LIBRARY_NOT_ENABLED')
				. ' ' . JText::sprintf($this->_lang_prefix . '_EXTENSION_CAN_NOT_FUNCTION', JText::_($this->_title))
			);

			return false;
		}

		return true;
	}

	/**
	 * Check if the Regular Labs Library is installed
	 *
	 * @return bool
	 */
	public function isFrameworkInstalled()
	{
		jimport('joomla.filesystem.file');

		if (
			! is_file(JPATH_PLUGINS . '/system/regularlabs/regularlabs.xml')
			|| ! is_file(JPATH_LIBRARIES . '/regularlabs/autoload.php')
		)
		{
			$this->throwError(
				JText::_($this->_lang_prefix . '_REGULAR_LABS_LIBRARY_NOT_INSTALLED')
				. ' ' . JText::sprintf($this->_lang_prefix . '_EXTENSION_CAN_NOT_FUNCTION', JText::_($this->_title))
			);

			return false;
		}

		return true;
	}

	/**
	 * Place an error in the message queue
	 */
	public function throwError($text)
	{
		JFactory::getApplication()->enqueueMessage($text, 'error');
	}

	public function uninstallNoNumberExtensionManager()
	{
		jimport('joomla.filesystem.folder');

		// Check if old NoNumber Extension Manager is still installed
		if ( ! JFolder::exists(JPATH_ADMINISTRATOR . '/components/com_nonumbermanager'))
		{
			return;
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('extension_id')
			->from('#__extensions')
			->where($db->quoteName('element') . ' = ' . $db->quote('com_nonumbermanager'));

		$db->setQuery($query);
		$id = $db->loadResult();

		if (empty($id))
		{
			return;
		}

		$installer = new JInstaller;
		$installer->uninstall('component', $id);
	}
}
