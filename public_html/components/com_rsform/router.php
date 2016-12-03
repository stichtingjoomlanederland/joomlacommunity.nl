<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

function RSFormBuildRoute(&$query)
{
	$segments = array();
	
	JFactory::getApplication()->triggerEvent('rsfp_onBeforeFormBuildRoute', array(&$segments, &$query));
	
	$view 	= isset($query['view']) ? $query['view'] : 'rsform';
	$layout = isset($query['layout']) ? $query['layout'] : 'default';
	
	// is this a menu item ?
	if (isset($query['Itemid'])) {
		$app 	= JFactory::getApplication();
		$menu 	= $app->getMenu();
		// found the menu item based on itemid
		if ($item = $menu->getItem($query['Itemid'])) {
			// the itemid belongs to rsform
			if (isset($item->component) && $item->component == 'com_rsform' && isset($item->query)) {
				// we've got a match
				if (isset($item->query['view']) && $item->query['view'] == $view) {
					switch ($view) {
						// form menu item
						case 'rsform':
							// if it's the same formId point to the menu item directly
							if (isset($item->query['formId']) && isset($query['formId']) && $item->query['formId'] == $query['formId']) {
								unset($query['view']);
								unset($query['formId']);
								
								// if we have a task append it
								if (isset($query['task']) && $query['task'] == 'confirm') {
									$segments[] = 'confirm-submission';
									unset($query['task']);
								}
								
								return $segments;
							}
						break;
						
						// submissions menu item
						case 'submissions':
							// submissions are only accessible through the menu, point to that
							if ($layout == 'default') {
								unset($query['view']);
								return $segments;
							}
							// otherwise we continue with the logic below to show a submission {detail}
						break;
					}
				}
			}
		}
	}
	
	switch ($view)
	{
		case 'directory':
			switch ($layout)
			{
				case 'view':
					$segments[] = 'submission-view';
					$segments[] = @$query['id'];
					
					unset($query['view'], $query['layout'], $query['id']);
				break;
				
				case 'edit':
					$segments[] = 'submission-edit';
					$segments[] = @$query['id'];
					
					unset($query['view'], $query['layout'], $query['id']);
				break;
				
				default:
				case 'default':
					$segments[] = 'submissions-directory';
					
					unset($query['view'], $query['layout']);
				break;
			}
		break;
		
		case 'submissions':
			switch ($layout)
			{
				case 'view':
					$segments[] = 'view-submission';
					$segments[] = @$query['cid'];
					
					unset($query['view'], $query['layout'], $query['cid']);
				break;
				
				default:
				case 'default':
					$segments[] = 'view-submissions';
					
					unset($query['view'], $query['layout']);
				break;
			}
		break;
		
		//default:
		case 'rsform':
			if (!empty($query['formId']))
			{
				$segments[] = 'form';
				
				$formId = (int) $query['formId'];
				
				$db = JFactory::getDBO();
				$db->setQuery("SELECT `FormTitle` FROM #__rsform_forms WHERE `FormId`='".$formId."'");
				$formName = JFilterOutput::stringURLSafe($db->loadResult());
				
				$segments[] = $formId.(!empty($formName) ? ':'.$formName : '');
				
				unset($query['formId'], $query['view']);
			}
			unset($query['view']);
		break;
	}
	
	if (isset($query['task']))
		switch($query['task'])
		{
			case 'confirm':
				$segments[] = 'confirm-submission';
				unset($query['task']);
			break;
		}
	
	JFactory::getApplication()->triggerEvent('rsfp_onAfterFormBuildRoute', array(&$segments, &$query));
	
	return $segments;
}

function RSFormParseRoute($segments)
{
	$query = array();
	
	JFactory::getApplication()->triggerEvent('rsfp_onBeforeFormParseRoute', array(&$segments, &$query));
	
	$segments[0] = !empty($segments[0]) ? $segments[0] : 'form';
	$segments[0] = str_replace(':', '-', $segments[0]);
	
	switch ($segments[0])
	{
		//default:
		case 'form':
			$exp = explode(':', @$segments[1]);
			$query['formId'] = (int) @$exp[0];
		break;
		
		case 'view-submissions':
			$query['view'] = 'submissions';
		break;
		
		case 'view-submission':
			$query['view'] = 'submissions';
			$query['layout'] = 'view';
			$query['cid'] = @$segments[1];
		break;
		
		case 'confirm-submission':
			$query['task'] = 'confirm';
		break;
		
		case 'submissions-directory':
			$query['view'] = 'directory';
		break;
		
		case 'submission-view':
			$query['view'] = 'directory';
			$query['layout'] = 'view';
			$query['id'] = @$segments[1];
		break;
		
		case 'submission-edit':
			$query['view'] = 'directory';
			$query['layout'] = 'edit';
			$query['id'] = @$segments[1];
		break;
	}
	
	JFactory::getApplication()->triggerEvent('rsfp_onAfterFormParseRoute', array(&$segments, &$query));
	
	return $query;
}