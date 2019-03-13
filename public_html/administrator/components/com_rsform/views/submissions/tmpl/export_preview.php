<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<p><?php echo JText::_('RSFP_EXPORT_PREVIEW_DESC'); ?></p>
<div id="previewExportDiv">
    <pre id="headersPre"><?php echo implode(',', $this->staticHeaders); ?><?php if (count($this->headers)) { ?>,<?php echo implode(',', $this->headers); ?><?php } ?></pre>
    <pre id="rowPre">&quot;<?php echo implode('&quot;,&quot;', $this->previewArray); ?>&quot;</pre>
</div>