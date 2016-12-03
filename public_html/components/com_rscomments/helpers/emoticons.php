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
			$return[] = '<a href="javascript:void(0);" class="btn btn-mini" onclick="rsc_smiley(\''.htmlspecialchars(addslashes($tag),ENT_COMPAT,'UTF-8').'\')"><img src="'.$img.'" alt="'.htmlspecialchars($tag,ENT_COMPAT,'UTF-8').'" /></a>';
		}
		
		return $return;
	}
	
	public static function setEmoticons() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$emoticons	= array();
		
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
			$emoticons[':confused:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/confused.gif';
			$emoticons[':cool:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/cool.gif';
			$emoticons[':cry:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/cry.gif';
			$emoticons[':laugh:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/laugh.gif';
			$emoticons[':lol:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/lol.gif';
			$emoticons[':normal:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/normal.gif';
			$emoticons[':blush:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/redface.gif';
			$emoticons[':rolleyes:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/rolleyes.gif';
			$emoticons[':sad:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/sad.gif';
			$emoticons[':shocked:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/shocked.gif';
			$emoticons[':sick:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/sick.gif';
			$emoticons[':sleeping:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/sleeping.gif';
			$emoticons[':smile:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/smile.gif';
			$emoticons[':surprised:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/surprised.gif';
			$emoticons[':tongue:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/tongue.gif';
			$emoticons[':unsure:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/unsure.gif';
			$emoticons[':whistle:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/whistling.gif';
			$emoticons[':wink:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/wink.gif';
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