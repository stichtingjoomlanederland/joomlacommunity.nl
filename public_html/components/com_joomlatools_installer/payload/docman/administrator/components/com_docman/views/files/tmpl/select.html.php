<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<? // Loading necessary Markup, CSS and JS ?>
<?= helper('ui.load', array('package' => 'docman')) ?>


<ktml:content>


<ktml:script src="media://com_docman/js/admin/files.select.js" />


<script>
window.addEvent('domready', function(){
	kQuery('#insert-document').click(function(e) {
		e.preventDefault();

        <? if (!empty($callback)): ?>
        window.parent.<?= $callback; ?>(Files.app.selected);
        <? endif; ?>
	});
});
</script>


<p id="document-insert-form" style="display: none;">
	<button class="k-button k-button--success k-button--block" type="button" id="insert-document" disabled><?= translate('Insert') ?></button>
</p>
