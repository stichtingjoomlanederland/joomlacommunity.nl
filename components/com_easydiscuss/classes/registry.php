<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

/**
 * @package     Joomla.Platform
 * @subpackage  Registry
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

jimport('joomla.utilities.arrayhelper');

class DiscussRegistry
{
	protected $data;

	protected static $instances = array();

	public function __construct($data = null)
	{
		// Instantiate the internal data object.
		$this->data = new stdClass;

		// Optionally load supplied data.
		if (is_array($data) || is_object($data))
		{
			$this->bindData($this->data, $data);
		}
		elseif (!empty($data) && is_string($data))
		{
			$this->loadString($data);
		}
	}

	public function __clone()
	{
		$this->data = unserialize(serialize($this->data));
	}

	public function __toString()
	{
		return $this->toString();
	}

	public function def($key, $default = '')
	{
		$value = $this->get($key, (string) $default);
		$this->set($key, $value);
		return $value;
	}

	public function exists($path)
	{
		// Explode the registry path into an array
		if ($nodes = explode('.', $path))
		{
			// Initialize the current node to be the registry root.
			$node = $this->data;

			// Traverse the registry to find the correct node for the result.
			for ($i = 0, $n = count($nodes); $i < $n; $i++)
			{
				if (isset($node->$nodes[$i]))
				{
					$node = $node->$nodes[$i];
				}
				else
				{
					break;
				}

				if ($i + 1 == $n)
				{
					return true;
				}
			}
		}

		return false;
	}

	public function get($path, $default = null)
	{
		// Initialise variables.
		$result = $default;

		if (!strpos($path, '.'))
		{
			return (isset($this->data->$path) && $this->data->$path !== null && $this->data->$path !== '') ? $this->data->$path : $default;
		}
		// Explode the registry path into an array
		$nodes = explode('.', $path);

		// Initialize the current node to be the registry root.
		$node = $this->data;
		$found = false;
		// Traverse the registry to find the correct node for the result.
		foreach ($nodes as $n)
		{
			if (isset($node->$n))
			{
				$node = $node->$n;
				$found = true;
			}
			else
			{
				$found = false;
				break;
			}
		}
		if ($found && $node !== null && $node !== '')
		{
			$result = $node;
		}

		return $result;
	}


	public static function getInstance($id)
	{
		if (empty(self::$instances[$id]))
		{
			self::$instances[$id] = new DiscussRegistry;
		}

		return self::$instances[$id];
	}

	public function loadArray($array)
	{
		$this->bindData($this->data, $array);

		return true;
	}

	public function loadObject($object)
	{
		$this->bindData($this->data, $object);

		return true;
	}

	public function loadFile($file, $format = 'JSON', $options = array())
	{
		// Get the contents of the file
		jimport('joomla.filesystem.file');
		$data = JFile::read($file);

		return $this->loadString($data, $format, $options);
	}

	public function loadString($data, $format = 'JSON', $options = array())
	{
		// Load a string into the given namespace [or default namespace if not given]
		$handler = DiscussRegistryFormat::getInstance($format);

		$obj = $handler->stringToObject($data, $options);
		$this->loadObject($obj);

		return true;
	}

	public function merge(&$source)
	{
		if ($source instanceof DiscussRegistry)
		{
			// Load the variables into the registry's default namespace.
			foreach ($source->toArray() as $k => $v)
			{
				if (($v !== null) && ($v !== ''))
				{
					$this->data->$k = $v;
				}
			}
			return true;
		}
		return false;
	}

	public function set($path, $value)
	{
		$result = null;

		// Explode the registry path into an array
		if ($nodes = explode('.', $path))
		{
			// Initialize the current node to be the registry root.
			$node = $this->data;

			// Traverse the registry to find the correct node for the result.
			for ($i = 0, $n = count($nodes) - 1; $i < $n; $i++)
			{
				if (!isset($node->$nodes[$i]) && ($i != $n))
				{
					$node->$nodes[$i] = new stdClass;
				}
				$node = $node->$nodes[$i];
			}

			// Get the old value if exists so we can return it
			$result = $node->$nodes[$i] = $value;
		}

		return $result;
	}

	public function toArray()
	{
		return (array) $this->asArray($this->data);
	}

	public function toObject()
	{
		return $this->data;
	}

	public function toString($format = 'JSON', $options = array())
	{
		// Return a namespace in a given format
		$handler = DiscussRegistryFormat::getInstance($format);

		return $handler->objectToString($this->data, $options);
	}

	protected function bindData(&$parent, $data)
	{
		// Ensure the input data is an array.
		if (is_object($data))
		{
			$data = get_object_vars($data);
		}
		else
		{
			$data = (array) $data;
		}

		foreach ($data as $k => $v)
		{
			if ((is_array($v) && JArrayHelper::isAssociative($v)) || is_object($v))
			{
				$parent->$k = new stdClass;
				$this->bindData($parent->$k, $v);
			}
			else
			{
				$parent->$k = $v;
			}
		}
	}

	protected function asArray($data)
	{
		$array = array();

		foreach (get_object_vars((object) $data) as $k => $v)
		{
			if (is_object($v))
			{
				$array[$k] = $this->asArray($v);
			}
			else
			{
				$array[$k] = $v;
			}
		}

		return $array;
	}

	public function loadXML($data, $namespace = null)
	{
		return $this->loadString($data, 'XML');
	}

	public function loadINI($data, $namespace = null, $options = array())
	{
		return $this->loadString($data, 'INI', $options);
	}

	public function loadJSON($data)
	{
		return $this->loadString($data, 'JSON');
	}

	public function makeNameSpace($namespace)
	{
		//$this->_registry[$namespace] = array('data' => new stdClass());
		return true;
	}

	public function getNameSpaces()
	{
		//return array_keys($this->_registry);
		return array();
	}

	public function getValue($path, $default = null)
	{
		$parts = explode('.', $path);
		if (count($parts) > 1)
		{
			unset($parts[0]);
			$path = implode('.', $parts);
		}
		return $this->get($path, $default);
	}

	public function setValue($path, $value)
	{
		$parts = explode('.', $path);
		if (count($parts) > 1)
		{
			unset($parts[0]);
			$path = implode('.', $parts);
		}
		return $this->set($path, $value);
	}

	public function loadSetupFile()
	{
		return true;
	}
}

abstract class DiscussRegistryFormat
{

	protected static $instances = array();

	public static function getInstance($type)
	{
		// Sanitize format type.
		$type = strtolower(preg_replace('/[^A-Z0-9_]/i', '', $type));

		// Only instantiate the object if it doesn't already exist.
		if (!isset(self::$instances[$type]))
		{
			// Only load the file the class does not exist.
			$class = 'DiscussRegistryFormat' . $type;
			if (!class_exists($class))
			{
				$path = dirname(__FILE__) . '/format/' . $type . '.php';
				if (is_file($path))
				{
					include_once $path;
				}
				else
				{
					throw new JException(JText::_('JLIB_REGISTRY_EXCEPTION_LOAD_FORMAT_CLASS'), 500, E_ERROR);
				}
			}

			self::$instances[$type] = new $class;
		}
		return self::$instances[$type];
	}

	abstract public function objectToString($object, $options = null);

	abstract public function stringToObject($data, $options = null);
}

class DiscussRegistryFormatINI extends DiscussRegistryFormat
{
	protected static $cache = array();

	public function objectToString($object, $options = array())
	{
		// Initialize variables.
		$local = array();
		$global = array();

		// Iterate over the object to set the properties.
		foreach (get_object_vars($object) as $key => $value)
		{
			// If the value is an object then we need to put it in a local section.
			if (is_object($value))
			{
				// Add the section line.
				$local[] = '';
				$local[] = '[' . $key . ']';

				// Add the properties for this section.
				foreach (get_object_vars($value) as $k => $v)
				{
					$local[] = $k . '=' . $this->getValueAsINI($v);
				}
			}
			else
			{
				// Not in a section so add the property to the global array.
				$global[] = $key . '=' . $this->getValueAsINI($value);
			}
		}

		return implode("\n", array_merge($global, $local));
	}

	public function stringToObject($data, $options = array())
	{
		// Initialise options.
		if (is_array($options))
		{
			$sections = (isset($options['processSections'])) ? $options['processSections'] : false;
		}
		else
		{
			// Backward compatibility for 1.5 usage.
			//@deprecated
			$sections = (boolean) $options;
		}

		// Check the memory cache for already processed strings.
		$hash = md5($data . ':' . (int) $sections);
		if (isset(self::$cache[$hash]))
		{
			return self::$cache[$hash];
		}

		// If no lines present just return the object.
		if (empty($data))
		{
			return new stdClass;
		}

		// Initialize variables.
		$obj = new stdClass;
		$section = false;
		$lines = explode("\n", $data);

		// Process the lines.
		foreach ($lines as $line)
		{
			// Trim any unnecessary whitespace.
			$line = trim($line);

			// Ignore empty lines and comments.
			if (empty($line) || ($line{0} == ';'))
			{
				continue;
			}

			if ($sections)
			{
				$length = strlen($line);

				// If we are processing sections and the line is a section add the object and continue.
				if (($line[0] == '[') && ($line[$length - 1] == ']'))
				{
					$section = substr($line, 1, $length - 2);
					$obj->$section = new stdClass;
					continue;
				}
			}
			elseif ($line{0} == '[')
			{
				continue;
			}

			// Check that an equal sign exists and is not the first character of the line.
			if (!strpos($line, '='))
			{
				// Maybe throw exception?
				continue;
			}

			// Get the key and value for the line.
			list ($key, $value) = explode('=', $line, 2);

			// Validate the key.
			if (preg_match('/[^A-Z0-9_]/i', $key))
			{
				// Maybe throw exception?
				continue;
			}

			// If the value is quoted then we assume it is a string.
			$length = strlen($value);
			if ($length && ($value[0] == '"') && ($value[$length - 1] == '"'))
			{
				// Strip the quotes and Convert the new line characters.
				$value = stripcslashes(substr($value, 1, ($length - 2)));
				$value = str_replace('\n', "\n", $value);
			}
			else
			{
				// If the value is not quoted, we assume it is not a string.

				// If the value is 'false' assume boolean false.
				if ($value == 'false')
				{
					$value = false;
				}
				// If the value is 'true' assume boolean true.
				elseif ($value == 'true')
				{
					$value = true;
				}
				// If the value is numeric than it is either a float or int.
				elseif (is_numeric($value))
				{
					// If there is a period then we assume a float.
					if (strpos($value, '.') !== false)
					{
						$value = (float) $value;
					}
					else
					{
						$value = (int) $value;
					}
				}
			}

			// If a section is set add the key/value to the section, otherwise top level.
			if ($section)
			{
				$obj->$section->$key = $value;
			}
			else
			{
				$obj->$key = $value;
			}
		}

		// Cache the string to save cpu cycles -- thus the world :)
		self::$cache[$hash] = clone ($obj);

		return $obj;
	}

	protected function getValueAsINI($value)
	{
		// Initialize variables.
		$string = '';

		switch (gettype($value))
		{
			case 'integer':
			case 'double':
				$string = $value;
				break;

			case 'boolean':
				$string = $value ? 'true' : 'false';
				break;

			case 'string':
				// Sanitize any CRLF characters..
				$string = '"' . str_replace(array("\r\n", "\n"), '\\n', $value) . '"';
				break;
		}

		return $string;
	}
}

class DiscussRegistryFormatJSON extends DiscussRegistryFormat
{
	public function objectToString($object, $options = array())
	{
		return json_encode($object);
	}

	public function stringToObject($data, $options = array('processSections' => false))
	{
		// Fix legacy API.
		if (is_bool($options))
		{
			$options = array('processSections' => $options);



		}

		$data = trim($data);
		if ((substr($data, 0, 1) != '{') && (substr($data, -1, 1) != '}'))
		{
			$ini = DiscussRegistryFormat::getInstance('INI');
			$obj = $ini->stringToObject($data, $options);
		}
		else
		{
			$obj = json_decode($data);
		}
		return $obj;
	}
}

class DiscussRegistryFormatPHP extends DiscussRegistryFormat
{
	public function objectToString($object, $params = array())
	{
		// Build the object variables string
		$vars = '';
		foreach (get_object_vars($object) as $k => $v)
		{
			if (is_scalar($v))
			{
				$vars .= "\tpublic $" . $k . " = '" . addcslashes($v, '\\\'') . "';\n";
			}
			elseif (is_array($v) || is_object($v))
			{
				$vars .= "\tpublic $" . $k . " = " . $this->getArrayString((array) $v) . ";\n";
			}
		}

		$str = "<?php\nclass " . $params['class'] . " {\n";
		$str .= $vars;
		$str .= "}";

		// Use the closing tag if it not set to false in parameters.
		if (!isset($params['closingtag']) || $params['closingtag'] !== false)
		{
			$str .= "\n?>";
		}

		return $str;
	}

	public function stringToObject($data, $options = array())
	{
		return true;
	}

	protected function getArrayString($a)
	{
		$s = 'array(';
		$i = 0;
		foreach ($a as $k => $v)
		{
			$s .= ($i) ? ', ' : '';
			$s .= '"' . $k . '" => ';
			if (is_array($v) || is_object($v))
			{
				$s .= $this->getArrayString((array) $v);
			}
			else
			{
				$s .= '"' . addslashes($v) . '"';
			}
			$i++;
		}
		$s .= ')';
		return $s;
	}
}

class DiscussRegistryFormatXML extends DiscussRegistryFormat
{
	public function objectToString($object, $options = array())
	{
		// Initialise variables.
		$rootName = (isset($options['name'])) ? $options['name'] : 'registry';
		$nodeName = (isset($options['nodeName'])) ? $options['nodeName'] : 'node';

		// Create the root node.
		$root = simplexml_load_string('<' . $rootName . ' />');

		// Iterate over the object members.
		$this->getXmlChildren($root, $object, $nodeName);

		return $root->asXML();
	}

	public function stringToObject($data, $options = array())
	{
		// Initialize variables.
		$obj = new stdClass;

		// Parse the XML string.
		$xml = simplexml_load_string($data);

		foreach ($xml->children() as $node)
		{
			$obj->$node['name'] = $this->getValueFromNode($node);
		}

		return $obj;
	}

	protected function getValueFromNode($node)
	{
		switch ($node['type'])
		{
			case 'integer':
				$value = (string) $node;
				return (int) $value;
				break;
			case 'string':
				return (string) $node;
				break;
			case 'boolean':
				$value = (string) $node;
				return (bool) $value;
				break;
			case 'double':
				$value = (string) $node;
				return (float) $value;
				break;
			case 'array':
				$value = array();
				foreach ($node->children() as $child)
				{
					$value[(string) $child['name']] = $this->getValueFromNode($child);
				}
				break;
			default:
				$value = new stdClass;
				foreach ($node->children() as $child)
				{
					$value->$child['name'] = $this->getValueFromNode($child);
				}
				break;
		}

		return $value;
	}

	protected function getXmlChildren(&$node, $var, $nodeName)
	{
		// Iterate over the object members.
		foreach ((array) $var as $k => $v)
		{
			if (is_scalar($v))
			{
				$n = $node->addChild($nodeName, $v);
				$n->addAttribute('name', $k);
				$n->addAttribute('type', gettype($v));
			}
			else
			{
				$n = $node->addChild($nodeName);
				$n->addAttribute('name', $k);
				$n->addAttribute('type', gettype($v));

				$this->getXmlChildren($n, $v, $nodeName);
			}
		}
	}
}
