<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="container-fluid">
	<?php 
		$this->tabs->addTitle('COM_RSEVENTSPRO_BATCH_GENERAL_TAB', 'general');
		$this->tabs->addContent($this->loadTemplate('batch_other'));
		$this->tabs->addTitle('COM_RSEVENTSPRO_BATCH_OPTIONS_TAB', 'options');
		$this->tabs->addContent($this->loadTemplate('batch_options'));
		echo $this->tabs->render();
	?>
</div>