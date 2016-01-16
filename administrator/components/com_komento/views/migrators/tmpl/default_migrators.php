<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
Komento.require().script('migrator.progress', 'migrator.actions', 'migrator.common').done(function($) {
	$(document).ready(function() {
		$('.migratorProgress').implement('Komento.Controller.Migrator.Progress');
		$('.tab-pane').implement('Komento.Controller.Migrator.Actions');
		$('.migratorTable').implement('Komento.Controller.Migrator.Common');
	});
});
</script>

<ul class="tab-master reset-ul">
	<li class="active">
		<a href="#easyblog" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_TAB_EASYBLOG' ); ?></a>
	</li>

	<li>
		<a href="#zoo" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_TAB_ZOO' ); ?></a>
	</li>

	<li>
		<a href="#k2" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_TAB_K2' ); ?></a>
	</li>

	<li>
		<a href="#slicomments" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_TAB_SLICOMMENTS' ); ?></a>
	</li>

	<li>
		<a href="#jcomments" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_TAB_JCOMMENTS' ); ?></a>
	</li>

	<li>
		<a href="#jacomment" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_TAB_JACOMMENT' ); ?></a>
	</li>

	<li>
		<a href="#cjcomment" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_TAB_CJCOMMENT' ); ?></a>
	</li>

	<li>
		<a href="#rscomments" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_TAB_RSCOMMENTS' ); ?></a>
	</li>

	<li>
		<a href="#custom" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_TAB_CUSTOM' ); ?></a>
	</li>
</ul>


<div class="tab-content">

	<div class="tab-pane active" id="easyblog">
		<?php echo $this->loadTemplate( 'easyblog' );?>
	</div>

	<div class="tab-pane" id="zoo">
		<?php echo $this->loadTemplate( 'zoo' );?>
	</div>

	<div class="tab-pane" id="k2">
		<?php echo $this->loadTemplate( 'k2' );?>
	</div>

	<div class="tab-pane" id="slicomments">
		<?php echo $this->loadTemplate( 'slicomments' );?>
	</div>

	<div class="tab-pane" id="jcomments">
		<?php echo $this->loadTemplate( 'jcomments' );?>
	</div>

	<div class="tab-pane" id="jacomment">
		<?php echo $this->loadTemplate( 'jacomment' );?>
	</div>

	<div class="tab-pane" id="cjcomment">
		<?php echo $this->loadTemplate( 'cjcomment' );?>
	</div>

	<div class="tab-pane" id="rscomments">
		<?php echo $this->loadTemplate( 'rscomments' );?>
	</div>

	<div class="tab-pane" id="custom">
		<?php echo $this->loadTemplate( 'custom' );?>
	</div>
</div>


