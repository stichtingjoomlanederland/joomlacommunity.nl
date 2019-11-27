<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

/**
 * PWT image plugin.
 *
 * @since  1.0
 */
class PlgSystemPwtimage extends CMSPlugin
{
	/**
	 * Application  instance
	 *
	 * @var    SiteApplication
	 * @since  1.0
	 */
	protected $app;

	/**
	 * Database driver
	 *
	 * @var    JDatabaseDriverMysqli
	 * @since  1.1.0
	 */
	protected $db;

	/**
	 * List of extension fields to convert
	 *
	 * @var    array
	 * @since  1.1.0
	 */
	private $extensions = array();

	/**
	 * Holds the current group the fields belong to
	 *
	 * @var    string
	 * @since  1.1.0
	 */
	private $fieldGroup = '';

	/**
	 * @var    string  base update url, to decide whether to process the event or not
	 *
	 * @since  1.0.0
	 */
	private $baseUrl = 'https://extensions.perfectwebteam.com/pwt-image';

	/**
	 * @var    string  Extension identifier, to retrieve its params
	 *
	 * @since  1.0.0
	 */
	private $extension = 'com_pwtimage';

	/**
	 * @var    string  Extension title, to retrieve its params
	 *
	 * @since  1.0.0
	 */
	private $extensionTitle = 'PWT Image';

	/**
	 * Constructor
	 *
	 * @param   object $subject   The object to observe
	 * @param   array  $config    An optional associative array of configuration settings.
	 *                            Recognized key values include 'name', 'group', 'params', 'language'
	 *                            (this list is not meant to be comprehensive).
	 *
	 * @since   1.0
	 */
	public function __construct(&$subject, array $config = array())
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Load the list of extensions to apply to the media fields.
	 *
	 * @return  void
	 *
	 * @since   1.1.0
	 */
	private function loadExtensions()
	{
		$db = $this->db;

		// Load all the profiles
		$query = $db->getQuery(true)
			->select($db->quoteName('extensions.path'))
			->from($db->quoteName('#__pwtimage_extensions', 'extensions'))
			->leftJoin(
				$db->quoteName('#__pwtimage_profiles', 'profiles')
				. ' ON ' . $db->quoteName('profiles.id') . ' = ' . $db->quoteName('extensions.profile_id')
			)
			->where($db->quoteName('profiles.published') . ' = 1');
		$db->setQuery($query);

		$this->extensions = $db->loadColumn();
	}

