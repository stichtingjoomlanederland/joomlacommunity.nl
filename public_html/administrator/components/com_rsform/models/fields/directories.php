<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldDirectories extends JFormFieldList
{
	protected $type = 'Directories';
	
	protected function getOptions()
    {
        $app        = JFactory::getApplication();
        $db         = JFactory::getDbo();
        $sortColumn = $app->getUserState('com_rsform.forms.filter_order', 'FormId');
        $sortOrder  = $app->getUserState('com_rsform.forms.filter_order_Dir', 'ASC');
        $options    = array();

        $query = $db->getQuery(true)
            ->select($db->qn('FormId'))
            ->select($db->qn('FormTitle'))
            ->select($db->qn('Lang'))
            ->from($db->qn('#__rsform_forms'))
            ->order($db->qn($sortColumn) . ' ' . $db->escape($sortOrder));
        if ($results = $db->setQuery($query)->loadObjectList())
        {
            $query->clear()
                ->select($db->qn('formId'))
                ->from($db->qn('#__rsform_directory'));

            $directories = $db->setQuery($query)->loadColumn();

            foreach ($results as $result)
            {
                $lang = RSFormProHelper::getCurrentLanguage($result->FormId);

                if ($lang != $result->Lang)
                {
                    if ($translations = RSFormProHelper::getTranslations('forms', $result->FormId, $lang))
                    {
                        foreach ($translations as $field => $value)
                        {
                            if (isset($result->$field))
                            {
                                $result->$field = $value;
                            }
                        }
                    }
                }

                $options[] = JHtml::_('select.option', $result->FormId, sprintf('(%d) %s', $result->FormId, $result->FormTitle), 'value', 'text', !in_array($result->FormId, $directories));
            }
        }

		reset($options);
		
		return $options;
	}
}
