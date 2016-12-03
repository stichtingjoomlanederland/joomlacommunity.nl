<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

function RSCommentsBuildRoute(&$query)
{
	// The output array, which will be parsed in the next function
	$segments = array();
	
	//set default view
	$query['view'] = 'rscomments';
		
		if (isset($query['layout']))
			switch ($query['layout'])
			{
				case 'report':
					$segments[] = 'report';
					
					if (isset($query['id']))
						$segments[] = $query['id'];
					
				break;
			}
		
		if (isset($query['task']))
			switch ($query['task'])
			{
				case 'terms':
					$segments[] = 'terms';
				break;
				
				case 'subscribe':
					$segments[] = 'subscribe';
					
					if (isset($query['theoption'])) {
						$segments[] = $query['theoption'];
					}
					
					if (isset($query['id'])) {
						$segments[] = $query['id'];
					}
					
				break;
				
				case 'captcha':
					$segments[] = 'captcha';
				break;
				
				case 'upload':
					$segments[] = 'upload';
				break;
				
				case 'uploadfile':
					$segments[] = 'uploadfile';
				break;
				
				case 'download':
					$segments[] = 'download';
					
					if (isset($query['id'])) {
						$segments[] = $query['id'];
					}
				break;
				
				case 'refresh':
					$segments[] = 'refresh';
				break;
				
				case 'subscribeuser':
					$segments[] = 'subscribeuser';
				break;
				
				case 'openthread':
					$segments[] = 'openthread';
				break;
				case 'closethread':
					$segments[] = 'closethread';
				break;
				
				case 'approve':
					$segments[] = 'approve';
					
					if (isset($query['id'])) {
						$segments[] = $query['id'];
					}
				break;
				
				case 'delete':
					$segments[] = 'delete';
					
					if (isset($query['id'])) {
						$segments[] = $query['id'];
					}
				break;
			}
	
	unset($query['view'],$query['layout'],$query['controller'],$query['task'],$query['tmpl'],$query['id'],$query['theoption']);
	
	return $segments;
}

function RSCommentsParseRoute($segments)
{
	$query = array();
	//Replacing the ':' which is by default with '-' in the segments
	$segments[0] = str_replace(':','-',$segments[0]);
	
	switch ($segments[0])
	{	
		case 'subscribe':
			$query['task'] = 'subscribe';
		break;
		
		case 'subscribeuser':
			$query['task'] = 'subscribeuser';
			$query['controller'] = 'rscomments';
		break;
		
		case 'terms':
			$query['task'] = 'terms';
			$query['tmpl'] = 'component';
		break;
		
		case 'captcha':
			$query['task'] = 'captcha';
		break;
		
		case 'upload':
			$query['task'] = 'upload';
			$query['tmpl'] = 'component';
		break;
		
		case 'uploadfile':
			$query['task'] = 'uploadfile';
			$query['tmpl'] = 'component';
		break;
		
		case 'download':
			$query['task'] = 'download';
			$query['id']   = (int) $segments[1];
			$query['tmpl'] = 'component';
		break;
		
		case 'refresh':
			$query['task'] = 'refresh';
			$query['tmpl'] = 'component';
		break;
		
		case 'openthread':
			$query['task'] = 'openthread';
			$query['controller'] = 'comments';
		break;
		
		case 'closethread':
			$query['task'] = 'closethread';
			$query['controller'] = 'comments';
		break;
		
		case 'report':
			$query['view'] = 'rscomments';
			$query['layout'] = 'report';
			$query['tmpl'] = 'component';
			
			if (isset($segments[1])) {
				$query['id'] = (int) $segments[1];
			}
		break;
		
		case 'approve':
			$query['task'] = 'approve';
			
			if (isset($segments[1])) {
				$query['id'] = (int) $segments[1];
			}
		break;
		
		case 'delete':
			$query['task'] = 'delete';
			
			if (isset($segments[1])) {
				$query['id'] = (int) $segments[1];
			}
		break;
	}
	return $query;
}