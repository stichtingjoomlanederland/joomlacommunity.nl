<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/
/**
 * This is a a library that offers support for handling spreadsheets in OpenDocument format
 * Copyright (C) 2008-2012 Alexandru Szasz <alexxed@gmail.com>
 * http://code.google.com/p/open-document-spreadsheet-php/
 *
 * Initially based on https://sourceforge.net/projects/ods-php/
 * Copyright (C) 2008 Juan Lao Tebar (juanlao@eyeos.org) and Jose Carlos Norte (jose@eyeos.org)
 */
class RSFormProODS {
	
	protected $hndContent;
	protected $strTmpDir;
	protected $arrRow;
	
	public function __construct($strTmpDir) {
		jimport('joomla.filesystem.archive');
		
		$this->strTmpDir = $strTmpDir;
		if (!is_dir($this->strTmpDir)) {
			mkdir($this->strTmpDir);
		}
		
		if (file_exists($this->strTmpDir.'/content.xml')) {
			$this->hndContent = fopen($this->strTmpDir . '/content.xml', 'a');
		}
	}
	
	public function startDoc() {
	    $this->hndContent = fopen($this->strTmpDir . '/content.xml', 'w');
		fwrite($this->hndContent, '<?xml version="1.0" encoding="UTF-8"?><office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc" xmlns:dom="http://www.w3.org/2001/xml-events" xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" office:version="1.0">');
		fwrite($this->hndContent, '<office:scripts/>');
		fwrite($this->hndContent, '<office:font-face-decls/>');
		fwrite($this->hndContent, '<office:automatic-styles />');
		fwrite($this->hndContent, '<office:body>');
		fwrite($this->hndContent, '<office:spreadsheet>');
	}
	
	public function startSheet($tableIndex = 0) {
	    fwrite($this->hndContent, '<table:table table:name="' . $tableIndex . '" table:print="false">');
	}
	
	public function endSheet() {
		fwrite($this->hndContent, '</table:table>');
	}
	
	public function endDoc() {
	    fwrite($this->hndContent, '</office:spreadsheet>');
	    fwrite($this->hndContent, '</office:body>');
	    
	    // Footer
	    fwrite($this->hndContent, '</office:document-content>');
	    
	    fclose($this->hndContent);
	}
	
	public function saveRow() {
	    $strRowContent = '<table:table-row>';
		
		foreach($this->arrRow as $cellIndex => $cellContent) {
		    $strRowContent .= '<table:table-cell ';
		    foreach ($cellContent['attrs'] as $attrName => $attrValue) {
		        $strRowContent .= strtolower($attrName) . '="' . $attrValue . '" ';
		    }
		    $strRowContent .= '>';
		    
		    if (isset($cellContent['value'])) {
		        $strRowContent .= '<text:p>' . $cellContent['value'] . '</text:p>';
		    }
		    
		    $strRowContent .= '</table:table-cell>';
		    		    
		}
		
		$strRowContent .= '</table:table-row>';
		
		fwrite($this->hndContent, $strRowContent);
		
		$this->arrRow = array();
	}
	
	private function getMeta($strLang = 'en-US') {
		$strDate = date('Y-m-j\TH:i:s');
		return '<?xml version="1.0" encoding="UTF-8"?>
		<office:document-meta xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:ooo="http://openoffice.org/2004/office" office:version="1.0">
			<office:meta>
				<meta:generator>ods-php</meta:generator>
				<meta:creation-date>' . $strDate . '</meta:creation-date>
				<dc:date>' . $strDate . '</dc:date>
				<dc:language>' . $strLang . '</dc:language>
				<meta:editing-cycles>2</meta:editing-cycles>
				<meta:editing-duration>PT15S</meta:editing-duration>
				<meta:user-defined meta:name="Info 1"/>
				<meta:user-defined meta:name="Info 2"/>
				<meta:user-defined meta:name="Info 3"/>
				<meta:user-defined meta:name="Info 4"/>
			</office:meta>
		</office:document-meta>';
	}
	
