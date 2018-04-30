<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2017 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class RSFormProGrid
{
    const FLOW_HORIZONTAL = 0;
    const FLOW_VERTICAL = 1;

	protected $components 	= array();
	protected $pages 		= array();
	protected $rows 		= array();
	protected $hidden 		= array();
	protected $formId;
	protected $formOptions;
	protected $requiredMarker;
	protected $showFormTitle;
	
	public function __construct($data, $formId, $formOptions, $requiredMarker, $showFormTitle)
	{
		$this->formId 			= $formId;
		$this->formOptions 		= $formOptions;
		$this->requiredMarker 	= $requiredMarker;
		$this->showFormTitle 	= $showFormTitle;
		$this->components 		= $this->getComponents();
		
		$data = json_decode($data, true);
		if (is_array($data) && isset($data[0], $data[1]))
		{
			$this->rows   = $data[0];
			$this->hidden = $data[1];
			$this->pages  = array();
			
			// Let's find the pages
			$length = 0;
			$offset = 0;
			foreach ($this->rows as $row_index => $row)
			{
				$length++;
				
				foreach ($row['columns'] as $fields)
				{
					foreach ($fields as $field)
					{
						if (isset($this->components[$field]))
						{
							if (!$this->components[$field]->Published)
							{
								continue;
							}
							
							if ($this->components[$field]->ComponentTypeId == RSFORM_FIELD_PAGEBREAK)
							{
								$this->pages[] = array_slice($this->rows, $offset, $length);
								$length = 0;
								$offset = $row_index + 1;
								continue 3;
							}
						}
					}
				}
			}
			
			$this->pages[] = array_slice($this->rows, $offset, $length);
		}
	}
	
	protected function getComponents()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('p.PropertyValue', 'ComponentName'))
			->select($db->qn('c.ComponentId'))
			->select($db->qn('c.ComponentTypeId'))
			->select($db->qn('c.Published'))
			->select($db->qn('ct.ComponentTypeName'))
			->from($db->qn('#__rsform_properties', 'p'))
			->join('LEFT', $db->qn('#__rsform_components', 'c').' ON ('.$db->qn('c.ComponentId').' = '.$db->qn('p.ComponentId').')')
			->join('LEFT', $db->qn('#__rsform_component_types', 'ct').' ON ('.$db->qn('ct.ComponentTypeId').' = '.$db->qn('c.ComponentTypeId').')')
			->where($db->qn('c.FormId') . ' = ' . $db->q($this->formId))
			->where($db->qn('p.PropertyName') . ' = ' . $db->q('NAME'));
			
		$components = $db->setQuery($query)->loadObjectList('ComponentId');
		
		$data = RSFormProHelper::getComponentProperties($components);
		foreach ($components as $component)
		{
			$component->Required = isset($data[$component->ComponentId], $data[$component->ComponentId]['REQUIRED']) && $data[$component->ComponentId]['REQUIRED'] == 'YES';

            if (in_array($component->ComponentTypeId, RSFormProHelper::$captchaFields))
            {
                // Invisible Captchas should not display a Required Marker
                $component->Required = isset($data[$component->ComponentId]) && in_array('INVISIBLE', $data[$component->ComponentId], true) ? false : true;
            }
		}
		
		return $components;
	}
	
	protected function getBlock($string)
	{
		// Remove any '-' from the string since they will be used as concatenaters
		$str = str_replace('-', ' ', $string);

		// Trim white spaces at beginning and end of alias and make lowercase
		$str = trim(strtolower($str));

		// Remove any duplicate whitespace, and ensure all characters are alphanumeric
		$str = preg_replace('/(\s|[^A-Za-z0-9\-])+/', '-', $str);

		// Trim dashes at beginning and end of alias
		$str = trim($str, '-');

		return $str;
	}
	
	protected function flattenRows()
	{
		$flat = array();

		foreach ($this->rows as $row)
		{
			foreach ($row['columns'] as $column => $fields)
			{
				foreach ($fields as $field)
				{
					$flat[] = $field;
				}
			}
		}
		
		return $flat;
	}
	
	protected function flatten()
	{
		return array_merge($this->flattenRows(), $this->hidden);
	}

    protected function doIf($placeholder, $then)
    {
        return '{if ' . $placeholder . '}' . $then . '{/if}';
    }
}