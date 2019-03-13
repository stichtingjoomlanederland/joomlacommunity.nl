<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

function rsformBuildRoute(&$query)
{
	$segments   = array();
    $app 	    = JFactory::getApplication();

    // Load language
    JFactory::getLanguage()->load('com_rsform', JPATH_SITE);
	
	$app->triggerEvent('rsfp_onBeforeFormBuildRoute', array(&$segments, &$query));
	
	$view 	= isset($query['view']) ? $query['view'] : 'rsform';
	$layout = isset($query['layout']) ? $query['layout'] : 'default';
	
	// Is this a menu item?
	if (isset($query['Itemid']))
	{
		$menu = $app->getMenu();
		// Found the menu item based on Itemid
		if ($item = $menu->getItem($query['Itemid']))
		{
			// The Itemid belongs to RSForm! Pro
			if (isset($item->component) && $item->component == 'com_rsform' && isset($item->query))
			{
				// We've got a match
				if (isset($item->query['view']) && $item->query['view'] == $view)
				{
					switch ($view)
                    {
						// Form menu item
						case 'rsform':
							// If it's the same formId point to the menu item directly
							if (isset($item->query['formId']) && isset($query['formId']) && $item->query['formId'] == $query['formId'])
							{
								unset($query['view']);
								unset($query['formId']);
								
								// If we have a task append it
								if (isset($query['task']) && $query['task'] == 'confirm')
								{
									$segments[] = JText::_('COM_RSFORM_SEF_CONFIRM_SUBMISSION');
									unset($query['task']);
								}
								
								return $segments;
							}
						break;
						
						// Submissions menu item
						case 'submissions':
							// Submissions are only accessible through the menu, point to that
							if ($layout == 'default')
							{
								unset($query['view']);
								return $segments;
							}
							// Otherwise we continue with the logic below to show a submission {detail}
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
					$segments[] = JText::_('COM_RSFORM_SEF_DIRECTORY_VIEW_SUBMISSION');
					if (isset($query['id']))
					{
                        $segments[] = $query['id'];
                    }

					unset($query['view'], $query['layout'], $query['id']);
				break;
				
				case 'edit':
                    $segments[] = JText::_('COM_RSFORM_SEF_DIRECTORY_EDIT_SUBMISSION');
                    if (isset($query['id']))
                    {
                        $segments[] = $query['id'];
                    }

					unset($query['view'], $query['layout'], $query['id']);
				break;
				
				default:
				case 'default':
                    $segments[] = JText::_('COM_RSFORM_SEF_SUBMISSIONS_DIRECTORY');

					unset($query['view'], $query['layout']);
				break;
			}
		break;
		
		case 'submissions':
			switch ($layout)
			{
				case 'view':
					$segments[] = JText::_('COM_RSFORM_SEF_SUBMISSIONS_VIEW_SUBMISSION');
                    if (isset($query['cid']))
                    {
                        $segments[] = $query['cid'];
                    }
					
					unset($query['view'], $query['layout'], $query['cid']);
				break;
				
				default:
				case 'default':
                    $segments[] = JText::_('COM_RSFORM_SEF_SUBMISSIONS_VIEW_SUBMISSIONS');
					
					unset($query['view'], $query['layout']);
				break;
			}
		break;

		case 'rsform':
			if (!empty($query['formId']))
			{
				$segments[] = JText::_('COM_RSFORM_SEF_FORM');
				
				$formId = (int) $query['formId'];

                $db = JFactory::getDbo();
				$sql = $db->getQuery(true)
                    ->select($db->qn('FormTitle'))
                    ->from($db->qn('#__rsform_forms'))
                    ->where($db->qn('FormId') . ' = ' . $db->q($formId));

				$formName = JFilterOutput::stringURLSafe($db->setQuery($sql)->loadResult());
				
				$segments[] = $formId . (!empty($formName) ? ':' . $formName : '');
				
				unset($query['formId'], $query['view']);
			}
			unset($query['view']);
		break;
	}
	
	if (isset($query['task']))
	{
        switch ($query['task'])
        {
            case 'confirm':
                $segments[] = JText::_('COM_RSFORM_SEF_CONFIRM_SUBMISSION');
                unset($query['task']);
                break;
        }
    }

    $app->triggerEvent('rsfp_onAfterFormBuildRoute', array(&$segments, &$query));
	
	return $segments;
}

function rsformParseRoute($segments)
{
	$query = array();
	$app = JFactory::getApplication();

    // Load language
    JFactory::getLanguage()->load('com_rsform', JPATH_SITE);
	
	$app->triggerEvent('rsfp_onBeforeFormParseRoute', array(&$segments, &$query));
	
	$segments[0] = !empty($segments[0]) ? $segments[0] : 'form';
	$segments[0] = str_replace(':', '-', $segments[0]);
	
	switch ($segments[0])
	{
        case JText::_('COM_RSFORM_SEF_FORM'):
            if (isset($segments[1]))
            {
                $exp = explode(':', $segments[1], 2);
                $query['formId'] = (int) $exp[0];
            }
		break;
		
        case JText::_('COM_RSFORM_SEF_SUBMISSIONS_VIEW_SUBMISSIONS'):
			$query['view'] = 'submissions';
		break;
		
        case JText::_('COM_RSFORM_SEF_SUBMISSIONS_VIEW_SUBMISSION'):
			$query['view'] = 'submissions';
			$query['layout'] = 'view';

			if (isset($segments[1]))
			{
                $query['cid'] = $segments[1];
            }
		break;
		
        case JText::_('COM_RSFORM_SEF_CONFIRM_SUBMISSION'):
			$query['task'] = 'confirm';
		break;
		
        case JText::_('COM_RSFORM_SEF_SUBMISSIONS_DIRECTORY'):
			$query['view'] = 'directory';
		break;
		
        case JText::_('COM_RSFORM_SEF_DIRECTORY_VIEW_SUBMISSION'):
			$query['view'] = 'directory';
			$query['layout'] = 'view';
            if (isset($segments[1]))
            {
                $query['id'] = $segments[1];
            }
		break;
		
        case JText::_('COM_RSFORM_SEF_DIRECTORY_EDIT_SUBMISSION'):
			$query['view'] = 'directory';
			$query['layout'] = 'edit';
            if (isset($segments[1]))
            {
                $query['id'] = $segments[1];
            }
		break;
	}
	
	$app->triggerEvent('rsfp_onAfterFormParseRoute', array(&$segments, &$query));
	
	return $query;
}