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
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;

/**
 * The Menu Type Controller
 *
 * @since  1.3.0
 */
class PwtSitemapControllerMenu extends FormController
{
	/**
	 * Dummy method to redirect back to standard controller
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached.
	 * @param   mixed    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  void     This object to support chaining.
	 *
	 * @since   1.3.0
	 *
	 * @throws  Exception
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$this->setRedirect(Route::_('index.php?option=com_pwtsitemap&view=menus', false));
	}

	/**
	 * Method to save a menu item.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   1.3.0
	 *
	 * @throws  Exception
	 */
	public function save($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		$this->checkToken();

		$app      = Factory::getApplication();
		$data     = $this->input->post->get('jform', [], 'array');
		$context  = 'com_pwtsitemap.edit.menu';
		$task     = $this->getTask();
		$recordId = $this->input->getInt('id');

		// Prevent using 'main' as menutype as this is reserved for backend menus
		if (strtolower($data['menutype']) === 'main')
		{
			$msg = Text::_('COM_PWTSITEMAP_ERROR_MENUTYPE');
			$app->enqueueMessage($msg, 'error');

			// Redirect back to the edit screen.
			$this->setRedirect(Route::_('index.php?option=com_pwtsitemap&view=menu&layout=edit', false));

			return false;
		}

		// Populate the row id from the session.
		$data['id'] = $recordId;

		// Get the model and attempt to validate the posted data.
		$model = $this->getModel('Menu');
		$form  = $model->getForm();

		if (!$form)
		{
			throw new Exception(implode("\n", $model->get('Errors')), 500);
		}

		$validData = $model->validate($form, $data);

		// Check for validation errors.
		if ($validData === false)
		{
			// Get the validation messages.
			$errors = $model->get('Errors');

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState($context . '.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(Route::_('index.php?option=com_pwtsitemap&view=menu&layout=edit', false));

			return false;
		}

		// Attempt to save the data.
		if (!$model->save($validData))
		{
			// Save the data in the session.
			$app->setUserState($context . '.data', $validData);

			// Redirect back to the edit screen.
			$this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->get('Errors')), 'error');
			$this->setRedirect(Route::_('index.php?option=com_pwtsitemap&view=menu&layout=edit', false));

			return false;
		}
		else
		{
			$this->setMessage(Text::_('COM_PWTSITEMAP_MENU_SAVE_SUCCESS'));
		}

		// Redirect the user and adjust session state based on the chosen task.
		switch ($task)
		{
			case 'apply':
				// Set the record data in the session.
				$recordId = (int) $validData['id'];

				// Redirect back to the edit screen.
				$this->setRedirect(
					Route::_(
						'index.php?option=com_pwtsitemap&view=menu&layout=edit' .
						$this->getRedirectToItemAppend($recordId),
						false
					)
				);
				break;

			case 'save2new':
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState($context . '.data', null);

				// Redirect back to the edit screen.
				$this->setRedirect(Route::_('index.php?option=com_pwtsitemap&view=menu&layout=edit', false));
				break;

			default:
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState($context . '.data', null);

				// Redirect to the list screen.
				$this->setRedirect(Route::_('index.php?option=com_pwtsitemap&view=menus', false));
				break;
		}

		return true;
	}
}
