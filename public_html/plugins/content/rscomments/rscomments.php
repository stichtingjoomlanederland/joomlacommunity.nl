<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin');

class plgContentRSComments extends JPlugin
{
	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	public function onContentAfterDisplay($context, &$article, &$params, $limitstart = 0) {
		
		if ($context == 'mod_articles_news.content') {
			return;
		}
		
		$parameters = $params;
		if (isset($params)) {
			if (is_string($params)) {
				$params = new JRegistry;
				$params->loadString($parameters);
			}
			
			if ($params->exists('moduleclass_sfx')) {
				return;
			}
		}
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$app	= JFactory::getApplication();
		$input	= $app->input;
		
		if ($app->getName() != 'site') {
			return;
		}
		
		// Check if RSComments! is installed
		if (!$this->canRun()) {
			return;
		}
		
		// Load the language file
		JFactory::getLanguage()->load('com_rscomments');
		
		$config = RSCommentsHelper::getConfig();
		$option = $input->getCmd('option');
		$view	= $input->getCmd('view');
		
		// Can we run this plugin ?
		if ($article instanceof JCategoryNode || $option != 'com_content' || !isset($article->id)) {
			RSCommentsHelper::clean($article); 
			return;
		}
		
		$content	= $article->introtext.$article->fulltext;
		$this->_on  = RSCommentsHelper::rscOn($content);
		$this->_off = RSCommentsHelper::rscOff($content);
		
		// Remove the {rscomments on|off}
		RSCommentsHelper::clean($article);
		
		// We are not allowed to show comments in these categories
		if (isset($config->categories)) {
			if ($categories = $config->categories) {
				if (in_array($article->catid,$categories) && !$this->_on) {
					return;
				}
			}
		}
		
		// Show the number of comments
		if ($view == 'frontpage' || $view == 'category' || $view == 'featured' || $option != 'com_content')  {
			if ($this->_off) {
				return;
			}
			
			$comments			= RSCommentsHelper::getCommentsNumber($article->id,true);
			$text				= empty($comments) ? JText::_('COM_RSCOMMENTS_NO_COMMENTS') : JText::sprintf('COM_RSCOMMENTS_COMMENTS_NUMBER',$comments);
			$show_no_comments	= $config->show_no_comments;
			
			$image = JHtml::image('com_rscomments/comments.png', '', array(), true);
			
			if (empty($comments)) {
				if ($show_no_comments) {
					$article->introtext = $article->introtext.'<div class="rsc_comments_count">'.$image.' <a href="'.JRoute::_(ContentHelperRoute::getArticleRoute(!empty($article->slug) ? $article->slug : $article->id, !empty($article->catslug) ? $article->catslug : $article->catid)).'">'.$text.'</a></div>'; 
				}
			} else {
				$article->introtext = $article->introtext.'<div class="rsc_comments_count">'.$image.' <a href="'.JRoute::_(ContentHelperRoute::getArticleRoute(!empty($article->slug) ? $article->slug : $article->id, !empty($article->catslug) ? $article->catslug : $article->catid)).'">'.$text.'</a></div>';
			}
			
			return;
		}
		
		if ($this->_off) {
			$comments_closed = RSCommentsHelper::getMessage('comments_closed');
			$msg = empty($comments_closed) ? '' : '<hr/><div class="rsc_comments_closed">'.$comments_closed.'</div>';
			
			return $msg; 
		}
		
		$this->_template = RSCommentsHelper::getTemplate();
		$this->_articleid = $article->id;
		
		// Clean the cache
		RSCommentsHelper::clearCache();
		
		// Load css/js data
		RSCommentsHelper::loadScripts();
		
		return RSCommentsHelper::showRSComments('com_content',$this->_articleid,$this->_template, null, $this->_on);
	}
	
	protected function canRun() {
		if (file_exists( JPATH_SITE.'/administrator/components/com_rscomments/rscomments.php' )) {
			require_once JPATH_SITE.'/components/com_rscomments/helpers/tooltip.php';
			require_once JPATH_SITE.'/components/com_rscomments/helpers/rscomments.php';
			
			return true;
		}
		
		return false;
	}
}