	/**
	 * Event method that runs on content preparation
	 *
	 * Turns all media fields to pwtimage.image fields
	 *
	 * @param   Form    $form The form object
	 * @param   integer $data The form data
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function onContentPrepareForm($form, $data)
	{
		// Check if we get a valid form
		if (!($form instanceof Form))
		{
			$this->_subject->setError(Text::_('JERROR_NOT_A_FORM'));

			return false;
		}

		// Check if we have any extensions to replace
		$this->loadExtensions();

		if (empty($this->extensions))
		{
			return true;
		}

		// Set that field path is not yet set
		$fieldPath = false;

		// Build the extension identifier
		$identifier = array();
		$option     = $this->app->input->get('option');
		$all        = in_array('all', $this->extensions);

		// Check if we are in com_modules
		if ($option === 'com_modules')
		{
			$option = $this->getModuleName($this->app->input->get('id'));
		}
		elseif ($option === 'com_config')
		{
			$option = $this->app->input->get('component');
		}

		// Set the main identifier
		$identifier[] = $option;

		// Run through the form to find any media fields
		foreach ($form->getXml() as $set => $item)
		{
			// Find the fields for this item
			$fieldCollection = $this->findFields($item, $set);

			// Check to see if we have any fields
			if (!$fieldCollection)
			{
				continue;
			}

			/** @var SimpleXMLElement $field */
			foreach ($fieldCollection as $groupName => $fields)
			{
				switch ($fields['path'])
				{
					case 'fieldset/fields':
					case 'fieldset':
						$name = (string) $item->attributes()->name;

						if (!$this->fieldGroup && $name)
						{
							$this->fieldGroup = $name;
						}

						if (!$name)
						{
							$name = (string) $item->fieldset->attributes()->name;
						}

						$identifier[] = $name;
						break;
					case 'fields/fieldset':
					case 'fields':
						$this->fieldGroup = (string) $item->fields->attributes()->name;
						$identifier[]     = $this->fieldGroup;
						break;
					case 'field':
						$this->fieldGroup = $groupName;
						break;
				}

				foreach ($fields['fields'] as $field)
				{
					$attributes = $field->attributes();

					// Check if we have a media type field
					if (!((string) $attributes->type === 'media') && !((string) $attributes->type === 'pwtimage.image'))
					{
						continue;
					}

					// Build
					if ($groupName)
					{
						$identifier[] = $groupName;
					}
					elseif ($set === 'fieldset')
					{
						/**
						 * If the structure of the XML is as follows, then there are no attributes, so no name. If there is no name
						 * don't add an empty identifier
						 *
						 * <form>
						 *  <fieldset>
						 *   <field name="id"
						 */
						$fieldsetName = (string) $item->attributes()->name;

						if ($fieldsetName)
						{
							$identifier[] = $fieldsetName;
						}
					}

					// Set the field name
					$identifier[] = (string) $field->attributes()->name;

					/**
					 * If we in com_content, and we have a profile for a specific category, load that one instead.
					 * With a fallback to general com_content
					 */
					if ($option === 'com_content')
					{
						// Depending on the alignment of celestial bodies (ugh) data can be either array or object
						$isArray = is_array($data);

						$data = (object) $data;

						if (isset($data->catid) && $data->catid > 0)
						{
							if (in_array(implode('.', $identifier) . '.' . $data->catid, $this->extensions))
							{
								$identifier[] = $data->catid;
							}
						}

						$data = $isArray ? (array) $data : $data;
					}

					// If there is a profile that applies to all fields or if the identifier is matched we set the field
					if ($all || in_array(implode('.', $identifier), $this->extensions))
					{
						$form->setFieldAttribute(
							(string) $attributes->name,
							'type',
							'pwtimage.image',
							$this->fieldGroup
						);

						$form->setFieldAttribute(
							(string) $attributes->name,
							'origin',
							(implode('.', $identifier) ?: 'all'),
							$this->fieldGroup
						);

						if (!$fieldPath)
						{
							$form->setFieldAttribute(
								(string) $attributes->name,
								'addfieldpath',
								'components/com_pwtimage/models/fields',
								$this->fieldGroup
							);

							$fieldPath = true;
						}
					}

					// Clean the identifier list back to component
					$identifier = array(reset($identifier));
				}

				// Clean the identifier list back to component
				$identifier = array(reset($identifier));
			}
		}

		return true;
	}

	/**
	 * Get the fields from the different XPaths.
	 *
	 * @param   SimpleXMLElement $item The item to check for paths
	 * @param   string           $set  The parent group name
	 *
	 * @return  array  List of fields.
	 *
	 * @since   1.1.0
	 */
	private function findFields($item, $set)
	{
		$fieldCollection = array();
		$paths           = array(
			'fieldset/fields',
			'fields/fieldset',
			'fieldset',
			'fields',
		);

		// First find the groups
		foreach ($paths as $index => $path)
		{
			$groups = $item->xpath($path);

			if ($groups)
			{
				foreach ($groups as $group)
				{
					$groupName = (string) $group->attributes()->name;

					$fieldCollection[$groupName]['path']   = $path;
					$fieldCollection[$groupName]['fields'] = $group->xpath('field');
				}

				break;
			}
		}

		// Second find the fields
		if (empty($fieldCollection))
		{
			$fields = $item->xpath('field');

			if ($fields)
			{
				$groupName = '';

				if ($set !== 'fieldset')
				{
					$groupName = (string) $item->attributes()->name;
				}

				$fieldCollection[$groupName]['path']   = 'field';
				$fieldCollection[$groupName]['fields'] = $fields;
			}
		}

		return $fieldCollection;
	}

	/**
	 * Find the real module name.
	 *
	 * @param   int $moduleId The ID of the module
	 *
	 * @return  string  The module name.
	 *
	 * @since   1.1.0
	 */
	private function getModuleName($moduleId)
	{
		$db    = $this->db;
		$query = $db->getQuery(true)
			->select($db->quoteName('module'))
			->from($db->quoteName('#__modules'))
			->where($db->quoteName('id') . ' = ' . (int) $moduleId);
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Replace tags before the content is being displayed.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onAfterRender()
	{
		// List of excluded extensions
		$excludedExtensions = [
			'com_acymailing'
		];

		// Only run on frontend, do not run when we are in AJAX mode
		if ($this->app->input->get('format') === 'json'
			|| ($this->app->input->get('layout') === 'edit')
			|| in_array($this->app->input->get('option'), $excludedExtensions)
		)
		{
			return;
		}

		$body = $this->app->getBody();
		$this->replaceTags($body);
		$this->app->setBody($body);
	}

	/**
	 * Replace tags in a given text.
	 *
	 * @param   string $text   The text to replace.
	 * @param   bool   $remove Set if the matched string should be replaced.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function replaceTags(&$text, $remove = false)
	{
		$this->replaceImage($text, $remove);
	}

	/**
	 * Replace Image.
	 *
	 * @param   string $text   The text to replace.
	 * @param   bool   $remove Set if the matched string should be replaced.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	private function replaceImage(&$text, $remove)
	{
		if (strpos($text, '{image') !== false)
		{
			$pattern = '/\{image([^\}]+)\}/';

			if (preg_match_all($pattern, $text, $matches))
			{
				foreach ($matches[0] as $i => $match)
				{
					if ($remove)
					{
						$text = str_replace('<p>' . $matches[0][$i] . '</p>', '', $text);
						$text = str_replace($matches[0][$i], '', $text);
					}
					else
					{
						// Set the defaults
						$image   = false;
						$alt     = '';
						$caption = false;

						// Check for placeholders
						$subPattern = '/\s?([a-zA-Z0-9]+)="([^"\\\]*(?:\\\.[^"\\\]*)*)"/';
						preg_match_all($subPattern, $matches[1][$i], $subMatches);

						// Backwards compatible check for path=/my/image
						if (count($subMatches[0]) === 0)
						{
							$path  = trim(str_replace('&nbsp;', '', $matches[1][$i]));
							$image = str_replace('path=', '', $path);
						}
						else
						{
							// Get the image
							$key = array_search('path', $subMatches[1]);

							if ($key !== false)
							{
								$image = str_replace(array('&nbsp;', ' '), array('', '%20'), trim($subMatches[2][$key]));
							}

							// Get the alt-text
							$key = array_search('alt', $subMatches[1]);

							if ($key)
							{
								$alt = htmlentities(trim(str_replace('\"', '"', $subMatches[2][$key])));
							}

							// Get the caption
							$key = array_search('caption', $subMatches[1]);

							if ($key)
							{
								$caption = htmlentities(trim(str_replace('\"', '"', $subMatches[2][$key])));
							}
						}

						// Using absolute path makes certain we can use it anywhere
						$image = Uri::root(false, '/' . $image);

						$data = array(
							'image'   => rtrim($image, '/'),
							'alt'     => $alt,
							'caption' => $caption
						);

						$layout = new FileLayout('image', null, array('debug' => JDEBUG, 'component' => 'com_pwtimage'));
						$embed  = $layout->render($data);

						$text = str_replace($matches[0][$i], $embed, $text);
					}
				}
			}
		}
	}

	/**
	 * Adding required headers for successful extension update
	 *
	 * @param   string $url     url from which package is going to be downloaded
	 * @param   array  $headers headers to be sent along the download request (key => value format)
	 *
	 * @return  boolean true    Always true, regardless of success
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		// Are we trying to update our own extensions?
		if (strpos($url, $this->baseUrl) !== 0)
		{
			return true;
		}

		// Load language file
		$jLanguage = Factory::getLanguage();
		$jLanguage->load('com_pwtimage', JPATH_ADMINISTRATOR . '/components/com_pwtimage/', 'en-GB', true, true);
		$jLanguage->load('com_pwtimage', JPATH_ADMINISTRATOR . '/components/com_pwtimage/', null, true, false);

		// Append key to url if not set yet
		if (strpos($url, 'key') == false)
		{
			// Get the Download ID from component params
			$downloadId = ComponentHelper::getComponent($this->extension)->params->get('downloadid', '');

			// Check if Download ID is set
			if (empty($downloadId))
			{
				Factory::getApplication()->enqueueMessage(
					Text::sprintf('COM_PWTIMAGE_DOWNLOAD_ID_REQUIRED',
						$this->extension,
						$this->extensionTitle
					),
					'error'
				);

				return true;
			}

			// Append the Download ID from component options
			$separator = strpos($url, '?') !== false ? '&' : '?';
			$url       .= $separator . 'key=' . trim($downloadId);
		}

		// Append domain to url if not set yet
		if (strpos($url, 'domain') == false)
		{
			// Get the domain for this site
			$domain = preg_replace('(^https?://)', '', rtrim(Uri::root(), '/'));

			// Append domain
			$url .= '&domain=' . $domain;
		}

		return true;
	}
}
