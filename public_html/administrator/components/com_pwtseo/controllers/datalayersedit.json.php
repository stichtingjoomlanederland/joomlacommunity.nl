<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Response\JsonResponse;

defined('_JEXEC') or die;

/**
 * DatalayersEdit controller class
 *
 * @since  1.3.1
 */
class PWTSEOControllerDatalayersEdit extends AdminController
{
	/**
	 * Removes an item.
	 *
	 * @return  void
	 *
	 * @since   1.3.1
	 */
	public function delete()
	{
		$id      = $this->input->get('context_id', 0, 'int');
		$context = $this->input->get('context', 0, 'cmd');

		/** @var PWTSEOModelDataLayersEdit $model */
		$model = $this->getModel('DataLayersEdit', 'PWTSEOModel');

		$pks = array(
			array(
				'context_id' => $id,
				'context'    => $context
			)
		);

		if ($model->delete($pks))
		{
			echo new JsonResponse(true);
		}
		else
		{
			// If we couldn't find it, we didn't actually get an error or we choose to ignore it
			$errors = $model->getErrors();

			if ($errors[0] === false)
			{
				echo new JsonResponse(true);
			}
			else
			{
				echo new JsonResponse(false, $errors, true);
			}
		}
	}
}
