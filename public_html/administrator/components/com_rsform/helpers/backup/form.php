<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__).'/helper.php';

class RSFormProBackupForm
{
	// JDatabase instance
	protected $db;
	
	// Holds the form.
	protected $form;
	
	// Holds an array in the form of field ID => field name.
	protected $fields;
	
	// The path to the backup.
	protected $path;
	
	// The XML Writer object
	protected $xml;

	// Holds the Grid Layout so we can process it after fields have been added
	protected $GridLayout;
	
	public function __construct($options = array()) {
		require_once dirname(__FILE__).'/xml.php';
		
		$this->db	= JFactory::getDbo();
		$this->xml	= new RSFormProBackupXML;
		
		// Load the form
		$query = $this->db->getQuery(true);
		$query->select('*')
			  ->from($this->db->qn('#__rsform_forms'))
			  ->where($this->db->qn('FormId').'='.$this->db->q($options['form']));
		$this->form = $this->db->setQuery($query)->loadObject();
		
		if (!$this->form) {
			throw new Exception(sprintf('Form #%d could not be loaded from the database!', $options['form']));
		}
		
		// Assign the path
		$this->path = $options['path'];
	}
	
	public function store() {
		// Add XML header
		$this->xml->addHeader();
		
		// Add <form> tag
		$this->xml->add('form');

		$this->storeStructure();
		$this->storeFields();
		$this->storeCalculations();
		$this->storePost();
		$this->storeConditions();
		$this->storeDirectory();
		$this->storeEmails();
		$this->storeMappings();
		$this->storeGridLayout();
		
		// Allow plugins to add their own data to the backup.
		JFactory::getApplication()->triggerEvent('rsfp_onFormBackup', array($this->form, $this->xml, $this->fields));
		
		// Close <form> tag
		$this->xml->add('/form');
		
		$archive = new RSFormProTar($this->path);
		$buffer = (string) $this->xml;
		$size	= strlen($buffer);
		
		$archive->addHeader($size, RSFormProBackupHelper::getHash($this->form->FormId).'.xml');
		$archive->add($buffer);
		$archive->addPadding($size);
		
		$archive->close();
	}
	
	// Form structure
	// ==============
	
	protected function storeStructure() {
		// Add the form structure #__rsform_forms
		$this->xml->add('structure');
		foreach ($this->form as $property => $value) {
		    if ($property == 'GridLayout' && strlen($value))
            {
                $this->GridLayout = $value;
                $value = '';
            }
			$this->xml->add($property, $value);
		}
		// Add the form translation
		if ($translations = $this->getFormTranslations()) {
			$this->xml->add('translations');
			foreach ($translations as $language => $properties) {
				$this->xml->add($language);
				foreach ($properties as $property => $value) {
					$this->xml->add($property, $value);
				}
				$this->xml->add('/'.$language);
			}
			$this->xml->add('/translations');
		}
		$this->xml->add('/structure');
	}
	
	// Fields
	// ======
	
	protected function storeFields() {
		// Add fields #__rsform_components
		if ($fields = $this->getFields()) {
			$this->xml->add('fields');
			foreach ($fields as $field) {
				$properties 	= isset($field->properties) ? $field->properties : array();
				$translations 	= isset($field->translations) ? $field->translations : array();
				
				// No need for these.
				unset($field->ComponentId, $field->FormId, $field->properties, $field->translations);
				
				$this->xml->add('field');
				foreach ($field as $property => $value) {
					$this->xml->add($property, $value);
				}
				
				// Add field properties
				$this->xml->add('properties');
				foreach ($properties as $property => $value) {
					$this->xml->add($property, $value);
				}
				$this->xml->add('/properties');
				
				// Add translations
				if ($translations) {
					$this->xml->add('translations');
					foreach ($translations as $language => $properties) {
						$this->xml->add($language);
						foreach ($properties as $property => $value) {
							$this->xml->add($property, $value);
						}
						$this->xml->add('/'.$language);
					}
					$this->xml->add('/translations');
				}
				
				$this->xml->add('/field');
			}
			$this->xml->add('/fields');
		}
	}
	
