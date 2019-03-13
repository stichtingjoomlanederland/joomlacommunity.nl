<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var \Akeeba\Backup\Admin\View\Discover\Html $this */

$js = <<< JS

;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
// due to missing trailing semicolon and/or newline in their code.
var akeeba_browser_callback = null;

akeeba.System.documentReady(function(){
	akeeba.Configuration.URLs['browser'] = 'index.php?option=com_akeeba&view=Browser&processfolder=1&tmpl=component&folder=';
	akeeba.System.addEventListener(document.getElementById('browserbutton'), 'click', function(el){
		var directory = document.getElementById('directory');
		akeeba.Configuration.onBrowser( directory.value, directory );
    });
})

JS;

$this->getContainer()->template->addJSInline($js);

?>

<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/FolderBrowser'); ?>

<div class="akeeba-block--info">
    <p>
		<?php echo \JText::sprintf('COM_AKEEBA_DISCOVER_LABEL_S3IMPORT', 'index.php?option=com_akeeba&view=S3Import'); ?>
    </p>
    <p>
        <a class="akeeba-btn--teal--small" href="index.php?option=com_akeeba&view=S3Import">
            <span class="icon-box-add"></span>
			<?php echo \JText::_('COM_AKEEBA_S3IMPORT'); ?>
        </a>
    </p>
</div>

<form name="adminForm" id="adminForm" action="index.php" method="post" class="akeeba-form--horizontal">

    <div class="akeeba-form-group">
        <label for="directory">
			<?php echo \JText::_('COM_AKEEBA_DISCOVER_LABEL_DIRECTORY'); ?>
        </label>
        <div class="akeeba-input-group">
            <input type="text" name="directory" id="directory" value="<?php echo $this->escape($this->directory); ?>"/>
            <span class="akeeba-input-group-btn">
                <button class="akeeba-btn--inverse" onclick="return false;" id="browserbutton">
                    <span class="akion-folder"></span>
                    <?php echo \JText::_('COM_AKEEBA_CONFIG_UI_BROWSE'); ?>
                </button>
            </span>
        </div>
        <p class="akeeba-help-text">
		    <?php echo \JText::_('COM_AKEEBA_DISCOVER_LABEL_SELECTDIR'); ?>
        </p>

    </div>

    <div class="akeeba-form-group--pull-right">
        <div class="akeeba-form-group--actions">
            <button class="akeeba-btn--primary" onclick="this.form.submit(); return false;">
				<?php echo \JText::_('COM_AKEEBA_DISCOVER_LABEL_SCAN'); ?>
            </button>
        </div>
    </div>

    <div class="akeeba-hidden-fields-container">
        <input type="hidden" name="option" value="com_akeeba"/>
        <input type="hidden" name="view" value="Discover"/>
        <input type="hidden" name="task" value="discover"/>
        <input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
    </div>
</form>
