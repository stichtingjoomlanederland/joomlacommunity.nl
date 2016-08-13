<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php defined('_JEXEC') or die('Restricted access'); ?>
<?php

class plgAcymailingTagcontent extends JPlugin{
	public function __construct(&$subject, $config){
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('acymailing', 'tagcontent');
			$this->params = new acyParameter($plugin->params);
		}
		$this->db = JFactory::getDBO();
		$this->acypluginsHelper = acymailing_get('helper.acyplugins');
	}

	public function acymailing_getPluginType(){
		$app = JFactory::getApplication();
		if($this->params->get('frontendaccess') == 'none' && !$app->isAdmin()) return;

		$onePlugin = new stdClass();
		$onePlugin->name = JText::_('JOOMLA_CONTENT');
		$onePlugin->function = 'acymailingtagcontent_show';
		$onePlugin->help = 'plugin-tagcontent';

		return $onePlugin;
	}

	public function acymailingtagcontent_show(){
		$app = JFactory::getApplication();

		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();

		$my = JFactory::getUser();
		$lang = JFactory::getLanguage();
		$lang->load('com_content', JPATH_SITE);

		$paramBase = ACYMAILING_COMPONENT.'.tagcontent';
		$pageInfo->filter->order->value = $app->getUserStateFromRequest($paramBase.".filter_order", 'filter_order', 'a.id', 'cmd');
		$pageInfo->filter->order->dir = $app->getUserStateFromRequest($paramBase.".filter_order_Dir", 'filter_order_Dir', 'desc', 'word');
		if(strtolower($pageInfo->filter->order->dir) !== 'desc') $pageInfo->filter->order->dir = 'asc';
		$pageInfo->search = $app->getUserStateFromRequest($paramBase.".search", 'search', '', 'string');
		$pageInfo->search = JString::strtolower(trim($pageInfo->search));
		$pageInfo->filter_cat = $app->getUserStateFromRequest($paramBase.".filter_cat", 'filter_cat', '', 'int');
		$pageInfo->contenttype = $app->getUserStateFromRequest($paramBase.".contenttype", 'contenttype', $this->params->get('default_type', 'intro'), 'string');
		$pageInfo->author = $app->getUserStateFromRequest($paramBase.".author", 'author', $this->params->get('default_author', '0'), 'string');
		$pageInfo->titlelink = $app->getUserStateFromRequest($paramBase.".titlelink", 'titlelink', $this->params->get('default_titlelink', 'link'), 'string');
		$pageInfo->lang = $app->getUserStateFromRequest($paramBase.".lang", 'lang', '', 'string');
		$pageInfo->pict = $app->getUserStateFromRequest($paramBase.".pict", 'pict', $this->params->get('default_pict', 1), 'string');
		$pageInfo->pictheight = $app->getUserStateFromRequest($paramBase.".pictheight", 'pictheight', $this->params->get('maxheight', 150), 'string');
		$pageInfo->pictwidth = $app->getUserStateFromRequest($paramBase.".pictwidth", 'pictwidth', $this->params->get('maxwidth', 150), 'string');


		$pageInfo->limit->value = $app->getUserStateFromRequest($paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int');
		$pageInfo->limit->start = $app->getUserStateFromRequest($paramBase.'.limitstart', 'limitstart', 0, 'int');

		$picts = array();
		$picts[] = JHTML::_('select.option', "1", JText::_('JOOMEXT_YES'));
		$pictureHelper = acymailing_get('helper.acypict');
		if($pictureHelper->available()) $picts[] = JHTML::_('select.option', "resized", JText::_('RESIZED'));
		$picts[] = JHTML::_('select.option', "0", JText::_('JOOMEXT_NO'));

		$contenttype = array();
		$contenttype[] = JHTML::_('select.option', "title", JText::_('TITLE_ONLY'));
		$contenttype[] = JHTML::_('select.option', "intro", JText::_('INTRO_ONLY'));
		$contenttype[] = JHTML::_('select.option', "text", JText::_('FIELD_TEXT'));
		$contenttype[] = JHTML::_('select.option', "full", JText::_('FULL_TEXT'));

		$titlelink = array();
		$titlelink[] = JHTML::_('select.option', "link", JText::_('JOOMEXT_YES'));
		$titlelink[] = JHTML::_('select.option', "0", JText::_('JOOMEXT_NO'));

		$authorname = array();
		$authorname[] = JHTML::_('select.option', "author", JText::_('JOOMEXT_YES'));
		$authorname[] = JHTML::_('select.option', "0", JText::_('JOOMEXT_NO'));

		$searchFields = array('a.id', 'a.title', 'a.alias', 'a.created_by', 'b.name', 'b.username');
		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search, true).'%\'';
			$filters[] = implode(" LIKE $searchVal OR ", $searchFields)." LIKE $searchVal";
		}

		if(!empty($pageInfo->filter_cat)){
			$filters[] = "a.catid = ".$pageInfo->filter_cat;
		}

		if($this->params->get('displayart', 'all') == 'onlypub'){
			$filters[] = "a.state = 1";
		}else{
			$filters[] = "a.state != -2";
		}

		if(!$app->isAdmin()){
			if(!ACYMAILING_J16){
				$filters[] = 'a.`access` <= '.(int)$my->get('aid');
			}else{
				$groups = implode(',', $my->getAuthorisedViewLevels());
				$filters[] = 'a.`access` IN ('.$groups.')';
			}
		}

		if($this->params->get('frontendaccess') == 'author' && !$app->isAdmin()){
			$filters[] = "a.created_by = ".intval($my->id);
		}

		$whereQuery = '';
		if(!empty($filters)){
			$whereQuery = ' WHERE ('.implode(') AND (', $filters).')';
		}

		$query = 'SELECT SQL_CALC_FOUND_ROWS a.*,b.name,b.username,a.created_by FROM '.acymailing_table('content', false).' as a';
		$query .= ' LEFT JOIN `#__users` AS b ON b.id = a.created_by';
		if(!empty($whereQuery)) $query .= $whereQuery;
		if(!empty($pageInfo->filter->order->value)){
			$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}

		$this->db->setQuery($query, $pageInfo->limit->start, $pageInfo->limit->value);
		$rows = $this->db->loadObjectList();

		if(!empty($pageInfo->search)){
			$rows = acymailing_search($pageInfo->search, $rows);
		}

		$this->db->setQuery('SELECT FOUND_ROWS()');
		$pageInfo->elements->total = $this->db->loadResult();
		$pageInfo->elements->page = count($rows);

		if(!ACYMAILING_J16){
			$query = 'SELECT a.id, a.id as catid, a.title as category, b.title as section, b.id as secid from #__categories as a ';
			$query .= 'INNER JOIN #__sections as b on a.section = b.id ORDER BY b.ordering,a.ordering';

			$this->db->setQuery($query);
			$categories = $this->db->loadObjectList('id');
			$categoriesValues = array();
			$categoriesValues[] = JHTML::_('select.option', '', JText::_('ACY_ALL'));
			$currentSec = '';
			foreach($categories as $catid => $oneCategorie){
				if($currentSec != $oneCategorie->section){
					if(!empty($currentSec)) $this->values[] = JHTML::_('select.option', '</OPTGROUP>');
					$categoriesValues[] = JHTML::_('select.option', '<OPTGROUP>', $oneCategorie->section);
					$currentSec = $oneCategorie->section;
				}
				$categoriesValues[] = JHTML::_('select.option', $catid, $oneCategorie->category);
			}
		}else{
			$query = "SELECT * from #__categories WHERE `extension` = 'com_content' ORDER BY lft ASC";

			$this->db->setQuery($query);
			$categories = $this->db->loadObjectList('id');
			$categoriesValues = array();
			$categoriesValues[] = JHTML::_('select.option', '', JText::_('ACY_ALL'));
			foreach($categories as $catid => $oneCategorie){
				$categories[$catid]->title = str_repeat('- - ', $categories[$catid]->level).$categories[$catid]->title;
				$categoriesValues[] = JHTML::_('select.option', $catid, $categories[$catid]->title);
			}
		}

		jimport('joomla.html.pagination');
		$pagination = new JPagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);

		$tabs = acymailing_get('helper.acytabs');
		echo $tabs->startPane('joomlacontent_tab');
		echo $tabs->startPanel(JText::_('JOOMLA_CONTENT'), 'joomlacontent_content');

		?>
		<script language="javascript" type="text/javascript">
			<!--
			var selectedContents = new Array();
			function applyContent(contentid, rowClass){
				var tmp = selectedContents.indexOf(contentid)
				if(tmp != -1){
					window.document.getElementById('content' + contentid).className = rowClass;
					delete selectedContents[tmp];
				}else{
					window.document.getElementById('content' + contentid).className = 'selectedrow';
					selectedContents.push(contentid);
				}
				updateTag();
			}

			function updateTag(){
				var tag = '';
				var otherinfo = '';
				for(var i = 0; i < document.adminForm.contenttype.length; i++){
					if(document.adminForm.contenttype[i].checked){
						selectedtype = document.adminForm.contenttype[i].value;
						otherinfo += '| type:' + document.adminForm.contenttype[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.titlelink.length; i++){
					if(document.adminForm.titlelink[i].checked && document.adminForm.titlelink[i].value.length > 1){
						otherinfo += '| ' + document.adminForm.titlelink[i].value;
					}
				}
				if(selectedtype != 'title'){
					for(var i = 0; i < document.adminForm.author.length; i++){
						if(document.adminForm.author[i].checked && document.adminForm.author[i].value.length > 1){
							otherinfo += '| ' + document.adminForm.author[i].value;
						}
					}
					for(var i = 0; i < document.adminForm.pict.length; i++){
						if(document.adminForm.pict[i].checked){
							otherinfo += '| pict:' + document.adminForm.pict[i].value;
							if(document.adminForm.pict[i].value == 'resized'){
								document.getElementById('pictsize').style.display = '';
								if(document.adminForm.pictwidth.value) otherinfo += '| maxwidth:' + document.adminForm.pictwidth.value;
								if(document.adminForm.pictheight.value) otherinfo += '| maxheight:' + document.adminForm.pictheight.value;
							}else{
								document.getElementById('pictsize').style.display = 'none';
							}
						}
					}
					document.getElementById('format').style.display = '';
				}else{
					document.getElementById('format').style.display = 'none';
				}

				if(document.adminForm.contentformat && document.adminForm.contentformat.value){
					otherinfo += '| format:' + document.adminForm.contentformat.value;
				}

				if(window.document.getElementById('jflang') && window.document.getElementById('jflang').value != ''){
					otherinfo += '|lang:';
					otherinfo += window.document.getElementById('jflang').value;
				}

				for(var i in selectedContents){
					if(selectedContents[i] && !isNaN(i)){
						tag = tag + '{joomlacontent:' + selectedContents[i] + otherinfo + '}<br />';
					}
				}
				setTag(tag);
			}
			//-->
		</script>
		<div class="onelineblockoptions">
			<table width="100%" class="acymailing_table">
				<tr>
					<td>
						<?php echo JText::_('DISPLAY'); ?>
					</td>
					<td colspan="2">
						<?php echo JHTML::_('acyselect.radiolist', $contenttype, 'contenttype', 'size="1" onclick="updateTag();"', 'value', 'text', $pageInfo->contenttype); ?>
					</td>
					<td>
						<?php $jflanguages = acymailing_get('type.jflanguages');
						$jflanguages->onclick = 'onchange="updateTag();"';
						echo $jflanguages->display('lang', $pageInfo->lang); ?>
					</td>
				</tr>
				<tr id="format" class="acyplugformat">
					<td valign="top">
						<?php echo JText::_('FORMAT'); ?>
					</td>
					<td valign="top">
						<?php echo $this->acypluginsHelper->getFormatOption('tagcontent'); ?>
					</td>
					<td valign="top"><?php echo JText::_('DISPLAY_PICTURES'); ?></td>
					<td valign="top"><?php echo JHTML::_('acyselect.radiolist', $picts, 'pict', 'size="1" onclick="updateTag();"', 'value', 'text', $pageInfo->pict); ?>
						<span id="pictsize" <?php if($pageInfo->pict != 'resized') echo 'style="display:none;"'; ?>><br/><?php echo JText::_('CAPTCHA_WIDTH') ?>
							<input name="pictwidth" type="text" onchange="updateTag();" value="<?php echo $pageInfo->pictwidth; ?>" style="width:30px;"/>
							x <?php echo JText::_('CAPTCHA_HEIGHT') ?>
							<input name="pictheight" type="text" onchange="updateTag();" value="<?php echo $pageInfo->pictheight; ?>" style="width:30px;"/>
						</span>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo JText::_('CLICKABLE_TITLE'); ?>
					</td>
					<td>
						<?php echo JHTML::_('acyselect.radiolist', $titlelink, 'titlelink', 'size="1" onclick="updateTag();"', 'value', 'text', $pageInfo->titlelink); ?>
					</td>
					<td>
						<?php echo JText::_('AUTHOR_NAME'); ?>
					</td>
					<td>
						<?php echo JHTML::_('acyselect.radiolist', $authorname, 'author', 'size="1" onclick="updateTag();"', 'value', 'text', (string)$pageInfo->author); ?>
					</td>
				</tr>
			</table>
		</div>
		<div class="onelineblockoptions">
			<table class="acymailing_table_options">
				<tr>
					<td width="100%">
						<?php acymailing_listingsearch($pageInfo->search); ?>
					</td>
					<td nowrap="nowrap">
						<?php echo JHTML::_('select.genericlist', $categoriesValues, 'filter_cat', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', (int)$pageInfo->filter_cat); ?>
					</td>
				</tr>
			</table>

			<table class="acymailing_table" cellpadding="1" width="100%">
				<thead>
				<tr>
					<th class="title">
					</th>
					<th class="title">
						<?php echo JHTML::_('grid.sort', JText::_('FIELD_TITLE'), 'a.title', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
					</th>
					<th class="title">
						<?php echo JHTML::_('grid.sort', JText::_('ACY_AUTHOR'), 'b.name', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
					</th>
					<th class="title">
						<?php echo JHTML::_('grid.sort', JText::_(ACYMAILING_J16 ? 'COM_CONTENT_PUBLISHED_DATE' : 'START PUBLISHING'), 'a.publish_up', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
					</th>
					<th class="title">
						<?php echo JHTML::_('grid.sort', JText::_('ACY_CREATED'), 'a.created', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
					</th>
					<th class="title titleid">
						<?php echo JHTML::_('grid.sort', JText::_('ACY_ID'), 'a.id', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
					</th>
				</tr>
				</thead>
				<tfoot>
				<tr>
					<td colspan="6">
						<?php echo $pagination->getListFooter(); ?>
						<?php echo $pagination->getResultsCounter(); ?>
					</td>
				</tr>
				</tfoot>
				<tbody>
				<?php
				$k = 0;
				for($i = 0, $a = count($rows); $i < $a; $i++){
					$row =& $rows[$i];
					?>
					<tr id="content<?php echo $row->id ?>" class="<?php echo "row$k"; ?>" onclick="applyContent(<?php echo $row->id.",'row$k'" ?>);" style="cursor:pointer;">
						<td class="acytdcheckbox"></td>
						<td>
							<?php
							$text = '<b>'.JText::_('JOOMEXT_ALIAS').': </b>'.$row->alias;
							echo acymailing_tooltip($text, $row->title, '', $row->title);
							?>
						</td>
						<td>
							<?php
							if(!empty($row->name)){
								$text = '<b>'.JText::_('JOOMEXT_NAME').' : </b>'.$row->name;
								$text .= '<br /><b>'.JText::_('ACY_USERNAME').' : </b>'.$row->username;
								$text .= '<br /><b>'.JText::_('ACY_ID').' : </b>'.$row->created_by;
								echo acymailing_tooltip($text, $row->name, '', $row->name);
							}
							?>
						</td>
						<td align="center">
							<?php echo JHTML::_('date', strip_tags($row->publish_up), JText::_('DATE_FORMAT_LC4')); ?>
						</td>
						<td align="center">
							<?php echo JHTML::_('date', strip_tags($row->created), JText::_('DATE_FORMAT_LC4')); ?>
						</td>
						<td align="center">
							<?php echo $row->id; ?>
						</td>
					</tr>
					<?php
					$k = 1 - $k;
				}
				?>
				</tbody>
			</table>
		</div>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="filter_order" value="<?php echo $pageInfo->filter->order->value; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $pageInfo->filter->order->dir; ?>"/>
		<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_('TAG_CATEGORIES'), 'joomlacontent_auto');

		$type = JRequest::getString('type');

		?>
		<script language="javascript" type="text/javascript">
			<!--
			window.onload = function(){
				if(window.document.getElementById('tagsauto')){
					window.document.getElementById('tagsauto').onchange = updateAutoTag;
				}
			}
			var selectedCategories = new Array();
			<?php if(!ACYMAILING_J16){ ?>
			function applyAutoContent(secid, catid, rowClass){
				if(selectedCategories[secid] && selectedCategories[secid][catid]){
					window.document.getElementById('content_sec' + secid + '_cat' + catid).className = rowClass;
					delete selectedCategories[secid][catid];
				}else{
					if(!selectedCategories[secid]) selectedCategories[secid] = new Array();
					if(secid == 0){
						for(var isec in selectedCategories){
							for(var icat in selectedCategories[isec]){
								if(selectedCategories[isec][icat] == 'content'){
									window.document.getElementById('content_sec' + isec + '_cat' + icat).className = 'row0';
									delete selectedCategories[isec][icat];
								}
							}
						}
					}else{
						if(selectedCategories[0] && selectedCategories[0][0]){
							window.document.getElementById('content_sec0_cat0').className = 'row0';
							delete selectedCategories[0][0];
						}

						if(catid == 0){
							for(var icat in selectedCategories[secid]){
								if(selectedCategories[secid][icat] == 'content'){
									window.document.getElementById('content_sec' + secid + '_cat' + icat).className = 'row0';
									delete selectedCategories[secid][icat];
								}
							}
						}else{
							if(selectedCategories[secid][0]){
								window.document.getElementById('content_sec' + secid + '_cat0').className = 'row0';
								delete selectedCategories[secid][0];
							}
						}
					}

					window.document.getElementById('content_sec' + secid + '_cat' + catid).className = 'selectedrow';
					selectedCategories[secid][catid] = 'content';
				}

				updateAutoTag();
			}
			<?php }else{ ?>
			function applyAutoContent(catid, rowClass){
				if(selectedCategories[catid]){
					window.document.getElementById('content_cat' + catid).className = rowClass;
					delete selectedCategories[catid];
				}else{
					window.document.getElementById('content_cat' + catid).className = 'selectedrow';
					selectedCategories[catid] = 'content';
				}

				updateAutoTag();
			}
			<?php } ?>

			function updateAutoTag(){
				tag = '{autocontent:';
				<?php if(!ACYMAILING_J16){ ?>
				for(var isec in selectedCategories){
					for(var icat in selectedCategories[isec]){
						if(selectedCategories[isec][icat] == 'content'){
							if(icat != 0){
								tag += 'cat' + icat + '-';
							}else{
								tag += 'sec' + isec + '-';
							}
						}
					}
				}
				<?php }else{ ?>
				for(var icat in selectedCategories){
					if(selectedCategories[icat] == 'content'){
						tag += icat + '-';
					}
				}
				<?php } ?>

				if(document.adminForm.min_article && document.adminForm.min_article.value && document.adminForm.min_article.value != 0){
					tag += '| min:' + document.adminForm.min_article.value;
				}
				if(document.adminForm.max_article.value && document.adminForm.max_article.value != 0){
					tag += '| max:' + document.adminForm.max_article.value;
				}
				if(document.adminForm.contentorder.value){
					tag += "| order:" + document.adminForm.contentorder.value + "," + document.adminForm.contentorderdir.value;
				}
				if(document.adminForm.contentfilter && document.adminForm.contentfilter.value){
					tag += document.adminForm.contentfilter.value;
				}
				if(document.adminForm.meta_article && document.adminForm.meta_article.value){
					tag += '| meta:' + document.adminForm.meta_article.value;
				}

				for(var i = 0; i < document.adminForm.contenttypeauto.length; i++){
					if(document.adminForm.contenttypeauto[i].checked){
						selectedtype = document.adminForm.contenttypeauto[i].value;
						tag += '| type:' + document.adminForm.contenttypeauto[i].value;
					}
				}

				for(var i = 0; i < document.adminForm.titlelinkauto.length; i++){
					if(document.adminForm.titlelinkauto[i].checked && document.adminForm.titlelinkauto[i].value.length > 1){
						tag += '|' + document.adminForm.titlelinkauto[i].value;
					}
				}
				if(selectedtype != 'title'){
					for(var i = 0; i < document.adminForm.authorauto.length; i++){
						if(document.adminForm.authorauto[i].checked && document.adminForm.authorauto[i].value.length > 1){
							tag += '|' + document.adminForm.authorauto[i].value;
						}
					}
					for(var i = 0; i < document.adminForm.pictauto.length; i++){
						if(document.adminForm.pictauto[i].checked){
							tag += '| pict:' + document.adminForm.pictauto[i].value;
							if(document.adminForm.pictauto[i].value == 'resized'){
								document.getElementById('pictsizeauto').style.display = '';
								if(document.adminForm.pictwidthauto.value) tag += '| maxwidth:' + document.adminForm.pictwidthauto.value;
								if(document.adminForm.pictheightauto.value) tag += '| maxheight:' + document.adminForm.pictheightauto.value;
							}else{
								document.getElementById('pictsizeauto').style.display = 'none';
							}
						}
					}
					document.getElementById('formatauto').style.display = '';
				}else{
					document.getElementById('formatauto').style.display = 'none';
				}

				if(document.getElementById('contentformatautoinvert').value == 1) tag += '| invert';
				if(document.adminForm.contentformatauto && document.adminForm.contentformatauto.value){
					tag += '| format:' + document.adminForm.contentformatauto.value;
				}

				if(document.adminForm.cols && document.adminForm.cols.value > 1){
					tag += '| cols:' + document.adminForm.cols.value;
				}
				if(window.document.getElementById('jflangauto') && window.document.getElementById('jflangauto').value != ''){
					tag += '| lang:' + window.document.getElementById('jflangauto').value;
				}
				if(window.document.getElementById('jlang') && window.document.getElementById('jlang').value != ''){
					tag += '| language:' + window.document.getElementById('jlang').value;
				}

				if(window.document.getElementById('tagsauto')){
					var tmp = 0;
					for(var i = 0; i < window.document.getElementById('tagsauto').length; i++){
						if(window.document.getElementById('tagsauto')[i].selected){
							if(tmp == 0){
								tag += '| tags:' + window.document.getElementById('tagsauto')[i].value;
								tmp = 1;
							}else{
								tag += ',' + window.document.getElementById('tagsauto')[i].value;
							}
						}
					}
				}

				tag += '}';

				setTag(tag);
			}
			//-->
		</script>
		<div class="onelineblockoptions">
			<table width="100%" class="acymailing_table">
				<tr>
					<td>
						<?php echo JText::_('DISPLAY'); ?>
					</td>
					<td colspan="2">
						<?php echo JHTML::_('acyselect.radiolist', $contenttype, 'contenttypeauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', $this->params->get('default_type', 'intro')); ?>
					</td>
					<td id="languagesauto">
						<?php $jflanguages = acymailing_get('type.jflanguages');
						$jflanguages->onclick = 'onchange="updateAutoTag();"';
						$jflanguages->id = 'jflangauto';
						echo $jflanguages->display('langauto');
						if(empty($jflanguages->found)){
							echo $jflanguages->displayJLanguages('jlangauto');
						}
						?>
					</td>
				</tr>
				<tr id="formatauto" class="acyplugformat">
					<td valign="top">
						<?php echo JText::_('FORMAT'); ?>
					</td>
					<td valign="top">
						<?php echo $this->acypluginsHelper->getFormatOption('tagcontent', 'TOP_LEFT', false, 'updateAutoTag'); ?>
					</td>
					<td valign="top"><?php echo JText::_('DISPLAY_PICTURES'); ?></td>
					<td valign="top"><?php echo JHTML::_('acyselect.radiolist', $picts, 'pictauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', $this->params->get('default_pict', '1')); ?>
						<span id="pictsizeauto" <?php if($this->params->get('default_pict', '1') != 'resized') echo 'style="display:none;"'; ?> ><br/><?php echo JText::_('CAPTCHA_WIDTH') ?>
							<input name="pictwidthauto" type="text" onchange="updateAutoTag();" value="<?php echo $this->params->get('maxwidth', '150'); ?>" style="width:30px;"/>
							x <?php echo JText::_('CAPTCHA_HEIGHT') ?>
							<input name="pictheightauto" type="text" onchange="updateAutoTag();" value="<?php echo $this->params->get('maxheight', '150'); ?>" style="width:30px;"/>
						</span>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo JText::_('CLICKABLE_TITLE'); ?>
					</td>
					<td>
						<?php echo JHTML::_('acyselect.radiolist', $titlelink, 'titlelinkauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', $this->params->get('default_titlelink', 'link')); ?>
					</td>
					<td>
						<?php echo JText::_('AUTHOR_NAME'); ?>
					</td>
					<td>
						<?php echo JHTML::_('acyselect.radiolist', $authorname, 'authorauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', (string)$this->params->get('default_author', '0')); ?>
					</td>
				</tr>
				<tr>
					<?php if(version_compare(JVERSION, '3.1.0', '>=')){ ?>
						<td valign="top">
							<?php echo JText::_('TAGS'); ?>
						</td>
						<td>
							<?php
							$form = JForm::getInstance('acytagcontenttags', JPATH_SITE.DS.'components'.DS.'com_acymailing'.DS.'params'.DS.'tagcontenttags.xml');
							foreach($form->getFieldset('tagcontenttagfield') as $field){
								echo $field->input;
							}
							?>
						</td>
					<?php }else{ ?>
						<td colspan="2"></td>
					<?php } ?>
					<td valign="top"><?php echo JText::_('FIELD_COLUMNS'); ?></td>
					<td valign="top">
						<select name="cols" style="width:150px" onchange="updateAutoTag();" size="1">
							<?php for($o = 1; $o < 11; $o++) echo '<option value="'.$o.'">'.$o.'</option>'; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo JText::_('MAX_ARTICLE'); ?>
					</td>
					<td>
						<input type="text" name="max_article" style="width:50px" value="20" onchange="updateAutoTag();"/>
					</td>
					<td>
						<?php echo JText::_('ACY_ORDER'); ?>
					</td>
					<td>
						<?php
						$values = array('id' => 'ACY_ID', 'ordering' => 'ACY_ORDERING', 'created' => 'CREATED_DATE', 'modified' => 'MODIFIED_DATE', 'title' => 'FIELD_TITLE');
						if(ACYMAILING_J16) $values['publish_up'] = 'COM_CONTENT_PUBLISHED_DATE';
						echo $this->acypluginsHelper->getOrderingField($values, 'id', 'DESC', 'updateAutoTag');
						?>
					</td>
				</tr>
				<?php if($this->params->get('metaselect')){ ?>
					<tr>
						<td>
							<?php echo JText::_('META_KEYWORDS'); ?>
						</td>
						<td colspan="3">
							<input type="text" name="meta_article" style="width:200px" value="" onchange="updateAutoTag();"/>
						</td>
					</tr>
				<?php } ?>
				<?php if($type == 'autonews'){ ?>
					<tr>
						<td>
							<?php echo JText::_('MIN_ARTICLE'); ?>
						</td>
						<td>
							<input type="text" name="min_article" style="width:50px" value="1" onchange="updateAutoTag();"/>
						</td>
						<td>
							<?php echo JText::_('JOOMEXT_FILTER'); ?>
						</td>
						<td>
							<?php $filter = acymailing_get('type.contentfilter');
							$filter->onclick = "updateAutoTag();";
							echo $filter->display('contentfilter', '|filter:created'); ?>
						</td>
					</tr>
				<?php } ?>
			</table>
		</div>

		<div class="onelineblockoptions">
			<table class="acymailing_table" cellpadding="1" width="100%">
				<thead>
				<tr>
					<th class="title"></th>
					<?php if(!ACYMAILING_J16){ ?>
						<th class="title">
							<?php echo JText::_('SECTION'); ?>
						</th>
					<?php } ?>
					<th class="title">
						<?php echo JText::_('TAG_CATEGORIES'); ?>
					</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$k = 0;
				if(!ACYMAILING_J16){
					?>
					<tr id="content_sec0_cat0" class="<?php echo "row$k"; ?>" onclick="applyAutoContent(0,0,'<?php echo "row$k" ?>');" style="cursor:pointer;">
						<td class="acytdcheckbox"></td>
						<td style="font-weight: bold;">
							<?php
							echo JText::_('ACY_ALL');
							?>
						</td>
						<td style="text-align:center;font-weight: bold;">
							<?php
							echo JText::_('ACY_ALL');
							?>
						</td>
					</tr>

					<?php
				}

				$k = 1 - $k;
				$currentSection = '';
				foreach($categories as $row){

					if(!ACYMAILING_J16 && $currentSection != $row->section){
						?>
						<tr id="content_sec<?php echo $row->secid ?>_cat0" class="<?php echo "row$k"; ?>" onclick="applyAutoContent(<?php echo $row->secid ?>,0,'<?php echo "row$k" ?>');" style="cursor:pointer;">
							<td class="acytdcheckbox"></td>
							<td style="font-weight: bold;">
								<?php
								echo $row->section;
								?>
							</td>
							<td style="text-align:center;font-weight: bold;">
								<?php
								echo JText::_('ACY_ALL');
								?>
							</td>
						</tr>
						<?php
						$k = 1 - $k;
						$currentSection = $row->section;
					}
					if(!ACYMAILING_J16){
						?>
						<tr id="content_sec<?php echo $row->secid ?>_cat<?php echo $row->catid ?>" class="<?php echo "row$k"; ?>" onclick="applyAutoContent(<?php echo $row->secid ?>,<?php echo $row->catid ?>,'<?php echo "row$k" ?>');" style="cursor:pointer;">
							<td class="acytdcheckbox"></td>
							<td>
							</td>
							<td>
								<?php
								echo $row->category;
								?>
							</td>
						</tr>
						<?php
					}else{ ?>
						<tr id="content_cat<?php echo $row->id ?>" class="<?php echo "row$k"; ?>" onclick="applyAutoContent(<?php echo $row->id ?>,'<?php echo "row$k" ?>');" style="cursor:pointer;">
							<td class="acytdcheckbox"></td>
							<td>
								<?php
								echo $row->title;
								?>
							</td>
						</tr>
					<?php }
					$k = 1 - $k;
				}
				?>
				</tbody>
			</table>
		</div>
		<?php

		echo $tabs->endPanel();
		echo $tabs->endPane();
	}

	public function acymailing_replacetags(&$email, $send = true){
		$this->_replaceAuto($email);
		$this->_replaceArticles($email);
	}

	private function _replaceArticles(&$email){
		$tags = $this->acypluginsHelper->extractTags($email, 'joomlacontent');
		if(empty($tags)) return;

		$this->newslanguage = new stdClass();
		if(!empty($email->language)){
			$this->db->setQuery('SELECT lang_id, lang_code FROM #__languages WHERE sef = '.$this->db->quote($email->language).' LIMIT 1');
			$this->newslanguage = $this->db->loadObject();
		}

		$this->currentcatid = -1;
		$this->readmore = empty($email->template->readmore) ? JText::_('JOOMEXT_READ_MORE') : '<img class="readmorepict" src="'.ACYMAILING_LIVE.$email->template->readmore.'" alt="'.JText::_('JOOMEXT_READ_MORE', true).'" />';

		require_once JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php';

		if($this->params->get('integration') == 'flexicontent' && file_exists(JPATH_SITE.DS.'components'.DS.'com_flexicontent'.DS.'helpers'.DS.'route.php')){
			require_once JPATH_SITE.DS.'components'.DS.'com_flexicontent'.DS.'helpers'.DS.'route.php';
		}

		$tagsReplaced = array();
		foreach($tags as $i => $oneTag){
			if(isset($tagsReplaced[$i])) continue;
			$tagsReplaced[$i] = $this->_replaceContent($oneTag);
		}

		$this->acypluginsHelper->replaceTags($email, $tagsReplaced, true);
	}

	private function _replaceContent(&$tag){
		$oldFormat = empty($tag->format);

		if(!ACYMAILING_J16){
			$query = 'SELECT a.*,b.name as authorname, c.alias as catalias, c.title as cattitle, c.image AS catpict, s.alias as secalias, s.title as sectitle FROM '.acymailing_table('content', false).' as a ';
			$query .= 'LEFT JOIN '.acymailing_table('users', false).' as b ON a.created_by = b.id ';
			$query .= ' LEFT JOIN '.acymailing_table('categories', false).' AS c ON c.id = a.catid ';
			$query .= ' LEFT JOIN '.acymailing_table('sections', false).' AS s ON s.id = a.sectionid ';
			$query .= 'WHERE a.id = '.$tag->id.' LIMIT 1';
		}else{
			$query = 'SELECT a.*,b.name as authorname, c.alias as catalias, c.title as cattitle, c.params AS catparams FROM '.acymailing_table('content', false).' as a ';
			$query .= 'LEFT JOIN '.acymailing_table('users', false).' as b ON a.created_by = b.id ';
			$query .= ' LEFT JOIN '.acymailing_table('categories', false).' AS c ON c.id = a.catid ';
			$query .= 'WHERE a.id = '.$tag->id.' LIMIT 1';
		}

		$this->db->setQuery($query);
		$article = $this->db->loadObject();

		if(empty($article)){
			$app = JFactory::getApplication();
			if($app->isAdmin()) $app->enqueueMessage('The article "'.$tag->id.'" could not be loaded', 'notice');
			return '';
		}

		if(empty($tag->lang) && !empty($this->newslanguage) && !empty($this->newslanguage->lang_code)) $tag->lang = $this->newslanguage->lang_code.','.$this->newslanguage->lang_id;

		$this->acypluginsHelper->translateItem($article, $tag, 'content');

		$varFields = array();
		foreach($article as $fieldName => $oneField){
			$varFields['{'.$fieldName.'}'] = $oneField;
		}

		$this->acypluginsHelper->cleanHtml($article->introtext);
		$this->acypluginsHelper->cleanHtml($article->fulltext);


		if($this->params->get('integration') == 'jreviews' && !empty($article->images)){
			$firstpict = explode('|', trim(reset(explode("\n", $article->images))).'|||||||');
			if(!empty($firstpict[0])){
				$picturePath = file_exists(ACYMAILING_ROOT.'images'.DS.'stories'.DS.str_replace('/', DS, $firstpict[0])) ? ACYMAILING_LIVE.'images/stories/'.$firstpict[0] : ACYMAILING_LIVE.'images/'.$firstpict[0];
				$myPict = '<img src="'.$picturePath.'" alt="" hspace="5" style="margin:5px" align="left" border="'.intval($firstpict[5]).'" />';
				$article->introtext = $myPict.$article->introtext;
			}
		}
		$completeId = $article->id;
		$completeCat = $article->catid;

		if(!empty($article->alias)) $completeId .= ':'.$article->alias;
		if(!empty($article->catalias)) $completeCat .= ':'.$article->catalias;

		if(empty($tag->itemid)){
			if(!ACYMAILING_J16){
				$completeSec = $article->sectionid;
				if(!empty($article->secalias)) $completeSec .= ':'.$article->secalias;
				if($this->params->get('integration') == 'flexicontent' && class_exists('FlexicontentHelperRoute')){
					$link = FlexicontentHelperRoute::getItemRoute($completeId, $completeCat, $completeSec);
				}else{
					$link = ContentHelperRoute::getArticleRoute($completeId, $completeCat, $completeSec);
				}
			}else{
				if($this->params->get('integration') == 'flexicontent' && class_exists('FlexicontentHelperRoute')){
					$link = FlexicontentHelperRoute::getItemRoute($completeId, $completeCat);
				}else{
					$link = ContentHelperRoute::getArticleRoute($completeId, $completeCat);
				}
			}
		}else{
			$link = 'index.php?option=com_content&view=article&id='.$completeId.'&catid='.$completeCat;
		}


		if($this->params->get('integration') == 'flexicontent' && !class_exists('FlexicontentHelperRoute')){
			$link = 'index.php?option=com_flexicontent&view=items&id='.$completeId;
		}elseif($this->params->get('integration') == 'jaggyblog'){
			$link = 'index.php?option=com_jaggyblog&task=viewpost&id='.$completeId;
		}

		if(!empty($tag->itemid)) $link .= '&Itemid='.$tag->itemid;

		if(!empty($tag->lang)) $link .= (strpos($link, '?') ? '&' : '?').'lang='.substr($tag->lang, 0, strpos($tag->lang, ACYMAILING_J16 ? '-' : ','));
		if(!empty($tag->autologin)) $link .= (strpos($link, '?') ? '&' : '?').'user={usertag:username|urlencode}&passw={usertag:password|urlencode}';

		if(empty($tag->lang) && !empty($article->language) && $article->language != '*'){
			if(!isset($this->langcodes[$article->language])){
				$this->db->setQuery('SELECT sef FROM #__languages WHERE lang_code = '.$this->db->Quote($article->language).' ORDER BY `published` DESC LIMIT 1');
				$this->langcodes[$article->language] = $this->db->loadResult();
				if(empty($this->langcodes[$article->language])) $this->langcodes[$article->language] = $article->language;
			}
			$link .= (strpos($link, '?') ? '&' : '?').'lang='.$this->langcodes[$article->language];
		}

		$link = acymailing_frontendLink($link);
		$varFields['{link}'] = $link;

		$afterTitle = '';
		$afterArticle = '';
		$contentText = '';
		$pictPath = '';

		if(!empty($tag->author)){
			$authorName = empty($article->created_by_alias) ? $article->authorname : $article->created_by_alias;
			if($tag->type == 'title') $afterTitle .= '<br />';
			$afterTitle .= '<span class="authorname">'.$authorName.'</span><br />';
		}

		$dateFormat = empty($tag->dateformat) ? JText::_('DATE_FORMAT_LC2') : $tag->dateformat;
		if(!empty($tag->created)){
			if($tag->type == 'title') $afterTitle .= '<br />';
			$varFields['{createddate}'] = JHTML::_('date', $article->created, $dateFormat);
			$afterTitle .= '<span class="createddate">'.$varFields['{createddate}'].'</span><br />';
		}

		if(!empty($tag->modified)){
			if($tag->type == 'title') $afterTitle .= '<br />';
			$varFields['{modifieddate}'] = JHTML::_('date', $article->modified, $dateFormat);
			$afterTitle .= '<span class="modifieddate">'.$varFields['{modifieddate}'].'</span><br />';
		}

		if(!isset($tag->pict) && $tag->type != 'title'){
			if($this->params->get('removepictures', 'never') == 'always' || ($this->params->get('removepictures', 'never') == 'intro' && $tag->type == "intro")){
				$tag->pict = 0;
			}else{
				$tag->pict = 1;
			}
		}

		if(strpos($article->introtext, 'jseblod') !== false && file_exists(ACYMAILING_ROOT.'plugins'.DS.'content'.DS.'cckjseblod.php')){
			global $mainframe;
			include_once(ACYMAILING_ROOT.'plugins'.DS.'content'.DS.'cckjseblod.php');
			if(function_exists('plgContentCCKjSeblod')){
				$paramsContent = JComponentHelper::getParams('com_content');
				$article->text = $article->introtext.$article->fulltext;
				plgContentCCKjSeblod($article, $paramsContent);
				$article->introtext = $article->text;
				$article->fulltext = '';
			}
		}

		if($tag->type != "title"){
			if($tag->type == "intro"){
				$forceReadMore = false;
				$mytag = new stdClass();
				$mytag->wrap = $this->params->get('wordwrap', 0);
				if(empty($article->fulltext)){
					$article->introtext = $this->acypluginsHelper->wrapText($article->introtext, $mytag);
					if(!empty($this->acypluginsHelper->wraped)) $forceReadMore = true;
				}
			}

			if(empty($article->fulltext) || $tag->type != "text"){
				$contentText .= $article->introtext;
			}

			if($tag->type != "intro" && !empty($article->fulltext)){
				if($tag->type != "text" && !empty($article->introtext) && !preg_match('#^<[div|p]#i', trim($article->fulltext))){
					$contentText .= '<br />';
				}
				$contentText .= $article->fulltext;
			}

			$contentText = $this->acypluginsHelper->wrapText($contentText, $tag);
			if(!empty($this->acypluginsHelper->wraped)) $forceReadMore = true;

			if(!empty($tag->clean)){
				$contentText = strip_tags($contentText, '<p><br><span><ul><li><h1><h2><h3><h4><a>');
			}

			if(ACYMAILING_J16 && !empty($article->images) && !empty($tag->pict)){
				$picthtml = '';
				$images = json_decode($article->images);
				$pictVar = ($tag->type == 'intro') ? 'image_intro' : 'image_fulltext';
				$floatVar = ($tag->type == 'intro') ? 'float_intro' : 'float_fulltext';
				if(!empty($images->$pictVar)){
					if($images->$floatVar != 'right'){
						if(empty($tag->format)) $tag->format = 'TOP_LEFT';
						$images->$floatVar = 'left';
					}elseif(empty($tag->format)) $tag->format = 'TOP_RIGHT';
					$style = 'float:'.$images->$floatVar.';padding-'.(($images->$floatVar == 'right') ? 'left' : 'right').':10px;padding-bottom:10px;';
					if(!empty($tag->link) && empty($tag->nopictlink)) $picthtml .= '<a href="'.$link.'" style="text-decoration:none" >';
					$alt = '';
					$altVar = $pictVar.'_alt';
					if(!empty($images->$altVar)) $alt = $images->$altVar;
					$picthtml .= '<img'.(empty($tag->nopictstyle) ? ' style="'.$style.'"' : '').' alt="'.$alt.'" border="0" src="'.JURI::root().$images->$pictVar.'" />';
					$pictPath = JURI::root().$images->$pictVar;
					if(!empty($tag->link) && empty($tag->nopictlink)) $picthtml .= '</a>';
					$varFields['{picthtml}'] = $picthtml;
				}
			}

			$contentText = preg_replace('/^\s*(<img[^>]*>)\s*(?:<br[^>]*>\s*)*/i', '$1', $contentText);

			if(file_exists(JPATH_SITE.DS.'plugins'.DS.'attachments') && empty($tag->noattach)){
				try{
					$query = 'SELECT display_name, url, filename '.'FROM #__attachments '.'WHERE (parent_entity = "article" '.'AND parent_id = '.intval($tag->id).')';
					if(ACYMAILING_J16){
						$query .= ' OR (parent_entity = "category" '.'AND parent_id = '.intval($article->catid).')';
					}
					$this->db->setQuery($query);
					$attachments = $this->db->loadObjectList();
				}catch(Exception $e){
					$attachments = array();
				}

				if(!empty($attachments)){
					$afterArticle .= '<br />'.JText::_('ATTACHED_FILES').' :';
					foreach($attachments as $oneAttachment){
						$afterArticle .= '<br /><a href="'.$oneAttachment->url.'">'.(empty($oneAttachment->display_name) ? $oneAttachment->filename : $oneAttachment->display_name).'</a>';
					}
				}
			}

			if(!empty($tag->share)){
				$links = array();
				$shareOpt = explode(',', $tag->share);
				foreach($shareOpt as $socialNetwork){
					$knownNetwork = true;
					$socialNetwork = strtolower(trim($socialNetwork));
					if($socialNetwork == 'facebook'){
						$linkShare = 'http://www.facebook.com/sharer.php?u='.urlencode($link).'&t='.urlencode($article->title);
						$picSrc = (file_exists(ACYMAILING_MEDIA.'plugins'.DS.'facebook.png') ? 'media/com_acymailing/plugins/facebook.png' : 'media/com_acymailing/images/facebookshare.png');
						$altText = 'Facebook';
					}elseif($socialNetwork == 'twitter'){
						$text = JText::sprintf('SHARE_TEXT', $link);
						$linkShare = 'http://twitter.com/home?status='.urlencode($text);
						$picSrc = (file_exists(ACYMAILING_MEDIA.'plugins'.DS.'twitter.png') ? 'media/com_acymailing/plugins/twitter.png' : 'media/com_acymailing/images/twittershare.png');
						$altText = 'Twitter';
					}elseif($socialNetwork == 'linkedin'){
						$linkShare = 'http://www.linkedin.com/shareArticle?mini=true&url='.urlencode($link).'&title='.urlencode($article->title);
						$picSrc = (file_exists(ACYMAILING_MEDIA.'plugins'.DS.'linkedin.png') ? 'media/com_acymailing/plugins/linkedin.png' : 'media/com_acymailing/images/linkedin.png');
						$altText = 'LinkedIn';
					}elseif($socialNetwork == 'hyves'){
						$linkShare = 'http://www.hyves-share.nl/button/respect/?hc_hint=1&url='.urlencode($link).'&title='.urlencode($article->title);
						$picSrc = (file_exists(ACYMAILING_MEDIA.'plugins'.DS.'hyves.png') ? 'media/com_acymailing/plugins/hyves.png' : 'media/com_acymailing/images/hyvesshare.png');
						$altText = 'Hyves';
					}elseif($socialNetwork == 'google'){
						$linkShare = 'https://plus.google.com/share?url='.urlencode($link);
						$picSrc = (file_exists(ACYMAILING_MEDIA.'plugins'.DS.'google.png') ? 'media/com_acymailing/plugins/google.png' : 'media/com_acymailing/images/google_plusshare.png');
						$altText = 'Google+';
					}elseif($socialNetwork == 'mailto'){
						$linkShare = 'mailto:?subject='.urlencode($article->title).'&body='.urlencode($article->title.' ('.$link.')');
						$picSrc = (file_exists(ACYMAILING_MEDIA.'plugins'.DS.'mailto.png') ? 'media/com_acymailing/plugins/mailto.png' : 'media/com_acymailing/images/mailto.png');
						$altText = 'MailTo';
					}else{
						$knownNetwork = false;
						acymailing_display('Network not found: '.$socialNetwork.'. Availables networks are facebook, twitter, linkedin, hyves, google and mailto.', 'warning');
					}
					if($knownNetwork){
						array_push($links, '<a target="_blank" href="'.$linkShare.'" title="'.JText::sprintf('SOCIAL_SHARE', $altText).'"><img alt="'.$altText.'" src="'.$picSrc.'" /></a>');
					}
				}
				$afterArticle .= '<br />'.(!empty($tag->sharetxt) ? $tag->sharetxt.' ' : '').implode(' ', $links);
			}
		}

		if(!empty($tag->jtags) && version_compare(JVERSION, '3.1.0', '>=')){
			$this->db->setQuery('SELECT t.id, t.alias, t.title FROM #__tags AS t JOIN #__contentitem_tag_map AS m ON t.id = m.tag_id WHERE t.published = 1 AND m.type_alias = "com_content.article" AND m.content_item_id = '.intval($tag->id));
			$tags = $this->db->loadObjectList();
			if(!empty($tags)){
				$afterArticle .= '<br />';
				foreach($tags as $oneTag){
					$afterArticle .= ' <a href="index.php?option=com_tags&view=tag&id='.$oneTag->id.'-'.$oneTag->alias.'">'.$oneTag->title.'</a> ';
				}
			}
		}

		$readMoreText = empty($tag->readmore) ? $this->readmore : $tag->readmore;
		$varFields['{readmore}'] = '<a class="acymailing_readmore_link" style="text-decoration:none;" target="_blank" href="'.$link.'"><span class="acymailing_readmore">'.$readMoreText.'</span></a>';

		if($tag->type == "intro" && empty($tag->noreadmore) && (!empty($article->fulltext) || $forceReadMore)){
			if(!empty($afterArticle)) $afterArticle .= '<br />';
			$afterArticle .= $varFields['{readmore}'];
		}

		$format = new stdClass();
		$format->tag = $tag;
		$format->title = empty($tag->notitle) ? $article->title : '';
		$format->afterTitle = $afterTitle;
		$format->afterArticle = $afterArticle;
		$format->imagePath = $pictPath;
		$format->description = $contentText;
		$format->link = empty($tag->link) ? '' : $link;
		$format->cols = 2;
		$result = $this->acypluginsHelper->getStandardDisplay($format);

		if(!empty($tag->theme)){
			if(preg_match('#<img[^>]*>#Uis', $article->introtext.$article->fulltext, $pregresult)){
				$cleanContent = strip_tags($result, '<p><br><span><ul><li><h1><h2><h3><h4><a>');
				$tdwidth = (empty($tag->maxwidth) ? $this->params->get('maxwidth', 150) : $tag->maxwidth) + 20;
				$result = '<table cellspacing="0" width="500" cellpadding="0" border="0" ><tr><td class="contentpicture" width="'.$tdwidth.'" valign="top" align="center"><a href="'.$link.'" target="_blank" style="border:0px;text-decoration:none">'.$pregresult[0].'</a></td><td class="contenttext">'.$cleanContent.'</td></tr></table>';
			}
		}

		if($tag->type != 'title') $result = '<div class="acymailing_content">'.$result.'</div>';

		if(!(empty($tag->cattitle) && empty($tag->catpict)) && ((!strpos($article->catid, ',') && $this->currentcatid != $article->catid) || (strpos($article->catid, ',') && !in_array($this->currentcatid, explode(',', $article->catid))))){
			if(strpos($article->catid, ',')){
				$catids = explode(',', $article->catid);
				$this->currentcatid = $catids[0];
			}else{
				$this->currentcatid = $article->catid;
			}

			if(ACYMAILING_J16){
				$params = json_decode($article->catparams);
				$article->catpict = $params->image;
			}

			$resultTitle = $article->cattitle;

			if(!empty($tag->catpict) && !empty($article->catpict)){
				$style = '';
				if(!empty($tag->catmaxwidth)) $style .= 'max-width:'.intval($tag->catmaxwidth).'px;';
				if(!empty($tag->catmaxheight)) $style .= 'max-height:'.intval($tag->catmaxheight).'px;';
				$resultTitle = '<img'.(empty($style) ? '' : ' style="'.$style.'"').' alt="" src="'.$article->catpict.'" />';
				if(!empty($tag->cattitlelink)) $resultTitle = '<a href="index.php?option=com_content&view=category&id='.$this->currentcatid.'">'.$resultTitle.'</a>';
			}else{
				if(!empty($tag->cattitlelink)) $resultTitle = '<a href="index.php?option=com_content&view=category&id='.$this->currentcatid.'">'.$resultTitle.'</a>';
				$resultTitle = '<h3 class="cattitle">'.$resultTitle.'</h3>';
			}

			$result = $resultTitle.$result;
		}

		if($oldFormat){
			if(file_exists(ACYMAILING_MEDIA.'plugins'.DS.'tagcontent_html.php')){
				ob_start();
				require(ACYMAILING_MEDIA.'plugins'.DS.'tagcontent_html.php');
				$result = ob_get_clean();
			}elseif(file_exists(ACYMAILING_MEDIA.'plugins'.DS.'tagcontent.php')){
				ob_start();
				require(ACYMAILING_MEDIA.'plugins'.DS.'tagcontent.php');
				$result = ob_get_clean();
			}
		}elseif(!empty($tag->template) && file_exists(ACYMAILING_MEDIA.'plugins'.DS.$tag->template)){
			ob_start();
			require(ACYMAILING_MEDIA.'plugins'.DS.$tag->template);
			$result = ob_get_clean();
		}
		$result = str_replace(array_keys($varFields), $varFields, $result);

		$result = $this->acypluginsHelper->removeJS($result);
		$result = $this->acypluginsHelper->replaceVideos($result);

		$tag->maxheight = empty($tag->maxheight) ? $this->params->get('maxheight', 150) : $tag->maxheight;
		$tag->maxwidth = empty($tag->maxwidth) ? $this->params->get('maxwidth', 150) : $tag->maxwidth;
		$result = $this->acypluginsHelper->managePicts($tag, $result);

		if(!empty($tag->maxchar) && strlen(strip_tags($result)) > $tag->maxchar){
			$result = strip_tags($result);
			for($i = $tag->maxchar; $i > 0; $i--){
				if($result[$i] == ' ') break;
			}
			if(!empty($i)) $result = substr($result, 0, $i).@$tag->textafter;
		}

		return $result;
	}

	private function _replaceAuto(&$email){
		$this->acymailing_generateautonews($email);
		if(empty($this->tags)) return;
		$this->acypluginsHelper->replaceTags($email, $this->tags, true);
	}

	public function acymailing_generateautonews(&$email){
		$time = time();

		$tags = $this->acypluginsHelper->extractTags($email, 'autocontent');
		$return = new stdClass();
		$return->status = true;
		$return->message = '';
		$this->tags = array();

		if(empty($tags)) return $return;

		foreach($tags as $oneTag => $parameter){
			if(isset($this->tags[$oneTag])) continue;
			$allcats = explode('-', $parameter->id);
			$selectedArea = array();
			foreach($allcats as $oneCat){
				if(!ACYMAILING_J16){
					$sectype = substr($oneCat, 0, 3);
					$num = substr($oneCat, 3);
					if(empty($num)) continue;
					if($sectype == 'cat'){
						$selectedArea[] = 'catid = '.(int)$num;
					}elseif($sectype == 'sec'){
						$selectedArea[] = 'sectionid = '.(int)$num;
					}
				}else{
					if(empty($oneCat)) continue;
					$selectedArea[] = intval($oneCat);
				}
			}

			$query = 'SELECT a.id FROM `#__content` as a ';
			$where = array();

			if(!empty($parameter->tags) && version_compare(JVERSION, '3.1.0', '>=')){
				$tagsArray = explode(',', $parameter->tags);
				JArrayHelper::toInteger($tagsArray);
				if(!empty($tagsArray)){
					foreach($tagsArray as $oneTagId){
						$query .= 'JOIN #__contentitem_tag_map AS tagsmap'.$oneTagId.' ON (a.id = tagsmap'.$oneTagId.'.content_item_id AND tagsmap'.$oneTagId.'.type_alias LIKE "com_content.article" AND tagsmap'.$oneTagId.'.tag_id = '.$oneTagId.') ';
					}
				}
			}

			if(!empty($parameter->featured)){
				if(ACYMAILING_J16){
					$where[] = 'a.featured = 1';
				}else{
					$query .= 'JOIN `#__content_frontpage` as b ON a.id = b.content_id ';
					$where[] = 'b.content_id IS NOT NULL';
				}
			}

			if(!empty($parameter->nofeatured)){
				if(ACYMAILING_J16){
					$where[] = 'a.featured = 0';
				}else{
					$query .= 'LEFT JOIN `#__content_frontpage` as b ON a.id = b.content_id ';
					$where[] = 'b.content_id IS NULL';
				}
			}

			if(ACYMAILING_J16 && !empty($parameter->subcats) && !empty($selectedArea)){
				$this->db->setQuery('SELECT lft,rgt FROM #__categories WHERE id IN ('.implode(',', $selectedArea).')');
				$catinfos = $this->db->loadObjectList();
				if(!empty($catinfos)){
					$whereCats = array();
					foreach($catinfos as $onecat){
						$whereCats[] = 'lft > '.$onecat->lft.' AND rgt < '.$onecat->rgt;
					}
					$this->db->setQuery('SELECT id FROM #__categories WHERE ('.implode(') OR (', $whereCats).')');
					$othercats = acymailing_loadResultArray($this->db);
					$selectedArea = array_merge($selectedArea, $othercats);
				}
			}

			if(!empty($selectedArea)){
				if(!ACYMAILING_J16){
					$where[] = implode(' OR ', $selectedArea);
				}else{
					$filter_cat = '`catid` IN ('.implode(',', $selectedArea).')';
					if(file_exists(JPATH_SITE.DS.'components'.DS.'com_multicats')){
						$filter_cat = '`catid` REGEXP "^([0-9]+,)*'.implode('(,[0-9]+)*$" OR `catid` REGEXP "^([0-9]+,)*', $selectedArea).'(,[0-9]+)*$"';
					}
					$where[] = $filter_cat;
				}
			}

			if(!empty($parameter->excludedcats)){
				$excludedCats = explode('-', $parameter->excludedcats);
				JArrayHelper::toInteger($excludedCats);
				$filter_cat = '`catid` NOT IN ("'.implode('","', $excludedCats).'")';
				if(file_exists(JPATH_SITE.DS.'components'.DS.'com_multicats')){
					$filter_cat = '`catid` NOT REGEXP "^([0-9]+,)*'.implode('(,[0-9]+)*$" AND `catid` NOT REGEXP "^([0-9]+,)*', $excludedCats).'(,[0-9]+)*$"';
				}
				$where[] = $filter_cat;
			}

			if(!empty($parameter->filter) && !empty($email->params['lastgenerateddate'])){
				$condition = '(`publish_up` > \''.date('Y-m-d H:i:s', $email->params['lastgenerateddate'] - date('Z')).'\' AND `publish_up` < \''.date('Y-m-d H:i:s', $time - date('Z')).'\')';
				$condition .= ' OR (`created` > \''.date('Y-m-d H:i:s', $email->params['lastgenerateddate'] - date('Z')).'\' AND `created` < \''.date('Y-m-d H:i:s', $time - date('Z')).'\')';
				if($parameter->filter == 'modify'){
					$modify = '(`modified` > \''.date('Y-m-d H:i:s', $email->params['lastgenerateddate'] - date('Z')).'\' AND `modified` < \''.date('Y-m-d H:i:s', $time - date('Z')).'\')';
					if(!empty($parameter->maxpublished)) $modify = '('.$modify.' AND `publish_up` > \''.date('Y-m-d H:i:s', time() - date('Z') - ((int)$parameter->maxpublished * 60 * 60 * 24)).'\')';
					$condition .= ' OR '.$modify;
				}

				$where[] = $condition;
			}

			if(!empty($parameter->maxcreated)){
				$date = $parameter->maxcreated;
				if(strpos($parameter->maxcreated, '[time]') !== false) $date = acymailing_replaceDate(str_replace('[time]', '{time}', $parameter->maxcreated));
				if(!is_numeric($date)) $date = strtotime($parameter->maxcreated);
				if(empty($date)){
					acymailing_display('Wrong date format ('.$parameter->maxcreated.' in '.$oneTag.'), please use YYYY-MM-DD', 'warning');
				}
				$where[] = '`created` < '.$this->db->Quote(date('Y-m-d H:i:s', $date)).' OR `publish_up` < '.$this->db->Quote(date('Y-m-d H:i:s', $date));
			}else{
				$where[] = '`publish_up` < \''.date('Y-m-d H:i:s', $time - date('Z')).'\'';
			}

			if(!empty($parameter->mincreated)){
				$date = $parameter->mincreated;
				if(strpos($parameter->mincreated, '[time]') !== false) $date = acymailing_replaceDate(str_replace('[time]', '{time}', $parameter->mincreated));
				if(!is_numeric($date)) $date = strtotime($parameter->mincreated);
				if(empty($date)){
					acymailing_display('Wrong date format ('.$parameter->mincreated.' in '.$oneTag.'), please use YYYY-MM-DD', 'warning');
				}
				$where[] = '`created` > '.$this->db->Quote(date('Y-m-d H:i:s', $date)).' OR `publish_up` > '.$this->db->Quote(date('Y-m-d H:i:s', $date));
			}


			if(!empty($parameter->meta)){
				$allMetaTags = explode(',', $parameter->meta);
				$metaWhere = array();
				foreach($allMetaTags as $oneMeta){
					if(empty($oneMeta)) continue;
					$metaWhere[] = "`metakey` LIKE '%".acymailing_getEscaped($oneMeta, true)."%'";
				}
				if(!empty($metaWhere)) $where[] = implode(' OR ', $metaWhere);
			}

			$where[] = '`publish_down` > \''.date('Y-m-d H:i:s', $time - date('Z')).'\' OR `publish_down` = 0';
			if(empty($parameter->unpublished)){
				$where[] = 'state = 1';
			}else{
				$where[] = 'state = 0';
			}

			if(!ACYMAILING_J16){
				if(isset($parameter->access)){
					$where[] = 'access <= '.intval($parameter->access);
				}else{
					if($this->params->get('contentaccess', 'registered') == 'registered'){
						$where[] = 'access <= 1';
					}elseif($this->params->get('contentaccess', 'registered') == 'public') $where[] = 'access = 0';
				}
			}elseif(isset($parameter->access)){
				if(strpos($parameter->access, ',')){
					$allAccess = explode(',', $parameter->access);
					JArrayHelper::toInteger($allAccess);
					$where[] = 'access IN ('.implode(',', $allAccess).')';
				}else{
					$where[] = 'access = '.intval($parameter->access);
				}
			}

			if(ACYMAILING_J16 && !empty($parameter->language)){
				$allLanguages = explode(',', $parameter->language);
				$langWhere = 'language IN (';
				foreach($allLanguages as $oneLanguage){
					$langWhere .= $this->db->Quote(trim($oneLanguage)).',';
				}
				$where[] = trim($langWhere, ',').')';
			}

			$query .= ' WHERE ('.implode(') AND (', $where).')';
			if(!empty($parameter->order)){
				$ordering = explode(',', $parameter->order);
				if($ordering[0] == 'rand'){
					$query .= ' ORDER BY rand()';
				}else{
					$query .= ' ORDER BY `'.acymailing_secureField($ordering[0]).'` '.acymailing_secureField($ordering[1]).' , a.`id` DESC';
				}
			}

			$start = '';
			if(!empty($parameter->start)) $start = intval($parameter->start).',';

			if(empty($parameter->max)) $parameter->max = 100;

			$query .= ' LIMIT '.$start.(int)$parameter->max;

			$this->db->setQuery($query);
			$allArticles = acymailing_loadResultArray($this->db);

			if(!empty($parameter->min) && count($allArticles) < $parameter->min){
				$return->status = false;
				$return->message = 'Not enough articles for the tag '.$oneTag.' : '.count($allArticles).' / '.$parameter->min.' between '.acymailing_getDate($email->params['lastgenerateddate']).' and '.acymailing_getDate($time);
			}

			$stringTag = empty($parameter->noentrytext) ? '' : $parameter->noentrytext;
			if(!empty($allArticles)){
				if(file_exists(ACYMAILING_MEDIA.'plugins'.DS.'autocontent.php')){
					ob_start();
					require(ACYMAILING_MEDIA.'plugins'.DS.'autocontent.php');
					$stringTag = ob_get_clean();
				}else{
					$arrayElements = array();
					$numArticle = 1;
					foreach($allArticles as $oneArticleId){
						$args = array();
						$args[] = 'joomlacontent:'.$oneArticleId;
						$args[] = 'num:'.$numArticle++;
						if(!empty($parameter->invert) && $numArticle % 2 == 1) $args[] = 'invert';
						if(!empty($parameter->type)) $args[] = 'type:'.$parameter->type;
						if(!empty($parameter->format)) $args[] = 'format:'.$parameter->format;
						if(!empty($parameter->template)) $args[] = 'template:'.$parameter->template;
						if(!empty($parameter->jtags)) $args[] = 'jtags';
						if(!empty($parameter->link)) $args[] = 'link';
						if(!empty($parameter->author)) $args[] = 'author';
						if(!empty($parameter->autologin)) $args[] = 'autologin';
						if(!empty($parameter->cattitle)) $args[] = 'cattitle';
						if(!empty($parameter->cattitlelink)) $args[] = 'cattitlelink';
						if(!empty($parameter->lang)) $args[] = 'lang:'.$parameter->lang;
						if(!empty($parameter->theme)) $args[] = 'theme';
						if(!empty($parameter->clean)) $args[] = 'clean';
						if(!empty($parameter->notitle)) $args[] = 'notitle';
						if(!empty($parameter->nopictstyle)) $args[] = 'nopictstyle';
						if(!empty($parameter->nopictlink)) $args[] = 'nopictlink';
						if(!empty($parameter->created)) $args[] = 'created';
						if(!empty($parameter->noattach)) $args[] = 'noattach';
						if(!empty($parameter->itemid)) $args[] = 'itemid:'.$parameter->itemid;
						if(!empty($parameter->noreadmore)) $args[] = 'noreadmore';
						if(isset($parameter->pict)) $args[] = 'pict:'.$parameter->pict;
						if(!empty($parameter->wrap)) $args[] = 'wrap:'.$parameter->wrap;
						if(!empty($parameter->maxwidth)) $args[] = 'maxwidth:'.$parameter->maxwidth;
						if(!empty($parameter->maxheight)) $args[] = 'maxheight:'.$parameter->maxheight;
						if(!empty($parameter->readmore)) $args[] = 'readmore:'.$parameter->readmore;
						if(!empty($parameter->dateformat)) $args[] = 'dateformat:'.$parameter->dateformat;
						if(!empty($parameter->textafter)) $args[] = 'textafter:'.$parameter->textafter;
						if(!empty($parameter->maxchar)) $args[] = 'maxchar:'.$parameter->maxchar;
						if(!empty($parameter->share)) $args[] = 'share:'.$parameter->share;
						if(!empty($parameter->sharetxt)) $args[] = 'sharetxt:'.$parameter->sharetxt;
						if(!empty($parameter->catpict)) $args[] = 'catpict';
						if(!empty($parameter->catmaxwidth)) $args[] = 'catmaxwidth:'.$parameter->catmaxwidth;
						if(!empty($parameter->catmaxheight)) $args[] = 'catmaxheight:'.$parameter->catmaxheight;
						$arrayElements[] = '{'.implode('|', $args).'}';
					}
					$stringTag = $this->acypluginsHelper->getFormattedResult($arrayElements, $parameter);
				}
			}
			$this->tags[$oneTag] = $stringTag;
		}

		return $return;
	}
}//endclass
