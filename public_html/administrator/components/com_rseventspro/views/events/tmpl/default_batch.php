<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="container-fluid">
	<?php 
		$this->tabs->title('COM_RSEVENTSPRO_BATCH_GENERAL_TAB', 'general');
		$content = $this->loadTemplate('batch_other');
		$this->tabs->content($content);
		$this->tabs->title('COM_RSEVENTSPRO_BATCH_OPTIONS_TAB', 'options');
		$content = $this->loadTemplate('batch_options');
		$this->tabs->content($content);
		echo $this->tabs->render();
	?>
</div>