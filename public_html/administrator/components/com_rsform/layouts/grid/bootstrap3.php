<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2017 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once __DIR__ . '/grid.php';

class RSFormProGridBootstrap3 extends RSFormProGrid
{
	public function generate()
	{
		$html = array();

		// Show title
		if ($this->showFormTitle) {
			$html[] = '<h2>{global:formtitle}</h2>';
		}

		// Error placeholder
		$html[] = '{error}';

		// Start with a page
		foreach ($this->pages as $page_index => $rows)
		{
            $classes = array('formContainer');
            if ($this->formOptions->FormLayoutFlow == static::FLOW_HORIZONTAL)
            {
                $classes[] = 'form-horizontal';
            }

            $html[] = '<!-- Do not remove this ID, it is used to identify the page so that the pagination script can work correctly -->';
            $html[] = '<fieldset class="' . implode(' ', $classes) . '" id="rsform_{global:formid}_page_' . $page_index . '">';
			foreach ($rows as $row_index => $row)
			{
				// Start a new row
				$html[] = "\t".'<div class="row">';

				foreach ($row['columns'] as $column_index => $fields)
				{
					$size = $row['sizes'][$column_index];

					$html[] = "\t"."\t".'<div class="col-sm-' . (int) $size . '">';

					foreach ($fields as $field)
					{
						if (isset($this->components[$field]))
						{
							if (!$this->components[$field]->Published)
							{
								continue;
							}

							$html[] = $this->generateField($this->components[$field]);
						}
					}

					$html[] = "\t"."\t".'</div>';
				}

				$html[] = "\t".'</div>';
			}
			$html[] = '</fieldset>';
		}

		foreach ($this->hidden as $field)
		{
			if (isset($this->components[$field]))
			{
				if (!$this->components[$field]->Published)
				{
					continue;
				}

				$html[] = $this->generateField($this->components[$field]);
			}
		}

		return implode("\n", $html);
	}

	protected function labelClass()
    {
        if ($this->formOptions->FormLayoutFlow == static::FLOW_HORIZONTAL)
        {
            return 'col-sm-3 ';
        }

        return '';
    }

    protected function fieldClass()
    {
        if ($this->formOptions->FormLayoutFlow == static::FLOW_HORIZONTAL)
        {
            return 'col-sm-9 ';
        }

        return '';
    }
	
	protected function generateField($data)
	{
		$html = array();
		
		// Placeholders
		$placeholders = array(
			'body' 		 	=> '{' . $data->ComponentName . ':body}',
			'caption'	 	=> '{' . $data->ComponentName . ':caption}',
			'description' 	=> '{' . $data->ComponentName . ':description}',
			'error' 	 	=> '{' . $data->ComponentName . ':errorClass}',
			'validation' 	=> '{' . $data->ComponentName . ':validation}',
		);
		
		// Some fields should span the entire width
		if ($data->ComponentTypeId == RSFORM_FIELD_FREETEXT)
		{
			$block = $this->getBlock($data->ComponentName);

			$html[] = "\t"."\t"."\t".'<div class="rsform-block rsform-block-' . $block . $placeholders['error'] . '">';
			$html[] = "\t"."\t"."\t"."\t"."\t".$placeholders['body'];
			$html[] = "\t"."\t"."\t".'</div>';
		}
		elseif (in_array($data->ComponentTypeId, array(RSFORM_FIELD_HIDDEN, RSFORM_FIELD_TICKET)))
		{
			$html[] = "\t"."\t"."\t"."\t"."\t".$placeholders['body'];
		}
		else
		{
			$block = $this->getBlock($data->ComponentName);
			
			$html[] = "\t"."\t"."\t".'<div class="form-group rsform-block rsform-block-' . $block . $placeholders['error'] . '">';
				if ($data->ComponentTypeId != RSFORM_FIELD_PAGEBREAK)
				{
					$label = "\t"."\t"."\t"."\t".'<label class="' . $this->labelClass() . 'control-label formControlLabel" data-toggle="tooltip" title="' . $placeholders['description'] . '"';
					if (!in_array($data->ComponentTypeId, array(RSFORM_FIELD_CHECKBOXGROUP, RSFORM_FIELD_RADIOGROUP, RSFORM_FIELD_BIRTHDAY)))
					{
						$label .= ' for="' . $data->ComponentName . '"';
					}
					$label .= '>';
					$label .= $placeholders['caption'];
					if ($data->Required && $this->requiredMarker)
					{
						$label .= '<strong class="formRequired">' . $this->requiredMarker . '</strong>';
					}
					$label .= '</label>';
					$html[] = $label;
				}
				else
				{
					$html[] = "\t"."\t"."\t"."\t".'<div class="' . $this->labelClass() .  'control-label formControlLabel"></div>';
				}
				
				$html[] = "\t"."\t"."\t"."\t".'<div class="' . $this->fieldClass() .  'formControls' . ($data->ComponentTypeId == RSFORM_FIELD_PAGEBREAK ? ' btn-group' : '') . '">';
					$html[] = "\t"."\t"."\t"."\t"."\t".$placeholders['body'];
                    if ($data->ComponentTypeId != RSFORM_FIELD_PAGEBREAK)
					{
						$html[]	= "\t"."\t"."\t"."\t"."\t".'<span class="help-block formValidation">' . $placeholders['validation'] . '</span>';
					}
				$html[] = "\t"."\t"."\t"."\t".'</div>';
			$html[] = "\t"."\t"."\t".'</div>';
		}
		
		// If it's a CAPTCHA field and the removing CAPTCHA is enabled, must wrap it in {if}
		if (in_array($data->ComponentTypeId, RSFormProHelper::$captchaFields) && !empty($this->formOptions->RemoveCaptchaLogged)) {
			array_unshift($html, '{if {global:userid} == "0"}');
			array_push($html, '{/if}');
		}
		
		return implode("\n", $html);
	}
}