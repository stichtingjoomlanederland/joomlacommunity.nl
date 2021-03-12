<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

/**
 * PWT SEO field
 * The field that is added to the content form
 *
 * @since  1.0
 */
class JFormFieldPWTSeo extends FormField
{
	/**
	 * A Registry object holding the parameters for the plugin
	 *
	 * @var    Registry
	 *
	 * @since  1.0
	 */
	private $params;

	/**
	 * Constructor for the field, we use this to get the plugin params in our field
	 *
	 * @param   JForm $form The form to attach to the form field object
	 *
	 * @since 1.0
	 */
	public function __construct($form = null)
	{
		$this->params = new Registry(PluginHelper::getPlugin('system', 'pwtseo')->params);

		parent::__construct($form);
	}

	/**
	 * Get the label of the PWTSeo Field. We abuse this to create our own layout.
	 *
	 * @return string The label - the left side of the panel
	 *
	 * @since 1.0
	 */
	protected function getLabel()
	{
		return '';
	}

	/**
	 * Get the any datalayers associated with this page
	 *
	 * @return string The label - the left side of the panel
	 *
	 * @since 1.0
	 */
	protected function getDataLayers()
	{
		return true;
	}

	/**
	 * Get the html/view of the input field. We abuse this to create our own layout.
	 *
	 * @return string The input - the right side of the panel
	 *
	 * @since 1.0
	 */
	protected function getInput()
	{
		ob_start();

		include JPATH_PLUGINS . '/system/pwtseo/tmpl/serp.php';

		// By loading another form model, we can trigger the onContentPrepareForm on our actual seo instead of the original
		$form = new Form('com_pwtseo');

		$form->loadFile(JPATH_PLUGINS . '/system/pwtseo/form/form.xml', false);
		$this->form->loadFile(JPATH_PLUGINS . '/system/pwtseo/form/form.xml', false);

		$dispatcher = JEventDispatcher::getInstance();
		$dispatcher->trigger('onContentPrepareForm', array(&$form, array()));

		// Now we update the original form with all our fields
		$fieldsets = $form->getFieldsets();

		foreach ($fieldsets as $set)
		{
			$fields = $form->getFieldset($set->name);

			/** @var FormField $field */
			foreach ($fields as $field)
			{
				if (method_exists($field, 'getAttribute') === false)
				{
					continue;
				}

				$xml = $form->getFieldXml($field->getAttribute('name'), 'pwtseo');

				if ($xml)
				{
					if ($this->form->getName() !== 'com_menus.item')
					{
						if ($field->getAttribute('name') === 'cascade_settings')
						{
							continue;
						}
					}

					$this->form->setField($xml, '', true, $set->name);
				}
			}
		}

		if ($this->form->getValue('strip_canonical_choice', 'pwtseo') === null)
		{
			$this->form->setValue('strip_canonical_choice', 'pwtseo', $form->getValue('strip_canonical_choice', 'pwtseo'));
		}

		if ($this->form->getValue('expand_og', 'pwtseo') === null)
		{
			$this->form->setValue('expand_og', 'pwtseo', $form->getValue('expand_og', 'pwtseo'));
		}

		if ($this->form->getName() === 'com_content.article')
		{
			$this->form->removeField('strip_canonical', 'pwtseo');
			$this->form->removeField('strip_canonical_choice', 'pwtseo');
		}

		if ($this->form->getName() !== 'com_menus.item')
		{
			$this->form->removeField('cascade_settings', 'pwtseo');
		}
		else
		{
			if ($this->form->getValue('cascade_settings', 'pwtseo') === null)
			{
				$this->form->setValue('cascade_settings', 'pwtseo', $form->getValue('cascade_settings', 'pwtseo'));
			}
		}

		echo $this->form->renderFieldset('left-side');
		echo $this->form->renderFieldset('basic_og');

		if ($this->params->get('show_datalayers', 0))
		{
			echo $this->form->renderFieldset('datalayers');
		}

		if ($this->params->get('show_structureddata', 0))
		{
			echo $this->form->renderFieldset('structureddata');
		}

		if ($this->params->get('advanced_mode'))
		{
			if (!$this->form->getValue('articletitleselector', 'pwtseo'))
			{
				$this->form->setValue('articletitleselector', 'pwtseo', $form->getValue('articletitleselector', 'pwtseo'));
			}

			echo $this->form->renderFieldset('advanced_og');
		}

		include JPATH_PLUGINS . '/system/pwtseo/tmpl/requirements.php';

		$sHTML = ob_get_clean();

		return $sHTML;
	}
}
