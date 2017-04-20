<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE.'/components/com_rscomments/models/comments.php';
require_once JPATH_SITE.'/components/com_rscomments/helpers/rscomments.php';

class RscommentsViewRscomments extends JViewLegacy {

	public function display() {
		$doc	= JFactory::getDocument();
		$id		= JFactory::getApplication()->input->get('id');
		$option	= JFactory::getApplication()->input->get('opt');
		
		$class = new RscommentsModelComments($id,$option);
		$rows  = $class->getComments();
		$permissions = RSCommentsHelper::getPermissions();
		
		foreach ($rows as $row ) {
			$item = new JFeedItem();
			$item->title 		= !empty($row->subject) ? $row->subject : JText::_('COM_RSCOMMENTS_NO_SUBJECT');
			$item->link 		= $this->escape(JURI::base().base64_decode($row->url));
			$item->description 	= RSCommentsHelper::parseComment($row->comment,$permissions);
			$item->date			= JFactory::getDate($row->date)->format('r');
			$item->category   	= 'Comments';
			
			$doc->addItem( $item );
		}
	}
}