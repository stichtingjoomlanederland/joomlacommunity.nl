<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.filesystem.file');

class RseventsproTableSpeaker extends JTable
{
	/**
	 * @param	JDatabase	A database connector object
	 */
	public function __construct($db) {
		parent::__construct('#__rseventspro_speakers', 'id', $db);
	}
	
	public function uploadImage() {
		jimport('joomla.filesystem.file');
		
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$images = JFactory::getApplication()->input->files->get('jform',array(),'array');
		$image 	= $images['image'];
		$path	= JPATH_SITE.'/components/com_rseventspro/assets/images/speakers/';
		$width	= rseventsproHelper::getConfig('speaker_icon_width', 'int', 100);
		$height	= rseventsproHelper::getConfig('speaker_icon_height', 'int', 150);
		
		if ($image['size'] > 0 && $image['error'] == 0) {
			$ext = strtolower(JFile::getExt($image['name']));
			
			if (in_array($ext,array('jpg','jpeg','png'))) {
			
				$query->select($db->qn('image'))
					->from($db->qn('#__rseventspro_speakers'))
					->where($db->qn('id').' = '.(int) $this->id);
				$db->setQuery($query);
				if ($theimage = $db->loadResult()) {
					if (file_exists($path.$theimage)) {
						JFile::delete($path.$theimage);
					}
				}
				
				require_once JPATH_SITE.'/components/com_rseventspro/helpers/phpthumb/phpthumb.class.php';
				
				$thumb = new phpThumb();
				$thumb->src = $image['tmp_name'];
				$thumb->w = $width;
				$thumb->h = $height;
				$thumb->zc = 'C';
				$thumb->q = 75;
				$thumb->config_output_format = $ext;
				$thumb->config_error_die_on_error = false;
				$thumb->config_cache_disable_warning = true;
				$thumb->config_allow_src_above_docroot = true;
				$thumb->cache_filename = $path.$this->id.'.'.$ext;
				
				if ($thumb->GenerateThumbnail()) {
					$thumb->RenderToFile($thumb->cache_filename);
					
					$query->clear()
						->update($db->qn('#__rseventspro_speakers'))
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
	
	/**
	 * Method to delete a node and, optionally, its child nodes from the table.
	 *
	 * @param   integer  $pk        The primary key of the node to delete.
	 * @param   boolean  $children  True to delete child nodes, false to move them up a level.
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     http://docs.joomla.org/JTable/delete
	 * @since   2.5
	 */
	public function delete($pk = null, $children = false) {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		
		$query->select($db->qn('image'))
			->from($db->qn('#__rseventspro_speakers'))
			->where($db->qn('id').' = '.$db->q($pk));
		$db->setQuery($query);
		if ($image = $db->loadResult()) {
			jimport('joomla.filesystem.file');
			if (file_exists(JPATH_SITE.'/components/com_rseventspro/assets/images/speakers/'.$image))
				JFile::delete(JPATH_SITE.'/components/com_rseventspro/assets/images/speakers/'.$image);
		}
		
		return parent::delete($pk, $children);
	}
}