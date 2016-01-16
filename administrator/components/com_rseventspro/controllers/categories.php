<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class rseventsproControllerCategories extends JControllerAdmin
{
	/**
	 * Proxy for getModel
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  The array of possible config values. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Category', $prefix = 'rseventsproModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Rebuild the nested set tree.
	 *
	 * @return  bool  False on failure or error, true on success.
	 *
	 * @since   1.6
	 */
	public function rebuild() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$this->setRedirect(JRoute::_('index.php?option=com_rseventspro&view=categories', false));

		$model = $this->getModel();

		if ($model->rebuild()) {
			// Rebuild succeeded.
			$this->setMessage(JText::_('COM_RSEVENTSPRO_CATEGORIES_REBUILD_SUCCESS'));

			return true;
		} else {
			// Rebuild failed.
			$this->setMessage(JText::_('COM_RSEVENTSPRO_CATEGORIES_REBUILD_FAILURE'));

			return false;
		}
	}

	/**
     * Method to save the submitted ordering values for records via AJAX.
     *
     * @access public
     */
    public function saveOrderAjax() {
		JSession::checkToken() or jexit( JText::_('JINVALID_TOKEN') );
		
		$app = JFactory::getApplication();
		$jinput = $app->input;
		
		// Get the arrays from the Request
		$pks			= $jinput->post->get('cid', null, 'array');
		$order			= $jinput->post->get('order', null, 'array');
		$originalOrder	= explode(',', $jinput->getString('original_order_values',''));
		
		// Make sure something has changed
		if ($order !== $originalOrder) {
			// Get the model.
			$model = $this->getModel();
			
			// Save the ordering.
			$return = $model->saveorder($pks, $order);
			
			if ($return) {
				echo 1;
			}
		}
		
		// Close the application
		$app->close();
    }
}