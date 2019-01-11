<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

/**
 * PWT Sitemap item controller
 *
 * @since  1.0.0
 */
class PwtSitemapControllerItem extends JControllerForm
{
	/**
	 * Method to run batch operations.
	 *
	 * @param   JModelLegacy  $model  The model of the component being processed.
	 *
	 * @return	boolean	 True if successful, false otherwise and internal error is set.
	 *
	 * @since	1.0.0
	 */
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model = $this->getModel('Item', '', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_pwtsitemap&view=items' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}
}
