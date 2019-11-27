<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\UserGroupsHelper;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

/**
 * Profile model.
 *
 * @package  Pwtimage
 * @since    1.1.0
 */
class PwtimageModelProfile extends AdminModel
{
	/**
	 * Database connector
	 *
	 * @var    JDatabase
	 * @since  1.1.0
	 */
	private $db = null;

	/**
	 * Language class
	 *
	 * @var    Language
	 * @since  1.1.0
	 */
	private $language;

	/**
	 * The current path being indexed
	 *
	 * @var    string
	 * @since  1.1.0
	 */
	private $path;

	/**
	 * The current file being indexed
	 *
	 * @var    string
	 * @since  1.1.0
	 */
	private $file;

	/**
	 * List of paths to the media field
	 *
	 * @var    array
	 * @since  1.1.0
	 */
	private $identifier = array();

	/**
	 * List of indexed image fields
	 *
	 * @var    array
	 * @since  1.1.0
	 */
	private $imagePaths = array();

	/**
	 * List of extension names
	 *
	 * @var    array
	 * @since  1.1.0
	 */
	private $extensionNames = array();

	/**
	 * Breadcrumb to media field
	 *
	 * @var    array
	 * @since  1.1.0
	 */
	private $breadcrumb = array();

	/**
	 * Constructor.
	 *
	 * @param   array $config An optional associative array of configuration settings.
	 *
	 * @since   1.1.0
	 */
	public function __construct($config = array())
	{
		$this->db       = Factory::getDbo();
		$this->language = new Language;

		parent::__construct($config);
	}

	/**
	 * Get the form.
	 *
	 * @param   array   $data     Data for the form.
	 * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A Form object on success | False on failure.
	 *
	 * @since   1.1.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_pwtimage.profile', 'profile', array('control' => 'jform', 'load_data' => $loadData));

		if (!is_object($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Save the configuration.
	 *
	 * @param   array $data The form data.
	 *
	 * @return  boolean  True on success or false on failure.
	 *
	 * @since   1.1.0
	 *
	 * @throws  Exception
	 * @throws  RuntimeException
	 */
	public function save($data)
	{
		$app = Factory::getApplication();

		// Store the settings as a JSON string
		$settings = $data;

		// Clean up the settings
		unset($settings['id']);
		unset($settings['name']);
		unset($settings['tags']);
		unset($settings['published']);

		// Get the extensions, we store them separately
		$extensions = array('all');

		if ((int) $data['allMediaFields'] === 0)
		{
			if ($data['catids'] && count($data['catids']))
			{
				// Add the specified category with the path
				foreach ($data['catids'] as $catid)
				{
					if (in_array('com_content.images.image_fulltext', $settings['extensions']))
					{
						$settings['extensions'][] = 'com_content.images.image_fulltext.' . $catid;
					}

					if (in_array('com_content.images.image_intro', $settings['extensions']))
					{
						$settings['extensions'][] = 'com_content.images.image_intro.' . $catid;
					}
				}

				// Remove all the general com_content paths, we no longer need them
				$extensions = array_filter(
					$settings['extensions'],
					function ($el) {
						return stripos($el, 'com_content') === false || preg_match('/\d+/', $el);
					}
				);
			}
			else
			{
				$extensions = $settings['extensions'];
			}
		}

		unset($settings['extensions']);

		$params           = new Registry($settings);
		$data['settings'] = $params->toString();

		// Alter the title for save as copy
		if ($app->input->get('task') == 'save2copy')
		{
			$origTable = clone $this->getTable();
			$origTable->load($app->input->getInt('id'));

			// Set the new ordering value
			$data['ordering'] = $origTable->get('ordering') + 1;

			// Unset the ID so a new item is created
			unset($data['id']);
		}

		// Save the profile
		if (!parent::save($data))
		{
			return false;
		}

		// Save the extensions
		$profileId = $this->getState($this->getName() . '.id');
		$this->saveExtensions($extensions, $profileId);

		return true;
	}

