<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
require_once JPATH_SITE.'/components/com_rsfiles/helpers/version.php';

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class rsfilesHelper {
	
	// Load Config
	public static function getConfig($what = null) {
		static $config;
		
		if (!is_object($config)) {
			$db		= JFactory::getDbo();
			$config	= new stdClass();
			
			$query = $db->getQuery(true)->select($db->qn('ConfigName'))->select($db->qn('ConfigValue'))->from($db->qn('#__rsfiles_config'));
			$db->setQuery($query);
			if ($configuration = $db->loadObjectList()) {
				foreach ($configuration as $option) {
					$config->{$option->ConfigName} = $option->ConfigValue;
				}
			}
		}
		
		if ($what != null) {
			if (isset($config->{$what})) 
				return $config->{$what};
			else return false;
		} else {
			return $config;
		}
	}
	
	// Get directory separator
	public static function ds() {
		return DIRECTORY_SEPARATOR;
	}
	
	// Get update code
	public static function genKeyCode() {
		$license = rsfilesHelper::getConfig('license_code');
		$version = new RSFilesVersion();
		return md5($license.$version->key);
	}
	
	// Check joomla version
	public static function isJ3() {
		return version_compare(JVERSION, '3.0', '>=');
	}
	
	// Load scripts 
	public static function initialize($from = 'admin') {
		$doc	= JFactory::getDocument();
		
		self::loadjQuery();
		
		self::tooltipLoad();
		
		if ($from == 'admin') {
			if (self::isJ3()) {
				JHtml::_('formbehavior.chosen', 'select');
				$doc->addStyleSheet(JURI::root(true).'/administrator/components/com_rsfiles/assets/css/j3.css?v='.RSF_RS_REVISION);
			} else {
				$doc->addStyleSheet(JURI::root(true).'/administrator/components/com_rsfiles/assets/css/j2.css?v='.RSF_RS_REVISION);
			}
			
			$doc->addStyleSheet(JURI::root(true).'/administrator/components/com_rsfiles/assets/css/style.css?v='.RSF_RS_REVISION);
			$doc->addScript(JURI::root(true).'/administrator/components/com_rsfiles/assets/js/scripts.js?v='.RSF_RS_REVISION);
		} else {
			JHtml::_('behavior.modal','.rs_modal');
			
			$doc->addStyleSheet(JURI::root(true).'/components/com_rsfiles/assets/css/style.css?v='.RSF_RS_REVISION);
			$doc->addScript(JURI::root(true).'/components/com_rsfiles/assets/js/script.js?v='.RSF_RS_REVISION);
			
			$doc->addStyleSheet(JURI::root(true).'/components/com_rsfiles/assets/icons/rsicon.css?v='.RSF_RS_REVISION);
			
			if (self::isJ3()) {
				if (self::getConfig('load_bootstrap')) {
					JHtml::_('bootstrap.framework');
					JHtml::_('bootstrap.loadCss', true);
				}
				$doc->addStyleSheet(JURI::root(true).'/components/com_rsfiles/assets/css/j3.css?v='.RSF_RS_REVISION);
			} else {
				if (self::getConfig('load_bootstrap')) {
					$doc->addStyleSheet(JURI::root(true).'/components/com_rsfiles/assets/css/j2.css?v='.RSF_RS_REVISION);
				}
			}
		}
	}
	
	// Load jQuery framework
	public static function loadjQuery() {
		$admin	= JFactory::getApplication()->isAdmin();
		$config = self::getConfig();
		$enable = $admin ? $config->load_backend_jquery : $config->load_frontend_jquery;
		
		if ($enable) {
			if (self::isJ3()) {
				JHtml::_('jquery.framework', true);
			} else {
				$doc = JFactory::getDocument();
				$doc->addScript(JURI::root(true).'/components/com_rsfiles/assets/js/jquery-1.11.1.min.js');
				$doc->addScript(JURI::root(true).'/components/com_rsfiles/assets/js/jquery.noconflict.js');
			}
		}
	}
	
	// Set title for tooltip
	// DEPRECATED
	public static function title($title) {
		return $title.(rsfilesHelper::isJ3() ? '' : '::');
	}
	
	// Prepare submenu
	public static function subMenu() {
		$jinput = JFactory::getApplication()->input;
		$view   = $jinput->getCmd('view');
		$layout = $jinput->getCmd('layout');
		$views  = array('files','licenses','groups','statistics','settings','updates');
		
		JHtmlSidebar::addEntry(JText::_('COM_RSFILES_SUBMENU_DASHBOARD'), 'index.php?option=com_rsfiles',(empty($view) && empty($layout)));
		
		foreach ($views as $theview) {
			JHtmlSidebar::addEntry(JText::_('COM_RSFILES_SUBMENU_'.strtoupper($theview)), 'index.php?option=com_rsfiles&view='.$theview, ($theview == $view));
		}
	}
	
	// Close modal
	public static function modalClose($script = true) {
		$html = array();
		
		if ($script) $html[] = '<script type="text/javascript">';
		$html[] = 'window.parent.SqueezeBox.close();';
		if ($script) $html[] = '</script>';
		
		return implode("\n",$html);
	}
	
	// Display date
	public static function showDate($date) {
		$date_format = self::getConfig('global_date');
		return JHTML::date($date, $date_format);
	}
	
	// Check if the current file is external
	public static function external($id) {
		static $cache = array();
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= (int) $id;
		
		if (empty($id)) {
			return false;
		}
		
		if (empty($cache[$id])) {		
			$query->clear()
				->select('COUNT('.$db->qn('IdFile').')')
				->from($db->qn('#__rsfiles_files'))
				->where($db->qn('IdFile').' = '.$id)
				->where($db->qn('FileType').' = 1');
			
			$db->setQuery($query);
			$cache[$id] = (bool) $db->loadResult();
		}
		
		return $cache[$id];
	}
	
	// Sort time ASC
	public static function sort_time_asc($a, $b) {
		if ($a->time == $b->time) return 0;
		return ($a->time < $b->time) ? -1 : 1;
	}
	
	// Sort time DESC
	public static function sort_time_desc($a, $b) {
		if ($a->time == $b->time) return 0;
		return ($a->time < $b->time) ? 1 : -1;
	}
	
	// Sort name DESC - ASC
	public static function sort_array_name($array, $dir = 'ASC') {
		$tmp = array();
		foreach ($array as $i => $element) {
			$name = !empty($element->filename) ? $element->filename : $element->name;
			$tmp[$i] = strtolower($name);
		}
		
		strtoupper($dir) == 'ASC' ? asort($tmp) : arsort($tmp);
		
		foreach ($tmp as $i => $element)
			$tmp[$i] = $array[$i];
		
		return $tmp;
	}
	
	// Sort hits ASC
	public static function sort_hits_asc($a,$b) {
		if ($a->hits == $b->hits) return 0;
		return ($a->hits < $b->hits) ? -1 : 1;
	}
	
	// Sort hits DESC
	public static function sort_hits_desc($a, $b) {
		if ($a->hits == $b->hits) return 0;
		return ($a->hits < $b->hits) ? 1 : -1;
	}
	
	// Format size
	public static function formatBytes($bytes, $precision = 2) {
		$units	= array('B', 'KB', 'MB', 'GB', 'TB');
		$bytes	= max($bytes, 0);
		$pow	= floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow	= min($pow, count($units) - 1);
		$bytes /= pow(1024, $pow);
		
		return round($bytes, $precision) . ' ' . $units[$pow];
	}
	
	// Make the name of a file/folder safe
	public static function makeSafe($text) {
		jimport('joomla.filesystem.file');
		$text = JFile::makeSafe($text);
		$text = trim($text);
		$text = str_replace('  ',' ',$text);
		
		return $text;
	}
	
	// Remove file / folder details
	public static function remove($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		// Delete mirrors
		$query->clear()->delete()->from($db->qn('#__rsfiles_mirrors'))->where($db->qn('IdFile').' = '.(int) $id);
		$db->setQuery($query);
		$db->execute();
		
		// Delete reports
		$query->clear()->delete()->from($db->qn('#__rsfiles_reports'))->where($db->qn('IdFile').' = '.(int) $id);
		$db->setQuery($query);
		$db->execute();
		
		// Delete statistics
		$query->clear()->delete()->from($db->qn('#__rsfiles_statistics'))->where($db->qn('IdFile').' = '.(int) $id);
		$db->setQuery($query);
		$db->execute();
		
		// Delete screenshots
		$query->clear()->select($db->qn('Path'))->from($db->qn('#__rsfiles_screenshots'))->where($db->qn('IdFile').' = '.(int) $id);
		$db->setQuery($query);
		
		if ($screenshots = $db->loadColumn()) {
			jimport('joomla.filesystem.file');
			foreach ($screenshots as $screenshot)
				JFile::delete(JPATH_SITE.'/components/com_rsfiles/images/screenshots/'.$screenshot);
			
			$query->clear()->delete()->from($db->qn('#__rsfiles_screenshots'))->where($db->qn('IdFile').' = '.(int) $id);
			$db->setQuery($query);
			$db->execute();
		}
		
		// Delete thumb and preview image
		$query->clear()->select($db->qn('FileThumb'))->select($db->qn('preview'))->from($db->qn('#__rsfiles_files'))->where($db->qn('IdFile').' = '.(int) $id);
		$db->setQuery($query);
		if ($file = $db->loadObject()) {
			if (!empty($file->FileThumb)) {
				if (file_exists(JPATH_SITE.'/components/com_rsfiles/images/thumbs/files/'.$file->FileThumb)) {
					JFile::delete(JPATH_SITE.'/components/com_rsfiles/images/thumbs/files/'.$file->FileThumb);
				}
			}
			
			if (!empty($file->preview)) {
				if (file_exists(JPATH_SITE.'/components/com_rsfiles/images/preview/'.$file->preview)) {
					JFile::delete(JPATH_SITE.'/components/com_rsfiles/images/preview/'.$file->preview);
				}
			}
		}
		
		return true;
	}
	
	// Get recordId
	public static function getRecordId($path, $fullpath, $briefcase) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('IdFile'))
			->from($db->qn('#__rsfiles_files'))
			->where($db->qn('FilePath').' = '.$db->q($path))
			->where($db->qn('briefcase').' = '.$db->q((int) $briefcase));
		$db->setQuery($query);
		if ($id = (int) $db->loadResult()) {
			return $id;
		} else {
			$query->clear()
				->insert($db->qn('#__rsfiles_files'))
				->set($db->qn('DateAdded').' = '.$db->q(JFactory::getDate()->toSql()))
				->set($db->qn('published').' = 1')
				->set($db->qn('FilePath').' = '.$db->q($path))
				->set($db->qn('briefcase').' = '.$db->q((int) $briefcase));
				
			if (is_file($fullpath))
				$query->set($db->qn('hash').' = '.$db->q(md5_file($fullpath)));
			
			$db->setQuery($query);
			$db->execute();
			return (int) $db->insertid();
		}
	}
	
	// Get file type
	public static function getType($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$root	= rsfilesHelper::root(true);
		
		if (!empty($id)) {
			$query->clear()
				->select($db->qn('FilePath'))
				->from($db->qn('#__rsfiles_files'))
				->where($db->qn('IdFile').' = '.(int) $id);
			$db->setQuery($query);
			$path = $db->loadResult();
			
			if (is_dir($root.$path))
				$type = 'folder';
			elseif (is_file($root.$path))
				$type = 'file';
			elseif (rsfilesHelper::external($id))
				$type = 'external';
			else $type = 'file';
		} else {
			$type = 'external';
		}
		
		return $type;
	}
	
	// Method to show sync response
	public static function showResponse($success, $data=null) {
		$app 		= JFactory::getApplication();
		$document 	= JFactory::getDocument();
		
		// set JSON encoding
		$document->setMimeEncoding('application/json');
		
		// compute the response
		$response = new stdClass();
		$response->success = $success;
		if ($data) {
			$response->data = $data;
		}
		
		// show the response
		echo json_encode($response);
		
		// close
		$app->close();
	}
	
	// Method to upload file related images
	public static function upload($id) {
		jimport('joomla.filesystem.file');
		
		$app		= JFactory::getApplication();
		$input		= $app->input;
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$thumbnail	= $input->files->get('thumb',array(),'array');
		$screens	= $input->files->get('screenshot',array(),'array');
		$preview	= $input->files->get('preview',array(),'array');
		$path		= JPATH_SITE.'/components/com_rsfiles/images/screenshots/';
		$uniqid		= uniqid($id);
		
		if (!empty($thumbnail)) {
			$extension = rsfilesHelper::getExt($thumbnail['name']);
			
			if (in_array(strtolower($extension),array('png','jpg','jpeg','gif'))) {
				$dest	= JPATH_SITE.'/components/com_rsfiles/images/thumbs/files/'.$uniqid;
				if (!$thumbnail['error'] && !empty($thumbnail['tmp_name'])) {
					// Create the thumbnail
					$params = array('width' => self::getConfig('thumb_width'), 'ext' => $extension);
					if (rsfilesHelper::createThumb($thumbnail['tmp_name'], $dest, $params)) {
						$query->clear()
							->update($db->qn('#__rsfiles_files'))
							->set($db->qn('FileThumb').' = '.$db->q($uniqid.'.'.$extension))
							->where($db->qn('IdFile').' = '.(int) $id);
						
						$db->setQuery($query);
						$db->execute();
					}
				}
			} else {
				if ($thumbnail['error'] == 0 && $thumbnail['size'] > 0) {
					$app->enqueueMessage(JText::_('COM_RSFILES_INVALID_THUMBNAIL_EXTENSION'),'error');
				}
			}
		}
		
		if (!empty($preview)) {
			$extension = rsfilesHelper::getExt($preview['name']);
			
			if (in_array(strtolower($extension),rsfilesHelper::previewExtensions())) {
				$dest = JPATH_SITE.'/components/com_rsfiles/images/preview/'.$uniqid;
				if (!$preview['error'] && !empty($preview['tmp_name'])) {
					// Create the preview
					if (in_array(strtolower($extension), array('png','jpg','jpeg','gif'))) {
						if ($input->getInt('resize',0)) {
							$params = array('width' => $input->getInt('resize_width',150), 'ext' => $extension);
							$result = rsfilesHelper::createThumb($preview['tmp_name'], $dest, $params);
						} else {
							$result = JFile::upload($preview['tmp_name'], $dest.'.'.$extension);
						}
					} else {
						$result = JFile::upload($preview['tmp_name'], $dest.'.'.$extension);
					}
					
					if ($result) {
						$query->clear()
							->update($db->qn('#__rsfiles_files'))
							->set($db->qn('preview').' = '.$db->q($uniqid.'.'.$extension))
							->where($db->qn('IdFile').' = '.(int) $id);
						
						$db->setQuery($query);
						$db->execute();
					}
				}
			} else {
				if ($preview['error'] == 0 && $preview['size'] > 0) {
					$app->enqueueMessage(JText::sprintf('COM_RSFILES_INVALID_PREVIEW_EXTENSION',rsfilesHelper::previewExtensions(true)),'error');
				}
			}
		}
		
		if (!empty($screens)) {
			foreach ($screens as $screen) {
				$extension = JFile::getExt($screen['name']);
				if (in_array(strtolower($extension),array('png','jpg','gif'))) {
					if (!$screen['error'] && !empty($screen['tmp_name'])) {
						$filename	= JFile::getName(JFile::stripExt($screen['name']));
					
						while(JFile::exists($path.$filename.'.'.$extension))
							$filename .= rand(1,999);
						
						if (JFile::upload($screen['tmp_name'], $path.$filename.'.'.$extension)) {
							$query->clear()
								->insert($db->qn('#__rsfiles_screenshots'))
								->set($db->qn('IdFile').' = '.(int) $id)
								->set($db->qn('Path').' = '.$db->q($filename.'.'.$extension));
							$db->setQuery($query);
							$db->execute();
						}
					}
				} else {
					if ($screen['error'] == 0 && $screen['size'] > 0) {
						$app->enqueueMessage(JText::sprintf('COM_RSFILES_INVALID_SCREEN_EXTENSION',$screen['name']),'error');
					}
				}
			}
		}
		
		return true;
	}
	
	// Get root folder
	public static function getRoot() {
		// Fix for the modal layout
		if (JFactory::getApplication()->input->get('layout') == 'modal')
			return 'download';
		
		$session = JFactory::getSession();
		return $session->get('rsfroot','download');
	}
	
	// Get page params
	public static function getParams() {
		$itemid = JFactory::getApplication()->input->getInt('Itemid',0);
		$params = null;
		
		if ($itemid) {
			$app = JFactory::getApplication();
			$menu = $app->getMenu();
			if ($active = $menu->getItem($itemid))
				$params = $active->params;
		}
		
		if (empty($params)) {
			$params = JFactory::getApplication()->getParams();
		}
		
		return $params;
	}
	
	// Get the Itemid
	public static function getItemid($isValue = false) {
		if ($itemid = JFactory::getApplication()->input->getInt('Itemid',0)) {
			return $isValue ? $itemid : '&Itemid='.$itemid;
		}
		return;
	}
	
	// Get users IP address
	public static function getIP($check_for_proxy = false) {
		$ip = $_SERVER['REMOTE_ADDR'];

		if ($check_for_proxy) {
			$headers = array('HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'HTTP_VIA', 'HTTP_X_COMING_FROM', 'HTTP_COMING_FROM');
			foreach ($headers as $header)
				if (!empty($_SERVER[$header]))
					$ip = $_SERVER[$header];
		}

		return $ip;
	}
	
	// Is RSMediaGallery! installed
	public static function gallery() {
		if (file_exists(JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/integration.php')) {
			require_once JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/integration.php';
			return true;
		}
		
		return false;
	}
	
	// Get email details
	public static function getMessage($type) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$lang	= JFactory::getLanguage()->getTag();
		
		$query->clear()
			->select('*')
			->from($db->qn('#__rsfiles_emails'))
			->where($db->qn('type').' = '.$db->q($type))
			->where($db->qn('lang').' = '.$db->q($lang));
		
		$db->setQuery($query);
		if (!$message = $db->loadObject()) {
			$query->clear()
				->select('*')
				->from($db->qn('#__rsfiles_emails'))
				->where($db->qn('type').' = '.$db->q($type))
				->where($db->qn('lang').' = '.$db->q('en-GB'));
			$db->setQuery($query);
			$message = $db->loadObject();
		}
		
		return $message;
	}
	
	// Get base address
	public static function getBase() {
		$uri = JURI::getInstance();
		return $uri->toString(array('scheme', 'host', 'port'));
	}
	
	// Get RSMediaGallery! tags
	public static function getGalleryTags() {
		if (!rsfilesHelper::gallery()) { 
			return array();
		}
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear();
		$query->select('DISTINCT('.$db->qn('tag').')')
			->from($db->qn('#__rsmediagallery_tags'))
			->order($db->qn('tag').' ASC');
		
		$db->setQuery($query);
		if ($tags = $db->loadColumn()) {
			foreach ($tags as $tag)
				$return[] = JHTML::_('select.option', $tag, $tag);
			return $return;
		}
		return array();
	}
	
	// Create thumb image
	public static function createThumb($src, $dest, $params = array()) {
		$width			= !empty($params['width'])			? (int) $params['width']		: null;
		$height			= !empty($params['height'])			? (int) $params['height']		: null;
		$constraint		= !empty($params['constraint'])		? $params['constraint']			: false;
		$rgb			= !empty($params['rgb'])			? $params['rgb']				: 0xFFFFFF;
		$quality		= !empty($params['quality'])		? (int) $params['quality']		: 100;
		$aspect_ratio	= isset($params['aspect_ratio'])	? $params['aspect_ratio']		: true;
		$crop			= isset($params['crop'])			? $params['crop']				: true;
		$ext			= isset($params['ext'])				? strtolower($params['ext'])	: false;
		
		$img_info	= getimagesize($src);
		
		if ($img_info === false || $ext === false) {
			return false;
		}
		
		$dest	= $dest.'.'.$ext;
		$ini_p	= $img_info[0] / $img_info[1];
		
		if ($constraint) {
			$con_p	= $constraint['width'] / $constraint['height'];
			$calc_p	= $constraint['width'] / $img_info[0];

			if ($ini_p < $con_p) {
				$height	= $constraint['height'];
				$width	= $height * $ini_p;
			} else {
				$width	= $constraint['width'];
				$height	= $img_info[1] * $calc_p;
			}
		} else {
			if (!$width && $height) {
				$width = ($height * $img_info[0]) / $img_info[1];
			} else if (!$height && $width) {
				$height = ($width * $img_info[1]) / $img_info[0];
			} else if (!$height && !$width) {
				$width	= $img_info[0];
				$height = $img_info[1];
			}
		}

		$output_format	= ($ext == 'jpg') ? 'jpeg' : $ext;
		$format 		= strtolower(substr($img_info['mime'], strpos($img_info['mime'], '/')+1));
		$icfunc			= "imagecreatefrom" . $format;
		$iresfunc		= "image" . $output_format;

		if (!function_exists($icfunc)) { 
			return false;
		}

		$dst_x = $dst_y = 0;
		$src_x = $src_y = 0;
		$res_p = $width / $height;
		
		if ($crop && !$constraint) {
			$dst_w = $width;
			$dst_h = $height;
			
			if ($ini_p > $res_p) {
				$src_h = $img_info[1];
				$src_w = $img_info[1] * $res_p;
				$src_x = ($img_info[0] >= $src_w) ? floor(($img_info[0] - $src_w) / 2) : $src_w;
			} else {
				$src_w = $img_info[0];
				$src_h = $img_info[0] / $res_p;
				$src_y = ($img_info[1] >= $src_h) ? floor(($img_info[1] - $src_h) / 2) : $src_h;
			}
		} else {
			if ($ini_p > $res_p) {
				$dst_w = $width;
				$dst_h = $aspect_ratio ? floor($dst_w / $img_info[0] * $img_info[1]) : $height;
				$dst_y = $aspect_ratio ? floor(($height - $dst_h) / 2) : 0;
			} else {
				$dst_h = $height;
				$dst_w = $aspect_ratio ? floor($dst_h / $img_info[1] * $img_info[0]) : $width;
				$dst_x = $aspect_ratio ? floor(($width - $dst_w) / 2) : 0;
			}
			
			$src_w = $img_info[0];
			$src_h = $img_info[1];
		}

		$isrc	= $icfunc($src);
		$idest	= imagecreatetruecolor($width, $height);
		
		if (($format == 'png' || $format == 'gif') && $output_format == $format ) {
			imagealphablending($idest, false);
			imagesavealpha($idest,true);
			imagefill($idest, 0, 0, IMG_COLOR_TRANSPARENT);
			imagealphablending($isrc, true);
			$quality = 0;
		} else {
			imagefill($idest, 0, 0, $rgb);
		}
		
		imagecopyresampled($idest, $isrc, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
		$iresfunc($idest, $dest, $quality);
		imagedestroy($isrc);
		imagedestroy($idest);
		
		return true;
	}
	
	// Get cached groups
	public static function getCachedGroupDetails() {
		static $group_acls_cache;
		
		if (empty($group_acls_cache)) {
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);
			
			$query->clear()
				->select($db->qn('IdGroup'))->select($db->qn('jgroups'))->select($db->qn('jusers'))
				->from($db->qn('#__rsfiles_groups'));
			
			$db->setQuery($query);
			$group_acls_cache = $db->loadObjectList('IdGroup');
		}
		
		return $group_acls_cache;
	}
	
	public static function hits($aPath, $rPath) {
		if (rsfilesHelper::isBriefcase() || empty($rPath)) {
			return;
		}
		
		// Initialize variables
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$session	= JFactory::getSession();
		$hash		= md5($rPath);
		$isExternal = rsfilesHelper::external($rPath);
		
		if ($session->get($hash)) {
			return;
		}
		
		// Search for the file in our database
		$query->select($db->qn('IdFile'))
			->from($db->qn('#__rsfiles_files'));
		
		if ($isExternal) {
			$query->where($db->qn('IdFile').' = '.$db->q((int) $rPath));
		} else {
			$query->where($db->qn('FilePath').' = '.$db->q($rPath));
		}
		
		$db->setQuery($query);
		if ($fileID = $db->loadResult()) {
			// Update hits
			$query->clear()
				->update($db->qn('#__rsfiles_files'))
				->set($db->qn('hits').' = '.$db->qn('hits').' + 1')
				->where($db->qn('IdFile').' = '.(int) $fileID);
			
			$db->setQuery($query);
			$db->execute();
			$session->set($hash, true);
		} else {
			if (!$isExternal) {
				$query->clear()
					->insert($db->qn('#__rsfiles_files'))
					->set($db->qn('FilePath').' = '.$db->q($rPath))
					->set($db->qn('DateAdded').' = '.$db->q(JFactory::getDate()->toSql()))
					->set($db->qn('ModifiedDate').' = '.$db->q(JFactory::getDate()->toSql()))
					->set($db->qn('DownloadMethod').' = 0')
					->set($db->qn('CanDownload').' = 0')
					->set($db->qn('CanView').' = 0')
					->set($db->qn('briefcase').' = 0')
					->set($db->qn('show_preview').' = 1')
					->set($db->qn('hits').' = 1');
				
				if (is_file($aPath)) {
					$query->set($db->qn('hash').' = '.$db->q(md5_file($aPath)));
				}
				
				$db->setQuery($query);
				$db->execute();
				
				$session->set($hash, true);
			}
		}
	}
	
	// Set folder / file statistics
	public static function statistics($aPath, $rPath, $external = false) {
		// Do not run in the Briefcase
		if (rsfilesHelper::isBriefcase()) {
			return;
		}
		
		// Initialize variables
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$user		= JFactory::getUser();
		$ip			= rsfilesHelper::getIP(true);
		$d_root		= realpath(rsfilesHelper::getConfig('download_folder'));
		
		// Check not to add Statistics to the download folder
		if ($aPath == $d_root || empty($rPath)) {
			return;
		}
		
		// Search for the file in our database
		$query->select($db->qn('IdFile'))
			->select($db->qn('FileStatistics'))
			->from($db->qn('#__rsfiles_files'));
		
		if ($external) {
			$query->where($db->qn('IdFile').' = '.(int) $external);
		} else {
			$query->where($db->qn('FilePath').' = '.$db->q($rPath));
		}
		
		$db->setQuery($query);
		if ($file = $db->loadObject()) {
			if (!empty($file->IdFile)) {
				// Add an entry in the Statistics table
				if ((is_file($aPath) || $external) && $file->FileStatistics) {
					$query->clear()
						->insert($db->qn('#__rsfiles_statistics'))
						->set($db->qn('Date').' = '.$db->q(JFactory::getDate()->toSql()))
						->set($db->qn('Ip').' = '.$db->q($ip))
						->set($db->qn('UserId').' = '.$db->q($user->get('id')))
						->set($db->qn('IdFile').' = '.(int) $file->IdFile);
					
					$db->setQuery($query);
					$db->execute();
				}
			}
		} else {
			$query->clear()
				->insert($db->qn('#__rsfiles_files'))
				->set($db->qn('FilePath').' = '.$db->q($rPath))
				->set($db->qn('DateAdded').' = '.$db->q(JFactory::getDate()->toSql()))
				->set($db->qn('ModifiedDate').' = '.$db->q(JFactory::getDate()->toSql()))
				->set($db->qn('DownloadMethod').' = 0')
				->set($db->qn('CanDownload').' = 0')
				->set($db->qn('CanView').' = 0')
				->set($db->qn('briefcase').' = 0')
				->set($db->qn('hits').' = 1');
			
			if (is_file($aPath))
				$query->set($db->qn('hash').' = '.$db->q(md5_file($aPath)));
			
			$db->setQuery($query);
			$db->execute();
		}
	}
	
	/**
	 *	Check briefcase permissions
	 *	
	 *	CanDownloadBriefcase	- check for download permission
	 *	CanUploadBriefcase		- check for upload permission
	 *	CanDeleteBriefcase		- check for delete permission
	 *	CanMaintainBriefcase	- check for maintain permission
	 *
	 */
	public static function briefcase($permission) {
		$db 			= JFactory::getDbo();
		$query			= $db->getQuery(true);
		$user 			= JFactory::getUser();
		$access 		= false;
		$user_groups	= JAccess::getGroupsByUser($user->id);

		if (!$user->get('guest')) {
			foreach ($user_groups as $key => $value) {
				if ($value == 1) unset($user_groups[$key]);
			}
		}
		
		static $groups = array();
		if (!isset($groups[$permission])) {
			$query->clear()
				->select($db->qn('IdGroup'))
				->from($db->qn('#__rsfiles_groups'))
				->where($db->qn($permission).' = 1');
			
			$db->setQuery($query);
			$groups[$permission] = $db->loadColumn();
		}
		
		if (!empty($groups[$permission])) {
			
			static $group_acls_cache;
			if (empty($group_acls_cache))
				$group_acls_cache = rsfilesHelper::getCachedGroupDetails();
				
			foreach($groups[$permission] as $group) {
				if ($group == 0) 
					continue; 
				
				if (!empty($group_acls_cache[$group])) {
					$registry = new JRegistry;
					$registry->loadString($group_acls_cache[$group]->jgroups);
					$group_acls = $registry->toArray();
					
					foreach($user_groups as $ugroup) {
						if (in_array($ugroup,$group_acls)) {
							$access = true;
						}
					}
					
					$registry = new JRegistry;
					$registry->loadString($group_acls_cache[$group]->jusers);
					$group_ids_array = $registry->toArray();

					if (in_array($user->get('id'),$group_ids_array) && !empty($group_acls_cache[$group]->jusers)) 
						$access = true;
				}
			}
		}
		
		return $access;
	}
	
	// Check for status
	public static function published($path) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$root	= rsfilesHelper::isBriefcase() ? rsfilesHelper::getConfig('briefcase_folder') : rsfilesHelper::getConfig('download_folder');
		$ds		= rsfilesHelper::ds();
		
		if (rsfilesHelper::external($path)) {
			$query->select($db->qn('published'))
			  ->from($db->qn('#__rsfiles_files'))
			  ->where($db->qn('IdFile').' = '.(int) $path);
		} else {
			$path = str_replace($root.$ds, '', $path);
			$query->select($db->qn('published'))
			  ->from($db->qn('#__rsfiles_files'))
			  ->where($db->qn('FilePath').' = '.$db->q($path));
		}
		
		$db->setQuery($query);
		$published = $db->loadResult();
		return $published != '' ? $published : 1;
	}
	
	// Get preview extensions
	public static function previewExtensions($tostring = false) {
		$supported = array('jpg','jpeg','txt','png','pdf','gif','mp3','mp4','mov');
		
		if (!rsfilesHelper::isiOS()) {
			$supported[] = 'ogg';
			$supported[] = 'webm';
		}
		
		return $tostring ? implode(', ',$supported) : $supported;
	}
	
	// Set metadata
	public static function metadata($file) {
		$doc = JFactory::getDocument();
		
		if (!empty($file->metatitle)) {
			$doc->setTitle($file->metatitle);
		}
		
		if (!empty($file->metakeywords)) {
			$doc->setMetaData('keywords',$file->metakeywords);
		}
		
		if (!empty($file->metadescription)) {
			$doc->setDescription($file->metadescription);
		}
	}
	
	// Make sef alias
	public static function sef($id, $name) {
		return intval($id).':'.JFilterOutput::stringURLSafe($name);
	}
	
	// Get file extension
	public static function getExt($file) {
		$file = basename($file);
		
		if (strrpos($file, '.') !== false) {
			$file = explode('.',$file);
			return end($file);
		}
		
		return false;
	}
	
	// Get file name
	public static function getName($file) {
		return basename($file);
	}
	
	// Read file
	public static function readfile_chunked($filename, $retbytes = true) {
		if (substr($filename,0,4) == 'http')
			$filename = str_replace(' ','%20', $filename);
		
		$chunksize = 1*(1024*1024); // how many bytes per chunk
		$buffer = '';
		$cnt =0;
		$handle = fopen($filename, 'rb');
		if ($handle === false) {
			return false;
		}
		while (!feof($handle)) {
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			if ($retbytes) {
				$cnt += strlen($buffer);
			}
		}
	   $status = fclose($handle);
	   if ($retbytes && $status) {
			return $cnt; // return num. bytes delivered like readfile() does.
		}
		return $status;
	}
	
	// Get briefcase folder
	public static function getBriefcase($folder = null) {
		$user  		 = JFactory::getUser();
		$root  		 = rsfilesHelper::getConfig('briefcase_folder');
		$canmaintain = rsfilesHelper::briefcase('CanMaintainBriefcase');
		$url_folder  = urldecode(JFactory::getApplication()->input->getString('folder'));
		$url_folder	 = !is_null($folder) ? $folder : $url_folder;
		$ds			 = rsfilesHelper::ds();

		if (strlen($url_folder)) 
			$url_folder = $ds.trim($url_folder, $ds);

		if ($user->get('id') != 0) {
			if ($canmaintain) {
				$briefcase_folder = $root.$url_folder;
			} else {
			
				$pieces = explode($ds,$url_folder);

				foreach ($pieces as $key => $piece)
					if ($piece == '..' || $piece == '') 
						unset($pieces[$key]);
				
				$first_folder = array_shift($pieces);
				
				if ($first_folder != $user->get('id')){
					array_unshift($pieces,$first_folder);
					array_unshift($pieces,$user->get('id'));
				} else array_unshift($pieces,$user->get('id'));
				
				$url_folder = implode($ds,$pieces);
				$briefcase_folder = $root.$ds.$url_folder;
			}
		} else  {
			$briefcase_folder = '';
		}
		
		return $briefcase_folder;
	}
	
	// Get the max files number
	public static function getMaxFilesNo() {
		$user 			= JFactory::getUser();
		$db 			= JFactory::getDbo();
		$query			= $db->getQuery(true);
		$canmaintain 	= rsfilesHelper::briefcase('CanMaintainBriefcase');
		$folder			= urldecode(JFactory::getApplication()->input->getString('folder'));

		if ($canmaintain && !empty($folder)) {
			$pieces 		= explode(rsfilesHelper::ds(),$folder);
			$user_folder	= (int) $pieces[0];
			$user 			= JFactory::getUser($user_folder);
			$mygroups 		= rsfilesHelper::getUserGroups($user->get('id'));
		} else {
			$mygroups 		= rsfilesHelper::getUserGroups($user->get('id'));
		}
		
		if (!empty($mygroups)) {
			$query->clear()
				->select('MAX('.$db->qn('MaxFilesNo').')')
				->from($db->qn('#__rsfiles_groups'))
				->where($db->qn('IdGroup').' IN ('.implode(',',$mygroups).')');
			
			$db->setQuery($query);
			$limit = (int) $db->loadResult();
			
			return $limit ? $limit : 3;
		}
		
		return 3;
	}
	
	// Get the max file size to uplaod
	public static function getMaxFileSize() {
		$db 		= JFactory::getDBO();
		$query		= $db->getQuery(true);
		$mygroups 	= rsfilesHelper::getUserGroups(JFactory::getUser()->get('id'));
		
		if (!empty($mygroups)) {
			$query->clear()
				->select('MAX('.$db->qn('MaxFileSize').')')
				->from($db->qn('#__rsfiles_groups'))
				->where($db->qn('IdGroup').' IN ('.implode(',',$mygroups).')');
			
			$db->setQuery($query);
			$limit = (int) $db->loadResult();
			
			return $limit ? $limit : 2;
		}
		
		return 2;
	}
	
	// Get the max files size
	public static function getMaxFilesSize() {
		$db 		= JFactory::getDBO();
		$query		= $db->getQuery(true);
		$maintain 	= rsfilesHelper::briefcase('CanMaintainBriefcase');
		$folder		= urldecode(JFactory::getApplication()->input->getString('folder'));
		
		if ($maintain && !empty($folder)) {
			$pieces 		= explode(rsfilesHelper::ds(),$folder);
			$user_folder	= (int) $pieces[0];
			$user 			= JFactory::getUser($user_folder);
			$mygroups 		= rsfilesHelper::getUserGroups($user->get('id'));
		} else {
			$user 			= JFactory::getUser();
			$mygroups 		= rsfilesHelper::getUserGroups($user->get('id'));
		}
		
		if (!empty($mygroups)) {
			$query->clear()
				->select('MAX('.$db->qn('MaxFilesSize').')')
				->from($db->qn('#__rsfiles_groups'))
				->where($db->qn('IdGroup').' IN ('.implode(',',$mygroups).')');
			
			$db->setQuery($query);
			$limit = (int) $db->loadResult();
			
			return $limit ? $limit : 10;
		}
		
		return 10;
	}
	
	// Get total number of files
	public static function getCurrentFilesNo() {
		$user 			= JFactory::getUser();
		$canmaintain 	= rsfilesHelper::briefcase('CanMaintainBriefcase');
		$folder			= urldecode(JFactory::getApplication()->input->getString('folder'));
		$briefcase_root = rsfilesHelper::getConfig('briefcase_folder');
		$ds				= rsfilesHelper::ds();
		
		if($canmaintain && !empty($folder)) {
			$pieces 	 = explode($ds,$folder);
			$user_folder = (int) $pieces[0];
			$user 		 = JFactory::getUser($user_folder);
			$path 		 = $briefcase_root.$ds.$user->get('id');
		} elseif($canmaintain && empty($folder)) {
			$path		= $briefcase_root;
		} else {
			$path 		= $briefcase_root.$ds.$user->get('id');
		}
		
		return count(JFolder::files($path,'', true,true,array('.htaccess')));
	}
	
	// Get user groups
	public static function getUserGroups($userid) {
		$user		= (isset($userid) && !empty($userid)) ? JFactory::getUser($userid) : JFactory::getUser();
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$mygroups	= array();
		
		$query->select('*')->from($db->qn('#__rsfiles_groups'));
		$db->setQuery($query);
		$all_groups = $db->loadObjectList();
		
		if (!empty($all_groups)) {
			foreach($all_groups as $group) {
				$registry = new JRegistry;
				$registry->loadString($group->jgroups);
				$acls = $registry->toArray();
				
				$registry = new JRegistry;
				$registry->loadString($group->jusers);
				$idusers = $registry->toArray();

				$user_groups = JAccess::getGroupsByUser($user->get('id'));
				
				if (!$user->get('guest')) {
					foreach ($user_groups as $i => $user_group) {
						if ($user_group == 1)
							unset($user_groups[$i]);
					}
				}
				
				if (!empty($user_groups)) {
					foreach($user_groups as $ugroup) {
						if (in_array($ugroup,$acls) && !in_array($group->IdGroup,$mygroups)) {
							$mygroups[] = $group->IdGroup;
						}
					}
				}

				if (in_array($user->get('id'),$idusers) && !in_array($group->IdGroup,$mygroups)) {
					$mygroups[] = $group->IdGroup;
				}
			}
		}
		
		return $mygroups;
	}
	
	// Get folder size
	public static function getFoldersize($path) {
		if (!JFolder::exists($path)) 
			return JText::sprintf('COM_RSFILES_FOLDER_DOES_NOT_EXIST',$path);
		
		$files = JFolder::files($path,'', true,true);
		$total_size = 0;
		foreach($files as $file)
			$total_size += round((rsfilesHelper::filesize($file)/1048576),2);

		return $total_size;
	}
	
	// Get file path content
	public static function filepath() {
		$briefcase	= rsfilesHelper::isBriefcase();
		$path		= rsfilesHelper::getPath();
		$preview	= JFactory::getApplication()->input->getInt('preview',0);
		$dld_fld	= $briefcase ? rsfilesHelper::getConfig('briefcase_folder') : JFactory::getSession()->get('rsfilesdownloadfolder');
		$isExternal = rsfilesHelper::external($path);
		$ds			= rsfilesHelper::ds();
		
		if ($preview) {
			$fullpath = JPATH_SITE.'/components/com_rsfiles/images/preview/'.$path;
			$dld_fld  = JPATH_SITE.'/components/com_rsfiles/images/preview';
		} else {		
			if ($briefcase) {
				if (rsfilesHelper::briefcase('CanMaintainBriefcase')) {
					$fullpath = $dld_fld.$ds.$path;
				} else {
					$fullpath = $dld_fld.$ds.JFactory::getUser()->get('id').$ds.$path;
				}
			} else {
				if ($isExternal) {
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->clear()->select($db->qn('FilePath'))->from($db->qn('#__rsfiles_files'))->where($db->qn('IdFile').' = '.(int) $path);
					$db->setQuery($query);
					$fullpath = $db->loadResult();
				} else {
					$fullpath = $dld_fld.$ds.$path;
					
					if (!realpath($fullpath)) {
						$download_folder = rsfilesHelper::getConfig('download_folder');
						$fullpath = $download_folder.$ds.$path;
					}
				}
			}
		}
		
		$fullpath	= realpath($fullpath);
		$extension 	= rsfilesHelper::getExt($fullpath);
		$extension	= strtolower($extension);
		$filename	= basename($fullpath);
		
		// if the users get outs of the root 
		if (empty($isExternal) && strpos(realpath($fullpath), realpath($dld_fld)) !== 0)
			return false;
		
		// check first if the user can download the file
		if (!rsfilesHelper::permissions('CanDownload',$path) && !$preview)
			return false;
		
		if (empty($isExternal) && JFile::stripExt($extension) == '')
			return false;
		
		@ob_end_clean();
		
		// Set mimetype header
		if ($extension == 'mp4' || $extension == 'mov') {
			header("Content-type: video/mp4");
		} elseif ($extension == 'mp3') {
			header("Content-type: audio/mpeg");
		} elseif ($extension == 'ogg') {
			header("Content-type: audio/ogg");
		} elseif ($extension == 'webm') {
			header("Content-type: audio/webm");
		} else {
			header("Content-Type: application/octetstream");
		}
		
		header('Cache-Control: no-cache');
		header('Content-Disposition:inline;filename="' . $filename . '"');
		
		if (isset($_SERVER['HTTP_RANGE'])) {
            rsfilesHelper::rangeDownload($fullpath);
        } else {
			if (!$isExternal) header('Content-length: ' . rsfilesHelper::filesize($fullpath));
			rsfilesHelper::readfile_chunked($fullpath);
        }
		
		exit();
	}
	
	// Get ordering values
	public static function getOrdering() {
		return array(JHtml::_('select.option','name',JText::_('COM_RSFILES_SEARCH_NAME')), JHtml::_('select.option','date',JText::_('COM_RSFILES_SEARCH_DATE')), JHtml::_('select.option','hits',JText::_('COM_RSFILES_SEARCH_HITS')));
	}
	
	// Get ordering directions values
	public static function getOrderingDirection() {
		return array(JHtml::_('select.option','asc',JText::_('COM_RSFILES_SEARCH_ASCENDING')), JHtml::_('select.option','desc',JText::_('COM_RSFILES_SEARCH_DESCENDING')));
	}
	
	// Check if RSMail! is installed
	public static function isRsmail() {
		return file_exists(JPATH_SITE.'/components/com_rsmail/helpers/actions.php');
	}
	
	// Handle errors
	public static function errors($message, $url) {
		$error = self::getConfig('error_handling');
		
		// 500 error
		if ($error == 0) {
			throw new Exception($message, 500);
		} elseif ($error == 1) { // 403 error
			throw new Exception($message, 403);
		} else { // Redirect
			JFactory::getApplication()->redirect($url, $message, 'error');
		}
	}
	
	// Create the download link
	public static function downloadlink($file, $path) {
		$input		= JFactory::getApplication()->input;
		$briefcase	= $input->get('from') == 'briefcase' || $input->get('layout') == 'briefcase';
		$itemid		= rsfilesHelper::getItemid();
		$config		= rsfilesHelper::getConfig();
		$hash		= $input->getString('hash','');
		$hash		= $hash ? '&hash='.$hash : '';
		
		if ($briefcase) {
			if ($file->DownloadMethod == 0) {
				if (empty($file->IdLicense)) {
					$dlink = JRoute::_('index.php?option=com_rsfiles&task=rsfiles.download&path='.rsfilesHelper::encode($path).'&from=briefcase'.$itemid,false);
					$rel = '';
					$enablemodal = '';
				} else {
					$dlink = JRoute::_('index.php?option=com_rsfiles&layout=agreement&tmpl=component&id='.rsfilesHelper::sef($file->IdLicense,$file->LicenseName).'&path='.rsfilesHelper::encode($path).'&from=briefcase'.$itemid,false);
					$rel = 'rel="{handler: \'iframe\', size: {x: 800, y: 600}}"';
					$enablemodal = ' rs_modal';
				}
			} else {
				$dlink = JRoute::_('index.php?option=com_rsfiles&layout=email&tmpl=component&path='.rsfilesHelper::encode($path).'&from=briefcase'.$itemid,false);
				$rel = 'rel="{handler: \'iframe\',size: {x: 600, y: 360}}"';
				$enablemodal = ' rs_modal';
			}
		} else {
			if ($file->DownloadMethod == 0) {
				if (empty($file->IdLicense)) {
					if ($config->captcha_enabled) {
						$dlink = JRoute::_('index.php?option=com_rsfiles&layout=validate&tmpl=component&path='.rsfilesHelper::encode($path).$hash.$itemid,false);
						$rel = 'rel="{handler: \'iframe\', size: {x: 800, y: 600}}"';
						$enablemodal = ' rs_modal';
					} else {
						$dlink = JRoute::_('index.php?option=com_rsfiles&task=rsfiles.download&path='.rsfilesHelper::encode($path).$hash.$itemid,false);
						$rel = '';
						$enablemodal = '';
					}
				} else {
					$dlink = JRoute::_('index.php?option=com_rsfiles&layout=agreement&tmpl=component&id='.rsfilesHelper::sef($file->IdLicense,$file->LicenseName).'&path='.rsfilesHelper::encode($path).$hash.$itemid,false);
					$rel = 'rel="{handler: \'iframe\', size: {x: 800, y: 600}}"';
					$enablemodal = ' rs_modal';
				}
			} else {
				$dlink = JRoute::_('index.php?option=com_rsfiles&layout=email&tmpl=component&path='.rsfilesHelper::encode($path).$hash.$itemid,false);
				$rel = 'rel="{handler: \'iframe\',size: {x: 600, y: 360}}"';
				$enablemodal = ' rs_modal';
			}
		}
		
		$download = new stdClass();
		$download->enablemodal 	= $enablemodal;
		$download->rel 			= $rel;
		$download->dlink 		= $dlink;
		
		return $download;
	}
	
	// Get thumbnail
	public static function thumbnail($item) {
		$thumb = new stdClass();
		
		if (!empty($item->thumb)) {
			$thumb->image = rsfilesHelper::tooltipText(htmlentities('<img src="'.$item->thumb.'" alt="" />',ENT_COMPAT,'UTF-8'));
			$thumb->class = ' '.rsfilesHelper::tooltipClass();
		} else {
			$thumb->image = '';
			$thumb->class = '';
		}
		
		return $thumb;
	}
	
	// Display a folder list, a file or an external file
	public static function display($path, $fparams) {
		$config				= rsfilesHelper::getConfig();
		$download_folder	= $config->download_folder;
		$type				= '';
		
		if (is_null($path)) {
			return false;
		}
		
		if (rsfilesHelper::external($path)) {
			$type = 'external';
		} else if (is_dir(realpath($download_folder.'/'.$path))) {
			$type = 'folder';
		} else if (is_file(realpath($download_folder.'/'.$path))) {
			$type = 'file';
		}
		
		if (empty($type)) {
			return false;
		}
		
		$itemid = $fparams->get('itemid');
		if (!empty($itemid)) {
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);
			
			$query->clear()
				->select($db->qn('params'))
				->from($db->qn('#__menu'))
				->where($db->qn('id').' = '.(int) $itemid);
			$db->setQuery($query);
			if ($params = $db->loadResult()) {
				$registry = new JRegistry;
				$registry->loadString($params);
				if ($folder = $registry->get('folder')) {
					$itemid = 0;
				}
			}
		}
		
		$class = self::isJ3() ? 'JViewLegacy' : 'JView';
		if ($class == 'JView') {
			jimport('joomla.application.component.view');
		}
		
		$view = new $class(array(
			'name' => 'rsfiles',
			'layout' => 'plugin',
			'base_path' => JPATH_SITE.'/components/com_rsfiles'
		));
		
		$view->addTemplatePath(JPATH_THEMES . '/' . JFactory::getApplication()->getTemplate() . '/html/com_rsfiles/' . $view->getName());
		
		$view->config		= $config;
		$view->params		= $fparams;
		$view->itemid		= $itemid ? '&Itemid='.$itemid : '';
		
		require_once JPATH_SITE.'/components/com_rsfiles/helpers/files.php';
		
		$fullpath	= $type == 'external' ? $path : realpath($download_folder.'/'.$path);
		$class		= new RSFilesFiles($fullpath, 'site', $itemid, 1, $fparams->get('ordering'), $fparams->get('order'));
		
		if ($type == 'folder') {
			$files		= $class->getFiles();
			$folders	= $class->getFolders();
			$external	= $class->getExternal();
			$view->items = array_merge($folders,$files,$external);
		} else if ($type == 'file') {
			$view->items = $class->getFiles(array('dld_fld' => $download_folder, 'file' => $fullpath));
		} else if ($type == 'external') {
			$view->items = $class->getExternal(array('file' => $path));
		}
		
		return $view->loadTemplate();
	}
	
	// Get number of bytes
	public static function return_bytes($val) {
		$val = trim($val);
		
		switch (strtolower(substr($val, -1))) {
			case 'm': $val = (int)substr($val, 0, -1) * 1048576; break;
			case 'k': $val = (int)substr($val, 0, -1) * 1024; break;
			case 'g': $val = (int)substr($val, 0, -1) * 1073741824; break;
			case 'b':
				switch (strtolower(substr($val, -2, 1))) {
					case 'm': $val = (int)substr($val, 0, -2) * 1048576; break;
					case 'k': $val = (int)substr($val, 0, -2) * 1024; break;
					case 'g': $val = (int)substr($val, 0, -2) * 1073741824; break;
					default : break;
				} break;
			default: break;
		}
		return $val;
	}
	
	// Encode path
	public static function encode($path) {
		return urlencode($path);
	}
	
	// Get the tooltip class
	public static function tooltipClass() {
		return self::isJ3() ? 'hasTooltip' : 'hasTip';
	}
	
	// Prepare the tooltip text
	public static function tooltipText($title, $content = '') {
		static $version;
		if (!$version) {
			$version = new JVersion();
		}
		
		if ($version->isCompatible('3.1.2')) {
			return JHtml::tooltipText($title, $content, 0, 0);
		} else {
			return $title.'::'.$content;
		}
	}
	
	// Load tooltip
	public static function tooltipLoad() {
		if (self::isJ3()) {
			$jversion = new JVersion();
			
			if ($jversion->isCompatible('3.3')) {
				JHtml::_('behavior.core');
			}
			
			JHtml::_('behavior.framework', true);
			JHtml::_('bootstrap.tooltip');
		} else {
			JHtml::_('behavior.tooltip');
		}
	}
	
	// Get the main root folder
	public static function root($trailing = false, $module = false, $plugin = false) {
		$app	= JFactory::getApplication();
		$config = rsfilesHelper::getConfig();
		$ds 	= rsfilesHelper::ds();
		
		if ($app->isAdmin()) {
			$root = $config->{rsfilesHelper::getRoot().'_folder'};
		} else {
			$session = JFactory::getSession();
			if ($app->input->get('from','') == 'briefcase' || $app->input->get('layout','') == 'briefcase') {
				$root = $config->briefcase_folder;
				
				if (!rsfilesHelper::briefcase('CanMaintainBriefcase')) {
					$root .= $ds.JFactory::getUser()->get('id');
				}
			} else {
				$root = $session->get('rsfilesdownloadfolder');
			}
			
			if ($module || $plugin) {
				$root = $config->download_folder;
			}
		}
		
		return realpath($root).($trailing ? $ds : '');
	}
	
	// Check if we are in the Briefcase folder
	public static function isBriefcase() {
		$input = JFactory::getApplication()->input;
		$jform = $input->get('jform',array(),'array');
		return $input->get('layout') == 'briefcase' || $input->get('from') == 'briefcase' || (isset($jform['from']) && $jform['from'] == 'briefcase');
	}
	
	// Get file/folder permissions
	public static function permissions($type, $path) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$access	= false;
		$groups	= '';
		
		if ($path == 'root_rs_files') {
			if ($type == 'CanCreate') {
				$groups = rsfilesHelper::getConfig('download_cancreate');
			} elseif ($type == 'CanUpload') {
				$groups = rsfilesHelper::getConfig('download_canupload');
			}
		} else {
			$query->select($db->qn($type))
				->from($db->qn('#__rsfiles_files'))
				->where($db->qn('briefcase').' = 0');
			
			if (rsfilesHelper::external($path))	{
				$query->where($db->qn('IdFile').' = '.$db->q((int) $path));
			} else {
				$query->where($db->qn('FilePath').' = '.$db->q($path));
			}
			
			$db->setQuery($query);
			$groups = $db->loadResult();
		}
		
		$groups  = is_null($groups) ? '' : $groups;
		
		if ($groups !== '') {
			$groups = explode(',',$groups);
			JArrayHelper::toInteger($groups);

			if (!in_array(0,$groups)) {
				$user		 = JFactory::getUser();
				$uid		 = $user->get('id');
				$user_groups = JAccess::getGroupsByUser($uid);
				
				if (!$user->get('guest')) {
					foreach ($user_groups as $key => $value) {
						if ($value == 1) unset($user_groups[$key]);
					}
				}
				
				static $group_acls_cache;
				if (empty($group_acls_cache)) {
					$group_acls_cache = rsfilesHelper::getCachedGroupDetails();
				}
				
				if (!empty($groups)) {
					foreach($groups as $group) {
						if ($group == 0) 
							continue;
						
						if (!empty($group_acls_cache[$group])) {
							$registry = new JRegistry;
							$registry->loadString($group_acls_cache[$group]->jgroups);
							$group_acls = $registry->toArray();
							
							foreach($user_groups as $ugroup) {
								if (in_array($ugroup,$group_acls)) {
									$access = true;
								}
							}
							
							$registry = new JRegistry;
							$registry->loadString($group_acls_cache[$group]->jusers);
							$group_ids_array = $registry->toArray();

							if (in_array($uid,$group_ids_array) && !empty($group_acls_cache[$group]->jusers)) {
								$access = true;
							}
						}
					}
				}
			} else {
				$access = true;
			}
		} else {
			if ($type == 'CanCreate' || $type == 'CanDelete' || $type == 'CanUpload' || $type == 'CanEdit') {
				$access = false;
			} else {
				$access = true;
			}
		}
		
		return $access;
	}
	
	// Get file mimetype
	public static function mimetype($extension) {
		$mimes = array(
		'3ds' => 'image/x-3ds',
		'3gp' => 'video/3gpp',
		'BLEND' => 'application/x-blender',
		'C' => 'text/x-c++src',
		'CSSL' => 'text/css',
		'NSV' => 'video/x-nsv',
		'XM' => 'audio/x-mod',
		'Z' => 'application/x-compress',
		'a' => 'application/x-archive',
		'abw' => 'application/x-abiword',
		'abw.gz' => 'application/x-abiword',
		'ac3' => 'audio/ac3',
		'adb' => 'text/x-adasrc',
		'ads' => 'text/x-adasrc',
		'afm' => 'application/x-font-afm',
		'ag' => 'image/x-applix-graphics',
		'ai' => 'application/illustrator',
		'aif' => 'audio/x-aiff',
		'aifc' => 'audio/x-aiff',
		'aiff' => 'audio/x-aiff',
		'al' => 'application/x-perl',
		'arj' => 'application/x-arj',
		'as' => 'application/x-applix-spreadsheet',
		'asc' => 'text/plain',
		'asf' => 'video/x-ms-asf',
		'asp' => 'application/x-asp',
		'asx' => 'video/x-ms-asf',
		'au' => 'audio/basic',
		'avi' => 'video/x-msvideo',
		'aw' => 'application/x-applix-word',
		'bak' => 'application/x-trash',
		'bcpio' => 'application/x-bcpio',
		'bdf' => 'application/x-font-bdf',
		'bib' => 'text/x-bibtex',
		'bin' => 'application/octet-stream',
		'blend' => 'application/x-blender',
		'blender' => 'application/x-blender',
		'bmp' => 'image/bmp',
		'bz' => 'application/x-bzip',
		'bz2' => 'application/x-bzip',
		'c' => 'text/x-csrc',
		'c++' => 'text/x-c++src',
		'cc' => 'text/x-c++src',
		'cdf' => 'application/x-netcdf',
		'cdr' => 'application/vnd.corel-draw',
		'cer' => 'application/x-x509-ca-cert',
		'cert' => 'application/x-x509-ca-cert',
		'cgi' => 'application/x-cgi',
		'cgm' => 'image/cgm',
		'chrt' => 'application/x-kchart',
		'class' => 'application/x-java',
		'cls' => 'text/x-tex',
		'cpio' => 'application/x-cpio',
		'cpio.gz' => 'application/x-cpio-compressed',
		'cpp' => 'text/x-c++src',
		'cpt' => 'application/mac-compactpro',
		'crt' => 'application/x-x509-ca-cert',
		'cs' => 'text/x-csharp',
		'csh' => 'application/x-shellscript',
		'css' => 'text/css',
		'csv' => 'text/x-comma-separated-values',
		'cur' => 'image/x-win-bitmap',
		'cxx' => 'text/x-c++src',
		'dat' => 'video/mpeg',
		'dbf' => 'application/x-dbase',
		'dc' => 'application/x-dc-rom',
		'dcl' => 'text/x-dcl',
		'dcm' => 'image/x-dcm',
		'dcr' => 'application/x-director',
		'deb' => 'application/x-deb',
		'der' => 'application/x-x509-ca-cert',
		'desktop' => 'application/x-desktop',
		'dia' => 'application/x-dia-diagram',
		'diff' => 'text/x-patch',
		'dir' => 'application/x-director',
		'djv' => 'image/vnd.djvu',
		'djvu' => 'image/vnd.djvu',
		'dll' => 'application/octet-stream',
		'dms' => 'application/octet-stream',
		'doc' => 'application/msword',
		'dsl' => 'text/x-dsl',
		'dtd' => 'text/x-dtd',
		'dvi' => 'application/x-dvi',
		'dwg' => 'image/vnd.dwg',
		'dxf' => 'image/vnd.dxf',
		'dxr' => 'application/x-director',
		'egon' => 'application/x-egon',
		'el' => 'text/x-emacs-lisp',
		'eps' => 'image/x-eps',
		'epsf' => 'image/x-eps',
		'epsi' => 'image/x-eps',
		'etheme' => 'application/x-e-theme',
		'etx' => 'text/x-setext',
		'exe' => 'application/x-executable',
		'ez' => 'application/andrew-inset',
		'f' => 'text/x-fortran',
		'fig' => 'image/x-xfig',
		'fits' => 'image/x-fits',
		'flac' => 'audio/x-flac',
		'flc' => 'video/x-flic',
		'fli' => 'video/x-flic',
		'flw' => 'application/x-kivio',
		'fo' => 'text/x-xslfo',
		'g3' => 'image/fax-g3',
		'gb' => 'application/x-gameboy-rom',
		'gcrd' => 'text/x-vcard',
		'gen' => 'application/x-genesis-rom',
		'gg' => 'application/x-sms-rom',
		'gif' => 'image/gif',
		'glade' => 'application/x-glade',
		'gmo' => 'application/x-gettext-translation',
		'gnc' => 'application/x-gnucash',
		'gnucash' => 'application/x-gnucash',
		'gnumeric' => 'application/x-gnumeric',
		'gra' => 'application/x-graphite',
		'gsf' => 'application/x-font-type1',
		'gtar' => 'application/x-gtar',
		'gz' => 'application/x-gzip',
		'h' => 'text/x-chdr',
		'h++' => 'text/x-chdr',
		'hdf' => 'application/x-hdf',
		'hh' => 'text/x-c++hdr',
		'hp' => 'text/x-chdr',
		'hpgl' => 'application/vnd.hp-hpgl',
		'hqx' => 'application/mac-binhex40',
		'hs' => 'text/x-haskell',
		'htm' => 'text/html',
		'html' => 'text/html',
		'icb' => 'image/x-icb',
		'ice' => 'x-conference/x-cooltalk',
		'ico' => 'image/x-ico',
		'ics' => 'text/calendar',
		'idl' => 'text/x-idl',
		'ief' => 'image/ief',
		'ifb' => 'text/calendar',
		'iff' => 'image/x-iff',
		'iges' => 'model/iges',
		'igs' => 'model/iges',
		'ilbm' => 'image/x-ilbm',
		'iso' => 'application/x-cd-image',
		'it' => 'audio/x-it',
		'jar' => 'application/x-jar',
		'java' => 'text/x-java',
		'jng' => 'image/x-jng',
		'jp2' => 'image/jpeg2000',
		'jpg' => 'image/jpeg',
		'jpe' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'jpr' => 'application/x-jbuilder-project',
		'jpx' => 'application/x-jbuilder-project',
		'js' => 'application/x-javascript',
		'kar' => 'audio/midi',
		'karbon' => 'application/x-karbon',
		'kdelnk' => 'application/x-desktop',
		'kfo' => 'application/x-kformula',
		'kil' => 'application/x-killustrator',
		'kon' => 'application/x-kontour',
		'kpm' => 'application/x-kpovmodeler',
		'kpr' => 'application/x-kpresenter',
		'kpt' => 'application/x-kpresenter',
		'kra' => 'application/x-krita',
		'ksp' => 'application/x-kspread',
		'kud' => 'application/x-kugar',
		'kwd' => 'application/x-kword',
		'kwt' => 'application/x-kword',
		'la' => 'application/x-shared-library-la',
		'latex' => 'application/x-latex',
		'lha' => 'application/x-lha',
		'lhs' => 'text/x-literate-haskell',
		'lhz' => 'application/x-lhz',
		'log' => 'text/x-log',
		'ltx' => 'text/x-tex',
		'lwo' => 'image/x-lwo',
		'lwob' => 'image/x-lwo',
		'lws' => 'image/x-lws',
		'lyx' => 'application/x-lyx',
		'lzh' => 'application/x-lha',
		'lzo' => 'application/x-lzop',
		'm' => 'text/x-objcsrc',
		'm15' => 'audio/x-mod',
		'm3u' => 'audio/x-mpegurl',
		'man' => 'application/x-troff-man',
		'md' => 'application/x-genesis-rom',
		'me' => 'text/x-troff-me',
		'mesh' => 'model/mesh',
		'mgp' => 'application/x-magicpoint',
		'mid' => 'audio/midi',
		'midi' => 'audio/midi',
		'mif' => 'application/x-mif',
		'mkv' => 'application/x-matroska',
		'mm' => 'text/x-troff-mm',
		'mml' => 'text/mathml',
		'mng' => 'video/x-mng',
		'moc' => 'text/x-moc',
		'mod' => 'audio/x-mod',
		'moov' => 'video/quicktime',
		'mov' => 'video/quicktime',
		'movie' => 'video/x-sgi-movie',
		'mp2' => 'video/mpeg',
		'mp3' => 'audio/x-mp3',
		'mp4' => 'application/mp4',
		'mpe' => 'video/mpeg',
		'mpeg' => 'video/mpeg',
		'mpg' => 'video/mpeg',
		'mpga' => 'audio/mpeg',
		'ms' => 'text/x-troff-ms',
		'msh' => 'model/mesh',
		'msod' => 'image/x-msod',
		'msx' => 'application/x-msx-rom',
		'mtm' => 'audio/x-mod',
		'mxu' => 'video/vnd.mpegurl',
		'n64' => 'application/x-n64-rom',
		'nc' => 'application/x-netcdf',
		'nes' => 'application/x-nes-rom',
		'nsv' => 'video/x-nsv',
		'o' => 'application/x-object',
		'obj' => 'application/x-tgif',
		'oda' => 'application/oda',
		'odb' => 'application/vnd.oasis.opendocument.database',
		'odc' => 'application/vnd.oasis.opendocument.chart',
		'odf' => 'application/vnd.oasis.opendocument.formula',
		'odg' => 'application/vnd.oasis.opendocument.graphics',
		'odi' => 'application/vnd.oasis.opendocument.image',
		'odm' => 'application/vnd.oasis.opendocument.text-master',
		'odp' => 'application/vnd.oasis.opendocument.presentation',
		'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		'odt' => 'application/vnd.oasis.opendocument.text',
		'ogg' => 'application/ogg',
		'old' => 'application/x-trash',
		'oleo' => 'application/x-oleo',
		'otg' => 'application/vnd.oasis.opendocument.graphics-template',
		'oth' => 'application/vnd.oasis.opendocument.text-web',
		'otp' => 'application/vnd.oasis.opendocument.presentation-template',
		'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
		'ott' => 'application/vnd.oasis.opendocument.text-template',
		'p' => 'text/x-pascal',
		'p12' => 'application/x-pkcs12',
		'p7s' => 'application/pkcs7-signature',
		'pas' => 'text/x-pascal',
		'patch' => 'text/x-patch',
		'pbm' => 'image/x-portable-bitmap',
		'pcd' => 'image/x-photo-cd',
		'pcf' => 'application/x-font-pcf',
		'pcf.Z' => 'application/x-font-type1',
		'pcl' => 'application/vnd.hp-pcl',
		'pdb' => 'application/vnd.palm',
		'pdf' => 'application/pdf',
		'pem' => 'application/x-x509-ca-cert',
		'perl' => 'application/x-perl',
		'pfa' => 'application/x-font-type1',
		'pfb' => 'application/x-font-type1',
		'pfx' => 'application/x-pkcs12',
		'pgm' => 'image/x-portable-graymap',
		'pgn' => 'application/x-chess-pgn',
		'pgp' => 'application/pgp',
		'php' => 'application/x-php',
		'php3' => 'application/x-php',
		'php4' => 'application/x-php',
		'pict' => 'image/x-pict',
		'pict1' => 'image/x-pict',
		'pict2' => 'image/x-pict',
		'pl' => 'application/x-perl',
		'pls' => 'audio/x-scpls',
		'pm' => 'application/x-perl',
		'png' => 'image/png',
		'pnm' => 'image/x-portable-anymap',
		'po' => 'text/x-gettext-translation',
		'pot' => 'application/vnd.ms-powerpoint',
		'ppm' => 'image/x-portable-pixmap',
		'pps' => 'application/vnd.ms-powerpoint',
		'ppt' => 'application/vnd.ms-powerpoint',
		'ppz' => 'application/vnd.ms-powerpoint',
		'ps' => 'application/postscript',
		'ps.gz' => 'application/x-gzpostscript',
		'psd' => 'image/x-psd',
		'psf' => 'application/x-font-linux-psf',
		'psid' => 'audio/prs.sid',
		'pw' => 'application/x-pw',
		'py' => 'application/x-python',
		'pyc' => 'application/x-python-bytecode',
		'pyo' => 'application/x-python-bytecode',
		'qif' => 'application/x-qw',
		'qt' => 'video/quicktime',
		'qtvr' => 'video/quicktime',
		'ra' => 'audio/x-pn-realaudio',
		'ram' => 'audio/x-pn-realaudio',
		'rar' => 'application/x-rar',
		'ras' => 'image/x-cmu-raster',
		'rdf' => 'text/rdf',
		'rej' => 'application/x-reject',
		'rgb' => 'image/x-rgb',
		'rle' => 'image/rle',
		'rm' => 'audio/x-pn-realaudio',
		'roff' => 'application/x-troff',
		'rpm' => 'application/x-rpm',
		'rss' => 'text/rss',
		'rtf' => 'application/rtf',
		'rtx' => 'text/richtext',
		's3m' => 'audio/x-s3m',
		'sam' => 'application/x-amipro',
		'scm' => 'text/x-scheme',
		'sda' => 'application/vnd.stardivision.draw',
		'sdc' => 'application/vnd.stardivision.calc',
		'sdd' => 'application/vnd.stardivision.impress',
		'sdp' => 'application/vnd.stardivision.impress',
		'sds' => 'application/vnd.stardivision.chart',
		'sdw' => 'application/vnd.stardivision.writer',
		'sgi' => 'image/x-sgi',
		'sgl' => 'application/vnd.stardivision.writer',
		'sgm' => 'text/sgml',
		'sgml' => 'text/sgml',
		'sh' => 'application/x-shellscript',
		'shar' => 'application/x-shar',
		'shtml' => 'text/html',
		'siag' => 'application/x-siag',
		'sid' => 'audio/prs.sid',
		'sik' => 'application/x-trash',
		'silo' => 'model/mesh',
		'sit' => 'application/x-stuffit',
		'skd' => 'application/x-koan',
		'skm' => 'application/x-koan',
		'skp' => 'application/x-koan',
		'skt' => 'application/x-koan',
		'slk' => 'text/spreadsheet',
		'smd' => 'application/vnd.stardivision.mail',
		'smf' => 'application/vnd.stardivision.math',
		'smi' => 'application/smil',
		'smil' => 'application/smil',
		'sml' => 'application/smil',
		'sms' => 'application/x-sms-rom',
		'snd' => 'audio/basic',
		'so' => 'application/x-sharedlib',
		'spd' => 'application/x-font-speedo',
		'spl' => 'application/x-futuresplash',
		'sql' => 'text/x-sql',
		'src' => 'application/x-wais-source',
		'stc' => 'application/vnd.sun.xml.calc.template',
		'std' => 'application/vnd.sun.xml.draw.template',
		'sti' => 'application/vnd.sun.xml.impress.template',
		'stm' => 'audio/x-stm',
		'stw' => 'application/vnd.sun.xml.writer.template',
		'sty' => 'text/x-tex',
		'sun' => 'image/x-sun-raster',
		'sv4cpio' => 'application/x-sv4cpio',
		'sv4crc' => 'application/x-sv4crc',
		'svg' => 'image/svg+xml',
		'swf' => 'application/x-shockwave-flash',
		'sxc' => 'application/vnd.sun.xml.calc',
		'sxd' => 'application/vnd.sun.xml.draw',
		'sxg' => 'application/vnd.sun.xml.writer.global',
		'sxi' => 'application/vnd.sun.xml.impress',
		'sxm' => 'application/vnd.sun.xml.math',
		'sxw' => 'application/vnd.sun.xml.writer',
		'sylk' => 'text/spreadsheet',
		't' => 'application/x-troff',
		'tar' => 'application/x-tar',
		'tar.Z' => 'application/x-tarz',
		'tar.bz' => 'application/x-bzip-compressed-tar',
		'tar.bz2' => 'application/x-bzip-compressed-tar',
		'tar.gz' => 'application/x-compressed-tar',
		'tar.lzo' => 'application/x-tzo',
		'tcl' => 'text/x-tcl',
		'tex' => 'text/x-tex',
		'texi' => 'text/x-texinfo',
		'texinfo' => 'text/x-texinfo',
		'tga' => 'image/x-tga',
		'tgz' => 'application/x-compressed-tar',
		'theme' => 'application/x-theme',
		'tif' => 'image/tiff',
		'tiff' => 'image/tiff',
		'tk' => 'text/x-tcl',
		'torrent' => 'application/x-bittorrent',
		'tr' => 'application/x-troff',
		'ts' => 'application/x-linguist',
		'tsv' => 'text/tab-separated-values',
		'ttf' => 'application/x-font-ttf',
		'txt' => 'text/plain',
		'tzo' => 'application/x-tzo',
		'ui' => 'application/x-designer',
		'uil' => 'text/x-uil',
		'ult' => 'audio/x-mod',
		'uni' => 'audio/x-mod',
		'uri' => 'text/x-uri',
		'url' => 'text/x-uri',
		'ustar' => 'application/x-ustar',
		'vcd' => 'application/x-cdlink',
		'vcf' => 'text/x-vcalendar',
		'vcs' => 'text/x-vcalendar',
		'vct' => 'text/x-vcard',
		'vfb' => 'text/calendar',
		'vob' => 'video/mpeg',
		'voc' => 'audio/x-voc',
		'vor' => 'application/vnd.stardivision.writer',
		'vrml' => 'model/vrml',
		'vsd' => 'application/vnd.visio',
		'wav' => 'audio/x-wav',
		'wax' => 'audio/x-ms-wax',
		'wb1' => 'application/x-quattropro',
		'wb2' => 'application/x-quattropro',
		'wb3' => 'application/x-quattropro',
		'wbmp' => 'image/vnd.wap.wbmp',
		'wbxml' => 'application/vnd.wap.wbxml',
		'wk1' => 'application/vnd.lotus-1-2-3',
		'wk3' => 'application/vnd.lotus-1-2-3',
		'wk4' => 'application/vnd.lotus-1-2-3',
		'wks' => 'application/vnd.lotus-1-2-3',
		'wm' => 'video/x-ms-wm',
		'wma' => 'audio/x-ms-wma',
		'wmd' => 'application/x-ms-wmd',
		'wmf' => 'image/x-wmf',
		'wml' => 'text/vnd.wap.wml',
		'wmlc' => 'application/vnd.wap.wmlc',
		'wmls' => 'text/vnd.wap.wmlscript',
		'wmlsc' => 'application/vnd.wap.wmlscriptc',
		'wmv' => 'video/x-ms-wmv',
		'wmx' => 'video/x-ms-wmx',
		'wmz' => 'application/x-ms-wmz',
		'wpd' => 'application/wordperfect',
		'wpg' => 'application/x-wpg',
		'wri' => 'application/x-mswrite',
		'wrl' => 'model/vrml',
		'wvx' => 'video/x-ms-wvx',
		'xac' => 'application/x-gnucash',
		'xbel' => 'application/x-xbel',
		'xbm' => 'image/x-xbitmap',
		'xcf' => 'image/x-xcf',
		'xcf.bz2' => 'image/x-compressed-xcf',
		'xcf.gz' => 'image/x-compressed-xcf',
		'xht' => 'application/xhtml+xml',
		'xhtml' => 'application/xhtml+xml',
		'xi' => 'audio/x-xi',
		'xls' => 'application/vnd.ms-excel',
		'xla' => 'application/vnd.ms-excel',
		'xlc' => 'application/vnd.ms-excel',
		'xld' => 'application/vnd.ms-excel',
		'xll' => 'application/vnd.ms-excel',
		'xlm' => 'application/vnd.ms-excel',
		'xlt' => 'application/vnd.ms-excel',
		'xlw' => 'application/vnd.ms-excel',
		'xm' => 'audio/x-xm',
		'xml' => 'text/xml',
		'xpm' => 'image/x-xpixmap',
		'xsl' => 'text/x-xslt',
		'xslfo' => 'text/x-xslfo',
		'xslt' => 'text/x-xslt',
		'xwd' => 'image/x-xwindowdump',
		'xyz' => 'chemical/x-xyz',
		'zabw' => 'application/x-abiword',
		'zip' => 'application/zip',
		'zoo' => 'application/x-zoo',
		'123' => 'application/vnd.lotus-1-2-3',
		'669' => 'audio/x-mod'
		);
	
		return isset($mimes[$extension]) ? $mimes[$extension] : '';
	}
	
	// Get the current folder
	public static function getFolder($return = false) {
		$folder = urldecode(JFactory::getApplication()->input->getString('folder',''));
		$folder = ltrim($folder,rsfilesHelper::ds());
		return  $return ? (!empty($folder) ? '&folder='.rsfilesHelper::encode($folder) : '') : $folder;
	}
	
	// Get the current path
	public static function getPath($return = false) {
		$path = urldecode(JFactory::getApplication()->input->getString('path',''));
		$path = ltrim($path,rsfilesHelper::ds());
		return  $return ? (!empty($path) ? '&path='.rsfilesHelper::encode($path) : '') : $path;
	}
	
	public static function getNavigation() {
		$folder		= rsfilesHelper::getFolder();
		$ds			= rsfilesHelper::ds();
		$navigation	= '';
		
		if (empty($folder)) {
			return false;
		}
		
		if ($elements = explode($ds, $folder)) {
			foreach ($elements as $i => $element) {
				$navigation .= $element;
				$newelement = new stdClass();
				$newelement->name = $element;
				$newelement->fullpath = urlencode($navigation);
				$elements[$i] = $newelement;
				$navigation .= $ds;
			}
		}
		
		return $elements;
	}
	
	// Get previous folder
	public static function getPrevious($return = false) {
		$folder		= rsfilesHelper::getFolder();
		$ds			= rsfilesHelper::ds();
		$elements	= explode($ds, $folder);
		
		array_pop($elements);
		$previous = !empty($elements) ? implode($ds, $elements) : ''; 
		
		return $return ? (!empty($previous) ? '&folder='.rsfilesHelper::encode($previous) : '') : $previous;
	}
	
	// Get previous path
	public static function getPreviousPath() {
		$path		= rsfilesHelper::getPath();
		$ds			= rsfilesHelper::ds();
		$elements	= explode($ds, $path);
		
		array_pop($elements);
		
		return rsfilesHelper::encode(implode($ds, $elements));
	}
	
	// Get the preview
	public static function preview($id) {
		$db			= JFactory::getDbo();
		$app		= JFactory::getApplication();
		$config		= rsfilesHelper::getConfig();
		$doc		= JFactory::getDocument();
		$briefcase	= rsfilesHelper::isBriefcase();
		$host		= JURI::getInstance()->toString(array('scheme','host'));
		$ds			= rsfilesHelper::ds();
		$userid		= JFactory::getUser()->get('id');
		$preview	= false;
		
		if ($briefcase) {
			if (rsfilesHelper::briefcase('CanMaintainBriefcase'))
				$root = $config->briefcase_folder;
			else 
				$root = $config->briefcase_folder.$ds.$userid;
		} else {
			$root = $config->download_folder;
		}
		
		if (!empty($id)) {
			$query = $db->getQuery(true)
						->select($db->qn('FilePath'))->select($db->qn('preview'))
						->select($db->qn('FileType'))
						->from($db->qn('#__rsfiles_files'))
						->where($db->qn('IdFile').' = '.(int) $id);
			$db->setQuery($query);
			if ($file = $db->loadObject()) {
				if (!empty($file->preview)) {
					$fullpath = JPATH_SITE.'/components/com_rsfiles/images/preview/'.$file->preview;
					$path = $file->preview;
					$preview = true;
				} else {
					$fullpath = $file->FileType ? $file->FilePath : $root.$ds.$file->FilePath;
					if ($file->FileType) {
						$path = (int) $id;
					} else {
						$path = $file->FilePath;
					}
				}
				
				if (!$file->FileType) {
					$fullpath = realpath($fullpath);
				}
			}
		} else {
			$path = rsfilesHelper::getPath();
			$fullpath = rsfilesHelper::root(true).$path;
		}
		
		$player = JURI::root().'components/com_rsfiles/assets/flowplayer/';
		$images	= array('jpg','jpeg','gif','png');
		$text	= array('pdf','txt');
		$audio	= array('mp3','ogg');
		$video	= array('mp4','mov','webm');

		$allowed	= array_merge($images,$text,$audio,$video);
		$extension	= strtolower(rsfilesHelper::getExt($fullpath));
		$extensions = rsfilesHelper::previewExtensions();
		
		if (!in_array($extension,$allowed)) {
			echo JText::_('COM_RSFILES_NO_PREVIEW');
			exit;
		} else {
			if ($extension == 'pdf') {
				header("Content-type:application/pdf");
				header("Content-Disposition:inline;filename=Preview");
				rsfilesHelper::readfile_chunked($fullpath);
				exit();
			}
			
			if ($extension == 'txt') {
				$o = fopen($fullpath,'r');
				$c = fread($o,rsfilesHelper::filesize($fullpath));
				fclose($o);
				echo '<pre>'.nl2br($c).'</pre>';
				exit();
			}
			
			if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'png') {
				jimport('joomla.environment.browser');
				$browser = JBrowser::getInstance();
				
				if ($extension == 'jpg') $extension = 'jpeg';
				
				if ($browser->getBrowser() != 'msie') 
					header('Content-Type: image/'.$extension);
				
				rsfilesHelper::readfile_chunked($fullpath);
				exit();
			}
			
			$url = 'index.php?option=com_rsfiles&task=filepath';
			$url .= '&path='.urlencode(urlencode($path));
			$url .= $preview ? '&preview=1' : ''; 
			$url .= $briefcase ? '&from=briefcase' : '';
			
			$thepath = $host.JRoute::_($url,false);
			
			if ($app->isAdmin()) {
				$thepath = str_replace('/administrator','',$thepath);
			}
			
			if (in_array($extension,$audio)) {
				$doc->addCustomTag('<script src="'.JURI::root(true).'/components/com_rsfiles/assets/jplayer/jplayer.min.js" type="text/javascript"></script>');
				$doc->addStyleSheet(JURI::root(true).'/components/com_rsfiles/assets/jplayer/jplayer.min.css');
				
				$js	  = array();
				$js[] = 'jQuery(document).ready(function(){';
				$js[] = "\t".'jQuery("#rsfiles-audio-player").jPlayer({';
				$js[] = "\t\t".'ready: function (event) {';
				$js[] = "\t\t\t".'jQuery(this).jPlayer("setMedia", {';
				$js[] = "\t\t\t\t".'mp3: "'.$thepath.'"';
				$js[] = "\t\t\t".'});';
				$js[] = "\t\t".'},';
				$js[] = "\t\t".'supplied: "mp3",';
				$js[] = "\t\t".'solution: "html,flash",';
				$js[] = "\t\t".'swfPath: "'.JURI::root().'components/com_rsfiles/assets/jplayer/",';
				$js[] = "\t\t".'wmode: "window",';
				$js[] = "\t\t".'useStateClassSkin: true,';
				$js[] = "\t\t".'autoBlur: false,';
				$js[] = "\t\t".'smoothPlayBar: true,';
				$js[] = "\t\t".'keyEnabled: false,';
				$js[] = "\t\t".'remainingDuration: true,';
				$js[] = "\t\t".'cssSelectorAncestor: "#jp_container",';
				$js[] = "\t\t".'toggleDuration: true';
				$js[] = "\t".'});';
				$js[] = '});';
				
				$doc->addScriptDeclaration(implode("\n",$js));
				
				echo '<div id="rsfiles-audio-player" class="jp-jplayer"></div>'."\n";
				echo "\t".'<div id="jp_container" class="jp-audio" role="application" aria-label="media player">'."\n";
				echo "\t\t".'<div class="jp-type-single">'."\n";
				echo "\t\t\t".'<div class="jp-gui jp-interface">'."\n";
				echo "\t\t\t\t".'<div class="jp-controls">'."\n";
				echo "\t\t\t\t\t".'<button class="jp-play" role="button" tabindex="0">&nbsp;</button>'."\n";
				echo "\t\t\t\t\t".'<button class="jp-stop" role="button" tabindex="0">&nbsp;</button>'."\n";
				echo "\t\t\t\t".'</div>'."\n";
				echo "\t\t\t\t".'<div class="jp-progress">'."\n";
				echo "\t\t\t\t\t".'<div class="jp-seek-bar">'."\n";
				echo "\t\t\t\t\t".'<div class="jp-play-bar"></div>'."\n";
				echo "\t\t\t\t".'</div>'."\n";
				echo "\t\t\t".'</div>'."\n";
				echo "\t\t\t".'<div class="jp-volume-controls">'."\n";
				echo "\t\t\t\t".'<button class="jp-mute" role="button" tabindex="0">&nbsp;</button>'."\n";
				echo "\t\t\t\t".'<button class="jp-volume-max" role="button" tabindex="0">&nbsp;</button>'."\n";
				echo "\t\t\t\t".'<div class="jp-volume-bar">'."\n";
				echo "\t\t\t\t\t".'<div class="jp-volume-bar-value"></div>'."\n";
				echo "\t\t\t\t".'</div>'."\n";
				echo "\t\t\t".'</div>'."\n";
				echo "\t\t\t".'<div class="jp-time-holder">'."\n";
				echo "\t\t\t\t".'<div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>'."\n";
				echo "\t\t\t\t".'<div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>'."\n";
				echo "\t\t\t".'</div>'."\n";
				echo "\t\t\t".'</div>'."\n";
				echo "\t\t\t".'<div class="jp-no-solution">'."\n";
				echo "\t\t\t\t".'<span>Update Required</span>'."\n";
				echo "\t\t\t\t".'To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.'."\n";
				echo "\t\t\t".'</div>'."\n";
				echo "\t\t".'</div>'."\n";
				echo "\t".'</div>'."\n";
			}
			
			if (in_array($extension,$video)) {
				$doc->addCustomTag('<script src="'.JURI::root(true).'/components/com_rsfiles/assets/jplayer/jplayer.min.js" type="text/javascript"></script>');
				$doc->addStyleSheet(JURI::root(true).'/components/com_rsfiles/assets/jplayer/jplayer.min.css');
				
				$js	  = array();
				$js[] = 'jQuery(document).ready(function(){';
				$js[] = "\t".'jQuery("#rsfiles-video-player").jPlayer({';
				$js[] = "\t\t".'ready: function (event) {';
				$js[] = "\t\t\t".'jQuery(this).jPlayer("setMedia", {';
				$js[] = "\t\t\t\t".'m4v: "'.$thepath.'"';
				$js[] = "\t\t\t".'});';
				$js[] = "\t\t".'},';
				$js[] = "\t\t".'size: {';
				$js[] = "\t\t\t".'width: "100%",';
				$js[] = "\t\t\t".'height: "360px",';
				$js[] = "\t\t\t".'cssClass: "rsfiles-full-video"';
				$js[] = "\t\t".'},';
				$js[] = "\t\t".'supplied: "m4v",';
				$js[] = "\t\t".'solution: "html,flash",';
				$js[] = "\t\t".'swfPath: "'.JURI::root().'components/com_rsfiles/assets/jplayer/",';
				$js[] = "\t\t".'useStateClassSkin: true,';
				$js[] = "\t\t".'autoBlur: false,';
				$js[] = "\t\t".'smoothPlayBar: true,';
				$js[] = "\t\t".'keyEnabled: false,';
				$js[] = "\t\t".'remainingDuration: true,';
				$js[] = "\t\t".'toggleDuration: true';
				$js[] = "\t".'});';
				$js[] = '});';

				$doc->addScriptDeclaration(implode("\n",$js));
				
				echo '<div id="jp_container_1" class="jp-video jp-video-360p" role="application" aria-label="media player">'."\n";
				echo "\t".'<div class="jp-type-single">'."\n";
				echo "\t".'<div id="rsfiles-video-player" class="jp-jplayer"></div>'."\n";
				echo "\t".'<div class="jp-gui">'."\n";
				echo "\t".'<div class="jp-video-play">'."\n";
				echo "\t".'<button class="jp-video-play-icon" role="button" tabindex="0">play</button>'."\n";
				echo "\t".'</div>'."\n";
				echo "\t".'<div class="jp-interface">'."\n";
				echo "\t".'<div class="jp-progress">'."\n";
				echo "\t".'<div class="jp-seek-bar">'."\n";
				echo "\t".'<div class="jp-play-bar"></div>'."\n";
				echo "\t".'</div>'."\n";
				echo "\t".'</div>'."\n";
				echo "\t".'<div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>'."\n";
				echo "\t".'<div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>'."\n";
				echo "\t".'<div class="jp-controls-holder">'."\n";
				echo "\t".'<div class="jp-controls">'."\n";
				echo "\t".'<button class="jp-play" role="button" tabindex="0">&nbsp;</button>'."\n";
				echo "\t".'<button class="jp-stop" role="button" tabindex="0">&nbsp;</button>'."\n";
				echo "\t".'</div>'."\n";
				echo "\t".'<div class="jp-volume-controls">'."\n";
				echo "\t".'<button class="jp-mute" role="button" tabindex="0">&nbsp;</button>'."\n";
				echo "\t".'<button class="jp-volume-max" role="button" tabindex="0">&nbsp;</button>'."\n";
				echo "\t".'<div class="jp-volume-bar">'."\n";
				echo "\t".'<div class="jp-volume-bar-value"></div>'."\n";
				echo "\t".'</div>'."\n";
				echo "\t".'</div>'."\n";
				echo "\t".'<div class="jp-toggles">'."\n";
				echo "\t".'<button class="jp-full-screen" role="button" tabindex="0">&nbsp;</button>'."\n";
				echo "\t".'</div>'."\n";
				echo "\t".'</div>'."\n";
				echo "\t".'</div>'."\n";
				echo "\t".'</div>'."\n";
				echo "\t".'<div class="jp-no-solution">'."\n";
				echo "\t".'<span>Update Required</span>'."\n";
				echo "\t".'To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.'."\n";
				echo "\t".'</div>'."\n";
				echo "\t".'</div>'."\n";
				echo "\t".'</div>'."\n";
			}
		}
	}
	
	public static function previewProperties($id, $path = null) {
		$db			= JFactory::getDbo();
		$config		= rsfilesHelper::getConfig();
		$briefcase	= rsfilesHelper::isBriefcase();
		$ds			= rsfilesHelper::ds();
		$userid		= JFactory::getUser()->get('id');
		$size		= 'size: {x: 800, y: 600}';
		$handler	= 'iframe';
		$root		= $briefcase ? $config->briefcase_folder : $config->download_folder;
		
		if (!empty($id)) {
			$query = $db->getQuery(true)
						->select($db->qn('FilePath'))->select($db->qn('preview'))
						->select($db->qn('FileType'))
						->from($db->qn('#__rsfiles_files'))
						->where($db->qn('IdFile').' = '.(int) $id);
			$db->setQuery($query);
			if ($file = $db->loadObject()) {
				if (!empty($file->preview)) {
					$fullpath = JPATH_SITE.'/components/com_rsfiles/images/preview/'.$file->preview;
				} else {
					$fullpath = $file->FileType ? $file->FilePath : $root.$ds.$file->FilePath;
				}
			}
		} else {
			$path = !is_null($path) ? $path : '';
			$fullpath = rsfilesHelper::root(true).$path;
		}
		
		$extension	= rsfilesHelper::getExt($fullpath);
		$extension	= strtolower($extension);
		
		if ($extension == 'mp3' || $extension == 'ogg') {
			$size = 'size: {x: 650, y: 100}';
		} else if (in_array($extension,array('mp4','mov','webm'))) {
			$size = 'size: {x: 700, y: 450}';
		} else {
			$size = 'size: {x: 660, y: 475}';
		}
		
		$handler = in_array($extension,array('jpg','jpeg','png','gif')) ? 'image' : 'iframe';
		
		return array('extension' => $extension, 'size' => $size, 'handler' => $handler);
	}
	
	public static function filesize($file) {
		$fp 	= @fopen($file, 'r');
		$return = false;
		
		if (is_resource($fp)) {
			if (PHP_INT_SIZE < 8) {
				// 32bit
				if (0 === fseek($fp, 0, SEEK_END)) {
					$return = 0.0;
					$step = 0x7FFFFFFF;
					
					while ($step > 0) {
						if (0 === fseek($fp, - $step, SEEK_CUR)) {
							$return += floatval($step);
						} else {
							$step >>= 1;
						}
					}
				}
			} elseif (0 === fseek($fp, 0, SEEK_END)) {
				// 64bit
				$return = ftell($fp);
			}
		}
		
		return $return;
	}
	
	public static function checkHash() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$hash	= JFactory::getApplication()->input->getString('hash','');
		
		if ($moderation_email = rsfilesHelper::getMessage('moderate')) {
			if (!empty($moderation_email->to)) {
				if ($emails = explode(',',$moderation_email->to)) {
					
					$query->clear()
						->select($db->qn('IdFile'))
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
					if ($db->loadResult()) {
						return true;
					}
				}
			}
		}
		
		return false;
	}
	
	public static function downloads($path) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		
		if (rsfilesHelper::external($path)) {
			$fileID = (int) $path;
		} else {
			$query->select($db->qn('IdFile'))
				->from($db->qn('#__rsfiles_files'))
				->where($db->qn('FilePath').' = '.$db->q($path));
			
			if (rsfilesHelper::isBriefcase()) {
				$query->where($db->qn('briefcase').' = 1');
			} else {
				$query->where($db->qn('briefcase').' = 0');
			}
			
			$db->setQuery($query);
			$fileID = (int) $db->loadResult();
		}
		
		if ($fileID) {
			$query->clear()
				->select('COUNT('.$db->qn('IdStatistic').')')
				->from($db->qn('#__rsfiles_statistics'))
				->where($db->qn('IdFile').' = '.$db->q($fileID));
			$db->setQuery($query);
			return (int) $db->loadResult();
		}
		
		return 0;
	}
	
	public static function isiOS() {
		$userAgent	= $_SERVER['HTTP_USER_AGENT'];
		
		if (stripos($userAgent, 'iPod') || stripos($userAgent, 'iPhone') || stripos($userAgent, 'iPad')) {
			// Check for Windows phones
			if (stripos($userAgent, 'Windows') === false) {
				return true;
			}
		}
		
		return false;
	}
	
	public static function isSafariWin() {
		$userAgent	= $_SERVER['HTTP_USER_AGENT'];
		
		return stripos($userAgent, 'Safari') && !stripos($userAgent, 'Chrome') && stripos($userAgent, 'Windows');
	}
	
	public static function rangeDownload($file) {
        $fp = @fopen($file, 'rb');

        $size   = filesize($file); // File size
        $length = $size;           // Content length
        $start  = 0;               // Start byte
        $end    = $size - 1;       // End byte

        header("Accept-Ranges: 0-$length");
        
        if (isset($_SERVER['HTTP_RANGE'])) {
            $c_start = $start;
            $c_end   = $end;

            // Extract the range string
            list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            // Make sure the client hasn't sent us a multibyte range
            if (strpos($range, ',') !== false) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $start-$end/$size");
                exit;
            }
            // If the range starts with an '-' we start from the beginning
            // If not, we forward the file pointer
            // And make sure to get the end byte if spesified
            if ($range{0} == '-') {
                // The n-number of the last bytes is requested
                $c_start = $size - substr($range, 1);
            } else {
                $range  = explode('-', $range);
                $c_start = $range[0];
                $c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
            }
            /* Check the range and make sure it's treated according to the specs.
             * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
             */
            // End bytes can not be larger than $end.
            $c_end = ($c_end > $end) ? $end : $c_end;
            // Validate the requested range and return an error if it's not correct.
            if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size){
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $start-$end/$size");
                // (?) Echo some info to the client?
                exit;
            }
			
            $start  = $c_start;
            $end    = $c_end;
            $length = $end - $start + 1; // Calculate new content length
            fseek($fp, $start);
            header('HTTP/1.1 206 Partial Content');
        }

        // Notify the client the byte range we'll be outputting
        header("Content-Range: bytes $start-$end/$size");
        header("Content-Length: $length");

        // Start buffered download
        $buffer = 1024 * 8;
        while(!feof($fp) && ($p = ftell($fp)) <= $end){
            if ($p + $buffer > $end){
                // In case we're only outputtin a chunk, make sure we don't read past the length
                $buffer = $end - $p + 1;
            }

            set_time_limit(0); // Reset time limit for big files
            echo fread($fp, $buffer);
            flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
        }

        fclose($fp);
    }
	
	public static function getChunkSize() {
		$chunk	= (int) rsfilesHelper::getConfig('chunk_size');
		$chunk	= round($chunk * 1024);
		$max	= rsfilesHelper::maxUploadBytes();
		
		return $chunk > $max ? $max : $chunk;
	}
	
	public static function maxUploadBytes() {
		static $max_size = -1;

		if ($max_size < 0) {
			$max_size = rsfilesHelper::toBytes(ini_get('post_max_size'));

			$upload_max = rsfilesHelper::toBytes(ini_get('upload_max_filesize'));
			if ($upload_max > 0 && $upload_max < $max_size) {
				$max_size = $upload_max;
			}
		}
		
		return $max_size - 20480;
	}

	public static function toBytes($size) {
		$unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
		$size = preg_replace('/[^0-9\.]/', '', $size);
		
		if ($unit) {
			return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
		} else {
			return round($size);
		}
	}
	
	public static function sendMail($from, $fromName, $recipient, $subject, $body, $mode = false, $cc = null, $bcc = null, $attachment = null, $replyTo = null, $replyToName = null) {
		jimport('joomla.mail.helper');
		
		$mailer	= JFactory::getMailer();
		
		$recipient		= array_filter(preg_split('/[;,]+/', $recipient), array('JMailHelper', 'isEmailAddress'));
		$cc 			= !is_null($cc) ? array_filter(preg_split('/[;,]+/', $cc), array('JMailHelper', 'isEmailAddress')) : null;
		$bcc			= !is_null($bcc) ? array_filter(preg_split('/[;,]+/', $bcc), array('JMailHelper', 'isEmailAddress')) : null;
		$replyTo		= !is_null($replyTo) ? array_filter(preg_split('/[;,]+/', $replyTo), array('JMailHelper', 'isEmailAddress')) : null;
		$replyToName	= !is_null($replyToName) ? array_filter(preg_split('/[;,]+/', $replyToName)) : null;
		
		return $mailer->sendMail($from, $fromName, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyTo, $replyToName);
	}
}