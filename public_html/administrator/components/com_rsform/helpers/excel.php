<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
 
defined('_JEXEC') or die('Restricted access');

if (!class_exists('ZipArchive')) {
	// Attempt to emulate through JArchiveZip if ZipArchive does not exist (highly unlikely)
	class ZipArchive
	{
		const CREATE = 1;

		protected $filename;
		protected $files = array();

		public function open($filename, $flag) {
			$this->filename = $filename;

			return is_writable(dirname($filename));
		}

		public function addEmptyDir($dir) {}

		public function addFile($path, $filename) {
			$this->addFromString($filename, file_get_contents($path));
		}

		public function addFromString($filename, $contents) {
			$this->files[] = array(
				'name' => $filename,
				'data' => $contents
			);
		}

		public function close() {
			// 2.5 fix
			jimport('joomla.filesystem.archive');
			$zip = JArchive::getAdapter('zip');
			$zip->create($this->filename, $this->files);
		}
	}
}

class RSFormProXLSX
{	
	protected $cell_formats = array('GENERAL');
	
	// Temporary filename
	protected $filename;
	
	// Submissions counter
	protected $start;
	
	// Max rows counter
	protected $rows;
	
	// Use headers flag
	public $useHeaders = false;
	
	// Form name
	public $name;

	// File pointer
	public $fp;
	
	public function open($filename, $mode, $start, $rows = null, $cols = null) {
		$this->filename = $filename;
		$this->start	= $start;
		
		$this->fp = @fopen($filename, $mode);
		if (!is_resource($this->fp)) {
			throw new Exception("Could not open $filename for writing.");
		}
		
		if ($mode == 'w') {
			$xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n".
			'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'."\n".
			  '<sheetPr filterMode="false">'."\n".
				'<pageSetUpPr fitToPage="false"/>'."\n".
			  '</sheetPr>'."\n".
			  '<dimension ref="A1:' . $this->getCellName($rows, $cols) . '"/>'."\n".
			  '<sheetViews>'."\n".
				'<sheetView colorId="64" defaultGridColor="true" rightToLeft="false" showFormulas="false" showGridLines="true" showOutlineSymbols="true" showRowColHeaders="true" showZeros="true" tabSelected="true" topLeftCell="A1" view="normal" windowProtection="false" workbookViewId="0" zoomScale="100" zoomScaleNormal="100" zoomScalePageLayoutView="100">'."\n".
				  '<selection activeCell="A1" activeCellId="0" pane="topLeft" sqref="A1"/>'."\n".
				'</sheetView>'."\n".
			  '</sheetViews>'."\n".
			  '<cols>'."\n".
				'<col collapsed="false" hidden="false" max="1025" min="1" style="0" width="11.5"/>'."\n".
			  '</cols>'."\n".
			  '<sheetData>'."\n";
		
			if (!fwrite($this->fp, $xml)) {
				throw new Exception("Could not write sheet XML head data to $filename.");
			}
		}
	}
	
	public function write($data) {
		$this->rows = $this->start + count($data);
		
		foreach ($data as $num => $row) {
			$this->writeRow($num, $row);
		}
	}
	
	public function writeHeaders($data) {
		$xml = '<row collapsed="false" customFormat="false" customHeight="false" hidden="false" ht="12.1" outlineLevel="0" r="1">'."\n";
		
		$column_number = 0;
		foreach ($data as $key => $value) {
			$xml .= $this->writeCell(0, $column_number, $value);
			$column_number++;
		}
		
		$xml .= '</row>'."\n";
		
		fwrite($this->fp, $xml);
	}
	
	protected function writeRow($num, $row) {
		if ($this->useHeaders) {
			// Skip one row since we have headers in that one
			$num++;
		}
		
		$xml = '<row collapsed="false" customFormat="false" customHeight="false" hidden="false" ht="12.1" outlineLevel="0" r="' . ($this->start + $num + 1) . '">'."\n";
		
		$column_number = 0;
		foreach ($row as $key => $value) {
			$xml .= $this->writeCell($this->start + $num, $column_number, $value);
			$column_number++;
		}
		
		$xml .= '</row>'."\n";
		
		fwrite($this->fp, $xml);
	}
	
	protected function writeCell($row_number, $column_number, $value)
	{
		$cell_name = $this->getCellName($row_number, $column_number);
		
		return "\t".'<c r="'.$cell_name.'" s="0" t="inlineStr"><is><t>'.$this->escape($value).'</t></is></c>'."\n";
	}
	
