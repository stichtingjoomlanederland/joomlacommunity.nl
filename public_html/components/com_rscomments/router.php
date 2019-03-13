<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsRouter extends JComponentRouterBase {
	
	/**
	 * Build the route for the com_content component
	 *
	 * @param   array  &$query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   3.3
	 */
	public function build(&$query) {
		$segments = array();
		
		if (isset($query['view'])) {
			$segments[] = 'rscomments';
		}
		
		// Do we have a task ?
		if (isset($query['task'])) {
			switch ($query['task']) {
				case 'terms':			$segments[] = 'terms'; 			break;
				case 'captcha':			$segments[] = 'captcha';		break;
				case 'upload':			$segments[] = 'upload';			break;
				case 'uploadfile':		$segments[] = 'uploadfile';		break;
				case 'refresh':			$segments[] = 'refresh';		break;
				case 'subscribeuser':	$segments[] = 'subscribeuser';	break;
				case 'openthread':		$segments[] = 'openthread';		break;
				case 'closethread':		$segments[] = 'closethread';	break;
				case 'report':			$segments[] = 'report';			break;
				case 'mycomments':		$segments[] = 'mycomments';		break;
				
				case 'removecomment':
					$segments[] = 'remove-comment';
					if (isset($query['id'])) {
						$segments[] = $query['id'];
						unset($query['id']);
					}
				break;
				
				case 'subscribe':
					$segments[] = 'subscribe';
					
					if (isset($query['theoption'])) {
						$segments[] = $query['theoption'];
					}
					
					if (isset($query['id'])) {
						$segments[] = $query['id'];
						unset($query['id']);
					}
				break;
				
				case 'download':
					$segments[] = 'download';
					
					if (isset($query['id'])) {
						$segments[] = $query['id'];
						unset($query['id']);
					}
				break;
				
				case 'approve':
					$segments[] = 'approve';
					
					if (isset($query['id'])) {
						$segments[] = $query['id'];
						unset($query['id']);
					}
				break;
				
				case 'delete':
					$segments[] = 'delete';
					
					if (isset($query['id'])) {
						$segments[] = $query['id'];
					}
				break;
			}
		}
	
		unset($query['view'],$query['layout'],$query['controller'],$query['task'],$query['tmpl'],$query['theoption']);
		
		return $segments;
	}
	
	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   3.3
	 */
	public function parse(&$segments) {
		$query = array();
		
		$segments[0] = str_replace(':','-',$segments[0]);
		switch ($segments[0]) {	
			
			case 'rscomments':
				$query['view'] = 'rscomments';
			break;
			
			case 'subscribe':
				$query['task'] = 'subscribe';
				$query['tmpl'] = 'component';
				
				if (isset($segments[1])) {
					$query['theoption'] = $segments[1];
				}
				
				if (isset($segments[2])) {
					$query['id'] = $segments[2];
				}
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
				$query['task'] = 'report';
				$query['tmpl'] = 'component';
			break;
			
			case 'mycomments':
				$query['task'] = 'mycomments';
				$query['tmpl'] = 'component';
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
			
			case 'remove-comment':
				$query['task'] = 'removecomment';
				
				if (isset($segments[1])) {
					$query['id'] = (int) $segments[1];
				}
			break;
		}
		
		// Joomla 4.x compatibility 
		$jversion = new JVersion();
		if ($jversion->isCompatible('4')) {
			$segments = array();
		}
		
		return $query;
	}
}

// Legacy functions 
function rscommentsBuildRoute(&$query) {
	$router = new RscommentsRouter;
	
	return $router->build($query);
}

function rscommentsParseRoute($segments) {
	$router = new RscommentsRouter;
	
	return $router->parse($segments);
}