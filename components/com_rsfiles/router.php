<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

function RSFilesBuildRoute(&$query) {
	
	$segments = array();
	
	$lang = JFactory::getLanguage();
	$lang->load('com_rsfiles', JPATH_SITE);
	
	// get a menu item based on Itemid or currently active
	$menu = JFactory::getApplication()->getMenu();
	if (empty($query['Itemid'])) {
		$menuItem = $menu->getActive();
	} else {
		$menuItem = $menu->getItem($query['Itemid']);
	}
	
	$mView	= (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
	
	$is_menu_item = false;
	
	if (isset($query['path']))
		$query['path'] = urlencode($query['path']);
	
	if (isset($query['folder']))
		$query['folder'] = urlencode($query['folder']);
	
	// RSFiles! tasks
	if (isset($query['task']))
	{
		switch ($query['task'])
		{
			case 'captcha':
				$segments[] = JText::_('COM_RSFILES_CAPTCHA_SEF');
			break;
			
			case 'filepath':
				$segments[] = JText::_('COM_RSFILES_FILEPATH_SEF');
			break;
			
			case 'rsfiles.download':
				$segments[] = JText::_('COM_RSFILES_DOWNLOAD_FILE_SEF');
			break;
			
			case 'rsfiles.removebookmark':
				$segments[] = JText::_('COM_RSFILES_DELETE_BOOKMARK_SEF');
			break;
			
			case 'rsfiles.delete':
				$segments[] = JText::_('COM_RSFILES_DELETE_SEF');
			break;
			
			case 'preview':
				$segments[] = JText::_('COM_RSFILES_PREVIEW_FILE_SEF');
				
				if (isset($query['id']))
					$segments[] = $query['id'];
				
			break;
			
			case 'approve':
				$segments[] = JText::_('COM_RSFILES_APPROVE_SEF');
			break;
		}
	}
	
	// Set the default view
	if (!isset($query['view']))
		$query['view'] = 'rsfiles';
	
	// RSFiles! views
	if (isset($query['view']))
	{
		switch ($query['view'])
		{
			case 'rsfiles':
				if (!isset($query['layout']))
					$query['layout'] = 'default';
				
				// are we dealing with a files list that is attached to a menu item?
				if (($mView == 'rsfiles')) {
					$is_menu_item = true;
					unset($query['view']);
				}
				
				switch($query['layout'])
				{
					case 'default':
						if (!$is_menu_item)
							$segments[] = JText::_('COM_RSFILES_FILES_SEF');
					break;
					
					case 'search':
						$segments[] = JText::_('COM_RSFILES_SEARCH_SEF');
					break;
					
					case 'upload':
						$segments[] = JText::_('COM_RSFILES_UPLOAD_SEF');
					break;
					
					case 'create':
						$segments[] = JText::_('COM_RSFILES_CREATE_SEF');
					break;
					
					case 'briefcase':
						$segments[] = JText::_('COM_RSFILES_BRIEFCASE_SEF');
					break;
					
					case 'bookmarks':
						$segments[] = JText::_('COM_RSFILES_BOOKMARKS_SEF');
					break;
					
					case 'download':
						$segments[] = JText::_('COM_RSFILES_DOWNLOAD_SEF');
					break;
					
					case 'details':
						$segments[] = JText::_('COM_RSFILES_DETAILS_SEF');
					break;
					
					case 'preview':
						$segments[] = JText::_('COM_RSFILES_PREVIEW_SEF');
					break;
					
					case 'edit':
						$segments[] = JText::_('COM_RSFILES_EDIT_SEF');
					break;
					
					case 'license':
						$segments[] = JText::_('COM_RSFILES_LICENSE_SEF');
						
						if (isset($query['id']))
							$segments[] = $query['id'];
					break;
					
					case 'report':
						$segments[] = JText::_('COM_RSFILES_REPORT_SEF');
					break;
					
					case 'agreement':
						$segments[] = JText::_('COM_RSFILES_AGREEMENT_SEF');
						
						if (isset($query['id']))
							$segments[] = $query['id'];
						
					break;
					
					case 'email':
						$segments[] = JText::_('COM_RSFILES_EMAIL_DOWNLOAD_SEF');
					break;
					
					case 'validate':
						$segments[] = JText::_('COM_RSFILES_VALIDATE_SEF');
					break;
					
				}
			break;
			
			case 'users':
				$segments[] = JText::_('COM_RSFILES_USERS_SEF');
			break;
		}
	}
	
	unset($query['view'], $query['layout'], $query['controller'], $query['task'], $query['id'], $query['tmpl']);
	return $segments;
}

function RSFilesParseRoute($segments) {
	
	$query = array();
	
	$lang = JFactory::getLanguage();
	$lang->load('com_rsfiles', JPATH_SITE);
	
	//Get the active menu item
	$menu			= JFactory::getApplication()->getMenu();
	$item			= $menu->getActive();
	$routes			= getAllRsfilesRoutes();
	$segments[0]	= str_replace(':','-',$segments[0]);
	
	if ($item && isset($item->query) && isset($item->query['option']) && $item->query['option'] == 'com_rsfiles')
	{
		if (isset($item->query['view']))
			switch ($item->query['view'])
			{
				case 'rsfiles':
					$query['view']   = 'rsfiles';
					if (!in_array($segments[0], $routes))
					{
						array_unshift($segments, JText::_('COM_RSFILES_FILES_SEF'));
						$query['layout'] = 'default';
					}
				break;
			}
	}
	
	switch ($segments[0])
	{
		case JText::_('COM_RSFILES_SEARCH_SEF'): 
			$query['view']		= 'rsfiles';
			$query['layout'] 	= 'search';
		break;
		
		case JText::_('COM_RSFILES_UPLOAD_SEF'): 
			$query['view']		= 'rsfiles';
			$query['layout'] 	= 'upload';
		break;
		
		case JText::_('COM_RSFILES_CREATE_SEF'): 
			$query['view']		= 'rsfiles';
			$query['layout'] 	= 'create';
			$query['tmpl']	 	= 'component';
		break;
		
		case JText::_('COM_RSFILES_BRIEFCASE_SEF'): 
			$query['view']		= 'rsfiles';
			$query['layout'] 	= 'briefcase';
		break;
		
		case JText::_('COM_RSFILES_BOOKMARKS_SEF'): 
			$query['view']		= 'rsfiles';
			$query['layout'] 	= 'bookmarks';
		break;
		
		case JText::_('COM_RSFILES_DOWNLOAD_SEF'): 
			$query['view']		= 'rsfiles';
			$query['layout'] 	= 'download';
		break;
		
		case JText::_('COM_RSFILES_DETAILS_SEF'): 
			$query['view']		= 'rsfiles';
			$query['layout'] 	= 'details';
		break;
		
		case JText::_('COM_RSFILES_PREVIEW_SEF'): 
			$query['view']		= 'rsfiles';
			$query['layout'] 	= 'preview';
			$query['tmpl']	 	= 'component';
		break;
		
		case JText::_('COM_RSFILES_EDIT_SEF'): 
			$query['view']		= 'rsfiles';
			$query['layout'] 	= 'edit';
		break;
		
		case JText::_('COM_RSFILES_LICENSE_SEF'): 
			$query['view']		= 'rsfiles';
			$query['layout'] 	= 'license';
			$query['id']		= isset($segments[1]) ? str_replace(':','-',$segments[1]) : null;
			$query['tmpl']	 	= 'component';
		break;
		
		case JText::_('COM_RSFILES_REPORT_SEF'): 
			$query['view']		= 'rsfiles';
			$query['layout'] 	= 'report';
			$query['tmpl']	 	= 'component';
		break;
		
		case JText::_('COM_RSFILES_AGREEMENT_SEF'): 
			$query['view']		= 'rsfiles';
			$query['layout'] 	= 'agreement';
			$query['id']		= isset($segments[1]) ? str_replace(':','-',$segments[1]) : null;
			$query['tmpl']	 	= 'component';
		break;
		
		case JText::_('COM_RSFILES_EMAIL_DOWNLOAD_SEF'): 
			$query['view']		= 'rsfiles';
			$query['layout'] 	= 'email';
			$query['tmpl']	 	= 'component';
		break;
		
		case JText::_('COM_RSFILES_VALIDATE_SEF'): 
			$query['view']		= 'rsfiles';
			$query['layout'] 	= 'validate';
			$query['tmpl']	 	= 'component';
		break;
		
		case JText::_('COM_RSFILES_CAPTCHA_SEF'): 
			$query['task']		= 'captcha';
		break;
		
		case JText::_('COM_RSFILES_DOWNLOAD_FILE_SEF'): 
			$query['task']		= 'rsfiles.download';
		break;
		
		case JText::_('COM_RSFILES_DELETE_BOOKMARK_SEF'): 
			$query['task']		= 'rsfiles.removebookmark';
		break;
		
		case JText::_('COM_RSFILES_DELETE_SEF'): 
			$query['task']		= 'rsfiles.delete';
		break;
		
		case JText::_('COM_RSFILES_FILEPATH_SEF'): 
			$query['task']		= 'filepath';
		break;
		
		case JText::_('COM_RSFILES_USERS_SEF'): 
			$query['view']		= 'users';
			$query['layout']	= 'default';
			$query['tmpl']		= 'component';
		break;
		
		case JText::_('COM_RSFILES_PREVIEW_FILE_SEF'): 
			$query['task']		= 'preview';
			$query['tmpl']		= 'component';
			$query['id']		= isset($segments[1]) ? str_replace(':','-',$segments[1]) : null;
		break;
		
		case JText::_('COM_RSFILES_APPROVE_SEF'): 
			$query['task']		= 'approve';
		break;
		
	}

	return $query;
}

function getAllRsfilesRoutes()
{
	return array(JText::_('COM_RSFILES_FILES_SEF'), JText::_('COM_RSFILES_SEARCH_SEF'), JText::_('COM_RSFILES_UPLOAD_SEF'), JText::_('COM_RSFILES_CREATE_SEF'), JText::_('COM_RSFILES_BRIEFCASE_SEF'), 
				JText::_('COM_RSFILES_BOOKMARKS_SEF'), JText::_('COM_RSFILES_DOWNLOAD_SEF'), JText::_('COM_RSFILES_DETAILS_SEF'), JText::_('COM_RSFILES_PREVIEW_SEF'), JText::_('COM_RSFILES_LICENSE_SEF'),
				JText::_('COM_RSFILES_EDIT_SEF'), JText::_('COM_RSFILES_REPORT_SEF'), JText::_('COM_RSFILES_AGREEMENT_SEF'), JText::_('COM_RSFILES_EMAIL_DOWNLOAD_SEF'), JText::_('COM_RSFILES_VALIDATE_SEF'),
				JText::_('COM_RSFILES_CAPTCHA_SEF'), JText::_('COM_RSFILES_DOWNLOAD_FILE_SEF'), JText::_('COM_RSFILES_DELETE_BOOKMARK_SEF'), JText::_('COM_RSFILES_DELETE_SEF'), JText::_('COM_RSFILES_USERS_SEF')
				, JText::_('COM_RSFILES_FILEPATH_SEF'), JText::_('COM_RSFILES_PREVIEW_FILE_SEF'), JText::_('COM_RSFILES_APPROVE_SEF')
				);
}