	protected function getFields() {
		$db 	= &$this->db;
		$query 	= $db->getQuery(true);
		
		$query->select('*')
			  ->from($db->qn('#__rsform_components'))
			  ->where($db->qn('FormId').'='.$db->q($this->form->FormId));
		$db->setQuery($query);
		$fields = $db->loadObjectList('ComponentId');
		
		// Get properties
		if ($fields) {
			$query->clear()
				  ->select('*')
				  ->from($db->qn('#__rsform_properties'))
				  ->where($db->qn('ComponentId').' IN ('.RSFormProBackupHelper::qi(array_keys($fields)).')');
			$db->setQuery($query);
			$properties = $db->loadObjectList();
		
			// Get translations
			if ($translations = $this->getTranslations('properties')) {
				foreach ($translations as $translation) {
					list($componentId, $property) = explode('.', $translation->reference_id, 2);
					if (!isset($fields[$componentId]->translations)) {
						$fields[$componentId]->translations = array();
					}
					if (!isset($fields[$componentId]->translations[$translation->lang_code])) {
						$fields[$componentId]->translations[$translation->lang_code] = array();
					}
					
					$fields[$componentId]->translations[$translation->lang_code][$property] = $translation->value;
				}
			}
		
			foreach ($properties as $property) {
				if (!isset($fields[$property->ComponentId]->properties)) {
					$fields[$property->ComponentId]->properties = array();
				}
				
				// exceptions for the calendars (YUI and jQuery)
				if ($property->PropertyName == 'VALIDATIONCALENDAR' && !empty($property->PropertyValue)) {
					$valueProperty = explode(' ', $property->PropertyValue);
					// get the name of the component
					$query->clear()
						  ->select('PropertyValue')
						  ->from($db->qn('#__rsform_properties'))
						  ->where($db->qn('ComponentId').' = '.$db->q($valueProperty[1]))
						  ->where($db->qn('PropertyName').' = '.$db->q('NAME'));
					$db->setQuery($query);
					$componentName = $db->loadResult();
					
					if (!empty($componentName)) {
						$valueProperty[1] = $componentName;
						
						$property->PropertyValue = implode(' ', $valueProperty);
					}
				}
				
				$fields[$property->ComponentId]->properties[$property->PropertyName] = $property->PropertyValue;
			}
			
			foreach ($fields as $field) {
				$this->fields[$field->ComponentId] = '';
				if (isset($field->properties['NAME'])) {
					$this->fields[$field->ComponentId] = $field->properties['NAME'];
				}
			}
		}
		
		return $fields;
	}
	
	// Calculations
	// ============
	
	protected function storeCalculations() {
		// Add Calculations #__rsform_calculations
		if ($calculations = $this->getCalculations()) {
			$this->xml->add('calculations');
			foreach ($calculations as $calculation) {
				// No need for these.
				unset($calculation->id, $calculation->formId);
			
				$this->xml->add('calculation');
				foreach ($calculation as $property => $value) {
					$this->xml->add($property, $value);
				}
				$this->xml->add('/calculation');
			}
			$this->xml->add('/calculations');
		}
	}
	