	/**
	 * Save the extensions for a given profile.
	 *
	 * @param   array $extensions The list of extensions to save
	 * @param   int   $profileId  The ID of the profile the extensions are linked to
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 *
	 * @since   1.1.0
	 */
	private function saveExtensions($extensions, $profileId)
	{
		$db = $this->getDbo();

		// All extensions removed
		if ($extensions === null)
		{
			$query = $db->getQuery(true)
				->delete($db->quoteName('#__pwtimage_extensions'))
				->where($db->quoteName('profile_id') . ' = ' . (int) $profileId);
			$db->setQuery($query)->execute();

			return;
		}

		// Get all existing relations
		$query = $db->getQuery(true)
			->select($db->quoteName('path'))
			->from($db->quoteName('#__pwtimage_extensions'))
			->where($db->quoteName('profile_id') . ' = ' . (int) $profileId);
		$db->setQuery($query);
		$paths = $db->loadColumn();

		// Get the new extensions
		$newPaths = array_diff($extensions, $paths);

		// Get the old extensions
		$oldPaths = array_diff($paths, $extensions);

		// Add the new relations if there are any
		if ($newPaths)
		{
			$query->clear()
				->insert($db->quoteName('#__pwtimage_extensions'))
				->columns($db->quoteName(array('profile_id', 'path')));

			foreach ($newPaths as $index => $extension)
			{
				$query->values($profileId . ',' . $db->quote($extension));
			}

			$db->execute();
		}

		if ($oldPaths)
		{
			$oldPaths = $db->quote($oldPaths);

			$query->clear()
				->delete($db->quoteName('#__pwtimage_extensions'))
				->where($db->quoteName('path') . ' IN (' . implode(',', $oldPaths) . ')');
			$db->setQuery($query)->execute();
		}
	}

	/**
	 * Build a list of image fields that can be configured in the profile.
	 *
	 * @return  array  List of extensions
	 *
	 * @since   1.1.0
	 */
	public function getExtensions()
	{
		jimport('joomla.filesystem.folder');

		// List of paths to search
		$filePaths = array(
			'administrator/components',
			'components',
			'modules'
		);

		// Get a list of XML files currently in the system
		foreach ($filePaths as $pathIndex => $path)
		{
			$files = JFolder::files(JPATH_SITE . '/' . $path, '.xml$', true, true);

			// Check if there is an image field in the XML
			foreach ($files as $fileIndex => $file)
			{
				// Do not process XML files that cannot include media files
				if (basename($file) === 'access.xml')
				{
					continue;
				}

				// Read the XML file
				$xml = simplexml_load_file($file);

				// Do not process if it is not a valid XML or does not have a media field
				if (!is_object($xml) || (!$xml->xpath("//field[@type='media']") && !$xml->xpath("//field[@type='pwtimage.image']")))
				{
					continue;
				}

				// Set the current path and file
				$this->path = $path;
				$this->file = JPath::clean($file, '/');

				// Get the children of the XML
				$children = $xml->children();

				// Parse the children if there are any
				foreach ($children as $child)
				{
					// Do not process the child if it has no media field
					if (!$child->xpath("//field[@type='media']") && !$child->xpath("//field[@type='pwtimage.image']"))
					{
						continue;
					}

					// Check for a valid identifier name
					$this->identifier = array((string) $child->attributes()->name);
					$this->breadcrumb = array((string) $child->attributes()->label);

					if ((int) strlen($this->identifier[0]) === 0)
					{
						$this->identifier = array();
					}

					if ((int) strlen($this->breadcrumb[0]) === 0)
					{
						$this->breadcrumb = array();
					}

					$this->parseXml($child);
				}
			}
		}

		foreach ($this->imagePaths as &$imagePath)
		{
			uasort(
				$imagePath,
				function ($a, $b) {
					return strcasecmp($a->breadcrumb, $b->breadcrumb);
				}
			);
		}

		return $this->imagePaths;
	}

