<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * RSFiles! system plugin
 */
class plgSystemRSFiles extends JPlugin
{
	/**
	 * Constructor
	 */
	public function __construct( &$subject, $config ) {
		parent::__construct( $subject, $config );
	}
	
	protected function canRun() {
		if (JFactory::getApplication()->isAdmin())
			return false;
		
		if (file_exists(JPATH_SITE.'/components/com_rsfiles/rsfiles.php')) {
			require_once JPATH_SITE.'/components/com_rsfiles/helpers/rsfiles.php';
			JFactory::getLanguage()->load('plg_system_rsfiles',JPATH_ADMINISTRATOR);
			
			return true;
		}
			
		return false;
	}
	
	public function onAfterDispatch() {
		if (!$this->canRun()) {
			return;
		}
		
		JHtml::_('behavior.modal','.rs_modal');
		rsfilesHelper::tooltipLoad();
	}
	
	public function onAfterRender() {
		if (!$this->canRun()) {
			return;
		}
		
		$content	= JResponse::getBody();
		$hasContent = false;
		$regex		= '#{rsfiles(.*?)}#is';
		$pattern	= '#\s+?(.*?)=["|\'](.*?)["|\']#is';
		
		preg_match_all($regex, $content, $filematches);
		
		if (isset($filematches) && isset($filematches[1])) {
			foreach ($filematches[1] as $i => $match) {
				if (!empty($match)) {
					preg_match_all($pattern,$match,$options);
					
					if (!empty($options) && !empty($options[1])) {
						$keys	= isset($options[1]) ? $options[1] : array();
						$values = isset($options[2]) ? $options[2] : array();
						
						$key_path		= array_search('path',$keys);
						$key_version	= array_search('version',$keys);
						$key_license	= array_search('license',$keys);
						$key_size		= array_search('size',$keys);
						$key_date		= array_search('date',$keys);
						$key_ordering	= array_search('ordering',$keys);
						$key_order		= array_search('order',$keys);
						$key_itemid		= array_search('itemid',$keys);
						
						$ordering = $key_ordering !== false && isset($values[$key_ordering]) ? strtolower($values[$key_ordering]) : null;
						if ($ordering) {
							if (in_array(strtolower($ordering), array('name','date','hits'))) {
								$ordering = strtolower($ordering);
							} else {
								$ordering = $this->params->get('order','name');
							}
						} else {
							$ordering = $this->params->get('order','name');
						}
						
						$order = $key_order !== false && isset($values[$key_order]) ? strtoupper($values[$key_order]) : null;
						if ($order) {
							if (in_array($order, array('ASC','DESC'))) {
								$order = $order;
							} else {
								$order = $this->params->get('order_way','ASC');
							}
						} else {
							$order = $this->params->get('order_way','ASC');
						}
						
						$itemid = $key_itemid !== false && isset($values[$key_itemid]) ? (int) $values[$key_itemid] : (int) $this->params->get('itemid',0);
						$path	= $key_path !== false && isset($values[$key_path]) ? $values[$key_path] : null;
						
						$fparams = new JRegistry;
						$fparams->set('ordering', $ordering);
						$fparams->set('order', $order);
						$fparams->set('itemid', $itemid);
						$fparams->set('version', $key_version !== false && isset($values[$key_version]) ? $values[$key_version] : (int) $this->params->get('list_show_version',1));
						$fparams->set('license', $key_license !== false && isset($values[$key_license]) ? $values[$key_license] : (int) $this->params->get('list_show_license',1));
						$fparams->set('size', $key_size !== false && isset($values[$key_size]) ? $values[$key_size] : (int) $this->params->get('list_show_size',1));
						$fparams->set('date', $key_date !== false && isset($values[$key_date]) ? $values[$key_date] : (int) $this->params->get('list_show_date',1));
						
						// Replace syntax
						if ($replace = $this->parse($path, $fparams)) {
							$content = str_replace($filematches[0][$i],$replace,$content);
							$hasContent = true;
						}
					}
				}
			}
		}

		if ($hasContent) {
			// Load css
			if (!is_null($path)) {
				$css  = '<link rel="stylesheet" href="'.JURI::root(true).'/components/com_rsfiles/assets/css/style.css" type="text/css" />'."\n";
				$css .= '<link rel="stylesheet" href="'.JURI::root(true).'/components/com_rsfiles/assets/icons/rsicon.css" type="text/css" />'."\n";
				
				if (rsfilesHelper::isJ3()) {
					$css .= '<link rel="stylesheet" href="'.JURI::root(true).'/components/com_rsfiles/assets/css/j3.css" type="text/css" />'."\n";
				} else {
					if (rsfilesHelper::getConfig('load_bootstrap')) {
						$css .= '<link rel="stylesheet" href="'.JURI::root(true).'/components/com_rsfiles/assets/css/j2.css" type="text/css" />'."\n";
					}
				}
				$css .= '</head>';
				
				$content = str_replace('</head>',$css,$content);
			}
			
			JResponse::setBody($content);
		}
	}
	
	/**
	 *	Parse rsfiles placeholders
	 */
	protected function parse($path, $params) {
		if (!$this->canRun()) {
			return;
		}
		
		$config				= rsfilesHelper::getConfig();
		$download_folder	= $config->download_folder;
		$path				= urldecode($path);
		
		if (empty($download_folder)) {
			return JText::_('PLG_RSFILES_NO_DOWNLOAD_FOLDER');
		}
		
		if (!is_dir(realpath($download_folder))) {
			return JText::_('PLG_RSFILES_INVALID_DOWNLOAD_FOLDER');
		}
		
		$params->set('header', $this->params->get('fheader', 0));
		return rsfilesHelper::display($path, $params);
	}
}