	private function getStyle() {
		return
		'<?xml version="1.0" encoding="UTF-8"?>
		<office:document-styles
		    xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"
		    xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0"
		    xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0"
		    xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0"
		    xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0"
		    xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0"
		    xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/"
		    xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0"
		    xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0"
		    xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0"
		    xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0"
		    xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0"
		    xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0"
		    xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0"
		    xmlns:ooo="http://openoffice.org/2004/office" xmlns:ooow="http://openoffice.org/2004/writer"
		    xmlns:oooc="http://openoffice.org/2004/calc" xmlns:dom="http://www.w3.org/2001/xml-events"
		    office:version="1.0">
		    <office:font-face-decls>
		        <style:font-face style:name="Liberation Sans"
		            svg:font-family="&apos;Liberation Sans&apos;"
		            style:font-family-generic="swiss" style:font-pitch="variable" />
		        <style:font-face style:name="DejaVu Sans"
		            svg:font-family="&apos;DejaVu Sans&apos;"
		            style:font-family-generic="system" style:font-pitch="variable" />
		    </office:font-face-decls>
		    <office:styles>
		        <style:default-style style:family="table-cell">
		            <style:table-cell-properties
		                style:decimal-places="2" />
		            <style:paragraph-properties
		                style:tab-stop-distance="1.25cm" />
		            <style:text-properties
		                style:font-name="Liberation Sans" fo:language="es"
		                fo:country="ES" style:font-name-asian="DejaVu Sans"
		                style:language-asian="zxx" style:country-asian="none"
		                style:font-name-complex="DejaVu Sans"
		                style:language-complex="zxx" style:country-complex="none" />
		        </style:default-style>
		        <number:number-style style:name="N0">
		            <number:number number:min-integer-digits="1" />
		        </number:number-style>
		        <number:currency-style style:name="N103P0"
		            style:volatile="true">
		            <number:number number:decimal-places="2"
		                number:min-integer-digits="1" number:grouping="true" />
		            <number:text>
		            </number:text>
		            <number:currency-symbol number:language="en" number:country="US">$</number:currency-symbol>
		        </number:currency-style>
		        <number:currency-style style:name="N103">
		            <style:text-properties fo:color="#ff0000" />
		            <number:text>-</number:text>
		            <number:number number:decimal-places="2"
		                number:min-integer-digits="1" number:grouping="true" />
		            <number:text>
		            </number:text>
		            <number:currency-symbol number:language="en" number:country="US">$</number:currency-symbol>
		            <style:map style:condition="value()&gt;=0"
		                style:apply-style-name="N103P0" />
		        </number:currency-style>
		        <style:style style:name="Default" style:family="table-cell" />
		        <style:style style:name="Result" style:family="table-cell"
		            style:parent-style-name="Default">
		            <style:text-properties fo:font-style="italic"
		                style:text-underline-style="solid"
		                style:text-underline-width="auto"
		                style:text-underline-color="font-color" fo:font-weight="bold" />
		        </style:style>
		        <style:style style:name="Result2" style:family="table-cell"
		            style:parent-style-name="Result" style:data-style-name="N103" />
		        <style:style style:name="Heading" style:family="table-cell"
		            style:parent-style-name="Default">
		            <style:table-cell-properties
		                style:text-align-source="fix" style:repeat-content="false" />
		            <style:paragraph-properties
		                fo:text-align="center" />
		            <style:text-properties fo:font-size="16pt"
		                fo:font-style="italic" fo:font-weight="bold" />
		        </style:style>
		        <style:style style:name="Heading1" style:family="table-cell"
		            style:parent-style-name="Heading">
		            <style:table-cell-properties
		                style:rotation-angle="90" />
		        </style:style>
		    </office:styles>
		    <office:automatic-styles>
		        <style:page-layout style:name="pm1">
		            <style:page-layout-properties
		                style:writing-mode="lr-tb" />
		            <style:header-style>
		                <style:header-footer-properties
		                    fo:min-height="0.751cm" fo:margin-left="0cm"
		                    fo:margin-right="0cm" fo:margin-bottom="0.25cm" />
		            </style:header-style>
		            <style:footer-style>
		                <style:header-footer-properties
		                    fo:min-height="0.751cm" fo:margin-left="0cm"
		                    fo:margin-right="0cm" fo:margin-top="0.25cm" />
		            </style:footer-style>
		        </style:page-layout>
		        <style:page-layout style:name="pm2">
		            <style:page-layout-properties
		                style:writing-mode="lr-tb" />
		            <style:header-style>
		                <style:header-footer-properties
		                    fo:min-height="0.751cm" fo:margin-left="0cm"
		                    fo:margin-right="0cm" fo:margin-bottom="0.25cm"
		                    fo:border="0.088cm solid #000000" fo:padding="0.018cm"
		                    fo:background-color="#c0c0c0">
		                    <style:background-image />
		                </style:header-footer-properties>
		            </style:header-style>
		            <style:footer-style>
		                <style:header-footer-properties
		                    fo:min-height="0.751cm" fo:margin-left="0cm"
		                    fo:margin-right="0cm" fo:margin-top="0.25cm"
		                    fo:border="0.088cm solid #000000" fo:padding="0.018cm"
		                    fo:background-color="#c0c0c0">
		                    <style:background-image />
		                </style:header-footer-properties>
		            </style:footer-style>
		        </style:page-layout>
		    </office:automatic-styles>
		    <office:master-styles>
		        <style:master-page style:name="Default"
		            style:page-layout-name="pm1">
		            <style:header>
		                <text:p>
		                    <text:sheet-name>???</text:sheet-name>
		                </text:p>
		            </style:header>
		            <style:header-left style:display="false" />
		            <style:footer>
		                <text:p>
		                    Página
		                    <text:page-number>1</text:page-number>
		                </text:p>
		            </style:footer>
		            <style:footer-left style:display="false" />
		        </style:master-page>
		        <style:master-page style:name="Report"
		            style:page-layout-name="pm2">
		            <style:header>
		                <style:region-left>
		                    <text:p>
		                        <text:sheet-name>???</text:sheet-name>
		                        (
		                        <text:title>???</text:title>
		                        )
		                    </text:p>
		                </style:region-left>
		                <style:region-right>
		                    <text:p>
		                        <text:date style:data-style-name="N2"
		                            text:date-value="2008-02-18">18/02/2008</text:date>
		                        ,
		                        <text:time>00:17:06</text:time>
		                    </text:p>
		                </style:region-right>
		            </style:header>
		            <style:header-left style:display="false" />
		            <style:footer>
		                <text:p>
		                    Page
		                    <text:page-number>1</text:page-number>
		                    /
		                    <text:page-count>99</text:page-count>
		                </text:p>
		            </style:footer>
		            <style:footer-left style:display="false" />
		        </style:master-page>
		    </office:master-styles>
		</office:document-styles>';
	}
	
