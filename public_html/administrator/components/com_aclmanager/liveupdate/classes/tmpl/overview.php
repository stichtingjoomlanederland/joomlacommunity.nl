<?php
/**
 * @package LiveUpdate
 * @copyright Copyright (c)2010-2012 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

$class = $this->updateInfo->hasUpdates ? 'hasupdates' : 'noupdates';
if(!$this->updateInfo->supported || $this->updateInfo->stuck) {
	$class ='issues';
}

$bootclass = $this->updateInfo->hasUpdates ? 'alert' : 'alert-success';
if(!$this->updateInfo->supported || $this->updateInfo->stuck) {
	$bootclass = 'alert';
} 

if(($this->needsAuth) && (!version_compare(JVERSION, '3.0', 'ge'))) {	
	JFactory::getApplication()->enqueueMessage(  JText::_('LIVEUPDATE_ERROR_NEEDSAUTH').' <a rel="{handler: \'iframe\', size: {x: 875, y: 550}, onClose: function() {}}" href="index.php?option=com_config&view=component&component=com_aclmanager&path=&tmpl=component" class="modal">'.JText::_('COM_ACLMANAGER_UPDATE_SET_DOWNLOAD_ID').'</a>', 'warning');
} elseif(($this->needsAuth) && (version_compare(JVERSION, '3.0', 'ge'))) {
	$uri = (string) JUri::getInstance();
	$return = urlencode(base64_encode($uri));
	JFactory::getApplication()->enqueueMessage( JText::_('LIVEUPDATE_ERROR_NEEDSAUTH').' <a href="index.php?option=com_config&view=component&component=com_aclmanager&path=&return='.$return.'">'.JText::_('COM_ACLMANAGER_UPDATE_SET_DOWNLOAD_ID').'</a>', 'warning');
}
?>

<div id="aclmanager" class="diagnostic update row-fluid">
	<!-- Begin changelog -->
	<div class="width-70 fltlft span9">
		<div class="well <?php echo $bootclass?>">
		<?php if (!version_compare(JVERSION, '3.0', 'ge')) :?>
		<fieldset id="updates" class="adminform <?php echo $class?>">
			<legend><?php echo JText::_('COM_ACLMANAGER_UPDATE_CHECK'); ?></legend>
		<?php endif;?>
				<?php if(!$this->updateInfo->supported): ?>				
			
				<h3><?php echo JText::_('LIVEUPDATE_NOTSUPPORTED_HEAD') ?></h3>
				
				<p><?php echo JText::_('LIVEUPDATE_NOTSUPPORTED_INFO'); ?></p>
				<p class="liveupdate-url"><?php echo $this->escape($this->updateInfo->extInfo->updateurl) ?></p>
				<p><?php echo JText::sprintf('LIVEUPDATE_NOTSUPPORTED_ALTMETHOD', $this->escape($this->updateInfo->extInfo->title)); ?></p>
				
				<p class="liveupdate-buttons">
					<button onclick="window.location='<?php echo $this->requeryURL ?>'" ><?php echo JText::_('LIVEUPDATE_REFRESH_INFO') ?></button>
				</p>
		
				<?php elseif($this->updateInfo->stuck):?>
				
				<h3><?php echo JText::_('LIVEUPDATE_STUCK_HEAD') ?></h3>
				
				<p><?php echo JText::_('LIVEUPDATE_STUCK_INFO'); ?></p>
				<p><?php echo JText::sprintf('LIVEUPDATE_NOTSUPPORTED_ALTMETHOD', $this->escape($this->updateInfo->extInfo->title)); ?></p>
				
				<p class="liveupdate-buttons">
					<button onclick="window.location='<?php echo $this->requeryURL ?>'" ><?php echo JText::_('LIVEUPDATE_REFRESH_INFO') ?></button>
				</p>
			
				<?php else: ?>
			
				<h3><?php echo JText::_('LIVEUPDATE_'.strtoupper($class).'_HEAD') ?>: <?php echo $this->updateInfo->extInfo->title ?> <?php echo $this->updateInfo->version ?>, <?php echo strtolower(JText::_('COM_ACLMANAGER_UPDATE_RELEASE_DATE')); ?> <?php echo $this->updateInfo->date ?></h3>
				
				<?php echo $this->updateInfo->releasenotes ?>
				
				<p><strong><?php echo JText::_('COM_ACLMANAGER_UPDATE_CHANGELOG_HISTORY'); ?>: </strong><a href="http://www.aclmanager.net/changelog" target="_blank">http://www.aclmanager.net/changelog</a></p>
				<p><strong><?php echo JText::sprintf('COM_ACLMANAGER_UPDATE_ANNOUNCEMENT', $this->updateInfo->extInfo->title.' '.$this->updateInfo->version);?>:</strong> <a href="<?php echo $this->updateInfo->infoURL ?>" target="_blank"><?php echo $this->updateInfo->infoURL ?></a></p>
				
				<p class="liveupdate-buttons">
					<?php if($this->updateInfo->hasUpdates):?>
					<?php $disabled = $this->needsAuth ? 'disabled="disabled"' : ''?>
					<button class="btn btn-primary" <?php echo $disabled?> onclick="window.location='<?php echo $this->runUpdateURL ?>'" ><i class="icon-upload"></i> <?php echo JText::_('LIVEUPDATE_DO_UPDATE') ?></button>
					<?php endif;?>
					<button class="btn" onclick="window.location='<?php echo $this->requeryURL ?>'" ><i class="icon-refresh"></i> <?php echo JText::_('LIVEUPDATE_REFRESH_INFO') ?></button>
				</p>
				
				<?php endif; ?>
		<?php if (!version_compare(JVERSION, '3.0', 'ge')) :?>
		</fieldset>
		<?php endif; ?>
		</div>
		<p class="liveupdate-poweredby">
			Powered by <a href="https://www.akeebabackup.com/software/akeeba-live-update.html">Akeeba Live Update</a>
		</p>
	</div>
	<!-- End changelog -->
	
	<!-- Begin sidebar -->
	<div class="width-30 fltrt span3">
		<div class="well installed">
		<fieldset class="adminform">
			<legend><?php echo JText::_('LIVEUPDATE_CURRENTVERSION'); ?></legend>
			<?php if (version_compare(JVERSION, '3.0', 'ge')) :?>
			<dl>
				<dt><?php echo JText::_('COM_ACLMANAGER_UPDATE_RELEASE'); ?></dt>
				<dd><?php echo $this->updateInfo->extInfo->version; ?></dd>
				<dt><?php echo JText::_('COM_ACLMANAGER_UPDATE_RELEASE_DATE'); ?></dt>
				<dd><?php echo $this->updateInfo->extInfo->date; ?></dd>
			</dl>
			<?php else:?>
			<ul class="adminformlist">
				<li>
					<label><?php echo JText::_('COM_ACLMANAGER_UPDATE_RELEASE'); ?></label>
					<span class="value"><?php echo $this->updateInfo->extInfo->version; ?></span>
				</li>
				<li>
					<label><?php echo JText::_('COM_ACLMANAGER_UPDATE_RELEASE_DATE'); ?></label>
					<span class="value"><?php echo $this->updateInfo->extInfo->date; ?></span>
				</li>
			</ul>
			<?php endif;?>
		</fieldset>
		</div>
		<div class="clr"></div>
	</div>
	<!-- End sidebar -->
</div>
<div class="copyright">
	<p><?php echo JText::_('COM_ACLMANAGER_COPYRIGHT'); ?> &copy; 2011 - <?php echo date('Y');?>. <?php echo JText::_('COM_ACLMANAGER_DEVELOPED_BY');?>. <a href="http://www.aclmanager.net" target="_blank">www.aclmanager.net</a></p>
</div>