<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\SchedulingInformation;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Model\SchedulingInformation;
use FOF30\View\DataView\Html as BaseView;

class Html extends BaseView
{
	/**
	 * Info about scheduling
	 *
	 * @var  object
	 */
	public $croninfo;

	protected function onBeforeMain()
	{
		/** @var SchedulingInformation $model */
		$model = $this->getModel();

		// Get the CRON paths
		$this->croninfo  = $model->getPaths();

		$js = <<<JS

	;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
	// due to missing trailing semicolon and/or newline in their code.
	(function($) {
		$(document).ready(function(){
			$('#abschedulingTabs a:first').tab('show');
		});
	})(akeeba.jQuery);

JS;
		$this->addJavascriptInline($js);
	}
}