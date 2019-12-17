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
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * The HTML Menus Menu Item View.
 *
 * @since  1.3.0
 */
class PwtSitemapViewMenu extends HtmlView
{
	/**
	 * @var    Form
	 * @since  1.3.0
	 */
	protected $form;

	/**
	 * @var    mixed
	 * @since  1.3.0
	 */
	protected $item;

	/**
	 * @var    CMSObject
	 * @since  1.3.0
	 */
	protected $state;

	/**
	 * @var    CMSObject
	 * @since  1.3.0
	 */
	protected $canDo;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed void is successful, otherwise an Error object
	 *
	 * @since   1.3.0
	 *
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		/** @var PwtSitemapModelMenu $model */
		$model       = $this->getModel();
		$this->form  = $model->getForm();
		$this->item  = $model->getItem();
		$this->state = $model->getState();

		// Check for errors.
		if (count($errors = $model->getErrors()))
		{
			throw new RuntimeException(implode("\n", $errors), 500);
		}

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.3.0
	 *
	 * @throws  Exception
	 */
	private function addToolbar()
	{
		$input = Factory::getApplication()->input;
		$input->set('hidemainmenu', true);

		ToolbarHelper::title(Text::_('COM_PWTSITEMAP_VIEW_EDIT_MENU_TITLE'), 'pwtsitemap');
		ToolbarHelper::apply('menu.apply');
		ToolbarHelper::save('menu.save');
		ToolbarHelper::cancel('menu.cancel', 'JTOOLBAR_CLOSE');
	}
}