	private function getSettings() {
		return
		'<?xml version="1.0" encoding="UTF-8"?>
	     <office:document-settings xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:config="urn:oasis:names:tc:opendocument:xmlns:config:1.0" xmlns:ooo="http://openoffice.org/2004/office" office:version="1.0">
	     <office:settings>
	    	<config:config-item-set config:name="ooo:view-settings">
	    		<config:config-item config:name="VisibleAreaTop" config:type="int">0</config:config-item>
	    		<config:config-item config:name="VisibleAreaLeft" config:type="int">0</config:config-item>
	    		<config:config-item config:name="VisibleAreaWidth" config:type="int">2258</config:config-item>
	    		<config:config-item config:name="VisibleAreaHeight" config:type="int">903</config:config-item>
	    		<config:config-item-map-indexed config:name="Views">
    			<config:config-item-map-entry>
    				<config:config-item config:name="ViewId" config:type="string">View1</config:config-item>
    				<config:config-item-map-named config:name="Tables">
    				<config:config-item-map-entry config:name="Hoja1">
	    				<config:config-item config:name="CursorPositionX" config:type="int">0</config:config-item>
	    				<config:config-item config:name="CursorPositionY" config:type="int">1</config:config-item>
            		    <config:config-item config:name="HorizontalSplitMode" config:type="short">0</config:config-item>
            		    <config:config-item config:name="VerticalSplitMode" config:type="short">0</config:config-item>
            		    <config:config-item config:name="HorizontalSplitPosition" config:type="int">0</config:config-item>
            		    <config:config-item config:name="VerticalSplitPosition" config:type="int">0</config:config-item>
            		    <config:config-item config:name="ActiveSplitRange" config:type="short">2</config:config-item>
            		    <config:config-item config:name="PositionLeft" config:type="int">0</config:config-item>
            		    <config:config-item config:name="PositionRight" config:type="int">0</config:config-item>
            		    <config:config-item config:name="PositionTop" config:type="int">0</config:config-item>
            		    <config:config-item config:name="PositionBottom" config:type="int">0</config:config-item>
    				</config:config-item-map-entry>
    				</config:config-item-map-named>
            		    <config:config-item config:name="ActiveTable" config:type="string">Hoja1</config:config-item>
            		    <config:config-item config:name="HorizontalScrollbarWidth" config:type="int">270</config:config-item>
            		    <config:config-item config:name="ZoomType" config:type="short">0</config:config-item>
            		    <config:config-item config:name="ZoomValue" config:type="int">100</config:config-item>
            		    <config:config-item config:name="PageViewZoomValue" config:type="int">60</config:config-item>
            		    <config:config-item config:name="ShowPageBreakPreview" config:type="boolean">false</config:config-item>
            		    <config:config-item config:name="ShowZeroValues" config:type="boolean">true</config:config-item>
            		    <config:config-item config:name="ShowNotes" config:type="boolean">true</config:config-item>
            		    <config:config-item config:name="ShowGrid" config:type="boolean">true</config:config-item>
            		    <config:config-item config:name="GridColor" config:type="long">12632256</config:config-item>
            		    <config:config-item config:name="ShowPageBreaks" config:type="boolean">true</config:config-item>
            		    <config:config-item config:name="HasColumnRowHeaders" config:type="boolean">true</config:config-item>
            		    <config:config-item config:name="HasSheetTabs" config:type="boolean">true</config:config-item>
            		    <config:config-item config:name="IsOutlineSymbolsSet" config:type="boolean">true</config:config-item>
            		    <config:config-item config:name="IsSnapToRaster" config:type="boolean">false</config:config-item>
            		    <config:config-item config:name="RasterIsVisible" config:type="boolean">false</config:config-item>
            		    <config:config-item config:name="RasterResolutionX" config:type="int">1000</config:config-item>
            		    <config:config-item config:name="RasterResolutionY" config:type="int">1000</config:config-item>
            		    <config:config-item config:name="RasterSubdivisionX" config:type="int">1</config:config-item>
            			<config:config-item config:name="RasterSubdivisionY" config:type="int">1</config:config-item>
						<config:config-item config:name="IsRasterAxisSynchronized" config:type="boolean">true</config:config-item>
				</config:config-item-map-entry>
				</config:config-item-map-indexed>
			</config:config-item-set>
			<config:config-item-set config:name="ooo:configuration-settings">
        		<config:config-item config:name="ShowZeroValues" config:type="boolean">true</config:config-item>
        		<config:config-item config:name="ShowNotes" config:type="boolean">true</config:config-item>
        		<config:config-item config:name="ShowGrid" config:type="boolean">true</config:config-item>
        		<config:config-item config:name="GridColor" config:type="long">12632256</config:config-item>
        		<config:config-item config:name="ShowPageBreaks" config:type="boolean">true</config:config-item>
        		<config:config-item config:name="LinkUpdateMode" config:type="short">3</config:config-item>
        		<config:config-item config:name="HasColumnRowHeaders" config:type="boolean">true</config:config-item>
        		<config:config-item config:name="HasSheetTabs" config:type="boolean">true</config:config-item>
        		<config:config-item config:name="IsOutlineSymbolsSet" config:type="boolean">true</config:config-item>
        		<config:config-item config:name="IsSnapToRaster" config:type="boolean">false</config:config-item>
        		<config:config-item config:name="RasterIsVisible" config:type="boolean">false</config:config-item>
        		<config:config-item config:name="RasterResolutionX" config:type="int">1000</config:config-item>
        		<config:config-item config:name="RasterResolutionY" config:type="int">1000</config:config-item>
        		<config:config-item config:name="RasterSubdivisionX" config:type="int">1</config:config-item>
        		<config:config-item config:name="RasterSubdivisionY" config:type="int">1</config:config-item>
        		<config:config-item config:name="IsRasterAxisSynchronized" config:type="boolean">true</config:config-item>
        		<config:config-item config:name="AutoCalculate" config:type="boolean">true</config:config-item>
        		<config:config-item config:name="PrinterName" config:type="string">Generic Printer</config:config-item>
        		<config:config-item config:name="PrinterSetup" config:type="base64Binary">WAH+/0dlbmVyaWMgUHJpbnRlcgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAU0dFTlBSVAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAWAAMAngAAAAAAAAAFAFZUAAAkbQAASm9iRGF0YSAxCnByaW50ZXI9R2VuZXJpYyBQcmludGVyCm9yaWVudGF0aW9uPVBvcnRyYWl0CmNvcGllcz0xCm1hcmdpbmRhanVzdG1lbnQ9MCwwLDAsMApjb2xvcmRlcHRoPTI0CnBzbGV2ZWw9MApjb2xvcmRldmljZT0wClBQRENvbnRleERhdGEKUGFnZVNpemU6TGV0dGVyAAA=</config:config-item>
        		<config:config-item config:name="ApplyUserData" config:type="boolean">true</config:config-item>
        		<config:config-item config:name="CharacterCompressionType" config:type="short">0</config:config-item>
        		<config:config-item config:name="IsKernAsianPunctuation" config:type="boolean">false</config:config-item>
        		<config:config-item config:name="SaveVersionOnClose" config:type="boolean">false</config:config-item>
        		<config:config-item config:name="UpdateFromTemplate" config:type="boolean">false</config:config-item>
        		<config:config-item config:name="AllowPrintJobCancel" config:type="boolean">true</config:config-item>
        		<config:config-item config:name="LoadReadonly" config:type="boolean">false</config:config-item>
			</config:config-item-set>
		</office:settings>
		</office:document-settings>';
	}
	
