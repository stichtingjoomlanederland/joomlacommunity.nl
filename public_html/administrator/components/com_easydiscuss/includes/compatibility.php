<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Users\Site\Model\RegistrationModel;
use Joomla\Component\Users\Site\Model\ProfileModel;
use Joomla\Component\Content\Site\Helper\RouteHelper;

if (!defined('ED_CLI')) {
	if (!ED::isJoomla4()) {
		require_once(JPATH_ROOT . '/components/com_content/helpers/route.php');
	}
}

if (!ED::isJoomla4()) {
	class EDStringBase extends JString
	{
	}
} 

if (ED::isJoomla4()) {
	class EDStringBase extends Joomla\String\StringHelper
	{
	}
}

class EDJString extends EDStringBase
{
}

class EDCompat
{
	public static function getTwoFactorForms($otpConfig, $userId = null)
	{
		if (ED::isJoomla4()) {
			$app = JFactory::getApplication();
			$model = $app->bootComponent('com_users')->getMVCFactory()->createModel('User', 'Administrator');
			$otpConfig = $model->getOtpConfig($userId);

			PluginHelper::importPlugin('twofactorauth');

			return $app->triggerEvent('onUserTwofactorShowConfiguration', array($otpConfig, $userId));
		}

		FOFPlatform::getInstance()->importPlugin('twofactorauth');

		$userId = JFactory::getUser($userId)->id;

		$contents = FOFPlatform::getInstance()->runPlugins('onUserTwofactorShowConfiguration', array($otpConfig, $userId));

		return $contents;
	}

	public static function getTwoFactorConfig($twoFactorMethod)
	{
		if (ED::isJoomla4()) {
			PluginHelper::importPlugin('twofactorauth');
			$otpConfigReplies = JFactory::getApplication()->triggerEvent('onUserTwofactorApplyConfiguration', array($twoFactorMethod));

			return $otpConfigReplies;
		}

		FOFPlatform::getInstance()->importPlugin('twofactorauth');
		$otpConfigReplies = FOFPlatform::getInstance()->runPlugins('onUserTwofactorApplyConfiguration', array($twoFactorMethod));

		return $otpConfigReplies;
	}
	
	/**
	 * Render Joomla editor since J4 and J3 does it differently
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public static function getEditor($editorType = null)
	{
		if (!$editorType) {
			$jconfig = ED::jconfig();

			$editorType = $jconfig->get('editor');
		}

		if (ED::isJoomla4()) {
			$editor = Joomla\CMS\Editor\Editor::getInstance($editorType);

			return $editor;
		}

		$editor = JFactory::getEditor($editorType);

		if ($editorType == 'none') {
			JHtml::_('behavior.core');
		}

		return $editor;
	}

	/**
	 * Determines if this is from the Joomla backend
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function isFromAdmin()
	{
		$app = JFactory::getApplication();

		if (ED::isJoomla4()) {
			$admin = $app->isClient('administrator');

			return $admin;
		}

		$admin = $app->isAdmin();

		return $admin;
	}

	/**
	 * Load JQuery from Joomla
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function renderJQueryFramework()
	{
		if (ED::isJoomla4()) {
			HTMLHelper::_('jquery.framework');

			return;
		}

		JHTML::_('jquery.framework');
	}

	/**
	 * Renders color picker library from Joomla
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function renderColorPicker()
	{
		if (ED::isJoomla4()) {
			HTMLHelper::_('jquery.framework');
			HTMLHelper::_('script', 'vendor/minicolors/jquery.minicolors.min.js', array('version' => 'auto', 'relative' => true));
			HTMLHelper::_('stylesheet', 'vendor/minicolors/jquery.minicolors.css', array('version' => 'auto', 'relative' => true));
			HTMLHelper::_('script', 'system/fields/color-field-adv-init.min.js', array('version' => 'auto', 'relative' => true));
			return;
		}

		JHTML::_('behavior.colorpicker');
	}

	/**
	 * Renders modal library from Joomla
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function renderModalLibrary()
	{
		if (ED::isJoomla4()) {
			HTMLHelper::_('bootstrap.framework');
			return;
		}

		JHTML::_('behavior.modal');
	}
}

class EDApplicationHelper
{
	/**
	 * Load up ApplicationHelper
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public static function load()
	{
		if (ED::isJoomla4()) {
			$app = new Joomla\CMS\Application\ApplicationHelper;

			return $app;
		}

		$app = new JApplicationHelper();

		return $app;
	}

	/**
	 * Provides a secure hash based on a seed
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public static function getHash($seed)
	{
		$app = self::load();

		return $app::getHash($seed);
	}
}

class EDJLanguage
{
	/**
	 * Retrieves a list of known languages
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public static function getKnownLanguages()
	{
		if (ED::isJoomla4()) {
			$language = LanguageHelper::getKnownLanguages();

			return $language;
		}

		$language = JLanguage::getKnownLanguages();

		return $language;
	}
}

class EDUserModel
{
	/**
	 * Load joomla's user forms
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public static function load()
	{
		if (ED::isJoomla4()) {
			$model = new Joomla\Component\Users\Administrator\Model\UserModel();

			return $model;
		}
		
		require_once(JPATH_ADMINISTRATOR . '/components/com_users/models/user.php');   
		$model = new UsersModelUser();

		return $model;
	}
}

if (!defined('ED_CLI')) {

	if (ED::isJoomla4()) {
		class EDUserModelRegistrationBase extends RegistrationModel
		{
			public function __construct()
			{
				// load com_user model form from frontend.
				Form::addFormPath(JPATH_ROOT . '/components/com_users/forms');
			}
		}
	}

	if (!ED::isJoomla4()) {
		require_once(JPATH_ROOT . '/components/com_users/models/registration.php');

		class EDUserModelRegistrationBase extends UsersModelRegistration
		{
		}
	}

	class EDUsersModelRegistration extends EDUserModelRegistrationBase
	{
		/**
		 * Get user custom fields if available
		 *
		 * @since	5.0.0
		 * @access	public
		 */
		public function getForm($data = array(), $loadData = true)
		{
			if (!$this->isEnabled()) {
				return false;
			}

			// add com_users forms and fields path
			JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/com_users/models/forms');
			JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_users//models/fields');
			JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/com_users//model/form');
			JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_users//model/field');

			$form = parent::getForm($data, $loadData);
			return $form;
		}

