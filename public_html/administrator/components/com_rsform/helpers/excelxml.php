<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormProXLS
{
	protected $file;
	protected $title;
	protected $rows = array();
	
	public function __construct($title = '') {
		$this->setTitle($title);
	}
	
	public function setTitle($title) {
		$title = preg_replace("/[\\\|:|\/|\?|\*|\[|\]]/", '', $title);
		$title = substr($title, 0, 31);
		$this->title = $title;
	}
	
	protected function escape($string) {
		return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
	}
	
	protected function addRow($values) {
		$cells = array();
		foreach ($values as $value) {
			$cells[] = $this->addCell($value);
		}
		$this->rows[] = '<Row>'."\n".implode("\n", $cells)."\n".'</Row>';
	}
	
	protected function addCell($value) {
		if (is_numeric($value)) {
			$type = 'Number';
		} else {
			$type  = 'String';
		}
		return '<Cell><Data ss:Type="'.$type.'">'.$this->escape($value).'</Data></Cell>'; 
	}
	
	public function open($file, $mode) {
		// Create the file pointer.
		$this->file = @fopen($file, $mode);
		
		// File has been opened successfully.
        if (is_resource($this->file)) {
			// First time this file is opened, add the Workbook & Worksheet header.
			if ($mode == 'w') {
				$header = array(
					'<?xml version="1.0" encoding="UTF-8"?>',
					'<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">',
					'<Worksheet ss:Name="'.$this->escape($this->title).'">',
					'<Table>'
				);
				fwrite($this->file, implode("\n", $header)."\n");
			}
			return true;
		} else {
			return false;
		}
	}
	
	public function write($data) {
		foreach ($data as $k => $v) {
			$this->addRow($v);
		}
	}
	
	public function close() {
		// We have items to add, write them to the file.
		if ($this->rows) {
			fwrite($this->file, implode("\n", $this->rows)."\n");
		} else {
			// Nothing to write, finish up the Excel file.
			$footer = array(
				'</Table>',
				'</Worksheet>',
				'</Workbook>'
			);
			fwrite($this->file, implode("\n", $footer));
		}
		return fclose($this->file);
	}
	
	public function writeHeaders($headers) {
		$this->addRow($headers);
	}
}