	private function getManifest() {
		return
			'<?xml version="1.0" encoding="UTF-8"?>
		    <manifest:manifest xmlns:manifest="urn:oasis:names:tc:opendocument:xmlns:manifest:1.0">
		    <manifest:file-entry manifest:media-type="application/vnd.oasis.opendocument.spreadsheet" manifest:full-path="/"/>
		    <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/statusbar/"/>
		    <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/accelerator/"/>
		    <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/floater/"/>
		    <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/popupmenu/"/>
		    <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/progressbar/"/>
		    <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/menubar/"/>
		    <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/toolbar/"/>
		    <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/images/Bitmaps/"/>
		    <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/images/"/>
		    <manifest:file-entry manifest:media-type="application/vnd.sun.xml.ui.configuration" manifest:full-path="Configurations2/"/>
		    <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="content.xml"/>
		    <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="styles.xml"/>
		    <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="meta.xml"/>
		    <manifest:file-entry manifest:media-type="" manifest:full-path="Thumbnails/"/>
		    <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="settings.xml"/>
		    </manifest:manifest>';
	}
	
	/**
	 * Adds a cell
	 * @param integer $intColumn the column number
	 * @param mixed $value the cell's value
	 * @param string $type float, string
	 */
	public function addCell($intColumn, $value, $type) {
		$this->arrRow[$intColumn]['value'] = str_replace(array('<', '>', '"', "'", '&', '//', chr(0x0b)), array('&lt;', '&gt;', '&quot;', '&apos;', '&amp;', '&#47;', "\n"), $value);
		
	    if (@simplexml_load_string(sprintf('<?xml version="1.0" encoding="UTF-8"?><root>%s</root>', $this->arrRow[$intColumn]['value'])) === false) {
	        if (@simplexml_load_string(sprintf('<?xml version="1.0" encoding="UTF-8"?><root>%s</root>', html_entity_decode($this->arrRow[$intColumn]['value'], ENT_QUOTES, 'UTF-8'))) === false) {
	            if (@simplexml_load_string(sprintf('<?xml version="1.0" encoding="UTF-8"?><root>%s</root>', html_entity_decode(iconv('ISO-8859-1', 'UTF-8//IGNORE', $this->arrRow[$intColumn]['value'])))) === false) {
	                $strFixedName = trim(preg_replace('/[^0-9a-z\-\_\'"\.\(\)\s]/i', ' ', $this->arrRow[$intColumn]['value']));
	                printf("fixed %s to %s\n", $this->arrRow[$intColumn]['value'], $strFixedName);
	            }
	        }
	        else {
	            $strFixedName = html_entity_decode($this->arrRow[$intColumn]['value'], ENT_QUOTES, 'UTF-8');
	            if ($strFixedName != $this->arrRow[$intColumn]['value'])
	                printf("fixed %s to %s\n", $this->arrRow[$intColumn]['value'], $strFixedName);
	        }
	    }
	    
	    if (isset($strFixedName) && $strFixedName != $this->arrRow[$intColumn]['value']) {
	        $this->arrRow[$intColumn]['value'] = $strFixedName;
	    }
	    
		$this->arrRow[$intColumn]['attrs'] = array('OFFICE:VALUE-TYPE'=> $type, 'OFFICE:VALUE'=>$this->arrRow[$intColumn]['value']);
	}
	