	protected function getCalculations() {
		$db 	= &$this->db;
		$query 	= $db->getQuery(true);
		
		$query->select('*')
			  ->from($db->qn('#__rsform_calculations'))
			  ->where($db->qn('formId').'='.$db->q($this->form->FormId));
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	// Post
	// ====
	
	protected function storePost() {
		// Add Post to Location #__rsform_posts
		if ($post = $this->getPost()) {
			// No need for this
			unset($post->form_id);
			
			$this->xml->add('post');
			foreach ($post as $property => $value) {
				$this->xml->add($property, $value);
			}
			$this->xml->add('/post');
		}
	}
	
	protected function getPost() {
		$db 	= &$this->db;
		$query 	= $db->getQuery(true);
		
		$query->select('*')
			  ->from($db->qn('#__rsform_posts'))
			  ->where($db->qn('form_id').'='.$db->q($this->form->FormId));
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	// Conditions
	// ==========
	
	protected function storeConditions() {
		// Add conditions #__rsform_conditions & #__rsform_condition_details
		if ($conditions = $this->getConditions()) {
			$this->xml->add('conditions');
			foreach ($conditions as $condition) {
				$component_id = $condition->component_id;
				
				// No need
				unset($condition->id, $condition->form_id, $condition->component_id);
				
				if (isset($this->fields[$component_id])) {
					$condition->component_id = $this->fields[$component_id];
				} else {
					$condition->component_id = '';
				}
				
				$this->xml->add('condition');
				foreach ($condition as $property => $value) {					
					if ($property == 'details') {
						$this->xml->add('details');
						foreach ($value as $detail) {
							$this->xml->add('detail');
							foreach ($detail as $property => $value) {
								$this->xml->add($property, $value);
							}
							$this->xml->add('/detail');
						}
						$this->xml->add('/details');
					} else {
						$this->xml->add($property, $value);
					}
				}
				$this->xml->add('/condition');
			}
			$this->xml->add('/conditions');
		}
	}
	
	protected function getConditions() {
		$db 		= &$this->db;
		$query		= $db->getQuery(true);
		$conditions = array();
		
		$query->select('*')
			  ->from($db->qn('#__rsform_conditions'))
			  ->where($db->qn('form_id').'='.$db->q($this->form->FormId));
		$db->setQuery($query);
		if ($conditions = $db->loadObjectList('id')) {
			$query->clear()
				  ->select('*')
				  ->from($db->qn('#__rsform_condition_details'))
				  ->where($db->qn('condition_id').' IN ('.RSFormProBackupHelper::qi(array_keys($conditions)).')');
			$db->setQuery($query);
			if ($details = $db->loadObjectList()) {
				foreach ($details as $detail) {
					$condition_id = $detail->condition_id;
					$component_id = $detail->component_id;
					
					// No need for these.
					unset($detail->id, $detail->condition_id, $detail->component_id);
					
					// Add the field's name so that we can restore correctly.
					$detail->component_id = isset($this->fields[$component_id]) ? $this->fields[$component_id] : '';
					
					if (!isset($conditions[$condition_id]->details)) {
						$conditions[$condition_id]->details = array();
					}
					
					$conditions[$condition_id]->details[] = $detail;
				}
			}
		}
		
		return $conditions;
	}
	
	// Directory
	// =========
	
	protected function storeDirectory() {
		// Add directory #__rsform_directory & #__rsform_directory_fields
		if ($directory = $this->getDirectory()) {
			// No need for these
			unset($directory->formId);
			
			$headers = RSFormProHelper::getDirectoryStaticHeaders();
			
			$this->xml->add('directory');
			foreach ($directory as $property => $value) {
				if ($property == 'fields') {
					$this->xml->add('fields');
					foreach ($value as $field) {
						// No need for this.
						unset($field->formId);
						
						// Special case - static headers
						if ($field->componentId < 0 && isset($headers[$field->componentId])) {
							// Do nothing
						} else {
							$field->componentId = isset($this->fields[$field->componentId]) ? $this->fields[$field->componentId] : '';
						}
						
						$this->xml->add('field');
						foreach ($field as $property => $value) {
							$this->xml->add($property, $value);
						}
						$this->xml->add('/field');
					}
					$this->xml->add('/fields');
				} else {
					$this->xml->add($property, $value);
				}
			}
			$this->xml->add('/directory');
		}
	}
	
	protected function getDirectory() {
		$db 	= &$this->db;
		$query 	= $db->getQuery(true);
		
		$query->select('*')
			  ->from($db->qn('#__rsform_directory'))
			  ->where($db->qn('formId').'='.$db->q($this->form->FormId));
		$db->setQuery($query);
		if ($directory = $db->loadObject()) {
			$query->clear()
				  ->select('*')
				  ->from($db->qn('#__rsform_directory_fields'))
				  ->where($db->qn('formId').'='.$db->q($this->form->FormId));
			$db->setQuery($query);
			$directory->fields = $db->loadObjectList();
			
			return $directory;
		}
		
		return false;
	}
	
	// Emails
	// ======
	
	protected function storeEmails() {
		// Add Emails #__rsform_emails
		if ($emails = $this->getEmails()) {
			$this->xml->add('emails');
			foreach ($emails as $email) {
				$translations = isset($email->translations) ? $email->translations : array();
				unset($email->translations, $email->id, $email->formId);
				
				$this->xml->add('email');
				foreach ($email as $property => $value) {
					$this->xml->add($property, $value);
				}
				
				// Add translations
				if ($translations) {
					$this->xml->add('translations');
					foreach ($translations as $language => $properties) {
						$this->xml->add($language);
						foreach ($properties as $property => $value) {
							$this->xml->add($property, $value);
						}
						$this->xml->add('/'.$language);
					}
					$this->xml->add('/translations');
				}
				$this->xml->add('/email');
			}
			$this->xml->add('/emails');
		}
	}
	
	protected function getEmails() {
		$db 	= &$this->db;
		$query 	= $db->getQuery(true);
		
		$query->select('*')
			  ->from($db->qn('#__rsform_emails'))
			  ->where($db->qn('formId').'='.$db->q($this->form->FormId));
		$db->setQuery($query);
		if ($emails = $db->loadObjectList('id')) {
			// Get translations
			if ($translations = $this->getTranslations('emails')) {
				foreach ($translations as $translation) {
					@list($id, $property) = explode('.', $translation->reference_id, 2);
					if (!isset($emails[$id])) {
						continue;
					}
					if (!isset($emails[$id]->translations)) {
						$emails[$id]->translations = array();
					}
					if (!isset($emails[$id]->translations[$translation->lang_code])) {
						$emails[$id]->translations[$translation->lang_code] = array();
					}
					
					$emails[$id]->translations[$translation->lang_code][$property] = $translation->value;
				}
			}
			
			return $emails;
		}
	}
	
	// Mappings
	// ========
	
	protected function storeMappings() {
		// Add Mappings #__rsform_mappings
		if ($mappings = $this->getMappings()) {
			$this->xml->add('mappings');
			foreach ($mappings as $mapping) {
				unset($mapping->id, $mapping->formId);
				
				$this->xml->add('mapping');
				foreach ($mapping as $property => $value) {
					$this->xml->add($property, $value);
				}
				$this->xml->add('/mapping');
			}
			$this->xml->add('/mappings');
		}
	}
	
	protected function getMappings() {
		$db 	= &$this->db;
		$query 	= $db->getQuery(true);
		
		$query->select('*')
			  ->from($db->qn('#__rsform_mappings'))
			  ->where($db->qn('formId').'='.$db->q($this->form->FormId));
		$db->setQuery($query);
		
		return $db->loadObjectList();
	}
	
	// Translations
	// ============
	
	protected function getFormTranslations() {
		$translations = array();
		if ($results = $this->getTranslations('forms')) {
			foreach ($results as $result) {
				if (!isset($translations[$result->lang_code])) {
					$translations[$result->lang_code] = array();
				}
				
				$translations[$result->lang_code][$result->reference_id] = $result->value;
			}
		}
		
		return $translations;
	}
	
	protected function getTranslations($reference) {
		$db 	= &$this->db;
		$query	= $db->getQuery(true);		
		$query->select('*')
			  ->from($db->qn('#__rsform_translations'))
			  ->where($db->qn('form_id').'='.$db->q($this->form->FormId))
			  ->where($db->qn('reference').'='.$db->q($reference));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	// Grid Layout - needs to replace IDs with field names.
	// ===========

	protected function storeGridLayout()
    {
        if (empty($this->GridLayout))
        {
            return false;
        }

        $data   = json_decode($this->GridLayout, true);
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

        $this->xml->replace('GridLayout', json_encode(array($rows, $hidden)));
    }
}