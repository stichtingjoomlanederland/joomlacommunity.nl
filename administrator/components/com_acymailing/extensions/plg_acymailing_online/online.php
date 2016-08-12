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

class plgAcymailingOnline extends JPlugin{
	function __construct(&$subject, $config){
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('acymailing', 'online');
			$this->params = new acyParameter($plugin->params);
		}
	}

	function acymailing_getPluginType(){

		$app = JFactory::getApplication();
		if($this->params->get('frontendaccess') == 'none' && !$app->isAdmin()) return;
		$onePlugin = new stdClass();
		$onePlugin->name = JText::_('WEBSITE_LINKS');
		$onePlugin->function = 'acymailingtagonline_show';
		$onePlugin->help = 'plugin-online';

		return $onePlugin;
	}

	function acymailingtagonline_show(){

		$others = array();
		$config = acymailing_config();
		$others['readonline'] = array('default' => JText::_('VIEW_ONLINE', true), 'desc' => JText::_('VIEW_ONLINE_LINK'));
		if($config->get('forward', true)){
			$others['forward'] = array('default' => JText::_('FORWARD_FRIEND', true), 'desc' => JText::_('FORWARD_FRIEND_LINK'));
		}

		?>
		<script language="javascript" type="text/javascript">
			<!--
			var selectedTag = '';
			function changeTag(tagName){
				selectedTag = tagName;
				defaultText = new Array();
				<?php
								$k = 0;
								foreach($others as $tagname => $tag){
									echo "document.getElementById('tr_$tagname').className = 'row$k';";
									echo "defaultText['$tagname'] = '".$tag['default']."';";
								}
								$k = 1-$k;
				?>
				document.getElementById('tr_' + tagName).className = 'selectedrow';
				document.adminForm.tagtext.value = defaultText[tagName];
				setOnlineTag();
			}

			function setOnlineTag(){
				if(!selectedTag) changeTag('readonline');
				otherinfo = '';
				for(var i = 0; i < document.adminForm.template.length; i++){
					if(document.adminForm.template[i].checked){
						otherinfo += '|template:' + document.adminForm.template[i].value;
					}
				}
				setTag('<a href=' + '"{' + selectedTag + otherinfo + '}{/' + selectedTag + '}" target="_blank" style="text-decoration:none;"><span class="acymailing_online">' + document.adminForm.tagtext.value + '</span></a>');
			}
			//-->
		</script>
		<?php
		echo JText::_('FIELD_TEXT').' : <input type="text" name="tagtext" size="100px" onchange="setOnlineTag();" /><br /><br />';
		$radios = array();
		$radios[] = JHTML::_('select.option', "standard", JText::_('IN_TEMPLATE'));
		$radios[] = JHTML::_('select.option', "notemplate", JText::_('WITHOUT_TEMPLATE'));
		echo JHTML::_('acyselect.radiolist', $radios, 'template', 'size="1" onclick="setOnlineTag();"', 'value', 'text', 'notemplate');
		echo '<div class="onelineblockoptions">
				<table class="acymailing_table" cellpadding="1">';
		$k = 0;
		foreach($others as $tagname => $tag){
			echo '<tr style="cursor:pointer" class="row'.$k.'" onclick="changeTag(\''.$tagname.'\');" id="tr_'.$tagname.'" ><td class="acytdcheckbox" ></td><td>'.$tag['desc'].'</td></tr>';
			$k = 1 - $k;
		}
		echo '</table></div>';
	}

	function acymailing_replaceusertags(&$email, &$user, $send = true){
		$match = '#(?:{|%7B)(readonline|forward)([^}]*)(?:}|%7D)(.*)(?:{|%7B)/(readonline|forward)(?:}|%7D)#Uis';
		$variables = array('body', 'altbody');
		$found = false;
		foreach($variables as $var){
			if(empty($email->$var)) continue;
			$found = preg_match_all($match, $email->$var, $results[$var]) || $found;
			if(empty($results[$var][0])) unset($results[$var]);
		}

		if(!$found) return;

		$config = acymailing_config();

		$tags = array();

		foreach($results as $var => $allresults){
			foreach($allresults[0] as $i => $oneTag){
				if(isset($tags[$oneTag])) continue;
				$arguments = explode('|', strip_tags(str_replace('%7C', '|', $allresults[2][$i])));
				$tag = new stdClass();
				$tag->type = $allresults[1][$i];
				$tag->template = ($tag->type == 'readonline') ? $this->params->get('viewtemplate', 'notemplate') : $this->params->get('forwardtemplate', 'notemplate');
				$tag->itemid = $config->get('itemid', 0);
				for($j = 0, $a = count($arguments); $j < $a; $j++){
					$args = explode(':', $arguments[$j]);
					$arg0 = trim($args[0]);
					if(empty($arg0)) continue;
					if(isset($args[1])){
						$tag->$arg0 = $args[1];
					}else{
						$tag->$arg0 = true;
					}
				}

				$addkey = (!empty($email->key) && $this->params->get('addkey', 'yes') == 'yes') ? '&key='.$email->key : '';
				$adduserkey = (!empty($user->key) && $this->params->get('adduserkey', 'yes') == 'yes') ? '&subid='.$user->subid.'-'.$user->key : '';
				$tmpl = ($tag->template == 'notemplate') ? '&tmpl=component' : '';
				$item = empty($tag->itemid) ? '' : '&Itemid='.$tag->itemid;
				$lang = empty($email->language) ? '' : '&lang='.$email->language;

				if($tag->type == 'readonline'){
					$link = acymailing_frontendLink('index.php?option=com_acymailing&ctrl=archive&task=view&mailid='.$email->mailid.$addkey.$adduserkey.$tmpl.$item.$lang);
				}elseif($tag->type == 'forward'){
					$link = acymailing_frontendLink('index.php?option=com_acymailing&ctrl=archive&task=forward&mailid='.$email->mailid.$addkey.$adduserkey.$tmpl.$item.$lang);
				}

				if(empty($allresults[3][$i])){
					$tags[$oneTag] = $link;
				}else $tags[$oneTag] = '<a style="text-decoration:none;" href="'.$link.'"><span class="acymailing_online">'.$allresults[3][$i].'</span></a>';
			}
		}

		$email->body = str_replace(array_keys($tags), $tags, $email->body);
		if(!empty($email->altbody)) $email->altbody = str_replace(array_keys($tags), $tags, $email->altbody);
	}
}//endclass
