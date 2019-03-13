<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormProRestoreForm
{
	// JDatabase instance
	protected $db;
	
	// Holds the form's structure as a JTable object
	protected $form;
	
	// Holds an array of the XML data.
	protected $xml;
	
	// Holds an array in the form of field ID => field name.
	protected $fields;
	
	// Holds the setting for keeping the form's ids from the backup
	protected $keepId;
	
	// Meta data information
	protected $metaData;
	
	public function __construct($options = array()) {
		$this->keepId		= !empty($options['keepId']) ? true : false;
		$path 	  			= &$options['path'];
		$this->db 			= JFactory::getDbo();
		
		// Check if the form's xml exists
		if (!file_exists($path)) {
			throw new Exception(sprintf('The file %s does not exist!', $path));
		}
		
		if (!is_readable($path)) {
			throw new Exception(sprintf('File %s is not readable!', $path));
		}
		
		// Attempt to load the XML data
		libxml_use_internal_errors(true);
		
		if (class_exists('DOMDocument')) {
			$dom = new DOMDocument('1.0', 'UTF-8');
			$dom->strictErrorChecking = false;
			$dom->validateOnParse = false;
			$dom->recover = true;
			$dom->loadXML(file_get_contents($options['path']));
			
			$this->xml = simplexml_import_dom($dom);
		} else {
			$this->xml = simplexml_load_file($options['path']);
		}
		
		if ($this->xml === false) {
			$errors = array();
			foreach (libxml_get_errors() as $error) {
				$errors[] = 'Message: '.$error->message.'; Line: '.$error->line.'; Column: '.$error->column;
			}
			throw new Exception(sprintf('Error while parsing XML: %s<br/>', implode('<br />', $errors)));
		}
		
		$this->metaData = $options['metaData'];
	}
	
	public function restore() {
		$this->restoreStructure();
		$this->restoreFields();
		$this->restoreCalculations();
		$this->restorePost();
		$this->restoreConditions();
		$this->restoreDirectory();
		$this->restoreEmails();
		$this->restoreMappings();
		$this->rebuildCalendarsValidationRules();
		$this->rebuildGridLayout();
		
		// Allow plugins to restore their own data from the backup.
		JFactory::getApplication()->triggerEvent('rsfp_onFormRestore', array($this->form, $this->xml, $this->fields));
	}
	
	public function getFormId() {
		return $this->form->FormId;
	}
	
	// Form structure
	// ==============
	
	protected function restoreStructure() {
		// Restore the form structure #__rsform_forms
		$data 			= array();
		$oldFormId 		= false;
		foreach ($this->xml->structure->children() as $property => $value) {
			// Skip translations for now
            // Skip ThemeParams, no longer exists
			if ($property == 'translations' || $property == 'ThemeParams') {
				continue;
			}
			
			if ($property == 'FormId') {
				$oldFormId = (string) $value;
				continue;
			}
			
			$data[$property] = (string) $value;
		}
		
		$this->form = JTable::getInstance('RSForm_Forms', 'Table');
		
		if ($this->keepId && $oldFormId) {
			if (!$this->form->load($oldFormId)) {
				$query = $this->db->getQuery(true);
				$query	->insert('#__rsform_forms')
						->set($this->db->qn('FormId') .'='. $this->db->q($oldFormId));
				$this->db->setQuery($query)->execute();
				
				$data['FormId'] = $oldFormId;
			}
			
			// Reset it back
			$this->form = JTable::getInstance('RSForm_Forms', 'Table');
		}
		
		// Responsive layout needs its own CSS to be loaded, make sure old forms still load it when restored.
		if (version_compare($this->metaData['version'], '1.51.12', '<')) {
			if ($data['FormLayoutName'] == 'responsive') {
				$data['LoadFormLayoutFramework'] = 1;
			}
		}
		
		if (!$this->form->save($data)) {
			throw new Exception(sprintf('Form %s could not be saved!', $this->form->FormTitle));
		}
		
		// Restore form translations
		if ($this->xml->structure->translations) {
			foreach ($this->xml->structure->translations->children() as $lang_code => $properties) {
				foreach ($properties->children() as $property => $value) {
					$query = $this->db->getQuery(true);
					$query	->insert('#__rsform_translations')
							->set(array(
									$this->db->qn('form_id')		.'='.$this->db->q($this->form->FormId),
									$this->db->qn('lang_code')		.'='.$this->db->q((string) $lang_code),
									$this->db->qn('reference')		.'='.$this->db->q('forms'),
									$this->db->qn('reference_id')	.'='.$this->db->q((string) $property),
									$this->db->qn('value')			.'='.$this->db->q((string) $value)
							));
					$this->db->setQuery($query)->execute();
				}
			}
		}
	}
	
	// Fields
	// ======
	
	protected function restoreFields() {
		// Restore the form fields #__rsform_components
		if (isset($this->xml->fields)) {
			foreach ($this->xml->fields->children() as $field) {
				$query = $this->db->getQuery(true);
				$componentTypeId = (string) $field->ComponentTypeId;
				// change fieldType if needed
				$changedField = '';
				if ($componentTypeId == '12') {
					$componentTypeId = '13';
					$changedField = 'imageButton';
				}
				
				$query	->insert('#__rsform_components')
						->set(array(
								$this->db->qn('FormId')				.'='.$this->db->q($this->form->FormId),
								$this->db->qn('ComponentTypeId')	.'='.$this->db->q($componentTypeId),
								$this->db->qn('Order')				.'='.$this->db->q((string) $field->Order),
								$this->db->qn('Published')			.'='.$this->db->q((string) $field->Published)
						));
				$this->db->setQuery($query)->execute();
				$componentId = $this->db->insertid();
				
				// we use the switch statement for further field types changes - at the moment we only need it for the image button
				$referenceProperties = array();
				if (!empty($changedField)) {
					switch ($changedField) {
						case 'imageButton':
							$this->db->setQuery("SELECT `FieldName` FROM `#__rsform_component_type_fields` WHERE `ComponentTypeId` = 13 ");
							$referenceProperties = $this->db->loadColumn();
						break;
					}
				}
				
				
				if (isset($field->properties)) {
					$newProperties = array();
					foreach ($field->properties->children() as $property => $value) {
						$property = (string) $property;
						$value = (string) $value;
						
						if (!isset($newProperties[$componentId])) {
							$newProperties[$componentId] = array();
						}
						
						if (!empty($changedField)) {
							switch ($changedField) {
								case 'imageButton':
									if (in_array($property, $referenceProperties)) {
										if ($property == 'ADDITIONALATTRIBUTES' && isset($newProperties[$componentId]['ADDITIONALATTRIBUTES'])) {
											$newProperties[$componentId]['ADDITIONALATTRIBUTES'] = $value."\r\n".$newProperties[$componentId]['ADDITIONALATTRIBUTES'];	
										} else {
											$newProperties[$componentId][$property] = $value;
										}
									} else if ($property == 'IMAGEBUTTON' && !empty($value)) {
										$additional = 'type="image"'."\r\n".'src="'.$value.'"';
										if (isset($newProperties[$componentId]['ADDITIONALATTRIBUTES']) && !empty($newProperties[$componentId]['ADDITIONALATTRIBUTES'])) {
											$additional = $newProperties[$componentId]['ADDITIONALATTRIBUTES']."\r\n".$additional;
										}
										$newProperties[$componentId]['ADDITIONALATTRIBUTES'] = $additional;
									}
								break;
							}
						} else  {
							$newProperties[$componentId][$property] = $value;
						}
					}
					
					// add the submit button extra properties
					if (!empty($changedField)) {
						switch ($changedField) {
							case 'imageButton':
								foreach ($newProperties as $CompId => $property) {
									foreach ($referenceProperties as $referenceProperty) {
										$value = '';
										switch ($referenceProperty) {
											case 'DISPLAYPROGRESS':
												$value = 'NO';
											break;
											case 'BUTTONTYPE':
												$value = 'TYPEINPUT';
											break;
											case 'DISPLAYPROGRESSMSG':
												$value = '<div>'."\r\n".' <p><em>Page <strong>{page}</strong> of {total}</em></p>'."\r\n".' <div class="rsformProgressContainer">'."\r\n".'  <div class="rsformProgressBar" style="width: {percent}%;"></div>'."\r\n".' </div>'."\r\n".'</div>';
											break;
										}
										
										if (!empty($value)) {
											$newProperties[$CompId][$referenceProperty] = $value;
										}
									}
								}
							break;
						}
					}
				
					
					foreach ($newProperties as $CompId => $property) {
						foreach ($property as $propertyName => $propertyValue) {
							$query = $this->db->getQuery(true);
							$query	->insert('#__rsform_properties')
									->set(array(
											$this->db->qn('ComponentId')	.'='.$this->db->q($CompId),
											$this->db->qn('PropertyName')	.'='.$this->db->q($propertyName),
											$this->db->qn('PropertyValue')	.'='.$this->db->q($propertyValue)
									));
							$this->db->setQuery($query)->execute();
							// store the ComponentId
							if ((string) $propertyName == 'NAME') {
								$this->fields[(string) $propertyValue] = $CompId;
							}
						}
					}
				}
				if (isset($field->translations)) {
					foreach ($field->translations->children() as $lang_code => $properties) {
						foreach ($properties->children() as $property => $value) {
							$query = $this->db->getQuery(true);
							$reference_id = $componentId.'.'.(string) $property;
							$query	->insert('#__rsform_translations')
									->set(array(
											$this->db->qn('form_id')		.'='.$this->db->q($this->form->FormId),
											$this->db->qn('lang_code')		.'='.$this->db->q((string) $lang_code),
											$this->db->qn('reference')		.'='.$this->db->q('properties'),
											$this->db->qn('reference_id')	.'='.$this->db->q($reference_id),
											$this->db->qn('value')			.'='.$this->db->q((string) $value)
									));
							$this->db->setQuery($query)->execute();
						}
					}
				}
			}
		}
	}
	
	protected function rebuildCalendarsValidationRules() {
		$db 	= &$this->db;
		$query 	= $db->getQuery(true);
		
		$query->clear()
			  ->select('c.ComponentId')
			  ->select('p.PropertyName')
			  ->select('p.PropertyValue')
			  ->from($db->qn('#__rsform_components', 'c'))
			  ->join('LEFT', $db->qn('#__rsform_properties', 'p') . ' ON (' . $db->qn('c.ComponentId') . ' = ' . $db->qn('p.ComponentId') . ')')
			  ->where($db->qn('c.FormId').' = '.$db->q($this->form->FormId))
			  ->where($db->qn('c.ComponentTypeId').' IN ('.$db->q(6).', '.$db->q(411).')')
			  ->where('('.$db->qn('p.PropertyName').' = '.$db->q('NAME').' OR '.$db->qn('p.PropertyName').' = '.$db->q('VALIDATIONCALENDAR').')');
		$db->setQuery($query);
		$formCalendarsComponents = $db->loadObjectList();
		
		$componentsNames = array();
		$componentsValidations = array();
		
		foreach ($formCalendarsComponents as $calendar) {
			if ($calendar->PropertyName == 'NAME') {
				$componentsNames[$calendar->PropertyValue] = $calendar->ComponentId;
			}
			
			if ($calendar->PropertyName == 'VALIDATIONCALENDAR') {
				$componentsValidations[$calendar->ComponentId] = $calendar->PropertyValue;
			}
		}
		
		foreach ($componentsValidations as $componentId => $value) {
			if (!empty($value)) {
				$ruleParts = explode(' ', $value, 2);
				$rule = $ruleParts[0];
				$otherComponentName = $ruleParts[1];
				
				$idOtherComponent = $componentsNames[$otherComponentName];
				$ruleParts[1] = $idOtherComponent; // replace the name with the id
				
				$newRule = implode(' ', $ruleParts);
				
				$query->clear()
						->update('#__rsform_properties')
						->set(array(
								$this->db->qn('PropertyValue').'='.$this->db->q($newRule)
						))
						->where($db->qn('ComponentId').' = '.$db->q($componentId))
						->where($db->qn('PropertyName').' = '.$db->q('VALIDATIONCALENDAR'));
				$this->db->setQuery($query)->execute();
			}
		}
	}
	
	// Calculations
	// ============
	
	protected function restoreCalculations() {
		// Restore Calculations #__rsform_calculations
		if (isset($this->xml->calculations)) {
			foreach ($this->xml->calculations->children() as $calculation) {
				$query = $this->db->getQuery(true);
				$query	->insert('#__rsform_calculations')
						->set(array(
								$this->db->qn('formId')		.'='.$this->db->q($this->form->FormId),
								$this->db->qn('total')		.'='.$this->db->q((string) $calculation->total),
								$this->db->qn('expression')	.'='.$this->db->q((string) $calculation->expression),
								$this->db->qn('ordering')	.'='.$this->db->q((string) $calculation->ordering)
						));
				$this->db->setQuery($query)->execute();
			}
		}
	}
	
	// Post
	// ====
	
	protected function restorePost() {
		// Restore Post to Location #__rsform_posts
		if (isset($this->xml->post)) {
			foreach ($this->xml->post as $post) {
				// Some older versions might have left some data here due to a bug, must delete it first.
				$query = $this->db->getQuery(true);
				$query->delete('#__rsform_posts')
					  ->where($this->db->qn('form_id').' = '.$this->db->q($this->form->FormId));
				$this->db->setQuery($query)->execute();
				
				$query = $this->db->getQuery(true);
				$query	->insert('#__rsform_posts')
						->set(array(
								$this->db->qn('form_id').'='.$this->db->q($this->form->FormId),
								$this->db->qn('enabled').'='.$this->db->q((string) $post->enabled),
								$this->db->qn('method')	.'='.$this->db->q((string) $post->method),
								$this->db->qn('fields')	.'='.$this->db->q((string) $post->fields),
								$this->db->qn('silent')	.'='.$this->db->q((string) $post->silent),
								$this->db->qn('url')	.'='.$this->db->q((string) $post->url)
						));
				$this->db->setQuery($query)->execute();
			}
		}
	}
	
	// Conditions
	// ==========
	
	protected function restoreConditions() {
		// Restore conditions #__rsform_conditions & #__rsform_condition_details
		if (isset($this->xml->conditions)) {
			foreach ($this->xml->conditions->children() as $condition) {
				if (empty($this->fields[(string) $condition->component_id])) {
					continue;
				}
				$query = $this->db->getQuery(true);
				$query	->insert('#__rsform_conditions')
						->set(array(
								$this->db->qn('form_id')		.'='.$this->db->q($this->form->FormId),
								$this->db->qn('action')			.'='.$this->db->q((string) $condition->action),
								$this->db->qn('block')			.'='.$this->db->q((string) $condition->block),
								$this->db->qn('component_id')	.'='.$this->db->q($this->fields[(string) $condition->component_id]),
								$this->db->qn('condition')		.'='.$this->db->q((string) $condition->condition),
								$this->db->qn('lang_code')		.'='.$this->db->q((string) $condition->lang_code)
						));
				$this->db->setQuery($query)->execute();
				$conditionId = $this->db->insertid();
				
				if (isset($condition->details)) {
					foreach ($condition->details->children() as $detail) {
						$query = $this->db->getQuery(true);
						$query	->insert('#__rsform_condition_details')
								->set(array(
										$this->db->qn('condition_id')	.'='.$this->db->q($conditionId),
										$this->db->qn('component_id')	.'='.$this->db->q($this->fields[(string) $detail->component_id]),
										$this->db->qn('operator')		.'='.$this->db->q((string) $detail->operator),
										$this->db->qn('value')			.'='.$this->db->q((string) $detail->value)
								));
						$this->db->setQuery($query)->execute();
						
					}
				}
			}
		}
	}
	
	// Directory
	// =========
	
	protected function restoreDirectory() {
		// Restore directory #__rsform_directory & #__rsform_directory_fields
		if (isset($this->xml->directory)) {
			foreach ($this->xml->directory as $directory) {				
				$query = $this->db->getQuery(true);
				$query	->insert('#__rsform_directory')
						->set(array(
								$this->db->qn('formId')					.'='.$this->db->q($this->form->FormId),
								$this->db->qn('filename')				.'='.$this->db->q((string) $directory->filename),
								$this->db->qn('enablepdf')				.'='.$this->db->q((string) $directory->enablepdf),
								$this->db->qn('enablecsv')				.'='.$this->db->q((string) $directory->enablecsv),
								$this->db->qn('ViewLayout')				.'='.$this->db->q((string) $directory->ViewLayout),
								$this->db->qn('ViewLayoutName')			.'='.$this->db->q((string) $directory->ViewLayoutName),
								$this->db->qn('ViewLayoutAutogenerate')	.'='.$this->db->q((string) $directory->ViewLayoutAutogenerate),
								$this->db->qn('CSS')					.'='.$this->db->q((string) $directory->CSS),
								$this->db->qn('JS')						.'='.$this->db->q((string) $directory->JS),
								$this->db->qn('ListScript')				.'='.$this->db->q((string) $directory->ListScript),
								$this->db->qn('DetailsScript')			.'='.$this->db->q((string) $directory->DetailsScript),
								$this->db->qn('EmailsScript')			.'='.$this->db->q((string) $directory->EmailsScript),
								$this->db->qn('EmailsCreatedScript')	.'='.$this->db->q((string) $directory->EmailsCreatedScript),
								$this->db->qn('groups')					.'='.$this->db->q((string) $directory->groups),
								$this->db->qn('DeletionGroups')			.'='.$this->db->q((string) $directory->DeletionGroups),
						));
				$this->db->setQuery($query)->execute();
				
				if (isset($directory->fields)) {
					foreach ($directory->fields->children() as $field) {
						// check for the component ID
						$componentId = (string) $field->componentId;
						
						if (isset($this->fields[$componentId])) {
							$componentId = $this->fields[$componentId];
						}
						
						$componentId = (int) $componentId;
						
						if (is_int($componentId)) {
							$query = $this->db->getQuery(true);
							$query	->insert('#__rsform_directory_fields')
									->set(array(
											$this->db->qn('formId')			.'='.$this->db->q($this->form->FormId),
											$this->db->qn('componentId')	.'='.$this->db->q($componentId),
											$this->db->qn('viewable')		.'='.$this->db->q((string) $field->viewable),
											$this->db->qn('searchable')		.'='.$this->db->q((string) $field->searchable),
											$this->db->qn('editable')		.'='.$this->db->q((string) $field->editable),
											$this->db->qn('indetails')		.'='.$this->db->q((string) $field->indetails),
											$this->db->qn('incsv')			.'='.$this->db->q((string) $field->incsv),
											$this->db->qn('ordering')		.'='.$this->db->q((string) $field->ordering)
									));
							$this->db->setQuery($query)->execute();
						}
					}
				}
			}
		}
	}
	
	// Emails
	// ======
	
	protected function restoreEmails() {
		// Restore Emails #__rsform_emails
		if (isset($this->xml->emails)) {
			foreach ($this->xml->emails->children() as $email) {
				$query = $this->db->getQuery(true);
				$query	->insert('#__rsform_emails')
						->set(array(
								$this->db->qn('formId')		.'='.$this->db->q($this->form->FormId),
								$this->db->qn('type')		.'='.$this->db->q((string) $email->type),
								$this->db->qn('from')		.'='.$this->db->q((string) $email->from),
								$this->db->qn('fromname')	.'='.$this->db->q((string) $email->fromname),
								$this->db->qn('replyto')	.'='.$this->db->q((string) $email->replyto),
								$this->db->qn('to')			.'='.$this->db->q((string) $email->to),
								$this->db->qn('cc')			.'='.$this->db->q((string) $email->cc),
								$this->db->qn('bcc')		.'='.$this->db->q((string) $email->bcc),
								$this->db->qn('subject')	.'='.$this->db->q((string) $email->subject),
								$this->db->qn('mode')		.'='.$this->db->q((string) $email->mode),
								$this->db->qn('message')	.'='.$this->db->q((string) $email->message)
						));
				$this->db->setQuery($query)->execute();
				
				if (isset($email->translations)) {
					foreach ($email->translations->children() as $lang_code => $properties) {
						foreach ($properties->children() as $property => $value) {
							$query = $this->db->getQuery(true);
							$query	->insert('#__rsform_translations')
									->set(array(
											$this->db->qn('form_id')		.'='.$this->db->q($this->form->FormId),
											$this->db->qn('lang_code')		.'='.$this->db->q((string) $lang_code),
											$this->db->qn('reference')		.'='.$this->db->q('emails'),
											$this->db->qn('reference_id')	.'='.$this->db->q((string) $property),
											$this->db->qn('value')			.'='.$this->db->q((string) $value)
									));
							$this->db->setQuery($query)->execute();
						}
					}
				}
			}
		}
	}
	
	// Mappings
	// ========
	
	protected function restoreMappings() {
		// Restore Mappings #__rsform_mappings
		if (isset($this->xml->mappings)) {
			foreach ($this->xml->mappings->children() as $mapping) {
				$driver = (string) $mapping->driver;
				if (empty($driver)) {
					$driver = JFactory::getConfig()->get('dbtype');
				}
				$query = $this->db->getQuery(true);
				$query	->insert('#__rsform_mappings')
						->set(array(
								$this->db->qn('formId')		.'='.$this->db->q($this->form->FormId),
								$this->db->qn('connection')	.'='.$this->db->q((string) $mapping->connection),
								$this->db->qn('host')		.'='.$this->db->q((string) $mapping->host),
								$this->db->qn('port')		.'='.$this->db->q((string) $mapping->port),
								$this->db->qn('driver')		.'='.$this->db->q($driver),
								$this->db->qn('username')	.'='.$this->db->q((string) $mapping->username),
								$this->db->qn('password')	.'='.$this->db->q((string) $mapping->password),
								$this->db->qn('database')	.'='.$this->db->q((string) $mapping->database),
								$this->db->qn('method')		.'='.$this->db->q((string) $mapping->method),
								$this->db->qn('table')		.'='.$this->db->q((string) $mapping->table),
								$this->db->qn('data')		.'='.$this->db->q((string) $mapping->data),
								$this->db->qn('wheredata')	.'='.$this->db->q((string) $mapping->wheredata),
								$this->db->qn('extra')		.'='.$this->db->q((string) $mapping->extra),
								$this->db->qn('andor')		.'='.$this->db->q((string) $mapping->andor),
								$this->db->qn('ordering')	.'='.$this->db->q((string) $mapping->ordering),
						));
				$this->db->setQuery($query)->execute();
			}
		}
	}

	// Grid Layout
    // ===========

	protected function rebuildGridLayout()
    {
        if (empty($this->form->GridLayout))
        {
            return false;
        }

        $data   = json_decode($this->form->GridLayout, true);
        $rows 	= array();
        $hidden	= array();

        // If decoding is successful, we should have $rows and $hidden
        if (is_array($data) && isset($data[0], $data[1]))
        {
            $rows 	= $data[0];
            $hidden = $data[1];
        }

        if ($rows)
        {
            foreach ($rows as $row_index => &$row)
            {
                foreach ($row['columns'] as $column_index => $fields)
                {
                    foreach ($fields as $position => $id)
                    {
                        if (isset($this->fields[$id]))
                        {
                            $row['columns'][$column_index][$position] = $this->fields[$id];
                        }
                        else
                        {
                            // Field doesn't exist, remove it from grid
                            unset($row['columns'][$column_index][$position]);
                        }
                    }
                }
            }
			unset($row);
        }

        if ($hidden)
        {
            foreach ($hidden as $hidden_index => $id)
            {
                if (isset($this->fields[$id]))
                {
                    $hidden[$hidden_index] = $this->fields[$id];
                }
                else
                {
                    // Field doesn't exist, remove it from grid
                    unset($hidden[$hidden_index]);
                }
            }
        }

        $query = $this->db->getQuery(true);
        $query->update('#__rsform_forms')
            ->set($this->db->qn('GridLayout') .'='. $this->db->q(json_encode(array($rows, $hidden))))
            ->where($this->db->qn('FormId') .'='. $this->db->q($this->form->FormId));
        $this->db->setQuery($query)->execute();
    }
}