    public function saveOds() {
    	file_put_contents($this->strTmpDir . '/mimetype','application/vnd.oasis.opendocument.spreadsheet');
    	file_put_contents($this->strTmpDir . '/meta.xml', $this->getMeta('en-US'));
    	file_put_contents($this->strTmpDir . '/styles.xml', $this->getStyle());
    	file_put_contents($this->strTmpDir . '/settings.xml', $this->getSettings());
		if (!is_dir($this->strTmpDir . '/META-INF/')) {
			mkdir($this->strTmpDir . '/META-INF/');
			mkdir($this->strTmpDir . '/Configurations2/');
			mkdir($this->strTmpDir . '/Configurations2/acceleator/');
			mkdir($this->strTmpDir . '/Configurations2/images/');
			mkdir($this->strTmpDir . '/Configurations2/popupmenu/');
			mkdir($this->strTmpDir . '/Configurations2/statusbar/');
			mkdir($this->strTmpDir . '/Configurations2/floater/');
			mkdir($this->strTmpDir . '/Configurations2/menubar/');
			mkdir($this->strTmpDir . '/Configurations2/progressbar/');
			mkdir($this->strTmpDir . '/Configurations2/toolbar/');
		}
		file_put_contents($this->strTmpDir . '/META-INF/manifest.xml',$this->getManifest());
		
		// create the zip archive
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$files = $this->_prepareZIPFiles(JFolder::files($this->strTmpDir, '.', true, true));
		
		$adapter = JArchive::getAdapter('zip');
		if ($adapter->create($this->strTmpDir.'.ods', $files)) {
			$this->cleanUp();
		}
    }
	
