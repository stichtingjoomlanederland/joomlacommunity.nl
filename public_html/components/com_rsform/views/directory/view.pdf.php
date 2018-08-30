<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformViewDirectory extends JViewLegacy
{
	public function display( $tpl = null )
    {
		$this->app 			= JFactory::getApplication();
		$this->params 		= $this->app->getParams('com_rsform');
		$this->template 	= $this->get('template');
		$this->directory	= $this->get('directory');
		
		if (!$this->directory->enablepdf)
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 500);
		}

		parent::display($tpl);

        // Build the PDF Document string from the document buffer
        $contents = ob_get_contents();
        ob_end_clean();

        $filename = $this->directory->filename;

        // Build root without Joomla! folder
        if ($folder = JUri::root(true))
        {
            $site_path = substr(str_replace(DIRECTORY_SEPARATOR, '/', JPATH_SITE), 0, -strlen($folder));
        }
        else
        {
            $site_path = str_replace(DIRECTORY_SEPARATOR, '/', JPATH_SITE);
        }

        // Add own CSS
        if ($css_path = realpath($site_path.'/' . JHtml::stylesheet('com_rsform/directory.css', array('pathOnly' => true, 'relative' => true))))
        {
            $contents = '<link rel="stylesheet" href="' . $this->escape($css_path) . '" type="text/css"/>' . $contents;
        }

        // Allow plugins to use their own PDF library
        $this->app->triggerEvent('rsfp_onPDFView', array($contents, $filename));

        /*
         * Setup external configuration options
         */
        define('K_TCPDF_EXTERNAL_CONFIG', true);
        define("K_PATH_MAIN", JPATH_LIBRARIES."/tcpdf");
        define("K_PATH_URL", JPATH_BASE);
        define("K_PATH_FONTS", K_PATH_MAIN.'/fonts/');
        define("K_PATH_CACHE", K_PATH_MAIN."/cache");
        define("K_PATH_URL_CACHE", K_PATH_URL."/cache");
        define("K_PATH_IMAGES", K_PATH_MAIN."/images");
        define("K_BLANK_IMAGE", K_PATH_IMAGES."/_blank.png");
        define("K_CELL_HEIGHT_RATIO", 1.25);
        define("K_TITLE_MAGNIFICATION", 1.3);
        define("K_SMALL_RATIO", 2/3);
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

        $pdf->WriteHTML($contents, true);
        $data = $pdf->Output('', 'S');

        // Build the PDF Document string from the document buffer
        header('Content-Type: application/pdf; charset=utf-8');
        header('Content-disposition: attachment; filename="'.$filename.'"', true);

        echo $data;

        $this->app->close();
	}
}