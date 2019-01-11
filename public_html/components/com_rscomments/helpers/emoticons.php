<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

abstract class RSCommentsEmoticons {	
	
	public static function createEmoticons() {
		$return		= array();
		$emoticons	= self::setEmoticons();
		
		foreach($emoticons as $tag => $img) {
			$return[] = '<a href="javascript:void(0);" class="btn btn-mini" data-rsc-task="bbcode" data-rsc-code="'.htmlspecialchars(addslashes($tag),ENT_COMPAT,'UTF-8').'"><img src="'.$img.'" alt="'.htmlspecialchars($tag,ENT_COMPAT,'UTF-8').'" /></a>';
		}
		
		return $return;
	}
	
	public static function setEmoticons() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$emoticons	= array();
		$root		= JUri::getInstance()->toString(array('scheme','host','port'));
		
		$query->select($db->qn('replace'))->select($db->qn('with'))
			->from($db->qn('#__rscomments_emoticons'));
		$db->setQuery($query);
		if ($data = $db->loadObjectList()) {
			foreach ($data as $emoticon) {
				if (empty($emoticon->replace) || empty($emoticon->with)) {
					continue;
				}
				
				if (strpos($emoticon->with,'http') !== false) {
					$with = $emoticon->with;
				} else {
					$with = JURI::root().$emoticon->with;
				}
				
				$emoticons[$emoticon->replace] = $with;
			}
		} else {
			$emoticons[':confused:'] = $root.JHtml::image('com_rscomments/emoticons/confused.gif', '', array(), true, 1);
			$emoticons[':cool:'] = $root.JHtml::image('com_rscomments/emoticons/cool.gif', '', array(), true, 1);
			$emoticons[':cry:'] = $root.JHtml::image('com_rscomments/emoticons/cry.gif', '', array(), true, 1);
			$emoticons[':laugh:'] = $root.JHtml::image('com_rscomments/emoticons/laugh.gif', '', array(), true, 1);
			$emoticons[':lol:'] = $root.JHtml::image('com_rscomments/emoticons/lol.gif', '', array(), true, 1);
			$emoticons[':normal:'] = $root.JHtml::image('com_rscomments/emoticons/normal.gif', '', array(), true, 1);
			$emoticons[':blush:'] = $root.JHtml::image('com_rscomments/emoticons/redface.gif', '', array(), true, 1);
			$emoticons[':rolleyes:'] = $root.JHtml::image('com_rscomments/emoticons/rolleyes.gif', '', array(), true, 1);
			$emoticons[':sad:'] = $root.JHtml::image('com_rscomments/emoticons/sad.gif', '', array(), true, 1);
			$emoticons[':shocked:'] = $root.JHtml::image('com_rscomments/emoticons/shocked.gif', '', array(), true, 1);
			$emoticons[':sick:'] = $root.JHtml::image('com_rscomments/emoticons/sick.gif', '', array(), true, 1);
			$emoticons[':sleeping:'] = $root.JHtml::image('com_rscomments/emoticons/sleeping.gif', '', array(), true, 1);
			$emoticons[':smile:'] = $root.JHtml::image('com_rscomments/emoticons/smile.gif', '', array(), true, 1);
			$emoticons[':surprised:'] = $root.JHtml::image('com_rscomments/emoticons/surprised.gif', '', array(), true, 1);
			$emoticons[':tongue:'] = $root.JHtml::image('com_rscomments/emoticons/tongue.gif', '', array(), true, 1);
			$emoticons[':unsure:'] = $root.JHtml::image('com_rscomments/emoticons/unsure.gif', '', array(), true, 1);
			$emoticons[':whistle:'] = $root.JHtml::image('com_rscomments/emoticons/whistling.gif', '', array(), true, 1);
			$emoticons[':wink:'] = $root.JHtml::image('com_rscomments/emoticons/wink.gif', '', array(), true, 1);
		}
		
		return $emoticons;
	}
	
	public static function cleanText($text) {
		$emoticons = self::setEmoticons();
		
		foreach($emoticons as $tag => $img)
			$text = str_replace($tag,'<img src="'.$img.'" alt="'.str_replace(':','',htmlspecialchars($tag,ENT_COMPAT,'UTF-8')).'" />',$text);
			
		return $text;
	}
}