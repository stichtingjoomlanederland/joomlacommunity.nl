<?php
/**
 * @package    RSForm! Pro
 * @copyright  (c) 2007 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');


class RSFormProQuickFields
{
	public static function getFieldNames($type = 'all')
	{
		static $done;
		static $fieldsets	= array();
		static $all	  		= array();
		static $required 	= array();
		static $hidden	  	= array();
		static $pages		= array();

		if (!$done) {
			// Get field properties first
			$data	= array();
			$mainframe = JFactory::getApplication();
			$formId = JFactory::getApplication()->input->getInt('formId');
			$db 	= JFactory::getDbo();
			$query	= $db->getQuery(true);
			$query->select($db->qn('c.ComponentId'))
			      ->select($db->qn('c.ComponentTypeId'))
			      ->select($db->qn('ct.ComponentTypeName'))
			      ->from($db->qn('#__rsform_components', 'c'))
			      ->join('LEFT', $db->qn('#__rsform_component_types', 'ct') .'ON ('.$db->qn('ct.ComponentTypeId').'='.$db->qn('c.ComponentTypeId').')')
			      ->where($db->qn('c.FormId').'='.$db->q($formId))
			      ->where($db->qn('c.Published').'='.$db->q(1))
			      ->order($db->qn('c.Order').' '.$db->escape('asc'));

			if ($components = $db->setQuery($query)->loadObjectList()) {
				$data = RSFormProHelper::getComponentProperties($components);

				$i = 0;
				foreach ($components as $component) {
					if (!empty($data[$component->ComponentId])) {
						$properties =& $data[$component->ComponentId];
						if (isset($properties['NAME'])) {
							// Populate the 'all' array
							$componentPlaceholders = array(
								'name' => $properties['NAME'],
								'id'   => $component->ComponentTypeId,
								'generate' => array(),
								'display'  => array()
							);
							
							// Add caption placeholders only if captions exist.
							if (isset($properties['CAPTION']))
							{
								$componentPlaceholders['generate'][] = '{' . $properties['NAME'] . ':caption}';
								$componentPlaceholders['display'][] = '{' . $properties['NAME'] . ':caption}';
							}

							// Body placeholder
							$componentPlaceholders['generate'][] = '{' . $properties['NAME'] . ':body}';

							// Add description placeholders only if a description exists.
							if (isset($properties['DESCRIPTION']))
							{
								$componentPlaceholders['generate'][] = '{' . $properties['NAME'] . ':description}';
							}
							
							// Validation placeholder
							$componentPlaceholders['generate'][] = '{' . $properties['NAME'] . ':validation}';

							// Value placeholder
							$componentPlaceholders['display'][] = '{' . $properties['NAME'] . ':value}';

							if ($component->ComponentTypeId == RSFORM_FIELD_FILEUPLOAD) {
								$componentPlaceholders['display'][] = '{' . $properties['NAME'] . ':path}';
								$componentPlaceholders['display'][] = '{' . $properties['NAME'] . ':localpath}';
								$componentPlaceholders['display'][] = '{' . $properties['NAME'] . ':filename}';
							}

							if (isset($properties['ITEMS'])) {
								$componentPlaceholders['display'][] = '{' . $properties['NAME'] . ':text}';
								if (strpos($properties['ITEMS'], '[p') !== false) {
									$componentPlaceholders['display'][] = '{' . $properties['NAME'] . ':price}';
								}
							}

							$mainframe->triggerEvent('rsfp_onAfterCreateQuickAddPlaceholders', array(&$componentPlaceholders, $component->ComponentTypeId));

							$all[] = $componentPlaceholders;

							// check if the field is required
							$isRequired = false;
							$propRequired  = self::getProperty($properties, 'REQUIRED');
							$propImagetype = self::getProperty($properties, 'IMAGETYPE');
							if ($propRequired || // Add required fields
							    (!is_null($propImagetype) && $propImagetype != 'INVISIBLE') || // Add CAPTCHA fields
							    (in_array($component->ComponentTypeId, RSFormProHelper::$captchaFields)) // Add ReCAPTCHA fields
							) {
								$isRequired = true;

								// Populate the 'required' array
								$required[] = $properties['NAME'];
							}

							// Populate the 'hidden' array
							if (isset($properties['LAYOUTHIDDEN']) && $properties['LAYOUTHIDDEN'] == 'YES') {
								$hidden[] = $properties['NAME'];
							}

							// check if the field is a pagebreak
							$pagebreak = false;
							if ($component->ComponentTypeId == RSFORM_FIELD_PAGEBREAK) {
								$pagebreak = true;
							}

							$fieldsets[$i][(($component->ComponentTypeName == 'hidden' || $component->ComponentTypeName == 'ticket') ? 'hidden':'visible')][] = array('data' => $properties, 'required' => $isRequired, 'pagebreak' => $pagebreak);
						}
						if ($component->ComponentTypeId == RSFORM_FIELD_PAGEBREAK) {
							// Populate the 'pages' array
							$pages[] = $properties['NAME'];
							$i++;
						}
					}
				}
			}

			$done = true;
		}

		return $$type;
	}

	public static function getProperty($fieldData, $prop, $default=null)
	{
		// Special case, we no longer use == 'YES' or == 'NO'
		if (isset($fieldData[$prop])) {
			if ($fieldData[$prop] === 'YES') {
				return true;
			} else if ($fieldData[$prop] === 'NO') {
				return false;
			} else {
				return $fieldData[$prop];
			}
		}

		if ($default === 'YES') {
			return true;
		} elseif ($default === 'NO') {
			return false;
		} else {
			return $default;
		}
	}
}