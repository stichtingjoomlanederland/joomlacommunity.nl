<?php
/**
 * @package AkeebaReleaseSystem
 * @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

?>

<?php if ($this->params->get('show_page_heading') && $this->params->get('show_title')) : ?>
<div class="page-header">
	<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
</div>
<?php endif;?>
<h1>Download Joomla! & Joomla extensies</h1>
<p class="lead">Er zijn duizenden extensies voor Joomla. Dit zijn uitbreidingen voor specifieke functionaliteiten. Hieronder een overzicht van extensies vertaald in het Nederlands.</p>

<?php if( array_key_exists('all', $this->items) ): ?>
	<?php echo $this->loadAnyTemplate('site:com_ars/browses/generic', array('renderSection' => 'all', 'title' => '')); ?>
<?php else: ?>
	<?php echo $this->loadAnyTemplate('site:com_ars/browses/generic', array('renderSection' => 'normal', 'title' => '')); ?>
	<?php echo $this->loadAnyTemplate('site:com_ars/browses/generic', array('renderSection' => 'bleedingedge', 'title' => '')); ?>
<?php endif; ?>