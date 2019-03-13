<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2019 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

class RSFormProConditions
{
    public static function getConditions($formId, $lang = null)
    {
        if ($lang === null)
        {
            $lang = RSFormProHelper::getCurrentLanguage();
        }

        $db = JFactory::getDbo();

        $query = $db->getQuery(true)
            ->select('c.*')
            ->select($db->qn('p.PropertyValue', 'ComponentName'))
            ->from($db->qn('#__rsform_conditions', 'c'))
            ->leftJoin($db->qn('#__rsform_properties', 'p') . ' ON (' . $db->qn('c.component_id') . ' = ' . $db->qn('p.ComponentId') . ')')
            ->leftJoin($db->qn('#__rsform_components', 'comp') . ' ON (' . $db->qn('comp.ComponentId') . ' = ' . $db->qn('p.ComponentId') . ')')
            ->where($db->qn('c.form_id') . ' = ' . $db->q($formId))
            ->where($db->qn('c.lang_code') . ' = ' . $db->q($lang))
            ->where($db->qn('comp.Published') . ' = ' . $db->q(1))
            ->where($db->qn('p.PropertyName') . ' = ' . $db->q('NAME'))
            ->order($db->qn('c.id') . ' ' . $db->escape('ASC'));

        if ($conditions = $db->setQuery($query)->loadObjectList())
        {
            // put them all in an array so we can use only one query
            $cids = array();
            foreach ($conditions as $condition)
            {
                $cids[] = $condition->id;
            }

            $query->clear()
                ->select('d.*')
                ->select($db->qn('p.PropertyValue', 'ComponentName'))
                ->from($db->qn('#__rsform_condition_details', 'd'))
                ->leftJoin($db->qn('#__rsform_properties', 'p') . ' ON (' . $db->qn('d.component_id') . ' = ' . $db->qn('p.ComponentId') . ')')
                ->leftJoin($db->qn('#__rsform_components', 'comp') . ' ON (' . $db->qn('comp.ComponentId') . ' = ' . $db->qn('p.ComponentId') . ')')
                ->where($db->qn('comp.Published') . ' = ' . $db->q(1))
                ->where($db->qn('p.PropertyName') . ' = ' . $db->q('NAME'));

            if ($cids)
            {
                $query->where($db->qn('d.condition_id') . ' IN (' . implode(',', $db->q($cids)) . ')');
            }

            $details = $db->setQuery($query)->loadObjectList();

            // arrange details within conditions
            foreach ($conditions as $condition)
            {
                $condition->details = array();
                foreach ($details as $detail)
                {
                    if ($detail->condition_id != $condition->id)
                    {
                        continue;
                    }
                    $condition->details[] = $detail;
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
        // Open script tag
        $script = '<script type="text/javascript">';

        $functions = array();

        if ($conditions)
        {
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
        }

        $script .= sprintf('function rsfp_runAllConditions%1$d(){%2$s};rsfp_runAllConditions%1$d();', $formId, implode('();', $functions) . '();');
        $script .= sprintf('RSFormPro.Conditions.addReset(%d);', $formId);

        // Close script tag
        $script .= '</script>';

        return $script;
    }
}