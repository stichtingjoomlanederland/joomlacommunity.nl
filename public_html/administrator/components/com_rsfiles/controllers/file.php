<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// No direct access
defined('_JEXEC') or die('Restricted access');

class rsfilesControllerFile extends JControllerForm
{
	/**
	 * Class constructor.
	 *
	 * @param   array  $config  A named array of configuration variables.
	 *
	 * @since	1.6
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Method to edit an existing record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key
	 * (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if access level check and checkout passes, false otherwise.
	 *
	 * @since   12.2
	 */
	public function edit($key = null, $urlVar = null) {
		$app 		= JFactory::getApplication();
		$context	= "$this->option.edit.$this->context";
		$cid		= $app->input->get('cid',array(),'array');
		
		if ($cid) {
			$config		= rsfilesHelper::getConfig();
			$ds			= rsfilesHelper::ds();
			$path		= urldecode($cid[0]);
			$urlVar		= 'IdFile';
			$root		= $config->{rsfilesHelper::getRoot().'_folder'};
			$briefcase	= rsfilesHelper::getRoot() == 'briefcase';
			
			// Remove trailing slash
			if (substr($root, -1) == $ds) {
				$root = substr($root, 0, -1);
			}
			
			// Remove leading slash
			if (!empty($path) && substr($path,0,1) == $ds) {
				$path = ltrim($path, $ds);
			}
			
			$fullpath	= $root.$ds.$path;
			$recordId	= rsfilesHelper::getRecordId($path, $fullpath, $briefcase);
			
			$this->holdEditId($context, $recordId);
			$app->setUserState($context . '.data', null);

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);

			return true;
		} else {
			return parent::edit($key, $urlVar);
		}
	}
	
	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   12.2
	 */
	protected function getRedirectToListAppend() {
		$append = parent::getRedirectToListAppend();
		$jform	= JFactory::getApplication()->input->get('jform',array(),'array');
		
		if ($jform['FileType']) {
			$path = $jform['FileParent'];
			
			if ($path == 'root') {
				$path = '';
			}
			
		} else {
			$path	= $jform['FilePath'];
			$path	= explode(rsfilesHelper::ds(),$path);
			
			unset($path[(count($path)-1)]);
			$path = implode(rsfilesHelper::ds(),$path);
		}
		
		if ($path) {
			$append .= '&folder='.urlencode($path);
		}

		return $append;
	}
	
	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   12.2
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id') {
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);
		$path	= JFactory::getApplication()->input->getString('path','');
		
		if ($path == rsfilesHelper::root()) {
			$path = 'root';
		} else {
			$root	= rsfilesHelper::root(true);
			$path	= str_replace($root,'',$path);
		}
		
		if (!empty($path) && empty($recordId)) {
			$append .= '&parent='.urlencode($path);
		}
		
		return $append;
	}
	
	/**
	 *	Method to save a mirror
	 */
	public function mirror() {
		// Get the model
		$model	= $this->getModel('File');
		$data	= new stdClass();
		
		$data->id = $model->mirror();
		
		rsfilesHelper::showResponse(true,$data);
	}
	
	/**
	 *	Method to delete a mirror
	 */
	public function deletemirror() {
		// Get the model
		$model	= $this->getModel('File');
		$id		= JFactory::getApplication()->input->getInt('id',0);
		$data	= new stdClass();
		
		$success = $model->deletemirror($id);
		
		rsfilesHelper::showResponse($success,null);
	}
	
	/**
	 *	Method to get a mirror details
	 */
	public function getmirror() {
		// Get the model
		$model	= $this->getModel('File');
		$id		= JFactory::getApplication()->input->getInt('id',0);
		$data	= $model->getmirror($id);
		
		rsfilesHelper::showResponse(true,$data);
	}
	
	/**
	 *	Method to delete a screenshot
	 */
	public function deletescreenshot() {
		// Get the model
		$model	= $this->getModel('File');
		$id		= JFactory::getApplication()->input->getInt('id',0);
		$data	= new stdClass();
		$success= true;
		
		if (!$model->deletescreenshot($id)) {
			$success = false;
			$data->message = JText::_('COM_RSFILES_SCREENSHOT_DELETE_ERROR');
		}
		
		rsfilesHelper::showResponse($success,$data);
	}
	
	/**
	 *	Method to delete the file thumb
	 */
	public function deletethumb() {
		// Get the model
		$model	= $this->getModel('File');
		$id		= JFactory::getApplication()->input->getInt('id',0);
		
		if (!$model->deletethumb($id)) {
			$this->setMessage($model->getError());
		}
		
		$this->setRedirect('index.php?option=com_rsfiles&view=file&layout=edit&IdFile='.$id);
	}
	
	/**
	 *	Method to delete the file preview
	 */
	public function deletepreview() {
		// Get the model
		$model	= $this->getModel('File');
		$id		= JFactory::getApplication()->input->getInt('id',0);
		
		if (!$model->deletepreview($id)) {
			$this->setMessage($model->getError());
		}
		
		$this->setRedirect('index.php?option=com_rsfiles&view=file&layout=edit&IdFile='.$id);
	}
}