<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormViewSubmissions extends JViewLegacy
{
	public function display($tpl = null) {
		$this->params 	= JFactory::getApplication()->getParams('com_rsform');
		$this->template = $this->get('template');
		
		parent::display($tpl);
		
		$this->buildPdf();
	}
	
	protected function buildPdf() {
		/*
		 * Setup external configuration options
		 */
		define('K_TCPDF_EXTERNAL_CONFIG', true);

		/*
		 * Path options
		 */

		// Installation path
		define("K_PATH_MAIN", JPATH_LIBRARIES."/tcpdf");

		// URL path
		define("K_PATH_URL", JPATH_BASE);

		// Fonts path
		define("K_PATH_FONTS", K_PATH_MAIN.'/fonts/');

		// Cache directory path
		define("K_PATH_CACHE", K_PATH_MAIN."/cache");

		// Cache URL path
		define("K_PATH_URL_CACHE", K_PATH_URL."/cache");

		// Images path
		define("K_PATH_IMAGES", K_PATH_MAIN."/images");

		// Blank image path
		define("K_BLANK_IMAGE", K_PATH_IMAGES."/_blank.png");

		/*
		 * Format options
		 */

		// Cell height ratio
		define("K_CELL_HEIGHT_RATIO", 1.25);

		// Magnification scale for titles
		define("K_TITLE_MAGNIFICATION", 1.3);

		// Reduction scale for small font
		define("K_SMALL_RATIO", 2/3);

		// Magnication scale for head
		define("HEAD_MAGNIFICATION", 1.1);

		/*
		 * Create the pdf document
		 */

		jimport('tcpdf.tcpdf');
		
		$pdf = new TCPDF();
		$pdf->SetMargins(15, 27, 15);
		$pdf->SetAutoPageBreak(true, 25);
		$pdf->SetHeaderMargin(5);
		$pdf->SetFooterMargin(10);
		$pdf->setImageScale(4);
		
		$document = JFactory::getDocument();
		
		// Set PDF Metadata
		$pdf->SetCreator($document->getGenerator());
		$pdf->SetTitle($document->getTitle());
		$pdf->SetSubject($document->getDescription());
		$pdf->SetKeywords($document->getMetaData('keywords'));
		
		// Set PDF Header data
		$pdf->setHeaderData('', 0, $document->getTitle(), null);
		
		// Set RTL
		$lang = JFactory::getLanguage();
		$pdf->setRTL($lang->isRTL());
		
		// Set Font
		$font = 'freesans';
		$pdf->setHeaderFont(array($font, '', 10));
		$pdf->setFooterFont(array($font, '', 8));
		
		// Initialize PDF Document
		if (is_callable(array($pdf, 'AliasNbPages'))) {
			$pdf->AliasNbPages();
		}
		$pdf->AddPage();
		
		$pdf->WriteHTML(ob_get_contents(), true);
		$data = $pdf->Output('', 'S');
		
		ob_end_clean();
		
		// Build the PDF Document string from the document buffer
		header('Content-Type: application/pdf; charset=utf-8');
		header('Content-disposition: inline; filename="export.pdf"', true);
		
		echo $data;
		die();
	}
}