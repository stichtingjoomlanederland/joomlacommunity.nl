<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\LanguageHelper;

if (!function_exists('mb_strlen')) {
	function mb_strlen($string, $encoding = 'UTF-8') {
		return strlen(utf8_decode($string));
	}
}

if (!function_exists('mb_substr')) {
	function mb_substr($string, $start, $length = null, $encoding = 'UTF-8') {
		return implode("", array_slice(preg_split("//u", $string, -1, PREG_SPLIT_NO_EMPTY), $start, $length));
	}
}

abstract class RSCommentsHelper {
	
	// Get component configuration
	public static function getConfig($name = null, $default = null) {
		static $config;
		
		if (!is_object($config)) {
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);
			$config = new stdClass();
			
			$query->clear();
			$query->select($db->qn('params'));
			$query->from($db->qn('#__extensions'));
			$query->where($db->qn('type').' = '.$db->q('component'));
			$query->where($db->qn('element').' = '.$db->q('com_rscomments'));
			$db->setQuery($query);
			$params = $db->loadResult();
			
			// Convert the params to an object.
			if (is_string($params)) {
				$temp = new JRegistry;
				$temp->loadString($params);
				$config = $temp->toObject();
			}
		}
		
		if ($name != null) {
			if (isset($config->{$name})) {
				return $config->{$name};
			} else {
				if (!is_null($default)) {
					return $default;
				} else {
					return false;
				}
			}
		} else {
			return $config;
		}
	}
	
	// Check for Joomla! 3.0
	public static function isJ3() {
		return version_compare(JVERSION, '3.0', '>=');
	}

	// Get update code
	public static function genKeyCode() {
		if ($code = RSCommentsHelper::getConfig('global_register_code')) {
			$version = new RSCommentsVersion();
			return md5($code.$version->key);
		}
	}
	
	// Load jQuery
	public static function loadjQuery($noconflict = true) {
		$enable = RSCommentsHelper::getConfig('backend_jquery', 1);
		
		if ($enable) {
			JHtml::_('jquery.framework', $noconflict);
		}
	}
	
	//set scripts
	public static function setScripts() {
		RSCommentsHelper::loadjQuery();
			
		JHtml::_('formbehavior.chosen', 'select');
		
		JHtml::stylesheet('com_rscomments/admin.css', array('relative' => true, 'version' => 'auto'));
		
		if (RSCommentsHelper::getConfig('fontawesome_admin') == 1) {
			JHtml::stylesheet('com_rscomments/font-awesome.min.css', array('relative' => true, 'version' => 'auto'));
		}
		
		RSCommentsHelper::cleancache();
		RSCommentsHelper::submenu();
	}
	
	// Clean the cache
	public static function cleancache() {
		JFactory::getCache('com_rscomments')->clean();
	}
	
	public static function showDate($date, $format = null) {
		$date_format = !is_null($format) ? $format : RSCommentsHelper::getConfig('date_format');
		return JHtml::date($date, $date_format);
	}

	public static function cleanComment($comment) {
		$patterns = array();
		$replacements = array();
		
		$patterns[] = '/\[b\](.*?)\[\/b\]/i';
		$replacements[] = '\\1';

		$patterns[] = '/\[i\](.*?)\[\/i\]/i';
		$replacements[] = '\\1';

		$patterns[] = '/\[u\](.*?)\[\/u\]/i';
		$replacements[] = '\\1';

		$patterns[] = '/\[s\](.*?)\[\/s\]/i';
		$replacements[] = '\\1';

		$patterns[] = '/\[url\]([ a-zA-Z0-9\:\/\-\?\&\.\=\_\~\#\']*)\[\/url\]/i';
		$replacements[] = '\\1';

		$patterns[] = '#\[img\](http:\/\/)?([^\s\<\>\(\)\"\']*?)\[\/img\]#i';
		$replacements[] = '\\2';

		$patterns[] = '#\[code\](.*?)\[\/code\]#ism';
		$replacements[] = '\\1';

		// Youtube
		$patterns[] = '/\[youtube\](.+?)\[\/youtube\]/';
		$replacements[] = '\\1';
		
		// Vimeo
		$patterns[] = '/\[vimeo\](.+?)\[\/vimeo\]/';
		$replacements[] = '\\1';
			
		$comment = preg_replace($patterns, $replacements, $comment);
		
		
		// QUOTE
		$quotePattern = '#\[quote\s?name=\"([^\"\'\<\>\(\)]+)+\"\](<br\s?\/?\>)*(.*?)(<br\s?\/?\>)*\[\/quote\]#i';
		$quoteReplace = '\\3';
		while(preg_match($quotePattern, $comment)) {
			$comment = preg_replace($quotePattern, $quoteReplace, $comment);
		}
		$quotePattern = '#\[quote[^\]]*?\](<br\s?\/?\>)*([^\[]+)(<br\s?\/?\>)*\[\/quote\]#i';
		$quoteReplace = '\\2';
		while(preg_match($quotePattern, $comment)) {
			$comment = preg_replace($quotePattern, $quoteReplace, $comment);
		}

		$comment = preg_replace('#\[\/?(b|i|u|s|url|img|list|quote|code)\]#', '', $comment);
		$smiley = array(':confused:',':cool:',':cry:',':laugh:',':lol:',':normal:',':blush:',':rolleyes:',':sad:',':shocked:',':sick:',':sleeping:',':smile:',':surprised:',':tongue:',':unsure:',':whistle:',':wink:');
		$comment = str_replace($smiley,'',$comment);
		
		return $comment;
	}
	
	public static function loadLang($admin = false) {
		$lang = JFactory::getLanguage();
		$from = $admin ? JPATH_ADMINISTRATOR : JPATH_SITE;
		
		$lang->load('com_rscomments', $from, 'en-GB', true);
		$lang->load('com_rscomments', $from, $lang->getDefault(), true);
		$lang->load('com_rscomments', $from, null, true);
		
		return true;
	}
	
	public static function ArticleTitle($component, $component_id) {
		$components = array('com_content', 'com_rsblog', 'com_k2', 'com_flexicontent');
		
		if(!empty($component_id) && in_array($component,$components)) {
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);

			$query->select($db->qn('title'));
			switch($component) {
				case 'com_content':
					$query->from($db->qn('#__content'));
				break;
				case 'com_rsblog':
					$query->from($db->qn('#__rsblog_posts'));
				break;
				case 'com_k2':
					$query->from($db->qn('#__k2_items'));
				break;
				case 'com_flexicontent':
					$query->from($db->qn('#__flexicontent_items'));
				break;
			}

			$query->where($db->qn('id').' = '.$component_id);
			$db->setQuery($query);

			return $db->loadResult();
		} else 
			return '';
	}
	
	public static function component($option) {
		$lang = JFactory::getLanguage();
		$lang->load($option.'.sys');
		$component = strtoupper($option);
		
		return $lang->hasKey($component) ? JText::_($component) : $option;
	}
	
	public static function getComponents() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$values	= array();
		
		$query->select($db->qn('element'))
			->from($db->qn('#__extensions'))
			->where($db->qn('type').' = '.$db->q('component'))
			->where($db->qn('enabled').' = 1')
			->where("`element` NOT IN ('com_admin', 'com_wrapper', 'com_weblinks', 'com_users', 'com_user', 'com_templates', 'com_search','com_poll', 'com_plugins', 'com_newsfeeds', 'com_modules', 'com_messages', 'com_menus', 'com_media', 'com_massmail', 'com_mailto', 'com_languages', 'com_installer', 'com_cpanel', 'com_config', 'com_cache', 'com_checkin', 'com_finder', 'com_joomlaupdate', 'com_login', 'com_redirect', 'com_ajax', 'com_contenthistory', 'com_postinstall', 'com_tags')")
			->order('ordering, name');
		$db->setQuery($query);
		if ($extensions = $db->loadColumn()) {
			$values = array_merge($values, $extensions);
		}
		
		$query->clear()
			->select('DISTINCT('.$db->qn('option').')')
			->from($db->qn('#__rscomments_comments'));
		$db->setQuery($query);
		if ($options = $db->loadColumn()) {
			$values = array_merge($values,$options);
		}
		
		$values = array_unique($values);
		return $values;
	}
	
	public static function language($tag) {
		$languages = LanguageHelper::getKnownLanguages();
		
		if ($languages && $languages[$tag]) {
			return !empty($languages[$tag]['name']) ? $languages[$tag]['name'] : $tag;
		}
		
		return $tag;
	}
	
	public static function canAdd() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear();
		$query->select('a.id');
		$query->from($db->qn('#__usergroups') . ' AS a');
		$query->join('LEFT', $db->qn('#__usergroups') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
		$query->group('a.id, a.title, a.lft, a.rgt');
		$query->order('a.lft ASC');
		$db->setQuery($query);
		
		if ($groups = $db->loadColumn()) {
			$groups = ArrayHelper::toInteger($groups);
			
			$query->clear();
			$query->select('gid');
			$query->from($db->qn('#__rscomments_groups'));
			$db->setQuery($query);
			if ($gids = $db->loadColumn()) {
				$gids = ArrayHelper::toInteger($gids);
				$diff = array_diff($groups,$gids);
				
				if (empty($diff)) {
					return false;
				}
			}
		}
		
		return true;
	}
	
	// Read a file chunk
	public static function readfile_chunked($filename, $retbytes = true) {
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
	
	public static function submenu() {
		$view = JFactory::getApplication()->input->get('view');
		
		JHtmlSidebar::addEntry(JText::_('COM_RSCOMMENTS_OVERVIEW'),				JRoute::_('index.php?option=com_rscomments'),						$view == 'overview' || $view == '');
		JHtmlSidebar::addEntry(JText::_('COM_RSCOMMENTS_COMMENTS'),				JRoute::_('index.php?option=com_rscomments&view=comments'),			$view == 'comments');
		JHtmlSidebar::addEntry(JText::_('COM_RSCOMMENTS_EMOTICONS'),			JRoute::_('index.php?option=com_rscomments&view=emoticons'),		$view == 'emoticons');
		JHtmlSidebar::addEntry(JText::_('COM_RSCOMMENTS_SUBSCRIPTIONS'),		JRoute::_('index.php?option=com_rscomments&view=subscriptions'),	$view == 'subscriptions');
		JHtmlSidebar::addEntry(JText::_('COM_RSCOMMENTS_GROUP_PERMISSIONS'),	JRoute::_('index.php?option=com_rscomments&view=groups'),			$view == 'groups');
		JHtmlSidebar::addEntry(JText::_('COM_RSCOMMENTS_IMPORT'),				JRoute::_('index.php?option=com_rscomments&view=import'),			$view == 'import');
		JHtmlSidebar::addEntry(JText::_('COM_RSCOMMENTS_MESSAGES'),				JRoute::_('index.php?option=com_rscomments&view=messages'),			$view == 'messages');
	}
}