<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class RSFormPDF
{
	public $dompdf;
	
	public function __construct() {
		require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/pdf/dompdf6/dompdf_config.inc.php';
		$this->dompdf = new DOMPDF();
	}
	
	public function render($filename, $html) {
		// suppress errors
		if (strlen($html) > 0) {
			$dompdf	= &$this->dompdf;
			
			if (preg_match_all('#[^\x00-\x7F]#u', $html, $matches)) {
				foreach ($matches[0] as $match) {
					$html = str_replace($match, $this->_convertASCII($match), $html);
				}
			}
			
			$dompdf->load_html(utf8_decode($html), 'utf-8');
			$dompdf->render();
		}
	}
	
	// Legacy
	public function write($filename, $html, $output = false) {
		// For convenience
		$dompdf	= &$this->dompdf;
		
		// Render
		$this->render($filename, $html);
		
		// Emulate old function behavior
		if ($output) {
			ob_end_clean();
			$dompdf->stream($filename);
		} else {
			return $dompdf->output();
		}
	}
	
	protected function _convertASCII($str) {
		$count	= 1;
		$out	= '';
		$temp	= array();
		
		for ($i = 0, $s = strlen($str); $i < $s; $i++) {
			$ordinal = ord($str[$i]);
			if ($ordinal < 128) {
				$out .= $str[$i];
			}
			else
			{
				if (count($temp) == 0) {
					$count = ($ordinal < 224) ? 2 : 3;
				}
			
				$temp[] = $ordinal;
			
				if (count($temp) == $count) {
					$number = ($count == 3) ? (($temp['0'] % 16) * 4096) + (($temp['1'] % 64) * 64) + ($temp['2'] % 64) : (($temp['0'] % 32) * 64) + ($temp['1'] % 64);

					$out .= '&#'.$number.';';
					$count = 1;
					$temp = array();
				}
			}
		}
		
		return $out;
	}
}