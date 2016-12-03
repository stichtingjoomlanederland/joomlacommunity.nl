<?php
/**
* @version 1.1.0
* @package RSFiles! 1.1.0
* @copyright (C) 2009 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * RSFiles! Search plugin
 *
 * @package		RSFiles!
 * @subpackage	Search
 * @since		1.6
 */
class plgSearchRSFiles extends JPlugin 
{
	/**
	 * @return array An array of search areas
	 */
	public function onContentSearchAreas() {
		JFactory::getLanguage()->load('plg_search_rsfiles',JPATH_ADMINISTRATOR);
		static $areas = array(
			'rsfiles' => 'PLG_RSFILES_SEARCH_AREA'
			);
		return $areas;
	}

	/**
	 * Content Search method
	 * The sql must return the following fields that are used in a common display
	 * routine: href, title, section, created, text, browsernav
	 * @param string Target search string
	 * @param string mathcing option, exact|any|all
	 * @param string ordering option, newest|oldest|popular|alpha|category
	 * @param mixed An array if the search it to be restricted to areas, null if search all
	 */

	public function onContentSearch($text, $phrase='', $ordering='', $areas = null ) {
		// Can we run the search plugin ?
		if (!$this->canRun())
			return array();
		
		// Load language
		JFactory::getLanguage()->load('plg_search_rsfiles',JPATH_ADMINISTRATOR);
		
		$db			= JFactory::getDBO();
		$query		= $db->getQuery(true);
		$session	= JFactory::getSession();
		$files		= array();
		
		$text = trim($text);
		if ($text == '') {
			return array();
		}
		
		if (is_array($areas)) {
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas()))) {
				return array();
			}
		}
		
		// Get params
		$limit	= $this->params->def('search_limit', 50);
		$iid	= $this->params->get('itemid',0);
		
		if (!empty($iid)) {
			$query->clear()
				->select($db->qn('params'))
				->from($db->qn('#__menu'))
				->where($db->qn('id').' = '.(int) $iid);
			$db->setQuery($query);
			if ($params = $db->loadResult()) {
				$registry = new JRegistry;
				$registry->loadString($params);
				if ($folder = $registry->get('folder')) {
					$iid = 0;
				}
			}
		}
		
		
		$download_folder 	= rsfilesHelper::getConfig('download_folder');
		$global_date	 	= rsfilesHelper::getConfig('global_date');
		$itemid				= $iid ? '&Itemid='.$iid : '';
		$ds					= rsfilesHelper::ds();
		
		$query->clear()
			->select($db->qn('IdFile'))->select($db->qn('FilePath'))
			->select($db->qn('FileName'))->select($db->qn('FileDescription'))
			->select($db->qn('FileType'))->select($db->qn('DateAdded'))
			->select($db->qn('published'))
			->from($db->qn('#__rsfiles_files'))
			->where($db->qn('briefcase').' = 0');
		
		if (!empty($text)) {
			$text = strtolower($text);
			
			switch ($phrase) {
				case 'exact':
					$text = $db->q('%' . $db->escape($text, true) . '%', false);
					$query->where('('.$db->qn('FileName').' LIKE '.$text.' OR '.$db->qn('FileDescription').' LIKE '.$text.' OR '.$db->qn('FilePath').' LIKE '.$text.')');
				break;

				case 'all':
				case 'any':
				default:
					$words	= explode(' ', $text);
					$wheres	= array();
					
					foreach ($words as $word) {
						$word = $db->q('%' . $db->escape($word, true) . '%', false);
						$wheres2   = array();
						$wheres2[] = $db->qn('FileName').' LIKE ' . $word;
						$wheres2[] = $db->qn('FileDescription').' LIKE ' . $word;
						$wheres2[] = $db->qn('FilePath').' LIKE ' . $word;
						$wheres[]  = implode(' OR ', $wheres2);
					}
					
					$where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
					$query->where($where);
				break;
			}
		}
		
		$db->setQuery($query);
		if ($all_files = $db->loadObjectList()) {
			foreach ($all_files as $file) {
				if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
					$file->FilePath = str_replace('/',"\\",$file->FilePath);
				
				$extension = explode($ds,$file->FilePath);
				$extension = end($extension);
				
				if (JFile::stripExt($extension) == '') 
					continue;
				
				$fullpath = $download_folder.$ds.$file->FilePath;
				
				// Check for publishing permission
				if ($file->FileType) {
					$published = $file->published;
				} else {
					$published	= 1;
					$parts		= explode($ds,$file->FilePath);
					
					if (!empty($parts)) {
						foreach ($parts as $i => $part) {
							$query->clear()->select($db->qn('published'))->from($db->qn('#__rsfiles_files'))->where($db->qn('FilePath').' = '.$db->q(implode($ds,$parts)));
							$db->setQuery($query);
							if ($db->loadResult() === 0) $published = 0;
							array_pop($parts);
						}
					}
				}
				
				if (!$published) {
					continue;
				}
				
				if (!rsfilesHelper::permissions('CanView', $file->FilePath)) { 
					continue;
				}
				
				$element = new stdClass();
				$element->type			= $file->FileType ? 'external' : (is_dir($fullpath) ? 'folder' : 'file');
				$element->name			= empty($file->FileName) ? rsfilesHelper::getName($file->FilePath) : $file->FileName;
				$element->description	= empty($file->FileDescription) ? '' : $file->FileDescription;
				$element->time			= empty($file->DateAdded) ? @filemtime($file->FilePath) : $file->DateAdded;
				$element->fullpath		= $file->FileType ? $file->IdFile : $file->FilePath;
				
				if (!empty($text)) {
					$text = strtolower($text);
					$skip = false;
					
					switch ($phrase) {
						case 'exact':
							if (strpos(strtolower($element->name),$text) === FALSE && strpos(strtolower($element->description),$text) === FALSE) 
								$skip = true;
						break;

						case 'all':
						case 'any':
						default:
							$text = $db->escape($text);
							$words = explode(' ', $text);
							
							foreach ($words as $word) {
								$word = $db->escape($word, true);
								if (strpos(strtolower($element->name),$word) === FALSE && strpos(strtolower($element->description),$word) === FALSE) 
									$skip = true;
							}
						break;
					}
					
					if ($skip) 
						continue;
				}
				
				$files[] = $element;
			}
			
			switch ($ordering) {
				case 'oldest':
					usort($files, array('rsfilesHelper', 'sort_time_asc'));
				break;
				
				case 'newest':
				default:
					usort($files, array('rsfilesHelper', 'sort_time_desc'));
				break;
				
				case 'popular':
					usort($files, array('rsfilesHelper', 'sort_hits_desc'));
				break;
				
				case 'alpha':
					$files = rsfilesHelper::sort_array_name($files, 'ASC');
				break;
			}
			
			$files	= array_slice($files,0,$limit);
			$result = array();
			
			if (count($files) > 0) {
				foreach ($files as $file) {
					$link = ($file->type == 'folder') ? 'index.php?option=com_rsfiles&folder='.$file->fullpath : 'index.php?option=com_rsfiles&layout=download&path='.$file->fullpath;
					$date = JFactory::getDate($file->time);
					
					$row				= new stdClass();
					$row->href			= JRoute::_($link.$itemid);			
					$row->title			= $file->name;
					$row->created		= $date->toSql();
					$row->text			= $file->description;
					$row->section		= '';
					$row->browsernav	= 1;
					
					$result[] = $row;
				}
			}
		
			return $result;
		}
		
		return array();
	}
	
	protected function canRun() {
		if (file_exists(JPATH_SITE.'/components/com_rsfiles/helpers/rsfiles.php')) {
			require_once JPATH_SITE.'/components/com_rsfiles/helpers/rsfiles.php';
			return true;
		}
		
		return false;
	}
}