<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die;

/**
 * Structured Data view. Allows to setup the structured data for the current item
 *
 * @since    1.3.0
 */
class PWTSEOViewStructuredData extends HtmlView
{
	/**
	 * The Form object
	 *
	 * @var    Form
	 * @since  1.3.0
	 */
	protected $form;

	/**
	 * The active item.
	 *
	 * @var    object
	 * @since  1.3.0
	 */
	protected $item;

	/**
	 * The model state
	 *
	 * @var    object
	 * @since  1.3.0
	 */
	protected $state;

	/**
	 * The id of the item
	 *
	 * @var    object
	 * @since  1.3.0
	 */
	protected $context_id;

	/**
	 * The current context from which we are called
	 *
	 * @var    object
	 * @since  1.3.0
	 */
	protected $context;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   1.3.0
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		$input = Factory::getApplication()->input;

		$this->context_id = $input->getInt('context_id', 0);
		$this->context    = $input->getCmd('context', '');

		if (!$this->context_id || !$this->context)
		{
			$tpl = 'empty';
		}
		else
		{
			$this->form  = $this->get('Form');
			$this->item  = $this->get('Item');
			$this->state = $this->get('State');

			// Check for errors.
			if (count($errors = $this->get('Errors')))
			{
				JError::raiseError(500, implode("\n", $errors));

				return false;
			}
		}

		return parent::display($tpl);
	}
}
