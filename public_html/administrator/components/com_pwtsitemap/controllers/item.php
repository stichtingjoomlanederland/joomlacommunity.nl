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

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

/**
 * PWT Sitemap item controller
 *
 * @since  1.0.0
 */
class PwtSitemapControllerItem extends FormController
{
	/**
	 * Method to run batch operations.
	 *
	 * @param   BaseDatabaseModel  $model  The model of the component being processed.
	 *
	 * @return  boolean     True if successful, false otherwise and internal error is set.
	 *
	 * @since   1.0.0
	 */
	public function batch($model = null)
	{
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Set the model
		$model = $this->getModel('Item', '', []);

		// Preset the redirect
		$this->setRedirect(Route::_('index.php?option=com_pwtsitemap&view=items' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}
}
