<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="row-fluid form-horizontal" style="min-height: 250px">
	<?php echo $this->form->renderField('locations'); ?>
	<?php echo $this->form->renderField('categories'); ?>
	<?php echo $this->form->renderField('tags'); ?>
	<?php echo $this->form->renderField('itemid'); ?>
	<?php echo $this->form->renderField('type'); ?>
</div>