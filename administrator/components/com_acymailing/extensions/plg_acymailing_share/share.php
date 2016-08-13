<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

class plgAcymailingShare extends JPlugin{
	var $pictresults = array();

	function __construct(&$subject, $config){
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('acymailing', 'share');
			$this->params = new acyParameter($plugin->params);
		}
	}

	function acymailing_getPluginType(){

		$app = JFactory::getApplication();
		if($this->params->get('frontendaccess') == 'none' && !$app->isAdmin()) return;
		$onePlugin = new stdClass();
		$onePlugin->name = JText::sprintf('SOCIAL_SHARE', '...');
		$onePlugin->function = 'acymailingtagshare_show';
		$onePlugin->help = 'plugin-share';

		return $onePlugin;
	}

	function _getPictures($folder){
		$allFolders = JFolder::folders($folder);
		foreach($allFolders as $oneFolder){
			$this->_getPictures($folder.DS.$oneFolder);
		}
		$allFiles = JFolder::files($folder, $this->regex);
		foreach($allFiles as $oneFile){
			$this->pictresults[substr($oneFile, 0, 4)][$oneFile.filesize($folder.DS.$oneFile)] = $folder.DS.$oneFile;
		}
	}

	function acymailingtagshare_show(){

		$networks = array();
		$networks['facebook'] = 'Facebook';
		$networks['linkedin'] = 'LinkedIn';
		$networks['twitter'] = 'Twitter';
		$networks['hyves'] = 'Hyves';
		$networks['google'] = 'Google+';
		$networks['print'] = JText::_('ACY_PRINT');

		$k = 0;
		jimport('joomla.filesystem.folder');
		$this->regex = '('.implode('|', array_keys($networks)).').*(png|gif|jpeg|jpg)';
		$this->_getPictures(ACYMAILING_MEDIA);

		echo '<br style="clear:both;">';
		foreach($networks as $name => $desc){
			$shortName = substr($name, 0, 4);
			if(empty($this->pictresults[$shortName])) continue;

			if($desc == JText::_('ACY_PRINT')){
				$legendTxt = $desc;
			}else{
				$legendTxt = JText::sprintf('SOCIAL_SHARE', $desc);
			}

			echo '<div class="onelineblockoptions">
					<span class="acyblocktitle">'.$legendTxt.'</span>';
			foreach($this->pictresults[$shortName] as $onePict){
				$imgPath = preg_replace('#^'.preg_quote(ACYMAILING_ROOT, '#').'#i', ACYMAILING_LIVE, $onePict);
				$imgPath = str_replace(DS, '/', $imgPath);

				if($desc == JText::_('ACY_PRINT')){
					$insertedtag = '<a target="_blank" href="{print:newsletter}" title="'.JText::_('ACY_PRINT').'" ><img src="'.$imgPath.'" alt="'.$desc.'" /></a>';
				}else $insertedtag = '<a target="_blank" href="{sharelink:'.$name.'}" title="'.JText::sprintf('SOCIAL_SHARE', $desc).'" ><img src="'.$imgPath.'" alt="'.$desc.'" /></a>';

				echo '<img style="max-width:200px;cursor:pointer;padding:5px;" onclick="setTag(\''.htmlentities($insertedtag).'\');insertTag();" src="'.$imgPath.'" />';
			}
			echo '</div>';
			$k = 1 - $k;
		}
	}


	function acymailing_replacetags(&$email, $send = true){
		$match = '#(?:{|%7B)(share|sharelink):(.*)(?:}|%7D)#Ui';
		$variables = array('body', 'altbody');
		$found = false;
		$results = array();
		foreach($variables as $var){
			if(empty($email->$var)) continue;
			$found = preg_match_all($match, $email->$var, $results[$var]) || $found;
			if(empty($results[$var][0])) unset($results[$var]);
		}

		if(!$found) return;

		$archiveLink = acymailing_frontendLink('index.php?option=com_acymailing&ctrl=archive&task=view&mailid='.$email->mailid, $this->params->get('template', 'component') == 'component' ? true : false);
		if(empty($email->published)){
			$archiveLink .= (strpos($archiveLink, '?') ? '&' : '?').'time='.time();
		}

		$tags = array();
		foreach($results as $var => $allresults){
			foreach($allresults[0] as $numres => $tagname){
				if(isset($tags[$tagname])) continue;
				$arguments = explode('|', $allresults[2][$numres]);
				$tag = new stdClass();
				$tag->network = $arguments[0];
				for($i = 1, $a = count($arguments); $i < $a; $i++){
					$args = explode(':', $arguments[$i]);
					if(isset($args[1])){
						$tag->{$args[0]} = $args[1];
					}else{
						$tag->{$args[0]} = true;
					}
				}

				$link = '';
				if($tag->network == 'facebook'){
					$link = 'http://www.facebook.com/sharer.php?u='.urlencode($archiveLink).'&t='.urlencode($email->subject);
					$tags[$tagname] = '<a target="_blank" href="'.$link.'" title="'.JText::sprintf('SOCIAL_SHARE', 'Facebook').'"><img alt="Facebook" src="'.ACYMAILING_LIVE.$this->params->get('picturefb', 'media/com_acymailing/images/facebookshare.png').'" /></a>';
				}elseif($tag->network == 'twitter'){
					$text = JText::sprintf('SHARE_TEXT', $archiveLink);
					$link = 'http://twitter.com/home?status='.urlencode($text);
					$tags[$tagname] = '<a target="_blank" href="'.$link.'" title="'.JText::sprintf('SOCIAL_SHARE', 'Twitter').'"><img alt="Twitter" src="'.ACYMAILING_LIVE.$this->params->get('picturetwitter', 'media/com_acymailing/images/twittershare.png').'" /></a>';
				}elseif($tag->network == 'linkedin'){
					$link = 'http://www.linkedin.com/shareArticle?mini=true&url='.urlencode($archiveLink).'&title='.urlencode($email->subject);
					$tags[$tagname] = '<a target="_blank" href="'.$link.'" title="'.JText::sprintf('SOCIAL_SHARE', 'LinkedIn').'"><img alt="LinkedIn" src="'.ACYMAILING_LIVE.$this->params->get('picturelinkedin', 'media/com_acymailing/images/linkedin.png').'" /></a>';
				}elseif($tag->network == 'hyves'){
					$link = 'http://www.hyves-share.nl/button/respect/?hc_hint=1&url='.urlencode($archiveLink).'&title='.urlencode($email->subject);
					$tags[$tagname] = '<a target="_blank" href="'.$link.'" title="'.JText::sprintf('SOCIAL_SHARE', 'Hyves').'"><img alt="Hyves" src="'.ACYMAILING_LIVE.$this->params->get('picturehyves', 'media/com_acymailing/images/hyvesshare.png').'" /></a>';
				}elseif($tag->network == 'google'){
					$link = 'https://plus.google.com/share?url='.urlencode($archiveLink);
					$tags[$tagname] = '<a target="_blank" href="'.$link.'" title="'.JText::sprintf('SOCIAL_SHARE', 'Google+').'"><img alt="Google+" src="'.ACYMAILING_LIVE.$this->params->get('picturegoogleplus', 'media/com_acymailing/images/google_plusshare.png').'" /></a>';
				}

				if($allresults[1][$numres] == 'sharelink'){
					$tags[$tagname] = $link;
				}

				if(file_exists(ACYMAILING_MEDIA.'plugins'.DS.'share.php')){
					ob_start();
					require(ACYMAILING_MEDIA.'plugins'.DS.'share.php');
					$tags[$tagname] = ob_get_clean();
				}
			}
		}

		$email->body = str_replace(array_keys($tags), $tags, $email->body);
		$email->altbody = str_replace(array_keys($tags), '', $email->altbody);
	}

	function acymailing_replaceusertags(&$email, &$user, $send = true){
		$variables = array('subject', 'body', 'altbody');
		$acypluginsHelper = acymailing_get('helper.acyplugins');
		$tags = $acypluginsHelper->extractTags($email, 'print');

		$archiveLink = acymailing_frontendLink('index.php?option=com_acymailing&ctrl=archive&task=view&mailid='.$email->mailid, $this->params->get('template', 'component') == 'component' ? true : false);
		$addkey = (!empty($email->key)) ? '&key='.$email->key : '';
		$adduserkey = (!empty($user->key)) ? '&subid='.$user->subid.'-'.$user->key : '';
		$link = $archiveLink.'&print=1'.$addkey.$adduserkey;

		foreach($variables as $var){
			if(empty($email->$var)) continue;
			$email->$var = str_replace(array_keys($tags), $link, $email->$var);
		}
	}
}//endclass
