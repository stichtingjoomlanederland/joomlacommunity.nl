<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsController extends JControllerLegacy {
	
	public function __construct() {
		parent::__construct();
		// Set the database object
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_rscomments/tables');
	}
	
	public function main() {
		$this->setRedirect('index.php?option=com_rscomments');
	}
	
	public function stats() {
		$model = $this->getModel('rscomments');
		echo json_encode($model->getStats());
		
		JFactory::getApplication()->close();
	}
	
	// Download file
	public function download() {
		$app	= JFactory::getApplication();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= $app->input->getInt('id',0);
		
		if ($id) {
			$query->clear()
				->select($db->qn('file'))
				->from($db->qn('#__rscomments_comments'))
				->where($db->qn('IdComment').' = '.$id);
			
			$db->setQuery($query);
			$file = $db->loadResult();

			$download_folder	= JPATH_SITE.'/components/com_rscomments/assets/files/';
			$fullpath			= $download_folder.$file;			
			
			if (strpos(realpath($fullpath), realpath($download_folder)) !== 0) {
				throw new Exception(JText::_('COM_RSCOMMENTS_ACCESS_DENIED'));
			}
			
			if (is_file($fullpath)) {
				@ob_end_clean();
				$filename = basename($fullpath);
				header("Cache-Control: public, must-revalidate");
				header('Cache-Control: pre-check=0, post-check=0, max-age=0');
				header("Pragma: no-cache");
				header("Expires: 0"); 
				header("Content-Description: File Transfer");
				header("Expires: Sat, 01 Jan 2000 01:00:00 GMT");
				if (preg_match('#Opera#', $_SERVER['HTTP_USER_AGENT']))
					header("Content-Type: application/octetstream"); 
				else 
					header("Content-Type: application/octet-stream");
				header("Content-Length: ".(string) filesize($fullpath));
				header('Content-Disposition: attachment; filename="'.$filename.'"');
				header("Content-Transfer-Encoding: binary\n");
				RSCommentsHelper::readfile_chunked($fullpath);
				$app->close();
			} else {
				throw new Exception(JText::_('COM_RSCOMMENTS_ACCESS_DENIED'));
			}
		}
		$app->close();
	}
}