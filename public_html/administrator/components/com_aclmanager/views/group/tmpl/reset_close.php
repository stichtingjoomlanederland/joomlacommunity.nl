<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;
?>
<script type="text/javascript">
    window.setTimeout('close();', 1);
	function close()
	{
		window.parent.document.location.reload();
		parent.SqueezeBox.close();
	}
</script>