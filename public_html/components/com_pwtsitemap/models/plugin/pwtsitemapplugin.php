<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;

/**
 * PWT Sitemap Plugin base class
 *
 * @since  1.0.0
 */
abstract class PwtSitemapPlugin extends CMSPlugin
{
	/**
	 * Joomla Application instance
	 *
	 * @var    JApplicationSite
	 * @since  1.0.0
	 */
	public $app;

	/**
	 * Automatic load plugin language files
	 *
	 * @var    boolean
	 * @since  1.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * JDatabase instance
	 *
	 * @var    JDatabaseDriver
	 * @since  1.0.0
	 */
	protected $db;

	/**
	 * Component where the plugin is build for
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $component = '';

	/**
	 * Name of the component (without com_)
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $componentName;

	/**
	 * Views where the plugin is build for
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $views = [];

	/**
	 * Constructor
	 *
	 * @param   object  $subject   The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
	 *                             Recognized key values include 'name', 'group', 'params', 'language'
	 *                             (this list is not meant to be comprehensive).
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	public function __construct($subject, array $config = [])
	{
		parent::__construct($subject, $config);

		$this->populateSitemapPlugin();

		if (!isset($this->component, $this->views))
		{
			Factory::getApplication()->enqueueMessage(
				Text::sprintf('PLG_SYSTEM_PWTSITEMAP_ERROR_POPULATEPLUGIN_FAILED', $this->_type . '_' . $this->_name),
				'error'
			);
		}
		elseif (!isset($this->componentName))
		{
			$this->componentName = substr($this->component, 4);
		}
	}

	/**
	 * Populate the PWT Sitemap plugin to use it a base class
	 *
	 * @return  void
	 *
	 * @since   2.3.0
	 */
	abstract public function populateSitemapPlugin();

	/**
	 * Adds additional fields to the user editing form
	 *
	 * @param   JForm     $form  The form to be altered.
	 * @param   stdClass  $data  The associated data for the form.
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	public function onContentPrepareForm($form, $data)
	{
		// We store which datatype it was to make sure we give it back the same way
		$wasArray = is_array($data);

		$data = (object) $data;

		// Make sure form element is a JForm object
		if (!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');

			return false;
		}

		// Make sure we are on the edit menu item page and the user is allowed to change options
		if ($form->getName() !== 'com_menus.item' || !Factory::getUser()->authorise('core.options', 'com_pwtsitemap'))
		{
			return true;
		}

		// Load selected option and view if selected
		if (isset($data->request['view'], $data->request['option']))
		{
			$view   = $data->request['view'];
			$option = $data->request['option'];

			if ($option === $this->component && in_array($view, $this->views))
			{
				JForm::addFormPath(__DIR__ . '/forms/');

				$form->loadFile('pwtsitemapplugin');

				// Replace labels and description attribute
				$form->setFieldAttribute(
					'addcomponenttohtmlsitemap',
					'label',
					'PLG_PWTSITEMAP_' . $this->componentName . '_ADD' . $this->componentName . 'TOHTMLSITEMAP_LABEL',
					'params'
				);
				$form->setFieldAttribute(
					'addcomponenttoxmlsitemap',
					'label',
					'PLG_PWTSITEMAP_' . $this->componentName . '_ADD' . $this->componentName . 'TOXMLSITEMAP_LABEL',
					'params'
				);
				$form->setFieldAttribute(
					'addcomponenttohtmlsitemap',
					'description',
					'PLG_PWTSITEMAP_' . $this->componentName . '_ADD' . $this->componentName . 'TOHTMLSITEMAP_DESC',
					'params'
				);
				$form->setFieldAttribute(
					'addcomponenttoxmlsitemap',
					'description',
					'PLG_PWTSITEMAP_' . $this->componentName . '_ADD' . $this->componentName . 'TOXMLSITEMAP_DESC',
					'params'
				);

				// Replace field name
				$form->setFieldAttribute(
					'addcomponenttohtmlsitemap',
					'name',
					'add' . $this->componentName . 'tohtmlsitemap',
					'params'
				);
				$form->setFieldAttribute(
					'addcomponenttoxmlsitemap',
					'name',
					'add' . $this->componentName . 'toxmlsitemap',
					'params'
				);
			}
		}

		// Revert back to it's original state
		$data = ($wasArray) ? (array) $data : $data;

		return true;
	}

	/**
	 * Check the display format against the parameters and the plugin parameters to determine if we can skip the item or not
	 *
	 * @param   stdClass  $item        Sitemap item
	 *
	 * @param   string    $format      The requested format
	 * @param   array     $extraViews  Optional extra views to validate
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	protected function checkDisplayParameters($item, $format, $extraViews = [])
	{
		$views = array_merge($this->views, $extraViews);

		if (isset($item->query['option'])
			&& $item->query['option'] === $this->component
			&& in_array($item->query['view'], $views, true)
		)
		{
			return true;
		}

		return false;
	}
}