	public function close() {
		$zip = new ZipArchive();
		if (!$zip->open($this->filename.'.zip', ZipArchive::CREATE)) {
			throw new Exception("Could not create archive {$this->filename}.zip.");
		}
		
		$zip->addEmptyDir("_rels/");
		$zip->addEmptyDir("docProps/");
		$zip->addEmptyDir("xl/_rels/");
		$zip->addEmptyDir("xl/worksheets/");
		
		$zip->addFromString("[Content_Types].xml", $this->buildContentTypesXML());
		$zip->addFromString("docProps/app.xml", $this->buildAppXML());
		$zip->addFromString("docProps/core.xml", $this->buildCoreXML());
		$zip->addFromString("_rels/.rels", $this->buildRelationshipsXML());
		$zip->addFromString("xl/_rels/workbook.xml.rels", $this->buildWorkbookRelsXML());
		$zip->addFromString("xl/styles.xml", $this->buildStylesXML()); 
		$zip->addFromString("xl/workbook.xml", $this->buildWorkbookXML());
		
		// Finalize sheet
		$xml = '</sheetData>'."\n".
		'<printOptions headings="false" gridLines="false" gridLinesSet="true" horizontalCentered="false" verticalCentered="false"/>'."\n".
		'<pageMargins left="0.5" right="0.5" top="1.0" bottom="1.0" header="0.5" footer="0.5"/>'."\n".
		'<pageSetup blackAndWhite="false" cellComments="none" copies="1" draft="false" firstPageNumber="1" fitToHeight="1" fitToWidth="1" horizontalDpi="300" orientation="portrait" pageOrder="downThenOver" paperSize="1" scale="100" useFirstPageNumber="true" usePrinterDefaults="false" verticalDpi="300"/>'."\n".
		'<headerFooter differentFirst="false" differentOddEven="false">'."\n".
			'<oddHeader>&amp;C&amp;&quot;Times New Roman,Regular&quot;&amp;12&amp;A</oddHeader>'."\n".
			'<oddFooter>&amp;C&amp;&quot;Times New Roman,Regular&quot;&amp;12Page &amp;P</oddFooter>'."\n".
		'</headerFooter>'."\n".
		'</worksheet>';
		
		fwrite($this->fp, $xml);
		
		$zip->addFile($this->filename, "xl/worksheets/sheet1.xml");
		
		$zip->close();
	}
	
	protected function buildAppXML()
	{
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n".
		'<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes"><TotalTime>0</TotalTime></Properties>';
	}
	
	protected function buildCoreXML()
	{
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n".
		'<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'."\n".
		'<dcterms:created xsi:type="dcterms:W3CDTF">'.JFactory::getDate()->format("Y-m-d\TH:i:s.00\Z").'</dcterms:created>'."\n".
		'<dc:creator>'.$this->escape(JFactory::getUser()->name).'</dc:creator>'."\n".
		'<cp:revision>0</cp:revision>'."\n".
		'</cp:coreProperties>';
	}
	
	protected function buildRelationshipsXML()
	{
		return '<?xml version="1.0" encoding="UTF-8"?>'."\n".
		'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'."\n".
		'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'."\n".
		'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'."\n".
		'<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'."\n".
		'</Relationships>';
	}
	
	protected function buildWorkbookXML()
	{
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n".
		'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'."\n".
		'<fileVersion appName="Calc"/><workbookPr backupFile="false" showObjects="all" date1904="false"/><workbookProtection/>'."\n".
		'<bookViews><workbookView activeTab="0" firstSheet="0" showHorizontalScroll="true" showSheetTabs="true" showVerticalScroll="true" tabRatio="212" windowHeight="8192" windowWidth="16384" xWindow="0" yWindow="0"/></bookViews>'."\n".
		'<sheets>'."\n".
		'<sheet name="'.$this->escape($this->name).'" sheetId="1" state="visible" r:id="rId2"/>'."\n".
		'</sheets>'."\n".
		'<calcPr iterateCount="100" refMode="A1" iterate="false" iterateDelta="0.001"/>'."\n".
		'</workbook>';
	}
	
	protected function buildWorkbookRelsXML()
	{
		return '<?xml version="1.0" encoding="UTF-8"?>'."\n".
		'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'."\n".
		'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'."\n".
		'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'."\n".
		'</Relationships>';
	}
	