	/**
	 * Parse an XML node to find the path to the media element.
	 *
	 * @param   SimpleXMLElement $xml   The XML node to parse
	 * @param   int              $level Keep track of the level we are at
	 *
	 * @return  boolean  True if found | False otherwise
	 *
	 * @since   1.1.0
	 */
	private function parseXml($xml, $level = 1)
	{
		if (count($xml) > 0)
		{
			foreach ($xml as $index => $node)
			{
				// Get the attributes from the node
				$attributes = $node->attributes();

				// Check if we have a fieldset or fields name
				if ((string) $node->getName() === 'fieldset' || (string) $node->getName() === 'fields')
				{
					$this->identifier[] = (string) $attributes->name;
					$this->breadcrumb[] = (string) $attributes->label;
				}

				// Check if the node has a type set, this means it is a field
				if (isset($attributes->type) && ((string) $attributes->type === 'media' || (string) $attributes->type === 'pwtimage.image'))
				{
					$this->identifier[] = (string) $attributes->name;
					$this->breadcrumb[] = (string) $attributes->label;

					// Add the node to the list of selectable extension fields
					$this->addToList();

					// Remove the path for this item
					array_pop($this->identifier);
					array_pop($this->breadcrumb);
				}

				// Check if the node has no type set that means it is not a field but a group
				if ($node instanceof SimpleXMLElement && !isset($attributes->type))
				{
					if ($this->parseXml($node, $level + 1))
					{
						return true;
					}

					if ($level === 1)
					{
						array_pop($this->identifier);
						array_pop($this->breadcrumb);
					}
					else
					{
						$this->identifier = array();
						$this->breadcrumb = array();
					}
				}
			}
		}

		// Only empty the array if we are not nested
		if ($level === 1)
		{
			$this->identifier = array();
			$this->breadcrumb = array();
		}

		return false;
	}

	/**
	 * Index image fields in a node.
	 *
	 * @return  void
	 *
	 * @since   1.1.0
	 */
	private function addToList()
	{
		// If we have a path add it to the list of image paths
		if (stristr($this->file, 'access.xml') === false
			&& stristr($this->file, '/tmpl/') === false
			&& count($this->identifier) > 0)
		{
			// Clean the site path
			$sitePath = JPath::clean(JPATH_SITE, '/');

			// Get the XML path
			$extensionPath = str_replace($sitePath . '/', '', $this->file);

			// Get the extension name
			$name          = str_replace($this->path . '/', '', $extensionPath);
			$extensionName = substr($name, 0, strpos($name, '/'));

			// Deal with plugins following a different scheme
			if ($this->path === 'plugins')
			{
				$firstPostion  = strpos($name, '/');
				$secondPostion = strpos($name, '/', $firstPostion + 1);
				$extensionName = 'plg_' . substr_replace($name, '_', $firstPostion)
					. substr($name, $firstPostion + 1, $secondPostion - $firstPostion - 1);
			}

			// Get the extension type
			$type = $this->getExtensionType($extensionName, true);

			// Get the translations
			if ($this->breadcrumb[0] !== $extensionName)
			{
				array_unshift($this->breadcrumb, $extensionName);
			}

			$translation = $this->getTranslatedExtensionName($this->breadcrumb);

			$extension             = new stdClass;
			$extension->path       = $extensionPath;
			$extension->identifier = PwtimageHelper::getSysonymForOrigin($extensionName . '.' . implode('.', $this->identifier));
			$extension->breadcrumb = implode('.', $translation);

			if (stripos($name, 'config.xml'))
			{
				$extension->breadcrumb = 'Configuration.' . $extension->breadcrumb;
			}

			$this->imagePaths[$type][$extension->identifier] = $extension;
		}
	}

	/**
	 * Get the extension type.
	 *
	 * @param   string  $extension The name of the extension to get the type for
	 * @param   boolean $translate If the type should be translated
	 *
	 * @return  string  The extension type
	 *
	 * @since   1.1.0
	 */
	private function getExtensionType($extension, $translate = false)
	{
		$type = substr($extension, 0, 3);
		$lang = Factory::getLanguage();
		$lang->load('mod_menu');

		switch ($type)
		{
			case 'com':
				$type = 'component';

				if ($translate)
				{
					$type = Text::_('MOD_MENU_COMPONENTS');
				}
				break;
			case 'mod':
				$type = 'module';

				if ($translate)
				{
					$type = Text::_('MOD_MENU_EXTENSIONS_MODULE_MANAGER');
				}
				break;
			case 'plg':
				$type = 'plugin';

				if ($translate)
				{
					$type = Text::_('MOD_MENU_EXTENSIONS_PLUGIN_MANAGER');
				}
				break;
		}

		return $type;
	}

