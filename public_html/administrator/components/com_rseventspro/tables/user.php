<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproTableUser extends JTable
{
	/**
	 * @param	JDatabase	A database connector object
	 */
	public function __construct($db) {
		parent::__construct('#__rseventspro_user_info', 'id', $db);
	}
	
	public function check() {
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		
		$query->select('COUNT('.$db->qn('id').')')
			->from($db->qn('#__rseventspro_user_info'))
			->where($db->qn('id').' = '.(int) $this->id);
		$db->setQuery($query);
		$count = $db->loadResult();
		
		if (!$count) {
			$query->clear()
				->insert($db->qn('#__rseventspro_user_info'))
				->set($db->qn('description').' = '.$db->q(''))
				->set($db->qn('id').' = '.(int) $this->id);
			$db->setQuery($query);
			$db->execute();
		}
		
		return true;
	}
	
	public function delete($pk = null, $children = false) {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		
		$query->select($db->qn('image'))
			->from($db->qn('#__rseventspro_user_info'))
			->where($db->qn('id').' = '.$db->q($pk));
		$db->setQuery($query);
		if ($image = $db->loadResult()) {
			jimport('joomla.filesystem.file');
			if (file_exists(JPATH_SITE.'/components/com_rseventspro/assets/images/users/'.$image))
				JFile::delete(JPATH_SITE.'/components/com_rseventspro/assets/images/users/'.$image);
		}
		
		return parent::delete($pk, $children);
	}
	
	public function uploadImage() {
		jimport('joomla.filesystem.file');
		
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$images = JFactory::getApplication()->input->files->get('jform',array(),'array');
		$image 	= $images['image'];
		$path	= JPATH_SITE.'/components/com_rseventspro/assets/images/users/';
		$width	= rseventsproHelper::getConfig('user_icon_width', 'int', 100);
		
		if ($image['size'] > 0 && $image['error'] == 0) {
			$ext = strtolower(JFile::getExt($image['name']));
			
			if (in_array($ext,array('jpg','jpeg','png'))) {
			
				$query->select($db->qn('image'))
					->from($db->qn('#__rseventspro_user_info'))
					->where($db->qn('id').' = '.(int) $this->id);
				$db->setQuery($query);
				if ($theimage = $db->loadResult()) {
					if (file_exists($path.$theimage)) {
						JFile::delete($path.$theimage);
					}
				}
				
				if (rseventsproHelper::resize($image['tmp_name'], $width, $path.$this->id.'.'.$ext)) {
					$query->clear()
						->update($db->qn('#__rseventspro_user_info'))
						->set($db->qn('image').' = '.$db->q($this->id.'.'.$ext))
						->where($db->qn('id').' = '.(int) $this->id);
					$db->setQuery($query);
					$db->execute();
				}
			} else {
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_RSEVENTSPRO_WRONG_EXTENSION', $image['name']), 'error');
			}
		}
	}
}