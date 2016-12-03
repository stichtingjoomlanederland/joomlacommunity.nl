<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesModelEmail extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller email.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_RSFILES';
	
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true) {
		$jinput = JFactory::getApplication()->input;
		
		// Get the form.
		$form = $this->loadForm('com_rsfiles.email', 'email', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
			return false;
		
		return $form;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData() {
		$data	= $this->getEmail();
		$data['language'] = $this->getLanguage();
		
		return $data;
	}
	
	/**
	 * Method to get messages types.
	 *
	 * @return	mixed	The messages types.
	 * @since	1.6
	 */
	public function getTypes() {
		return array('admin','download','upload','report','briefcaseupload','moderate');
	}
	
	/**
	 * Method to get the current message type.
	 *
	 * @return	mixed	The current message type.
	 * @since	1.6
	 */
	public function getType() {
		return JFactory::getApplication()->input->getCmd('type');
	}
	
	/**
	 * Method to get the current selected message.
	 *
	 * @return	mixed	The current selected message.
	 * @since	1.6
	 */
	public function getLanguage() {
		$jform	= JFactory::getApplication()->input->get('jform',array(),'array');
		return !empty($jform['language']) ? $jform['language'] : JFactory::getLanguage()->getTag();
	}
	
	/**
	 * Method to get email details.
	 *
	 * @return	mixed	The email details.
	 * @since	1.6
	 */
	protected function getEmail() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$lang	= $this->getLanguage();
		$type	= $this->getType();
		
		$query->clear()
			->select($db->qn('subject',$type.'_subject'))->select($db->qn('message',$type.'_message'))
			->select($db->qn('enable',$type.'_enable'))->select($db->qn('mode',$type.'_mode'))
			->select($db->qn('to',$type.'_to'))
			->from($db->qn('#__rsfiles_emails'))
			->where($db->qn('type').' = '.$db->q($type))
			->where($db->qn('lang').' = '.$db->q($lang));
		
		$db->setQuery($query);
		$email = $db->loadObject();
		
		if (empty($email)) {
			$query->clear()
				->select($db->qn('subject',$type.'_subject'))->select($db->qn('message',$type.'_message'))
				->select($db->qn('enable',$type.'_enable'))->select($db->qn('mode',$type.'_mode'))
				->select($db->qn('to',$type.'_to'))
				->from($db->qn('#__rsfiles_emails'))
				->where($db->qn('type').' = '.$db->q($type))
				->where($db->qn('lang').' = '.$db->q('en-GB'));
			
			$db->setQuery($query);
			$email = $db->loadObject();
			
			if (empty($email)) {
				$email = new stdClass();
				$email->{$type.'_subject'} = '';
				$email->{$type.'_message'} = '';
				$email->{$type.'_enable'} = 1;
				$email->{$type.'_mode'} = 1;
				$email->{$type.'_to'} = '';
			}
		}
		
		return (array) $email;
	}
	
	/**
	 * Method to save email details.
	 *
	 * @return	boolean		True if success.
	 * @since	1.6
	 */
	public function save($data) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$type	= $data['type'];
		$types	= $this->getTypes();
		
		if (!in_array($type,$types))
			return false;
		
		// Prepare data for insertion
		$language	= isset($data['language']) ? $data['language'] : $this->getLanguage();
		$enable		= isset($data[$type.'_enable']) ? $data[$type.'_enable'] : 1;
		$to			= isset($data[$type.'_to']) ? $data[$type.'_to'] : '';
		$subject	= isset($data[$type.'_subject']) ? $data[$type.'_subject'] : '';
		$mode		= isset($data[$type.'_mode']) ? $data[$type.'_mode'] : 1;
		$message	= isset($data[$type.'_message']) ? $data[$type.'_message'] : '';
		
		$query->clear()
			->select($db->qn('id'))
			->from($db->qn('#__rsfiles_emails'))
			->where($db->qn('lang').' = '.$db->q($language))
			->where($db->qn('type').' = '.$db->q($type));
		$db->setQuery($query);
		$exists = $db->loadResult();
		
		if ($exists) {
			$query->clear()
				->update($db->qn('#__rsfiles_emails'))
				->set($db->qn('enable').' = '.$db->q($enable))
				->set($db->qn('to').' = '.$db->q($to))
				->set($db->qn('subject').' = '.$db->q($subject))
				->set($db->qn('mode').' = '.$db->q($mode))
				->set($db->qn('message').' = '.$db->q($message))
				->where($db->qn('id').' = '.(int) $exists);
		} else {
			$query->clear()
				->insert($db->qn('#__rsfiles_emails'))
				->set($db->qn('enable').' = '.$db->q($enable))
				->set($db->qn('to').' = '.$db->q($to))
				->set($db->qn('subject').' = '.$db->q($subject))
				->set($db->qn('mode').' = '.$db->q($mode))
				->set($db->qn('message').' = '.$db->q($message))
				->set($db->qn('lang').' = '.$db->q($language))
				->set($db->qn('type').' = '.$db->q($type));
		}
		
		$db->setQuery($query);
		$db->execute();
		
		return true;
	}
}