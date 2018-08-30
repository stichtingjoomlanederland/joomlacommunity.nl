<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_rseventspro
 * @since       1.6
 */
class JFormFieldFbpages extends JFormFieldList
{
	/**
	 *
	 * @var		string
	 * @since	1.6
	 */
	public $type = 'Fbpages';
	
	protected function getInput() {
		if (!is_array($this->value)) {		
			if (strpos($this->value, ',') !== false) {
				$this->setValue(explode(',',$this->value));
			}
		}
		
		return parent::getInput();
	}
	
	protected function getOptions() {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/rseventspro.php';
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/facebook/autoload.php';
		
		$options	= array();
		$config		= rseventsproHelper::getConfig();
		
		if ($config->facebook_token) {
			try {
				$facebook = new Facebook\Facebook(array(
					'app_id' => $config->facebook_appid,
					'app_secret' => $config->facebook_secret,
					'default_graph_version' => 'v2.10',
					'default_access_token' => $config->facebook_token
				));
				
				$request	= $facebook->get('me/accounts?fields=id,name&limit=200');
				$accounts	= $request->getDecodedBody();
				
				if (!empty($accounts['data'])) {
					foreach ($accounts['data'] as $account) {
						$options[] = JHtml::_('select.option', $account['id'], $account['name']);
					}
				}
			} catch (Exception $e) {
				
			}
			
			// Merge any additional options in the XML definition.
			$options = array_merge(parent::getOptions(), $options);
			
			return $options;
		}
	}
}