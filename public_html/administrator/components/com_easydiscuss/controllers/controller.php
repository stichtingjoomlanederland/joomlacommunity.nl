<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussController extends JControllerLegacy
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->app = JFactory::getApplication();
		$this->input = ED::request();
		$this->doc = JFactory::getDocument();
		$this->config = ED::getJConfig();
		$this->my = JFactory::getUser();
		$this->jconfig = ED::jConfig();

		if ($this->doc->getType() == 'ajax') {
			$this->ajax = ED::ajax();
		}
	}

	/**
	 * Checks if the current viewer can really access this section
	 *
	 * @since   4.0
	 * @access  public

	 */
	public function checkAccess($rule)
	{
		if (!$this->my->authorise($rule , 'com_easydiscuss')) {
			ED::setMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			return $this->app->redirect('index.php?option=com_easydiscuss');
		}
	}

	/**
	 * Default display method which is invoked by Joomla
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function display($params = array() , $urlparams = false)
	{
		$type = $this->doc->getType();
		$name = $this->input->get('view', 'easydiscuss', 'cmd');
		$view = $this->getView($name, $type, '');

		// Once we have the view, set the appropriate layout.
		$layout = $this->input->get('layout', 'default', 'cmd');

		$view->setLayout($layout);

		// For ajax methods, we just load the view methods.
		if ($type == 'ajax') {

			if (!method_exists($view, $layout)) {
				$view->display();
			} else {
				$params = $this->input->get('params', '', 'default');
				$params = json_decode($params);

				call_user_func_array(array($view, $layout), $params);
			}

		} else {

			if ($layout != 'default') {
				if (!method_exists($view, $layout)) {
					$view->display();
				} else {
					call_user_func_array(array($view, $layout), $params);
				}
			} else {
				$view->display();
			}
		}
	}

	public function updatedb()
	{
		require_once JPATH_ROOT . '/administrator/components/com_easydiscuss/install.default.php';
		jimport('joomla.installer.installer');

		$jinstaller = new JInstaller;
		$jinstaller->setPath( 'source', JPATH_ROOT . '/administrator/components/com_easydiscuss' );
		$installer = new EasyDiscussInstaller($jinstaller);
		$installer->updatedb();

		$messages = $installer->getMessages();

		if( empty($messages) )
		{
			ED::setMessageQueue( 'DB Updated', DISCUSS_QUEUE_SUCCESS );
			$this->setRedirect( 'index.php?option=com_easydiscuss', 'DB Updated', 'message' );
		}
		else
		{
			$app = JFactory::getApplication();
			foreach ($installer->getMessages() as $item)
			{
				ED::setMessageQueue( $item , DISCUSS_QUEUE_ERROR );
			}
			$this->setRedirect( 'index.php?option=com_easydiscuss' );
		}

		return;
	}

	public function home()
	{
		$this->setRedirect( 'index.php?option=com_easydiscuss' );
		return;
	}
}