		/**
		 * Checks if custom fields supported or not.
		 *
		 * @since	5.0.0
		 * @access	public
		 */
		public function isEnabled()
		{
			JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

			// Only joomla 3.7.x and above have custom fields
			if (!class_exists('FieldsHelper')) {
				return false;
			}

			return true;
		}
	}

	if (ED::isJoomla4()) {
		class EDUsersModelProfileBase extends ProfileModel
		{
			public function __construct()
			{
				// load com_user model form from frontend.
				Form::addFormPath(JPATH_ROOT . '/components/com_users/forms');
			}
		}
	}
	if (!ED::isJoomla4()) {
		require_once(JPATH_SITE.'/components/com_users/models/profile.php');
		class EDUsersModelProfileBase extends UsersModelProfile {}
	}

	class EDUsersModelProfile extends EDUsersModelProfileBase {}

}

class EDArchive
{
	/**
	 * Load Joomla's Archive
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public static function load()
	{
		if (ED::isJoomla4()) {
			$archive = new Joomla\Archive\Archive();

			return $archive;
		} 

		$archive = new JArchive();

		return $archive;
	}

	/**
	 * Perform extract method from Joomla Archive
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function extract($destination, $extracted)
	{
		$archive = self::load();

		if (!ED::isJoomla4()) {
			$state = $archive::extract($destination, $extracted);

			return $state;
		} 

		$state = $archive->extract($destination, $extracted);
		
		return $state;
	}

	/**
	 * Get a file compression adapter from Joomla Archive
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function getAdapter($type)
	{
		$archive = self::load();

		if (!ED::isJoomla4()) {
			$adapter = $archive::getAdapter($type);

			return $adapter;
		} 

		$adapter = $archive->getAdapter($type);
		
		return $adapter;
	}
}

class EDArrayHelper
{
	/**
	 * Utility function to map an object to an array
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public static function fromObject($data)
	 {
		if (ED::isJoomla4()) {
			$data = Joomla\Utilities\ArrayHelper::fromObject($data);
			return $data;
		}


		$data = JArrayHelper::fromObject($data);
		return $data;
	 }

	/**
	 * Utility function to return a value from a named array or a specified default
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public static function getValue($array, $name, $default = null, $type = '')
	{
		if (ED::isJoomla4()) {
			$data = Joomla\Utilities\ArrayHelper::getValue($array, $name, $default, $type);
			return $data;
		}

		$data = JArrayHelper::getValue($array, $name, $default, $type);
		return $data;
	}

	/**
	 * Method to convert array to integer values
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public static function toInteger($array, $default = null)
	{
		if (ED::isJoomla4()) {
			$data = Joomla\Utilities\ArrayHelper::toInteger($array, $default);

			return $data;
		}

		$data = JArrayHelper::toInteger($array, $default);
		return $data;
	}

	/**
	 * Method to determine if an array is an associative array.
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public static function isAssociative($array)
	{
		if (ED::isJoomla4()) {
			$isAssociative = Joomla\Utilities\ArrayHelper::isAssociative($array);

			return $isAssociative;
		}

		$isAssociative = JArrayHelper::isAssociative($array);
		return $isAssociative;
	}
}

class EDDispatcher
{
	/**
	 * Load the Joomla Dispacther
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function load()
	{
		if (ED::isJoomla4()) {
			$dispatcher = new Joomla\Event\Dispatcher();

			return $dispatcher;
		}

		$dispatcher = JDispatcher::getInstance();

		return $dispatcher;
	}

	/**
	 * Triggers an event
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function trigger($eventName, $data = array())
	{
		$dispatcher = self::load();

		if (ED::isJoomla4()) {
			return $dispatcher->triggerEvent($eventName, $data);
		}

		return $dispatcher->trigger($eventName, $data);
	}
}

use Joomla\Component\Finder\Administrator\Indexer\Adapter;
use Joomla\Component\Finder\Administrator\Indexer\Helper;
use Joomla\Component\Finder\Administrator\Indexer\Indexer;
use Joomla\Component\Finder\Administrator\Indexer\Result;

if (ED::isJoomla4()) {
	class EDFinderBase extends Adapter{

		protected function index(Result $item)
		{
			$data = $this->proxyIndex($item);
			return $data;
		}

		protected function setup()
		{
			return parent::setup();
		}
	}
}

if (!ED::isJoomla4() && !defined('ED_CLI')) {
	require_once(JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php');

	class EDFinderBase extends FinderIndexerAdapter{

		protected function index(FinderIndexerResult $item, $format = 'html')
		{
			$data = $this->proxyIndex($item, $format);
			return $data;
		}

		protected function setup()
		{
			return parent::setup();
		}
	}

}

class EDFactory
{
	/**
	 * Returns a query variable by name.
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public static function getURI($requestPath = false)
	{
		$uri = JUri::getInstance();

		// Gets the full request path.
		if ($requestPath) {
			$uri = $uri->toString(array('path', 'query'));
		}

		return $uri;
	}

	/**
	 * Render Joomla editor.
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	// public static function getEditor($editorType = null)
	// {
	// 	if (!$editorType) {

	// 		$config = EB::config();
	// 		$jConfig = EB::jConfig();

	// 		// If use system editor, we should check if the configured editor exists or enabled.
	// 		$editorType = $config->get('layout_editor');

	// 		// if use build-in composer, we should check from the global configuration setting
	// 		if ($editorType == 'composer') {
	// 			$editorType = $jConfig->get('editor');
	// 		}
	// 	}

	// 	if (EBUtility::isJoomla4()) {
	// 		$editor = Joomla\CMS\Editor\Editor::getInstance($editorType);
	// 	} else {
	// 		$editor = JFactory::getEditor($editorType);

	// 		if ($editorType == 'none') {
	// 			JHtml::_('behavior.core');
	// 		}
	// 	}

	// 	return $editor;
	// }

	/**
	 * Returns a query variable by name.
	 *
	 * @since   5.4.6
	 * @access  public
	 */
	// public static function getApplication()
	// {
	// 	if (EBUtility::isJoomla31()) {
	// 		$app = JFactory::getApplication();
	// 	}

