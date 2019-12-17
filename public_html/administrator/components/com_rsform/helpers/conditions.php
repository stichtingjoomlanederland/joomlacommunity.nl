<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2019 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

class RSFormProConditions
{
	protected static function getFields($formId, $published = 1)
	{
		static $cache = array();
		
		if (!isset($cache[$formId]))
		{
			$cache[$formId] = array();
			
			$db 	= JFactory::getDbo();
			$query 	= $db->getQuery(true);
			
			$query->select($db->qn('p.PropertyValue', 'ComponentName'))
				->select($db->qn('p.ComponentId'))
				->from($db->qn('#__rsform_components', 'c'))
				->join('LEFT', $db->qn('#__rsform_properties', 'p') . ' ON (' . $db->qn('c.ComponentId') . '=' . $db->qn('p.ComponentId') . ')')
				->where($db->qn('c.FormId') . '=' . $db->q($formId))
				->where($db->qn('p.PropertyName') . '=' . $db->q('NAME'))
				->order($db->qn('c.Order') . ' ' . $db->escape('ASC'));

			if ($published !== null)
			{
				$query->where($db->qn('c.Published') . ' = ' . $db->q($published));
			}

			$cache[$formId] = $db->setQuery($query)->loadObjectList('ComponentId');
		}
		
		return $cache[$formId];
	}

	public static function parseComponentIds($value)
	{
		if ((int) $value)
		{
			return array((int) $value);
		}
		elseif (is_string($value))
		{
			$tmp_ids = json_decode($value);
			if (is_array($tmp_ids))
			{
				return $tmp_ids;
			}
		}

		return array();
	}
	
    public static function getConditions($formId, $lang = null, $published = 1)
    {
        if ($lang === null)
        {
            $lang = RSFormProHelper::getCurrentLanguage();
        }

		if (RSFormProHelper::getConfig('global.disable_multilanguage'))
		{
			$lang = RSFormProHelper::getConfig('global.default_language');
		}

		$fields = self::getFields($formId, $published);
        $db 	= JFactory::getDbo();

        $query = $db->getQuery(true)
			->select('*')
            ->from($db->qn('#__rsform_conditions'))
            ->where($db->qn('form_id') . ' = ' . $db->q($formId))
            ->where($db->qn('lang_code') . ' = ' . $db->q($lang))
            ->order($db->qn('id') . ' ' . $db->escape('ASC'));

        if ($conditions = $db->setQuery($query)->loadObjectList())
        {
            // put them all in an array so we can use only one query
            $cids = array();
            foreach ($conditions as $condition)
            {
            	$condition->component_id = self::parseComponentIds($condition->component_id);
            	$condition->ComponentNames = array();

            	foreach ($condition->component_id as $component_id)
				{
					if (!isset($fields[$component_id]))
					{
						continue;
					}

					$condition->ComponentNames[] = $fields[$component_id]->ComponentName;
				}

				$cids[] = $condition->id;
            }

            if ($cids)
            {
				$query->clear()
					->select('*')
					->from($db->qn('#__rsform_condition_details'))
					->where($db->qn('condition_id') . ' IN (' . implode(',', $db->q($cids)) . ')');

				if ($details = $db->setQuery($query)->loadObjectList())
				{
					// arrange details within conditions
					foreach ($conditions as $condition)
					{
						$condition->details = array();
						foreach ($details as $detail)
						{
							if ($detail->condition_id != $condition->id || !isset($fields[$detail->component_id]))
							{
								continue;
							}

							$detail->ComponentName = $fields[$detail->component_id]->ComponentName;

							$condition->details[] = $detail;
						}
					}
				}
            }

            // all done
            return $conditions;
        }

        // nothing found
        return false;
    }

    public static function buildJS($formId, $conditions)
    {
    	$script = '';

        if ($conditions)
        {
			$functions = array();

            foreach ($conditions as $condition)
            {
                if ($condition->details)
                {
                    // Create an object clone
                    $data = clone $condition;

                    // Remove unneeded data
                    unset($data->lang_code, $data->id, $data->component_id);

                    // This is our function name
                    $functions[] = $function = 'rsfp_runCondition' . $condition->id;

                    // Add condition events
                    $uniques = array();
                    $scriptConditions = '';
                    foreach ($data->details as $detail)
                    {
                        // Remove unneeded data
                        unset($detail->id, $detail->condition_id, $detail->component_id);

                        // Run script just once
                        if (!in_array($detail->ComponentName, $uniques))
                        {
                            $scriptConditions .= sprintf('rsfp_addCondition(%1$d, \'%2$s\', %3$s);', $formId, addslashes($detail->ComponentName), $function);

                            $uniques[] = $detail->ComponentName;
                        }
                    }

                    // The script we're outputting
                    $script .= sprintf('function %1$s(){RSFormPro.Conditions.run(%2$s);}', $function, json_encode($data));

                    $script .= $scriptConditions;
                }
            }

			if ($functions)
			{
				// Open script tag
				$script = '<script type="text/javascript">' . $script;

				$script .= sprintf('function rsfp_runAllConditions%1$d(){%2$s};RSFormPro.Conditions.delayRun(%1$d);', $formId, implode('();', $functions) . '();');
				$script .= sprintf('RSFormPro.Conditions.addReset(%d);', $formId);

				// Close script tag
				$script .= '</script>';
			}
        }

        return $script;
    }
}