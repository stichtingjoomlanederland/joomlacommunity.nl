<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\ScanAlerts;

defined('_JEXEC') or die;

use FOF30\View\DataView\Form as BaseView;

class Form extends BaseView
{
	/**
	 * The currently displayed scan
	 *
	 * @var  \Akeeba\AdminTools\Admin\Model\ScanAlerts
	 */
	public $scan;

	protected function onBeforeBrowse()
	{
		parent::onBeforeBrowse();

		// We will add a new hidden field using Javascript: with this little trick we can still use FOF Form and customize it
		$scan_id   = $this->getModel()->getState('scan_id', '');
		$js = <<<JS

;;

akeeba.jQuery(document).ready(function($){
	$('#showComment').click(function(){
		$('#comment').toggle(400);
	})
	
	$('#adminForm').append('<input type="hidden" name="scan_id" value="$scan_id" />')
});

JS;

		$this->addJavascriptInline($js);
	}
}