	protected function buildContentTypesXML()
	{
		return '<?xml version="1.0" encoding="UTF-8"?>'."\n".
		'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'."\n".
		'<Override PartName="/_rels/.rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'."\n".
		'<Override PartName="/xl/_rels/workbook.xml.rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'."\n".
		'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'."\n".
		'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'."\n".
		'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'."\n".
		'<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'."\n".
		'<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'."\n".
		'</Types>';
	}
	
	protected function buildStylesXML()
	{
		$xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n".
		'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'."\n".
		'<numFmts count="'.count($this->cell_formats).'">'."\n";
		
		foreach ($this->cell_formats as $i => $format) {
			$xml .= '<numFmt numFmtId="'.(164 + $i).'" formatCode="'.$this->escape($format).'" />'."\n";
		}
		
		$xml .= '</numFmts>'."\n".
		'<fonts count="4">'."\n".
				'<font><name val="Arial"/><charset val="1"/><family val="2"/><sz val="10"/></font>'."\n".
				'<font><name val="Arial"/><family val="0"/><sz val="10"/></font>'."\n".
				'<font><name val="Arial"/><family val="0"/><sz val="10"/></font>'."\n".
				'<font><name val="Arial"/><family val="0"/><sz val="10"/></font>'."\n".
		'</fonts>'."\n".
		'<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'."\n".
		'<borders count="1"><border diagonalDown="false" diagonalUp="false"><left/><right/><top/><bottom/><diagonal/></border></borders>'."\n".
			'<cellStyleXfs count="20">'."\n".
				'<xf applyAlignment="true" applyBorder="true" applyFont="true" applyProtection="true" borderId="0" fillId="0" fontId="0" numFmtId="164">'."\n".
				'<alignment horizontal="general" indent="0" shrinkToFit="false" textRotation="0" vertical="bottom" wrapText="false"/>'."\n".
				'<protection hidden="false" locked="true"/>'."\n".
				'</xf>'."\n".
				'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="1" numFmtId="0"/>'."\n".
				'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="1" numFmtId="0"/>'."\n".
				'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="2" numFmtId="0"/>'."\n".
				'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="2" numFmtId="0"/>'."\n".
				'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="0"/>'."\n".
				'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="0"/>'."\n".
				'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="0"/>'."\n".
				'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="0"/>'."\n".
				'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="0"/>'."\n".
				'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="0"/>'."\n".
				'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="0"/>'."\n".
				'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="0"/>'."\n".
				'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="0"/>'."\n".
				'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="0"/>'."\n".
				'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="1" numFmtId="43"/>'."\n".
				'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="1" numFmtId="41"/>'."\n".
				'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="1" numFmtId="44"/>'."\n".
				'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="1" numFmtId="42"/>'."\n".
				'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="1" numFmtId="9"/>'."\n".
			'</cellStyleXfs>'."\n";

		$xml .= '<cellXfs count="'.count($this->cell_formats).'">'."\n";
		foreach ($this->cell_formats as $i => $format)
		{
			$xml .= '<xf applyAlignment="false" applyBorder="false" applyFont="false" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="'.(164 + $i).'" xfId="0"/>'."\n";
		}
		$xml .= '</cellXfs>'."\n".
		'<cellStyles count="6">'."\n".
			'<cellStyle builtinId="0" customBuiltin="false" name="Normal" xfId="0"/>'."\n".
			'<cellStyle builtinId="3" customBuiltin="false" name="Comma" xfId="15"/>'."\n".
			'<cellStyle builtinId="6" customBuiltin="false" name="Comma [0]" xfId="16"/>'."\n".
			'<cellStyle builtinId="4" customBuiltin="false" name="Currency" xfId="17"/>'."\n".
			'<cellStyle builtinId="7" customBuiltin="false" name="Currency [0]" xfId="18"/>'."\n".
			'<cellStyle builtinId="5" customBuiltin="false" name="Percent" xfId="19"/>'."\n".
		'</cellStyles>'."\n".
		'</styleSheet>'."\n";
		
		return $xml;
	}
	
	public function getCellName($row_number, $column_number)
	{
		$n = $column_number;
		for($r = ""; $n >= 0; $n = intval($n / 26) - 1) {
			$r = chr($n%26 + 0x41) . $r;
		}
		return $r . ($row_number+1);
	}
	
	protected function escape($string) {
		return htmlspecialchars($string, ENT_QUOTES, 'utf-8');
	}
}