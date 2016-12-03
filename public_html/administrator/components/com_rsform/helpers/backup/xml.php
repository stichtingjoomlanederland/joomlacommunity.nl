<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormProBackupXML
{
	// Complete path to the XML.
	protected $path;
	
	// File pointer to $this->path
	protected $fp;
	
	// XML contents before being written to file.
	protected $buffer = '';
	
	// Number of [TAB] to indent.
	protected $indent = 0;
	
	// File handling
	// =============
	
	// Open the file resource for appending.
	public function open($path) {
		$isNew = !file_exists($path);
		if (!$this->fp = @fopen($path, 'ab')) { // "b" flag for binary mode.
			throw new Exception(sprintf('Could not open "%s" for writing!', $path));
		}
		
		// Add the <?xml header if the file is newly created.
		if ($isNew) {
			$this->write('<?xml version="1.0" encoding="utf-8"?>'."\n");
		}
		
		$this->path = $path;
		
		// Allow chaining
		return $this;
	}
	
	// Close the file resource.
	public function close($flush = true) {
		if ($flush) {
			$this->write($this->buffer);
		}
		
		return fclose($this->fp);
	}
	
	// Write to the file.
	public function write($value) {
		if (!fwrite($this->fp, $value)) {
			throw new Exception(sprintf('Could not write %d bytes to "%s"!', strlen(utf8_decode($value)), $this->path));
		}
		
		// Allow chaining
		return $this;
	}
	
	// Flush the buffer to the file.
	public function flush() {
		$this->write($this->buffer);
		
		return $this;
	}
	
	// XML handling
	// ============
	
	// Escapes a value before being stored in the XML tags.
	protected function escape($value) {
		if (is_numeric($value) || !$value) {
			return $value;
		} else {
			return '<![CDATA['.str_replace(']]>', ']]]]><![CDATA[>', $value).']]>';
		}
	}
	
	public function addHeader() {
		return $this->add('?xml version="1.0" encoding="utf-8"?');
	}
	
	public function add($tag, $value = null) {
		// If a value is not supplied, this means that we're adding a single tag.
		if (is_null($value)) {
			$this->buffer .= "<$tag>\n";
		} else {
			$this->buffer .= "<$tag>".$this->escape($value)."</$tag>\n";
		}
		
		return $this;
	}
	
	public function __toString() {
		return $this->buffer;
	}
}