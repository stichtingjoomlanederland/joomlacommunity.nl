<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesViewRsfiles extends JViewLegacy
{
	public function display($tpl = null) {
		$app	= JFactory::getApplication();
		$doc	= JFactory::getDocument();
		$itemid	= rsfilesHelper::getItemid();
		
		// Get some data from the model
		$app->input->set('limit', $app->getCfg('feed_limit'));
		$items = $this->get('Items');
		
		if (!empty($items)) {
			foreach ($items as $item) {
				// strip html from feed item title
				$title = $this->escape($item->name);
				$title = html_entity_decode($title);
				
				// url link to article
				// & used instead of &amp; as this is converted by feed creator
				if ($item->type == 'folder') {
					$link = 'index.php?option=com_rsfiles&folder='.$item->fullpath.$itemid;
				} else {
					$link = 'index.php?option=com_rsfiles&layout=download&path='.$item->fullpath.$itemid;
				}
				$link = JRoute::_( $link );
				@$date = ($item->time ? date('r', strtotime($item->time)) : '');
				
				// load individual item creator class
				$item = new JFeedItem();
				$item->title 		= $title;
				$item->link 		= $link;
				$item->description 	= $item->filedescription;
				$item->date			= $date;

				// loads item info into rss array
				$doc->addItem( $item );
			}
		}
	}
}