	/**
	 * Get the extension translated name.
	 *
	 * @param   array $breadcrumb The breadcrumb to translate
	 *
	 * @return  array  The translated breadcrumb
	 *
	 * @since   1.1.0
	 */
	private function getTranslatedExtensionName($breadcrumb)
	{
		$name    = $breadcrumb[0];
		$prefix  = strtolower(substr($name, 0, 3));
		$element = '';
		$folder  = '';
		$lookup  = false;

		switch ($prefix)
		{
			case 'com':
				$type    = 'component';
				$element = $name;
				$lookup  = true;
				break;
			case 'mod':
				$type    = 'module';
				$element = $name;
				$lookup  = true;
				break;
			case 'plg':
				$type                        = 'plugin';
				$this->extensionNames[$name] = $name;
				break;
		}

		if ($lookup && !array_key_exists($name, $this->extensionNames))
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('name'))
				->from($db->quoteName('#__extensions'))
				->where($db->quoteName('type') . ' = ' . $db->quote($type))
				->where($db->quoteName('element') . ' = ' . $db->quote($element));

			if ($folder)
			{
				$query->where($db->quoteName('folder') . ' = ' . $db->quote($folder));
			}

			$db->setQuery($query);
			$this->extensionNames[$name] = $db->loadResult();
		}

		$language    = $this->loadLanguageFile($this->extensionNames[$name], $type);
		$translation = array();

		foreach ($breadcrumb as $index => $item)
		{
			if (!$item)
			{
				continue;
			}

			// Nasty, but what can we do :)
			$item = $this->getLanguageException($item);

			// Get the translation for the field
			$translation[] = $language->_($item);
		}

		return $translation;
	}

	/**
	 * Load the language file for a given extensions.
	 *
	 * @param   string $name The name of the extension to translate
	 * @param   string $type The type of extension
	 *
	 * @return  Language  The language class
	 *
	 * @since   1.1.0
	 */
	private function loadLanguageFile($name, $type)
	{
		switch ($type)
		{
			case 'plugin':
				// Load using conventional method
				if (!$this->language->load($name))
				{
					list ($type, $folder, $element) = explode('_', $name);

					// Try loading it from the plugin folder
					$this->language->load($name, JPATH_ROOT . '/plugins/' . $folder . '/' . $element);
					$this->language->load($name . '.sys', JPATH_ROOT . '/plugins/' . $folder . '/' . $element);
				}
				break;
			case 'module':
				$this->language->load('com_modules');
				$this->language->load($name);
				$this->language->load($name, JPATH_SITE);
				break;
			default:
				$this->language->load($name);
				break;
		}

		return $this->language;
	}

	/**
	 * List of language exceptions to deal with.
	 *
	 * @param   string $string The string to change
	 *
	 * @return  string  The string to translate
	 *
	 * @since   1.1.0
	 */
	private function getLanguageException($string)
	{
		$newString = $string;

		switch ($string)
		{
			case 'CONFIG_SITE_SETTINGS_LABEL':
				$newString = 'COM_CONFIG_SITE_SETTINGS';
				break;
		}

		return $newString;
	}

	/**
	 * Method to get the data that should be injected in the form..
	 *
	 * @return  array  The data for the form..
	 *
	 * @since   1.1.0
	 *
	 * @throws  Exception
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_pwtimage.edit.profile.data', array());

		if (0 === count($data))
		{
			$data = $this->getItem();
		}

		// We have to remove the specific category identifiers from the paths
		array_walk(
			$data->extensions,
			function (&$el) {
				if (stripos($el, 'com_content') === 0 && preg_match('/\d+/', $el))
				{
					$el = substr($el, 0, strrpos($el, '.'));
				}
			}
		);

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer $pk The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   1.1.0
	 *
	 * @throws  Exception
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		// Set the default values
		if (!$pk)
		{
			$item->subPath        = '{year}/{month}';
			$item->filenameFormat = '{d}_{random}_{name}';
		}

		// Get the settings as regular data
		$settings = (new Registry($item->settings))->toArray();

		unset($item->settings);

		$item = (object) array_merge($item->getProperties(), $settings);

		// Load the extensions
		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('path'))
			->from($db->quoteName('#__pwtimage_extensions'))
			->where($db->quoteName('profile_id') . ' = ' . (int) $item->id);
		$db->setQuery($query);

		$item->extensions = $db->loadColumn();

		// Check if we have the user groups
		if (!isset($item->usergroups))
		{
			// Get all available user groups
			$item->usergroups = array();

			// If this is a new profile, we select all user groups
			if (empty($item->id))
			{
				$item->usergroups = array_keys(UserGroupsHelper::getInstance()->getAll());
			}
		}

		return $item;
	}
}
