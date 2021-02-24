<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;

/**
 * Profile controller.
 *
 * @package  JDiDEAL
 * @since    4.0.0
 */
class JdidealgatewayControllerProfile extends FormController
{
	/**
	 * Change the active iDEAL type.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 *
	 * @since   4.0.0
	 */
	public function change(): void
	{
		/** @var CMSApplication $app */
		$app  = Factory::getApplication();
		$form = $this->input->get('jform', [], 'array');

		if (array_key_exists('psp', $form))
		{
			$app->setUserState('profile.psp', $form['psp']);
		}

		$id = $this->input->getInt('id');
		$app->redirect('index.php?option=com_jdidealgateway&task=profile.edit&id=' . $id);
	}
}
