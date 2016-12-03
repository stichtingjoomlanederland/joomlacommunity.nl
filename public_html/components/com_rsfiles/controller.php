<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' ); 
jimport('joomla.application.component.controller');

class rsfilesController extends JControllerLegacy
{
	/**
	 *	Main constructor
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 *	Set the captcha image
	 */
	public function captcha() {
		$config = rsfilesHelper::getConfig();
		
		if ($config->captcha_enabled == 1) {
			ob_end_clean();
			$captcha = new JSecurImage();
			
			$captcha->num_lines = $config->captcha_lines ? 8 : 0;
			$captcha_characters = $config->captcha_characters;
			$captcha->code_length = $captcha_characters;
			$captcha->image_width = 30*$captcha_characters + 50;
			$captcha->show();
		}
		die();
	}
	
	/**
	 *	Get content
	 */
	public function filepath() {
		rsfilesHelper::filepath();
	}
	
	/**
	 *	Method to display the preview
	 */
	public function preview() {
		$id = JFactory::getApplication()->input->getInt('id',0);
		return rsfilesHelper::preview($id);
	}
	
	public function approve() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$app	= JFactory::getApplication();
		$hash	= $app->input->getString('hash','');
		$where	= array();
		
		if ($moderation_email = rsfilesHelper::getMessage('moderate')) {
			if (!empty($moderation_email->to)) {
				if ($emails = explode(',',$moderation_email->to)) {
					
					$query->clear()
						->select($db->qn('IdFile'))->select($db->qn('FilePath'))
						->from($db->qn('#__rsfiles_files'))
						->where($db->qn('briefcase').' = 0')
						->where($db->qn('published').' = 0');
					
					foreach ($emails as $email) {
						$email	= trim($email);
						
						if (empty($email)) {
							continue;
						}
						
						$where[] = 'MD5(CONCAT('.$db->q($email).','.$db->qn('IdFile').')) = '.$db->q($hash);
					}
					
					if ($where) {
						$query->where(implode(' OR ',$where));
					}
					
					$db->setQuery($query);
					if ($file = $db->loadObject()) {
						$query->clear()
							->update($db->qn('#__rsfiles_files'))
							->set($db->qn('published').' = 1')
							->where($db->qn('IdFile').' = '.$db->q($file->IdFile));
						$db->setQuery($query);
						$db->execute();
						
						$app->enqueueMessage(JText::_('COM_RSFILES_FILE_APPROVED'));
						return $this->setRedirect(JRoute::_('index.php?option=com_rsfiles&layout=download&path='.rsfilesHelper::encode($file->FilePath),false));
					}
				}
			}
		}
		
		$app->enqueueMessage(JText::_('COM_RSFILES_FILE_APPROVED_ERROR'));
		return $this->setRedirect(JRoute::_('index.php?option=com_rsfiles',false));
	}
}