	// 	if (EBUtility::isJoomla4()) {
	// 		$app = Joomla\CMS\Factory::getApplication();
	// 	}

	// 	return $app;
	// }
}

class EDRouter
{
	/**
	 * Determine whether the site enable SEF.
	 *
	 * @since   5.2
	 * @access  public
	 */
	public static function getMode()
	{
		static $mode = null;

		if (is_null($mode)) {
			$jConfig = ED::jConfig();
			$mode = $jConfig->get('sef');

			if (ED::isFromAdmin()) {
				$mode = false;
			}
		}

		return $mode;
	}
}

class EDContentHelperRoute
{
	/**
	 * Get the article route.
	 *
	 * @since   3.3
	 * @access  public
	 */
	public static function getArticleRoute($id, $catid = 0, $language = 0, $layout = null)
	{
		if (ED::isJoomla4()) {
			return RouteHelper::getArticleRoute($id, $catid, $language, $layout);
		}

		return ContentHelperRoute::getArticleRoute($id, $catid, $language, $layout);
	}

	/**
	 * Get the category route.
	 *
	 * @since   3.3
	 * @access  public
	 */
	public static function getCategoryRoute($catid, $language = 0, $layout = null)
	{
		if (ED::isJoomla4()) {
			return RouteHelper::getCategoryRoute($catid, $language, $layout);
		}

		return ContentHelperRoute::getCategoryRoute($catid, $language, $layout);
	}

	/**
	 * Get the form route.
	 *
	 * @since   3.3
	 * @access  public
	 */
	public static function getFormRoute($id)
	{
		if (ED::isJoomla4()) {
			return RouteHelper::getFormRoute($id);
		}

		return ContentHelperRoute::getFormRoute($id);
	}
}