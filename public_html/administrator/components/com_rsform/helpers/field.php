<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormProField
{
	protected $formId;
	protected $componentId;
	protected $data;
	protected $value;
	protected $invalid;
	protected $errorClass = '';
    protected $fieldErrorClass = '';
	
	protected $name;
	protected $namespace = 'form';
	
	// calculation handling
	public $pricePattern = '#\[p([0-9\.\-\+]+)\]#s';
	public $prices = array();
	
	public $preview = false;
	
	public function __construct($config) {
		foreach ($config as $key => $value)
		{
			$this->{$key} = $value;
		}

		$this->name = $this->getProperty('NAME');
	}

	public function __get($property) {
		if ($property == 'output') {
			// generate the actual field
			return  ($this->preview ? $this->getPreviewInput() : $this->getFormInput());
		}
	}
	
	// @desc Get the input's preview HTML
	public function getPreviewInput() {
		return '';
	}
	// @desc Get the input's HTML
	public function getFormInput() {
		return '';
	}
	
	// @desc Escapes HTML user input
	protected function escape($value) {
		return htmlentities($value, ENT_COMPAT, 'UTF-8');
	}
	
	// @desc Checks if <code> tags are found.
	protected function hasCode($value) {
		return strpos($value, '<code>') !== false;
	}
	
	// @desc If <code> tags are found executes it as a PHP script
	protected function isCode($value) {
		if ($this->hasCode($value)) {
			return eval($value);
		}
		
		return $value;
	}
	
	// @desc Checks if a property exists.
	public function hasProperty($prop) {
		return isset($this->data[$prop]);
	}
	
	// @desc Returns a field property from $data
	public function getProperty($prop, $default=null) {
		// Special case, we no longer use == 'YES' or == 'NO'
		if (isset($this->data[$prop])) {
			if ($this->data[$prop] === 'YES') {
				return true;
			} else if ($this->data[$prop] === 'NO') {
				return false;
			} else {
				return $this->data[$prop];
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
	
	// @desc Sets a property - useful for overriding them, such as the validation message.
	public function setProperty($prop, $value) {
		$this->data[$prop] = $value;
	}
	
	// @desc Returns the full name of the name HTML tag (eg. form[textbox])
	public function getName() {
		return $this->namespace.'['.$this->name.']';
	}
	
	// @desc Returns just the name to be used as an ID
	public function getId() {
		return $this->name;
	}
	
	// @desc Parses attributes from HTML code.
	protected function parseAttributes($string) {
		if (!isset($this->attr)) {
			$this->attr = array();
			$attr = array();

			// Let's grab all the key/value pairs using a regular expression
			preg_match_all('/([\w:-]+)[\s]?(=[\s]?"([^"]*)")?/i', $string, $attr);

			if (is_array($attr))
			{
				$numPairs = count($attr[1]);
				for ($i = 0; $i < $numPairs; $i++)
				{
					$this->attr[$attr[1][$i]] = $attr[3][$i];
				}
			}
		}

		return $this->attr;
	}
	
	public function getAttributes()
	{
		static $focused = array();
		$return = array();

        if ($attr = $this->getProperty('ADDITIONALATTRIBUTES'))
		{
			$return = $this->parseAttributes($attr);
		}

		if (!isset($return['class']))
		{
			$return['class'] = '';
		}

		if ($this->invalid)
        {
			if (strlen($this->fieldErrorClass))
			{
				if (strlen($return['class']))
				{
					$return['class'] .= ' ';
				}
				$return['class'] .= $this->fieldErrorClass;
			}
			
			if (!isset($focused[$this->formId]))
			{
                $formOptions = RSFormProHelper::getForm($this->formId);
                if (!$formOptions->ScrollToError)
                {
                    $return['autofocus'] = '';
                }

				$focused[$this->formId] = true;
			}
        }

		return $return;
	}

	public function attributeToHtml($key, $values) {
		$html = '';

		// Add only valid attributes
		if (strlen($key)) {
			// Skip adding empty class value
			if ($key == 'class' && !strlen($values)) {
				return $html;
			}
			// Escape HTML
			$html .= ' '.$this->escape($key);

			// If we have a value, append it, otherwise just attribute is fine according to HTML5
			if (strlen($values)) {
				$html .= '='.'"'.$this->escape($values).'"';
			}
		}

		return $html;
	}
	
	public function getValue() {
		// Default value processing
		if (!isset($this->value[$this->name])) {
			$default = $this->getProperty('DEFAULTVALUE', '');
			return $default ? $this->isCode($default) : $default;
		}
		
		// Actual value is set, return it
		return $this->value[$this->name];
	}
	
	public function addScript($path) {
		RSFormProAssets::addScript($path);
	}
	
	public function addCustomTag($tag) {
		RSFormProAssets::addCustomTag($tag);
	}
	
	public function addScriptDeclaration($script) {
		RSFormProAssets::addScriptDeclaration($script);
	}

	// process field or file before storing it to the database
	public function processBeforeStore($submissionId, &$post, &$files) {
		return;
	}
}