	protected function _prepareZIPFiles($files) {
		$return = array();
		foreach ($files as $file) {
			$return[] = array(
				'data' => file_get_contents($file),
				'name' => substr_replace($file, '', 0, strlen($this->strTmpDir)+1)
			);
		}
		
		return $return;
	}
	
	protected function cleanUp() {
		unlink($this->strTmpDir . '/mimetype');
		unlink($this->strTmpDir . '/meta.xml');
		unlink($this->strTmpDir . '/content.xml');
		unlink($this->strTmpDir . '/styles.xml');
		unlink($this->strTmpDir . '/settings.xml');
		unlink($this->strTmpDir . '/META-INF/manifest.xml');
		rmdir($this->strTmpDir . '/META-INF/');
		rmdir($this->strTmpDir . '/Configurations2/acceleator/');
		rmdir($this->strTmpDir . '/Configurations2/images/');
		rmdir($this->strTmpDir . '/Configurations2/popupmenu/');
		rmdir($this->strTmpDir . '/Configurations2/statusbar/');
		rmdir($this->strTmpDir . '/Configurations2/floater/');
		rmdir($this->strTmpDir . '/Configurations2/menubar/');
		rmdir($this->strTmpDir . '/Configurations2/progressbar/');
		rmdir($this->strTmpDir . '/Configurations2/toolbar/');
		rmdir($this->strTmpDir . '/Configurations2/');
		rmdir($this->strTmpDir);
	}
}